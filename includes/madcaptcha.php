<?
error_reporting( E_ALL );

require_once( "{$_SERVER['DOCUMENT_ROOT']}/includes/autoload.php" );

header( 'Content-Type: image/png' );
header( 'Cache-control: no-cache, no-store' );

echo Madone_Helper_Captcha::create()->getImage();

?>