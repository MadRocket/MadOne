<?php
error_reporting( E_ALL );
date_default_timezone_set('Asia/Krasnoyarsk');

require_once( __DIR__."/autoload.php" );

Madone_Core::init();

if( preg_match('~^/admin~', Madone_Core::getRequest()->getPathInfo()) ) {
    $app = new Madone_Application_Cms();
    $app->run();
}
else {
	Madone_Core::run();
}
