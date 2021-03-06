<?
/**
 *  Галерея
 */

class Model_Galleryimage extends Storm_Model
{
    static function definition()
    {
        return array
        (

			'page'  => new Storm_Db_Field_Fk( array( 'model' => 'Model_Page', 'related' => 'images' ) ),
            'title'    => new Storm_Db_Field_Char( array( 'maxlength' => 255 ) ),
			'image'    => new Storm_Db_Field_File_Image( array(	'path' => "/upload/images/gallery",
													'variants' => array(
														'large' => array( 'width' => 1000, 'height' => 1000 ),
														'cms'   => array( 'width' => 120, 'height' => 120, 'spacefill' => true ),
													),
												 )
			),
			
			'position' => new Storm_Db_Field_Integer(),
        );
    }
    
    function afterSave( $new )
    {
        if( $new && ! $this->position )
        {
            $last = $this->getQuerySet()->filter( array( 'id__ne' => $this->id, 'page' => $this->page ) )->orderDesc( 'position' )->first();
            $this->position = $last ? $last->position + 1 : 1;
            $this->hiddenSave();
        }
    }
}

?>
