<?
class GuestbookModule extends Madone_Module {
	function handleHtmlRequest( $uri ) {
		$paginator = new Madone_Paginator( Model_GuestbookRecords()->orderDesc( 'date' ), 'core/paginator', 20 );
		if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
			$path = $paginator->getPageCount() ? "page{$paginator->getPageCount()}/" : "";
			header(  "Location: {$this->uri}/{$path}", true );
			exit;
		}
		return $this->getTemplate( 'index', array( 'paginator' => $paginator ) );
	}
}

?>