<?
/**
 * GuestbookApplication class.
 * 
 * @extends Madone_Application
 *
 * Default settings:
 * title = Гостевая книга
 * has_text = 0
 * has_meta = 1
 * has_subpages = 1
 * priority = 2
 */
class GuestbookApplication extends Madone_Application {
	function run( Model_Page $page, $uri = '' ) {
		$vars = Madone_Utilites::vars();
		$mode = null;
		$paginator = null;
		$maxphotos = 5;
		
		switch( $uri ) {
			case '/send':
				if( ! array_key_exists( 'text', $vars ) || ! $vars['text'] ) {
					$mode = 'notext';
				} elseif( ! Madone_Helper_Captcha::create()->check() ) {
					$mode = 'nocaptcha';
				}
				else {
					$record = Model_GuestbookRecords()->create( $vars );
					if( array_key_exists( 'photos', $vars ) && is_array( $vars['photos'] ) ) {
						for( $i = 0; $i < $maxphotos; $i++ ) {
							if( is_uploaded_file( $vars['photos'][$i]['tmp_name'] ) ) {
								$record->photos->create( array( 'image' => $vars['photos'][$i] ) );
							}
						}
					}
					$mode = 'added';
				}
				break;
			default:
				$paginator = new Madone_Paginator( Model_GuestbookRecords( array( 'enabled' => true ) )->orderDesc( 'date' ), 'paginator', 10 );
				break;
		}
		
		print new Template( 'guestbook-page', array(
			'page'		=> $page,
			'mode'		=> $mode,
			'paginator'	=> $paginator,
			'maxphotos'	=> $maxphotos,
		) );
		
		return true;    	
	}
}

?>