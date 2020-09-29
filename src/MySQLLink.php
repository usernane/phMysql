<?php
/**
 * MIT License
 *
 * Copyright (c) 2019, phMysql library.
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
namespace webfiori\phMysql;

use mysqli;
/**
 * A class that is used to connect to MySQL database. It works as an interface 
 * for <b>mysqli</b>.
 * @author Ibrahim
 * @version 1.3.2
 */
class MySQLLink {
    /**
     * The index of the current row in result set.
     * @var int 
     * @since 1.3
     */
    private $currentRow;
    /**
     * The database instance that will be selected once the connection is 
     * established.
     * @var string 
     * @since 1.0
     */
    private $db;
    /**
     * The name of database host. It can be an IP address (such as '134.123.111.3') or 
     * a URL.
     * @var string 
     * @since 1.0
     */
    private $host;
    /**
     * The last generated error message.
     * @var string 
     * @since 1.0
     */
    private $lastErrorMessage = 'NO ERRORS';
    /**
     * The last generated error number.
     * @var int 
     * @since 1.0
     */
    private $lastErrorNo;
    /**
     * The last executed query.
     * @var MySQLQuer An object of type 'MySQLQuery.
     * @since 1.0
     */
    private $lastQuery;
    /**
     * Database connection. It is simply the handler for executing queries.
     * @var type 
     * @since 1.0
     */
    private $link;
    /**
     * The password of the database user.
     * @var string 
     * @since 1.0
     */
    private $pass;
    /**
     * Port number of MySQL server.
     * @var int 
     * @since 1.3.1
     */
    private $portNum;
    /**
     * The result of executing last query, <b>mysqli_result</b> object
     * @var mysqli_result|null 
     * @since 1.0
     */
    private $result;
    /**
     * An array which contains rows from executing MySQL query.
     * @var array|null
     * @since 1.2 
     */
    private $resultRows;
    /**
     * The name of database user (such as 'Admin').
     * @var string 
     * @since 1.0
     */
    private $user;
    /**
     * Creates new instance of the class.
     * @param string $host Database host address.
     * @param string $user The username of database user.
     * @param string $password The password of the user.
     * @param int $port The number of the port that is used to connect to 
     * database host. Default is 3306.
     */
    public function __construct($host, $user, $password,$port = 3306) {
        set_error_handler(function()
        {
        });
        $this->link = @mysqli_connect($host, $user, $password,null,$port);
        restore_error_handler();
        $this->user = $user;
        $this->pass = $password;
        $this->host = $host;
        $this->portNum = $port;
        $this->currentRow = -1;
        $this->lastErrorNo = 0;

        if ($this->link) {
            $this->link->set_charset("utf8");
            mysqli_query($this->link, "set character_set_client='utf8'");
            mysqli_query($this->link, "set character_set_results='utf8'");
        } else {
            $this->lastErrorNo = mysqli_connect_errno();
            $this->lastErrorMessage = mysqli_connect_error();
        }
    }
    /**
     * Execute MySQL query.
     * Note that the method does not support the execution of multi-queries in 
     * one transaction. It supports only one SQL query per transaction.
     * @param MySQLQuery $query an object of type 'MySQLQuery'.
     * @return boolean true if the query was executed successfully, Other than that, 
     * the method will return false in case of error.
     * @since 1.0
     */
    public function executeQuery($query) {
        if ($query instanceof MySQLQuery) {
            $this->resultRows = null;
            $this->currentRow = -1;
            $this->lastQuery = $query;

            if ($this->isConnected()) {
                if (!$query->isBlobInsertOrUpdate()) {
                    mysqli_query($this->link, 'set collation_connection =\''.$query->getStructure()->getCollation().'\'');
                }
                //this part has a bug. The bug can happen if query body 
                //contain other ';'. Example: insert into articles (id,articte_text) 
                // values (99, 'This is An example; ;; Can Cause a bug;;')
                // most queries who have this type of syntax fall under 
                // insert and update.
                $qType = $query->getType();

                if ($qType == 'insert' || $qType == 'update') {
                    return $this->_insertQuery();
                } else if ($qType == 'select' || $qType == 'show'
                   || $qType == 'describe') {
                    return $this->_selectQuery();
                } else {
                    return $this->_otherQuery();
                }
            }
        }

        return false;
    }
    /**
     * Returns an array which contains all data from a specific column given its 
     * name.
     * @param string $colKey The name of the column as specified in the last 
     * executed query. It must be a value when passed to the method 
     * Table::getCol() will return an object of type 'Column'.
     * @return array An array which contains all data from the given column. 
     * if the column does not exist, the method will return the constant 
     * 'Table::NO_SUCH_TABLE'.
     * @since 1.2
     */
    public function getColumn($colKey) {
        $retVal = [];
        $rows = $this->getRows();
        $colNameInDb = $this->getLastQuery()->getColName($colKey);

        if ($colNameInDb != MySQLTable::NO_SUCH_COL) {
            foreach ($rows as $row) {
                if (isset($row[$colNameInDb])) {
                    $retVal[] = $row[$colNameInDb];
                } else {
                    break;
                }
            }
        } else {
            $retVal = MySQLTable::NO_SUCH_COL;
        }

        return $retVal;
    }
    /**
     * Returns the name of the database that the instance is connected to.
     * @return string The name of the database.
     */
    public function getDBName() {
        return $this->db;
    }
    /**
     * Returns the last error number that was generated by MySQL server.
     * @return int The last generated error number. If no error is generated, 
     * the returned value will be 0.
     * @since 1.0
     */
    public function getErrorCode() {
        return $this->lastErrorNo;
    }
    /**
     * Returns the last generated error message that was generated by MySQL server.
     * @return string The last generated error message that was generated by MySQL server. 
     * If no error is generated, the method will return the string 'NO ERRORS'.
     * @since 1.0
     */
    public function getErrorMessage() {
        return trim($this->lastErrorMessage);
    }
    /**
     * Returns the name of database host.
     * @return string Database host name.
     * @since 1.3.2
     */
    public function getHost() {
        return $this->host;
    }
    /**
     * Returns the last executed query object.
     * @return MySQLQuery An object of type 'MySQLQuery'
     * @since 1.1
     */
    public function getLastQuery() {
        return $this->lastQuery;
    }
    /**
     * Returns the number of port that is used to connect to the host.
     * @return int The number of port that is used to connect to the host.
     * @since 1.3.1
     */
    public function getPortNumber() {
        return $this->portNum;
    }
    /**
     * Returns the result set in case of executing select query.
     * The method will return null in case of none-select queries.
     * @return mysqli_result|null
     * @since 1.0
     * @deprecated since version 1.3.1
     */
    public function getResult() {
        return $this->result;
    }
    /**
     * Returns the row which the class is pointing to in the result set.
     * @return array|null an associative array that represents a table row.  
     * If no results are fetched, the method will return null. 
     * @since 1.0
     */
    public function getRow() {
        if ($this->resultRows == null) {
            $this->getRows();
        }

        if (count($this->resultRows) != 0) {
            if ($this->currentRow == -1) {
                return $this->getRows()[0];
            } else if ($this->currentRow < $this->rows()) {
                return $this->getRows()[$this->currentRow];
            }
        } else {
            return $this->_getRow();
        }

        return null;
    }
    /**
     * Returns an array which contains all fetched results from the database.
     * @return array An array which contains all fetched results from the database. 
     * Each row will be an associative array. The index will represents the 
     * column of the table.
     * @since 1.2
     */
    public function getRows() {
        if ($this->resultRows != null) {
            return $this->resultRows;
        }
        $execResult = $this->getResult();

        if (function_exists('mysqli_fetch_all')) {
            $rows = $execResult !== null ? mysqli_fetch_all($execResult, MYSQLI_ASSOC) : [];
        } else {
            $rows = [];

            if ($execResult !== null) {
                while ($row = $execResult->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
        }

        if ($this->getLastQuery()->getMappedEntity() !== null) {
            $this->resultRows = [];

            foreach ($rows as $row) {
                $this->resultRows[] = $this->_map($row);
            }
        } else {
            $this->resultRows = $rows;
        }

        return $this->resultRows;
    }
    /**
     * Returns the name of the user which is used to access the database.
     * @return string the name of the user which is used to access the database.
     */
    public function getUsername() {
        return $this->user;
    }
    /**
     * Checks if the connection is still active or its dead and try to reconnect.
     * @return boolean true if still active, false if dead. If the connection is 
     * dead, more details can be found by getting the error message and error 
     * number.
     * @since 1.0
     * @deprecated since version 1.3.1
     */
    public function isConnected() {
        $test = false;

        if ($this->link instanceof mysqli) {
            set_error_handler(function()
            {
            });
            $this->link = @mysqli_connect($this->host, $this->user, $this->pass,null , $this->portNum);
            restore_error_handler();

            if ($this->link) {
                $test = true;
                $this->link->set_charset("utf8");
                mysqli_query($this->link, "set character_set_client='utf8'");
                mysqli_query($this->link, "set character_set_results='utf8'");

                if ($this->db !== null) {
                    $test = mysqli_select_db($this->link, $this->db);

                    if ($test === false) {
                        $this->lastErrorMessage = mysqli_error($this->link);
                        $this->lastErrorNo = mysqli_errno($this->link);
                        $test = true;
                    }
                } else {
                    $test = true;
                }
            } else {
                $this->lastErrorNo = mysqli_connect_errno();
                $this->lastErrorMessage = mysqli_connect_error();
            }
        }

        return $test;
    }
    /**
     * Returns the next row that was resulted from executing a query that has 
     * results.
     * @return array|null The next row in the result set. If no more rows are 
     * in the set, the method will return null.
     * @since 1.3
     */
    public function nextRow() {
        $this->currentRow++;
        $rows = $this->getRows();

        if (isset($rows[$this->currentRow])) {
            return $rows[$this->currentRow];
        }

        return null;
    }
    /**
     * Reconnect to MySQL server if a connection was established before.
     * @return boolean If the reconnect attempt was succeeded, the method 
     * will return true.
     * @since 1.3.1
     */
    public function reconnect() {
        return $this->isConnected();
    }

    /**
     * Return the number of rows returned by last query.
     * @return int If no result returned by MySQL server, the method will return -1. If 
     * the executed query returned 0 rows, the method will return 0.
     * @since 1.0
     */
    public function rows() {
        if ($this->result) {
            return count($this->getRows());
        }

        return -1;
    }
    /**
     * Select a database instance.
     * This method will always return false if no connection has been 
     * established with the database. 
     * @param string $dbName The name of the database instance.
     * @return boolean true if the instance is selected. false
     * otherwise.
     * @since 1.0
     */
    public function setDB($dbName) {
        $this->db = $dbName;
        $this->reconnect();

        if ($this->getErrorCode() == 1049) {
            return false;
        }

        return true;
    }
    /**
     * Helper method that is used to initialize the array of rows in case 
     * of first call to the method getRow()
     * @param type $retry
     * @return type
     */
    private function _getRow($retry = 0) {
        if (count($this->resultRows) != 0) {
            return $this->getRows()[0];
        } else if ($retry == 1) {
            return null;
        } else {
            $this->getRows();
            $retry++;

            return $this->_getRow($retry);
        }
    }
    private function _insertQuery() {
        $query = $this->getLastQuery();
        $retVal = false;
        $r = mysqli_query($this->link, $query->getQuery());

        if (!$r) {
            $this->lastErrorMessage = $this->link->error;
            $this->lastErrorNo = $this->link->errno;
            $this->result = null;
            $r = mysqli_multi_query($this->link, $query->getQuery());

            if ($r) {
                $this->lastErrorMessage = 'NO ERRORS';
                $this->lastErrorNo = 0;
                $this->result = null;
                $retVal = true;
            }
        } else {
            $retVal = true;
        }
        $query->setIsBlobInsertOrUpdate(false);

        return $retVal;
    }
    /**
     * Map a record to an entity class.
     * @param array $row An associative array that contains record's data.
     * @return object The object at which the record was mapped to.
     */
    private function _map($row) {
        $entityName = $this->getLastQuery()->getMappedEntity();
        $entity = new $entityName();
        $table = $this->getLastQuery()->getTable();
        $mapper = new EntityMapper($table, 'E');
        $datatypes = $table->types();
        $colsNames = $table->getColsNames();
        $index = 0;

        foreach ($mapper->getSettersMap() as $methodName => $colName) {
            if (isset($row[$colName]) && method_exists($entity, $methodName)) {
                if ($datatypes[$index] == 'boolean' && $colsNames[$index] == $colName) {
                    $bool = $row[$colName] == 'Y';
                    $entity->$methodName($bool);
                } else {
                    $entity->$methodName($row[$colName]);
                }
            }
            $index++;
        }

        return $entity;
    }
    private function _otherQuery() {
        $this->result = null;
        $query = $this->getLastQuery();
        $r = mysqli_query($this->link, $query->getQuery());
        $retVal = false;

        if (!$r) {
            $this->lastErrorMessage = $this->link->error;
            $this->lastErrorNo = $this->link->errno;
            $this->result = null;
            $r = mysqli_multi_query($this->link, $query->getQuery());

            if ($r) {
                $this->lastErrorMessage = 'NO ERRORS';
                $this->lastErrorNo = 0;
                $this->result = null;
                $retVal = true;
            }
        } else {
            $this->lastErrorMessage = 'NO ERRORS';
            $this->lastErrorNo = 0;
            $this->result = null;
            $query->setIsBlobInsertOrUpdate(false);

            $retVal = true;
        }
        $query->setIsBlobInsertOrUpdate(false);

        return $retVal;
    }
    private function _selectQuery() {
        $r = mysqli_query($this->link, $this->getLastQuery()->getQuery());

        if ($r) {
            $this->result = $r;
            $this->lastErrorNo = 0;

            return true;
        } else {
            $this->lastErrorMessage = $this->link->error;
            $this->lastErrorNo = $this->link->errno;
            $this->result = null;
            $this->getLastQuery()->setIsBlobInsertOrUpdate(false);

            return false;
        }
    }
}
