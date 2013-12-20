<?php

use Vero\Web\Request;

class RequestTest extends PHPUnit_Framework_TestCase {
	
	public function testQuery() {
		$request = new Request([
			'server' => ['REQUEST_URI' => '/vero/asdasd/asdasd?asd=asd']
		]);
		$this->assertEquals($request -> getQuery('/vero/'), 'asdasd/asdasd');
		
		$request = new Request([
			'server' => ['REQUEST_URI' => '/vero/index.php/asdasd/asdasd?asd=asd']
		]);
		$this->assertEquals($request -> getQuery('/vero/','index.php/'), 'asdasd/asdasd');
		
		$request = new Request([
			'server' => ['REQUEST_URI' => '/vero/index.php/']
		]);
		$this->assertEquals($request -> getQuery('/vero/','index.php/'), '');
		
		$request = new Request([
			'server' => ['REQUEST_URI' => '/vero/']
		]);
		$this->assertEquals($request -> getQuery('/vero/','index.php/'), '');
	}
	
}
