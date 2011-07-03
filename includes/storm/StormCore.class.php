<?

/**
    Штормоядро!
    Штуки, нужные для слаженной работы всех механизмов — связи между моделями, настройки соединения с базой и прочего.
    Singleton, экземпляр можно получить вызовом StormCore::getInstance();
*/

class StormCore
{
    private static $instance = null;    // Для реализации singleton-а
    private $utilities_registered = false;

    private $related;  // массив связанных полей моделей
    private $metadata = array(); // массив метаданных моделей

    private $models; // массив зарегистрированных моделей
    private $querysets; // массив названий querysetов для моделей, ключ - имя модели

    private $backend;
    /**
     * @var StormDbMapper
     */
    private $mapper;
    
    private $language;	// текущий язык, включенный в ядре. Влияет на выборку и сохранение данных.
    private $languages; // массив доступных языков
    
    /************
        Общедоступные штуки.
     *********************/

    /**
        Синхронизация базы данных
    */
    static public function sync()
    {
        return self::getInstance()->syncdb();
    }

    /**
     * Доступ к объекту-синглтону
     * @static
     * @return StormCore
     */
    public static function getInstance()
    {
        if( self::$instance == null )
        {
            self::$instance = new StormCore();
        }

        return self::$instance;
    }

    /**
        Утилита — получение текущего бэкенда БД
    */
    public static function getBackend()
    {
        return self::getInstance()->backend;
    }

    /**
        Получение текущего маппера БД
    */
    public static function getMapper()
    {
        return self::getInstance()->mapper;
    }

    /**
    *	Установка текущего языка Storm.
    *	Возвращает true или выбрасывает исключение.
    */
    public static function setLanguage( StormLanguage $language ) {
    	if( $language instanceof StormLanguage ) {
    		$language = $language->getName();
    	}
		if( array_key_exists( $language, self::getInstance()->languages ) ) {
			self::getInstance()->language = self::getInstance()->languages[ $language ];
			return true;
		}
		throw new StormException( "Неизвестный язык '{$language}'" );
    }
    
    /**
     * Получение текущего языка Storm.
     * @static
     * @throws StormException
     * @param null $name
     * @return StormLanguage
     */
    public static function getLanguage( $name = null ) {
    	if( ! $name ) {
			return self::getInstance()->language;
		}
		if( array_key_exists( $name, self::getInstance()->languages ) ) {
			return self::getInstance()->languages[ $name ];
		}
		throw new StormException( "Неизвестный язык '{$name}'" );
    }
    
    /**
     * Получение списка доступных языков Storm. Возвращает массив StormLanguage.
     * @static
     * @return array StormLanguage
     */
	public static function getAvailableLanguages() {
		return self::getInstance()->languages;
	}

    /**
        Функция старта класса
    */
    public static function init()
    {
        // Зарегистрируем все известные из конфига модели
        foreach( StormConfig::$models as $def )
        {
            if( is_array( $def ) )
            {
                $classname = $def[0];
                $querysetname = $def[1];
            }
            else
            {
                $classname = $def;
                $querysetname = "{$def}s";
            }

            // Запомним имя модели и querysetа
            self::getInstance()->models[] = $classname;
            self::getInstance()->querysets[ $classname ] = $querysetname;
        }

        foreach( self::getInstance()->models as $classname )
        {
            // Получим связи типа один-ко-многим, и сложим их в наше поле related.
            // Ключи в этом поле — модель, содержащая записи-ключи (one)
            foreach( self::getInstance()->getStormOneToManyRelations( $classname ) as $relation )
            {
                self::getInstance()->related[ $relation->key_model ][] = $relation;
            }
        }
    }

    /*******************************
    Системные методы — обеспечивают работу Storm как единого целого
    ********************************/

    /**
        Приватный конструктор — извне невозможно сконструировать экземпляр объекта.
    */
    private function __construct()
    {
        // Сделаем пустой массив related
        $this->related = array();

		// Починим список локалей, если он не указан        
		if( ! is_array( StormConfig::$locales ) ) {
			StormConfig::$locales = array( 'ru_RU.UTF-8' );
		}
    	// Заполним список языков, выберем первый в качестве текущего
    	$this->languages = array();
    	foreach( StormConfig::$locales as $locale ) {
    		$language = new StormLanguage( $locale );
    		$this->languages[ $language->getName() ] = $language;
    		if( ! $this->language ) {
    			$this->language = $language;
    		}
    	}

        // Получим backend
        $this->backend =  new StormConfig::$db_backend( array
        (
            'host'      => StormConfig::$db_host,
            'port'      => StormConfig::$db_port,
            'name'      => StormConfig::$db_name,
            'user'      => StormConfig::$db_user,
            'password'  => StormConfig::$db_password,
            'charset'   => StormConfig::$db_charset,
        ) );

        // Получим mapper
        $this->mapper =  new StormConfig::$db_mapper();
    }

    /**
        Приватная функция клонирования — клонирование недоступно извне
    */
    private function __clone()
    {

    }

    /**
        Проверка наличия модели в списках штормоядра
    */
    function checkModel( $classname )
    {
        if( ! in_array( $classname, $this->models ) )
        {
            throw new StormException( "'{$classname}' model is not known by StormCore. Please add it to the \$models property of StormConfig class." );
        }
    }

    /**
        Получение метаданных модели
        Возвращает массив метаданных так, как он выглядит в свежесозданном экземпляре модели
    */
    /**
     * @param $classname
     * @return StormModelMetadata
     */
    public function getStormModelMetadata( $classname )
    {
        // Проверим, нет ли у нас готовой копии метаданных для этой модели
        if( ! array_key_exists($classname, $this->metadata) || ! $this->metadata[ $classname ] )
        {
            //Данных нет, их нужно получить
            $instance = new $classname();

            $this->metadata[ $classname ] = $instance->meta;
        }

        return $this->metadata[ $classname ];
    }

