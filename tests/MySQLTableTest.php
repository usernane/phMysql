<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phMysql\tests;
use phMysql\MySQLTable;
use PHPUnit\Framework\TestCase;
use phMysql\tests\ArticleQuery;
/**
 * A set of test units for testing the class 'MySQLTable'.
 *
 * @author Ibrahim
 */
class MySQLTableTest extends TestCase{
    /**
     * @test
     */
    public function testAddDefaultCols00() {
        $table = new MySQLTable();
        $table->addDefaultCols();
        $this->assertEquals(3,count($table->columns()));
        $this->assertTrue($table->hasColumn('id'));
        $this->assertTrue($table->hasColumn('created-on'));
        $this->assertTrue($table->hasColumn('last-updated'));
    }
    /**
     * @test
     */
    public function testRemoveColumn00() {
        $query = new ArticleQuery();
        $table = $query->getStructure();
        $this->assertTrue($table->removeColumn('author-id'));
        $this->assertEquals(3,count($table->columns()));
        $this->assertFalse($table->hasColumn('author-id'));
    }
    /**
     * @test
     */
    public function testRemoveColumn01() {
        $query = new ArticleQuery();
        $table = $query->getStructure();
        $this->assertTrue($table->removeColumn(0));
        $this->assertEquals(3,count($table->columns()));
        $this->assertFalse($table->hasColumn('article-id'));
    }
    /**
     * @test
     */
    public function testRemoveColumn02() {
        $query = new ArticleQuery();
        $table = $query->getStructure();
        $this->assertTrue($table->removeColumn(1));
        $this->assertEquals(3,count($table->columns()));
        $this->assertFalse($table->hasColumn('author-id'));
    }
    /**
     * @test
     */
    public function testRemoveColumn03() {
        $query = new ArticleQuery();
        $table = $query->getStructure();
        $this->assertTrue($table->removeColumn(2));
        $this->assertEquals(3,count($table->columns()));
        $this->assertFalse($table->hasColumn('author-name'));
    }
    /**
     * @test
     */
    public function testRemoveColumn04() {
        $query = new ArticleQuery();
        $table = $query->getStructure();
        $this->assertFalse($table->removeColumn(20));
        $this->assertEquals(4,count($table->columns()));
    }
}
