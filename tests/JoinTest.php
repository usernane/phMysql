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
        $joinQuery = $articleQ->join($userQ);
        $joinQuery->select();
        $this->assertEquals('select * from (select * from articles left join system_users) as T0;',$joinQuery->getQuery());
    }
    /**
     * @test
     */
    public function test01() {
        $articleQ = new ArticleQuery();
        $userQ = new UsersQuery();
        $joinQuery = $articleQ->join($userQ);
        foreach ($articleQ->getTable()->getColumns() as $index => $colObj){
            $this->assertTrue($joinQuery->getTable()->hasColumn($index));
        }
        foreach ($userQ->getTable()->getColumns() as $index => $colObj){
            $this->assertTrue($joinQuery->getTable()->hasColumn($index));
        }
    }
    public function test02() {
        $articleQ = new ArticleQuery();
        $userQ = new UsersQuery();
        $joinQuery = $articleQ->join($userQ);
        $joinQuery->select([
            'where'=>[
                'article-id'=>55
            ],
            'as-view'=>true,
            'view-name'=>'author_content'
        ]);
        $this->assertEquals('',$joinQuery->getQuery());
    }
}
