<?
/**
 * TextPageApplication class.
 * 
 * @extends AbstractApplication
 *
 * Default settings:
 * title = Обычная страница (текст с изображениями)
 * has_text = 1
 * has_meta = 1
 * has_subpages = 0
 * priority = 2
 */
class TextPageApplication extends AbstractApplication
{
    function run( MadonePage $page, $uri = '' )
    {
        print new Template( 'text-page', array( 'page' => $page ) );
        
        return true;
    }
}

?>