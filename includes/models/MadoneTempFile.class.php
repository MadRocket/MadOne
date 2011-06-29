<?
/**
 * Временный файл
 */

class MadoneTempFile extends StormModel {
    static function definition() {
        return array (
			'file' 	 => new StormFileDbField( array( 'path' => "/upload/temp/files" ) ),
			'date'   => new StormDatetimeDbField( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
        );
    }
}

?>
