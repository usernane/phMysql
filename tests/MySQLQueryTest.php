<?php
namespace phMysql\tests;
use PHPUnit\Framework\TestCase;
use phMysql\ForeignKey;
use phMysql\MySQLTable;
use phMysql\tests\QueryTestObj;
use phMysql\tests\ArticleQuery;
/**
 * Unit tests for testing the class 'MySQLQuery'.
 *
 * @author Ibrahim
 */
class MySQLQueryTest extends TestCase{
    /**
     * @test
     */
    public function testAddPrimaryKey00() {
        $aq = new ArticleQuery();
        $aq->addPrimaryKey($aq->getStructure());
        $this->assertEquals("alter table articles add constraint articles_pk primary key (article_id,author_name);\n"
                . "alter table articles modify article_id int(11) not null unique auto_increment;",$aq->getQuery());
    }
    /**
     * @test
     */
    public function testAddForeignKey00() {
        $aq = new ArticleQuery();
        $aq->addForeignKey($aq->getStructure()->getForeignKeys()[0]);
        $this->assertEquals("alter table articles add constraint author_fk foreign key (author_id) references system_users(user_id) on delete set null on update set null",$aq->getQuery());
    }
    /**
     * @test
     */
    public function createStructureTest00() {
        $articleQuery = new ArticleQuery();
        $articleQuery->createStructure();
        $this->assertEquals("",'');
    }
    /**
     * @test
     */
    public function testSelect000() {
        $obj = new QueryTestObj();
        $obj->select();
        $this->assertEquals('select * from first_table;',$obj->getQuery());
    }
    /**
     * @test
     */
    public function testSelect001() {
        $obj = new QueryTestObj();
        $obj->select([
            'limit'=>3
        ]);
        $this->assertEquals('select * from first_table limit 3;',$obj->getQuery());
    }
    /**
     * @test
     */
    public function testSelect002() {
        $obj = new QueryTestObj();
        $obj->select([
            'limit'=>3,
            'offset'=>7
        ]);
        $this->assertEquals('select * from first_table limit 3 offset 7;',$obj->getQuery());
    }
    /**
     * @test
     */
    public function testSelect004() {
        $obj = new QueryTestObj();
        $obj->select([
            'offset'=>3
        ]);
        $this->assertEquals('select * from first_table;',$obj->getQuery());
    }
    /**
     * @test
     */
    public function testSelect005() {
        $obj = new QueryTestObj();
        $obj->select([
            'order-by'=>[
                [
                    'col'=>'first-col'
                ]
            ]
        ]);
        $this->assertEquals('select * from first_table order by col_00;',$obj->getQuery());
    }
    /**
     * @test
     */
    public function testSelect006() {
        $obj = new QueryTestObj();
        $obj->select([
            'order-by'=>[
                [
                    'col'=>'first-col',
                    'order-type'=>'D'
                ],
                [
                    'col'=>'second-col'
                ],
                [
                    'col'=>'fourth-col',
                    'order-type'=>'A'
                ]
            ]
        ]);
        $this->assertEquals('select * from first_table order by col_00 desc, col_01, col_03 asc;',$obj->getQuery());
    }
    /**
     * @test
     */
    public function testSelect007() {
        $obj = new QueryTestObj();
        $obj->select([
            'columns'=>['first-col'],
            'order-by'=>[
                [
                    'col'=>'first-col',
                    'order-type'=>'D'
                ],
                [
                    'col'=>'second-col'
                ],
                [
                    'col'=>'fourth-col',
                    'order-type'=>'A'
                ]
            ]
        ]);
        $this->assertEquals('select col_00 from first_table order by col_00 desc, col_01, col_03 asc;',$obj->getQuery());
    }
    /**
     * @test
     */
    public function testSelect008() {
        $obj = new QueryTestObj();
        $obj->select([
            'columns'=>['first-col','fourth-col'],
            'order-by'=>[
                [
                    'col'=>'first-col',
                    'order-type'=>'D'
                ],
                [
                    'col'=>'second-col'
                ],
                [
                    'col'=>'fourth-col',
                    'order-type'=>'A'
                ]
            ]
        ]);
        $this->assertEquals('select col_00,col_03 from first_table order by col_00 desc, col_01, col_03 asc;',$obj->getQuery());
    }
    /**
     * @test
     */
    public function testSelect009() {
        $obj = new QueryTestObj();
        $obj->select([
            'order-by'=>[
                [
                    'col'=>'first-col'
                ]
            ],
            'group-by'=>[
                [
                    'col'=>'first-col'
                ]
            ]
        ]);
        $this->assertEquals('select * from first_table group by col_00 order by col_00;',$obj->getQuery());
    }
    /**
     * @test
     */
    public function testSelect010() {
        $obj = new QueryTestObj();
        $obj->select([
            'condition-cols-and-vals'=>[
                'fourth-col'=>'7U'
            ],
            'order-by'=>[
                [
                    'col'=>'first-col'
                ]
            ],
            'group-by'=>[
                [
                    'col'=>'fourth-col'
                ],
                [
                    'col'=>'first-col'
                ]
            ]
        ]);
        $this->assertEquals('select * from first_table where col_03 = \'7U\' group by col_03, col_00 order by col_00;',$obj->getQuery());
    }
    /**
     * @test
     */
    public function testSelect011() {
        $obj = new QueryTestObj();
        $obj->select([
            'condition-cols-and-vals'=>[
                'fourth-col'=>'7U',
                'first-col'=>'*I',
                'third-col'=>'X'
            ],
            'order-by'=>[
                [
                    'col'=>'first-col'
                ]
            ],
            'group-by'=>[
                [
                    'col'=>'fourth-col'
                ],
                [
                    'col'=>'first-col'
                ]
            ]
        ]);
        $this->assertEquals('select * from first_table where col_03 = \'7U\' and col_00 = \'*I\' and col_02 = \'X\' group by col_03, col_00 order by col_00;',$obj->getQuery());
    }
}
