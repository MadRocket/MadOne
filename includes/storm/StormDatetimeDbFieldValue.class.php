<?
/**
    Поле дата+время
*/
class StormDatetimeDbFieldValue extends StormDbFieldValue {
    protected $format = '%Y-%m-%d %H:%M:%S'; // Формат отображения даты, как в `man 3 strftime`
    
	/**
	*	Конструктор.
	*	Принимает параметры от конструктора поля, заполняет всякие штуки
	*/
	function __construct( array $params = array() ) {
		if( $params['format'] ) {
			$this->format = $params['format'];
		}
	}

	// Нормальная работа в качестве поля таблицы
	function &get() {
		return is_null( $this->value ) ? null : $this;
	}
	
	// Нормальная работа в качестве поля таблицы
	function set( $value ) {
		if( is_null( $value ) ) {
			// nothing to do :D
		} else if( is_numeric( $value ) ) {
			$value = (integer) $value;
		} else {
			$time = strtotime( $value );
			if( $time === false ) {
				throw new StormValidationException( "Value '{$value}' is not valid Datetime value" );
			} else {
				$value = $time;
			}
		}
		$this->value = $value;
		return $this;
	}
	
	// Для сохранения значения в БД
	function getForDatabase() {
        return is_null( $this->value ) ? null : strftime( '%Y-%m-%d %H:%M:%S', $this->value );
	}
	
	// Для инициализации значением из БД
	function setFromDatabase( $value ) {
		$this->value = is_null( $value ) ? null : strtotime( $value );
		return $this;
	}

	// Получение значения в виде отдельного объекта упрощенного вида, например перевода в JSON
	function getSimplified() {
    	return is_null( $this->value ) ? null : strftime( $this->format, $this->value );
	}

    /**
     * __get function.
     * 
     * Получение полезных параметров
     *
     * @access private
     * @param mixed $name
     * @return void
     */
    function __get( $name ) {
    	if( ! is_null( $this->value ) ) {
	    	switch( $name ) {
			case 'timestamp':	return $this->value; break;
			
			case 'date':		return strftime("%Y-%m-%d", $this->value ); break;
			case 'time': 		return strftime("%H:%M:%S", $this->value ); break;
			
			case 'year': 		return strftime('%Y', $this->value ); break;
			case 'month': 		return strftime('%m', $this->value ); break;
			case 'day': 		return strftime('%d', $this->value ); break;
			case 'hour': 		return strftime('%H', $this->value ); break;
			case 'minute': 		return strftime('%M', $this->value ); break;
			case 'second': 		return strftime('%S', $this->value ); break;
	    	}
    	}
		return null;
    }
    
    /**
     * format function.
     * 
     * Форматирование даты по формату strftime
     *
     * @access public
     * @param mixed $format
     * @return void
     */
    function format( $format ) {
    	return is_null( $this->value ) ? null : strftime( $format, $this->value );
    }
    
    /**
     * __toString function.
     * 
     * @access private
     * @return void
     */
    function __toString() {
		return is_null( $this->value ) ? '' : strftime( $this->format, $this->value );
    }
}

?>
