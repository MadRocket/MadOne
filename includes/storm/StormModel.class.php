<?
/**
    Абстрактная STORM-модель базы данных.
*/
abstract class StormModel
{
	private static $defaultValueCache;

    /**
     * @var \StormModelMetadata
     */
    public $meta;           // Мета-информация модели

    private $constructed = false;   // Признак сконструированности модели

    /**
     * @var \StormLanguage
     */
	private $language;	// Язык, в которой создан экземпляр модели

    /**
     * @var array
     */
	protected $proxies; // Языковые прокси-объекты

    /**
        Инициализация класса.
        Вызывается автолоадером как только заданный класс где-то упонимается в коде.
        Этот метод играет важную роль в построении связей между зависимыми моделями — регистрирует все модели в ядре
    */
    public static function init( $classname = '' )
    {
        // Регистрируем только реальные классы моделей, абстрактный ни к чему
        if( $classname && $classname != 'StormModel' && $classname != 'StormKiModel' )
        {
            StormCore::getInstance()->checkModel( $classname );
        }
    }

    /**
        Конструктор.
        Вызывается при создании, должен рассовать метаинформацию для модели.
        Не предназначен для переопределения, используй метод construct() для имитации конструктора класса в потомках.
    */
    final function __construct( $params = null )
    {
    	// Запомним текущий язык, будем считать его нашим
    	$this->language = StormCore::getLanguage();
    
        // Генерируем метаинформацию модели
        $this->meta = new StormModelMetadata( get_class( $this ), $this->getDefinition() );

        // Спросим у штормоядра какие модели ссылаются на нас и создадим связанные наборы данных
        foreach( StormCore::getInstance()->getRelatedModels( $this->meta->name ) as $r )
        {
            // Создадим связанный источник данных
            $this->{ $r->related_queryset_name } = new StormFkQuerySet( $r->set_model, $r->key_field_name, $this );
        }

        // Модель сконструтирована
        $this->constructed = true;
        
        // Вызовем пользовательский конструктор, если он есть
        if( method_exists( $this, 'construct' ) )
        {
            $this->construct( $params );
        }

        // Установим значения полей по умолчанию
		foreach( $this->meta->getFields() as $name => $field ) {
			$languages = $field->localized ? StormCore::getAvailableLanguages() : array( $this->language );
			foreach( $languages as $language ) {
				if( ! @array_key_exists( $language->getName(), self::$defaultValueCache[ get_class( $this ) ][ $name ] ) ) {
					self::$defaultValueCache[ get_class( $this ) ][ $name ][ $language->getName() ]  = $field->getDefaultValue( $language );
				}
				if( ! @is_null( self::$defaultValueCache[ get_class( $this ) ][ $name ][ $language->getName() ] ) ) {
					$field->setValue( self::$defaultValueCache[ get_class( $this ) ][ $name ][ $language->getName() ], $language );
				}
			}
			
		}
		
        // Посмотрим, что за параметры пришли к нам и выполним установку значений полей
        if( ! is_array( $params ) )
        {
            if( $params ) $this->meta->setPkValue( $params );
        }
        else
        {
            $this->copyFrom( $params, true );
        }
        
        // Заполним массив проксей, для начала языками
		$this->proxies = array();
        foreach( StormCore::getAvailableLanguages() as $name => $language ) {
			$this->proxies[ mb_strtoupper( $name ) ] = $language;
		}
    }
    
	protected function getLanguageProxy( $name ) {
		if( ! array_key_exists( $name, $this->proxies ) ) {
			throw new StormException( "В модели '{$this->meta->name}' отсутствует языковой прокси '{$name}'." );
		}
		if( ! $this->proxies[ $name ] instanceof StormModelLanguageProxy ) {
			$this->proxies[ $name ] = new StormModelLanguageProxy( $this, $this->proxies[ $name ] );
		}
		return $this->proxies[ $name ];
	}

    /**
        Проверка загруженности данных модели.
        Возвращает true или false.
    */
    function loaded()
    {
        return $this->meta->getPkValue() > 0;
    }

    /**
        Установка значений полей объекта
    */
    final function __set( $name, $value )
    {
        // Объект еще не сконструирован
        if( ! $this->constructed )
        {
            if( $value instanceof StormQuerySet )
            {
                $this->{ $name } = $value;
            }
            else
            {
                throw new StormException( "Can't set model property '{$name}' in construction phase" );
            }
        }
        // Объект сконструирован - выполняем установку значений полей
        else
        {
            // Если поле с указанным именем у нас есть - устанавливаем его значение
            if( $this->meta->fieldExists( $name ) )
            {
                $this->meta->getField( $name )->setValue( $value );
            }
            // Поля нет - exception
            else
            {
                throw new StormException( "There is no '${name}' property in {$this->meta->name} model" );
            }
        }
    }

