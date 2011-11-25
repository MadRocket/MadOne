<?
/**
*	Класс-посредник для переключения на лету языка полей экземпляра модели.
*	Копирует поведение модели, включая в вызовы язык, или типа того.
*/

class Storm_Model_Languageproxy {
	protected $instance;
	protected $language;
	
	function __construct( $instance, $language ) {
		$this->instance = $instance;
		$this->language = $language;
	}
	
	function & __get( $name ) {
        if( $this->instance->meta->fieldExists( $name ) ) {
        	$result = & $this->instance->meta->getField( $name )->getValue( $this->language );
            return $result;
        }
		throw new Storm_Exception( "В модели '{$this->instance->meta->name}' нет поля с именем '{$name}'." );
    }
    
    function __set( $name, $value ) {
		if( $this->instance->meta->fieldExists( $name ) ) {
			return $this->instance->meta->getField( $name )->setValue( $value, $this->language );
		}
		throw new Storm_Exception( "В модели '{$this->instance->meta->name}' нет поля с именем '{$name}'." );
    }
}

?>