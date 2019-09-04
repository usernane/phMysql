<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phMysql\tests;
use phMysql\ForeignKey;
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
     * 
     * @param ForeignKey $fk
     * @test
     * @depends testConstructor00
     */
    public function testAddSourceCol($fk) {
        $this->assertFalse($fk->addSourceCol('no-link'));
    }
    /**
     * 
     * @param ForeignKey $fk
     * @test
     * @depends testConstructor00
     */
    public function testAddOwnerCol($fk) {
        $this->assertFalse($fk->addOwnerCol('no-link'));
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
