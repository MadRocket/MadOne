<?
/**
 * Управление интернет-витриной
 */
class ShowcaseModule extends AbstractModule
{
    /**
     * Получение содержимого административного интерфейса
     */
    function handleHtmlRequest( $uri ) {
        // Получаем дерево категорий
        $sections = MadoneShowcaseSections()->kiOrder()->tree();

        // Если список пуст, проверим наличие главной категории
        if( ! $sections ) {
            if( MadoneShowcaseSections()->filterLevel( 1 )->count() == 0 ) {
                MadoneShowcaseSections()->createRoot( array( 'title' => 'Главный раздел' ) );
		        $sections = MadoneShowcaseSections()->kiOrder()->tree();
            }
        }
        
		// Фильтруем вывод позиций раздела
		$names = Mad::getUriPathNames();
		if( $names && array_key_exists( 2, $names ) && is_numeric( $id = $names[2] ) ) {
			if( $section = MadoneShowcaseSections()->get( $id ) ) {
				return $this->getTemplate( 'items', array(
					'paginator' => new StormPaginator( $section->items->order( 'title' )->order( 'id' ), 'core/paginator', 20 ),
					'section'	=> $section,
				) );
			}
		}
		
        return $this->getTemplate( 'index', array (
            'items' => $sections,
        ) );
    }

}

?>