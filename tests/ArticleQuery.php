<?php
namespace phMysql\tests;
use phMysql\MySQLQuery;
use phMysql\MySQLColumn;
use phMysql\MySQLTable;
/**
 * Description of ArticleQuery
 *
 * @author Ibrahim
 */
class ArticleQuery extends MySQLQuery{
    public function __construct() {
        parent::__construct('articles');
        $this->getTable()->addDefaultCols([
            'id'=>[
                'key-name'=>'article-id',
                'db-name'=>'article_id'
            ],
            'created-on'=>[],
            'last-updated'=>[]
        ]);
        $this->getTable()->addColumns([
            'author-id'=>[
                'name'=>'author_id',
                'datatype'=>'int',
                'size'=>11
            ],
            'author-name'=>[
                'name'=>'author_name',
                'size'=>20,
                'is-primary'=>true
            ],
            'content'=>[
                'name'=>'content',
                'size'=>5000
            ]
        ]);
        
        $this->getTable()->addReference('phMysql\tests\UsersQuery', [
            'author-id'=>'user-id'
        ], 'author_fk');
    }
}

