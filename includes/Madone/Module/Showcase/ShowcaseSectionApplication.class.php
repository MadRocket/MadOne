<?
/**
 * ShowcaseSectionApplication class.
 * 
 * @extends Madone_Application
 *
 * Default settings:
 * title = Каталог
 * has_text = 0
 * has_meta = 1
 * has_subpages = 1
 * priority = 2
 */
class ShowcaseSectionApplication extends Madone_Application {
    function run( Model_Page $page, $uri = '' ) {
		$names = Madone_Utilites::getUriPathNames( $uri );
		$templateVars = array('page' => $page);
		
		if( $names && preg_match( '/page\d+/', $names[ count( $names ) - 1 ] ) ) {
			array_pop( $names );
		}
		
		$id = null;
		if( is_numeric( $names[ count( $names ) - 1 ] ) ) {
			$id = array_pop( $names );
		}

		if( count($names) > 0 ) {
			$section = Model_ShowcaseSections( array( 'enabled' => true, 'uri' => '/'.join( '/', $names ) ) )->first();
		}
		else {
			$section = Model_ShowcaseSections( array( 'enabled' => true, 'id' => $page->app_settings ) )->first();
		}

		if( ! $section ) {
			return false;
		}

		$templateVars['catalogSection'] = $section;
		
		if( $id ) {
			$item = Model_ShowcaseItems( array( 'section' => $section, 'pk' => $id, 'enabled' => true ) )->first();
			
			if( ! $item ) {
				return false;
			}

			$templateVars['catalogItem'] = $item;
			
			print new Template( 'showcase-item-page', $templateVars );
			
			return true;
		}
		else {
			// Сделаем выборку позиций
			$subsections = Model_ShowcaseSections( array( 'enabled' => true ) )->filterChildren( $section )->embrace( $section )->kiOrder()->all();
			
			$paginator = new Madone_Paginator( Model_ShowcaseItems( array( 'enabled' => true, 'section__in' => $subsections ) )->follow( 2 )->order( 'section__lk' )->order( 'title' ), 'paginator', 10 );

			if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
				return false;
			}
			
			$templateVars['paginator'] = $paginator;
		
			print new Template( 'showcase-page', $templateVars );
		}

        return true;
    }
}

?>