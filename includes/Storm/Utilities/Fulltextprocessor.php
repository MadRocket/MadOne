<?

class Storm_Utilities_Fulltextprocessor {
	function getBaseForm( $text, $language = null ) {
		if( ! $language ) {
			$language = Storm_Core::getLanguage();
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

        if( ! Outer_Phpmorphy::checkLanguage( $language->getName() ) ) {
			return join( ' ', $bulk_words );
		}

        $morphy = new Outer_Phpmorphy();
		$base_form = $morphy->get( $language->getName() )->getBaseForm( $bulk_words );

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
			$language = Storm_Core::getLanguage();
		}

		$words = preg_split('/[\s,.:;!?"\'()]/u', mb_strtolower( strip_tags( $text ) ), -1, PREG_SPLIT_NO_EMPTY);
		
		$bulk_words = array();
		foreach ( $words as $v ) {
            if ( strlen($v) > 3 ) {
                $bulk_words[] = mb_strtoupper($v);
            }
        }

		if( ! Outer_Phpmorphy::checkLanguage( $language->getName() ) ) {
			return join( ' ', $bulk_words );
		}
				
		$morphy = new Outer_Phpmorphy();
		$forms = $morphy->get( $language->getName() )->getAllForms( $bulk_words );
		
		if(!is_array($forms) && is_string($forms)) {
			$forms = array( $forms => array( $forms ) );
		}
		
		return $forms;
	}
}

?>