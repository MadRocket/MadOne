<?

class StormFulltextProcessor {

	protected $dictionaries;
	protected $options;
	
	function __construct() {
		// Подключаем phpMorphy, готовоим опции
		require_once( dirname( __FILE__ )."/3rdparty/phpmorphy-0.3.7/src/common.php" );

		$this->dictionaries = array(
			'ru' => array(
				'path'	=> dirname( __FILE__ )."/3rdparty/phpmorphy-dicts/morphy-0.3.x-ru_RU-nojo-utf8",
				'lang'	=> 'ru_RU',
			),
		);

		$this->options = array(
			'storage'			=> PHPMORPHY_STORAGE_FILE,
			'predict_by_suffix'	=> true,
			'predict_by_db'		=> true,
			'graminfo_as_text'	=> false,
		);
	}
	
	function getBaseForm( $text, $language = null ) {
	
		if( ! $language ) {
			$language = StormCore::getLanguage();
		}
		
		$text = mb_strtolower( strip_tags( $text ) );
		$text = preg_replace('~&(#x([0-9a-f]+)|\w+);~eiu', '', $text );
		
		$words = preg_replace( '/\[.*\]/isUu', '', $text );
		$words = preg_split( '/[\s,.:;!?"\'()]/u', $words, -1, PREG_SPLIT_NO_EMPTY );
		
		$bulk_words = array();
		foreach( $words as $v ) {
			if( mb_strlen( $v ) > 3 ) {
				$bulk_words[] = mb_strtoupper( $v );
			}
		}
		
		if( ! array_key_exists( $language->getName(), $this->dictionaries ) ) {
			return join( ' ', $bulk_words );
		}
				
		$morphy = new phpMorphy( $this->dictionaries[ $language->getName() ]['path'], $this->dictionaries[ $language->getName() ]['lang'], $this->options );
		
		$base_form = $morphy->getBaseForm( $bulk_words );
		
		$fullList = array();
		if( $base_form ) {
			foreach( $base_form as $k => $v ) {
				if( is_array( $v ) ) {
					foreach( $v as $v1 ) {
						if( mb_strlen( $v1 ) > 3 ) {
							$fullList[ $v1 ] = 1;
						}
					}
				} else if( mb_strlen( $k ) > 3 ) {
					$fullList[ $k ] = 1;
				}
			}
		}

		return join( ' ', array_keys( $fullList ) );
	}
	
	function getAllForms( $text, $language = null ) {
		if( ! $language ) {
			$language = StormCore::getLanguage();
		}

		$words = preg_split('/[\s,.:;!?"\'()]/u', mb_strtolower( strip_tags( $text ) ), -1, PREG_SPLIT_NO_EMPTY);
		
		$bulk_words = array();
		foreach ( $words as $v )
			if ( strlen($v) > 3 )
				$bulk_words[] = mb_strtoupper($v);
		
		if( ! array_key_exists( $language->getName(), $this->dictionaries ) ) {
			return join( ' ', $bulk_words );
		}
				
		$morphy = new phpMorphy( $this->dictionaries[ $language->getName() ]['path'], $this->dictionaries[ $language->getName() ]['lang'], $this->options );
		
		$forms = $morphy->getAllForms( $bulk_words );
		
		if(!is_array($forms) && is_string($forms)) {
			$forms = array( $forms => array( $forms ) );
		}
		
		return $forms;
	}
}

?>