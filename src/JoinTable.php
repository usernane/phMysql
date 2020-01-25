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
use phMysql\MySQLColumn;
use phMysql\MySQLQuery;
/**
 * Experimental class. DO NOT USE.
 *
 * @author Ibrahim
 * @version 1.0
 */
class JoinTable extends MySQLTable{
    /**
     * The number of joins which was performed before.
     * @var type 
     */
    private static $JoinsCount = 0;
    private $leftTable;
    private $rightTable;
    private $joinType;
    private $joinCond;
    private $selectCols;
    /**
     * Creates new instance of the class.
     * @param MySQLQuery|MySQLTable $leftTable The left table.
     * @param MySQLQuery|MySQLTable $rightTable The right table.
     * @param string $tableName An optional name for the table which will be 
     * generated from the join. If not given, a name will be generated automatically.
     * @since 1.0
     */
    public function __construct($leftTable,$rightTable,$tableName=null) {
        parent::__construct();
        if(!$this->setName($tableName)){
            $this->setName('T'.self::$JoinsCount);
        }
        self::$JoinsCount++;
        if($leftTable instanceof MySQLTable){
            $this->leftTable = $leftTable;
        }
        else if($leftTable instanceof MySQLQuery){
            $this->leftTable = $leftTable->getTable();
        }
        else{
            $this->leftTable = new MySQLTable('left_table');
        }
        if($rightTable instanceof MySQLTable){
            $this->rightTable = $rightTable;
        }
        else if($leftTable instanceof MySQLQuery){
            $this->rightTable = $rightTable->getTable();
        }
        else{
            $this->rightTable = new MySQLTable('right_table');
        }
        $this->joinType = 'left';
        $this->selectCols = '';
        $this->_addAndValidateColmns();
    }
    /**
     * Sets the type of the join that will be performed.
     * @param string $type A string that represents join type. Possible values
     * are: 
     * <ul>
     * <li>left</li>
     * <li>right</li>
     * <li>natural</li>
     * <li>natural left</li>
     * <li>natural right</li>
     * <li>cross</li>
     * <li>join</li>
     * </ul>
     * @since 1.0
     */
    public function setJoinType($type) {
        $lType = strtolower(trim($type));
        if($lType == 'left' || $lType == 'natural left' ||
           $lType == 'right' || $lType == 'natural right'|| 
           $lType == 'cross' || $lType == 'natural' || $lType == 'join'){
            $this->joinType = $lType;
        }
    }
    /**
     * Returns a string that represents join condition.
     * @return string A string that represents join condition.
     * @since 1.0
     */
    public function getJoinCondition() {
        return $this->joinCond;
    }
    /**
     * Sets the condition at which the two tables will be joined on.
     * @param array $cols An associative array of columns. The indices should be 
     * the names of columns keys taken from left table and the values should be 
     * columns keys taken from right table.
     * @param string $conds An optional array of join conditions. It can have 
     * values like '=' or '!='.
     * @param string $joinOps An array that contains conditions which are used 
     * to join the conditions in case of multiple columns joins. It can have 
     * one of two values, 'and' or 'or'.
     * @since 1.0
     */
    public function setJoinCondition($cols,$conds=[],$joinOps=[]) {
        if(gettype($cols) == 'array'){
            while (count($conds) < count($cols)){
                $conds[] = '=';
            }
            while (count($joinOps) < count($cols)){
                $joinOps[] = 'and';
            }
            $index = 0;
            foreach ($cols as $leftCol => $rightCol){
                $leftColObj = $this->getLeftTable()->getCol($leftCol);
                if($leftColObj instanceof MySQLColumn){
                    $rightColObj = $this->getRightTable()->getCol($rightCol);
                    if($rightColObj instanceof MySQLColumn){
                        if($rightColObj->getType() == $leftColObj->getType()){
                            $cond = $conds[$index];
                            if(strlen($this->joinCond) == 0){
                                $this->joinCond = 'on '. $this->getLeftTable()->getName().'.'
                                       . $leftColObj->getName().' '.$cond.' '
                                       . $this->getRightTable()->getName().'.'
                                       . $rightColObj->getName();
                            }
                            else {
                                $joinOp = $joinOps[$index - 1];
                                if($joinOp != 'and' && $joinOp != 'or'){
                                    $joinOp = 'and';
                                }
                                $this->joinCond .= 
                                       ' '.$joinOp.' '.$this->getLeftTable()->getName().'.'
                                       . $leftColObj->getName().' '.$cond.' '
                                       . $this->getRightTable()->getName().'.'
                                       . $rightColObj->getName();
                            }
                        }
                    }
                }
                $index++;
            }
        } 
    }
    /**
     * Returns a string that represents the type of the join that will 
     * be performed.
     * @return string Possible return values are:
     * <ul>
     * <li>left</li>
     * <li>right</li>
     * <li>cross</li>
     * </ul>
     * @since 1.0
     */
    public function getJoinType() {
        return $this->joinType;
    }
    /**
     * Returns the right table of the join.
     * @return MySQLTable An instance of the class 'MySQLTable' that represents 
     * right table of the join.
     * @since 1.0
     */
    public function getRightTable() {
        return $this->rightTable;
    }
    /**
     * Returns the left table of the join.
     * @return MySQLTable An instance of the class 'MySQLTable' that represents 
     * left table of the join.
     * @since 1.0
     */
    public function getLeftTable() {
        return $this->leftTable;
    }
    /**
     * @since 1.0
     */
    private function _addAndValidateColmns() {
        //collect common keys btween the two tables.
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
        //collect common columns names in the two tables.
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
        //build an array that contains all columns in the joined table.
        $colsArr = [];
        foreach ($leftColsKeys as $col){
            if(in_array($col, $commonColsKeys)){
                $colsArr['left-'.$col] = clone $this->getLeftTable()->getCol($col);
            }
            else{
                $colsArr[$col] = clone $this->getLeftTable()->getCol($col);
            }
        }
        foreach ($rightColsKeys as $col){
            if(in_array($col, $commonColsKeys)){
                $colsArr['right-'.$col] = clone $this->getRightTable()->getCol($col);
            }
            else{
                $colsArr[$col] = clone $this->getRightTable()->getCol($col);
            }
        }
        //rename common columns.
        $index = 0;
        $leftCount = count($leftCols);
        $totalCount = $leftCount + count($rightCols);
        $hasCommon = false;
        foreach ($colsArr as $colkey => $colObj){
            if($colObj instanceof MySQLColumn){
                if(in_array($colObj->getName(), $commonCols)){
                    $hasCommon = true;
                    if($index < $leftCount){
                        if($index + 1 == $totalCount){
                            $this->selectCols .= $colObj->getName(true).' as left_'.$colObj->getName()."\n";
                        }
                        else{
                            $this->selectCols .= $colObj->getName(true).' as left_'.$colObj->getName().",\n";
                        }
                        $colObj->setName('left_'.$colObj->getName());
                    }
                    else{
                        if($index + 1 == $totalCount){
                            $this->selectCols .= $colObj->getName(true).' as right_'.$colObj->getName()."\n";
                        }
                        else{
                            $this->selectCols .= $colObj->getName(true).' as right_'.$colObj->getName().",\n";
                        }
                        $colObj->setName('right_'.$colObj->getName());
                    }
                }
                else{
                    if($hasCommon){
                        if($index + 1 == $totalCount){
                            $this->selectCols .= $colObj->getName(true)."\n";
                        }
                        else{
                            $this->selectCols .= $colObj->getName(true).",\n";
                        }
                    }
                }
            }
            $this->addColumn($colkey, $colObj);
            $index++;
        }
        if(!$hasCommon){
            $this->selectCols = '*';
        }
    }
    /**
     * 
     * @return type
     * @since 1.0
     */
    public function getSelectStatement() {
        return $this->selectCols;
    }
}
