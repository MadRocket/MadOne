<?

/**
    Абстрактный маппер моделей БД в запросы SQL
*/
abstract class StormDbMapper
{
    protected $creation_data_types;
	protected $fulltextColumnSuffix = '_fts'; // Full Text Search

    /**
        Получение строки создания поля
    */
	function getColumnDefinitionSql( StormDbField $field, $language = null ) {
        // Получим класс поля
        $class = get_class( $field );

        if( ! array_key_exists( $class, $this->creation_data_types ) ) {
            throw new StormException( "Don't know how to map {$class} to database column type" );
        }

        // Получим строку мапирования поля в БД и заполним ее полями поля
        return is_null( $this->creation_data_types[ $class ] ) ? NULL :
                preg_replace( '/%\{([^ ]+)\}/e', "\$field->getCreationVariable('\$1')", $this->creation_data_types[ $class ] );
    }
    
    function getLanguageSuffix( $language ) {
    	return "_{$language->getKey()}";
    }
    
    /**
    *	Получение имени колонки базы данных для указанного поля в указанной локали.
    *	$field — поле, объект — потомок StormDbField
    *	$language — язык, StormLanguage
    *	Возвращает строку - имя колонки в базе данных
    */
    function getFieldColumnName( StormDbField $field, $language = null ) {
    	if( ! $field->localized ) {
			return $field->name;
    	}
		if( ! $language ) {
			$language = StormCore::getLanguage();
		}
		return $field->name.$this->getLanguageSuffix( $language );
    }

	/**
	*	Получение имени колонки для полнотекстового поиска поля в указанной локали
	*
	*/    
    function getFieldFulltextColumnName( StormDbField $field, $language = null ) {
		if( ! $language ) {
			$language = StormCore::getLanguage();
		}
		return	$field->name.
				( $field->fulltext ? $this->fulltextColumnSuffix : '' ).
				( $field->localized ? $this->getLanguageSuffix( $language ) : '' );
    }
    
    /**
        Возвращает строку создания таблицы
    */
    function getTableCreationSql( $model )
    {
        // Получим метаданные модели
        $meta = StormCore::getInstance()->getStormModelMetadata( $model );

        // Тут будем хранить строки создания колонок
        $columns = array();
        
        foreach( $meta->getFields() as $field ) {
			foreach( $this->getFieldColumns( $field ) as $column ) {
				$definition = $this->getColumnDefinitionSql( $field, $column->language );
				if( $definition ) {
					$table = $this->getModelTable( $model );
					$columns[] = "`{$column->name}` {$definition} ". ( $field->null ? 'NULL' : 'NOT NULL' );
				}
			}
        }

        // Собственно, сгенерим запрос создания таблицы
        $table = $this->getModelTable( $model );
        $table_sql = "CREATE TABLE `{$table}` (". join( ', ', $columns ). ", PRIMARY KEY( `{$meta->pkname}` ) )";

        return $table_sql;
    }

