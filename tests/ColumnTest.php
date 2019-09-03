<?php
namespace phMysql\tests;
use PHPUnit\Framework\TestCase;
use phMysql\Column;
/**
 * Unit tests for testing the class 'Column'.
 *
 * @author Ibrahim
 */
class ColumnTest extends TestCase{
    /**
     * @test
     */
    public function testConstructor00() {
        $col = new Column();
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(1,$col->getSize());
        $this->assertEquals('col',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $col = new Column('user_id ', 'varchar', 15);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(15,$col->getSize());
        $this->assertEquals('user_id',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor02() {
        $col = new Column('invalid name', 'varchar', 15);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(15,$col->getSize());
        $this->assertEquals('col',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $col = new Column('0invalid_name', 'varchar', 15);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(15,$col->getSize());
        $this->assertEquals('col',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor04() {
        $col = new Column('valid_name', 'invalid type', 15);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(15,$col->getSize());
        $this->assertEquals('valid_name',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor05() {
        $col = new Column('valid_name', 'InT', 15);
        $this->assertEquals('int',$col->getType());
        $this->assertEquals(11,$col->getSize());
        $this->assertEquals('valid_name',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor06() {
        $col = new Column('valid_name', 'Varchar ', 15);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(15,$col->getSize());
        $this->assertEquals('valid_name',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor07() {
        $col = new Column('valid_name', 'Varchar ', 21846);
        $this->assertEquals('mediumtext',$col->getType());
        $this->assertEquals(21846,$col->getSize());
        $this->assertEquals('valid_name',$col->getName());
    }
    /**
     * @test
     */
    public function testSetColName00() {
        $col = new Column();
        $this->assertTrue($col->setName('my_file'));
        $this->assertEquals('my_file',$col->getName());
        $this->assertTrue($col->setName('   user_id   '));
        $this->assertEquals('user_id',$col->getName());
    }
}
