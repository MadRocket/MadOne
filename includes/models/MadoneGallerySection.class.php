<?
/**
 *  Галерея
 */

class MadoneGallerySection extends StormKiModel
{
    static function definition()
    {
        return array
        (
			'title'    => new StormCharDbField( array( 'maxlength' => 255, 'defaults' => array(
            	'ru' => 'Новый раздел',
            	'en' => 'New section',
            	) ) ),
			'name'     => new StormCharDbField( array( 'localized' => false, 'maxlength' => 100, 'index' => true ) ),
			'uri'      => new StormCharDbField( array( 'localized' => false, 'maxlength' => 600, 'index' => true ) ),
			'text'	   => new StormTextDbField(),
			'enabled'  => new StormBoolDbField( array( 'localized' => false, 'default' => 0, 'index' => 'nav' ) ),
        );
    }

    public function getFirstImage() {
        return $this->images->order('position')->first();
    }

    function beforeSave()
    {
    	if( ! $this->title ) {
    		throw new Exception( 'Не указано название раздела.' );
    	}
		else {
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
	}

    function afterSave( $new )
    {
        // Проверим, не продублировали ли мы name кого-то из соседей, если да — добавим к нему свой id
        if( MadoneGallerySections()->filterSiblings( $this )->filter( array( 'id__ne' => $this->id, 'name' => $this->name ) )->count() > 0 )
        {
            $this->name .= $this->id;
            $this->hiddenSave();
        }

        // Генерируем полный uri страницы
        $path = array_map( create_function( '$i', 'return $i->name;' ), MadoneGallerySections()->filterParents( $this )->embrace( $this )->filterLevel( 2, 0 )->kiOrder()->all() );
        $uri = '/'.join( '/', $path );
        if( $this->uri != $uri )
        {
            $this->uri = $uri;
            $this->hiddenSave();
        }
    }
    
    function beforeDelete() {
    	foreach( $this->images->all() as $i ) {
    		$i->delete();
    	}
    }
}

?>
