<?php

ini_set('memory_limit', '4G');
set_time_limit(0);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("App.php");

$app = new App();

$app->run();
