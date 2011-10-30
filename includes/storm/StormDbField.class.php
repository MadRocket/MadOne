<?

/**
    Абстрактный класс поля базы данных.
    Основа для всех реальных типов полей.
*/

abstract class StormDbField
{
    protected $value = null; // Поле хранения данных
    protected $null = true; // Может ли поле быть null, флаг
    protected $default = null; // Значение по умолчанию
    protected $default_callback = null; // Функция, подставляющая значение по умолчанию
    protected $defaults = null; // Массив локализованных значений по умолчанию
    protected $index = false; // Индексируемое поле
    protected $name = null; // Имя поля в модели
	protected $localized = false; // Локализуемо ли поле
	protected $fulltext = false; // Должно ли поле иметь полнотекстовый индекс
	
	protected $valueClassname = 'StormDbFieldValue'; // Имя класса значений
	protected $valueOptions;
	
	function val() {
		return $this->value;
	}

    /**
        Конструктор
        Заполняет свойства, общие для всех полей
    */
    function __construct( array $params = array() )
    {
        // Прочитаем все параметры циклом
        foreach( $params as $k => $v )
        {
            if( $k == 'value' ) throw new StormException( "Can't set field value through constructor" );
            if( property_exists( $this, $k ) ) $this->$k = $v;
        }
        
		$this->value = new StormDbFieldValueStorage( $this->localized );
		
		$this->valueOptions = $params;
	}

    /**
        Установка значение по умолчанию
    */
    public function getDefaultValue( $language = null )
    {
        if( ! is_null( $this->default ) )
        {
            return $this->default;
        }
		else if( ! is_null( $this->defaults ) && is_array( $this->defaults ) ) {
			if( ! $language ) {
				$language = StormCore::getLanguage();
			}
			if( array_key_exists( $language->getName(), $this->defaults ) ) {
				return $this->defaults[ $language->getName() ];
			}
			list( $first ) = array_keys( $this->defaults );
			return $this->defaults[ $first ];
		}
        else if( ! is_null( $this->default_callback ) )
        {
            return eval( $this->default_callback );
        }

        return null;
    }

    /**
        Интерфейс доступа к приватным переменным извне
    */
    public function __get( $name )
    {
        if( ! property_exists( $this, $name ) ) throw new StormException( "Unknown property '{$name}'" );

        return $this->$name;
    }

    public function __set( $name, $value )
    {
        if( $name == 'name' && is_null( $this->name ) ) $this->name = $value;
    }

    /**
        Установка значения поля
    */
    function setValue( $value, $language = null )
    {
        # Если передано значение null -  проверим флаг this->null
        if( $this->null == false && is_null( $value ) )
        {
            throw new StormValidationException( "Cannot be null", $this );
        }

        if( $this->value->isNull( $language ) ) {
			$this->value->set( new $this->valueClassname( $this->valueOptions ), $language );
        }

		$this->value->get( $language )->set( $value );
    }

    /**
        Получение значения поля
    */
    function & getValue( $language = null ) {
    	$value = $this->value->get( $language );
    	if(is_null( $value )) {
    		return null;
    	}
    	else {
            $val = $value->get();
    		return $val;
    	}
    }


	/**
	*	Установка значения, пришедшего из базы данных. Используется исключительно при инициализации результатов запросов через QuerySet.
	*/
	function setValueFromDatabase( $value, $language = null ) {
        if( $this->value->isNull( $language ) ) {
			$this->value->set( new $this->valueClassname( $this->valueOptions ), $language );
        }
		$this->value->get( $language )->setFromDatabase( $value );
		return true;
	}

    /**
        Получение значения поля для сохранения в базе данных.
        По умолчанию просто возвращает значение, но это можно переопределить.
    */
    function getValueForDatabase( $language = null )
    {
    	$value = $this->value->get( $language );
        return is_null( $value ) ? null : $value->getForDatabase();
    }

    /**
        Представление объекта в строке
    */
    function __toString()
    {
		$value = $this->value->get();
        return is_null( $value ) ? '' : (string)$value->get();
    }

	/**
	 * Получение значения поля в виде элемента массива
	 * @return
	 */
	function asArrayElement( $full = false, $language = null ) {
    	$value = $this->value->get( $language );
        return is_null( $value ) ? null : $value->getSimplified( ! $full );
	}

    /**
        Получение значения, отформатированного для запроса
    */
    static public function getCheckOpValue( $value )
    {
        return $value;
    }

	/**
	 * Набор действий, которые необходимо выполнить перед тем, как информация поля будет сохранена в базу
	 * @return
	 */
	public function beforeSave() {
		$this->value->trigger( 'beforeSave' );
	}

	/**
	 * Набор действий, которые необходимо выполнить перед тем, как информация поля будет удалена из базы
	 * @return
	 */
	public function beforeDelete() {
		$this->value->trigger( 'beforeDelete' );
	}
	
	/**
	 * Набор действий, которые необходимо выполнить при синхронизации модели и БД. Можно проверить доступ к файлам, например.
	 */
	public function beforeSync() {
		// Создадим объект значения поля
		$valueInstance = new $this->valueClassname( $this->valueOptions );
		if( method_exists( $valueInstance, 'beforeSync' ) ) {
			$valueInstance->beforeSync();
		}
	}

	/**
    * Получение переменной, используемой при создании поля в БД (переменные вида %{maxlen} и прочее
	*/
	public function getCreationVariable( $name ) {
        if( property_exists( $this, $name ) ) {
            return $this->$name;
        }
        return null;
	}
}

?>
