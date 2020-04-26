<?php
namespace phMysql\tests;

use phMysql\MySQLColumn;
use PHPUnit\Framework\TestCase;
/**
 * Unit tests for testing the class 'Column'.
 *
 * @author Ibrahim
 */
class ColumnTest extends TestCase {
    
    /**
     * @test
     */
    public function testCreateColObj00() {
        $obj = MySQLColumn::createColObj([]);
        $this->assertNull($obj);
    }
    /**
     * @test
     */
    public function testCreateColObj01() {
        $obj = MySQLColumn::createColObj([
            'name'=>'hello'
        ]);
        $this->assertNotNull($obj);
        $this->assertEquals('varchar',$obj->getType());
        $this->assertEquals(1,$obj->getSize());
        $this->assertFalse($obj->isNull());
        $this->assertFalse($obj->isPrimary());
        $this->assertFalse($obj->isUnique());
    }
    /**
     * @test
     */
    public function testCreateColObj02() {
        $obj = MySQLColumn::createColObj([
            'name'=>'hello',
            'type'=>'int',
            'primary'=>true,
            'size'=>5
        ]);
        $this->assertNotNull($obj);
        $this->assertEquals('int',$obj->getType());
        $this->assertEquals(5,$obj->getSize());
        $this->assertFalse($obj->isNull());
        $this->assertTrue($obj->isPrimary());
        $this->assertTrue($obj->isUnique());
    }
    /**
     * @test
     */
    public function testCreateColObj03() {
        $obj = MySQLColumn::createColObj([
            'name'=>'hello',
            'type'=>'int',
            'primary'=>true,
            'size'=>5,
            'validator'=>function($orgVal, $basicValidated){
                return "Original = '$orgVal'. Basic Cleaned = '$basicValidated'";
            }
        ]);
        $this->assertNotNull($obj);
        $this->assertEquals('int',$obj->getType());
        $this->assertEquals(5,$obj->getSize());
        $this->assertFalse($obj->isNull());
        $this->assertTrue($obj->isPrimary());
        $this->assertTrue($obj->isUnique());
        $this->assertEquals("Original = '99'. Basic Cleaned = '99'", $obj->cleanValue('99'));
        $this->assertEquals("Original = 'hello'. Basic Cleaned = '0'", $obj->cleanValue("hello"));
    }
    /**
     * @test
     */
    public function testCreateColObj04() {
        $obj = MySQLColumn::createColObj([
            'name'=>'hello',
            'type'=>'blob',
            'validator'=>function($orgVal, $basicValidated){
                return "BLOB TYPE: $orgVal";
            }
        ]);
        $this->assertNotNull($obj);
        $this->assertEquals('blob',$obj->getType());
        $this->assertFalse($obj->isNull());
        $this->assertEquals("BLOB TYPE: XYZ", $obj->cleanValue('XYZ'));
    }
    /**
     * @test
     */
    public function testCreateColObj05() {
        $obj = MySQLColumn::createColObj([
            'name'=>'hello',
            'type'=>'datetime',
            'validator'=>function($orgVal, $basicValidated){
                if($basicValidated == ''){
                    return 'now()';
                }
            }
        ]);
        $this->assertEquals("now()", $obj->cleanValue('2020-03-04 23:00'));
    }
    /**
     * @test
     */
    public function testCustomCleaner00() {
        $col = new MySQLColumn('hello', 'varchar');
        $col->setCustomFilter(function($originalVal, $basicFilterResult){
            
        });
        $this->assertNull($col->cleanValue('Hello World'));
        $col->setCustomFilter(function($originalVal, $basicFilterResult){
            return $originalVal.'?';
        });
        $this->assertEquals('Hello World.?',$col->cleanValue('Hello World.'));
    }
    /**
     * @test
     */
    public function testCustomCleaner01() {
        $col = new MySQLColumn('hello', 'int');
        $col->setCustomFilter(function($originalVal, $basicFilterResult){
            return $basicFilterResult*10;
        });
        $this->assertEquals(0,$col->cleanValue('Hello World.'));
        $this->assertEquals(10,$col->cleanValue(1));
        $this->assertEquals(260,$col->cleanValue(26));
        $col->setCustomFilter(function($originalVal, $basicFilterResult){
            return $basicFilterResult*$originalVal;
        });
        $this->assertEquals(100,$col->cleanValue(10));
        $this->assertEquals(9,$col->cleanValue(3));
    }
    /**
     * @test
     */
    public function testCustomCleaner02() {
        $col = new MySQLColumn('hello', 'int');
        $col->setCustomFilter(function(){
            return 5;
        });
        $this->assertEquals(5,$col->cleanValue('Hello World.'));
    }
    /**
     * @test
     */
    public function setCommentTest00() {
        $col = new MySQLColumn('user_id ', 'varchar', 15);
        $col->setComment('A unique ID for the user.');
        $this->assertEquals('A unique ID for the user.',$col->getComment());
        $this->assertEquals('user_id varchar(15) not null collate utf8mb4_unicode_ci comment \'A unique ID for the user.\'',$col.'');

        return $col;
    }
    /**
     * @test
     * @depends setCommentTest00
     * @param MySQLColumn $col Description
     */
    public function setCommentTest01($col) {
        $col->setComment(null);
        $this->assertNull($col->getComment());
        $this->assertEquals('user_id varchar(15) not null collate utf8mb4_unicode_ci',$col.'');
    }
    /**
     * @test
     */
    public function testAutoUpdate00() {
        $col = new MySQLColumn();
        $this->assertFalse($col->isAutoUpdate());
        $col->setAutoUpdate(true);
        $this->assertFalse($col->isAutoUpdate());
        $col->setType('datetime');
        $col->setAutoUpdate(true);
        $this->assertTrue($col->isAutoUpdate());
    }
    /**
     * @test
     */
    public function testAutoUpdate01() {
        $col = new MySQLColumn();
        $this->assertFalse($col->isAutoUpdate());
        $col->setAutoUpdate(true);
        $this->assertFalse($col->isAutoUpdate());
        $col->setType('timestamp');
        $col->setAutoUpdate(true);
        $this->assertTrue($col->isAutoUpdate());
    }
    /**
     * @test
     */
    public function testBoolean00() {
        $col = new MySQLColumn('my_col', 'boolean');
        $this->assertEquals('boolean',$col->getType());
        $this->assertEquals('my_col varchar(1) not null',$col.'');
    }
    /**
     * @test
     */
    public function testBoolean01() {
        $col = new MySQLColumn('my_col', 'bool');
        $this->assertEquals('boolean',$col->getType());
        $col->setDefault(true);
        $this->assertEquals('my_col varchar(1) not null default \'Y\'',$col.'');
        $col->setDefault(false);
        $this->assertEquals('my_col varchar(1) not null default \'N\'',$col.'');
        $col->setDefault();
        $this->assertEquals('my_col varchar(1) not null',$col.'');
        $col->setDefault('Random Val');
        $this->assertEquals('my_col varchar(1) not null default \'N\'',$col.'');
    }
    /**
     * @test
     */
    public function testBoolean02() {
        $col = new MySQLColumn('my_col', 'bool');
        $this->assertEquals('boolean',$col->getType());
        $col->setIsNull(true);
        $this->assertEquals('my_col varchar(1) not null',$col.'');
        $col->setIsAutoInc(true);
        $this->assertEquals('my_col varchar(1) not null',$col.'');
        $col->setIsPrimary(true);
        $this->assertEquals('my_col varchar(1) not null',$col.'');
        $col->setIsUnique(true);
        $this->assertEquals('my_col varchar(1) not null',$col.'');
    }
    /**
     * @test
     */
    public function testCleanValue00() {
        $col = new MySQLColumn('col', 'varchar');
        $this->assertEquals('\'Hello World!\'',$col->cleanValue('Hello World!'));
        $this->assertEquals('\'I wouln\\\'t do That\'',$col->cleanValue('I wouln\'t do That'));
    }
    /**
     * @test
     */
    public function testCleanValue01() {
        $col = new MySQLColumn('col', 'text');
        $this->assertEquals('\'Hello World!\'',$col->cleanValue('Hello World!'));
        $this->assertEquals('\'I wouln\\\'t do That\'',$col->cleanValue('I wouln\'t do That'));
    }
    /**
     * @test
     */
    public function testCleanValue02() {
        $col = new MySQLColumn('col', 'mediumtext');
        $this->assertEquals('\'Hello World!\'',$col->cleanValue('Hello World!'));
        $this->assertEquals('\'I wouln\\\'t do That\'',$col->cleanValue('I wouln\'t do That'));
    }
    /**
     * @test
     */
    public function testCleanValue03() {
        $col = new MySQLColumn('col', 'int');
        $this->assertEquals(0,$col->cleanValue('Hello World!'));
        $this->assertEquals(0,$col->cleanValue('I wouln\';select * from x'));
        $this->assertEquals(43,$col->cleanValue('43'));
        $this->assertEquals(-99,$col->cleanValue('-99.65'));
        $this->assertEquals(0,$col->cleanValue('hello-99.65'));
        $this->assertEquals(5,$col->cleanValue(5));
    }
    /**
     * @test
     */
    public function testCleanValue04() {
        $col = new MySQLColumn('col', 'decimal');
        $this->assertEquals('\'0\'',$col->cleanValue('Hello World!'));
        $this->assertEquals('\'0\'',$col->cleanValue('I wouln\';select * from x'));
        $this->assertEquals('\'43\'',$col->cleanValue('43'));
        $this->assertEquals('\'-99.65\'',$col->cleanValue('-99.65'));
        $this->assertEquals('\'0\'',$col->cleanValue('hello-99.65'));
        $this->assertEquals('\'5\'',$col->cleanValue(5));
        $this->assertEquals('\'6532.887\'',$col->cleanValue(6532.887));
    }
    /**
     * @test
     */
    public function testCleanValue06() {
        $col = new MySQLColumn('col', 'decimal');
        $this->assertEquals('\'0\'',$col->cleanValue('Hello World!'));
        $this->assertEquals('\'0\'',$col->cleanValue('I wouln\';select * from x'));
        $this->assertEquals('\'43\'',$col->cleanValue('43'));
        $this->assertEquals('\'-99.65\'',$col->cleanValue('-99.65'));
        $this->assertEquals('\'0\'',$col->cleanValue('hello-99.65'));
        $this->assertEquals('\'5\'',$col->cleanValue(5));
        $this->assertEquals('\'6532.887\'',$col->cleanValue(6532.887));
    }
    /**
     * @test
     */
    public function testCleanValue07() {
        $col = new MySQLColumn('col', 'timestamp');
        $this->assertEquals('',$col->cleanValue('Hello World!'));
        $this->assertEquals('',$col->cleanValue('I wouln\';select * from x'));
        $this->assertEquals('',$col->cleanValue(5));
        $this->assertEquals('\'2019-11-01 00:00:00\'',$col->cleanValue('2019-11-01'));
        $this->assertEquals('\'2019-11-01 23:09:44\'',$col->cleanValue('2019-11-01 23:09:44'));
    }
    /**
     * @test
     */
    public function testConstructor00() {
        $col = new MySQLColumn();
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(1,$col->getSize());
        $this->assertEquals('col',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor01() {
        $col = new MySQLColumn('user_id ', 'varchar', 15);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(15,$col->getSize());
        $this->assertEquals('user_id',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor02() {
        $col = new MySQLColumn('invalid name', 'varchar', 15);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(15,$col->getSize());
        $this->assertEquals('col',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor03() {
        $col = new MySQLColumn('0invalid_name', 'varchar', 15);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(15,$col->getSize());
        $this->assertEquals('col',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor04() {
        $col = new MySQLColumn('valid_name', 'invalid type', 15);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(15,$col->getSize());
        $this->assertEquals('valid_name',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor05() {
        $col = new MySQLColumn('valid_name', 'InT', 15);
        $this->assertEquals('int',$col->getType());
        $this->assertEquals(11,$col->getSize());
        $this->assertEquals('valid_name',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor06() {
        $col = new MySQLColumn('valid_name', 'Varchar ', 15);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(15,$col->getSize());
        $this->assertEquals('valid_name',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor07() {
        $col = new MySQLColumn('valid_name', 'Varchar ', 21846);
        $this->assertEquals('mediumtext',$col->getType());
        $this->assertEquals(21846,$col->getSize());
        $this->assertEquals('valid_name',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor08() {
        $col = new MySQLColumn('valid_name', 'Varchar ', 0);
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(1,$col->getSize());
        $this->assertEquals('valid_name',$col->getName());
    }
    /**
     * @test
     */
    public function testConstructor09() {
        $col = new MySQLColumn('amount', 'decimal ');
        $this->assertEquals('decimal',$col->getType());
        $this->assertEquals(1,$col->getSize());

        return $col;
    }
    /**
     * @test
     */
    public function testConstructor10() {
        $col = new MySQLColumn('amount', 'decimal ',0);
        $this->assertEquals('decimal',$col->getType());
        $this->assertEquals(0,$col->getSize());
        $this->assertEquals(0,$col->getScale());

        return $col;
    }
    /**
     * @test
     */
    public function testConstructor11() {
        $col = new MySQLColumn('amount', 'decimal ',1);
        $this->assertEquals('decimal',$col->getType());
        $this->assertEquals(1,$col->getSize());
        $this->assertEquals(0,$col->getScale());

        return $col;
    }
    /**
     * @test
     */
    public function testConstructor12() {
        $col = new MySQLColumn('amount', 'decimal ',2);
        $this->assertEquals('decimal',$col->getType());
        $this->assertEquals(2,$col->getSize());
        $this->assertEquals(1,$col->getScale());

        return $col;
    }
    /**
     * @test
     */
    public function testConstructor13() {
        $col = new MySQLColumn('amount', 'decimal ',3);
        $this->assertEquals('decimal',$col->getType());
        $this->assertEquals(3,$col->getSize());
        $this->assertEquals(2,$col->getScale());

        return $col;
    }
    /**
     * @test
     */
    public function testConstructor14() {
        $col = new MySQLColumn('amount', 'decimal ',4);
        $this->assertEquals('decimal',$col->getType());
        $this->assertEquals(4,$col->getSize());
        $this->assertEquals(2,$col->getScale());

        return $col;
    }
    /**
     * @test
     */
    public function testConstructor15() {
        $col = new MySQLColumn('amount', 'decimal ',-9);
        $this->assertEquals('decimal',$col->getType());
        $this->assertEquals(10,$col->getSize());
        $this->assertEquals(2,$col->getScale());

        return $col;
    }
    /**
     * @test
     */
    public function testSetColName00() {
        $col = new MySQLColumn();
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
    public function testSetDefault00() {
        $col = new MySQLColumn('date', 'timestamp');
        $col->setDefault('2019-11-09');
        $this->assertEquals('2019-11-09 00:00:00',$col->getDefault());
        $this->assertEquals('date timestamp not null default \'2019-11-09 00:00:00\'',$col.'');
    }
    /**
     * @test
     */
    public function testSetDefault01() {
        $col = new MySQLColumn('date', 'timestamp');
        $col->setDefault('2019-07-07 09:09:09');
        $this->assertEquals('2019-07-07 09:09:09',$col->getDefault());
        $this->assertEquals('date timestamp not null default \'2019-07-07 09:09:09\'',$col.'');
    }
    /**
     * @test
     */
    public function testSetDefault02() {
        $col = new MySQLColumn('date', 'datetime');
        $col->setDefault();
        $this->assertNull($col->getDefault());
        $this->assertEquals('date datetime not null',$col.'');
    }
    /**
     * @test
     */
    public function testSetDefault03() {
        $col = new MySQLColumn('date', 'datetime');
        $col->setDefault('2019-07-07 09:09:09');
        $this->assertEquals('2019-07-07 09:09:09',$col->getDefault());
        $this->assertEquals('date datetime not null default \'2019-07-07 09:09:09\'',$col.'');
    }
    /**
     * @test
     */
    public function testSetDefault04() {
        $col = new MySQLColumn('date', 'datetime');
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
        $col = new MySQLColumn('id', 'int');
        $this->assertEquals('id int(1) not null',$col.'');
        $col->setDefault(-122);
        $this->assertEquals(-122,$col->getDefault());
        $this->assertEquals('id int(1) not null default -122',$col.'');
        $col->setDefault(null);
        $this->assertNull($col->getDefault());
        $col->setDefault('a string');
        $this->assertEquals(0,$col->getDefault());
        $col->setDefault(1.8);
        $this->assertEquals(1,$col->getDefault());
    }
    /**
     * @test
     */
    public function testSetDefault06() {
        $col = new MySQLColumn('id', 'varchar');
        $this->assertEquals('id varchar(1) not null collate utf8mb4_unicode_ci',$col.'');
        $col->setDefault('A random string.');
        $this->assertEquals('A random string.',$col->getDefault());
        $this->assertEquals('id varchar(1) not null default \'A random string.\' collate utf8mb4_unicode_ci',$col.'');
        $col->setDefault(null);
        $this->assertNull($col->getDefault());
        $col->setDefault(33);
        $this->assertEquals(33,$col->getDefault());
        $col->setDefault(1.8);
        $this->assertEquals(1.8,$col->getDefault());
    }
    /**
     * @test
     */
    public function testSetDefault07() {
        $col = new MySQLColumn('id', 'decimal');
        $this->assertEquals('id decimal(1,0) not null',$col.'');
        $col->setDefault(1);
        $this->assertEquals(1,$col->getDefault());
        $this->assertEquals('id decimal(1,0) not null default \'1\'',$col.'');
        $col->setDefault(1.66);
        $this->assertEquals(1.66,$col->getDefault());
        $this->assertEquals('id decimal(1,0) not null default \'1.66\'',$col.'');
        $col->setDefault(null);
        $this->assertNull($col->getDefault());
        $col->setDefault('33');
        $this->assertEquals(33,$col->getDefault());
        $col->setDefault('');
        $this->assertEquals(0,$col->getDefault());
    }
    /**
     * @test
     */
    public function testSetMySQLVersion00() {
        $col = new MySQLColumn();
        $col->setMySQLVersion('5.4');
        $this->assertEquals('5.4',$col->getMySQLVersion());
        $this->assertEquals('utf8mb4_unicode_ci',$col->getCollation());
    }
    /**
     * @test
     */
    public function testSetMySQLVersion01() {
        $col = new MySQLColumn();
        $col->setMySQLVersion('8.0');
        $this->assertEquals('8.0',$col->getMySQLVersion());
        $this->assertEquals('utf8mb4_unicode_520_ci',$col->getCollation());
    }
    /**
     * @test
     */
    public function testSetMySQLVersion02() {
        $column = new MySQLColumn();
        $column->setMySQLVersion('8');
        $this->assertEquals('5.5',$column->getMySQLVersion());
        $this->assertEquals('utf8mb4_unicode_ci',$column->getCollation());
    }
    /**
     * @test
     */
    public function testSetMySQLVersion03() {
        $col = new MySQLColumn();
        $col->setMySQLVersion('8.0.77');
        $this->assertEquals('8.0.77',$col->getMySQLVersion());
        $this->assertEquals('utf8mb4_unicode_520_ci',$col->getCollation());
    }
    /**
     * @test
     */
    public function testSetName00() {
        $col = new MySQLColumn();
        $this->assertFalse($col->setName('invalid,name'));
        $this->assertFalse($col->setName('invalid-name'));
        $this->assertFalse($col->setName('invalid name'));
        $this->assertFalse($col->setName(''));
        $this->assertFalse($col->setName('       '));
    }
    /**
     * @test
     */
    public function testSetName01() {
        $col = new MySQLColumn();
        $this->assertTrue($col->setName('valid_name'));
        $this->assertEquals('valid_name',$col->getName());
        $this->assertTrue($col->setName('  valid_name_2  '));
        $this->assertEquals('valid_name_2',$col->getName());
        $this->assertTrue($col->setName('VALID_name'));
        $this->assertEquals('VALID_name',$col->getName());
    }
    /**
     * 
     * @param MySQLColumn $col
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
    public function testSetType00() {
        $col = new MySQLColumn();
        $this->assertTrue($col->setType('int', 0));
        $this->assertEquals('int',$col->getType());
        $this->assertEquals(1,$col->getSize());
        $this->assertNull($col->getDefault());
        $this->assertTrue($col->setType('  int', 11,6000));
        $this->assertEquals('int',$col->getType());
        $this->assertEquals(11,$col->getSize());
        $this->assertEquals(6000,$col->getDefault());
        $this->assertTrue($col->setType('int  ', 12,698));
        $this->assertEquals('int',$col->getType());
        $this->assertEquals(11,$col->getSize());
    }
    /**
     * @test
     */
    public function testSetType01() {
        $col = new MySQLColumn();
        $this->assertTrue($col->setType('varchar', 0));
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(1,$col->getSize());
        $this->assertNull($col->getDefault());
        $this->assertTrue($col->setType('  varchar', 5000,6000));
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(5000,$col->getSize());
        $this->assertSame('6000',$col->getDefault());
        $this->assertTrue($col->setType('varchar  ', 500000,'Hello World'));
        $this->assertEquals('mediumtext',$col->getType());
        $this->assertEquals(500000,$col->getSize());
        $this->assertSame('Hello World',$col->getDefault());
    }
    /**
     * @test
     */
    public function testSetType02() {
        $col = new MySQLColumn();
        $this->assertTrue($col->setType('varchar', 5000,'Hello'));
        $this->assertEquals('varchar',$col->getType());
        $this->assertEquals(5000,$col->getSize());
        $this->assertSame('Hello',$col->getDefault());
        $col->setType('int');
        $this->assertEquals('int',$col->getType());
        $this->assertEquals(1,$col->getSize());
        $this->assertNull($col->getDefault());
    }
    /**
     * @test
     */
    public function testSetType03() {
        $col = new MySQLColumn();
        $this->assertTrue($col->setType('datetime', 0,'2019-01-11'));
        $this->assertEquals('datetime',$col->getType());
        $this->assertEquals(1,$col->getSize());
        $this->assertSame('2019-01-11 00:00:00',$col->getDefault());
    }
    /**
     * @test
     */
    public function testSetType04() {
        $col = new MySQLColumn();
        $this->assertTrue($col->setType('datetime', 0,'2019-01-11 28:00:00'));
        $this->assertEquals('datetime',$col->getType());
        $this->assertEquals(1,$col->getSize());
        $this->assertNull($col->getDefault());
        $this->assertTrue($col->setType('timestamp', 0,'2019-13-11 00:00:00'));
        $this->assertNull($col->getDefault());
        $this->assertTrue($col->setType('timestamp', 0,'2019-04-44 00:00:00'));
        $this->assertNull($col->getDefault());
        $this->assertTrue($col->setType('timestamp', 0,'2019-12-11 00:60:00'));
        $this->assertNull($col->getDefault());
        $this->assertTrue($col->setType('timestamp', 0,'2019-12-11 00:00:60'));
        $this->assertNull($col->getDefault());
        $this->assertTrue($col->setType('timestamp', 0,'2019-12-30 23:59:59'));
        $this->assertEquals('2019-12-30 23:59:59',$col->getDefault());
    }
    /**
     * @test
     */
    public function testSetType05() {
        $col = new MySQLColumn();
        $this->assertTrue($col->setType('datetime', 0,'now()'));
        $this->assertTrue(in_array($col->getDefault(), 
                [date('Y-m-d H:i:s + 1'),date('Y-m-d H:i:s'),date('Y-m-d H:i:s - 1')]));
        $this->assertTrue($col->setType('datetime', 0,'current_timestamp'));
        $this->assertTrue(in_array($col->getDefault(), 
                [date('Y-m-d H:i:s + 1'),date('Y-m-d H:i:s'),date('Y-m-d H:i:s - 1')]));
    }
    /**
     * @test
     */
    public function testSetType06() {
        $col = new MySQLColumn();
        $this->assertTrue($col->setType('timestamp', 0,'now()'));
        $this->assertTrue(in_array($col->getDefault(), 
                [date('Y-m-d H:i:s + 1'),date('Y-m-d H:i:s'),date('Y-m-d H:i:s - 1')]));
        $this->assertTrue($col->setType('timestamp', 0,'current_timestamp'));
        $this->assertTrue(in_array($col->getDefault(), 
                [date('Y-m-d H:i:s + 1'),date('Y-m-d H:i:s'),date('Y-m-d H:i:s - 1')]));
    }
}
