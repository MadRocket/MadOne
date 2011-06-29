<?
/**
 * MadoneSearchRecord class.
 * 
 * @extends StormModel
 */
class MadoneSearchRecord extends StormModel
{
    static function definition()
    {
        return array
        (
			'title'			=> new StormCharDbField( array( 'maxlength' => 255, 'index' => true, 'fulltext' => true, 'localized' => false ) ),
			'text'			=> new StormTextDbField( array( 'fulltext' => true, 'localized' => false ) ),
			'uri'			=> new StormCharDbField( array( 'maxlength' => 700, 'localized' => false ) ),
			'date'			=> new StormDatetimeDbField( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),			
        );
    }

}

?>
