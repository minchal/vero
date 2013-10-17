<?php

use Vero\Web\Request;

class RequestTest extends PHPUnit_Framework_TestCase {
	
	public function testQuery() {
		$request = new Request('/vero/');
		$_SERVER['REQUEST_URI'] = '/vero/asdasd/asdasd?asd=asd';
		$this->assertEquals($request -> getQuery(), 'asdasd/asdasd');
		
		$request = new Request('/vero/','index.php/');
		$_SERVER['REQUEST_URI'] = '/vero/index.php/asdasd/asdasd?asd=asd';
		$this->assertEquals($request -> getQuery(), 'asdasd/asdasd');
		
		$request = new Request('/vero/','index.php/');
		$_SERVER['REQUEST_URI'] = '/vero/index.php/';
		$this->assertEquals($request -> getQuery(), '');
		$_SERVER['REQUEST_URI'] = '/vero/';
		$this->assertEquals($request -> getQuery(), '');
	}
	
}
