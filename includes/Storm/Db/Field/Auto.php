<?
/**
*	Поле-первичный ключ. Должно использовать автоинкремент в БД.
*/
class Storm_Db_Field_Auto extends Storm_Db_Field {
	protected $null = false;
	protected $localized = false;

	function __construct( array $params = array() ) {
		if( array_key_exists( 'localized', $params ) && $params['localized'] ) {
			throw new Storm_Exception( get_class( $this )." cannot be localized" );
		}
		if( array_key_exists( 'null', $params ) && $params['null'] ) {
			throw new Storm_Exception( get_class( $this )." cannot be null" );
		}
		parent::__construct( $params );
	}

	function setValue( $string, $language = null ) {
		if( ! is_numeric( $string ) || $string <= 0 ) {
			throw new Storm_Exception_Validation( "'$string' is not valid Storm_Db_Field_Auto value", $this );
		}
		return parent::setValue( (int)$string, $language );
	}
}

?>
