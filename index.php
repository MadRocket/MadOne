<?php
error_reporting( E_ALL );
date_default_timezone_set('Asia/Krasnoyarsk');

require_once( "./includes/autoload.php" );
require_once("./includes/Storm/loader.php");

if( preg_match('~^/admin~', $_SERVER['REQUEST_URI']) ) {    
    Madone_Core::setErrorHandlers();
    $app = new Madone_Application_Cms();
    $app->run();
}
else {
	Madone_Core::run();
}