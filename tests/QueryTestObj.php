<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phMysql\tests;
use phMysql\MySQLQuery;
use phMysql\MySQLTable;
use phMysql\Column;
/**
 * An object which used as a base for testing the class 'MySQLQuery'.
 *
 * @author Ibrahim
 */
class QueryTestObj extends MySQLQuery{
    /**
     *
     * @var MySQLTable 
     */
    private $MySQLTable;
    public function __construct() {
        parent::__construct();
        $this->MySQLTable = new MySQLTable('first_table');
        $this->getStructure()->addColumn('first-col', new Column('col_00'));
        $this->getStructure()->addColumn('second-col', new Column('col_01'));
        $this->getStructure()->addColumn('third-col', new Column('col_02'));
        $this->getStructure()->addColumn('fourth-col', new Column('col_03'));
    }
    /**
     * 
     * @return MySQLTable
     */
    public function getStructure() {
        return $this->MySQLTable;
    }

}
