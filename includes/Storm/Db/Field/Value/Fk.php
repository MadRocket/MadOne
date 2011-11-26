<?
/**
	Поле — внешний ключ
*/
class Storm_Db_Field_Value_Fk extends Storm_Db_Field_Value {
	protected $model;			// Имя класса модели, на которую ссылается поле
	protected $instance;		// Инициализированный объект модели, соответствующий значению поля
	
	protected function syncValue() {
		if( $this->instance ) {
			$this->value = $this->instance->meta->getPkValue();
		}
	}
    
	/**
	*	Конструктор.
	*	Принимает параметры от конструктора поля, заполняет всякие штуки
	*/
	function __construct( array $params = array() ) {
		$this->model = $params['model'];
		$this->instance = null;
	}

	// Нормальная работа в качестве поля таблицы
	function & get() {
        // Проверим наличие instance
        if( ! $this->instance && ! is_null( $this->value ) ) {
            $this->instance = Storm_Queryset( $this->model )->get( $this->value );
        }
        return $this->instance;
	}
	
	// Нормальная работа в качестве поля таблицы
	function set( $value ) {
    	// Проверим значение
		if( is_object( $value ) ) {
			if( ! $value instanceof $this->model ) {
				throw new Storm_Exception_Validation( "Cannot use object of '". get_class( $value ) ."' class as a  foreign key to '{$this->model}' value" );
			}
			$this->instance = $value;
			return parent::set( $value->meta->getPkValue() );
		}
		
        if( is_numeric( $value ) ) {
        	if( $this->value != (int) $value ) {
				$this->instance = null;
			}
			return parent::set( $value );
        }
		
        if( is_null( $value ) ) {
            $this->instance = null;
			return parent::set( null );
        }
		
		throw new Storm_Exception_Validation( "Cannot use '{$value}' as a foreign key field value" );
	}
	
	// Для сохранения значения в БД
	function getForDatabase() {
		if( $this->instance && ! $this->instance->loaded() ) {
			$this->instance->save();
			$this->value = $this->instance->meta->getPkValue();
		}
		$this->syncValue();
        return parent::getForDatabase();
	}

	// Для инициализации значением из БД
	function setFromDatabase( $value ) {
		$this->instance = null;
		return parent::setFromDatabase( $value );
	}
	
	// Получение значения в виде отдельного объекта упрощенного вида, например перевода в JSON
	function getSimplified() {
		// Проверим значение
		if( $this->instance ) {
			// Если загружена модель, то вернем ее представление в виде массива
			return $this->instance->asArray( true );
		} else {
			// Иначе вернем только значение внешнего ключа
			return $this->value;
		}
	}
	
    function __toString() {
		return (string) $this->instance;
    }
}

?>
