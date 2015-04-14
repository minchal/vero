<?php

use Vero\Validate\Validator as V;

class ValidatorTest extends PHPUnit_Framework_TestCase {

    public function testNotScalar() {
        $data = array(
            'arr' => array(1, 2),
            'obj' => new \stdClass()
        );

        $this->assertFalse(V::create($data)->map('arr', 'string')->isValid());
        $this->assertFalse(V::create($data)->map('arr', 'integer')->isValid());
        $this->assertFalse(V::create($data)->map('arr', 'number')->isValid());
        $this->assertFalse(V::create($data)->map('arr', 'idstr')->isValid());
        $this->assertFalse(V::create($data)->map('arr', 'boolean')->isValid());
        $this->assertFalse(V::create($data)->map('arr', 'email')->isValid());
        $this->assertFalse(V::create($data)->map('arr', 'url')->isValid());
        $this->assertFalse(V::create($data)->map('arr', 'date')->isValid());
        $this->assertFalse(V::create($data)->map('arr', 'datetime')->isValid());
        $this->assertFalse(V::create($data)->map('arr', 'time')->isValid());

        $this->assertFalse(V::create($data)->map('obj', 'string')->isValid());
        $this->assertFalse(V::create($data)->map('obj', 'integer')->isValid());
        $this->assertFalse(V::create($data)->map('obj', 'number')->isValid());
        $this->assertFalse(V::create($data)->map('obj', 'idstr')->isValid());
        $this->assertFalse(V::create($data)->map('obj', 'boolean')->isValid());
        $this->assertFalse(V::create($data)->map('obj', 'email')->isValid());
        $this->assertFalse(V::create($data)->map('obj', 'url')->isValid());
        $this->assertFalse(V::create($data)->map('obj', 'date')->isValid());
        $this->assertFalse(V::create($data)->map('obj', 'datetime')->isValid());
        $this->assertFalse(V::create($data)->map('obj', 'time')->isValid());
    }

    public function testString() {
        $data = array(
            'content' => 'foo bar',
            'empty' => '',
            '10chars' => 'qwertyuiop'
        );

        $this->assertTrue(V::create($data)->map('content', 'string')->isValid());

        $this->assertFalse(V::create($data)->map('empty', 'string')->isValid());
        $this->assertFalse(V::create($data)->map('notexisting', 'string')->isValid());

        $this->assertTrue(V::create($data)->map('empty', 'string', array('optional' => true))->isValid());
        $this->assertTrue(V::create($data)->map('notexisting', 'string', array('optional' => true))->isValid());

        $this->assertTrue(V::create($data)->map('10chars', 'string', array('min' => 5, 'max' => 15))->isValid());
        $this->assertTrue(V::create($data)->map('10chars', 'string', array('min' => 5, 'max' => 10))->isValid());
        $this->assertTrue(V::create($data)->map('10chars', 'string', array('min' => 10, 'max' => 15))->isValid());

        $this->assertFalse(V::create($data)->map('10chars', 'string', array('min' => 11, 'max' => 15))->isValid());
        $this->assertFalse(V::create($data)->map('10chars', 'string', array('min' => 8, 'max' => 9))->isValid());
    }

    public function testEmail() {
        $data = array(
            'correct' => 'test@gmail.com',
            'plus' => 'test+foo@gmail.com',
            'ip' => 'test+foo@83.27.158.239',
            'toolong' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop@qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop.qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop',
            'empty' => ''
        );

        $this->assertTrue(V::create($data)->map('correct', 'email')->isValid());
        $this->assertTrue(V::create($data)->map('plus', 'email')->isValid());
        //$this->assertTrue(V::create($data)->map('ip', 'email')->isValid());

        $this->assertFalse(V::create($data)->map('toolong', 'email')->isValid());
        $this->assertFalse(V::create($data)->map('empty', 'email')->isValid());
        $this->assertTrue(V::create($data)->map('empty', 'email', array('optional' => true))->isValid());
    }

