<?

class PagesModule extends Madone_Module {

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

        $types = MadonePageTypes( array( 'enabled' => true ) )->order( 'position' )->all();
        $types_array = array();
		foreach( $types as $type ) {
			$types_array[ "{$type->id}" ] = $type->asArray( true );
			$types_array[ "{$type->id}" ]['settings'] = json_decode($type->settings);
		}

        return $this->getTemplate( 'index.twig', array(
            'root' => $pages[0],
            'items' => $pages[0]->getChildren(),
            'types' => $types,
            'types_json' => $types_array
        ) );
    }
}

?>