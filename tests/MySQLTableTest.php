<?php
/**
 * MIT License
 *
 * Copyright (c) 2019 Ibrahim BinAlshikh, phMysql library.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace phMysql\tests;
use phMysql\MySQLTable;
use phMysql\Column;
use PHPUnit\Framework\TestCase;
use phMysql\tests\ArticleQuery;
/**
 * A set of test units for testing the class 'MySQLTable'.
 *
 * @author Ibrahim
 */
class MySQLTableTest extends TestCase{
    /**
     * @test
     */
    public function testAddColumn00() {
        $table = new MySQLTable();
        $this->assertTrue($table->addColumn('new-col', new Column()));
        $this->assertFalse($table->addColumn('new-col-2', new Column()));
        $this->assertTrue($table->addColumn('new-col-2', new Column('col_2', 'varchar')));
        $this->assertFalse($table->addColumn('new-col-2', new Column('col_3', 'varchar')));
        return $table;
    }
    /**
     * 
     * @param MySQLTable $table
     * @depends testAddColumn00
     */
    public function testHasCol00($table) {
        $this->assertTrue($table->hasColumn('new-col'));
        $this->assertTrue($table->hasColumn(' new-col '));
        $this->assertTrue($table->hasColumn('new-col-2'));
    }
    /**
     * @test
     */
    public function testAddColumn01() {
        $table = new MySQLTable();
        $this->assertTrue($table->addColumn(' new-col ', new Column()));
        $this->assertFalse($table->addColumn('invalid key', new Column('col_2')));
        return $table;
    }
    /**
     * 
     * @param MySQLTable $table
     * @depends testAddColumn00
     */
    public function testHasCol01($table) {
        $this->assertTrue($table->hasColumn('new-col'));
        $this->assertFalse($table->hasColumn('invalid key'));
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $table = new MySQLTable();
        $this->assertEquals('table',$table->getName());
    }
    /**
     * @test
     */
    public function testAddColumn02() {
        $table = new MySQLTable();
        $table->addDefaultCols();
        $this->assertFalse($table->addColumn('id', new Column('user_id')));
        $this->assertFalse($table->addColumn('user-id', new Column('id')));
        $this->assertFalse($table->addColumn('c-on', new Column('created_on')));
        $this->assertFalse($table->addColumn('created-on', new Column('cr_date')));
        $this->assertFalse($table->addColumn('last-u', new Column('last_updated')));
        $this->assertFalse($table->addColumn('last-updated', new Column('l_updated')));
        return $table;
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $table = new MySQLTable('valid_name');
        $this->assertEquals('valid_name',$table->getName());
    }
    /**
     * @test
     */
    public function testConstructor02() {
        $table = new MySQLTable('    another_Valid_Name    ');
        $this->assertEquals('another_Valid_Name',$table->getName());
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $table = new MySQLTable('invalid name');
        $this->assertEquals('table',$table->getName());
    }
    /**
     * @test
     */
    public function testConstructor04() {
        $table = new MySQLTable('0invalid_name');
        $this->assertEquals('table',$table->getName());
    }
    /**
     * @test
     */
    public function testConstructor05() {
        $table = new MySQLTable('invalid-name');
        $this->assertEquals('table',$table->getName());
    }
    /**
     * @test
     */
    public function testAddDefaultCols00() {
        $table = new MySQLTable();
        $table->addDefaultCols();
        $this->assertEquals(3,count($table->columns()));
        $this->assertTrue($table->hasColumn('id'));
        $this->assertTrue($table->hasColumn('created-on'));
        $this->assertTrue($table->hasColumn('last-updated'));
    }
    /**
     * @test
     */
    public function testAddDefaultCols01() {
        $table = new MySQLTable();
        $table->addDefaultCols([]);
        $this->assertEquals(0,count($table->columns()));
        $this->assertFalse($table->hasColumn('id'));
        $this->assertFalse($table->hasColumn('created-on'));
        $this->assertFalse($table->hasColumn('last-updated'));
    }
    /**
     * @test
     */
    public function testAddDefaultCols02() {
        $table = new MySQLTable();
        $table->addDefaultCols([
            'id'=>[
                'key-name'=>'user-id',
                'db-name'=>'user_id'
            ]
        ]);
        $this->assertEquals(1,count($table->columns()));
        $this->assertFalse($table->hasColumn('id'));
        $this->assertTrue($table->hasColumn('user-id'));
        $this->assertFalse($table->hasColumn('created-on'));
        $this->assertFalse($table->hasColumn('last-updated'));
    }
    /**
     * @test
     */
    public function testAddDefaultCols03() {
        $table = new MySQLTable();
        $table->addDefaultCols([
            'id'=>[
                'key-name'=>'user id',
                'db-name'=>'user_id'
            ]
        ]);
        $this->assertEquals(1,count($table->columns()));
        $this->assertFalse($table->hasColumn('user id'));
        $this->assertTrue($table->hasColumn('id'));
        $this->assertEquals('user_id',$table->getCol('id')->getName());
    }
    /**
     * @test
     */
    public function testAddDefaultCols04() {
        $table = new MySQLTable();
        $table->addDefaultCols([
            'id'=>[
                'key-name'=>'an-id',
                'db-name'=>'user id'
            ]
        ]);
        $this->assertEquals(1,count($table->columns()));
        $this->assertEquals('id',$table->getCol('an-id')->getName());
    }
    /**
     * @test
     */
    public function testAddDefaultCols05() {
        $table = new MySQLTable();
        $table->addDefaultCols([
            'created-on'=>[
                'key-name'=>'created on',
                'db-name'=>'cr_date'
            ]
        ]);
        $this->assertEquals(1,count($table->columns()));
        $this->assertFalse($table->hasColumn('created on'));
        $this->assertTrue($table->hasColumn('created-on'));
        $this->assertEquals('cr_date',$table->getCol('created-on')->getName());
    }
    /**
     * @test
     */
    public function testAddDefaultCols06() {
        $table = new MySQLTable();
        $table->addDefaultCols([
            'created-on'=>[
                'key-name'=>'a-date',
                'db-name'=>'created on'
            ]
        ]);
        $this->assertEquals(1,count($table->columns()));
        $this->assertEquals('created_on',$table->getCol('a-date')->getName());
    }
    /**
     * @test
     */
    public function testAddDefaultCols07() {
        $table = new MySQLTable();
        $table->addDefaultCols([
            'last-updated'=>[
                'key-name'=>'updated on',
                'db-name'=>'u_date'
            ]
        ]);
        $this->assertEquals(1,count($table->columns()));
        $this->assertFalse($table->hasColumn('updated on'));
        $this->assertTrue($table->hasColumn('last-updated'));
        $this->assertEquals('u_date',$table->getCol('last-updated')->getName());
    }
    /**
     * @test
     */
    public function testAddDefaultCols08() {
        $table = new MySQLTable();
        $table->addDefaultCols([
            'created-on'=>[
                'key-name'=>'a-date',
                'db-name'=>'updated_on'
            ]
        ]);
        $this->assertEquals(1,count($table->columns()));
        $this->assertEquals('updated_on',$table->getCol('a-date')->getName());
    }
    /**
     * @test
     */
    public function testGetColIndex() {
        $table = new MySQLTable();
        $table->addDefaultCols();
        $this->assertEquals(-1,$table->getColIndex('not-exist'));
        $this->assertEquals(0,$table->getColIndex('id'));
        $this->assertEquals(1,$table->getColIndex('created-on '));
        $this->assertEquals(2,$table->getColIndex(' last-updated'));
    }
    /**
     * @test
     */
    public function testGetColByKey() {
        $table = new MySQLTable();
        $table->addDefaultCols();
        $this->assertNull($table->getCol('not-exist'));
        $this->assertEquals('id',$table->getCol('id')->getName());
        $this->assertEquals('timestamp',$table->getCol('created-on ')->getType());
        $this->assertEquals('datetime',$table->getCol(' last-updated')->getType());
    }
    /**
     * @test
     */
    public function testGetCreatePrimaryKeyStatement00() {
        $table = new MySQLTable();
    }
    /**
     * @test
     */
    public function testRemoveColumn00() {
        $query = new ArticleQuery();
        $table = $query->getStructure();
        $this->assertTrue($table->removeColumn('author-id'));
        $this->assertEquals(5,count($table->columns()));
        $this->assertFalse($table->hasColumn('author-id'));
    }
    /**
     * @test
     */
    public function testRemoveColumn01() {
        $query = new ArticleQuery();
        $table = $query->getStructure();
        $this->assertTrue($table->removeColumn(0));
        $this->assertEquals(5,count($table->columns()));
        $this->assertFalse($table->hasColumn('article-id'));
    }
    /**
     * @test
     */
    public function testRemoveColumn02() {
        $query = new ArticleQuery();
        $table = $query->getStructure();
        $this->assertTrue($table->removeColumn(3));
        $this->assertEquals(5,count($table->columns()));
        $this->assertFalse($table->hasColumn('author-id'));
    }
    /**
     * @test
     */
    public function testRemoveColumn03() {
        $query = new ArticleQuery();
        $table = $query->getStructure();
        $this->assertTrue($table->removeColumn(4));
        $this->assertEquals(5,count($table->columns()));
        $this->assertFalse($table->hasColumn('author-name'));
    }
    /**
     * @test
     */
    public function testRemoveColumn04() {
        $query = new ArticleQuery();
        $table = $query->getStructure();
        $this->assertFalse($table->removeColumn(20));
        $this->assertEquals(6,count($table->columns()));
    }
}
