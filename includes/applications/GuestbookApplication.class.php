<?
/**
 * GuestbookApplication class.
 * 
 * @extends AbstractApplication
 *
 * Default settings:
 * title = Гостевая книга
 * has_text = 0
 * has_meta = 1
 * has_subpages = 1
 * priority = 2
 */
class GuestbookApplication extends AbstractApplication {
	function run( MadonePage $page, $uri = '' ) {
		$vars = Mad::vars();
		$mode = null;
		$paginator = null;
		$maxphotos = 5;
		
		switch( $uri ) {
			case '/send':
				if( ! array_key_exists( 'text', $vars ) || ! $vars['text'] ) {
					$mode = 'notext';
				} elseif( ! MadCaptcha::create()->check() ) {
					$mode = 'nocaptcha';
				}
				else {
					$record = MadoneGuestbookRecords()->create( $vars );
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
				$paginator = new StormPaginator( MadoneGuestbookRecords( array( 'enabled' => true ) )->orderDesc( 'date' ), 'paginator', 10 );
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