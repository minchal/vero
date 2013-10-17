<?php

use Vero\ACL\ACL;
use Vero\ACL\Role;
use Vero\ACL\Backend\XML as XMLBackend;
use Vero\Cache\Cache;
use Vero\Cache\Backend\Void;

class ACLTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		$this->acl = new ACL(new XMLBackend(RESOURCES.'acl/', new Cache(new Void())));
		
		$this->role_1 = new Role1();
		$this->role_2 = new Role2();
		$this->role_t = new RoleTest();
	}
	
	/**
	 * 1.xml has access to all
	 */
	public function testAdmin() {
		$this->assertTrue($this->acl->check('admin', $this->role_1));
		$this->assertTrue($this->acl->check('admin/news', $this->role_1));
	}
	
	/**
	 * 2.xml has access to all except few admin actions.
	 */
	public function testMod() {
		$this->assertTrue($this->acl->check('something', $this->role_2));
		
		$this->assertTrue($this->acl->check('admin', $this->role_2));
		$this->assertTrue($this->acl->check('admin/news', $this->role_2));
		$this->assertTrue($this->acl->check('admin/news/add', $this->role_2));
		
		$this->assertTrue($this->acl->check('admin/system/overview', $this->role_2));
		$this->assertFalse($this->acl->check('admin/system/actions', $this->role_2));
	}
	
	/**
	 * test.xml has acces to few global actions
	 */
	public function testUser() {
		$this->assertTrue($this->acl->check('pm', $this->role_t));
		$this->assertTrue($this->acl->check('pm/send', $this->role_t));
		$this->assertTrue($this->acl->check('user', $this->role_t));
		$this->assertTrue($this->acl->check('user/profile', $this->role_t));
		
		$this->assertFalse($this->acl->check('admin', $this->role_t));
		$this->assertFalse($this->acl->check('semething', $this->role_t));
		$this->assertFalse($this->acl->check('admin/system', $this->role_t));
	}
}

class Role1 implements Role {
	public function getRole() {
		return 1;
	}
}

class Role2 implements Role {
	public function getRole() {
		return 2;
	}
}

class RoleTest implements Role {
	public function getRole() {
		return 'test';
	}
}
