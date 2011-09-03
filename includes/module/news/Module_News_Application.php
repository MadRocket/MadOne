<?php
/**
 * 
 * @author \$Author$
 */
 
class Module_News_Application extends AbstractApplication {
    function run( MadonePage $page, $uri = '' ) {
		if( $uri && preg_match( '/\/rss$/', $uri ) ) {
        	if( ! headers_sent() ) {
        		header("Content-type: application/rss+xml");
        	}
			return new Template( 'news-rss', array( 'page' => $page, 'items' => MadoneNewsList(array('enabled' => true))->all() ) );
        }
        elseif( $uri && preg_match( '/\/news(\d+)$/', $uri, $m ) ) {
        	$id = intval($m[1]);

        	$item = MadoneNewsList(array('id' => $id))->first();

        	if($item) {
				return new Template( 'news-item-page', array( 'page' => $page, 'item' => $item ) );
        	}
        	else {
	        	return false;
        	}
        }
        else {
			$paginator = new StormPaginator( MadoneNewsList( array( 'enabled' => true, 'page' => $page ) )->orderDesc( 'date' ),  10 );

	        // Выбрана левая страница - не обрабатываем
	        if( ! $paginator->getObjects() && $paginator->getPage() > 1 )
	        {
	            return false;
	        }

	        // Передан не наш uri - не обрабатываем
	        if( $uri && ! preg_match( '/^\/page\d+$/', $uri ) )
	        {
	            return false;
	        }

	        // Остальное - обрабатываем
	        return new Template( 'news-page', array( 'page' => $page, 'paginator' => $paginator, 'type' => 'companynews' ) );
        }
    }
}