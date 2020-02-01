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
    /**
     * @test
     */
    public function test03() {
        $articleQ = new ArticleQuery();
        $userQ = new UsersQuery();
        $joinQuery = $articleQ->join($userQ,[
            'author-id'=>'user-id'
        ],'left',[],[],'myT');
        $joinQuery->select();
        $this->assertEquals('select * from (select * from articles left join system_users on '
                . 'articles.author_id = system_users.user_id) as myT;',$joinQuery->getQuery());
        $joinQuery2 = $joinQuery->join($articleQ, [
            'user-id'=>'author-id'
        ],'join',[],[],'XTable');
        $joinQuery2->select([
            'where'=>[
                'left-article-id'=>55
            ]
        ]);
        $this->assertEquals(''
                . 'select * from (select myT.article_id as left_article_id,'."\n"
                . 'myT.created_on as left_created_on,'."\n"
                . 'myT.last_updated as left_last_updated,'."\n"
                . 'myT.author_id as left_author_id,'."\n"
                . 'myT.title as left_title,'."\n"
                . 'myT.article_content as left_article_content,'."\n"
                . 'myT.user_id,'."\n"
                . 'myT.name,'."\n"
                . 'myT.email,'."\n"
                . 'articles.article_id as right_article_id,'."\n"
                . 'articles.created_on as right_created_on,'."\n"
                . 'articles.last_updated as right_last_updated,'."\n"
                . 'articles.author_id as right_author_id,'."\n"
                . 'articles.title as right_title,'."\n"
                . 'articles.article_content as right_article_content'."\n"
                . ' from (select * from articles left join system_users on articles.author_id = system_users.user_id) as myT join articles on myT.user_id = articles.author_id) as XTable where XTable.left_article_id = 55;'
                ,$joinQuery2->getQuery());
        $joinQuery3 = $joinQuery2->join($userQ, [
            'left-author-id'=>'user-id'
        ], 'right', [], [], 'AnotherJoin');
        $joinQuery3->select();
        $this->assertEquals(''
                . 'select * from (select XTable.user_id as left_user_id,'."\n"
                . 'XTable.name as left_name,'."\n"
                . 'XTable.email as left_email,'."\n"
                . 'XTable.right_article_id,'."\n"
                . 'XTable.right_created_on,'."\n"
                . 'XTable.right_last_updated,'."\n"
                . 'XTable.right_author_id,'."\n"
                . 'XTable.right_title,'."\n"
                . 'XTable.right_article_content,'."\n"
                . 'system_users.user_id as right_user_id,'."\n"
                . 'system_users.name as right_name,'."\n"
                . 'system_users.email as right_email'."\n"
                . ' from (select myT.article_id as left_article_id,'."\n"
                . 'myT.created_on as left_created_on,'."\n"
                . 'myT.last_updated as left_last_updated,'."\n"
                . 'myT.author_id as left_author_id,'."\n"
                . 'myT.title as left_title,'."\n"
                . 'myT.article_content as left_article_content,'."\n"
                . 'myT.user_id,'."\n"
                . 'myT.name,'."\n"
                . 'myT.email,'."\n"
                . 'articles.article_id as right_article_id,'."\n"
                . 'articles.created_on as right_created_on,'."\n"
                . 'articles.last_updated as right_last_updated,'."\n"
                . 'articles.author_id as right_author_id,'."\n"
                . 'articles.title as right_title,'."\n"
                . 'articles.article_content as right_article_content'."\n"
                . ' from (select * from articles left join system_users on articles.author_id = system_users.user_id) as myT join articles on myT.user_id = articles.author_id) as XTable right join system_users on XTable.left_author_id = system_users.user_id) as AnotherJoin;'
                . ''
                . '',$joinQuery3->getQuery());
        $joinQuery4 = $joinQuery3->join($joinQuery2, [
            'left-user-id'=>'user-id'
        ], 'join', [], [], 'SuperJoin');
        $joinQuery4->select();
    }
    /**
     * @test
     */
    public function test04() {
        $articleQ = new ArticleQuery();
        $userQ = new UsersQuery();
        $joinQuery = $articleQ->join($userQ,['author-id'=>'user-id'],'left');
        $joinQuery->select([
            'columns'=>[
                'author-id','article-id','name','title'
            ]
        ]);
        $this->assertEquals('select * from (select author_id,article_id,name,title from articles left join system_users on articles.author_id = system_users.user_id) as T0;',$joinQuery->getQuery());
    }
}
