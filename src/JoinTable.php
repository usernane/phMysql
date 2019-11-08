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
namespace phMysql;
use phMysql\MySQLTable;

/**
 * Experimental class. DO NOT USE.
 *
 * @author Ibrahim
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
