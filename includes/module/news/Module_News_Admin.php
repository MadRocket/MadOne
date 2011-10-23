<?php

class Module_News_Admin extends Madone_Module {

    function handleHtmlRequest( $uri ) {
        $path = Mad::getUriPathNames($uri);
        $page = array_key_exists(0, $path) ? $path[0] : null;

        $paginator = new StormPaginator( MadoneNewsList(array('page' => $page))->orderDesc( 'date' ), 10 );
        
        if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
        	$path = $paginator->getPageCount() ? "page{$paginator->getPageCount()}/" : "";
			header(  "Location: {$this->uri}/{$path}", true );
            exit;
        }
        
        return $this->getTemplate( 'index.twig', array( 'paginator' => $paginator, 'items' => $paginator->getObjects(), 'page' => $page ) );
    }
}