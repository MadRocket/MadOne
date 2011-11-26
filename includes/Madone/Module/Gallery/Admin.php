<?

class Madone_Module_Gallery_Admin extends Madone_Module {

    function handleHtmlRequest( $uri ) {
        // Получим дерево разделов
        $sections = Model_GallerySections()->kiOrder()->tree();
        
        // Если разделов нет — создадим корневой раздел
        if( ! $sections ) {
            if( Model_GallerySections()->filterLevel( 1 )->count() == 0 ) {
                Model_GallerySections()->createRoot( array( 'title' => 'Главный раздел', 'enabled' => 1 ) );
                $sections = Model_GallerySections()->kiOrder()->tree();
            }
        }

        return $this->getTemplate( 'index.twig', array(
            'items' => $sections,
            'root' => $sections[0],
            'items' => $sections[0]->getChildren(),
            'sessid' => $_COOKIE['PHPSESSID']
        ) );
    }
}

?>