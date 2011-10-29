<?php

/**
 * Вывод страницы новостей
 */
class NewsApplication extends AbstractApplication {
    protected $routes = array(
        '/?' => 'index',
        '/news[i:id]' => 'view',
        '/rss' => 'rss'
    );

    function index() {
        $paginator = new StormPaginator( MadoneNewsList( array( 'enabled' => true ) )->orderDesc( 'date' ), 3 );

        // Выбрана левая страница - не обрабатываем
        if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
            return false;
        }

        print new Template( 'news-page', array( 'page' => $this->page, 'paginator' => $paginator, 'type' => 'companynews' ) );
    }

    function view($id) {
        $item = MadoneNewsList(array('id' => $id))->first();
        if($item) {
            print new Template( 'news-item-page', array( 'page' => $this->page, 'item' => $item ) );
        }
    }

    function rss() {
        if( ! headers_sent() ) {
            header("Content-type: application/rss+xml");
        }

        print new Template( 'news-rss', array( 'page' => $this->page, 'items' => MadoneNewsList(array('enabled' => true))->all() ) );
    }
}