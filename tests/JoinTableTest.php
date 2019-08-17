<?php

namespace phMysql\tests;
namespace phMysql\tests;
use PHPUnit\Framework\TestCase;
use phMysql\JoinTable;
Use phMysql\tests\UsersQuery;
use phMysql\tests\ArticleQuery;
/**
 * Description of JoinTableTest
 *
 * @author Ibrahim
 */
class JoinTableTest extends TestCase{
    /**
     * @test
     */
    public function testGetJoinStatement00() {
        $q1 = new UsersQuery();
        $q2 = new ArticleQuery();
        $join = new JoinTable($q1->getStructure(), $q2->getStructure());
        $this->assertEquals('system_users join articles',$join->getJoinStatement());
    }
    public function testGetOnCondition00() {
        $q1 = new UsersQuery();
        $q2 = new ArticleQuery();
        $join = new JoinTable($q1->getStructure(), $q2->getStructure());
        $on = $join->getOnCondition([
            'user-id'=>'author-id'
        ]);
        $this->assertEquals('on (system_users.user_id = articles.author_id)',$on);
    }
}
