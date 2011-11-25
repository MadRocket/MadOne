<?

/**
    Оператор - проверка значения переменной
*/

class Storm_Qc_Op_Check extends Storm_Qc_Op
{
	static private $ftsParamIdx = 1;

    protected $op;

    protected $field;
    protected $value;
    
    static private function getFtsParamName() {
    	return "fts" . self::$ftsParamIdx++;
    }

    /**
        Конструктор
    */
    function __construct( $field, $value )
    {
        // Разделим field на имя переменной и оператор
        $pieces = explode( '__', $field );

        // Проверим оператор поиска, если он не задан - используем exact
        if( count( $pieces ) < 2 || ! Storm_Core::getMapper()->operatorExists( $pieces[ count( $pieces ) -1 ] ) )
        {
            $this->op = 'exact';
        }
        else
        {
            $this->op = array_pop( $pieces );
        }

        // Сгенерим имя поля (оно могло быть составным, и из него мог быть изъят оператор, так что выполняем join)
        $this->field = join( '__', $pieces );

        // Значение в любом случае — то, что передали. Приведение экземпляров моделей к значениям ключевого поля будет сделано прямо перед выборкой
        $this->value = $value;
    }

    /*  Получение частей SQL-кода запроса, соответствующего данному условию выборки
        Аргументы:
            $model - имя модели, для которой выполняется запрос
        Возвращает
            array( 'joins'=>array(), where=>'sql', params=>array(...))
            joins  - array( array( 'alias'=>'mytable', 'table'=>'mytable', 'condition'=>'', 'type'=>'inner join' ) ... )
            where  - SQL-код с позиционными плейсхолдерами вида %s
            params - array параметров для where
    */
    function getStormQueryParts( $model )
    {
        // То, что будем возвращать
        $result = array
        (
            'joins'  => array(),
            'where'  => '',
            'params' => array(),
            'expressions' => array(),
        );

        // Поле, которое преобразуем в SQL
        $field = $this->field;

        // Класс поля
        $field_class = null;
        
        // Экстра-данные поля
		$extra = array();
		
		
		// Поле * — все полнотекстовые поля модели
		if( $field == '*' ) {
			if( $this->op !== 'match' ) {
	            throw new Storm_Exception( "Fake field '*' could not be used in '{$this->op}' operator. Consider 'match'." );
			}

            // Создадим экземпляр модели
			$meta = Storm_Core::getInstance()->getStormModelMetadata( $model );
            // Получим alias таблицы
            $alias = Storm_Core::getMapper()->getModelAlias( $model );
            
            $fields = array();
            $fts = array();
            foreach( $meta->getFields() as $f ) {
            	if( $f->fulltext ) {
	            	$fields[] = "{$alias}.". Storm_Core::getMapper()->getFieldColumnName( $f );
	            	$fields[] = "{$alias}.". Storm_Core::getMapper()->getFieldFulltextColumnName( $f );
	            	$fts[] = "{$alias}.". Storm_Core::getMapper()->getFieldFulltextColumnName( $f );
            	}
            }
            
            if( ! $fields ) {
	            throw new Storm_Exception( "Could not perform '*__match' on '{$model}'. There are no fulltext columns." );
            }
            
            $field = join( ', ', $fields );
            $extra['fts'] = join( ', ', $fts );
            $field_class = 'Storm_Db_Field_Text';
            
		} else if( strpos( $field, '__' ) === false ) {
	        // Проверяем поле. Самый простой вариант — в поле нет двойных подчерков, это просто поле текущей модели

            // Создадим экземпляр модели
			$meta = Storm_Core::getInstance()->getStormModelMetadata( $model );

            // Первая проверка - замена primary key
            if( $field == 'pk' ) $field = $meta->getPkname();

            // Проверим наличие поля в модели
            if( ! $meta->fieldExists( $field ) ) throw new Storm_Exception( "Field '{$field}' not found in model {$model}" );

            // Получим alias таблицы
            $alias = Storm_Core::getMapper()->getModelAlias( $model );
            
            $fieldObject = $meta->getField( $field );
            $field_class = get_class( $fieldObject );

            // Сгенерируем имя поля
			$field = "{$alias}.". Storm_Core::getMapper()->getFieldColumnName( $fieldObject );
			
			// Дополнительная инфа по fulltext-полям
			if( $this->op == 'match' && $fieldObject->fulltext ) {
				$field .= ", {$alias}.". Storm_Core::getMapper()->getFieldFulltextColumnName( $fieldObject );
				$extra['fts'] = "{$alias}.". Storm_Core::getMapper()->getFieldFulltextColumnName( $fieldObject );
			}
        }
        else
        {
            // complex field lookup, связка по ForeignKey
            try {
            	$cf = new Storm_Db_Field_Complex( $model, $field );
            } catch( Storm_Exception $e ) {
            	if( $e->getCode() == 1 ) {
            		throw new Storm_Exception( "Incorrect QC check operator '{$field}' for {$model} model" );
            	} else {
            		throw $e;
            	}
            }

            // Имя поля
            $field = $cf->field;
            
            // Класс поля
            $field_class = $cf->field_class;

			// Дополнительные данные
			if( $cf->extra ) {
				$extra = array_merge( $extra, $cf->extra );
			}

            // Добавим join-ов
            $result['joins'] = $cf->joins;
        }

		$value = null;

        // Значение, которое подставляем
		if(is_array($this->value)) {
			$value = $this->value;
			
			foreach($value as $k => & $v) {
				$v = call_user_func( array( $field_class, 'getCheckOpValue' ), $v );
			}
		} 
		else {
			$value = call_user_func( array( $field_class, 'getCheckOpValue' ), $this->value );
		}

        // Если передан instance модели - преобразуем его в значение PK
        if( $value instanceof Storm_Model )
        {
            $value = $value->meta->getPkValue();
        }

        // Запиздачим value, если это требуется оператору
        switch( $this->op )
        {
            case 'iexact':
                $value = $this->getLikeValue( $value, true, true );
                break;
            case 'contains':
            case 'icontains':
                $value = $this->getLikeValue( $value, false, false );
                break;
            case 'startswith':
            case 'istartswith':
                $value = $this->getLikeValue( $value, true, false );
                break;
            case 'endswith':
            case 'iendswith':
                $value = $this->getLikeValue( $value, false, true );
                break;
			case 'match':
				if( ! array_key_exists( 'fts', $extra ) ) {
					throw new Storm_Exception( "Can't use 'match' operator on '{$this->field}' field because it has fulltext = false" );
				}
				$fulltextProcessor = new Storm_Utilities_Fulltextprocessor();
				
				// TODO почистить $value от знаков препинаний, тегов, entities и всего такого остального
				$words = array();
				
				foreach( $fulltextProcessor->getAllForms( $value ) as $k => $w ) {
					if( mb_strlen( $k ) > 3 && $w ) {
						$words[] = '('. join( ' ', $w ) . ')';
					} else if( mb_strlen( $k ) > 3 && ! $w ) {
						$words[] = "{$k}";
					}
				}

				$value = '>('.( $value ).') <+('. join( ' ', $words ) .')';
				
				$extra['fts_param'] = self::getFtsParamName();
				
				break;
        }

        // Получим маппинг оператора
        if( is_null( $value ) )
        {
            if( $this->op == 'eq' || $this->op == 'exact' || $this->op == 'iexact' ) $this->op = 'isnull';
            elseif( $this->op == 'ne' ) $this->op = 'isnotnull';
        }
        
        $operator = Storm_Core::getMapper()->getOperatorSql( $field, $this->op, $extra );
        
        if( ! $operator ) {
			throw new Storm_Exception( "Unknown StormQueryCheck operator '{$this->op}'" );
        }
        
        if( $this->op == 'match' ) {
        	$result['expressions']['fulltext_relevance'] = $operator;
			$result['params'][ $extra['fts_param'] ] = $value;
        } else {
			if( is_null( $value ) ) {
				$operator = Storm_Utilities::array_printf( $operator, array( 'NULL' ) );
			}
			else {
				$result['params'][] = $value;
			}
        }

        // Наконец сгенерируем условие выборки
        $result['where'] = $operator;
        
        return $result;
    }
}

?>
