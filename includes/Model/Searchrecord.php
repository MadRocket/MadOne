<?
/**
 * Model_Searchrecord class.
 * 
 * @extends Storm_Model
 */
class Model_Searchrecord extends Storm_Model
{
    static function definition()
    {
        return array
        (
			'title'			=> new Storm_Db_Field_Char( array( 'maxlength' => 255, 'index' => true, 'fulltext' => true, 'localized' => false ) ),
			'text'			=> new Storm_Db_Field_Text( array( 'fulltext' => true, 'localized' => false ) ),
			'uri'			=> new Storm_Db_Field_Char( array( 'maxlength' => 700, 'localized' => false ) ),
			'date'			=> new Storm_Db_Field_Datetime( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
        );
    }

}

?>
