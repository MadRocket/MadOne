<?php

/**
 * Вывод страницы новостей
 */
class NewsApplication extends AbstractApplication {
    /**
     * Запуск приложения!
     * @param MadonePage $page - соответствующий объект структуры сайта
     * @param string $uri - путь к искомой странице _внутри_ приложения.
     * @return bool Возвращает true, если страница обработана этим приложением, false, если страница приложением не обработана.
     */
    function run( MadonePage $page, $uri = '' ) {
        respond('/?', function($request, $response) use ($page, $uri) {
            $paginator = new StormPaginator( MadoneNewsList( array( 'enabled' => true ) )->orderDesc( 'date' ), 3 );

            // Выбрана левая страница - не обрабатываем
            if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
                return false;
            }

            print new Template( 'news-page', array( 'page' => $page, 'paginator' => $paginator, 'type' => 'companynews' ) );
        });

        respond('/news[i:id]', function($request, $response) use ($page) {
        	$item = MadoneNewsList(array('id' => $request->id))->first();

        	if($item) {
				print new Template( 'news-item-page', array( 'page' => $page, 'item' => $item ) );
        	}
        });

        respond('/rss', function($request, $response) use($page) {
            if( ! headers_sent() ) {
        		header("Content-type: application/rss+xml");
        	}

			print new Template( 'news-rss', array( 'page' => $page, 'items' => MadoneNewsList(array('enabled' => true))->all() ) );
        });

        return dispatch($uri, null, null, true);
    }
}