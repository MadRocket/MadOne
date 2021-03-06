<?

/**
 * Процессор REST-запросов к моделям шторма.
 * Нужно очень аккуратно разграничивать доступ к этому интерфейсу, ибо он позволяет редактировать любую модель сайта.
 */
class Storm_Processor {

    // Массив имен моделей, которые нельзя редактировать через процессор
    protected $disallowedModels = array(
        'MadoneUser',
    );

    /**
     * Обработка запроса
     * @param $model имя модели
     * @param $uri путь запроса, например '/5/update/'
     * @param $vars переменные, переданные вместе с запросом (обычно это Mad::vars())
     * @return mixed
     */
    function process( $model, $uri, $vars )
    {
        try {
            // Проверим данные
            if( ! ( class_exists( $model ) && is_subclass_of( $model, 'Storm_Model' ) ) ) {
                throw new Exception( "Класс {$model} не является шторм-моделью и не может быть обработан." );
            } elseif( in_array( $model, $this->disallowedModels ) ) {
                throw new Exception( "Доступ к модели {$model} запрещен." );
            }
            
            // Ответные данные запроса
            $data = null;

            // Получим путь, чтобы понять что делать
            $names = Madone_Utilites::getUriPathNames( $uri );
        
        	if( ! $names ) {
        		// Если в uri ничего не указано — возвращаем структуру модели
        		
        		$object = new $model;
        		$data = $object->asArray( true );
        	}        		
			elseif( array_key_exists(0, $names) ) {
				// Действия над моделью и множеством объектов
				
				switch( $names[0] ) {
	                
	                case 'create':
					$data = Storm_Queryset( $model )->create( ( count( $vars ) == 1 && array_key_exists( 'json_data', $vars ) ) ? json_decode( $vars['json_data'], true ) : $vars )->asArray();
	                break;
	
	                case 'update':
	                $objects = json_decode( $vars['objects'], true );
	                foreach( Storm_Queryset( $model )->filter( array( 'pk__in' => array_keys( $objects ) ) )->all() as $o ) {
	                    $o->copyFrom( $objects[ $o->meta->getPkValue() ] )->save();
	                    unset( $objects[ $o->meta->getPkValue() ] );
	                }
	                $this->checkUnusedObjects( $model, $objects );
	                break;
	
	                case 'delete':
	                $objects = array();
	                foreach( json_decode( $vars['objects'], true ) as $id ) {
	                    $objects[ $id ] = $id;
	                }
	                foreach( Storm_Queryset( $model )->filter( array( 'pk__in' => $objects ) )->all() as $o ) {
	                    $o->delete();
	                    unset( $objects[ $o->meta->getPkValue() ] );
	                }
	                $this->checkUnusedObjects( $model, $objects );
	                break;
	
	                case 'reorder':
                        if( ! ( class_exists( $model ) && is_subclass_of( $model, 'Storm_Model_Tree' ) ) ) {
                            throw new Exception( "Модель {$model} не имеет Ki-индекса, и упорядочить ее невозможно." );
                        }
                        Storm_Queryset( $model )->reorder( json_decode( $vars['objects'], true ) );
	                break;
	
	                case 'retrieve':
	                    /*
	                    query должен быть JSON-закодированным массивом следующего вида
	                    [
	                        {                       // первый вызов
	                            filter:             // имя метода
	                            [                   // массив аргументов
	                                'position__lt', // первый аргумент
	                                4               // второй аргумент
	                            ]
	                        },
	                        {                       // второй вызов
	                            order:              // имя метода
	                            [                   // массив аргументов
	                                'position'      // первый аргумент
	                            ]
	                        
	                        },
	                        {                       // третий вызов
	                            limit:              // имя метода
	                            [                   // массив аргументов
	                                10              // первый аргумент
	                            ]
	                        }
	                    ]
	                    Вся эта структура преобразуется в последовательные запросы к соответствующему Storm_Queryset-у.
	                    Результат обрабатывается методом getJsonSafe и возвращается.
	                    Можно применять финальные методы limit, first, all, tree, а можно и не применять — автоматически применится all.
	                    Структура возвращается максимально соответствующая запросу — asArray( true ) для объектов, массивы для массивов.
	                    */
	                
	                    $query = Storm_Queryset( $model );
	                    
	                    if( $vars['query'] ) {
	                        // Фильтруем, фильтруем, фильтруем
	                        foreach( json_decode( $vars['query'], true ) as $part ) {
	                        
	                            foreach( $part as $method => $args ) {
	                            
	                                if( ! is_object( $query ) ) {
	                                    throw new Exception( "Метод {$method} не может быть вызван, так как текущий запрос ({$query}) не является объектом." );
	                                } elseif( ! method_exists( $query, $method ) ) {
	                                    throw new Exception( "Метод {$method} не найден в классе ".get_class( $query )."." );
	                                }
	                                
	                                $query = call_user_func_array( array( $query, $method ), $args );
	                            }
	                        }
	                    }
	                    
	                    // Нафильтровали! Теперь смотрим, что получилось, переводим его в array или что-то подобное и готово.
	                    $data = $this->getJsonSafe( $query );
	                break;
	                
	                default:
						// Действия над конкретным элементом модели, идентифицируемым по значению PK
						
						if( is_numeric( $names[0] ) ) {
							switch( $names[1] ) {
								case 'update':
								$data = $this->getModelObject( $model, $names[0] )->copyFrom( ( count( $vars ) == 1 && array_key_exists( 'json_data', $vars ) ) ? json_decode( $vars['json_data'], true ) : $vars )->save()->asArray();
								break;
			
								case 'delete':
								$this->getModelObject( $model, $names[0] )->delete();
								break;
			
								case 'retrieve':
								$data = $this->getModelObject( $model, $names[0] )->asArray( true );
								break;
									
								default:
								$data = $this->getModelObject( $model, $names[0] )->asArray();
								break;
							}						
						}
					break;
	            }	
	            
	            		
			}

            return json_encode( array( 'success' => true, 'data' => $data ) );
        }
        catch( Exception $e ) {
            return json_encode( array(
                'success' => false,
                'message' => $e->getMessage(),
            ) );
        }    
    }

