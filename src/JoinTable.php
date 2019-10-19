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
        $this->_addAndValidateColmns();
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
                    $commonColsKeys[] = $col2;
                }
            }
        }
        $commonCols = [];
        $rightCols = $this->getRightTable()->getColsNames();
        $leftCols = $this->getLeftTable()->getColsNames();
        foreach ($rightCols as $col){
            foreach ($leftCols as $col2){
                if($col == $col2){
                    $commonCols[] = $col2;
                }
            }
        }
        $colsArr = [];
        foreach ($leftColsKeys as $col){
            if(in_array($col, $commonColsKeys)){
                $colsArr['left-'.$col] = $this->getLeftTable()->getCol($col);
            }
            else{
                $colsArr[$col] = $this->getLeftTable()->getCol($col);
            }
        }
        foreach ($rightColsKeys as $col){
            if(in_array($col, $commonColsKeys)){
                $colsArr['right-'.$col] = $this->getRightTable()->getCol($col);
            }
            else{
                $colsArr[$col] = $this->getRightTable()->getCol($col);
            }
        }
        $index = 0;
        $leftCount = count($leftCols);
        foreach ($colsArr as $colkey => $colObj){
            if($colObj instanceof Column){
                if(in_array($colObj->getName(), $commonCols)){
                    if($index < $leftCount){
                        $colObj->setName('left_'.$colObj->getName());
                    }
                    else{
                        $colObj->setName('right_'.$colObj->getName());
                    }
                }
            }
            $this->addColumn($colkey, $colObj);
            $index++;
        }
    }
}
