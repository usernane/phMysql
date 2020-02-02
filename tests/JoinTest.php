<?php
namespace phMysql\tests;
use PHPUnit\Framework\TestCase;
use phMysql\JoinTable;
use phMysql\MySQLTable;
use phMysql\tests\ArticleJoinUserQuery;
use phMysql\tests\UsersQuery;
use phMysql\MySQLQuery;
/**
 * Description of JoinTest
 *
 * @author Ibrahim
 */
class JoinTest extends TestCase{
    /**
     * @test
     */
    public function testConstructor00() {
        $table = new JoinTable(null, null);
        $this->assertEquals('left_table',$table->getLeftTable()->getName());
        $this->assertEquals('right_table',$table->getRightTable()->getName());
        $this->assertEquals('left',$table->getJoinType());
        $this->assertNull($table->getJoinCondition());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $leftTable = new MySQLTable('a_left_table');
        $leftTable->addDefaultCols();
        $rightTable = new MySQLTable('a_right_table');
        $rightTable->addDefaultCols();
        $joinTable = new JoinTable($leftTable, $rightTable, 'JoinTable');
        $this->assertEquals('a_left_table',$joinTable->getLeftTable()->getName());
        $this->assertEquals('a_right_table',$joinTable->getRightTable()->getName());
        $keys = $joinTable->colsKeys();
        $this->assertEquals(6,count($keys));
        $this->assertTrue($joinTable->hasColumn('left-id'));
        $this->assertTrue($joinTable->hasColumn('left-created-on'));
        $this->assertTrue($joinTable->hasColumn('left-last-updated'));
        $this->assertTrue($joinTable->hasColumn('right-id'));
        $this->assertTrue($joinTable->hasColumn('right-created-on'));
        $this->assertTrue($joinTable->hasColumn('right-last-updated'));
        
        $this->assertEquals('left_id',$joinTable->getCol('left-id')->getName());
        $this->assertEquals('left_created_on',$joinTable->getCol('left-created-on')->getName());
        $this->assertEquals('left_last_updated',$joinTable->getCol('left-last-updated')->getName());
        $this->assertEquals('right_id',$joinTable->getCol('right-id')->getName());
        $this->assertEquals('right_created_on',$joinTable->getCol('right-created-on')->getName());
        $this->assertEquals('right_last_updated',$joinTable->getCol('right-last-updated')->getName());
        return $joinTable;
    }
    /**
     * @depends testConstructor01
     * @param JoinTable $table
     */
    public function testSetJoinCondition00($table) {
        $table->setJoinCondition(['id'=>'id']);
        $this->assertEquals('on a_left_table.id = a_right_table.id',$table->getJoinCondition());
        $table->setJoinCondition(['id'=>'id','created-on'=>'created-on']);
        $this->assertEquals('on a_left_table.id = a_right_table.id and a_left_table.created_on = a_right_table.created_on',$table->getJoinCondition());
    }
    /**
     * @depends testSetJoinCondition00
     * @param JoinTable $table
     */
    public function testJoinSelect00($table) {
        $query = new MySQLQuery();
        $query->setTable($table);
        $query->select();
        $this->assertEquals('',$query->getQuery());
    }
}






