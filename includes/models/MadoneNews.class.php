<?
/**
	Новость
*/

class MadoneNews extends Storm_Model
{
	static function definition()
	{
		return array
		(
			'date'  => new Storm_Db_Field_Datetime( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
			'title'   => new Storm_Db_Field_Char( array( 'maxlength' => 255, 'default' => 'Заголовок новости') ),
            'image' => new Storm_Db_Field_File_Image( array('path' => '/upload/images/news' )),
			'text'    => new Storm_Db_Field_Text(),
			'announce'    => new Storm_Db_Field_Text(),
			'enabled' => new Storm_Db_Field_Bool( array( 'default' => 1, 'index' => true ) ),
		);
	}
}

?>