    /**
        Получение значения поля
    */
    final function & __get( $name )
    {
        // Разрешаем получение имеющихся в объекте полей
        if( property_exists( $this, $name ) )
        {
            return $this->{ $name };
        }

        // Выборка полей модели
        if( $this->meta->fieldExists( $name ) )
        {
        	$return = & $this->meta->getField( $name )->getValue();
            return $return;
        }
        
		// Получение языковых копий
		if( strlen( $name ) == '2' ) {
			try {
				$result = & $this->getLanguageProxy( $name );
				return $result;
			} catch( Exception $e ) {
			}
		}

        // Поля нет - exception
		throw new StormException( "There is no field '$name' in {$this->meta->name} model"  );
    }

    function __isset($name) {
        return $this->meta->fieldExists( $name );
    }
    /**
        Представление объекта в виде строки - generic-овая версия.
        Отображает объект в виде Имя_модели(имя_первичного_ключа: значение_первичного_ключа)
    */
    function __toString()
    {
        return "{$this->meta->name} ({$this->meta->pkname}: ". ( is_null( $this->{ $this->meta->pkname } ) ? 'NULL' : $this->{ $this->meta->pkname } ).')';
    }

    /**
        Получение объявления полей модели.
        Учитывает поля, объявленные пользователям и служебные поля.
        Возвращает массив объявленных полей.
    */
    protected function getDefinition()
    {
        $fields = $this->definition();

        if( method_exists( $this, '_definition' ) )
        {
            $fields = array_merge( $fields, $this->_definition() );
        }

        return $fields;
    }

    /**
        Копирование данных модели из переданного массива
        $source — массив или объект с полями, названными так же, как поля модели
        $copy_pk — флаг, копировать или нет поле primary key
    */
    public function copyFrom( array $source, $copy_pk = false )
    {
        if( ! is_array( $source ) ) throw new StormException( "Cannot read data of {$this->meta->name} model from {$source}" );

        foreach( $this->meta->getFields() as $name => $field )
        {
            // Пропустим ключевое поле
            if( ! $copy_pk && $name == $this->meta->pkname ) continue;
            if( array_key_exists( $name, $source ) ) $field->setValue( $source[ $name ] );
        }

        return $this;
    }
    
    /**
    *	Инициализация значений полей данными, выбранными из БД.
    *	В первую очередь метод предназначен для использования в StormQuerySet::limit.
    */
    public function setValuesFromDatabase( array $source ) {
        foreach( $this->meta->getFields() as $name => $field ) {
			if( array_key_exists( $name, $source ) ) {
				if( $field->localized ) {
					foreach( StormCore::getAvailableLanguages() as $language_name => $language ) {
						$field->setValueFromDatabase( $source[ $name ][ $language_name ], $language );
					}
				} else {
					$field->setValueFromDatabase( $source[ $name ] );
				}
			}
		}
		return $this;
    }

    /**
        Тихое сохранение объекта в БД — не вызываются пользовательские обработчики before и after Save
    */
    public final function hiddenSave()
    {
        return $this->save( false );
    }

