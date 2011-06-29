<?

class PagesModule extends AbstractModule {

    function handleHtmlRequest( $uri ) {
        // Получаем дерево страниц
        $pages = MadonePages()->kiOrder()->follow(2)->tree();
        
        // Если список пуст, проверим наличие главной страницы
        if( ! $pages ) {
            if( MadonePages()->filterLevel( 1 )->count() == 0 ) {
                MadonePages()->createRoot( array( 'title' => 'Главная страница', 'type' => MadonePageTypes( array('app_classname' => 'IndexPageApplication'))->first(), 'enabled' => true ) );
                $pages = MadonePages()->kiOrder()->tree();
            }
        }
        
        return $this->getTemplate( 'index', array(
            'items' => $pages,
            'types' => MadonePageTypes( array( 'enabled' => true ) )->order( 'position' )->all(),
        ) );
    }
}

?>