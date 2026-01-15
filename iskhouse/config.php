<?php
define('CURRENCY', 'Rp');
define('WEB_URL', 'http://localhost:8000/');
#define('WEB_URL', 'https://e4ea-112-78-153-102.ap.ngrok.io/ams/'); 
define('ROOT_PATH', '/Users/guti/Desktop/isk door lock/ISK-Doorlock-v2/iskhouse/');
error_reporting(0);

define('DB_HOSTNAME', '127.0.0.1:3307');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'iskpass');
define('DB_DATABASE', 'ams');
$link = new mysqli(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
