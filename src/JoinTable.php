<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phMysql;
use phMysql\MySQLTable;

/**
 * Description of JoinTable
 *
 * @author Eng.Ibrahim
 */
class JoinTable extends MySQLTable{
    private $leftTable;
    private $rightTable;
    private $joinType;
    public function __construct($leftTable,$rightTable,$tableName) {
        parent::__construct($tableName);
        if($leftTable instanceof MySQLTable){
            $this->leftTable = $leftTable;
        }
        else{
            $this->leftTable = new MySQLTable('left_table');
        }
        if($rightTable instanceof MySQLTable){
            $this->rightTable = $rightTable;
        }
        else{
            $this->rightTable = new MySQLTable('right_table');
        }
        $this->joinType = 'left';
    }
    public function getJoinType() {
        return $this->joinType;
    }
    /**
     * 
     * @return MySQLTable
     */
    public function getRightTable() {
        return $this->rightTable;
    }
    /**
     * 
     * @return MySQLTable
     */
    public function getLeftTable() {
        return $this->leftTable;
    }
    private function _addAndValidateColmns() {
        $commonColsKeys = [];
        $leftColsKeys = $this->getLeftTable()->colsKeys();
        $rightColsKeys = $this->getRightTable()->colsKeys();
        foreach ($rightColsKeys as $col){
            foreach ($leftColsKeys as $col2){
                if($col == $col2){
                    $commonColsKeys[] = $commonColsKeys;
                }
            }
        }
        foreach ($leftColsKeys as $col){
            if(in_array($col, $commonColsKeys)){
                $this->addColumn('left-'.$col, $this->getLeftTable()->getCol($col));
            }
            else{
                $this->addColumn($col, $this->getLeftTable()->getCol($col));
            }
        }
        foreach ($rightColsKeys as $col){
            if(in_array($col, $commonColsKeys)){
                $this->addColumn('right-'.$col, $this->getRightTable()->getCol($col));
            }
            else{
                $this->addColumn($col, $this->getRightTable()->getCol($col));
            }
        }
    }
}