    /**
        Сохранение объекта в базе данных
    */
    public final function save( $callBeforeAfterHandlers = true )
    {
        // Вызовем обработчик
        if( method_exists( $this, '_beforeSave' ) ) $this->_beforeSave();
        if( $callBeforeAfterHandlers )
        {
            if( method_exists( $this, 'beforeSave' ) ) $this->beforeSave();
        }

        // Проверим, чтобы все NOT NULL поля кроме StormAutoDbField были заполнены
        foreach( $this->meta->getFields() as $name => $field )
        {
            if( ! $field instanceof StormAutoDbField && ! $field->null && is_null( $field->getValue() ) )
            {
                throw new StormValidationException( "Field '{$name}' cannot be null", $field );
            }
        }

        // Получим имя таблицы
        $table = StormCore::getBackend()->escapeName( StormCore::getMapper()->getModelTable( $this->meta->name ) );

        // Подготовим массив данных модели
        $data = array();
        foreach( $this->meta->getFields() as $name => $field )
        {
            // Пропустим ключевое поле
            if( $name == $this->meta->pkname ) continue;

            // Получим значение поля
			$field->beforeSave();
			
			$languages = $field->localized ? StormCore::getAvailableLanguages() : array( $this->language );
			
			foreach( $languages as $language ) {
				$value = $field->getValueForDatabase( $language );
	
				// Проверим, не наткнулись ли мы на ForeignKey
/*	TODO: удалить, это перенесено в FkDbField.
				if( ! is_null( $value ) && $field instanceof StormFkDbField )
				{
					// Сохраним значение ключевого поля, если оно не сохранено, чтобы все прошло хорошо
					if( ! $value->loaded() ) $value->save();
	
					// Получим значение ключа связанного объекта, и используем его в качестве нашего значения
					$value = $value->meta->getPkValue();
				}

*/	
				// Поместим экранированное значение в массив данных, имя поля тоже экранируем
				$data[ StormCore::getBackend()->escapeName( StormCore::getMapper()->getFieldColumnName( $field, $language ) ) ] = is_null( $value ) ? 'NULL' : "'" . StormCore::getBackend()->escape( $value ) . "'";
				
				// Если поле полнотекстовое — обновляем его данные
				if( $field->fulltext ) {
					if( ! isset( $fulltextProcessor ) ) {
						$fulltextProcessor = new StormFulltextProcessor();
					}
					$data[ StormCore::getBackend()->escapeName( StormCore::getMapper()->getFieldFulltextColumnName( $field, $language ) ) ] =  is_null( $value ) ? 'NULL' : "'" . StormCore::getBackend()->escape( $fulltextProcessor->getBaseForm( $value ) ) . "'";
				}
			}
        }
        
        // Получим имя и значение первичного ключа
        $pkname = $this->meta->pkname;
        $pkvalue = $this->meta->getPkValue();

        // флаг вставки новой записи
        $update_done = false;

        // Значение ключевого поля задано - обновляем БД. Есть вероятность, что ключ задали от балды и обновление ничего не даст
        if( ! is_null( $pkvalue ) )
        {
            // Сконструируем запрос
            $query = "UPDATE {$table} SET " .
                     join( ', ', array_map( create_function( '$k,$v', 'return "$k=$v";' ), array_keys( $data ), $data ) ) .
                     " WHERE " .
                     StormCore::getBackend()->escapeName( $pkname ) . "='" . StormCore::getBackend()->escape( $pkvalue ) . "'";

            // Affected rows устанавливается только если имеет место _именно_ изменение данных, то есть заданы новые значения. Поэтому 0 не означает, что такой записи нет.
            // Единственное, что можно точно сказать — если вернули 1, то обновление точно произошло
            if( StormCore::getBackend()->cursor->execute( $query ) )
            {
                $update_done = true;
            }
            else
            {
                // 0 — или запрос не прошел, или данные не менялись. Проверим наличие записи в таблице.
                StormCore::getBackend()->cursor->execute( "SELECT " . StormCore::getBackend()->escapeName( $pkname ) . " FROM {$table} WHERE " . StormCore::getBackend()->escapeName( $pkname ) . "='" . StormCore::getBackend()->escape( $pkvalue ) . "'" );
                // Если выбрана запись — данные просто не были изменены, все в порядке
                if( count( StormCore::getBackend()->cursor->fetchAll() ) > 0 )
                {
                    $update_done = true;
                }
            }
        }

        // Если обновление прошло — вызываем обработчик и заканчиваем работу
        if( $update_done )
        {
            // Вызовем обработчик
            if( method_exists( $this, '_afterSave' ) ) $this->_afterSave( false );
            if( $callBeforeAfterHandlers )
            {
                if( method_exists( $this, 'afterSave' ) ) $this->afterSave( false );
            }

            return $this;
        }

        // Если мы попали сюда - апдейт либо не планировался, либо не прошел
        // В любом случае, нужно выполнить insert, а значение primary key нужно установить после этого
        if( ! is_null( $pkvalue ) )
        {
            // Добавим pk в массив данных
            $data = array_merge( array( StormCore::getBackend()->escapeName( $pkname ) => "'" . StormCore::getBackend()->escape( $pkvalue ) . "'" ), $data );
        }

        // Сконструируем INSERT-запрос
        $query = "INSERT INTO $table (" .
                 join( ", ", array_keys( $data ) ) . ") VALUES (" .
                 join( ", ", $data )  . ")";

        // выполним запрос
        StormCore::getBackend()->cursor->execute( $query );

        // Если значение PK не было установлено - прочитаем его из last insert id БД
        if( is_null( $pkvalue ) )
        {
            $this->meta->setPkValue( StormCore::getBackend()->getLastInsertId( StormCore::getBackend()->cursor, StormCore::getMapper()->getModelTable( $this->meta->name ), $pkname ) );
        }

        // Вызовем обработчик
        if( method_exists( $this, '_afterSave' ) ) $this->_afterSave( true );
        if( method_exists( $this, 'afterSave' ) ) $this->afterSave( true );

        return $this;
    }

