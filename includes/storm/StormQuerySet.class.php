<?

/**
 * StormQuerySet class.
 * Запрашивалка данных модели.
 */
class StormQuerySet
{
    protected static $cache = array();

    /**
     * @var StormModel
     */
    protected $model;   // Модель, по которой выполняем выборки
	
    protected $qc;  // Условия выборки
    protected $distinct = false; // Признак distinct-выборки
    protected $order = array();  // Поля сортировки, каждый элемент — массив с ключами field и order (asc или desc)
    protected $follow = 0;  // Проследование вглубь модели при выборках
    
    protected $skipFields = array(); // массив имен полей, данные которых не следует выбирать. TODO: переделать в отложеные поля, загружающиеся по обращению из модели.
    
    protected $onlyFields = array(); // массив имен полей, данные которых и только их будут выбраны. Из этого массива можно исключить поля методом skip()

    /**
        Конструктор.
        $model -  имя модели, для которой конструируется StormQuerySet.
    */
    function __construct( $model )
    {
        // Проверим имя модели
        if( ! ( class_exists( $model ) &&  is_subclass_of( $model, 'StormModel' ) ) )
        {
            throw new StormException( "$model is not a Storm model" );
        }

        // Установим имя своей модели
        $this->model = $model;

        // Условия по умолчанию — выбираем все записи в базе
        $this->qc = new StormQCALL();
    }

    /**
        Получение копии StormQuerySet-а
    */
    function __clone()
    {
        // Клонируем проверки, остальное склонируется само
        $this->qc = clone $this->qc;
    }

    /**
        Объединение двух массивов объектов StormQueryJoin.
        Проверяет, чтобы не было повторов по алиасам. Первый массив — ведущий.
        Возвращает объединенный массив.
    */
    protected function mergeJoins( $joins1 = array(), $joins2 = array() )
    {
        if( array_key_exists('last', $joins1) && $joins1['last'] ) unset( $joins1['last'] );
        if( array_key_exists('last', $joins2) && $joins2['last'] ) unset( $joins2['last'] );

        // Замаппируем имеющиеся join-ы
        $existing = array_map( create_function( '$j', 'return $j->alias;' ), $joins1 );

        foreach( $joins2 as $j )
        {
            if( ! in_array( $j->alias, $existing ) )
            {
                $existing[] = $j->alias;
                $joins1[] = $j;
            }
        }

        return $joins1;
    }

    /**
      Функция генерации SQL-запроса выборки данных
      Аргументы:
        $type - типа запроса, 'values' - значения, 'count' - подсчет количества записей
        $extra - массив, дополнительные данные для указанного типа запроса,
                 для 'values' это 'limit' и 'offset'
      Возвращает массив ( sql=>'...', params=>array(...), fields=>'...' )
        sql - текст запроса
        params - параметры, которыми следует заполнить sql для выполнения
        fields - массив соответствия alias-ов полей, которые будут выбраны запросом
    */
    function getStormQueryParts( $type = 'values', array $extra = array())
    {
        /* Так как follow подключает связанные таблицы, которые могут ограничить нашу выборку, то использование
        всех join-ов обязательно в обоих типах запросов — values и count */

        // Выберем данные полей запроса
        $fdata = $this->getStormQueryEntities();

        // Вытащим из частей запроса массив Join-ов
        $Joins = array();
        foreach( $fdata as $part )
        {
            if( $part->join ) $Joins[] = $part->join;
        }

        // Преобразуем условия выборки в SQL форму
        $qc = $this->qc->getStormQueryParts( $this->model );

        // Добавим join-ы
        $Joins = $this->mergeJoins( $Joins, $qc['joins'] );

        // Часть where
        $Where = $qc['where'] ? "WHERE {$qc['where']}" : '';

        // Главная таблица и ее алиас
        $Alias = StormCore::getMapper()->getModelAlias( $this->model );
        $Table = StormCore::getMapper()->getModelTable( $this->model );

        // Флаг distinct-выборки
        $Distinct = $this->distinct ? 'DISTINCT' : '';

        // Поля выборки (будут инициализированы позднее)
        $Fields = '';

        // Сортировка
        $Order = '';
        
        // Ограничение
        $Limit = '';

        // Дальше запросы отличаются: для values нужно сделать выборку полей, а для count — нет :)
        switch( $type)
        {
            // Запрос — получение данных
            case 'values':
                // Подготовим список полей
                $Fields = join( ', ', array_map( create_function( '$i', 'return join( ", ", $i->fields );' ), $fdata ) );
                
                
				if( $qc['expressions'] ) {
					$entities = array();
					foreach( $qc['expressions'] as $alias => $expression ) {
						$entities[] = "{$expression} as {$alias}";
					}
					
					$Fields .= ', '.join( ', ', $entities );
				}

                // Инициализируем limit и offset, если они еще не указаны
                if( ! array_key_exists( 'limit', $extra ) ) $extra['limit'] = 0;
                if( ! array_key_exists( 'offset', $extra ) ) $extra['offset'] = 0;

                // Получим limit-строку
                $Limit = StormCore::getMapper()->getLimitOffsetSql( $extra['limit'], $extra['offset'] );

                // Определимся с Order
                if( count( $this->order ) > 0 )
                {
                    $orders = array();

                    // Пройдемся по полям, которые переданы для упорядочивания по ним результата
                    foreach( $this->order as $o )
                    {
                        // Сложные поля разберем
                        if( mb_strpos( $o['field'], '__', 0, 'utf-8' ) !== false )
                        {
                            $cf = new StormComplexField( $this->model, $o['field'] );
                            $orders[] = "{$cf->field} {$o['order']}";
                            $Joins = $this->mergeJoins( $Joins, $cf->joins );
                        }
                        elseif( mb_strpos( $o['field'], '()', 0, 'utf-8' ) !== false ) {
                        	// Функции
                        	$orders[] = "{$o['field']}";
                        }
                        elseif( $o['field'][0] == '-' ) {
                        	$orders[] = mb_substr( $o['field'], 1, mb_strlen( $o['field'], 'utf-8' ), 'utf-8' )." {$o['order']}";
                        }
                        else
                        {
                            // Простые добавим как есть
							$field = StormCore::getInstance()->getStormModelMetadata( $this->model )->getField( $o['field'] );
                            $orders[] = "{$Alias}.". StormCore::getMapper()->getFieldColumnName( $field ) ." {$o['order']}";
                        }
                    }

                    $Order = count( $orders ) ? "ORDER BY ".join( ', ', $orders ) : '';
                }
            break;

            case 'count':
                $Fields = 'count(*) as count';
            break;

            default: throw new StormException( "Unknown query type '{$type}'" );
        }

        // Соединим join-ы
        $Joins = join( ' ', $Joins );

        // Сгенерируем текст запроса
        $sql = "SELECT $Distinct $Fields FROM $Table AS $Alias $Joins $Where $Order $Limit";

        return array( 'sql' => $sql, 'params' => $qc['params'], 'fields' => $fdata );
    }