    /**
     * Получение объекта модели
     * @param $model
     * @param $id
     * @return bool|mixed|null
     */
    protected function getModelObject( $model, $id ) {
        $object = Storm_Queryset( $model )->get( $id );
        
        if( ! $object ) {
            throw new Exception( "Объект {$model} с идентификатором {$id} не найден." );
        }

        return $object;
    }

    /**
     * Проверка пустоты массива данных объектов. Если в массиве есть элементы, выбрасывается Exception
     * с сообщением, что имеющиеся в массиве объекты не найдены.
     * @param $model
     * @param array $objects
     * @return bool
     */
    protected function checkUnusedObjects( $model, array $objects ) {
        if( $objects ) {
            throw new Exception( 
                count( $objects ) > 1 ?
                "Объекты {$model} с идентификаторами ". join( ', ', array_keys( $objects ) ) ." не найдены." :
                "Объект {$model} с идентификатором ". join( ', ', array_keys( $objects ) ) ." не найден."
            );
        }
        return true;
    }

    /**
     * Перевод чего-то в JSON-безопасную форму.
     * @param $input могут быть строкой, массивом, массивом объектов, QuerySet-ом, моделью %D
     * @return array|null
     */
    protected function getJsonSafe( $input ) {
    
        $result = null;
    
        if( is_object( $input ) ) {

            if( $input instanceof Storm_Model ) {
                $result = $input->asArray( true );
                
            } elseif( $input instanceof Storm_Queryset ) {
                $result = $this->getJsonSafe( $input->all() );

            } else {
                throw new Exception( "Cannot make ".get_class( $input )." object JSON-safe." );
            }
        } elseif( is_array( $input ) ) {
            $result = array();
            foreach( $input as $k => $v ) {
                $result[ $k ] = $this->getJsonSafe( $v );
            }
        } else {
            $result = $input;
        }
        
        return $result;
    }
}

?>