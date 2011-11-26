<?

class Madone_Module_Pages_Admin extends Madone_Module {

    function handleHtmlRequest( $uri ) {
        // Получаем дерево страниц
        $pages = Model_Pages()->kiOrder()->follow(2)->tree();
        
        // Если список пуст, проверим наличие главной страницы
        if( ! $pages ) {
            if( Model_Pages()->filterLevel( 1 )->count() == 0 ) {
                Model_Pages()->createRoot( array( 'title' => 'Стартовая страница', 'type' => Model_Pagetypes( array('app_classname' => 'Madone_Module_Pages_Application'))->first(), 'enabled' => true ) );
                $pages = Model_Pages()->kiOrder()->tree();
            }
        }

        $types = Model_Pagetypes( array( 'enabled' => true ) )->order( 'position' )->all();
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