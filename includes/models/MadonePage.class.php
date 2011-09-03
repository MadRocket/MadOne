<?
/**
    Страница сайта
*/

class MadonePage extends StormKiModel {
    static function definition() {
        return array (
            'title'		=> new StormCharDbField( array( 'maxlength' => 255, 'default' => 'Новая страница' )),
			'name'		=> new StormCharDbField( array( 'localized'=> false, 'maxlength' => 100, 'index' => true ) ),
            'uri'		=> new StormCharDbField( array( 'localized'=> false, 'maxlength' => 700, 'index' => true ) ),
        	'text'		=> new StormTextDbField( array( 'default' => '' ) ),

            'module' => new StormCharDbField( array('default' => 'content') ),
            'template' => new StormCharDbField( array( 'localized' => false, 'default' => 'default') ),

            'menu'			=> new StormBoolDbField( array( 'default' => 1, 'index' => 'nav' ) ),
            'enabled'		=> new StormBoolDbField( array( 'localized'=> true, 'default' => 0, 'index' => 'nav' ) ),

            'meta_title'          => new StormTextDbField(),
            'meta_keywords'       => new StormTextDbField(),
            'meta_description'    => new StormTextDbField(),
        );
    }
    
    function beforeSave() {
        // Имя главной страницы - /
        if( $this->lvl == 1 ) {
            $this->name = '/';
            return true;
        }
        
        // Приводим в порядок name страницы
        if( ! Mad::isPathName( $this->name ) ) {
            // Попробуем сначала довести до ума исходный name
            if( $this->name ) {
                $this->name = Mad::getPathName( $this->name );
            }
            
            // Не получилось — сделаем на основе title
            if( ! Mad::isPathName( $this->name ) ) {
                $this->name = Mad::getPathName( $this->title );
            }
        }
    }
    
    function afterSave( $new ) {
        // Проверим, не продублировали ли мы name кого-то из соседей, если да — добавим к нему свой id
        if( MadonePages()->filterSiblings( $this )->filter( array( 'id__ne' => $this->id, 'name' => $this->name ) )->count() > 0 ) {
            $this->name .= $this->id;
            $this->hiddenSave();
        }
        
        // Генерируем полный uri страницы
        $path = array_map( create_function( '$i', 'return $i->name;' ), MadonePages()->filterParents( $this )->embrace( $this )->filterLevel( 2, 0 )->kiOrder()->all() );
        $uri = '/'.join( '/', $path );
        if( $this->uri != $uri ) {
            $this->uri = $uri;
            $this->hiddenSave();
        }
        
		// Пересохраним непосредственные дочерние страницы, чтобы обновились их uri
        foreach( MadonePages()->filterChildren( $this )->filterLevel( $this->lvl + 1 )->all() as $sub_page ) {
            // Дочерняя страница сама точно так же обновит все свои дочерние страницы, поэтому и работаем только на 1 уровень вниз
            $sub_page->save();
        }
    }
}

?>
