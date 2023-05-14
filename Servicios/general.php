<?php

date_default_timezone_set('America/Bogota');
header("Content-type: application/json; charset=utf-8");

define("DBHOST", "localhost:3308");
define("DBNAME", "clima");
define("DBUSER", "root");
define("DBPASS", "");

require_once('../WebService/WebServices.php');