    /**
        Упорядочивание записей
        Принимает массив или строку, типа
        Pieces()->orderAsc( 'name' )
        Pieces()->orderDesc( array( 'name', 'value' ) )
        Pieces()->order( 'colour__name' )   — комплексные поля тоже позволительны, да :3 о join-ах позаботится штормоядро
        order эквивалентна orderAsc
    */
    protected function _order( array $fields, $order )
    {
        foreach( $fields as $name )
        {
            $this->order[] = array( 'field' => $name, 'order' => $order );
        }
    }

    /**
        Сортировка по возрастанию
    */
    function order( $params )
    {
        return $this->orderAsc( $params );
    }

    /**
        Сортировка по возрастанию
    */
    function orderAsc( $params )
    {
        $next = clone $this;
        $next->_order( is_array( $params ) ? $params : array( $params ), 'asc' );
        return $next;
    }

    /**
        Сортировка по убыванию
    */
    function orderDesc( $params )
    {
        $next = clone $this;
        $next->_order( is_array( $params ) ? $params : array( $params ), 'desc' );
        return $next;
    }
    
    function orderRand() {
    	$next = clone $this;
        $next->_order( array("RAND()"), null );
        return $next;
    }
    
    function orderRelevant() {
    	$next = clone $this;
        $next->_order( array( "-fulltext_relevance" ), 'desc' );
        return $next;
    }

    // Переключение проследования
    function follow( $depth )
    {
        $next = clone $this;
        $next->follow = $depth;
        return $next;
    }

    // Пропуск загрузки полей
    function skip( $fields )
    {
    	if( ! is_array( $fields ) ) {
    		$fields = array( $fields );
    	}
    
        $next = clone $this;
        $next->skipFields = array_merge( $next->skipFields, $fields );
        return $next;
    }
    
    // Выборка определенных полей
    function only( $fields )
    {
    	if( ! is_array( $fields ) ) {
    		$fields = array( $fields );
    	}
    
        $next = clone $this;
        $next->onlyFields = array_merge( $next->onlyFields, $fields );
        return $next;
    }    

    // запрос записей по параметрам
    private function query( $params, $embrace = false )
    {
        // Клонируем себя, чтобы остаться неизменным
        $next = clone $this;

        // Добавляем параметров следующему в цепочке StormQuerySet-у
        $qc = $params instanceof StormQC ? $params : new StormQC( $params );
        $next->qc = $next->qc ? ( $embrace ? QOR( $next->qc, $qc ) : QAND( $next->qc, $qc ) ) : $qc;

        // Возвращаем следующий объект
        return $next;
    }

