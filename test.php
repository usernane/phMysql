<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
require 'MySQLColumn.php';
require 'MySQLTable.php';
require 'MySQLQuery.php';
require 'MySQLLink.php';
use phMysql\MySQLColumn;
use phMysql\Table;
use phMysql\MySQLQuery;
use phMysql\MySQLLink;
class Q extends MySQLQuery{
    /**
     *
     * @var Table
     */
    private $table;
    public function __construct() {
        parent::__construct();
        $this->table = new Table('test_table');
        $this->table->addColumn('col-1', new MySQLColumn('x_col', 'varchar', 25));
        $this->table->addColumn('col-2', new MySQLColumn('yc_col', 'varchar', 225));
        $this->table->addColumn('col-3', new MySQLColumn('x_ncol', 'int', 3));
    }
    public function getStructure() {
        return $this->table;
    }

}

$link = new MySQLLink('localhost', 'root', 'xxxx');
if($link->isConnected()){
    print_message('Connected.');
    $result = $link->setDB('x_db');
    if($result === TRUE){
        print_message('Database Selected.');
        $q = new Q();
        $q->select();
        print_message($q->getQuery());
        $result = $link->executeQuery($q);
        if($result === TRUE){
            print_message('Query was executed.');
            var_dump($link->getColumn('col-3'));
            fetchRows($link);
        }
        else{
            print_message('Query not executed.');
            print_message('Error Number: '.$link->getErrorCode());
            print_message('Details: '.$link->getErrorMessage());
        }
    }
    else{
        print_message('Unable to select database.');
        print_message('Error Number: '.$link->getErrorCode());
        print_message('Details: '.$link->getErrorMessage());
    }
}
else{
    print_message('Error Number: '.$link->getErrorCode());
    print_message('Details: '.$link->getErrorMessage());
}

function print_message($message){
    echo '<pre>'.$message.'</pre>';
}
/**
 * 
 * @param DatabaseLink $link
 */
function fetchRows($link){
    while($row = $link->getRow()){
        ?><pre><?php print_r($row)?></pre><?php
    }
}