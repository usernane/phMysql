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
    public function test00() {
        $articleQ = new ArticleQuery();
        $userQ = new UsersQuery();
        $joinQuery = $articleQ->join($userQ);
        $joinQuery->select();
        $this->assertEquals('select * from (select * from articles left join system_users) as T0;',$joinQuery->getQuery());
    }
}
