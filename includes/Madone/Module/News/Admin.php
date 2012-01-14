<?php

class Madone_Module_News_Admin extends Madone_Module {
    protected $routes = array(
        "/[i:id]" => "_index"
    );

    function _index($id) {
        $page = Model_Pages()->get(array('id' => $id));

        $paginator = new Madone_Paginator( Model_NewsList(array('page' => $page))->orderDesc( 'date' ), 10 );
        $paginator->setContainer($this->container);

        if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
        	$path = $paginator->getPageCount() ? "page{$paginator->getPageCount()}/" : "";

			$this->container['response']->setStatusCode(302);
            $this->container['response']->headers->set('Location', $path);
            $this->container['response']->send();

            exit;
        }

        return $this->getTemplate( 'index.twig', array( 'paginator' => $paginator, 'items' => $paginator->getObjects(), 'page' => $page ) );
    }
}
