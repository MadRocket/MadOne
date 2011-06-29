<?
/**
 * Подписчик рассылки
 */
class MadoneSubscriptionRecipient extends StormModel {
	static function definition() {
		return array(
			'email'		=> new StormCharDbField( array( 'localized' => false, 'maxlength' => 255 ) ),
			'date'		=> new StormDatetimeDbField( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
			'enabled'	=> new StormBoolDbField( array( 'default' => 1 ) ),
		);
	}
}

?>
