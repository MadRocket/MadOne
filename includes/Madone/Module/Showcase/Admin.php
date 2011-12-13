<?
/**
 * Управление интернет-витриной
 */
class Madone_Module_Showcase_Admin extends Madone_Module
{
    /**
     * Получение содержимого административного интерфейса
     * @param $uri
     * @return mixed
     */
    function handleHtmlRequest( $uri ) {

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
        return null;
    }

}

?>