    public function testNumber() {
        $data = array(
            'a' => '123',
            'b' => '123',
            'c' => '123.22',
            'd' => '123.22',
            'e' => '123,22',
            'f' => 'a123',
            'g' => '-123',
            'h' => '-123.34',
            'i' => '-123.34567',
            'j' => '-123.345678',
            'opt' => '0',
            'empty' => ''
        );

        $v = V::create($data)->map('a', 'number');
        $this->assertTrue($v->isValid());
        $this->assertEquals(123, $v->a);
        $this->assertInternalType('float', $v->a);

        $this->assertTrue(V::create($data)->map('b', 'number')->isValid());
        $this->assertTrue(V::create($data)->map('c', 'number')->isValid());
        $this->assertTrue(V::create($data)->map('d', 'number')->isValid());
        $this->assertTrue(V::create($data)->map('e', 'number')->isValid());

        $this->assertFalse(V::create($data)->map('f', 'number')->isValid());
        $this->assertTrue(V::create($data)->map('g', 'number', array('min' => null))->isValid());
        $this->assertTrue(V::create($data)->map('h', 'number', array('min' => -124))->isValid());
        $this->assertTrue(V::create($data)->map('i', 'number', array('precision' => 5, 'min' => null))->isValid());
        $this->assertFalse(V::create($data)->map('i', 'number', array('precision' => 4, 'min' => null))->isValid());
        $this->assertFalse(V::create($data)->map('j', 'number')->isValid());

        $this->assertFalse(V::create($data)->map('opt', 'number')->isValid());
        $this->assertFalse(V::create($data)->map('empty', 'number')->isValid());
        $this->assertTrue(V::create($data)->map('opt', 'number', array('optional' => true))->isValid());
        $this->assertTrue(V::create($data)->map('empty', 'number', array('optional' => true))->isValid());

        $this->assertFalse(V::create($data)->map('f', 'number', array('optional' => true))->isValid());
        $this->assertFalse(V::create($data)->map('f', 'number', array('optional' => true))->isValid());
    }

    public function testInteger() {
        $data = array(
            'a' => '123',
            'b' => '123',
            'c' => '123.22',
            'd' => '123.22',
            'e' => '123,22',
            'f' => 'a123'
        );

        $v = V::create($data)->map('a', 'integer');
        $this->assertTrue($v->isValid());
        $this->assertEquals(123, $v->a);
        $this->assertInternalType('integer', $v->a);

        $this->assertTrue(V::create($data)->map('b', 'integer')->isValid());
        $this->assertFalse(V::create($data)->map('c', 'integer')->isValid());
        $this->assertFalse(V::create($data)->map('d', 'integer')->isValid());
        $this->assertFalse(V::create($data)->map('e', 'integer')->isValid());
        $this->assertFalse(V::create($data)->map('f', 'integer')->isValid());
    }

    public function testRange() {
        $data = array(
            'a' => '123',
            'b' => '123.22',
            'c' => '-123.34',
        );

        $this->assertTrue(V::create($data)->map('a', 'integer', array('min' => 123))->isValid());
        $this->assertFalse(V::create($data)->map('a', 'integer', array('min' => 123.5))->isValid());

        $this->assertTrue(V::create($data)->map('b', 'number', array('min' => 123))->isValid());
        $this->assertFalse(V::create($data)->map('b', 'number', array('min' => 123.5))->isValid());

        $this->assertTrue(V::create($data)->map('b', 'number', array('max' => 123.22))->isValid());
        $this->assertFalse(V::create($data)->map('b', 'number', array('max' => 122))->isValid());

        $this->assertTrue(V::create($data)->map('c', 'number', array('min' => -124, 'max' => 1))->isValid());
        $this->assertFalse(V::create($data)->map('c', 'number', array('min' => -200, 'max' => -123.4))->isValid());
    }

    public function testBoolean() {
        $data = array(
            'on' => 'on',
            'true' => true,
            'trues' => 'true',
            'one' => 1,
            'ones' => '1',
            'off' => 'off',
            'false' => false,
            'falses' => 'false',
            'zero' => 0,
            'zeros' => '0',
            'unexpected' => 'asdasd'
        );

        $this->assertTrue(V::create($data)->map('on', 'boolean')->isValid());
        $this->assertTrue(V::create($data)->map('true', 'boolean')->isValid());
        $this->assertTrue(V::create($data)->map('trues', 'boolean')->isValid());
        $this->assertTrue(V::create($data)->map('one', 'boolean')->isValid());
        $this->assertTrue(V::create($data)->map('ones', 'boolean')->isValid());

        $this->assertFalse(V::create($data)->map('off', 'boolean')->isValid());
        $this->assertFalse(V::create($data)->map('false', 'boolean')->isValid());
        $this->assertFalse(V::create($data)->map('falses', 'boolean')->isValid());
        $this->assertFalse(V::create($data)->map('zero', 'boolean')->isValid());
        $this->assertFalse(V::create($data)->map('zeros', 'boolean')->isValid());

        $this->assertTrue(V::create($data)->map('off', 'boolean', array('optional' => true))->isValid());
        $this->assertTrue(V::create($data)->map('false', 'boolean', array('optional' => true))->isValid());
        $this->assertTrue(V::create($data)->map('falses', 'boolean', array('optional' => true))->isValid());
        $this->assertTrue(V::create($data)->map('zero', 'boolean', array('optional' => true))->isValid());
        $this->assertTrue(V::create($data)->map('zeros', 'boolean', array('optional' => true))->isValid());

        $this->assertFalse(V::create($data)->map('unexpected', 'boolean')->isValid());
        $this->assertTrue(V::create($data)->map('unexpected', 'boolean', array('optional' => true))->isValid());
    }

