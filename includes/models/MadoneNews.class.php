<?
/**
	Новость
*/

class MadoneNews extends StormModel
{
	static function definition()
	{
		return array
		(
			'date'  => new StormDatetimeDbField( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
			'title'   => new StormCharDbField( array( 'maxlength' => 255, 'default' => 'Заголовок новости') ),
            'image' => new StormImageDbField( array('path' => '/upload/images/news' )),
			'text'    => new StormTextDbField(),
			'announce'    => new StormTextDbField(),
			'enabled' => new StormBoolDbField( array( 'default' => 1, 'index' => true ) ),
		);
	}
}

?>
