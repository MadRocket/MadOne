<?
/**
    Поле типа varchar
*/
class StormEnumDbFieldValue extends StormDbFieldValue {
    protected $values;

	/**
	*	Конструктор.
	*	Принимает параметры от конструктора поля, заполняет всякие штуки
	*/
	function __construct( array $params = array() ) {
		$this->values = $params['values'];
	}

	// Нормальная работа в качестве поля таблицы
	function get() {
		return is_null( $this->value ) ? null : $this;
	}
	
	// Нормальная работа в качестве поля таблицы
	function set( $value ) {
        if( ! is_null( $value ) && array_search( $value, $this->values ) === false ) {
            throw new StormException( "Недопустимое значение перечисляемого поля: '{$value}'." );
        }
        return parent::set( $value );
	}

	// Получение значения в виде отдельного объекта упрощенного вида, например перевода в JSON
	function getSimplified( $short ) {
		return $short ? $this->value : (object) array( 'value' => $this->value, 'values' => $this->values );
	}

    function __get( $name ) {
    	if( ! is_null( $this->value ) ) {
	    	switch( $name ) {
			case 'values':		return $this->values; break;
			case 'value':		return $this->value; break;
	    	}
    	}
		return null;
    }
    
    function __toString() {
		return $this->value;
    }
}

?>