    /**
        Получение списка связей типа один-ко-многим, определенных заданной моделью
        Возвращает массив объектов StormOneToManyRelation
    */
    private function getStormOneToManyRelations( $classname )
    {
        // Получим поля модели
        $definition = call_user_func( array( $classname, 'definition' ) );

        $relations = array();

        // Идем по полям циклом
        foreach( $definition as $fieldname => $fieldobject )
        {
            // Поле — ForeignKey?
            if( $fieldobject instanceof StormFkDbField )
            {
                $relations[] = new StormOneToManyRelation( $fieldobject->model, $fieldname, $classname, $fieldobject->related );
            }
        }

        return $relations;
    }

    /**
        Получение списка внешних связей модели.
        Возвращает массив, каждый элемент содержит ключи model, field и related.
        Возвращает пустой массив, если связей нет.
    */
    function getRelatedModels( $class )
    {
        if( array_key_exists($class, $this->related) && is_array( $this->related[ $class ] ) && count( $this->related[ $class ] ) > 0 )
        {
            return $this->related[ $class ];
        }

        return array();
    }

    /**
        Получение параметров для внешних вызовов
    */
    public function __get( $name )
    {
        if( property_exists( $this, $name ) )
        {
            return $this->$name;
        }
    }

    /**
        Синхронизация базы данных
        Принимает массив имен моделей, которые следует синхронизировать
        Выполняется со следующими ограничениями:
            1. Отсутствующие модели создаются.
            2. Отсутствующие поля существующих моделей создаются.
            3. Тип полей не проверяется вообще, нужно пересоздать поле - удаляй его из БД и синхронизируй еще раз.
            4. Индексы создаются автоматически, но не удаляются.
    */
    private function syncdb()
    {
    	// Прогоним событие «перед синхронизацией модели»
		$this->triggerFieldHandler( 'beforeSync' );
    
        // Получим список имеющихся в БД таблиц
        $tables = $this->mapper->getTableList( $this->backend->cursor );

        // Пройдемся по списку моделей и создадим их
        foreach( $this->models as $model )
        {
            $table = $this->mapper->getModelTable( $model );
            
            // Проверим наличие таблицы
            if( array_search( $table, $tables ) !== false )
            {
                // Получим колонки базы данных (которые уже есть) и колонки модели (которые должны быть)
                $dbColumns = $this->mapper->getColumnList( $this->backend->cursor, $model );
                $modelColumns = $this->mapper->getModelColumnList( $model );
                
                // Сверим колонки и создадим отсутствующие
                foreach( $modelColumns as $column => $definitionSql ) {
                	if( ! in_array( $column, $dbColumns ) ) {
                		$this->backend->cursor->execute( $definitionSql );
                	}
                }
			}
            else
            {
                // Таблицы нет - создадим её
                $this->backend->cursor->execute( $this->mapper->getTableCreationSql( $model ) );
			}
		}

        // Пройдемся по списку моделей и создадим индексы
        foreach( $this->models as $model )
        {
            // Получим желаемые и имеющиеся индексы
            $desired = $this->mapper->getModelIndexes( $model );
            $existing = $this->mapper->getIndexList( $this->backend->cursor, $model );

            // Сверяем все циклом
            foreach( $desired as $idx )
            {
                // Несуществующее создаем
                if( ! array_key_exists( $idx->getName(), $existing ) )
                {
                    $this->backend->cursor->execute( $this->mapper->getIndexCreationSql( $idx ) );
                }
            }
        }
    }
    
	function triggerFieldHandler( $handler ) {
		foreach( $this->models as $model ) {
			foreach( $this->getStormModelMetadata( $model )->getFields() as $field ) {
    			$field->beforeSync();
			}
		}
    }

    /**
        Создание функций для быстрого доступа к моделям, StormQueryCheck-ам и прочим прелестям.
        В этом методе все является читерством и извращением в той или иной степени, но что делать? Унылый PHP уныл.
    */
    function registerUtilities()
    {
        if( ! $this->utilities_registered )
        {
            // Быстрое создание StormQC
            function Q( $params )           { return new StormQC( $params ); }
            function QOR( $left, $right )   { return new StormQCOR( $left, $right ); }
            function QAND( $left, $right )  { return new StormQCAND( $left, $right ); }
            function QNOT( $op )            { return new StormQCNOT( $op ); }

            // Быстрое создание StormQuerySet
            function StormQuerySet( $model ) { return ( class_exists( $model ) && is_subclass_of( $model, 'StormKiModel' ) ) ? new StormKiQuerySet( $model ) : new StormQuerySet( $model ); }

            // Утилиты для получения моделей и их StormQuerySet-ов
            $code = '';
            foreach( $this->querysets as $model => $set )
            {
                // Определим класс StormQuerySet-а для этой модели
                $queryset = ( class_exists( $model ) && is_subclass_of( $model, 'StormKiModel' ) ) ? 'StormKiQuerySet' : 'StormQuerySet';

                $code .=
                "
                function {$model}( \$params = null )
                {
                    return \$params ? new {$model}( \$params ) : new {$model}();
                }

                function {$set}( \$params = null )
                {
                    \$qs = new {$queryset}( '{$model}' );
                    return \$params ? \$qs->filter( \$params ) :  \$qs;
                }
                ";
            }

            eval( $code ); // :D

            $this->utilities_registered = true;
        }
    }
}

?>
