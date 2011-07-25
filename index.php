<?
error_reporting( E_ALL );
date_default_timezone_set('Asia/Krasnoyarsk');

require_once( "{$_SERVER['DOCUMENT_ROOT']}/includes/autoload.php" );
require_once( "{$_SERVER['DOCUMENT_ROOT']}/includes/storm/loader.php" );

if( preg_match('~^/admin~', $_SERVER['REQUEST_URI']) ) {    
    Madone::setErrorHandlers();
    $app = new MadoneCmsApplication();
    $app->run();
}
else {
	Madone::run();
}
?>