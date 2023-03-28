<?php
define('CURRENCY', 'Rp');
define('WEB_URL', 'localhost/www/');
#define('WEB_URL', 'https://e4ea-112-78-153-102.ap.ngrok.io/ams/'); 
define('ROOT_PATH', '/www');
error_reporting(0);

define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'ams');
$link = new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