    public function testIdString() {
        $data = array(
            'correct1' => 'ns_sdAWDW-sdcni21332nnj',
            'incorrect1' => 'ąckiaskdop4',
            'incorrect2' => '1noin22o',
            'incorrect3' => '_assdad2ed',
            'incorrect4' => '-asasdi97aysd',
            'toolong' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop@qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop.qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop',
            'empty' => ''
        );

        $this->assertTrue(V::create($data)->map('correct1', 'idstr')->isValid());

        $this->assertFalse(V::create($data)->map('incorrect1', 'idstr')->isValid());
        $this->assertFalse(V::create($data)->map('incorrect2', 'idstr')->isValid());
        $this->assertFalse(V::create($data)->map('incorrect3', 'idstr')->isValid());
        $this->assertFalse(V::create($data)->map('incorrect4', 'idstr')->isValid());
        $this->assertFalse(V::create($data)->map('toolong', 'idstr')->isValid());
        $this->assertFalse(V::create($data)->map('empty', 'idstr')->isValid());
        $this->assertTrue(V::create($data)->map('empty', 'idstr', array('optional' => true))->isValid());
    }

    public function testPassword() {
        $data = array(
            'correct1' => 'ns_sdAWDW-sdcni%21332nnj',
            'incorrect1' => 'ąckiaskdop4',
            'incorrect2' => 'aswd',
            'toolong' => 'qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop@qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop.qwertyuiopqwertyuiopqwertyuiopqwertyuiopqwertyuiop',
        );

        $this->assertTrue(V::create($data)->map('correct1', 'password')->isValid());

        $this->assertFalse(V::create($data)->map('incorrect1', 'password')->isValid());
        $this->assertFalse(V::create($data)->map('incorrect2', 'password')->isValid());
        $this->assertFalse(V::create($data)->map('toolong', 'password')->isValid());
    }

    public function testDateTime() {
        $data = array(
            'correct1' => '1999-12-15',
            'correct2' => '1999-12-15 11:12:54',
            'correct3' => '14:15:26',
            'correct4' => '1999-02-31',
            'correct5' => '1:15:18',
            'correct6' => '99-11-05',
            'correct7' => '1999-11-5',
            'incorrect1' => '25:15:25',
            'incorrect2' => 'asdf',
            'incorrect3' => '---',
        );

        $this->assertTrue(V::create($data)->map('correct1', 'date')->isValid());
        $this->assertTrue(V::create($data)->map('correct2', 'datetime')->isValid());
        $this->assertTrue(V::create($data)->map('correct3', 'time')->isValid());
        $this->assertTrue(V::create($data)->map('correct4', 'date')->isValid());
        $this->assertTrue(V::create($data)->map('correct5', 'time')->isValid());
        $this->assertTrue(V::create($data)->map('correct6', 'date')->isValid());
        $this->assertTrue(V::create($data)->map('correct7', 'date')->isValid());
        $this->assertFalse(V::create($data)->map('incorrect1', 'time')->isValid());
        $this->assertFalse(V::create($data)->map('incorrect2', 'datetime')->isValid());
        $this->assertFalse(V::create($data)->map('incorrect3', 'date')->isValid());
    }

    public function testSet() {
        $data = array(
            'correct1' => 'a',
            'incorrect1' => 'c',
            'incorrect2' => 1,
        );

        $items1 = array('a' => 'A', 'b' => 'B');

        $this->assertTrue(V::create($data)->map('correct1', 'set', array('items' => $items1))->isValid());
        $this->assertFalse(V::create($data)->map('incorrect1', 'set', array('items' => $items1))->isValid());
        $this->assertFalse(V::create($data)->map('incorrect1a', 'set', array('items' => $items1))->isValid());
        $this->assertFalse(V::create($data)->map('incorrect2', 'set', array('items' => $items1))->isValid());
    }

    public function testArray() {
        $data = array(
            'correct1' => [1, 2, 3],
            'correct2' => ['2012-01-01', '2013-01-01'],
            'incorrect1' => 'a',
        );

        $this->assertTrue(V::create($data)->map('correct1', 'array')->isValid());
        $this->assertTrue(V::create($data)->map('correct1', 'array', array('rule' => 'integer', 'options' => array('max' => 3)))->isValid());
        $this->assertFalse(V::create($data)->map('correct1', 'array', array('rule' => 'integer', 'options' => array('max' => 2)))->isValid());

        $this->assertTrue(V::create($data)->map('correct2', 'array', array('rule' => 'date'))->isValid());
    }

    public function testArraySet() {
        $data = array(
            'correct1' => ['a', 'b'],
            'incorrect1' => 'a',
        );

        $items1 = array('a' => 'A', 'b' => 'B');

        $this->assertTrue(V::create($data)->map('correct1', 'array', array('rule' => 'set', 'options' => array('items' => $items1)))->isValid());
        //$this->assertFalse(V::create($data)->map('incorrect1', 'array', array('rule'=>'set','options'=>array('items'=>$items1)))->isValid());
    }
}
