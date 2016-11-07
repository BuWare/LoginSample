<?php

/**
 * unlock.php
 */

namespace MyApp;

use MyApp\controller\LoginController;
use MyApp\common\Template;

define('LAYOUT', 'index');

try {
	require_once '../common.php';
	Template::assign('is_lock', LoginController::isAccountLock());
	Template::assign('success', LoginController::unlock());
} catch (\Exception $e) {
	Template::exception($e);
} finally {
	Template::display();
}