<?php
//if we use session, use this code
session_set_cookie_params(0);
session_start();
date_default_timezone_set('UTC');

require_once './application/config/test.php';
require_once './org/weemvc/util/AutoloadHelper.php';
use org\weemvc\core\Application;

// start the application
$app = Application::getInstance();
// 
$controller = $app->getController();
// to do some timed tasks
$controller->sendWee('IntervalTask', 'timestrap.cache');
