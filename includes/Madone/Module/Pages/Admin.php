<?php

class Madone_Module_Pages_Admin extends Madone_Module {
    protected $routes = array(
        "/" => 'index',
        "/[i:id]" => "item"
    );

    function index() {
        // Получаем дерево страниц
        $pages = Model_Pages()->kiOrder()->tree();
        
        // Если список пуст, проверим наличие главной страницы
        if( ! $pages ) {
            if( Model_Pages()->filterLevel( 1 )->count() == 0 ) {
                Model_Pages()->createRoot( array( 'title' => 'Стартовая страница', 'type' => Model_Pagetypes( array('app_classname' => 'Madone_Module_Pages_Application'))->first(), 'enabled' => true ) );
                $pages = Model_Pages()->kiOrder()->tree();
            }
        }

        $modules = array(
            array('name' => 'Pages', 'title' => "Текстовая страница"),
            array('name' => 'News', 'title' => "Новости"),
            array('name' => 'Showcase', 'title' => "Каталог"),
            array('name' => 'Gallery', 'title' => "Галерея"),
        );

        return $this->getTemplate( 'index.twig', array(
            'root' => $pages[0],
            'items' => $pages[0]->getChildren(),
            'modules' => $modules
        ) );
    }

    function item($id) {
        $page = Model_Pages()->get($id);

        if($this->container['request']->getMethod() == 'POST') {
            $page->copyFrom($_POST);
            $page->save();

            header("Location: /admin/pages/");
            return true;
        }

        $modules = array(
            array('name' => 'Pages', 'title' => "Текстовая страница"),
            array('name' => 'News', 'title' => "Новости"),
            array('name' => 'Showcase', 'title' => "Каталог"),
            array('name' => 'Gallery', 'title' => "Галерея"),
        );
        return $this->getTemplate( 'item.twig', array(
            'page' => $page,
            'modules' => $modules
        ) );
    }
}
