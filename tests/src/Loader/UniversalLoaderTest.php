<?php

use Vero\Loader\UniversalLoader as Loader;

/**
 * @see http://groups.google.com/group/php-standards/web/psr-0-final-proposal?pli=1
 */
class UniversalLoaderTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		$this -> loader = new Loader();
		
		$dir = RESOURCES.'Loader/';
		
		$this -> loader 
			-> add('Lib1', $dir)
			-> add('Lib_lib', $dir)
			
			-> add('Class1', $dir)
			
			-> add('Lib3', $dir)
			
			-> addDirect('Application', $dir.'app/')
			-> register();
	}
	
	public function testNamespace() {
		$this->assertTrue(class_exists('Lib1\PackagePackage\Class1'));
		$this->assertTrue(class_exists('Lib1\PackagePackage\Class_Class2'));
		$this->assertTrue(class_exists('Lib1\package_package\Class1'));
		$this->assertTrue(class_exists('Lib1\package_package\Class_Class2'));
		
		$this->assertTrue(class_exists('Lib_lib\PackagePackage\Class1'));
		$this->assertTrue(class_exists('Lib_lib\PackagePackage\Class_Class2'));
		$this->assertTrue(class_exists('Lib_lib\package_package\Class1'));
		$this->assertTrue(class_exists('Lib_lib\package_package\Class_Class2'));
	}
	
	public function testSimple() {
		$this->assertTrue(class_exists('Class1'));
	}
	
	public function testLagacy() {
		$this->assertTrue(class_exists('Lib3_Package_Class1'));
		$this->assertTrue(class_exists('Lib3_Package'));
	}
	
	public function testReplacedNS() {
		$this->assertTrue(class_exists('Application\Test'));
	}
}
