<?

class StormDbColumn {
	protected $name;
	protected $language;
	
	function __construct( $name, $language = null ) {
		$this->name = $name;
		$this->language = $language;	
	}
	
	function __get( $name ) {
		if( property_exists( $this, $name ) ) {
			return $this->$name;
		}
		return null;
	}
}

?>