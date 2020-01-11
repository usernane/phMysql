<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phMysql\tests;
use phMysql\MySQLQuery;
use phMysql\tests\ArticleQuery;
use phMysql\tests\UsersQuery;
use phMysql\JoinTable;
/**
 * Description of ArticleJoinUserQuery
 *
 * @author Ibrahim
 */
class ArticleJoinUserQuery extends MySQLQuery{
    private $table;
    public function __construct() {
        parent::__construct();
        //$this->table = self::join('phMysql\tests\ArticleQuery', 'phMysql\tests\UsersQuery');
    }
    /**
     * 
     * @return MySQLTable
     */
    public function getStructure(){
        return $this->table;
    }

}
