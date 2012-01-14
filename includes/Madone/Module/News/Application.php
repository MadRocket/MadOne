<?php

/**
 * Вывод страницы новостей
 */
class Madone_Module_News_Application extends Madone_Application {
    protected $routes = array(
        '/?' => 'index',
        '/news[i:id]' => 'view',
        '/rss' => 'rss'
    );

    function index() {
        $paginator = new Madone_Paginator( Model_Newslist( array( 'enabled' => true, 'page' => $this->page ) )->orderDesc( 'date' ), 5 );

        // Выбрана левая страница - не обрабатываем
        if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
            return false;
        }

        return $this->render('index.twig', array('items' => $paginator->getObjects(), 'paginator' => $paginator, 'page' => $this->page));
    }

    function view($id) {
        $item = Model_Newslist(array('id' => $id))->first();
        if($item) {
            return $this->render( 'view.twig', array( 'page' => $this->page, 'item' => $item ) );
        }
        return false;
    }

    function rss() {
        if( ! headers_sent() ) {
            header("Content-type: application/rss+xml");
        }

//        print new Template( 'news-rss', array( 'page' => $this->page, 'items' => Model_Newslist(array('enabled' => true))->all() ) );
    }

    function last($number = 5) {
        $news = Model_Newslist( array( 'enabled' => true ) )->orderDesc( 'date' )->limit($number);
        return $this->render('last.twig', array('items' => $news, 'page' => $this->page));
    }
}