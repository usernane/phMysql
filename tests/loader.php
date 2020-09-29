<?php

/* 
 * The MIT License
 *
 * Copyright 2018 Ibrahim.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
$testsDirName = 'tests';
$rootDir = substr(__DIR__, 0, strlen(__DIR__) - strlen($testsDirName));
$DS = DIRECTORY_SEPARATOR;
$rootDirTrimmed = trim($rootDir,'/\\');
echo 'Include Path: \''.get_include_path().'\''."\n";

if (explode($DS, $rootDirTrimmed)[0] == 'home') {
    //linux.
    $rootDir = $DS.$rootDirTrimmed.$DS;
} else {
    $rootDir = $rootDirTrimmed.$DS;
}
define('ROOT', $rootDir);
echo 'Root Directory: \''.$rootDir.'\'.'."\n";
require_once $rootDir.'src'.$DS.'MySQLColumn.php';
require_once $rootDir.'src'.$DS.'ForeignKey.php';
require_once $rootDir.'src'.$DS.'MySQLLink.php';
require_once $rootDir.'src'.$DS.'MySQLQuery.php';
require_once $rootDir.'src'.$DS.'MySQLTable.php';
require_once $rootDir.'src'.$DS.'EntityMapper.php';
require_once $rootDir.'src'.$DS.'JoinTable.php';
require_once $rootDir.'tests'.$DS.'QueryTestObj.php';
require_once $rootDir.'tests'.$DS.'UsersQuery.php';
require_once $rootDir.'tests'.$DS.'ArticleQuery.php';
require_once $rootDir.'tests'.$DS.'EntityUser.php';
require_once $rootDir.'tests'.$DS.'User.php';

echo "Initializing Database...\n";
$conn = new webfiori\phMysql\MySQLLink('localhost', 'root', '123456');
$conn->setDB('testing_db');
$q00 = new phMysql\tests\UsersQuery();
$q00->createStructure();
echo $q00->getQuery()."\n";

if ($conn->executeQuery($q00)) {
    $q00 = new phMysql\tests\ArticleQuery();
    $q00->createStructure();
    echo $q00->getQuery()."\n";

    if ($conn->executeQuery($q00)) {
        echo "Successfully Created Tables.\n";
        echo "Adding Test Dataset...\n";
        $articleId = 1;

        for ($x = 0 ; $x < 5 ; $x++) {
            $q = new phMysql\tests\UsersQuery();
            $q->insertRecord([
                'user-id' => $x + 1,
                'email' => $x.'@test.com',
                'name' => 'Test User #'.$x
            ]);
            echo $q->getQuery()."\n";

            if ($conn->executeQuery($q)) {
                for ($y = 0 ; $y < 4 ; $y++) {
                    $q = new \phMysql\tests\ArticleQuery();
                    $q->insertRecord([
                        'author-id' => $x + 1,
                        'content' => 'This is the body of article number '.($y + 1).' which '
                        .'is created by the user which has the ID '.($x + 1).'.',
                        'title' => 'User # '.($x + 1).' Article #'.($y + 1),
                        'article-id' => $articleId
                    ]);
                    echo $q->getQuery()."\n";

                    if (!$conn->executeQuery($q)) {
                        echo "Unable to execute query.\n";
                        echo $conn->getErrorCode().': '.$conn->getErrorMessage()."\n";
                    }
                    $articleId++;
                }
            } else {
                echo "Unable to execute query.\n";
                echo $conn->getErrorCode().': '.$conn->getErrorMessage()."\n";
            }
        }
    } else {
        echo 'Unable to create the table '.$q00->getTableName()."\n";
        echo $conn->getErrorCode().': '.$conn->getErrorMessage()."\n";
    }
} else {
    echo 'Unable to create the table '.$q00->getTableName()."\n";
    echo $conn->getErrorCode().': '.$conn->getErrorMessage()."\n";
}
register_shutdown_function(function()
{
    echo "Dropping tables...\n";
    $conn = new phMysql\MySQLLink('localhost', 'root', '123456');
    $conn->setDB('testing_db');
    $q = new \phMysql\tests\ArticleQuery();
    $q->dropTable();
    echo $q->getQuery()."\n";

    if ($conn->executeQuery($q)) {
        echo 'Table '.$q->getTableName()." Dropped.\n";
        $q = new \phMysql\tests\UsersQuery();
        $q->dropTable();
        echo $q->getQuery()."\n";

        if ($conn->executeQuery($q)) {
            echo 'Table '.$q->getTableName()." Dropped.\n";
        } else {
            echo 'Unable to drop Table '.$q->getTableName().".\n";
            echo 'Error: '.$conn->getErrorCode().' - '.$conn->getErrorMessage()."\n";
        }
    } else {
        echo 'Unable to drop Table '.$q->getTableName().".\n";
        echo 'Error: '.$conn->getErrorCode().' - '.$conn->getErrorMessage()."\n";
    }
    unlink(ROOT.'tests'.DIRECTORY_SEPARATOR.'User2.php');
    echo "Done.\n";
});
