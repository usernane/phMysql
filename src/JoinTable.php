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
use phMysql\MySQLQuery;
use phMysql\MySQLTable;
use phMysql\Column;
use Exception;
/**
 * A class that represents a join table of two tables.
 *
 * @author Ibrahim
 * @version 1.0
 */
class JoinTable extends MySQLTable{
    /**
     * An array that contains supported join types.
     * The array has the following string values:
     * <ul>
     * <li>left</li>
     * <li>right</li>
     * </ul>
     * @since 1.0
     */
    const SUPPORTED_JOINS = [
        'left','right'
    ];
    /**
     * The left table of a join statement.
     * @var MySQLTable 
     * @since 1.0
     */
    private $leftTable;
    /**
     * The right table of a join statement.
     * @var MySQLTable 
     * @since 1.0
     */
    private $rightTable;
    /**
     * A string that represents join type
     * @var string
     * @since 1.0 
     */
    private $joinType;
    /**
     * A static variable which is used in default table name.
     * The value is incrumented every time a new instance of the 
     * class is created.
     * @var int
     * @since 1.0 
     */
    private static $JoinCount = 0;
    /**
     * Creates new instance of the class.
     * @param MySQLTable $left The left table of join statement.
     * @param MySQLTable $right The right table of join statement.
     * @param string $joinType A string that represents join type. It can be 
     * one of the following values:
     * <ul>
     * <li>join</li>
     * <li>left</li>
     * <li>right</li>
     * If the parameter is not provided or given type is not supported, then 
     * 'join' is used.
     * </ul>
     */
    public function __construct($left,$right,$joinType='join') {
        parent::__construct('Table'.self::$JoinCount);
        self::$JoinCount++;
        if($left instanceof MySQLTable && $right instanceof MySQLTable){
            $this->leftTable = $left;
            $this->rightTable = $right;
            $lType = strtolower(trim($joinType));
            if(in_array($lType, self::SUPPORTED_JOINS)){
                $this->joinType = $lType;
            }
            else{
                $this->joinType = 'join';
            }
            return;
        }
        throw new Exception('Given parameter(s) are not of type \'phpStructs\\MySQLTable\'.');
    }
    /**
     * Returns the right table of the join statement.
     * @return MySQLTable An object of type 'MySQlTable' that represents right 
     * table of the join statement.
     * @since 1.0
     */
    public function getRightTable() {
        return $this->rightTable;
    }
    /**
     * Returns the left table of the join statement.
     * @return MySQLTable An object of type 'MySQlTable' that represents left 
     * table of the join statement.
     * @since 1.0
     */
    public function getLeftTable() {
        return $this->leftTable;
    }
    /**
     * Returns a string that represents join type.
     * @return string The method might return one of the following values:
     * <ul>
     * <li>join</li>
     * <li>left join</li>
     * <li>right join</li>
     * </ul>
     * @since 1.0
     */
    public function getJoinType() {
        return $this->joinType;
    }
    /**
     * Returns a string that represents join clause of join statement.
     * @return string The returned string will have the following format:
     * <p>Table1 &lt;join_type&gt; Table2</p>
     * @since 1.0
     */
    public function getJoinStatement() {
        $retVal = $this->getLeftTable()->getName()
                .' '.$this->getJoinType().' '.$this->getRightTable()->getName();
        return $retVal;  
    }
    /**
     * Constructs a string that represents the 'on' condition of join statement.
     * @param array $cols An associative array of columns indices. The keys 
     * must be columns indices taken from left table and the values must be columns 
     * indices from the right table.
     * @param array $conditions An array that contains join conditions (such as 
     * '=' or '!='). If not provided or invalid value is given, '=' will be used 
     * as a default value.
     * @param array $joinOps An array that contains string which contains conditions 
     * join operators ('and' or 'or'). If not provided or invalid join operators 
     * are given, 'and' will be used as a default value.
     * @return string A string that represents the 'on' condition of join statement. 
     * If no condition is constructed, the method will return empty string.
     * @since 1.0
     */
    public function getOnCondition($cols,$conditions=[],$joinOps=[]) {
        if(gettype($cols) == 'array'){
            if(gettype($conditions) != 'array'){
                $conditions = [];
            }
            if(gettype($joinOps) != 'array'){
                $joinOps = [];
            }
            $conditionsCount = count($conditions);
            $colsCount = count($cols);
            $joinOpsCount = count($joinOps);
            if($colsCount > $conditionsCount){
                while ($conditionsCount != $colsCount){
                    $conditions[] = '=';
                }
            }
            if($conditionsCount - 1 > $conditionsCount){
                while ($conditionsCount - 1 != $joinOpsCount){
                    $joinOps[] = 'and';
                }
            }
            $index = 0;
            $onCond = '';
            foreach ($cols as $leftColName => $rightColName){
                $leftColObj = $this->getLeftTable()->getCol($leftColName);
                if($leftColObj instanceof Column){
                    $rightColObj = $this->getRightTable()->getCol($rightColName);
                    if($rightColObj instanceof Column){
                        $cond = strtolower(trim($conditions[$index]));
                        if($cond == '=' || 
                           $cond == '!=' || 
                           $cond == '<' || 
                           $cond == '>' ||
                           $cond == '<=' || 
                           $cond == '>='){
                            //do nothing
                        }
                        else{
                            $cond = '=';
                        }
                        if($index != 0){
                            $joinOp = strtolower(trim($joinOps[$index]));
                            if($joinOp == 'and' || $joinOp == 'or'){
                                
                            }
                            else{
                                $joinOp = 'and';
                            }
                            $onCond .= $joinOp.' '.$this->getLeftTable()->getName().'.'.$leftColObj->getName()
                                .' '.$cond.' '
                                .$this->getRightTable()->getName().'.'.$rightColObj->getName();
                        }
                        else{
                            $onCond .= $this->getLeftTable()->getName().'.'.$leftColObj->getName()
                                .' '.$cond.' '
                                .$this->getRightTable()->getName().'.'.$rightColObj->getName();
                        }
                    }
                }
                $index++;
            }
            if(strlen($onCond) !== 0){
                return 'on ('.$onCond.')';
            }
        }
        return '';
    }
}
