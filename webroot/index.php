<?php

/**
 * index.php
 */

namespace MyApp;

use MyApp\controller\LoginController;
use MyApp\common\Template;

define('LAYOUT', 'index');

try {
	require_once '../common.php';

	LoginController::login();
} catch (\Exception $e) {
	Template::exception($e);
} finally {
	Template::display();
}