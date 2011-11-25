<?
/**
 *  Галерея
 */

class MadoneShowcaseMovie extends Storm_Model
{
    static function definition()
    {
        return array
        (

			'section'	=> new Storm_Db_Field_Fk( array( 'model' => 'MadoneShowcaseItem', 'related' => 'movies' ) ),
            'title'		=> new Storm_Db_Field_Char( array( 'maxlength' => 255 ) ),
			'movie'		=> new Storm_Db_Field_File_Flv( array(
				'path'		=> '/upload/files/showcase',
				'ffmpeg'	=> '/opt/local/bin/ffmpeg',
				'width'		=> 320,
				'height'	=> 240,
			) ),
			'position'	=> new Storm_Db_Field_Integer(),
        );
    }
    
    function beforeSave( ) {
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
