<?
/**
 *  Галерея
 */

class MadoneShowcaseImage extends Storm_Model
{
    static function definition()
    {
        return array
        (
			'section'  => new Storm_Db_Field_Fk( array( 'model' => 'MadoneShowcaseItem', 'related' => 'images' ) ),
            'title'    => new Storm_Db_Field_Char( array( 'maxlength' => 255 ) ),
			'image' 	 => new Storm_Db_Field_File_Image( array(	'path' => "/upload/images/showcase",
                                                    'variants' => array(
														'cms'   => array( 'width' => 120, 'height' => 120, 'spacefill' => true ),
													))),
			'position' => new Storm_Db_Field_Integer(),
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
