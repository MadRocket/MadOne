<?

class GalleryModule extends AbstractModule {

    function handleHtmlRequest( $uri ) {
        // Получим дерево разделов
        $sections = MadoneGallerySections()->kiOrder()->tree();
        
        // Если разделов нет — создадим корневой раздел
        if( ! $sections ) {
            if( MadoneGallerySections()->filterLevel( 1 )->count() == 0 ) {
                MadoneGallerySections()->createRoot( array( 'title' => 'Главный раздел', 'enabled' => 1 ) );
                $sections = MadoneGallerySections()->kiOrder()->tree();
            }
        }

        return $this->getTemplate( 'index', array(
            'items' => $sections,
        ) );
    }
}

?>