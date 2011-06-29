<?
/**
*	Значение поля StormDbField модели.
*	Учитывает факт локализованности и все прочие штуки.
*/

class StormDbFieldValueStorage {
	protected $storage = null;
	protected $localized;

	function __construct( $localized ) {
		$this->localized = $localized;
		if( $this->localized ) {
			$this->storage = array();
		}
	}
	
	protected function getStorageKey( $language ) {
		if( ! $this->localized ) {
			return null;
		}
		if( ! $language instanceof StormLanguage ) {
			$language = StormCore::getLanguage();
		}
		return $language->name;
	}

	function get( $language = null ) {
		$storageKey = $this->getStorageKey( $language );
		if( $storageKey ) {
			return array_key_exists( $storageKey, $this->storage ) ? $this->storage[ $storageKey ] : null;
		}
		return $this->storage;
	}

	function set( $value, $language = null ) {
		$storageKey = $this->getStorageKey( $language );
		if( $storageKey ) {
			$this->storage[ $storageKey ] = $value;
		} else {
			$this->storage = $value;
		}
		return $this;
	}
	
	function isNull( $language = null ) {
		$storageKey = $this->getStorageKey( $language );
		return is_null( $this->get( $language ) );
	}
	
	function trigger( $method, $args = array() ) {
		foreach( $this->localized ? $this->storage : array( $this->storage ) as $value ) {
			if( is_object( $value ) && method_exists( $value, $method ) ) {
				call_user_func_array( array( $value, $method ), $args );
			}
		}
	}
}

?>