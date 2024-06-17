<?php
// DB Params
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', '');


// Special file paths
define("APPROOT", dirname(dirname(__FILE__)));

// URL directories
define("URLROOT", 'http://localhost/foldername/public/');
define("ASSETS", 'http://localhost/foldername/public/assets/');
define('UPLOADS', URLROOT . '/uploads');
define('BGCOLOR', "#f9f9f9");


// OTP EXPIRATION TIME
define('OTP_EXPIRY_DURATION', 600);

// json status 
define('STATUS_SUCCESS', 'success');
define('STATUS_ERROR', 'error');

// Limit
define("LIMIT", 20);
