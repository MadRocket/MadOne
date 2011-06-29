<?

class SearchApplication extends AbstractApplication {

    function run( MadonePage $page, $uri = '' ) {
    	$vars = & Mad::vars();
    	$query = '';
    	$paginator = null;
    	
    	if( array_key_exists('q', $vars) && $vars['q'] != "" ) {
	    	$query = strip_tags($vars['q']);
			
			if(Madone::getLanguage() == 'zh') {
				$paginator = new StormPaginator( MadoneSearchRecords( array( 'text__contains' => $query ) ), 'paginator', 15,  "{$page->uri}/page%{page}/?q={$query}" );			
			}
			else {
				$paginator = new StormPaginator( MadoneSearchRecords( array( '*__match' => $query ) )->orderRelevant(), 'paginator', 15,  "{$page->uri}/page%{page}/?q={$query}" );			
			
			}
    	}

		print new Template( 'search-page', array( 'page' => $page, 'query' => $query, 'paginator' => $paginator ) );

        return true;
    }
}

?>