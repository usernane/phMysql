<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phMysql\tests;
use phMysql\MySQLQuery;
/**
 * An object which used as a base for testing the class 'MySQLQuery'.
 *
 * @author Ibrahim
 */
class QueryTestObj extends MySQLQuery{
    public function __construct() {
        parent::__construct('first_table');
        $this->getTable()->addColumns([
            'first-col'=>[
                'name'=>'col_00'
            ],
            'second-col'=>[
                'name'=>'col_01'
            ],
            'third-col'=>[
                'name'=>'col_02'
            ],
            'fourth-col'=>[
                'name'=>'col_03'
            ]
        ]);
    }
}
