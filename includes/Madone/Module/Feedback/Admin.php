<?php

class Madone_Module_Feedback_Admin extends Madone_Module {
	function index() {
		$paginator = new Madone_Paginator( Model_Feedbackmessages()->orderDesc( 'date' ), 20 );
        $paginator->setContainer($this->container);

		if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
			$path = $paginator->getPageCount() ? "page{$paginator->getPageCount()}/" : "";
			header(  "Location: {$this->uri}/{$path}", true );
			exit;
		}
		return $this->getTemplate( 'index.twig',
           array(
                'paginator' => $paginator,
                'items' => $paginator->getObjects()
           )
        );
	}
}
