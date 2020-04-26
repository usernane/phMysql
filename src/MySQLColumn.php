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
 * A class that represents a column in MySQL table.
 * @author Ibrahim
 * @version 1.6.8
 */
class MySQLColumn {
    /**
     * An array of supported data types.
     * <p>The supported types are:</p>
     * <ul>
     * <li><b>int</b>: Used to store integers. Maximum size is 11.</li>
     * <li><b>varchar</b>: Used to store strings.</li>
     * <li><b>boolean</b> Used to store true or false as a varchar.</li>
     * <li><b>timestamp</b>: Used to store changes on the record. Note that only one column 
     * in the table can have this type.</li>
     * <li><b>date</b>: Used to store date in the formate 'YYYY-MM-DD' The range is '1000-01-01' to '9999-12-31'.</li>
     * <li><b>datetime</b>: Used to store a point in time. Somehow, similar to timestamp.</li>
     * <li><b>text</b>: Used to store text.</li>
     * <li><b>mediumtext</b>: Used to store text.</li>
     * <li><b>tinyblob</b>: Used to store up to 256 bytes of raw binary data.</li>
     * <li><b>blob</b> Used to store up to 16 kilobytes of raw binary data.</li>
     * <li><b>mediumblob</b> Used to store up to 16 megabytes of raw binary data.</li>
     * <li><b>longblob</b> Used to store up to 4 gigabytes of raw binary data.</li>
     * <li><b>decimal</b> Used to store exact numeric values.</li>
     * <li><b>float</b> Used to store numbers in a single precision notation (approximate values).</li>
     * <li><b>double</b> Used to store numbers in a double precision notation (approximate values).</li>
     * </ul>
     * @var array 
     * @since 1.0
     */
    const DATATYPES = [
        'int',
        'varchar',
        'timestamp',
        'tinyblob',
        'blob',
        'mediumblob',
        'longblob',
        'datetime',
        'text',
        'mediumtext',
        'decimal',
        'double',
        'float',
        'boolean', 
        'bool'
    ];
    /**
     * A constant that is returned by some methods to tell that the 
     * datatype of a column is invalid.
     * @var string 
     * @since 1.2
     */
    const INV_COL_DATATYPE = 'inv_col_datatype';
    /**
     * A constant that is returned by some methods to tell that the 
     * name of a column is invalid.
     * @var string 
     * @since 1.2
     */
    const INV_COL_NAME = 'inv_col_nm';
    /**
     * A constant that is returned by some methods to tell that the 
     * size datatype of a column is invalid (for 'varchar' and 'int').
     * @var string 
     * @since 1.2
     */
    const INV_DATASIZE = 'inv_col_datatype';
    /**
     * A constant that indicates the datatype of the 
     * column does not support size.
     * @var string
     * @since 1.4
     */
    const SIZE_NOT_SUPPORTED = 'TYPE_DOES_NOT_SUPPORT_SIZE';
    /**
     *
     * @var type 
     * @since 1.6.6
     */
    private $alias;
    /**
     * A boolean which can be set to true in order to update column timestamp.
     * @var boolean 
     */
    private $autoUpdate;
    /**
     * The index of the column in owner table.
     * @var int
     * @since 1.6 
     */
    private $columnIndex;
    /**
     * A comment to add to the column.
     * @var string|null 
     * @since 1.6.3
     */
    private $comment;
    /**
     * Default value for the column.
     * @var mixed 
     * @since 1.0
     */
    private $default;
    /**
     * A boolean value. Set to true if column is primary and auto increment.
     * @var boolean 
     * @since 1.0
     */
    private $isAutoInc;
    /**
     * A boolean value. Set to true if column allow null values. Default 
     * is false.
     * @var boolean 
     */
    private $isNull;
    /**
     * A boolean value. Set to true if the column is a primary key. Default 
     * is false.
     * @var boolean 
     * @since 1.0
     */
    private $isPrimary;

