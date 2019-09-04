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
use phMysql\Column;
/**
 * A class that represents a foreign key.
 *
 * @author Ibrahim
 * @version 1.3.1
 */
class ForeignKey {
    /**
     * The table at which the key will be added to.
     * @var MySQLTable 
     * @since 1.3.1
     */
    private $ownerTableObj;
    /**
     * The table which the values are taken from.
     * @var MySQLTable 
     * @since 1.3.1
     */
    private $sourceTableObj;
    /**
     * An array of allowed conditions for 'on delete' and 'on update'.
     * @var array 
     * @since 1.0 
     */
    const CONDITIONS = array(
        'set null','restrict','set default',
        'no action','cascade'
    );
    /**
     * An array that contains the names of sources columns. 
     * @var array 
     * @since 1.3
     */
    private $ownerCols;
    /**
     * An array that contains the names of referenced columns. 
     * @var array 
     * @since 1.3
     */
    private $referencedTableCols;
    /**
     * The 'on delete' condition.
     * @var string 
     * @since 1.0  
     */
    private $onDeleteCondition;
    /**
     * The 'on update' condition.
     * @var string 
     * @since 1.0  
     */
    private $onUpdateCondition;
    /**
     * The name of the key.
     * @var string 
     * @since 1.0 
     */
    private $keyName;
    /**
     * Sets the name of the key.
     * @param string $name The name of the key. A valid key name must follow the 
     * following rules:
     * <ul>
     * <li>Must be non-empty string.</li>
     * <li>First character must not be a number.</li>
     * <li>Can only contain the following characters: [A-Z], [a-z], [0-9] and 
     * underscore.</li>
     * </ul>
     * @return boolean|string true if the name of the key is set. The method will 
     * return the constant ForeignKey::INV_KEY_NAME in 
     * case if the given key name is invalid.
     * @since 1.1
     */
    public function setKeyName($name) {
        $trim = trim($name);
        if($this->validateAttr($trim) == true){
            $this->keyName = $trim;
            return true;
        }      
        return false;
    }
    /**
     * A method that is used to validate the names of the key attributes (such as source column 
     * name or source table name).
     * @param string $trimmed The string to validate. It must be a string and its not empty. 
     * Also it must not contain any spaces or any characters other than A-Z, a-z and 
     * underscore.
     * @return boolean true if the given parameter is valid. false in 
     * case if the given parameter is invalid.
     */
    private function validateAttr($trimmed){
        $len = strlen($trimmed);
        if($len != 0){
            if(strpos($trimmed, ' ') === false){
                for ($x = 0 ; $x < $len ; $x++){
                    $ch = $trimmed[$x];
                    if($x == 0 && ($ch >= '0' && $ch <= '9')){
                        return false;
                    }
                    if($ch == '_' || ($ch >= 'a' && $ch <= 'z') || ($ch >= 'A' && $ch <= 'Z') || ($ch >= '0' && $ch <= '9')){

                    }
                    else{
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }
    /**
     * Returns the name of the key.
     * @return string The name of the key.
     * @since 1.0
     */
    public function getKeyName() {
        return $this->keyName;
    }
    /**
     * Returns an array which contains the names of the columns 
     * that will contain the value of the referenced columns.
     * @return array An array which contains the names of the columns 
     * that will contain the value of the referenced columns.
     * @return array the name of the source column.
     * @since 1.3
     */
    public function getOwnerCols() {
        return $this->ownerCols;
    }
    /**
     * Adds new column to the set of owner table columns.
     * This method will work only if the owner table is set. Also, the given 
     * column name must belong to the owner table.
     * @param string $colName The name of the column as specified while 
     * initializing the owner table.
     * @return boolean true if the source column name is added. false in 
     * case if the given name is invalid.
     * @since 1.3
     */
    public function addOwnerCol($colName) {
        $owner = $this->getOwner();
        if($owner !== null){
            $col = $owner->getCol($colName);
            if($col instanceof Column){
                $this->ownerCols[] = $col->getName();
                return true;
            }
        }
        return false;
    }
    /**
     * Returns an array that contains the names of the referenced columns.
     * @return string An array that contains the names of the referenced columns.
     * @since 1.3
     */
    public function getSourceCols(){
        return $this->referencedTableCols;
    }
    /**
     * Adds new source column.
     * This method will work only if the source table is set. Also, the given 
     * column name must belong to the source table.
     * @param string $colName The name of the column as specified while 
     * initializing the owner table.
     * @return boolean true if source column is added. false otherwise.
     * @since 1.3
     */
    public function addSourceCol($colName){
        $source = $this->getSource();
        if($source !== null){
            $col = $source->getCol($colName);
            if($col instanceof Column){
                $this->referencedTableCols[] = $col->getName();
                return true;
            }
        }
        return false;
    }
    /**
     * Sets the table who owns the key.
     * The table that owns the key is simply the table that will take values 
     * from source table.
     * @param MySQLTable $table An object of type 'MySQLTable'.
     * @since 1.3.1
     */
    public function setOwner($table) {
        if($table instanceof MySQLTable){
            $this->ownerTableObj = $table;
            $this->ownerCols = [];
        }
    }
    /**
     * Returns the table who owns the key.
     * The table that owns the key is simply the table that will take values 
     * from source table.
     * @return MySQLTable|null If the key owner is set, the method will return 
     * an object of type 'MySQLTable'. that represent it. If not set, 
     * the method will return null.
     * @since 1.3.1
     */
    public function getOwner() {
        return $this->ownerTableObj;
    }
    /**
     * Sets the source table that will be referenced.
     * The source table is simply the table that will contain 
     * original values.
     * @param MySQLTable $table An object of type 'MySQLTable'.
     * @since 1.3.1
     */
    public function setSource($table) {
        if($table instanceof MySQLTable){
            $this->sourceTableObj = $table;
            $this->referencedTableCols = [];
        }
    }
    /**
     * Returns the source table.
     * The source table is simply the table that will contain 
     * original values.
     * @return MySQLTable|null If the source is set, the method will return 
     * an object of type 'MySQLTable'. that represent it. If not set, 
     * the method will return null.
     * @since 1.3.1
     */
    public function getSource() {
        return $this->sourceTableObj;
    }
    /**
     * Returns the condition that will happen if the value of the column in the 
     * reference table is deleted.
     * @return string|null The on delete condition as string or null in 
     * case it is not set.
     * @since 1.0 
     */
    public function getOnDelete(){
        return $this->onDeleteCondition;
    }
    /**
     * Sets the value of the property $onUpdateCondition.
     * @param string $val A value from the array ForeignKey::CONDITIONS. 
     * If the given value is null, the condition will be set to null.
     * @since 1.0
     */
    public function setOnDelete($val){
        $fix = strtolower(trim($val));
        if(in_array($fix, self::CONDITIONS)){
            $this->onDeleteCondition = $fix;
        }
        else if ($val === null) {
            $this->onDeleteCondition = null;
        }
    }
    /**
     * Returns the condition that will happen if the value of the column in the 
     * reference table is updated.
     * @return string|null The on update condition as string or null in 
     * case it is not set.
     * @since 1.0 
     */
    public function getOnUpdate(){
        return $this->onUpdateCondition;
    }
    /**
     * Sets the value of the property $onUpdateCondition.
     * @param string $val A value from the array ForeignKey::CONDITIONS. 
     * If the given value is null, the condition will be set to null.
     * @since 1.0
     */
    public function setOnUpdate($val){
        $fix = strtolower(trim($val));
        if(in_array($fix, self::CONDITIONS)){
            $this->onUpdateCondition = $fix;
        }
        elseif ($val == null) {
            $this->onUpdateCondition = null;
        }
    }

    /**
     * Returns the name of the table that is referenced by the key.
     * The referenced table is simply the table that contains original values.
     * @return string The name of the table that is referenced by the key. If 
     * it is not set, the method will return empty string.
     * @since 1.0
     */
    public function getSourceName(){
        $source = $this->getSource();
        if($source !== null){
            return $source->getName();
        }
        return '';
    }
    /**
     * Creates new foreign key.
     * @param string $name The name of the key. It must be a string and its not empty. 
     * Also it must not contain any spaces or any characters other than A-Z, a-z and 
     * underscore. The default value is 'key_name'.
     * @param MySQLTable $ownerTable The table that will contain the key.
     * @param array|string $ownerCols An array that contains the names of the columns that 
     * will be reference the source table. They must belong to the owner table. Also, 
     * it can be a string if it is only one column.
     * @param MySQLTable $sourceTable The name of the table that contains the 
     * original values.
     * @param array|string $sourceCols An array that contains the names of the columns that 
     * will be referenced. They must belong to the source table. Also, 
     * it can be a string if it is only one column.
     */
    public function __construct(
            $name='key_name',
            $ownerTable=null,
            $ownerCols=null,
            $sourceTable=null,
            $sourceCols=null) {
        $this->referencedTableCols = [];
        $this->ownerCols = [];
        if($sourceTable instanceof MySQLTable){
            $this->setSource($sourceTable);
        }
        if($ownerTable instanceof MySQLTable){
            $this->setOwner($ownerTable);
        }
        if($this->setKeyName($name) !== true){
            $this->setKeyName('key_name');
        }
        if(gettype($ownerCols) == 'array'){
            foreach ($ownerCols as $col){
                $this->addOwnerCol($col);
            }
        }
        else{
            $this->addOwnerCol($ownerCols);
        }
        if(gettype($sourceCols) == 'array'){
            foreach ($sourceCols as $col){
                $this->addSourceCol($col);
            }
        }
        else{
            $this->addSourceCol($sourceCols);
        }
    }
}
