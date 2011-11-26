<?

/**
    Маппер базы данных для MySQL
*/
class Storm_Db_Mapper_Mysql extends Storm_Db_Mapper
{
	/**
	*	Описание charset и collation для каждой локали. Ключ - локаль, значение — SQL-код
	*	Пример:
	*	'ru_RU.UTF-8' => 'CHARSET UTF8 COLLATE utf8_general_ci
	*	При отсутствии ключа берется значение 'DEFAULT'
	*/
	protected $collations = array(
		'DEFAULT'		=> 'CHARSET UTF8 COLLATE utf8_general_ci',
	);

    // Описание типов данных колонок этого бэкэнда
    protected $creation_data_types = array
    (
        'Storm_Db_Field_Auto'      => 'INTEGER UNSIGNED AUTO_INCREMENT',
        'Storm_Db_Field_Integer'   => 'INTEGER',
        'Storm_Db_Field_Float'		=> 'FLOAT',
        'Storm_Db_Field_Bool'      => 'TINYINT UNSIGNED',
        'Storm_Db_Field_Char'      => 'VARCHAR(%{maxlength})',
        'Storm_Db_Field_Text'      => 'LONGTEXT',
		'Storm_Db_Field_File_Image'     => 'TEXT',
        'Storm_Db_Field_Fk'        => 'INTEGER UNSIGNED',
        'Storm_Db_Field_Datetime'  => 'DATETIME',
        'Storm_Db_Field_Enum'      => 'ENUM(%{values})',
		'Storm_Db_Field_File'		=> 'TEXT',
		'Storm_Db_Field_File_Flv'		=> 'TEXT',
		'StormNewFileDbField'	=> 'TEXT',
    );

    /**
        Описание маппирования операторов выборки
        %s - значение, которое будет подставлено в запрос
    */
    protected $operator_mapping = array
    (
        'ne'            => '%{field} != %s',
        'eq'            => '%{field} = %s',
        'exact'         => '%{field} = %s',
        'iexact'        => '%{field} LIKE %s',
        'contains'      => '%{field} LIKE BINARY %s',
        'icontains'     => '%{field} LIKE %s',
        'gt'            => '%{field} > %s',
        'ge'            => '%{field} >= %s',
        'lt'            => '%{field} < %s',
        'le'            => '%{field} <= %s',
        'startswith'    => '%{field} LIKE BINARY %s',
        'endswith'      => '%{field} LIKE BINARY %s',
        'istartswith'   => '%{field} LIKE %s',
        'iendswith'     => '%{field} LIKE %s',
        'isnull'        => '%{field} IS NULL',
        'isnotnull'     => '%{field} IS NOT NULL',
        'substringof'   => "%s LIKE BINARY CONCAT( %{field}, '%' )",
		'in'   			=> "%{field} in ( %s )",
		'match'			=> 'MATCH( %{field} ) AGAINST ( %{%{fts_param}} IN BOOLEAN MODE )',
    );

    /**
        Получение списка таблиц
    */
    function getTableList( Storm_Db_Cursor $cursor )
    {
        $cursor->execute( "SHOW TABLES" );
        return array_map( create_function( '$row', 'return $row[0];'), $cursor->fetchAll() );
    }

    /**
        Получение списка колонок модели, имеющихся в базе данных
    */
    function getColumnList( Storm_Db_Cursor $cursor, $model )
    {
        $table = $this->getModelTable( $model );

        $cursor->execute( "SHOW COLUMNS FROM `{$table}`" );
        return array_map( create_function( '$row', 'return $row[0];'), $cursor->fetchAll() );
    }

    /**
        Получение списка индексов, для каждого индекса возвращаются флаги primary и unique
    */
    function getIndexList( Storm_Db_Cursor $cursor, $model )
    {
        $table = $this->getModelTable( $model );

        $cursor->execute( "SHOW INDEX FROM `{$table}`" );

        $indexes = array();
        while( $row = $cursor->fetchOne() )
        {
            $indexes[ $row[2] ] = array
            (
                'primary_key' => ( $row[2] == 'PRIMARY' ),
                'unique'      => ( ! (boolean) $row[1] ),
            );
        }

        return $indexes;
    }

    /**
        Получение строки создания индекса
    */
    function getIndexCreationSql( Storm_Index $idx )
    {
        $table = $this->getModelTable( $idx->model );

        if( $idx->primary )
        {
            return "ALTER TABLE `{$table}` ADD PRIMARY KEY (" . implode( $idx->fields, ', ' ).")";
        }
        else
        {
            return 'CREATE' . ( $idx->unique ? ' UNIQUE' : '' ) . ( $idx->fulltext ? ' FULLTEXT' : '' ) . ' INDEX `'.
			$idx->getName()."` ON `{$table}` (". join( ', ', array_map( create_function( '$name', 'return "`{$name}`";' ), $idx->fields ) ).")";
        }
    }

    /**
        Получение строки удаления индекса
    */
    function getIndexRemovingSql( $model, $index_name )
    {
        $table = $this->getModelTable( $model );
        return "DROP INDEX `{$index_name}` ON `{$table}`";
    }

