<?
/**
 *  Галерея
 */

class MadoneShowcaseSection extends Storm_Model_Tree
{
    static function definition()
    {
        return array
        (
			'title'		=> new Storm_Db_Field_Char( array( 'maxlength' => 255, 'defaults' => array(
            	'ru' => 'Новый раздел',
            	'en' => 'New section',
            	) ) ),
			'name'		=> new Storm_Db_Field_Char( array( 'localized' => false, 'maxlength' => 200, 'index' => true ) ),
			'uri'		=> new Storm_Db_Field_Char( array( 'localized' => false, 'maxlength' => 900, 'index' => true ) ),
			'text'		=> new Storm_Db_Field_Text(),
			'enabled'	=> new Storm_Db_Field_Bool( array( 'localized' => true, 'default' => 0, 'index' => 'nav' ) ),
        );
    }

    function beforeSave()
    {
        // Приводим в порядок URI страницы
        $this->name = trim( $this->name );
        if( ! preg_match( '/^[a-zA-Z0-9_-]+$/', $this->name ) )
        {
            // Попробуем сначала довести до ума исходный name
            if( $this->name )
            {
                $this->name = strtolower( Mad::getPathName( $this->name ) );
            }

            // Не получилось — сделаем на основе title
            if( ! preg_match( '/^[a-zA-Z0-9_-]+$/', $this->name ) )
            {
                $this->name = strtolower( Mad::getPathName( $this->title ) );
            }
        }
    }

    function afterSave( $new )
    {
        // Проверим, не продублировали ли мы name кого-то из соседей, если да — добавим к нему свой id
        if( MadoneShowcaseSections()->filterSiblings( $this )->filter( array( 'id__ne' => $this->id, 'name' => $this->name ) )->count() > 0 )
        {
            $this->name .= $this->id;
            $this->hiddenSave();
        }

        // Генерируем полный uri страницы
        $path = array_map( create_function( '$i', 'return $i->name;' ), MadoneShowcaseSections()->filterParents( $this )->embrace( $this )->filterLevel( 2, 0 )->kiOrder()->all() );
        $uri = '/'.join( '/', $path );
        if( $this->uri != $uri )
        {
            $this->uri = $uri;
            $this->hiddenSave();
        }
    }

    function beforeDelete() {
    	foreach( $this->items->all() as $i ) {
    		$i->delete();
    	}
    }
}

?>
