<?
/**
    Страница сайта
*/

class Model_Page extends Storm_Model_Tree {
    static function definition() {
        return array (
            'title'		=> new Storm_Db_Field_Char( array( 'maxlength' => 255, 'default' => 'Новая страница' )),
			'name'		=> new Storm_Db_Field_Char( array( 'localized'=> false, 'maxlength' => 100, 'index' => true ) ),
            'uri'		=> new Storm_Db_Field_Char( array( 'localized'=> false, 'maxlength' => 700, 'index' => true ) ),
        	'text'		=> new Storm_Db_Field_Text( array( 'default' => '' ) ),
        	'type'		=> new Storm_Db_Field_Fk( array(
        									'model' => 'Model_Pagetype',
        									'related' => 'pages', 
        									'null' => false, 
        									'index' => true, 
        									'default' => 0
            ) ),

            'template' => new Storm_Db_Field_Char( array('maxlength' => 255, 'localized' => false, 'default' => null) ),

        	'app_settings'	=> new Storm_Db_Field_Text( array( 'localized' => false ) ),

            'menu'			=> new Storm_Db_Field_Bool( array( 'default' => 1, 'index' => 'nav' ) ),
            'enabled'		=> new Storm_Db_Field_Bool( array( 'localized'=> true, 'default' => 0, 'index' => 'nav' ) ),

            'meta_title'          => new Storm_Db_Field_Text(),
            'meta_keywords'       => new Storm_Db_Field_Text(),
            'meta_description'    => new Storm_Db_Field_Text(),
        );
    }
    
    function beforeSave() {
        // Имя главной страницы - /
        if( $this->lvl == 1 ) {
            $this->name = '/';
            return true;
        }
        
        // Приводим в порядок name страницы
        if( ! Madone_Utilites::isPathName( $this->name ) ) {
            // Попробуем сначала довести до ума исходный name
            if( $this->name ) {
                $this->name = Madone_Utilites::getPathName( $this->name );
            }
            
            // Не получилось — сделаем на основе title
            if( ! Madone_Utilites::isPathName( $this->name ) ) {
                $this->name = Madone_Utilites::getPathName( $this->title );
            }
        }

        return $this;
    }
    
    function afterSave( $new ) {
        // Проверим, не продублировали ли мы name кого-то из соседей, если да — добавим к нему свой id
        if( Model_Pages()->filterSiblings( $this )->filter( array( 'id__ne' => $this->id, 'name' => $this->name ) )->count() > 0 ) {
            $this->name .= $this->id;
            $this->hiddenSave();
        }
        
        // Генерируем полный uri страницы
        $path = array_map( create_function( '$i', 'return $i->name;' ), Model_Pages()->filterParents( $this )->embrace( $this )->filterLevel( 2, 0 )->kiOrder()->all() );
        $uri = '/'.join( '/', $path );
        if( $this->uri != $uri ) {
            $this->uri = $uri;
            $this->hiddenSave();
        }
        
		// Пересохраним непосредственные дочерние страницы, чтобы обновились их uri
        foreach( Model_Pages()->filterChildren( $this )->filterLevel( $this->lvl + 1 )->all() as $sub_page ) {
            // Дочерняя страница сама точно так же обновит все свои дочерние страницы, поэтому и работаем только на 1 уровень вниз
            $sub_page->save();
        }
    }
}

?>
