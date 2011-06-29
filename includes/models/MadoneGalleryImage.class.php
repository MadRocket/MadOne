<?
/**
 *  Галерея
 */

class MadoneGalleryImage extends StormModel
{
    static function definition()
    {
        return array
        (

			'section'  => new StormFkDbField( array( 'model' => 'MadoneGallerySection', 'related' => 'images' ) ),
            'title'    => new StormCharDbField( array( 'maxlength' => 255 ) ),
			'image'    => new StormImageDbField( array(	'path' => "/upload/images/gallery",
													'variants' => array(
														'large' => array( 'width' => 1000, 'height' => 1000 ),
														'cms'   => array( 'width' => 120, 'height' => 120, 'spacefill' => true ),
													),
												 )
			),
			
			'position' => new StormIntegerDbField(),
        );
    }
    
    function afterSave( $new )
    {
        if( $new && ! $this->position )
        {
            $last = $this->getQuerySet()->filter( array( 'id__ne' => $this->id, 'section' => $this->section ) )->orderDesc( 'position' )->first();
            $this->position = $last ? $last->position + 1 : 1;
            $this->hiddenSave();
        }
    }
}

?>
