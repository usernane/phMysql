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

/**
 * Experimental class. DO NOT USE.
 *
 * @author Ibrahim
 * @version 1.0
 */
class JoinTable extends MySQLTable {
    /**
     * An array that contains the names of the columns which are shared between 
     * joined tables.
     * @var array
     * @since 1.0 
     */
    private $commonCols;
    private $hasCommon;
    private $joinCond;
    private $joinType;
    private $keysMap;
    private $leftTable;
    private $rightTable;
    /**
     * Creates new instance of the class.
     * @param MySQLQuery|MySQLTable $leftTable The left table. It can be an 
     * instance of the class 'MySQLQuery' or 'MySQLTable'.
     * @param MySQLQuery|MySQLTable $rightTable The right table. It can be an 
     * instance of the class 'MySQLQuery' or 'MySQLTable'.
     * @param string $tableName An optional name for the table which will be 
     * be given to the joined table. If not given, a name will be generated automatically.
     * @param array $keysMap An optional array that can have two associative 
     * arrays. One with key 'left' and the other is with key 'right'. Each one 
     * of the two arrays can have new names for table columns keys. The indices 
     * in each array are the original keys names taken from joined tables and 
     * the values are the new keys which will exist in the joined table. It is 
     * simply used to map joined keys with new keys which will exist in the new 
     * joined table.
     * @since 1.0
     */
    public function __construct($leftTable,$rightTable,$tableName = null,$keysMap = []) {
        parent::__construct();
        $this->hasCommon = false;

        if (!$this->setName($tableName)) {
            $this->setName('Temp'.strtoupper(substr(hash('sha256',rand()), 0, 5)));
        }

        if ($leftTable instanceof MySQLTable) {
            $this->leftTable = $leftTable;
        } else if ($leftTable instanceof MySQLQuery) {
            $this->leftTable = $leftTable->getTable();
        } else {
            $this->leftTable = new MySQLTable('left_table');
        }

        if ($rightTable instanceof MySQLTable) {
            $this->rightTable = $rightTable;
        } else if ($leftTable instanceof MySQLQuery) {
            $this->rightTable = $rightTable->getTable();
        } else {
            $this->rightTable = new MySQLTable('right_table');
        }
        $this->joinType = 'left';
        $this->commonCols = [];
        $this->keysMap = [
            'left' => [],
            'right' => []
        ];
        $this->_addAndValidateColmns($keysMap);
    }
    /**
     * Returns the column object given the key that it was stored in.
     * The method will first check if the given key is mapped to one of the 
     * joined tables. If it was mapped, the method will return the column 
     * taken from the joined table.
     * @param string $key The name of the column key.
     * @return MySQLColumn|null An object of type Column is returned if the given 
     * column was found. null in case of no column was found.
     * @since 1.0
     */
    public function getCol($key) {
        if (isset($this->keysMap['left'][$key])) {
            return $this->getLeftTable()->getCol($this->keysMap['left'][$key]);
        } else if (isset($this->keysMap['right'][$key])) {
            return $this->getRightTable()->getCol($this->keysMap['right'][$key]);
        }

        return $this->getJoinCol($key);
    }
    public function getCommonColsNames() {
        return $this->commonCols;
    }
    /**
     * Returns the column object given the key that it was stored in.
     * This method is used to skip mapping check which is used by the 
     * method Table::getCol().
     * @param string $key The name of the column key.
     * @return MySQLColumn|null An object of type Column is returned if the given 
     * column was found. null in case of no column was found.
     * @since 1.0
     */
    public function getJoinCol($key) {
        return parent::getCol($key);
    }
    /**
     * Returns a string that represents join condition.
     * @return string|null A string that represents join condition. If join 
     * condition is not set, the method will return null.
     * @since 1.0
     */
    public function getJoinCondition() {
        return $this->joinCond;
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
     * Default return value is 'left'.
     * @since 1.0
     */
    public function getJoinType() {
        return $this->joinType;
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
     * Returns the right table of the join.
     * @return MySQLTable An instance of the class 'MySQLTable' that represents 
     * right table of the join.
     * @since 1.0
     */
    public function getRightTable() {
        return $this->rightTable;
    }
    /**
     * Checks if the two joined tables has common columns between them.
     * A two tables will have common columns if the two share at least one 
     * column with the same name in the database.
     * @return boolean If two tables share same column name, the method will 
     * return true. If not, it will return false.
     * @since 1.0
     */
    public function hasCommon() {
        return $this->hasCommon;
    }
    /**
     * Checks if a column has the same name in the left and the right table.
     * @param string $colName The name of the column as it appears in the 
     * database.
     * @return boolean If the column is common between the two tables, the 
     * method will return true. Other than that, the method will return false.
     * @since 1.0
     */
    public function isCommon($colName) {
        $trimmed = trim($colName);

        return in_array($trimmed, $this->getCommonColsNames());
    }
    /**
     * Sets the condition at which the two tables will be joined on.
     * @param array $cols An associative array of columns. The indices should be 
     * the names of columns keys taken from left table and the values should be 
     * columns keys taken from right table.
     * @param string $conds An optional array of join conditions. It can have 
     * values like '=' or '!='.
     * @since 1.0
     */
    public function setJoinCondition($cols, $conds = []) {
        if (gettype($cols) == 'array') {
            while (count($conds) < count($cols)) {
                $conds[] = '=';
            }
            $joinOps = [];

            while (count($joinOps) < count($cols)) {
                $joinOps[] = 'and';
            }
            $index = 0;
            $this->joinCond = null;
            $leftTable = $this->getLeftTable();
            $rightTable = $this->getRightTable();
            $tempJoinStr = '';

            foreach ($cols as $leftCol => $rightCol) {
                $leftColObj = $this->_getCol($leftTable, $leftCol);
                $rightColObj = $this->_getCol($rightTable, $rightCol);
 
                if ($leftColObj instanceof MySQLColumn 
                    && $rightColObj instanceof MySQLColumn 
                    && $rightColObj->getType() == $leftColObj->getType()) {
                    $cond = $conds[$index];
                    $tempJoinStr .= 
                           ' '.$this->_getJoinOp($joinOps, $index).' '.$leftTable->getName().'.'
                           .$leftColObj->getName().' '.$cond.' '
                           .$rightTable->getName().'.'
                           .$rightColObj->getName();
                }
                $index++;
            }
        }
        $this->joinCond = 'on '.trim($tempJoinStr);
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

        if ($lType == 'left' || $lType == 'natural left' ||
           $lType == 'right' || $lType == 'natural right' || 
           $lType == 'cross' || $lType == 'natural' || $lType == 'join') {
            $this->joinType = $lType;
        }
    }
    /**
     * @since 1.0
     */
    private function _addAndValidateColmns($keysMap = []) {
        //collect common keys btween the two tables.
        $commonColsKeys = $this->_getCommonColsKeys();

        //collect common columns names in the two tables.
        $this->commonCols = $this->_getCommonCols();

        //build an array that contains all columns in the joined table.
        $colsArr = $this->_getColsArr($commonColsKeys);


        //rename common columns.
        $index = 0;
        $leftCount = count($this->getLeftTable()->columns());
        $this->hasCommon = false;
        $commonNamesArr = $this->getCommonColsNames();

        foreach ($colsArr as $colkey => $colObj) {
            $isAdded = false;
            $colName = $colObj->getName();

            if (in_array($colName, $commonNamesArr)) {
                $this->hasCommon = true;
                $isAdded = false;

                if ($index < $leftCount) {
                    $colObj->setName('left_'.$colName);
                    $isAdded = $this->_addWithAlias($colkey, $colObj, $keysMap, 'left');
                } else {
                    $colObj->setName('right_'.$colName);
                    $isAdded = $this->_addWithAlias($colkey, $colObj, $keysMap, 'right');
                }
            }

            if (!$isAdded) {
                if ($index < $leftCount) {
                    $isAdded = $this->_addWithAlias($colkey, $colObj, $keysMap, 'left');
                } else {
                    $isAdded = $this->_addWithAlias($colkey, $colObj, $keysMap, 'right');
                }

                if (!$isAdded) {
                    $this->addColumn($colkey, $colObj);
                }
            }
            $index++;
        }
    }
    private function _addWithAlias($colkey,$colObj,&$aliasArr,$leftOrRight) {
        $isAdded = false;
        $colsNames = isset($aliasArr[$leftOrRight]) && gettype($aliasArr[$leftOrRight]) == 'array' ? 
                $aliasArr[$leftOrRight] : [];

        foreach ($colsNames as $originalName => $newName) {
            $originalNameC = $leftOrRight.'-'.$originalName;

            if ($originalNameC == $colkey || $originalName == $colkey) {
                unset($aliasArr[$leftOrRight][$originalName]);
                $isAdded = $this->addColumn($newName, $colObj);

                if ($isAdded) {
                    $this->keysMap[$leftOrRight][$newName] = $originalName;
                }
                break;
            }
        }

        return $isAdded;
    }
    /**
     * 
     * @param MySQLTable $table
     * @param string $colName
     * @return MySQLColumn|null
     */
    private function _getCol($table, $colName) {
        if ($table instanceof JoinTable) {
            $colObj = $table->getJoinCol($colName);
        } else {
            $colObj = $table->getCol($colName);
        }

        return $colObj;
    }
    private function _getColsArr($commonColsArr) {
        $colsArr = $this->_getColsArrHelper(true, $commonColsArr);

        return array_merge($colsArr, $this->_getColsArrHelper(false, $commonColsArr));
    }
    private function _getColsArrHelper($isLeft, $commonColsArr) {
        if ($isLeft) {
            $table = $this->getLeftTable();
            $prefix = 'left-';
        } else {
            $table = $this->getRightTable();
            $prefix = 'right-';
        }
        $colsArr = [];

        foreach ($table->colsKeys() as $col) {
            if (in_array($col, $commonColsArr)) {
                if ($table instanceof JoinTable) {
                    $newCol = clone $table->getJoinCol($col);
                    $colsArr[$prefix.$col] = $newCol;
                } else {
                    $colsArr[$prefix.$col] = clone $table->getCol($col);
                }
            } else if ($table instanceof JoinTable) {
                $newCol = clone $table->getJoinCol($col);
                $colsArr[$col] = $newCol;
            } else {
                $colsArr[$col] = clone $table->getCol($col);
            }
        }

        return $colsArr;
    }
    private function _getCommonCols() {
        $rightCols = $this->getRightTable()->getColsNames();
        $leftCols = $this->getLeftTable()->getColsNames();
        $commonColsArr = [];

        foreach ($rightCols as $col) {
            foreach ($leftCols as $col2) {
                if ($col == $col2) {
                    $commonColsArr[] = $col2;
                }
            }
        }

        return $commonColsArr;
    }
    private function _getCommonColsKeys() {
        $commonColsKeys = [];
        $leftColsKeys = $this->getLeftTable()->colsKeys();
        $rightColsKeys = $this->getRightTable()->colsKeys();

        foreach ($rightColsKeys as $col) {
            foreach ($leftColsKeys as $col2) {
                if ($col == $col2) {
                    $commonColsKeys[] = $col2;
                }
            }
        }

        return $commonColsKeys;
    }
    private function _getJoinOp($joinOps, $index) {
        if ($index != 0) {
            $joinOp = $joinOps[$index - 1];

            if ($joinOp != 'and' && $joinOp != 'or') {
                $joinOp = 'and';
            }
        } else {
            $joinOp = '';
        }

        return $joinOp;
    }
}
