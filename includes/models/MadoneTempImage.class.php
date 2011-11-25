<?
/**
 * Временная картинка
 */

class MadoneTempImage extends Storm_Model
{
    static function definition()
    {
        return array
        (
			'image' 	=> new Storm_Db_Field_File_Image( array(
													'path' 	=> "/upload/temp/images",
												   	'variants' => array(
														'cms'   => array( 'width' => 120, 'height' => 120, 'crop' => true ),
													),
												  )
			),
			
			'date'  => new Storm_Db_Field_Datetime( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
        );
    }
    
    function afterSave( $new )
    {
    }
}

?>
