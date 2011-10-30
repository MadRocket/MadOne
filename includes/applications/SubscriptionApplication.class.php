<?
/**
 * SubscriptionApplication class.
 * 
 * @extends Madone_Application
 *
 * Default settings:
 * title = Подписка
 * has_text = 1
 * has_meta = 1
 * has_subpages = 1
 * priority = 2
 */
class SubscriptionApplication extends Madone_Application {
	function run( MadonePage $page, $uri = '' ) {
		$vars = Mad::vars();
		$mode = null;
		
		switch( $uri ) {
			case '/save':
				if( array_key_exists( 'subscribe', $vars ) && preg_match( '/^[^@]+@[^@]+\.[^@]+$/', $vars['subscribe'] ) ) {
					if( ! MadoneSubscriptionRecipients( array( 'email' => $vars['subscribe'] ) )->count() ) {
						MadoneSubscriptionRecipients()->create( array( 'email' => $vars['subscribe'] ) );
					}
					$mode = 'added';
				} elseif( array_key_exists( 'unsubscribe', $vars ) && preg_match( '/^[^@]+@[^@]+\.[^@]+$/', $vars['unsubscribe'] ) ) {
					$recipient = MadoneSubscriptionRecipients( array( 'email' => $vars['unsubscribe'] ) )->first();
					if( $recipient ) {
						$recipient->delete();
					}
					$mode = 'removed';
				}

				break;
				
			case '/unsubscribe':
					$mode = 'unsubscribe';
				break;
		}

		print new Template( 'subscription-page', array(
			'page' => $page,
			'mode' => $mode,
		) );
		return true;    	
	}
}

?>