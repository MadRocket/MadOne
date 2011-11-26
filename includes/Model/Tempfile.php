<?
/**
 * Временный файл
 */

class Model_Tempfile extends Storm_Model {
    static function definition() {
        return array (
			'file' 	 => new Storm_Db_Field_File( array( 'path' => "/upload/temp/files" ) ),
			'date'   => new Storm_Db_Field_Datetime( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
        );
    }
}

?>
