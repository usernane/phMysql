<?php
namespace phMysql\tests;

use phMysql\MySQLColumn;
use phMysql\MySQLQuery;
use phMysql\MySQLTable;

/**
 * Description of UsersQuery
 *
 * @author Ibrahim
 */
class UsersQuery extends MySQLQuery {
    /**
     *
     * @var MySQLTable 
     */
    private $table;
    public function __construct() {
        parent::__construct();
        $this->table = new MySQLTable('system_users');
        $this->table->addColumn('user-id', new MySQLColumn('user_id', 'int', 11));
        $this->table->getCol('user-id')->setIsPrimary(true);
        $this->table->addColumn('name', new MySQLColumn('name', 'varchar', 25));
        $this->table->addColumn('email', new MySQLColumn('email', 'varchar', 100));
        $this->table->addColumn('is-active', [
            'type' => 'boolean',
            'default' => true
        ]);
        $this->setTable($this->table);
    }
}
