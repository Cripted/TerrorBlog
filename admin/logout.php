<?php
/**
 * admin/logout.php
 */
require_once '../config/auth.php';
$auth->logout();
redirect(SITE_URL . '/admin/login.php');