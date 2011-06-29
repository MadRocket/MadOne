<?
/**
 * Временная картинка
 */

class MadoneTempImage extends StormModel
{
    static function definition()
    {
        return array
        (
			'image' 	=> new StormImageDbField( array( 
													'path' 	=> "/upload/temp/images",
												   	'variants' => array(
														'cms'   => array( 'width' => 120, 'height' => 120, 'crop' => true ),
													),
												  )
			),
			
			'date'  => new StormDatetimeDbField( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
        );
    }
    
    function afterSave( $new )
    {
    }
}

?>
