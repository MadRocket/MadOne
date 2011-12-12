<?
/**
 * Управление интернет-витриной
 */
class Madone_Module_Showcase_Admin extends Madone_Module
{
    /**
     * Получение содержимого административного интерфейса
     */
    function handleHtmlRequest( $uri ) {
        // Получаем дерево категорий
//        $sections = Model_ShowcaseSections()->kiOrder()->tree();

        // Если список пуст, проверим наличие главной категории
//        if( ! $sections ) {
//            if( Model_ShowcaseSections()->filterLevel( 1 )->count() == 0 ) {
//                Model_ShowcaseSections()->createRoot( array( 'title' => 'Главный раздел' ) );
//		        $sections = Model_ShowcaseSections()->kiOrder()->tree();
//            }
//        }
        
		// Фильтруем вывод позиций раздела
		$names = Madone_Utilites::getUriPathNames();
		if( $names && array_key_exists( 2, $names ) && is_numeric( $id = $names[2] ) ) {
			if( $page = Model_Pages()->get( $id ) ) {
                $pagination = new Madone_Paginator( $page->items->order( 'position' )->order( 'id' ), 20 );
				return $this->getTemplate( 'items.twig', array(
					'paginator' => $pagination,
					'items' => $pagination->getObjects(),
					'page'	=> $page,
                    'sessid' => $_COOKIE['PHPSESSID']
				) );
			}
		}
		
//        return $this->getTemplate( 'index.twig', array (
//            'root' => $sections[0],
//            'items' => $sections[0]->getChildren(),
//        ) );
    }

}

?>