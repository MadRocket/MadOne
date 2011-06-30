<?

/**
 * TableofcontentsApplication class - Вывод содержания раздела
 * 
 * @extends AbstractApplication
 *
 * Default settings:
 * title = Содержание раздела
 * has_text = 1
 * has_meta = 1
 * has_subpages = 0
 * priority = 2
 */
class TableofcontentsApplication extends AbstractApplication
{
    function run( MadonePage $page, $uri = '' )
    {
        $path = Mad::getUriPathNames( $uri );
        
		$paginator = new StormPaginator( MadonePages()->filterChildren( $page ), 'paginator', 10 );

        // Выбрана левая страница - не обрабатываем
        if( ! $paginator->getObjects() && $paginator->getPage() > 1 )
        {
            return false;
        }
        
        // Передан не наш uri - не обрабатываем
        if( $uri && ! preg_match( '/^\/page\d+$/', $uri ) )
        {
            return false;
        }
        
        print new Template('toc-page', array( 'page' => $page, 'paginator' => $paginator ) )

        return true;
    }
}

?>