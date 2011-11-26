<?
/**
 * Подписчик рассылки
 */
class Model_Subscriptionrecipient extends Storm_Model {
	static function definition() {
		return array(
			'email'		=> new Storm_Db_Field_Char( array( 'localized' => false, 'maxlength' => 255 ) ),
			'date'		=> new Storm_Db_Field_Datetime( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
			'enabled'	=> new Storm_Db_Field_Bool( array( 'default' => 1 ) ),
		);
	}
}

?>
