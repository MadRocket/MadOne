<?php

/**
 * Вывод страницы новостей
 */
class Module_News_Application extends Madone_Application {
    protected $routes = array(
        '/?' => 'index',
        '/news[i:id]' => 'view',
        '/rss' => 'rss'
    );

    function index() {
        $paginator = new StormPaginator( MadoneNewsList( array( 'enabled' => true ) )->orderDesc( 'date' ), 5 );

        // Выбрана левая страница - не обрабатываем
        if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
            return false;
        }

        print $this->render('news/index.twig', array('items' => $paginator->getObjects(), 'paginator' => $paginator, 'page' => $this->page));
        return true;
    }

    function view($id) {
        $item = MadoneNewsList(array('id' => $id))->first();
        if($item) {
            print $this->render( 'news/view.twig', array( 'page' => $this->page, 'item' => $item ) );
        }
    }

    function rss() {
        if( ! headers_sent() ) {
            header("Content-type: application/rss+xml");
        }

        print new Template( 'news-rss', array( 'page' => $this->page, 'items' => MadoneNewsList(array('enabled' => true))->all() ) );
    }

    function last($number = 5) {
        $news = MadoneNewsList( array( 'enabled' => true ) )->orderDesc( 'date' )->limit($number);
        return $this->render('news/last.twig', array('items' => $news, 'page' => $this->page));
    }
}