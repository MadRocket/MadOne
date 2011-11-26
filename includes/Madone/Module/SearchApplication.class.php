<?

class SearchApplication extends Madone_Application {

    function run( Model_Page $page, $uri = '' ) {
    	$vars = & Madone_Utilites::vars();
    	$query = '';
    	$paginator = null;
    	
    	if( array_key_exists('q', $vars) && $vars['q'] != "" ) {
	    	$query = strip_tags($vars['q']);
			
			if(Madone_Core::getLanguage() == 'zh') {
				$paginator = new Madone_Paginator( Model_SearchRecords( array( 'text__contains' => $query ) ), 'paginator', 15,  "{$page->uri}/page%{page}/?q={$query}" );
			}
			else {
				$paginator = new Madone_Paginator( Model_SearchRecords( array( '*__match' => $query ) )->orderRelevant(), 'paginator', 15,  "{$page->uri}/page%{page}/?q={$query}" );
			
			}
    	}

		print new Template( 'search-page', array( 'page' => $page, 'query' => $query, 'paginator' => $paginator ) );

        return true;
    }
}

?>