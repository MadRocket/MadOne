<?php
error_reporting( E_ALL );
date_default_timezone_set('Asia/Krasnoyarsk');

require_once( __DIR__."/autoload.php" );

$madone = new Madone_Core();

if( preg_match('~^/admin~', $madone['request']->getPathInfo()) ) {
    $app = new Madone_Application_Cms($madone);
    $app->run();
}
else {
	$madone->run();
}