    /**
        Удаление записи из БД
    */
    public final function delete()
    {
        // Вызовем обработчик
        if( method_exists( $this, '_beforeDelete' ) ) $this->_beforeDelete();
        if( method_exists( $this, 'beforeDelete' ) ) $this->beforeDelete();

        // Получим имя и значение первичного ключа
        $pkname = StormCore::getBackend()->escapeName( $this->meta->pkname );
        $pkvalue = $this->meta->getPkValue();

        // Проверим, чтобы ключевое поле было заполнено
        if( is_null( $pkvalue ) ) throw new StormException( "Primary key is not set, cannot delete a record" );

        // Пройдемся по моделям, которые имеют нас в качестве foreign key и выполним очистку

        // Сначала - проверим все связи на not null
        foreach( StormCore::getInstance()->getRelatedModels( $this->meta->name ) as $r )
        {
            // Посмотрим, может ли наше поле быть NULL
            $meta = StormCore::getInstance()->getStormModelMetadata( $r->set_model );
            if( ! $meta->getField( $r->key_field_name )->null )
            {
                // Поле NOT NULL, посмотрим, есть ли записи
                if( $this->{ $r->related_queryset_name }->count() > 0 )
                {
                    throw new StormException( "Cannot delete instance of {$this->meta->name} because it is referenced by {$meta->name} instance with NOT NULL constraint" );
                }
            }
        }

        // Связи проверены, можно смело все удалить и обнулить
        foreach( StormCore::getInstance()->getRelatedModels( $this->meta->name ) as $r ) {
            $this->{ $r->related_queryset_name }->clear();
        }

        // Получим имя таблицы
        $table = StormCore::getBackend()->escapeName( StormCore::getMapper()->getModelTable( $this->meta->name ) );

        // Выполним запрос удаления нашей записи
        StormCore::getBackend()->cursor->execute( "DELETE FROM $table WHERE {$pkname}=%{pk}", array( 'pk'=>$pkvalue ) );

        // Удаление не прошло - exception
        if( ! StormCore::getBackend()->cursor->rowcount > 0 ) {
            throw new StormException( "There is no {$this->meta->name} with {$pkname} = {$pkvalue} in the database" );
        }

        // Выполним действия полей по удалению записи
		foreach($this->meta->getFields() as $key => $field) {
			$field->beforeDelete();
		}

        // Вызовем обработчик
        if( method_exists( $this, '_afterDelete' ) ) $this->_afterDelete();
        if( method_exists( $this, 'afterDelete' ) ) $this->afterDelete();

        return $this;
    }

    /**
        Получение QuerySet-а объектов этой модели
    */
    function getQuerySet()
    {
        return new StormQuerySet( $this->meta->name );
    }

    /**
        Получение данных объекта в виде массива.
        Может быть использовано для ajax-ответов и т.п., поэтому по умолчанию не включает большие по объему поля БД.
        Возвращает массив с ключами — именами полей, значения — строки или null.
        Ссылки на связанные модели заменяются идентификаторами.
    */
    function asArray( $full = false )
    {
        $result = array();

        foreach( $this->meta->getFields() as $name => $field )
        {
            if( $full || ! $field instanceof StormTextDbField )
            {
            	if( $field->localized ) {
            		foreach( StormCore::getAvailableLanguages() as $language ) {
            			$key = mb_strtoupper( $language->getName() );
            			if( ! array_key_exists( $key, $result ) ) {
            				$result[ $key ] = array();
            			}
						$value = $field->asArrayElement( $full, $language );
						if( $value instanceof StormModel ) $value = $value->meta->getPkValue();
						$result[ $key ][ $name ] = $value;
            		}
            	}
				$value = $field->asArrayElement( $full );
				if( $value instanceof StormModel ) $value = $value->meta->getPkValue();
				$result[ $name ] = $value;
            }
        }

        return $result;
    }
    
    function toTraceString() {
    	return get_class( $this )."[ ". $this->meta->getPkValue() ." ]";
    }
}

?>
