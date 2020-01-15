<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phMysql\tests;
use phMysql\ForeignKey;
use phMysql\MySQLTable;
use phMysql\MySQLColumn;
use PHPUnit\Framework\TestCase;
/**
 * Description of FKTest
 *
 * @author Ibrahim
 */
class FKTest extends TestCase{
    /**
     * @test
     */
    public function testSetOwner00() {
        $fk = new ForeignKey();
        $fk->setOwner(null);
        $this->assertNull($fk->getOwner());
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $fk = new ForeignKey();
        $this->assertNull($fk->getOwner());
        $this->assertNull($fk->getSource());
        $this->assertEquals('',$fk->getSourceName());
        $this->assertNull($fk->getOnDelete());
        $this->assertNull($fk->getOnUpdate());
        $this->assertEquals('key_name',$fk->getKeyName());
        $this->assertEquals([],$fk->getSourceCols());
        $this->assertEquals([],$fk->getOwnerCols());
        return $fk;
    }
    /**
     * @test
     */
    public function testConstructor02() {
        $fk = new ForeignKey('invalid name');
        $this->assertEquals('key_name',$fk->getKeyName());
        $this->assertFalse($fk->setKeyName('0invalid'));
        $this->assertEquals('key_name',$fk->getKeyName());
        return $fk;
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $owner = new MySQLTable('users');
        $owner->addColumn('user-id', new MySQLColumn('id', 'int'));
        $owner->addColumn('user-name', new MySQLColumn('name', 'varchar'));
        $owner->addColumn('user-password', new MySQLColumn('password', 'varchar'));
        $owner->addColumn('user-email', new MySQLColumn('email', 'varchar'));
        $source = new MySQLTable('anothet_table');
        $source->addColumn('user-id', new MySQLColumn('user_id', 'int'));
        $source->addColumn('email', new MySQLColumn('email', 'int'));
        $source->addColumn('email-2', new MySQLColumn('email_2', 'varchar'));
        $fk = new ForeignKey('new_key',$owner,$source,[
            'user-id'=>'user-id',
            'user-email'=>'email-2'
        ]);
        $this->assertNotNull($fk->getOwner());
        $this->assertEquals(2,count($fk->getOwnerCols()));
        $this->assertEquals(2,count($fk->getSourceCols()));
        return $fk;
    }
    /**
     * @test
     * @param ForeignKey $fk
     * @depends testConstructor01
     */
    public function testRemoveReference00($fk) {
        $this->assertFalse($fk->removeReference('not-exist'));
        $this->assertEquals(2,count($fk->getOwnerCols()));
        $this->assertEquals(2,count($fk->getSourceCols()));
        $this->assertFalse($fk->removeReference(' email-2 '));
        $this->assertEquals(2,count($fk->getOwnerCols()));
        $this->assertEquals(2,count($fk->getSourceCols()));
        $this->assertTrue($fk->removeReference(' user-email'));
        $this->assertTrue($fk->removeReference(' user-id'));
        $this->assertEquals(0,count($fk->getOwnerCols()));
        $this->assertEquals(0,count($fk->getSourceCols()));
        return $fk;
    }
    /**
     * 
     * @param ForeignKey $fk
     * @depends testRemoveReference00
     */
    public function testAddReference00($fk) {
        $this->assertTrue($fk->addReference('user-id'));
        $this->assertEquals(1,count($fk->getOwnerCols()));
        $this->assertEquals(1,count($fk->getSourceCols()));
        $this->assertTrue($fk->addReference('user-id'));
        $this->assertEquals(1,count($fk->getOwnerCols()));
        $this->assertEquals(1,count($fk->getSourceCols()));
        $this->assertFalse($fk->addReference('user-email'));
        $this->assertEquals(1,count($fk->getOwnerCols()));
        $this->assertEquals(1,count($fk->getSourceCols()));
        $this->assertTrue($fk->addReference('user-email','email-2'));
        $this->assertEquals(2,count($fk->getOwnerCols()));
        $this->assertEquals(2,count($fk->getSourceCols()));
    }
    /**
     * @test
     */
    public function testSetOnDelete() {
        $fk = new ForeignKey();
        $fk->setOnDelete('cascade');
        $this->assertEquals('cascade',$fk->getOnDelete());
        $fk->getOnDelete('some random');
        $this->assertEquals('cascade',$fk->getOnDelete());
        $fk->setOnDelete('SET Null');
        $this->assertEquals('set null',$fk->getOnDelete());
        $fk->setOnDelete('cascade');
        $this->assertEquals('cascade',$fk->getOnDelete());
        $fk->setOnDelete(null);
        $this->assertNull($fk->getOnDelete());
        $fk->setOnDelete('  no actioN   ');
        $this->assertEquals('no action',$fk->getOnDelete());
    }
    /**
     * @test
     */
    public function testSetOnUpdate() {
        $fk = new ForeignKey();
        $fk->setOnUpdate('restrict');
        $this->assertEquals('restrict',$fk->getOnUpdate());
        $fk->setOnUpdate('some random');
        $this->assertEquals('restrict',$fk->getOnUpdate());
        $fk->setOnUpdate('SET default');
        $this->assertEquals('set default',$fk->getOnUpdate());
        $fk->setOnUpdate('cascade');
        $this->assertEquals('cascade',$fk->getOnUpdate());
        $fk->setOnUpdate(null);
        $this->assertNull($fk->getOnUpdate());
        $fk->setOnUpdate('  no actioN   ');
        $this->assertEquals('no action',$fk->getOnUpdate());
    }
    /**
     * @test
     */
    public function testSetKeyName00() {
        $fk = new ForeignKey();
        $this->assertTrue($fk->setKeyName('valid_key_name'));
        $this->assertEquals('valid_key_name',$fk->getKeyName());
    }
    /**
     * @test
     */
    public function testSetKeyName01() {
        $fk = new ForeignKey();
        $this->assertFalse($fk->setKeyName('0invalid_key_name'));
        $this->assertEquals('key_name',$fk->getKeyName());
    }
    /**
     * @test
     */
    public function testSetKeyName02() {
        $fk = new ForeignKey();
        $this->assertFalse($fk->setKeyName('invalid-key-name'));
        $this->assertEquals('key_name',$fk->getKeyName());
    }
    /**
     * @test
     */
    public function testSetKeyName03() {
        $fk = new ForeignKey();
        $this->assertTrue($fk->setKeyName("   valid_key_name \n"));
        $this->assertEquals('valid_key_name',$fk->getKeyName());
    }
    /**
     * @test
     */
    public function testSetKeyName04() {
        $fk = new ForeignKey();
        $this->assertFalse($fk->setKeyName(''));
        $this->assertEquals('key_name',$fk->getKeyName());
    }
}
