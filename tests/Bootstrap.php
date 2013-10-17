<?php

if (!defined('RESOURCES')) {
	define('RESOURCES', __DIR__.'/data/');
}

require_once __DIR__ . '/../src/autoload.php';

error_reporting(-1);

function x() {
	$arr = func_num_args() ? func_get_args() : array('DEBUG MARKER');
	call_user_func_array('var_dump', $arr);
}
