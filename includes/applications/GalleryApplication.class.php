<?
/**
 * GalleryApplication class - Вывод страницы фотогалереи
 * 
 * @extends Madone_Application
 *
 * Default settings:
 * title = Фотогалерея
 * has_text = 0
 * has_meta = 1
 * has_subpages = 1
 * priority = 2
 */
 
class GalleryApplication extends Madone_Application {
    /**
        Запуск приложения!
            $page - соответствующий объект структуры сайта
            $uri - путь к искомой странице _внутри_ приложения.
        Возвращает true, если страница обработана этим приложением, false, если страница приложением не обработана.
    */
    function run( MadonePage $page, $uri = '' ) {
        $path = Mad::getUriPathNames( $uri );
        
        if( preg_match( '/\/page\d+$/', $uri ) ) {
			// Отбросим страницу из uri, paginator сам знает что к чему
			array_pop($path);
			$uri = "/".join("/", $path);
		}
		
        // Не указывает ли наш uri на картинку?
        $image = null;
        if( $path ) {
        	try {
				$image = MadoneGalleryImages( array( 'pk' => array_pop( $path ) ) )->follow( 2 )->first();
				if( $image ) {
					$uri = $path ? '/' . join( '/', $path ) : '';
				}
			} catch( StormException $e ) {
			}
		}

    	// Определяем выбраный раздел
    	$section = null;
    	$paginator = null;    	
    	if( $uri || $page->app_settings != null ) {
    		if ($uri ) {
    			$section = MadoneGallerySections( array( 'enabled' => true, 'uri' => $uri ) )->first();
    		} else {
    			$section = MadoneGallerySections( array( 'enabled' => true, 'id' => $page->app_settings ) )->first();
    		}
    		
    		
    		// Передан uri, по которому не нашелся раздел
    		if( ! $section ) {
    			return false;
    		} else {
    			$paginator = new StormPaginator( $section->images->order( 'position' ), 'paginator', 5 );
    		}
    	}
    	
    	// Проверим чтобы картинка была внутри своей категории
    	if( $image && ! ( ( $image->section == null && $section == null ) || $image->section->id == $section->id ) ) {
    		return false;
    	}
    	
    	// Если картинки нет — возьмем первую из категории
    	if( $section && ! $image ) {
    		$image = $section->images->order( 'position' )->first();
    	}
    	
        // Остальное - обрабатываем
        print new Template( 'gallery-page', array(
            'page'	=> $page,
            'section'	=> $section,
			'image'	=> $image,
			'paginator'	=> $paginator,
		) );

        return true;
    }
}

?>