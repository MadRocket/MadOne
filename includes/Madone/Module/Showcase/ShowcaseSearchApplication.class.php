<?
/**
 * ShowcaseSearchApplication class.
 * 
 * @extends Madone_Application
 *
 * Default settings:
 * title = Поиск по каталогу
 * has_text = 0
 * has_meta = 1
 * has_subpages = 1
 * priority = 2
 */
class ShowcaseSearchApplication extends Madone_Application
{
    function run( Model_Page $page, $uri = '' )
    {
    	$vars = & Madone_Utilites::vars();
    	$query = '';
    	$paginator = null;
    	
    	if( array_key_exists('q', $vars) && $vars['q'] != "" ) {
	    	$query = strip_tags($vars['q']);
			$paginator = new Madone_Paginator( Model_ShowcaseItems( array( 'enabled' => true, 'section__enabled' => true, '*__match' => $query ) )->follow( 2 )->orderRelevant(), 'paginator', 10,  "{$page->uri}/page%{page}/?q={$query}" );
    	}

		print new Template( 'showcase-search-page', array( 'page' => $page, 'query' => $query, 'paginator' => $paginator ) );

        return true;
    }
}

?>