    /**
        Получение строки создания колонки модели
    */
    function getColumnCreationSql( $model, $column, $language = null )
    {
        // Получим метаданные модели для исследования
        $meta = Storm_Core::getInstance()->getStormModelMetadata( $model );
        // Получим ссылку на поле
        $field = $meta->getField( $column );

        // Получим строку создания колонки
        $cs = $this->getColumnDefinitionSql( $field, $language );
        
        if( ! is_null( $cs ) ) {
            // Сгенерим имя таблицы
            $table = $this->getModelTable( $model );
            // Возвращаем то, что получилось
            return "ALTER TABLE `{$table}` ADD COLUMN `".  $this->getFieldColumnName( $field, $language ) ."` $cs ". ( $field->null ? 'NULL' : 'NOT NULL' );
        } else {
            return NULL;
        }
    }

    /**
        Получение имени таблицы модели
    */
    function getModelTable( $model )
    {
        $model = preg_replace('~Model_~', '', $model);
        return ( Storm_Config::$db_prefix ? Storm_Config::$db_prefix : '' ). strtolower( $model );
    }

    /**
        Получение alias-а таблицы модели для выполнения запросов
    */
    function getModelAlias( $model )
    {
        // Желательно, чтобы alias не пересекался с таблицами, поэтому просто добавим _
        return '_'.strtolower( $model );
    }

    /**
        Маппирование оператора Storm_Qc
    */
    function getOperatorSql( $field, $op, $extra = array() ) {
		if( ! array_key_exists( $op, $this->operator_mapping ) ) {
			throw new Storm_Exception( "Unknown operator '{$op}" );
		}
		
		$data = array_merge( array( 'field' => $field ), $extra );
		
		return Storm_Utilities::array_printf( $this->operator_mapping[$op], $data );
	}

    /**
        Проверка существования оператора
    */
    function operatorExists( $op )
    {
        return array_key_exists( $op, $this->operator_mapping );
    }

    /**
        Получение SQL-строки limit offset
    */
    function getLimitOffsetSql( $limit = null, $offset = null )
    {
        # Проверим аргументы
        if( is_null( $limit ) )
        {
            if( is_null( $offset ) ) return '';

            throw new Storm_Exception( 'Cannot use offset without a limit' );
        }

        if( ! is_numeric( $limit ) )
            throw new Storm_Exception( 'Must use a numeric limit' );

        if( ! is_null( $offset ) && !is_numeric( $offset ) )
            throw new Storm_Exception( 'Must use a numeric offset' );

        return "LIMIT ". ( $offset ? "{$offset}," : '' ). $limit;
    }
    
	function getColumnDefinitionSql( Storm_Db_Field $field, Storm_Language $language = null ) {
		$sql = parent::getColumnDefinitionSql( $field, $language );
		if( $language ) {
			// Для текстовых полей указываем charset и collate :3
			if( $field instanceof Storm_Db_Field_Char || $field instanceof Storm_Db_Field_Text || $field instanceof Storm_Db_Field_Enum ) {
				$collation = array_key_exists( $language->getName(), $this->collations ) ? $this->collations[ $language->getName() ] : $this->collations['DEFAULT'];
				$sql .= " {$collation}";
			}
		}
    	return $sql;
	}
	
	/**
	*	Получение списка колонок, которые нужно создать для этого поля
	*	Возвращает массив колонок
	*/	
	function getFieldColumns( $field ) {
		$columns = array();
		if( $field->localized ) {
			foreach( Storm_Core::getAvailableLanguages() as $language ) {
				$columns[] = new Storm_Db_Column( $this->getFieldColumnName( $field, $language ), $language );
				if( $field->fulltext ) {
					$columns[] = new Storm_Db_Column( $this->getFieldFulltextColumnName( $field, $language ), $language );
				}
			}
		} else {
			$columns[] = new Storm_Db_Column( $this->getFieldColumnName( $field ) );
			if( $field->fulltext ) {
				$columns[] = new Storm_Db_Column( $this->getFieldFulltextColumnName( $field ) );
			}
		}
		return $columns;
	}
	
	/**
	*	Получение SQL создания переданного поля в указанной модели.
	*	Возвращает массив, ключи — имена колонок таблицы, которые нужно создать, значения — соответствующие SQL-запросы.
	*	Если создавать ничего не требуется, возвращает пустой массив.
	*/
	function getFieldCreationSql( $model, Storm_Db_Field $field ) {
		$queries = array();

		foreach( $this->getFieldColumns( $field ) as $column ) {
			$definition = $this->getColumnDefinitionSql( $field, $column->language );
			if( $definition ) {
				$table = $this->getModelTable( $model );
				$queries[ $column->name ] = "ALTER TABLE `{$table}` ADD COLUMN `{$column->name}` {$definition} ". ( $field->null ? 'NULL' : 'NOT NULL' );
			}
		}

		return $queries;
	}

    function getTableCreationSql( $model )
    {
        $table_sql = parent::getTableCreationSql($model);
        return "$table_sql ENGINE = MYISAM";
    }
}

?>