    /**
     * A boolean value. Set to true if column is unique.
     * @var boolean
     * @since 1.0 
     */
    private $isUnique;
    /**
     * Version number of MySQL server.
     * @var string 
     */
    private $mySqlVersion;
    /**
     * The name of the column.
     * @var string
     * @since 1.0 
     */
    private $name;
    /**
     * The table that this column belongs to.
     * @var MySQLTable
     * @since 1.5 
     */
    private $ownerTable;
    /**
     * The number of numbers that will appear after the decimal point.
     * @var int 
     * @since 1.6.2
     */
    private $scale;
    /**
     * The size of the data in the column (for 'int' and 'varchar'). It must be 
     * a positive value.
     * @var int 
     * @since 1.0
     */
    private $size;
    /**
     * The type of the data that the column will have.
     * @var string 
     * @since 1.0
     */
    private $type;
    /**
     * A user defined custom cleanup function to clean the values even more before 
     * inserting or doing any SQL transaction.
     * @var callback 
     * @since 1.6.7
     */
    private $customCleaner;
    /**
     * Creates new instance of the class.
     * This method is used to initialize basic attributes of the column. 
     * First of all, it sets MySQL version number to 5.5. Then it validates the 
     * given column name and datatype and size.
     * @param string $colName It must be a string and its not empty. 
     * Also it must not contain any spaces or any characters other than A-Z, a-z and 
     * underscore. If the given column name is invalid the value 'col' will be 
     * set as an initial name for the column.
     * @param string $datatype The type of column data. It must be a value from the 
     * array Column::DATATYPES. If the given datatype is invalid, 'varchar' 
     * will be used as default type for the column.
     * @param int $size The size of the column. Used only in case of 
     * 'varachar', 'int' or decimal. If the given size is invalid, 1 will be used as default 
     * value. Note that in case of decimal, if this value is 1, scale is set to 
     * 0. If this value is 2, scale is set to 1. If this value is greater than 
     * or equal to 3, scale is set to 2 by default.
     * @since 1.0
     */
    public function __construct($colName = 'col',$datatype = 'varchar',$size = 1) {
        $this->mySqlVersion = '5.5';
        $this->autoUpdate = false;
        $this->isPrimary = false;

        if ($this->setName($colName) !== true) {
            $this->setName('col');
        }

        if (!$this->setType($datatype)) {
            $this->setType('varchar');
        }
        $realDatatype = $this->getType();

        if (($realDatatype == 'varchar' || $realDatatype == 'int' || $realDatatype == 'text') && !$this->setSize($size)) {
            $this->setSize(1);
        }

        if ($realDatatype == 'decimal' || $realDatatype == 'float' || $realDatatype == 'double') {
            $this->_setFloatTypeSize($size);
        }

        $this->setIsNull(false);
        $this->setIsUnique(false);
    }
    private function _setFloatTypeSize($size) {
        if (!$this->setSize($size)) {
            $this->setSize(10);
            $this->setScale(2);
        } else {
            $size = $this->getSize();

            if ($size == 0 || $size == 1) {
                $this->setScale(0);
            } else if ($size == 2) {
                $this->setScale(1);
            } else {
                $this->setScale(2);
            }
        }
    }
    /**
     * Sets a custom filter to clean up query values even more. 
     * The function will have two parameters, the first parameter is the original 
     * value and the second one is the cleaned value using the basic validator. 
     * The developer must implement the function in a a way that it returns the 
     * value after doing more validation before submitting it to the database.
     * @param callable $func A user defined function to filter column value 
     * even further.
     * @since 1.6.7
     */
    public function setCustomFilter($func) {
        if(is_callable($func)){
            $this->customCleaner = $func;
        }
    }
    private function _firstColPart(){
        $retVal = $this->getName().' ';
        $colDataType = $this->getType();

        if ($colDataType == 'int' || $colDataType == 'varchar' || $colDataType == 'text') {
            $retVal .= $colDataType.'('.$this->getSize().') ';
        } else if ($colDataType == 'boolean') {
            $retVal .= 'varchar(1) ';
        } else if ($colDataType == 'decimal' || $colDataType == 'float' || $colDataType == 'double') {
            if ($this->getSize() != 0) {
                $retVal .= $colDataType.'('.$this->getSize().','.$this->getScale().') ';
            } else {
                $retVal .= $colDataType.' ';
            }
        } else {
            $retVal .= $colDataType.' ';
        }
        return $retVal;
    }
    private function _nullPart() {
        $colDataType = $this->getType();
        if (!$this->isNull() || $colDataType == 'boolean') {
            return 'not null ';
        } else {
            return 'null ';
        }
    }
    private function _defaultPart() {
        $colDataType = $this->getType();
        $colDefault = $this->default;
        if ($colDefault !== null) {
            if ($colDataType == 'boolean') {
                if ($this->getDefault() === true) {
                    return 'default \'Y\' ';
                } else {
                    return 'default \'N\' ';
                }
            } else {
                return 'default '.$colDefault.' ';
            }
        }
    }
    private function _commentPart() {
        $colComment = $this->getComment();
        if ($colComment !== null) {
            return 'comment \''.$colComment.'\'';
        }
    }
    /**
     * Returns a string that represents the datatype of column data in 
     * PHP.
     * This method basically maps the data that can be stored in a column from 
     * MySQL type to PHP type. For example, if column type is 'varchar', the method 
     * will return the value 'string'. If the column allow null values, the 
     * method will return 'string|null' and so on.
     * @return string A string that represents column type in PHP (such as 
     * 'integer' or 'boolean').
     * @since 1.6.8
     */
    public function getPHPType() {
        $isNullStr = $this->isNull() ? '|null' : '';
        $colType = $this->getType();

        if ($colType == 'int') {
            return 'int'.$isNullStr;
        } else if ($colType == 'decimal' || $colType == 'double' || $colType == 'float') {
            return 'double'.$isNullStr;
        } else if ($colType == 'boolean') {
            return 'boolean'.$isNullStr;
        } else {
            return 'string'.$isNullStr;
        }
    }
    /**
     * Creates an instance of the class 'Column' given an array of options.
     * @param array $options An associative array of options. The available options 
     * are: 
     * <ul>
     * <li><b>name</b>: Required. The name of the column in the database. If not 
     * provided, no object will be created.</li>
     * <li><b>datatype</b>: The datatype of the column. If not provided, 'varchar' 
     * will be used. Equal option: 'type'.</li>
     * <li><b>size</b>: Size of the column (if datatype does support size). 
     * If not provided, 1 will be used.</li>
     * <li><b>default</b>: A default value for the column if its value 
     * is not present in case of insert.</li>
     * <li><b>is-null</b>: A boolean. If the column allows null values, this should 
     * be set to true. Default is false.</li>
     * <li><b>is-primary</b>: A boolean. It must be set to true if the column 
     * represents a primary key. Note that the column will be set as unique 
     * once its set as a primary. Equal option: primary.</li>
     * <li><b>auto-inc</b>: A boolean. Only applicable if the column is a 
     * primary key. Set to true to auto-increment column value by 1 for every 
     * insert.</li>
     * <li><b>is-unique</b>: A boolean. If set to true, a unique index will 
     * be created for the column.</li>
     * <li><b>auto-update</b>: A boolean. If the column datatype is 'timestamp' or 
     * 'datetime' and this parameter is set to true, the time of update will 
     * change automatically without having to change it manually.</li>
     * <li><b>scale</b>: Number of numbers to the left of the decimal 
     * point. Only supported for decimal datatype.</li>
     * <li><b>comment</b>: A comment which can be used to describe the column.</li>
     * <li><b>validator</b>: A PHP function which can be used to validate user 
     * values before submitting the query to database.</li>
     * </ul>
     * 
     * @return MySQLColumn|null The method will return an object of type 'MySQLColumn' 
     * if created. If the index 'name' is not set, the method will return null.
     * @since 1.6.8
     */
    public static function createColObj($options) {
        if (isset($options['name'])) {
            if (isset($options['datatype'])) {
                $datatype = $options['datatype'];
            } else if (isset($options['type'])) {
                $datatype = $options['type'];
            } else {
                $datatype = 'varchar';
            }
            $col = new MySQLColumn($options['name'], $datatype);
            $size = isset($options['size']) ? intval($options['size']) : 1;
            $col->setSize($size);
            
            self::_primaryCheck($col, $options);
            self::_extraAttrsCheck($col, $options);
            
            return $col;
        }

        return null;
    }
    /**
     * 
     * @param MySQLColumn $col
     * @param array $options
     */
    private static function _extraAttrsCheck(&$col, $options) {
        $scale = isset($options['scale']) ? intval($options['scale']) : 2;
        $col->setScale($scale);
        
        if (isset($options['default'])) {
            $col->setDefault($options['default']);
        }
        
        if (isset($options['is-unique'])) {
            $col->setIsUnique($options['is-unique']);
        }
        
        //the 'not null' or 'null' must be specified or it will cause query 
        //or it will cause query error.
        $isNull = isset($options['is-null']) ? $options['is-null'] : false;
        $col->setIsNull($isNull);
        
        if (isset($options['auto-update'])) {
            $col->setAutoUpdate($options['auto-update']);
        }

        if (isset($options['comment'])) {
            $col->setComment($options['comment']);
        }
        
        if(isset($options['validator'])){
            $col->setCustomFilter($options['validator']);
        }
    }
    /**
     * 
     * @param MySQLColumn $col
     * @param array $options
     */
    private static function _primaryCheck(&$col, $options) {
        $isPrimary = isset($options['primary']) ? $options['primary'] : false;
        if(!$isPrimary){
            $isPrimary = isset($options['is-primary']) ? $options['is-primary'] : false;
        }
        $col->setIsPrimary($isPrimary);
        if ($isPrimary && isset($options['auto-inc'])) {
            $col->setIsAutoInc($options['auto-inc']);
            $col->setIsNull(true);
        }
    }
    /**
     * Constructs a string that can be used to create the column in a table.
     * @return string A string that can be used to create the column in a table.
     */
    public function __toString() {
        $retVal = $this->_firstColPart();
        $retVal .= $this->_nullPart();
        $colDataType = $this->getType();
        if ($this->isUnique() && $colDataType != 'boolean') {
            $retVal .= 'unique ';
        }
        $retVal .= $this->_defaultPart();

        if ($colDataType == 'varchar' || $colDataType == 'text' || $colDataType == 'mediumtext') {
            $retVal .= 'collate '.$this->getCollation().' ';
        }
        $retVal .= $this->_commentPart();

        return trim($retVal);
    }
    /**
     * Clean and validates a value against the datatype of the column.
     * @param mixed $val The value that will be cleaned. It can be a single value or 
     * an array of values.
     * @param boolean $dateEndOfDay If the datatype of the column is 'datetime' 
     * or 'timestamp' and time is not specified in the passed value and this 
     * attribute is set to true, The time will be set to '23:59:59'. Default is 
     * false.
     * @return int|string|null The return type of the method will depend on 
     * the type of the column as follows:
     * <ul>
     * <li>If no default is set or type does not support default values, null is returned.</li>
     * <li><b>int</b>: The method will return an integer.</li>
     * <li><b>decimal, float and double</b>: A quoted string (such as '1.06')</li>
     * <li><b>varchar, text and mediumtext</b>: A quoted string (such as 'It is fun'). 
     * Note that any single quot inside the string will be escaped.</li>
     * <li><b>datetime and timestamp</b>: A quoted string (such as '2019-11-09 00:00:00')</li>
     * </ul>
     * If the column has a custom clean function is set, the 
     * value will also depend on the return value of the custom filter
     * function
     * @since 1.6.4
     */
    public function cleanValue($val,$dateEndOfDay = false) {
        $valType = gettype($val);

        if ($valType == 'array') {
            $retVal = [];

            foreach ($val as $arrVal) {
                $retVal[] = $this->_cleanValueHelper($arrVal, $dateEndOfDay);
            }

            return $retVal;
        } else {
            return $this->_cleanValueHelper($val, $dateEndOfDay);
        }
    }
    /**
     * Returns the name of column alias.
     * @return string|null If column alias is set, the method will return it. 
     * If it is not set, the method will return null.
     * @since 1.6.6
     */
    public function getAlias($tablePrefix = false) {
        if ($tablePrefix === true && $this->getOwner() !== null && $this->alias !== null) {
            return $this->getOwner()->getName().'.'.$this->alias;
        }

        return $this->alias;
    }
    /**
     * Returns the value of column collation.
     * @return string If MySQL version is '5.5' or lower, the method will 
     * return 'utf8mb4_unicode_ci'. Other than that, the method will return 
     * 'utf8mb4_unicode_520_ci'.
     * @since 1.0
     */
    public function getCollation() {
        $split = explode('.', $this->getMySQLVersion());

        if (isset($split[0]) && intval($split[0]) <= 5 && isset($split[1]) && intval($split[1]) <= 5) {
            return 'utf8mb4_unicode_ci';
        }

        return 'utf8mb4_unicode_520_ci';
    }
    /**
     * Returns a string that represents a comment which was added with the column.
     * @return string|null Comment text. If it is not set, the method will return 
     * null.
     * @since 1.6.3
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * Returns the default value of the column.
     * @return mixed The default value of the column.
     * @since 1.0
     */
    public function getDefault() {
        $defaultVal = $this->default;
        $retVal = null;
        if ($defaultVal !== null) {
            $dt = $this->getType();

            if ($dt == 'varchar' || $dt == 'text' || $dt == 'mediumtext' || 
                    //$dt == 'timestamp' || $dt == 'datetime' || 
                    $dt == 'tinyblob' || $dt == 'blob' || $dt == 'mediumblob' || 
                    $dt == 'longblob' || $dt == 'decimal' || $dt == 'float' || $dt == 'double'
                    ) {
                $retVal = substr($defaultVal, 1, strlen($defaultVal) - 2);

                if ($dt == 'decimal' || $dt == 'float' || $dt == 'double') {
                    $retVal = floatval($retVal);
                }

            } else if (($this->default == 'now()' || $this->default == 'current_timestamp') &&
                ($dt == 'datetime' || $dt == 'timestamp')) {
                $retVal = date('Y-m-d H:i:s');
            } else if ($dt == 'timestamp' || $dt == 'datetime') {
                $retVal = substr($defaultVal, 1, strlen($defaultVal) - 2);
            } else if ($dt == 'int') {
                $retVal = intval($defaultVal);
            } else if ($dt == 'boolean'){
                return $defaultVal === true;
            }
            return $retVal;
        }
        else{
            return $this->default;
        }
    }
    /**
     * Returns the index of the column in its parent table.
     * @return int The index of the column in its parent table starting from 0. 
     * If the column has no parent table, the method will return -1.
     * @since 1.6
     */
    public function getIndex() {
        return $this->columnIndex;
    }
    /**
     * Returns version number of MySQL server.
     * @return string MySQL version number (such as '5.5'). If version number 
     * is not set, The default return value is '5.5'.
     * @since 1.6.1
     */
    public function getMySQLVersion() {
        return $this->mySqlVersion;
    }
    /**
     * Returns the name of the column.
     * @param boolean $tablePrefix If this parameter is set to true and the column 
     * has an owner table, the name of the column will be prefixed with the owner 
     * table name. Default value is false.
     * @return string The name of the column. If the name is not set, the method 
     * will return the value 'col'.
     * @since 1.0
     */
    public function getName($tablePrefix = false) {
        if ($tablePrefix === true && $this->getOwner() !== null) {
            return $this->getOwner()->getName().'.'.$this->name;
        }

        return $this->name;
    }
    /**
     * Returns the table which owns this column.
     * @return MySQLTable|null The owner table of the column. 
     * If the column has no owner, the method will return null.
     * @since 1.5
     */
    public function getOwner() {
        return $this->ownerTable;
    }
    /**
     * Returns the value of scale.
     * Scale is simply the number of digits that will appear to the right of 
     * decimal point. Only applicable if the datatype of the column is decimal, 
     * float or double.
     * @return int The number of numbers after the decimal point. Note that 
     * if the size of datatype of the column is 1, scale is set to 
     * 0 by default. If if the size of datatype of the column is 2, scale is 
     * set to 1. If if the size of datatype of the column is greater than 
     * or equal to 3, scale is set to 2 by default.
     * @since 1.6.2
     */
    public function getScale() {
        return $this->scale;
    }
    /**
     * Returns the size of the column.
     * @return int The size of the column. If column data type is int, decimal, double 
     * or float, the value will represents the overall number of digits in the 
     * number (Precision) (e.g: size of 54.323 is 5). If the datatype is varchar, then the 
     * number will represents number of characters. Default value is 1 for 
     * all types including datetime and timestamp.
     * @since 1.0
     */
    public function getSize() {
        return $this->size;
    }
    /**
     * Returns the type of column data (such as 'varchar').
     * @return string The type of column data. Default return value is 'varchar' 
     * if the column data type is not set.
     * @since 1.0
     */
    public function getType() {
        return $this->type;
    }
    /**
     * Checks if the column is auto increment or not.
     * @return boolean true if the column is auto increment.
     * @since 1.1
     */
    public function isAutoInc() {
        return $this->isAutoInc;
    }
    /**
     * Returns the value of the property 'autoUpdate'.
     * @return boolean If the column type is 'datetime' or 'timestamp' and the 
     * column is set to auto update in case of update query, the method will 
     * return true. Default return value is valse.
     * @since 1.6.5
     */
    public function isAutoUpdate() {
        return $this->autoUpdate;
    }
    /**
     * Checks if the column allows null values.
     * @return boolean true if the column allows null values. Default return 
     * value is false which means that the column does not allow null values.
     * @since 1.0
     */
    public function isNull() {
        return $this->isNull;
    }
    /**
     * Checks if the column is part of the primary key or not.
     * @return boolean true if the column is primary. 
     * Default return value is false.
     * @since 1.0
     */
    public function isPrimary() {
        return $this->isPrimary;
    }
    /**
     * Returns the value of the property $isUnique.
     * @return boolean true if the column value is unique. 
     * @since 1.0
     */
    public function isUnique() {
        return $this->isUnique;
    }
    /**
     * Sets an optional alias name for the column.
     * @param string|null $name A string that represents the alias. If null 
     * is given, it means the alias will be unset.
     * @return boolean If the property value is updated, the method will return 
     * true. Other than that, the method will return false.
     * @since 1.6.6
     */
    public function setAlias($name) {
        if ($name === null) {
            $this->alias = null;

            return true;
        }
        $trimmed = trim($name);

        if (strlen($trimmed) != 0 && $this->_validateName($trimmed)) {
            $this->alias = $trimmed;

            return true;
        }

        return false;
    }
    /**
     * Sets the value of the property 'autoUpdate'.
     * It is used in case the user want to update the date of a column 
     * that has the type 'datetime' or 'timestamp' automatically if a record is updated. 
     * This method has no effect for other datatypes.
     * @param boolean $bool If true is passed, then the value of the column will 
     * be updated in case an update query is constructed. 
     * @since 1.1
     */
    public function setAutoUpdate($bool) {
        if ($this->getType() == 'datetime' || $this->getType() == 'timestamp') {
            $this->autoUpdate = $bool === true;
        }
    }
    /**
     * Sets a comment which will appear with the column.
     * @param string|null $comment Comment text. It must be non-empty string 
     * in order to set. If null is passed, the comment will be removed.
     * @since 1.6.3
     */
    public function setComment($comment) {
        if ($comment == null || strlen($comment) != 0) {
            $this->comment = $comment;
        }
    }
    /**
     * Sets the default value for the column to use in case of insert.
     * For integer data type, the passed value must be an integer. For string types such as 
     * 'varchar' or 'text', the passed value must be a string. If the datatype 
     * is 'timestamp', the default will be set to current time and date 
     * if non-null value is passed (the value which is returned by the 
     * function date('Y-m-d H:i:s). If the passed 
     * value is a date string in the format 'YYYY-MM-DD HH:MM:SS', then it 
     * will be set to the given value. If the passed 
     * value is a date string in the format 'YYYY-MM-DD', then the default 
     * will be set to 'YYYY-MM-DD 00:00:00'. same applies to 'datetime' datatype. If 
     * null is passed, it implies that no default value will be used.
     * @param mixed $default The default value which will be set.
     * @since 1.0
     */
    public function setDefault($default = null) {
        $this->default = $this->cleanValue($default);
        $type = $this->getType();

        if (($type == 'datetime' || $type == 'timestamp') && strlen($this->default) == 0 && $this->default !== null) {
            $this->default = null;
        }
    }
    /**
     * Sets the value of the property <b>$isAutoInc</b>.
     * This attribute can be set only if the column is primary key and the 
     * datatype of the column is set to 'int'.
     * @param boolean $bool true or false.
     * @return boolean <b>true</b> if the property value changed. false 
     * otherwise.
     * @since 1.0
     */
    public function setIsAutoInc($bool) {
        if ($this->isPrimary() && gettype($bool) == 'boolean' && $this->getType() == 'int') {
                $this->isAutoInc = $bool;

                return true;
        }

        return false;
    }
    /**
     * Updates the value of the property $isNull.
     * This property can be set to true if the column allow the insertion of 
     * null values. Note that if the column is set as a primary or the datatype 
     * of the column is set to 'boolean', the property will not be updated.
     * @param boolean $bool true if the column allow null values. false 
     * if not.
     * @return boolean true If the property value is updated. If the given 
     * value is not a boolean, the method will return false. Also if 
     * the column represents a primary key, the method will always return false.
     * @since 1.0
     */
    public function setIsNull($bool) {
        if (gettype($bool) == 'boolean' && !$this->isPrimary() && $this->getType() != 'boolean') {
            $this->isNull = $bool;

            return true;
        }

        return false;
    }
    /**
     * Updates the value of the property <b>$isPrimary</b>.
     * Note that once the column become primary, it becomes unique by default. Also, 
     * Note that if column type is 'boolean', it cannot be a primary.
     * @param boolean $bool <b>true</b> if the column is primary key. false 
     * if not.
     * @since 1.0
     */
    public function setIsPrimary($bool) {
        if ($this->getType() != 'boolean') {
            $this->isPrimary = $bool === true;

            if ($this->isPrimary() === true) {
                $this->setIsNull(false);
                $this->setIsUnique(true);
            }
        } else {
            $this->isPrimary = false;
        }
    }
    /**
     * Sets the value of the property $isUnique.
     * Note that if column type is 'boolean', it cannot be unique.
     * @param boolean $bool True if the column value is unique. false 
     * if not.
     * @since 1.0
     */
    public function setIsUnique($bool) {
        if ($this->getType() != 'boolean') {
            $this->isUnique = $bool === true;
        } else {
            $this->isUnique = false;
        }
    }
    /**
     * Sets version number of MySQL server.
     * Version number of MySQL is used to set the correct collation for the column 
     * in case of varchar or text data types. If MySQL version is '5.5' or lower, 
     * collation will be set to 'utf8mb4_unicode_ci'. Other than that, the 
     * collation will be set to 'utf8mb4_unicode_520_ci'.
     * @param string $vNum MySQL version number (such as '5.5').
     * @since 1.6.1
     */
    public function setMySQLVersion($vNum) {
        if (strlen($vNum) > 0) {
            $split = explode('.', $vNum);

            if (count($split) >= 2) {
                $major = intval($split[0]);
                $minor = intval($split[1]);

                if ($major >= 0 && $minor >= 0) {
                    $this->mySqlVersion = $vNum;
                }
            }
        }
    }
    /**
     * Sets the name of the column.
     * The name of the column must be a string and its not empty. 
     * Also it must not contain any spaces or any characters other than A-Z, a-z and 
     * underscore.
     * @param string $name The name of the table as it appears in the database.
     * @return boolean The method will return true if the column name updated. 
     * If the given value is null or invalid string, the method will return 
     * false.
     * @since 1.0
     */
    public function setName($name) {
        $trimmed = trim($name);

        if (strlen($trimmed) != 0 && $this->_validateName($trimmed)) {
            $this->name = $trimmed;

            return true;
        }

        return false;
    }
    /**
     * Sets or unset the owner table of the column.
     * Note that the developer should not call this method manually. It is 
     * used only if the column is added or removed from MySQLTable object.
     * @param MySQLTable|null $table The owner of the column. If null is given, 
     * The owner will be unset.
     * @since 1.5
     */
    public function setOwner($table) {
        if ($table instanceof MySQLTable) {
            $this->ownerTable = $table;
            $colsCount = count($table->columns());
            $this->columnIndex = $colsCount == 0 ? 0 : $colsCount;
            $this->setMySQLVersion($table->getMySQLVersion());
        } else if ($table === null) {
            $this->ownerTable = null;
            $this->columnIndex = -1;
        }
    }
    /**
     * Sets the value of Scale.
     * Scale is simply the number of digits that will appear to the right of 
     * decimal point. Only applicable if the datatype of the column is decimal, 
     * float and double.
     * @param int $val Number of numbers after the decimal point. It must be a 
     * positive number.
     * @return boolean If scale value is set, the method will return true. 
     * false otherwise. The method will not set the scale in the following cases:
     * <ul>
     * <li>Datatype of the column is not decimal, float or double.</li>
     * <li>Size of the column is 0.</li>
     * <li>Given scale value is greater than the size of the column.</li>
     * </ul>
     * @since 1.6.2
     */
    public function setScale($val) {
        $type = $this->getType();

        if ($type == 'decimal' || $type == 'float' || $type == 'double') {
            $size = $this->getSize();

            if ($size != 0 && $val >= 0 && ($size - $val > 0)) {
                $this->scale = $val;

                return true;
            }
        }

        return false;
    }
    /**
     * Sets the size of data (for 'int' and 'varchar' only). 
     * If the data type of the column is 'int', the maximum size is 11. If a 
     * number greater than 11 is given, the value will be set to 11. The 
     * maximum size for the 'varchar' is 21845. If a value greater that that is given, 
     * the datatype of the column will be changed to 'mediumtext'.
     * For decimal, double and float data types, the value will represent 
     * the  precision. If zero is given, then no specific value for precision 
     * and scale will be used. If the datatype is boolean, the passed value will 
     * be ignored and the size is set to 1.
     * @param int $size The size to set.
     * @return boolean true if the size is set. The method will return 
     * false in case the size is invalid or datatype does not support 
     * size attribute. Also The method will return 
     * false in case the datatype of the column does not 
     * support size.
     * @since 1.0
     */
    public function setSize($size) {
        $type = $this->getType();
        $retVal = false;
        if ($type == 'boolean') {
            $this->size = 1;

            $retVal = true;
        } else if ($type == 'varchar' || $type == 'text') {
            $retVal = $this->_textTypeSize($size);
        } else if ($type == 'int') {
            $retVal = $this->_intSize($size);
        } else if (($type == 'decimal' || $type == 'float' || $type == 'double') && $size >= 0) {
            $this->size = $size;

            $retVal = true;
        } else {
            $retVal = false;
        }

        return $retVal;
    }
    private function _intSize($size) {
        if ($size > 0 && $size < 12) {
            $this->size = $size;

            return true;
        } else if ($size > 11) {
            $this->size = 11;

            return true;
        }
        return false;
    }
    private function _textTypeSize($size) {
        if ($size > 0) {
            $this->size = $size;

            if ($size > 21845) {
                $this->setType('mediumtext');
            }

            return true;
        }
        return false;
    }
    /**
     * Sets the type of column data.
     * The datatype must be a value from the array <b>Column::DATATYPES</b>. It 
     * can be in lower case or upper case.
     * @param string $type The type of column data.
     * @param int $size Size of column data (for 'int', 'varchar', 'float', 'double' and 
     * 'decimal'). If the passed size is invalid, 1 will be used as a default value.
     * @param mixed $default Default value for the column to set in case no value is 
     * given in case of insert.
     * @return boolean The method will return true if the data type is set. False otherwise.
     * @since 1.0
     */
    public function setType($type,$size = 1,$default = null) {
        $s_type = strtolower(trim($type));

        if (in_array($s_type, self::DATATYPES)) {
            if ($s_type != 'int') {
                $this->setIsAutoInc(false);
            }

            if ($s_type == 'bool') {
                $this->type = 'boolean';
            } else {
                $this->type = $s_type;
            }

            if ($s_type == 'varchar' || $s_type == 'int' || 
               $s_type == 'double' || $s_type == 'float' || $s_type == 'decimal') {
                if (!$this->setSize($size)) {
                    $this->setSize(1);
                }
            } else {
                $this->setSize(1);
            }
            $this->default = null;

            if ($default !== null) {
                $this->setDefault($default);
            }

            return true;
        }

        return false;
    }
    private function _cleanValueHelper($val,$dateEndOfDay = false) {
        $colDatatype = $this->getType();
        $cleanedVal = null;
        if ($val === null) {
            return null;
        } else if ($colDatatype == 'int') {
            $cleanedVal = intval($val);
        } else if ($colDatatype == 'boolean') {
            $cleanedVal = $val === true;
        } else if ($colDatatype == 'decimal' || $colDatatype == 'float' || $colDatatype == 'double') {
            $cleanedVal = '\''.floatval($val).'\'';
        } else if ($colDatatype == 'varchar' || $colDatatype == 'text' || $colDatatype == 'mediumtext') {
            $cleanedVal = '\''.str_replace("'", "\'", $val).'\'';
        } else if ($colDatatype == 'datetime' || $colDatatype == 'timestamp') {
            $cleanedVal = $this->_dateCleanUp($val, $dateEndOfDay);
        } else {
            //blob mostly
            $cleanedVal = $val;
        }
        if($this->customCleaner !== null){
            return call_user_func($this->customCleaner, $val, $cleanedVal);
        }
        return $cleanedVal;
    }
    private function _dateCleanUp($val, $dateEndOfDay) {
        $trimmed = strtolower(trim($val));
        $cleanedVal = '';
        if ($trimmed == 'current_timestamp') {
            $cleanedVal = 'current_timestamp';
        } else if ($trimmed == 'now()') {
            $cleanedVal = 'now()';
        } else if ($this->_validateDateAndTime($trimmed)) {
            $cleanedVal = '\''.$trimmed.'\'';
        } else if ($this->_validateDate($trimmed)) {
            if ($dateEndOfDay === true) {
                $cleanedVal = '\''.$trimmed.' 23:59:59\'';
            } else {
                $cleanedVal = '\''.$trimmed.' 00:00:00\'';
            }
        }
        return $cleanedVal;
    }
    /**
     * 
     * @param type $date
     */
    private function _validateDate($date) {
        if (strlen($date) == 10) {
            $split = explode('-', $date);

            if (count($split) == 3) {
                $year = intval($split[0]);
                $month = intval($split[1]);
                $day = intval($split[2]);

                return $year > 1969 && $month > 0 && $month < 13 && $day > 0 && $day < 32;
            }
        }

        return false;
    }
    /**
     * Checks if a date-time string is valid or not.
     * @param string $date A date string in the format 'YYYY-MM-DD HH:MM:SS'.
     * @return boolean If the string represents correct date and time, the 
     * method will return true. False if it is not valid.
     */
    private function _validateDateAndTime($date) {
        $trimmed = trim($date);

        if (strlen($trimmed) == 19) {
            $dateAndTime = explode(' ', $trimmed);

            if (count($dateAndTime) == 2) {
                return $this->_validateDate($dateAndTime[0]) && $this->_validateTime($dateAndTime[1]);
            }
        }

        return false;
    }
    /**
     * 
     * @param type $name
     * @return boolean
     * @since 1.6.6
     */
    private function _validateName($name) {
        if (strpos($name, ' ') === false) {
            for ($x = 0 ; $x < strlen($name) ; $x++) {
                $ch = $name[$x];

                if ($x == 0 && ($ch >= '0' && $ch <= '9')) {
                    return false;
                }

                if (!($ch == '_' || ($ch >= 'a' && $ch <= 'z') || ($ch >= 'A' && $ch <= 'Z') || ($ch >= '0' && $ch <= '9'))) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
    /**
     * 
     * @param type $time
     */
    private function _validateTime($time) {
        if (strlen($time) == 8) {
            $split = explode(':', $time);

            if (count($split) == 3) {
                $hours = intval($split[0]);
                $minutes = intval($split[1]);
                $sec = intval($split[2]);

                return $hours >= 0 && $hours <= 23 && $minutes >= 0 && $minutes < 60 && $sec >= 0 && $sec < 60;
            }
        }

        return false;
    }
}
