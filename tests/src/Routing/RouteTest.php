<?php

use Vero\Routing\PatternRoute;

class RouteTest extends PHPUnit_Framework_TestCase {
	
	public function testStatic() {
		$route = new PatternRoute('action', '');
		$this->assertTrue($route->match(''));
		$this->assertFalse($route->match('a'));
		
		$route = new PatternRoute('action', 'contact');
		$this->assertTrue($route->match('contact'));
	}
	
	public function testRequiredOnly() {
		$route = new PatternRoute('action', 'news/{id:int}');
		$this->assertTrue($route->match('news/18'));
		
		$route = new PatternRoute('action', 'news/{id:int}/{txt}');
		$this->assertTrue($route->match('news/18/asd_asd-asdsa.asd'));
		$this->assertFalse($route->match('news/18a/asd_asd-asdsa.asd'));
		$this->assertFalse($route->match('news//asd_asd-asdsa.asd'));
		$this->assertFalse($route->match('news/11/'));
		$this->assertFalse($route->match('news/11'));
		$this->assertFalse($route->match('news/15/asd_asd-asdsa.asd/'));
	}
	
	public function testOptionals() {
		$route = new PatternRoute('action', 'news/{id:int:}');
		$this->assertTrue($route->match('news/18'));
		$this->assertTrue($route->match('news/'));
		$this->assertTrue($route->match('news'));
		$this->assertFalse($route->match('news/123a'));
		
		$route = new PatternRoute('action', 'news/{id:int}/{txt:any:default}');
		$this->assertTrue($route->match('news/18/asd_asd-asdsa.asd'));
		$this->assertFalse($route->match('news/18a/asd_asd-asdsa.asd'));
		$this->assertFalse($route->match('news//asd_asd-asdsa.asd'));
		$this->assertTrue($route->match('news/11/'));
		$this->assertTrue($route->match('news/11'));
		$this->assertFalse($route->match('news/15/asd_asd-asdsa.asd/'));
		$this->assertFalse($route->match('news/15//asdasd'));
		
		$route = new PatternRoute('action', 'news/{category:int:0}/{page:int:1}/{order:int:}');
		$this->assertTrue($route->match('news/18/12/0'));
		$this->assertTrue($route->match('news///'));
		$this->assertTrue($route->match('news'));
		$this->assertFalse($route->match('newssss'));
		
		$route = new PatternRoute('action', 'news/{page:int:1}/{order:any:}');
		$this->assertTrue($route->match('news'));
		$this->assertFalse($route->match('newsssss'));
	}
	
	public function testAdvanced() {
		$route = new PatternRoute('action', 'page/{path:.+}');
		$this->assertTrue($route->match('page/page1/page2'));
		$this->assertFalse($route->match('page'));
		$this->assertFalse($route->match('page/'));
		
		$route = new PatternRoute(
			'action', 'news/{year}-{month}/{page:int:1}',
			array(
				'year' =>array('reqs'=>'\d{4}'),
				'month'=>array('reqs'=>'\d{2}')
			)
		);
		
		$this->assertTrue($route->match('news/1833-23/21'));
		$this->assertFalse($route->match('news/183323/21'));
		$this->assertFalse($route->match('news/18332-23'));
		
		$route = new PatternRoute('action', 'sitemap/{test}.xml');
		$this->assertTrue($route->match('sitemap/qwert.xml'));
		$this->assertFalse($route->match('sitemap/qwert.xml/'));
		$this->assertFalse($route->match('sitemap/.xml'));
		
		$route = new PatternRoute('action', 'sitemap',array('ext'=>array('default'=>'html')));
		$this->assertTrue($route->match('sitemap',$m));
		$this->assertEquals($m['ext'],'html');
		
		// other method to change requirement
		$route = new PatternRoute('action', 'news/{id:int}',array('id'=>array('required'=>false)));
		$this->assertTrue($route->match('news/18'));
		$this->assertTrue($route->match('news/'));
		$this->assertTrue($route->match('news'));
		$this->assertFalse($route->match('news/123a'));
	}
	
	public function testUrls() {
		$route = new PatternRoute('action', 'user/{login}');
		$this->assertEquals($route->url(array('login'=>'admin')), 'user/admin');
		
		$route = new PatternRoute('action', 'news/{year:int}-{month:int}/{page:int:1}');
		$this->assertEquals($route->url(array('year'=>'2011','month'=>'07')), 'news/2011-07');
		$this->assertEquals($route->url(array('year'=>'2011','month'=>'07','page'=>3)), 'news/2011-07/3');
	}
}
