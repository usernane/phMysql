<?php
namespace phMysql\tests;
use phMysql\MySQLQuery;
use phMysql\Column;
use phMysql\MySQLTable;

/**
 * Description of UsersQuery
 *
 * @author Ibrahim
 */
class UsersQuery extends MySQLQuery{
    /**
     *
     * @var MySQLTable 
     */
    private $table;
    public function __construct() {
        parent::__construct();
        $this->table = new MySQLTable('system_users');
        $this->table->addColumn('user-id', new Column('user_id', 'int', 11));
        $this->table->addColumn('name', new Column('name', 'varchar', 25));
        $this->table->addColumn('email', new Column('email', 'varchar', 100));
    }
    /**
     * 
     * @return MySQLTable
     */
    public function getStructure(){
        return $this->table;
    }

}
