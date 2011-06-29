<?
class FeedbackModule extends AbstractModule {
	function handleHtmlRequest( $uri ) {
		$paginator = new StormPaginator( MadoneFeedbackMessages()->orderDesc( 'date' ), 'core/paginator', 20 );
		if( ! $paginator->objects && $paginator->page > 1 ) {
			$path = $paginator->pageCount ? "page{$paginator->pageCount}/" : "";
			header(  "Location: {$this->uri}/{$path}", true );
			exit;
		}
		return $this->getTemplate( 'index', array( 'paginator' => $paginator ) );
	}
}

?>