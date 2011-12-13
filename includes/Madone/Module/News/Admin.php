<?

class Madone_Module_News_Admin extends Madone_Module {

    function handleHtmlRequest( $uri ) {
        $names = Madone_Utilites::getUriPathNames();
        if( empty($names[2]) || !is_numeric( $id_page = $names[2] ) ) {
            return null;
        }
        $page = Model_Pages()->get(array('id' => $id_page));

        $paginator = new Madone_Paginator( Model_NewsList(array('page' => $page))->orderDesc( 'date' ), 10 );
        
        if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
        	$path = $paginator->getPageCount() ? "page{$paginator->getPageCount()}/" : "";
			header(  "Location: {$this->uri}/{$path}", true );
            exit;
        }
        
        return $this->getTemplate( 'index.twig', array( 'paginator' => $paginator, 'items' => $paginator->getObjects(), 'page' => $page ) );
    }
}

?>