    // Фильтрация записей
    function filter( $params )
    {
        return $this->query( $params, false );
    }

    // Добавление записей, ограниченых переданными условиями.
    // Отличается от filter условием OR вместо AND.
    function embrace( $params )
    {
        return $this->query( $params, true );
    }

    /**
        Получение списка сущностей, которые будут участвовать в текущем запросе.
        Возвращает массив объектов StormQueryEntity в нужном порядке
    */
    protected function getStormQueryEntities( $model = null, $depth = 0, $path = array(), $supalias = null, $null = false )
    {
        // Проверим, не ушли ли мы уже дальше, чем следует
        if( $depth > $this->follow ) return array();

        $core = StormCore::getInstance();

        // Тут будем накапливать результат!
        $entities = array();
        $alias = null;

        // Получим метаданные текущей обрабатываемой модели
        $meta = $core->getStormModelMetadata( $model ? $model : $this->model );

        // Следующая часть запроса
        $part = new StormQueryEntity( $model ? $model : $this->model );

        if( $model )
        {
            // Мы в глубине
            $part->path = $path;

            $supfield = $path[ count( $path ) - 1 ];
            $alias = "{$supalias}__{$supfield}";

            $part->join = new StormQueryJoin
            (
                $null ? 'LEFT' : 'INNER',
                $core->getMapper()->getModelTable( $model ),
                $alias,
                "{$supalias}.{$supfield} = {$alias}.{$meta->getPkname()}"
            );


            foreach( $meta->getFields() as $n => $v )
            {
                if( ! $v instanceof StormFkDbField || $depth == $this->follow )
                {
                	if( $v->localized ) {
                		foreach( StormCore::getAvailableLanguages() as $language ) {
							$falias = join( '__', array_merge( array( $this->model ), $path, array( $n ) ) ) . "__{$language->getName()}";
							$part->fields[] = "{$alias}." . StormCore::getMapper()->getFieldColumnName( $v, $language ) . " AS $falias";
							$part->aliases[ $n ][ $language->getName() ] = "$falias";
                		}
                	} else {
						$falias = join( '__', array_merge( array( $this->model ), $path, array( $n ) ) );
						$part->fields[] = "{$alias}." . StormCore::getMapper()->getFieldColumnName( $v ) . " AS $falias";
						$part->aliases[ $n ] = "$falias";
					}
                }
            }
        }
        else
        {
            // Мы в начале, разбираем основную сущность запроса
            $alias = $core->getMapper()->getModelAlias( $this->model );

            foreach( $meta->getFields() as $n => $v )
            {
            	// Выбираем поле только в тех случаях, если массив определенных к выборке полей пуст, 
            	// либо поле явно определено к выборке и его имя есть в массиве onlyFields, 
            	// либо это первичный ключ
            	if( empty($this->onlyFields) || in_array($n, $this->onlyFields) || $v instanceof StormAutoDbField ) {

	            	// Пропустим поля, которые не нужно загружать TODO: сделать defered
	            	if( in_array( $n, $this->skipFields ) ) {
	            		continue;
	            	}
	            	
	            
	                if( ! $v instanceof StormFkDbField || $depth == $this->follow )
	                {
	                	if( $v->localized ) {
	                		foreach( StormCore::getAvailableLanguages() as $language ) {
								$falias = "{$this->model}__{$n}__{$language->getName()}";
								$part->fields[] = "{$alias}." . StormCore::getMapper()->getFieldColumnName( $v, $language ) . " AS $falias";
								$part->aliases[$n][ $language->getName() ] = $falias;
	                		}
	                	} else {
							$falias = "{$this->model}__{$n}";
							$part->fields[] = "{$alias}." . StormCore::getMapper()->getFieldColumnName( $v ) . " AS $falias";
							$part->aliases[$n] = $falias;
	                	}
	                }
            	}
            }
        }

        $entities[] = $part;

        // Идем по полям текущей модели дальше в рекурсию
        foreach( $meta->getFields() as $n => $v )
        {
            if( $v instanceof StormFkDbField )
            {
                $more_entities = $this->getStormQueryEntities( $v->model, $depth + 1, array_merge( $path, array( $n ) ), $alias, $v->null || $null );
                $entities = array_merge( $entities, $more_entities );
            }
        }

        return $entities;
    }

