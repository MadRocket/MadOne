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
			'title'   => new StormCharDbField( array( 'maxlength' => 255, 'defaults' => array(
            	'ru' => 'Заголовок новости',
            	'en' => 'News title',
            	) ) ),
			'text'    => new StormTextDbField(),
			'announce'    => new StormTextDbField(),
			'enabled' => new StormBoolDbField( array( 'default' => 1, 'index' => true ) ),
		);
	}
}

?>
