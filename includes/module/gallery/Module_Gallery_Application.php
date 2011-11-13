<?php
/**
 * Module_Gallery_Application class - Вывод страницы фотогалереи
 * @extends Madone_Application
 */
 
class Module_Gallery_Application extends Madone_Application {

    protected $routes = array(
        '/?' => 'index',
        '/[*:slug]' => 'category'
    );

    public function index() {
        $paginator = new StormPaginator( MadoneGallerySections( array( 'enabled' => true ) )->filterLevel(2,0)->kiOrder(), 9 );
        $items = $paginator->getObjects();
        $items = array_chunk($items, 2);

        print $this->render('gallery/index.twig', array('items' => $items, 'paginator' => $paginator, 'page' => $this->page));
    }

    public function category($slug) {
        $category = MadoneGallerySections( array( 'enabled' => true, 'uri' => "/{$slug}" ) )->first();
        $paginator = new StormPaginator( $category->images->order('position'), 9 );
        $items = $paginator->getObjects();
        $items = array_chunk($items, 2);

        print $this->render('gallery/category.twig', array('category' => $category,'items' => $items, 'paginator' => $paginator, 'page' => $this->page));
    }
}