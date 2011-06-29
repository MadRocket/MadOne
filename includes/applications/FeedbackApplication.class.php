<?
/**
 * FeedbackApplication class.
 * 
 * @extends AbstractApplication
 * Default settings:
 * title = Обратная связь
 * has_text = 1
 * has_meta = 1
 * has_subpages = 1
 * priority = 2
 */
class FeedbackApplication extends AbstractApplication {
	function run( MadonePage $page, $uri = '' ) {
		$vars = Mad::vars();
		$mode = null;
		
		switch( $uri ) {
			case '/send':
				if( array_key_exists( 'text', $vars ) && $vars['text'] ) {
					MadoneFeedbackMessages()->create( $vars );
					$mode = 'added';
				} else {
					$mode = 'notext';
				}
				break;
		}

		print new Template( 'feedback-page', array(
			'page' => $page,
			'mode' => $mode,
		) );
		return true;    	
	}
}

?>