<?php
namespace phMysql\tests;
use PHPUnit\Framework\TestCase;
use phMysql\tests\ArticleJoinUserQuery;
use phMysql\tests\UsersQuery;
/**
 * Description of JoinTest
 *
 * @author Ibrahim
 */
class JoinTest extends TestCase{
    /**
     * @test
     */
    public function test00() {
        $articleQ = new ArticleQuery();
        $userQ = new UsersQuery();
        $joinQuery = $articleQ->join($userQ,['author-id'=>'user-id'],'left');
        $joinQuery->select();
        $this->assertEquals('select * from (select * from articles left join system_users on articles.author_id = system_users.user_id) as T0;',$joinQuery->getQuery());
    }
    /**
     * @test
     */
    public function test01() {
        $articleQ = new ArticleQuery();
        $userQ = new UsersQuery();
        $joinQuery = $articleQ->join($userQ,['author-id'=>'user-id'],'left');
        foreach ($articleQ->getTable()->getColumns() as $index => $colObj){
            $this->assertTrue($joinQuery->getTable()->hasColumn($index));
        }
        foreach ($userQ->getTable()->getColumns() as $index => $colObj){
            $this->assertTrue($joinQuery->getTable()->hasColumn($index));
        }
    }
    /**
     * @test
     */
    public function test02() {
        $articleQ = new ArticleQuery();
        $userQ = new UsersQuery();
        $joinQuery = $articleQ->join($userQ,[
            'author-id'=>'user-id'
        ],'left',[],[],'myT');
        $joinQuery->select([
            'where'=>[
                'article-id'=>55
            ],
            'as-view'=>true,
            'view-name'=>'author_content'
        ]);
        $this->assertEquals('create view author_content as (select * from (select * from articles left join system_users on articles.author_id = system_users.user_id) as myT where myT.article_id = 55);',$joinQuery->getQuery());
    }
}
