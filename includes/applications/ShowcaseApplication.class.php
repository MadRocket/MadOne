<?
/**
 * ShowcaseApplication class.
 * 
 * @extends AbstractApplication
 *
 * Default settings:
 * title = Каталог
 * has_text = 0
 * has_meta = 1
 * has_subpages = 1
 * priority = 2
 */
class ShowcaseApplication extends AbstractApplication {
    function run( MadonePage $page, $uri = '' ) {
		$names = Mad::getUriPathNames( $uri );
		
		$templateVars = array(
			'page' => $page,
		);
		
		if( $names ) {
			if( preg_match( '/page\d+/', $names[ count( $names ) - 1 ] ) ) {
				array_pop( $names );
			}
			
			$id = null;
			if( is_numeric( $names[ count( $names ) - 1 ] ) ) {
				$id = array_pop( $names );
			}

			$section = MadoneShowcaseSections( array( 'enabled' => true, 'uri' => '/'.join( '/', $names ) ) )->first();
			if( ! $section ) {
				return false;
			}

			$templateVars['catalogSection'] = $section;
			
			if( $id ) {
				$item = MadoneShowcaseItems( array( 'section' => $section, 'pk' => $id, 'enabled' => true ) )->first();
				
				if( ! $item ) {
					return false;
				}

				$templateVars['catalogItem'] = $item;
				
				print new Template( 'showcase-item-page', $templateVars );
				
				return true;
			}
			else {
				// Сделаем выборку позиций
				$subsections = MadoneShowcaseSections( array( 'enabled' => true ) )->filterChildren( $section )->embrace( $section )->kiOrder()->all();
				
				$paginator = new StormPaginator( MadoneShowcaseItems( array( 'enabled' => true, 'section__in' => $subsections ) )->follow( 2 )->order( 'section__lk' )->order( 'title' ), 'paginator', 10 );
	
				if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
					return false;
				}
				
				$templateVars['paginator'] = $paginator;
			
				print new Template( 'showcase-page', $templateVars );
			}
		} else {
				print new Template( 'showcase-index-page', $templateVars );
		}
		

        return true;
    }
}

?>