    /**
        Возвращает массив индексов, которые должны быть созданы для модели
    */
    function getModelIndexes( $model )
    {
        // Создадим экземпляр модели для исследования
        $meta = StormCore::getInstance()->getStormModelMetadata( $model );

        $indexes = array();

        // Пройдемся по полям и приготовим для каждого из них строку создания
        foreach( $meta->getFields() as $name => $field )
        {
            // Ключ индекса по умолчанию
            $idx_name = "__{$name}";

            // Primary key
            if( $field instanceof StormAutoDbField )
            {
                $indexes[ $idx_name ] = new StormIndex( $model, array( $name ) );
                $indexes[ $idx_name ]->primary = true;
            }
            // Индексируемое поле
            else if( $field->index ) {
            	if( ! is_bool( $field->index ) ) {
            		$idx_name = $field->index;
            	}

            	if( $field->localized ) {
					foreach( StormCore::getAvailableLanguages() as $language ) {
						$sub_idx_name = $idx_name.$this->getLanguageSuffix( $language );
						if( ! array_key_exists( $sub_idx_name, $indexes ) ) {
							$indexes[ $sub_idx_name ] = new StormIndex( $model );
						}
						$indexes[ $sub_idx_name ]->addField( $this->getFieldColumnName( $field, $language ) );
					}
            	} else {
					if( ! array_key_exists( $idx_name, $indexes ) ) {
						$indexes[ $idx_name ] = new StormIndex( $model );
					}
					$indexes[ $idx_name ]->addField( $this->getFieldColumnName( $field ) );
            	}
            
            }
        }
        
        // Полнотекстовый индекс создается один на весь список полнотекстовых полей, но для каждой локали отдельно, если есть локализованные поля
        $gotLocalizedFields = false;
        $fulltextFields = array();
        foreach( $meta->getFields() as $field ) {
        	if( $field->fulltext ) {
				$fulltextFields[] = $field;
				if( $field->localized ) {
					$gotLocalizedFields = true;
				}
        	}
        }
        
		if( $fulltextFields ) {
        	if( $gotLocalizedFields ) {
				foreach( StormCore::getAvailableLanguages() as $language ) {
					foreach( $fulltextFields as $field ) {
						$idx1 = new StormIndex( $model, array( $this->getFieldColumnName( $field, $language ) ) );
						$idx1->fulltext = true;
						$indexes[] = $idx1;
						$idx2 = new StormIndex( $model, array( $this->getFieldColumnName( $field, $language ), $this->getFieldFulltextColumnName( $field, $language ) ) );
						$idx2->fulltext = true;
						$indexes[] = $idx2;
					}
					$idx1 = new StormIndex( $model );
					$idx1->fulltext = true;
					$idx2 = new StormIndex( $model );
					$idx2->fulltext = true;
					foreach( $fulltextFields as $field ) {
						$idx1->addField( $this->getFieldColumnName( $field, $language ) );
						$idx2->addField( $this->getFieldColumnName( $field, $language ) );
						$idx2->addField( $this->getFieldFulltextColumnName( $field, $language ) );
					}
					$indexes[] = $idx1;
					$indexes[] = $idx2;
				}
        	} else {
				// Создаем индексы для кажой колонки отдельно, на каждую колонку по две версии индекса — со служебным полем и без
        		foreach( $fulltextFields as $field ) {
					$idx1 = new StormIndex( $model, array( $this->getFieldColumnName( $field ) ) );
					$idx1->fulltext = true;
					$indexes[] = $idx1;
					$idx2 = new StormIndex( $model, array( $this->getFieldColumnName( $field ), $this->getFieldFulltextColumnName( $field ) ) );
					$idx2->fulltext = true;
					$indexes[] = $idx2;
        		}
				$idx1 = new StormIndex( $model );
				$idx1->fulltext = true;
				$idx2 = new StormIndex( $model );
				$idx2->fulltext = true;
				foreach( $fulltextFields as $field ) {
					$idx1->addField( $this->getFieldColumnName( $field ) );
					$idx2->addField( $this->getFieldColumnName( $field ) );
					$idx2->addField( $this->getFieldFulltextColumnName( $field ) );
				}
				$indexes[] = $idx1;
				$indexes[] = $idx2;
        	}
        }

        return array_values( $indexes );
    }
    
    /**
    *	Получение списка колонок модели с SQL их добавления в таблицу
    *
    *
    */
    function getModelColumnList( $model ) {
    	$columns = array();
		$meta = StormCore::getInstance()->getStormModelMetadata( $model );
		foreach( $meta->getFields() as $field ) {
			$columns = array_merge( $columns, $this->getFieldCreationSql( $model, $field ) );
		}
		return $columns;    
    }

    // Эти функции должны быть переопределены в реальных мапперах, работающих с конкретной СУБД
    abstract function getModelTable( $model );
    abstract function getModelAlias( $model );
    abstract function getTableList( StormDbCursor $cursor );
    abstract function getIndexCreationSql( StormIndex $idx );
    abstract function getIndexRemovingSql( $model, $index_name );
    abstract function getLimitOffsetSql( $limit = null, $offset = null );
    abstract function getOperatorSql( $field, $op, $extra = array() );
    abstract function operatorExists( $op );
}

?>
