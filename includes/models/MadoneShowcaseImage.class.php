<?
/**
 *  Галерея
 */

class MadoneShowcaseImage extends StormModel
{
    static function definition()
    {
        return array
        (

			'section'  => new StormFkDbField( array( 'model' => 'MadoneShowcaseItem', 'related' => 'images' ) ),
            'title'    => new StormCharDbField( array( 'maxlength' => 255 ) ),
			'image' 	 => new StormImageDbField( array(	'path' => "/upload/images/showcase",
													'variants' => array(
														'large' => array( 'width' => 500, 'format'=> 'jpeg' ),
														'small' => array( 'width' => 120, 'format'=> 'jpeg' ),
														'cms'   => array( 'width' => 120, 'height' => 120, 'spacefill'=> true ),
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
