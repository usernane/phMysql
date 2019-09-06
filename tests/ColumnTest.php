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
    public function testConstructor08() {
        $col = new Column('valid_name', 'Varchar ', 0);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(1,$col->getSize());
        $this->assertEquals('valid_name',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor09() {
        $col = new Column('amount', 'decimal ');
        $this->assertEquals('decimal',$col->getType());
        $this->assertEquals(0,$col->getSize());
        return $col;
    }
    /**
     * 
     * @param Column $col
     * @depends testConstructor09
     */
    public function testSetScale00($col) {
        $col->setSize(10);
        $this->assertTrue($col->setScale(3));
        $this->assertEquals(3,$col->getScale());
        $this->assertTrue($col->setScale(0));
        $this->assertEquals(0,$col->getScale());
        $this->assertTrue($col->setScale(9));
        $this->assertEquals(9,$col->getScale());
        $this->assertFalse($col->setScale(10));
        $this->assertEquals(9,$col->getScale());
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
        $this->assertFalse($col->setName('0user_id   '));
        $this->assertFalse($col->setName('use id   '));
        $this->assertFalse($col->setName('  '));
    }
    /**
     * @test
     */
    public function setCommentTest00() {
        $col = new Column('user_id ', 'varchar', 15);
        $col->setComment('A unique ID for the user.');
        $this->assertEquals('A unique ID for the user.',$col->getComment());
        $this->assertEquals('user_id varchar(15) not null collate utf8mb4_unicode_ci comment \'A unique ID for the user.\'',$col.'');
        return $col;
    }
    /**
     * @test
     * @depends setCommentTest00
     * @param Column $col Description
     */
    public function setCommentTest01($col) {
        $col->setComment(null);
        $this->assertNull($col->getComment());
        $this->assertEquals('user_id varchar(15) not null collate utf8mb4_unicode_ci',$col.'');
    }
    /**
     * @test
     */
    public function testSetDefault00() {
        $col = new Column('date', 'timestamp');
        $col->setDefault();
        $this->assertEquals('current_timestamp',$col->getDefault());
        $this->assertEquals('date timestamp not null default current_timestamp',$col.'');
    }
    /**
     * @test
     */
    public function testSetDefault01() {
        $col = new Column('date', 'timestamp');
        $col->setDefault('2019-07-07 09:09:09');
        $this->assertEquals('2019-07-07 09:09:09',$col->getDefault());
        $this->assertEquals('date timestamp not null default \'2019-07-07 09:09:09\'',$col.'');
    }
    /**
     * @test
     */
    public function testSetDefault02() {
        $col = new Column('date', 'datetime');
        $col->setDefault();
        $this->assertEquals('now()',$col->getDefault());
        $this->assertEquals('date datetime not null default now()',$col.'');
    }
    /**
     * @test
     */
    public function testSetDefault03() {
        $col = new Column('date', 'datetime');
        $col->setDefault('2019-07-07 09:09:09');
        $this->assertEquals('2019-07-07 09:09:09',$col->getDefault());
        $this->assertEquals('date datetime not null default \'2019-07-07 09:09:09\'',$col.'');
    }
    /**
     * @test
     */
    public function testSetDefault04() {
        $col = new Column('date', 'datetime');
        $col->setDefault('2019-15-07 09:09:09');
        $this->assertNull($col->getDefault());
        $this->assertEquals('date datetime not null',$col.'');
        $col->setDefault('2019-12-33 09:09:09');
        $this->assertNull($col->getDefault());
        $this->assertEquals('date datetime not null',$col.'');
        $col->setDefault('2019-12-31 24:09:09');
        $this->assertNull($col->getDefault());
        $col->setDefault('2019-12-31 23:60:09');
        $this->assertNull($col->getDefault());
        $col->setDefault('2019-12-31 23:59:60');
        $this->assertNull($col->getDefault());
        $col->setDefault('2019-12-31 23:59:59');
        $this->assertEquals('2019-12-31 23:59:59',$col->getDefault());
    }
    /**
     * @test
     */
    public function testSetDefault05() {
        $col = new Column('id', 'int');
        $this->assertEquals('id int(1) not null',$col.'');
        $this->assertTrue($col->setDefault(-122));
        $this->assertEquals(-122,$col->getDefault());
        $this->assertEquals('id int(1) not null default -122',$col.'');
        $this->assertFalse($col->setDefault(null));
        $this->assertFalse($col->setDefault('a string'));
        $this->assertFalse($col->setDefault(1.8));
    }
    /**
     * @test
     */
    public function testSetDefault06() {
        $col = new Column('id', 'varchar');
        $this->assertEquals('id varchar(1) not null collate utf8mb4_unicode_ci',$col.'');
        $this->assertTrue($col->setDefault('A random string.'));
        $this->assertEquals('A random string.',$col->getDefault());
        $this->assertEquals('id varchar(1) not null collate utf8mb4_unicode_ci default \'A random string.\'',$col.'');
        $this->assertFalse($col->setDefault(null));
        $this->assertFalse($col->setDefault(33));
        $this->assertFalse($col->setDefault(1.8));
    }
    /**
     * @test
     */
    public function testSetDefault07() {
        $col = new Column('id', 'decimal');
        $this->assertEquals('id decimal not null',$col.'');
        $this->assertTrue($col->setDefault(1));
        $this->assertEquals(1,$col->getDefault());
        $this->assertEquals('id decimal not null default \'1\'',$col.'');
        $this->assertTrue($col->setDefault(1.66));
        $this->assertEquals(1.66,$col->getDefault());
        $this->assertEquals('id decimal not null default \'1.66\'',$col.'');
        $this->assertFalse($col->setDefault(null));
        $this->assertFalse($col->setDefault('33'));
        $this->assertFalse($col->setDefault(''));
    }
}
