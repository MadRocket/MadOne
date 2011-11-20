<?
/**
 * Управление интернет-витриной
 */
class Module_Showcase_Admin extends Madone_Module
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
                $pagination = new StormPaginator( $section->items->order( 'title' )->order( 'id' ), 20 );
				return $this->getTemplate( 'items.twig', array(
					'paginator' => $pagination,
					'items' => $pagination->getObjects(),
					'section'	=> $section,
                    'sessid' => $_COOKIE['PHPSESSID']
				) );
			}
		}
		
        return $this->getTemplate( 'index.twig', array (
            'root' => $sections[0],
            'items' => $sections[0]->getChildren(),
        ) );
    }

}

?>