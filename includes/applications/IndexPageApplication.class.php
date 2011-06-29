<?
/**
 * IndexPageApplication class.
 * 
 * @extends AbstractApplication
 *
 * Default settings:
 * title = Главная страница
 * has_text = 1
 * has_meta = 1
 * has_subpages = 0
 * priority = 1
 */

class IndexPageApplication extends AbstractApplication
{
    function run( MadonePage $page, $uri = '' )
    {
        print new Template( 'index-page', array( 'page' => $page ) );
        return true;
    }
}

?>