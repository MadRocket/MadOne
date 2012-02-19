<?

class Madone_Module_Search_Application extends Madone_Application
{
    function index() {
    	$vars = & Madone_Utilites::vars();
    	$query = '';
    	$paginator = null;
    	
    	if( array_key_exists('q', $vars) && $vars['q'] != "" ) {
	    	$query = strip_tags($vars['q']);
			
//				$paginator = new Madone_Paginator( Model_SearchRecords( array( 'text__contains' => $query ) ), 'paginator', 15,  "{$page->uri}/page%{page}/?q={$query}" );
				$paginator = new Madone_Paginator( Model_SearchRecords( array( '*__match' => $query ) )->orderRelevant(), 'paginator', 15,  "{$page->uri}/page%{page}/?q={$query}" );
    	}

		$this->render( 'index.twig', array( 'query' => $query, 'paginator' => $paginator ) );
    }
}

?>