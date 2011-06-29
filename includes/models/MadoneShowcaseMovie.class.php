<?
/**
 *  Галерея
 */

class MadoneShowcaseMovie extends StormModel
{
    static function definition()
    {
        return array
        (

			'section'	=> new StormFkDbField( array( 'model' => 'MadoneShowcaseItem', 'related' => 'movies' ) ),
            'title'		=> new StormCharDbField( array( 'maxlength' => 255 ) ),
			'movie'		=> new StormFlvDbField( array(
				'path'		=> '/upload/files/showcase',
				'ffmpeg'	=> '/opt/local/bin/ffmpeg',
				'width'		=> 320,
				'height'	=> 240,
			) ),
			'position'	=> new StormIntegerDbField(),
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
