<?php
namespace phMysql\tests;
use phMysql\MySQLQuery;
use phMysql\Column;
use phMysql\MySQLTable;
/**
 * Description of ArticleQuery
 *
 * @author Ibrahim
 */
class ArticleQuery extends MySQLQuery{
    /**
     *
     * @var MySQLTable 
     */
    private $table;
    public function __construct() {
        parent::__construct();
        $this->table = new MySQLTable('articles');
        $this->table->addColumn('article-id', new Column('article_id', 'int', 11));
        $this->table->addColumn('author-id', new Column('author_id', 'int', 11));
        $this->table->addColumn('content', new Column('content', 'varchar', 5000));
    }
    /**
     * 
     * @return MySQLTable
     */
    public function getStructure(){
        return $this->table;
    }

}

