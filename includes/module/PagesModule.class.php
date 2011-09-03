<?php

class PagesModule extends AbstractModule {

    function handleHtmlRequest( $uri ) {
        // Получаем дерево страниц
        $pages = MadonePages()->kiOrder()->follow(2)->tree();
        
        // Если список пуст, проверим наличие главной страницы
        if( ! $pages ) {
            if( MadonePages()->filterLevel( 1 )->count() == 0 ) {
                MadonePages()->createRoot( array( 'title' => 'Стартовая страница', 'module' => 'content', 'enabled' => true ) );
                $pages = MadonePages()->kiOrder()->tree();
            }
        }

        $modules = array(
            array('name' => 'content', 'title' => 'Обычная страница'),
            array('name' => 'news', 'title' => 'Лента новостей'),
            array('name' => 'feedback', 'title' => 'Обратная связь'),
        );

        return $this->getTemplate( 'index.twig', array(
            'root' => $pages[0],
            'items' => $pages[0]->getChildren(),
            'modules' => $modules
        ) );
    }
}