    /**
     * Выборка указанного количества записей.
     * Это конечный метод, на котором работают остальные методы, выбирающие данные, такие как all() и count()
     * $limit - количество записей, null - не ограничено
     * $offset - смещение в наборе записей, null - не указывать смещение, 0
     */
    function limit( $limit = null, $offset = null )
    {
        // Получим ссылку на backend STORM
        $backend = StormCore::getBackend();

        // Получим sql запроса
        $query = $this->getStormQueryParts( 'values', array( 'limit' => $limit, 'offset' => $offset ) );

        // Выполним запрос
        $backend->cursor->execute( $query['sql'], $query['params'] );

        // Пройдемся по результатам запроса, и сложим все в массив объектов
        $results = array();
		foreach( $backend->cursor->fetchAll() as $row )
        {
            // Создадим для каждой записи объект
            $record = null;

            // Идем по полям запроса
            foreach( $query['fields'] as $f )
            {
                // Собираем данные для наполнения очередного экземпляра модели
                $data = array();
                foreach( $f->aliases as $field => $alias )
                {
                	if( is_array( $alias ) ) {
                		foreach( $alias as $language_name => $field_alias ) {
                			$data[$field][ $language_name ] = $row[ $field_alias ];
                		}
                	} else {
	                    $data[$field] = $row[$alias];
	                }
                }

                // Тут интересный момент — проследование вглубь по моделям — FK и создание их объектов
                if( $record )
                {
                    $dst = & $record;
                    foreach( $f->path as $next )
                    {
                        $dst = & $dst->$next;
                        if( $dst == null ) break;
                    }

                    // Создаем только если PK != NULL, а такое может быть при FK IS NULL
                    $meta = StormCore::getInstance()->getStormModelMetadata( $f->model );
                    if( $data[ $meta->getPkname() ] )
                    {
                        $dst = new $f->model();
                        $dst->setValuesFromDatabase( $data );
                    }
                }
                else
                {
                    // Стандартная ситуация — объект основной модели
                    $record = new $this->model();
					$record->setValuesFromDatabase( $data );
                }
            }

            $results[] = $record;
        }

        return $results;
    }

    // Выборка всех записей
    function all()
    {
        # Просто limit без ограничений :D
        return $this->limit();
    }

    // Получение первой записи запроса
    function first()
    {
        $records = $this->limit( 1, 0 );
        return array_key_exists(0, $records) && $records[0] ? $records[0] : null;
    }

    /* Подсчет количества объектов */
    function count()
    {
        // Получим sql запроса
        $query = $this->getStormQueryParts( 'count' );

        // Получим ссылку на backend STORM
        $backend = StormCore::getBackend();

        // Выполним запрос
        $backend->cursor->execute( $query['sql'], $query['params'] );

		// Запросы типа count возвращают колонку count
		$row = $backend->cursor->fetchOne();

		return $row[ 'count' ];
    }

    /**
        Получение кеш-ключа
    */
    protected function getCacheKey( $params )
    {
        $key = array();
        foreach( $params as $k => $v ) $key[] = "{$k}={$v}";
        return $this->model . ':'. join( ',', $key ) . ':' . StormCore::getLanguage()->getKey();
    }

    /**
        Чтение из кеша
    */
    protected function getCached( $params )
    {
        $key = $this->getCacheKey( $params );
        return array_key_exists( $key, self::$cache ) ? unserialize( self::$cache[ $key ] ) : false;
    }

    /**
        Кеширование объекта
    */
    protected function setCached( $params, $obj )
    {
        self::$cache[ $this->getCacheKey( $params ) ] = serialize( $obj );
        return true;
    }

    /**
        Получение одного объекта
    */
    function getNotCached( $params )
    {
        return $this->get( $params, false );
    }

    /**
        Получение одного объекта
    */
    function get( $params, $use_cache = true )
    {
        // Проверим параметры, это должен быть массив или integer
        if( ! is_array( $params ) )
        {
            if( ! ( is_numeric( $params ) || $params instanceof StormModel ) )
                throw new StormException( 'Must use filter parameter, integer value or StormModel instance to get model object ' );
            $params = array( 'pk' => is_object( $params ) ? $params->meta->getPkValue() : (int)$params );
        }

        // Проверим кеш
        if( $use_cache && ( $obj = $this->getCached( $params ) ) !== false ) return $obj;

        // Создадим пустой StormQuerySet
        $query_set = new StormQuerySet( $this->model );

        // Выбираем один объект
        $objects = $query_set->filter( $params )->limit( 1 );

        $obj = array_key_exists( 0, $objects ) ? $objects[0] : null;

        // Закешируем объект
        $this->setCached( $params, $obj );

        return $obj;
    }

    /**
        Создание объекта по переданным аргументам
    */
    function create( array $params )
    {
        // Создадим новый объект с переданными параметрами
        $obj = new $this->model( $params );

        // Сохраним его в БД и вернем
        $obj->save();

        return $obj;
    }

    /**
        Получение или создание объекта
    */
    function getOrCreate( array $params, $cached = true )
    {
        $obj = $this->get( $params, $cached );

        if( ! $obj )
        {
            $obj = $this->create( $params );
        }

        return $obj;
    }
}

?>