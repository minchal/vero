<?php

use Vero\Routing\Router;
use Vero\Routing\URL;
use Vero\Routing\Builder\XML;
use Vero\Cache\Cache;
use Vero\Cache\Backend\Void;

class RouterTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		$builder = new XML(RESOURCES.'routes/', new Cache(new Void()));
		$this->router = $builder -> fill(new Router('/dir/','index.php/','example.com'));
	}
	
	public function testMatch() {
        /*$args = $args2 = $args3 = $args4 = array();
		$this->assertEquals($this->router->match('', $args), 'App\Action\Index');
		
		$this->assertEquals($this->router->match('sitemap.xml', $args), 'App\Action\Sitemap');
		$this->assertEquals($args['ext'], 'xml');
		
		$this->assertEquals($this->router->match('archives/2010-03', $args2), 'App\Action\News\Archives');
		$this->assertEquals($args2['year'], 2010);
		$this->assertFalse($this->router->match('archives/20101-03', $args3));
		
		$this->assertEquals($this->router->match('admin/aktualnosci/1/2/3', $args4), 'App\Action\News\Admin\Listing');
		$this->assertEquals($this->router->match('admin/aktualnosci/1/2/', $args4), 'App\Action\News\Admin\Listing');
		$this->assertEquals($this->router->match('admin/aktualnosci/1/2', $args4), 'App\Action\News\Admin\Listing');
		$this->assertEquals($this->router->match('admin/aktualnosci/', $args4), 'App\Action\News\Admin\Listing');
		$this->assertEquals($this->router->match('admin/aktualnosci', $args4), 'App\Action\News\Admin\Listing');*/
	}
	
	public function testUrls() {
		$this->assertEquals((string) $this->router->url(), '/dir/');
		$this->assertEquals((string) $this->router->url('index'), '/dir/');
		$this->assertEquals((string) $this->router->url('news/archives/year', 2011), '/dir/index.php/archives/2011');
		$this->assertEquals((string) $this->router->url('news/archives/month', 2011, 10), '/dir/index.php/archives/2011-10');
		$this->assertEquals((string) $this->router->url('news/archives/month', 2011, 10, 5), '/dir/index.php/archives/2011-10/5');
		$this->assertEquals((string) $this->router->url('news/archives/month', array(2011, 10, 5)), '/dir/index.php/archives/2011-10/5');
		$this->assertEquals((string) $this->router->url('news/archives/month', array('year'=>2011, 'page'=>5, 'month'=>10)), '/dir/index.php/archives/2011-10/5');
        
		$this->assertEquals((string) $this->router->url('news/archives/month', null, 10, 5), '/dir/index.php/archives/-10/5');
	}
	
	public function testFullUrls() {
		$this->assertEquals((string) $this->router->url()->full(), 'http://example.com/dir/');
		$this->assertEquals((string) $this->router->url('index')->full(), 'http://example.com/dir/');
		$this->assertEquals((string) $this->router->url('news/archives/month', array('year'=>2011, 'page'=>5, 'month'=>10))->full(), 'http://example.com/dir/index.php/archives/2011-10/5');
	}
}
