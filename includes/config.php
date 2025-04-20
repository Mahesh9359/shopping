<?php
// Database connection only
define('DB_SERVER', 'shuttle.proxy.rlwy.net');
define('DB_USER', 'root');
define('DB_PASS', 'iBBQXBVUlxpctlJzhpnVFWjOYeCAzvPa');
define('DB_NAME', 'railway');
define('DB_PORT', 57055);


$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Turn off unnecessary notices/deprecated warnings in production
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
