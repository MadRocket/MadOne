<?php
error_reporting( E_ALL );
date_default_timezone_set('Asia/Krasnoyarsk');

require_once( "./includes/autoload.php" );
require_once( "./includes/storm/loader.php" );

if( preg_match('~^/admin~', $_SERVER['REQUEST_URI']) ) {    
    Madone::setErrorHandlers();
    $app = new MadoneCmsApplication();
    $app->run();
}
else {
	Madone::run();
}