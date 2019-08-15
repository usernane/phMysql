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
/**
 * A class that represents a join statement between two tables.
 *
 * @author Ibrahim
 */
class JoinTable extends MySQLTable{
    const SUPPORTED_JOINS = [
        'left','right'
    ];
    private $leftTable;
    private $rightTable;
    private $joinType;
    private static $JoinCount = 0;
    public function __construct($left,$right,$joinType='join') {
        parent::__construct('Table'.self::$JoinCount);
        self::$JoinCount++;
        if($left instanceof MySQLTable && $right instanceof MySQLTable){
            $this->leftTable = $left;
            $this->rightTable = $right;
        }
        $lType = strtolower(trim($joinType));
        if(in_array($lType, self::SUPPORTED_JOINS)){
            $this->joinType = $lType;
        }
        else{
            $this->joinType = 'join';
        }
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
    public function getJoinType() {
        return $this->joinType;
    }
    public function getJoinStatement() {
        $retVal = $this->getLeftTable()->getName()
                .' '.$this->getJoinType().' '.$this->getRightTable()->getName();
        return $retVal;  
    }
    
    public function getOnCondition($cols,$conditions=[],$joinOps=[]) {
        if(gettype($cols) == 'array' && gettype($conditions) == 'array'){
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
