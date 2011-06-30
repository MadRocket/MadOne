<?

/**
    Вывод страницы новостей
*/

class NewsApplication extends AbstractApplication {
    /**
        Запуск приложения!
            $page - соответствующий объект структуры сайта
            $uri - путь к искомой странице _внутри_ приложения.
        Возвращает true, если страница обработана этим приложением, false, если страница приложением не обработана.
    */
    function run( MadonePage $page, $uri = '' ) {
		if( $uri && preg_match( '/\/rss$/', $uri ) ) {
        	if( ! headers_sent() ) {
        		header("Content-type: application/rss+xml");
        	}
			print new Template( 'news-rss', array( 'page' => $page, 'items' => MadoneNewsList(array('enabled' => true))->all() ) );
        } 
        elseif( $uri && preg_match( '/\/news(\d+)$/', $uri, $m ) ) {
        	$id = intval($m[1]);
        	
        	$item = MadoneNewsList(array('id' => $id))->first();
        	
        	if($item) {
				print new Template( 'news-item-page', array( 'page' => $page, 'item' => $item ) );	        	
        	}
        	else {
	        	return false;
        	}
        }
        else {
			$paginator = new StormPaginator( MadoneNewsList( array( 'enabled' => true ) )->orderDesc( 'date' ), 'paginator', 10 );
	        
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
	        print new Template( 'news-page', array( 'page' => $page, 'paginator' => $paginator, 'type' => 'companynews' ) );
        }
        
		return true;        
    }
}

?>