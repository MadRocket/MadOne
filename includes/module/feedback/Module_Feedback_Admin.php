<?php
class Module_Feedback_Admin extends AbstractModule {
	function handleHtmlRequest( $uri ) {
		$paginator = new StormPaginator( MadoneFeedbackMessages()->orderDesc( 'date' ), 20 );
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