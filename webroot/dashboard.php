<?php

/**
 * dashboard.php
 */

namespace MyApp;

use MyApp\common\Template;
use MyApp\controller\LoginController;

define('LAYOUT', 'main');

try {
	require_once '../common.php';

	LoginController::checkLogin();
} catch (\Exception $e) {
	Template::exception($e);
} finally {
	Template::display();
}