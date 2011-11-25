<?

/**
    Курсор для работы с MySQL
*/

class Storm_Db_Cursor_Mysql extends Storm_Db_Cursor
{
    private $result = NULL;
    private $position = NULL;
    private $num_rows = NULL;
    private $rows_affected = NULL;


    /**
        Выполнение запроса
    */
    function execute( $query, array $args = array() )
    {
        // Экранируем аргументы, если они есть
        foreach( $args as $key => $val )
        {
        	if(is_array($val)) {
				$escaped = array();

				foreach($val as $k => $v) {
					$escaped[] = "'" . $this->connection->escape( ( is_object( $v ) && $v instanceof Storm_Model ) ? $v->meta->getPkValue() : $v  ) . "'";
				}

				$args[ $key ] = join(", ", $escaped);

        	}
			else {
        		$args[ $key ] = "'" . $this->connection->escape( $val ) . "'";
        	}
        }

        // Заполним строку запроса аргументами
        $filled_query = Storm_Utilities::array_printf( $query, $args );

        if( Storm_Config::$db_debug )
        {
            print( "<p><small>$filled_query</small></p>" );
        }

        // Выполним запрос
        $result = @mysql_query( $filled_query, $this->connection->handler );

        // Проверим результат
        if( $result === false )
        {
            throw new Storm_Exception( mysql_error() );
        }

        // Посмотрим, какого типа ответ вернул нам сервер
        if( $result === true  )
        {
            // Запрос не вернул данных, это был update/delete запрос
            $this->rows_affected = @mysql_affected_rows();
            $this->result = NULL;
            $this->num_rows = NULL;
        }
        else
        {
            // Запрос вернул данные
            $this->result = $result;
            $this->position = 0;
            $this->rows_affected = NULL;
            $this->num_rows = @mysql_num_rows( $this->result );
        }

        # Вернем количество подвергшихся изменению / выбраных строк
        return $this->rowcount;
    }

    # Проверяет наличие результата, если его нет - генерирует exception
    private function checkResult()
    {
        # Проверим наличие данных
        if( ! $this->result )
        {
            throw new Storm_Exception( 'A query that selects some rows must be executed before any data could be fetched' );
        }
        return true;
    }

    # Получение всех колонок, выбранных из базы в виде ассоциативного массива
    function fetchAll()
    {
        # Проверим наличие данных
        $this->checkResult();

        # Отскроллимся в начало
        $this->scroll( 0, 'abs' );

        # Циклом заполним массив записей
        $rows = array();
        while( ( $row = mysql_fetch_array( $this->result, MYSQL_BOTH ) ) !== false )
        {
            $rows[] = $row;
        }

        # Установим позицию
        $this->position = $this->num_rows;

        return $rows;
    }

    # Выборка нескольких записей с текущей позиции
    function fetchMany( $count )
    {
        # Проверим наличие данных
        $this->checkResult();

        # Циклом заполним массив записей
        $rows = array();
        while( $count-- > 0 && ( $row = mysql_fetch_array( $this->result, MYSQL_BOTH ) ) !== false )
        {
            $rows[] = $row;
            $this->position++;
        }

        # Вернем то, что получилось
        return $rows;
    }

    # Выборка одной записи с текущей позиции курсора
    // $field - имя поля, которое нужно вернуть, при отсутствии возвращается вся запись
    function fetchOne( $field = null )
    {
        # Проверим наличие данных
        $this->checkResult();

        # Вытащим запись, ++ позицию
        if( ( $row = mysql_fetch_array( $this->result, MYSQL_BOTH ) ) !== false )
        {
            $this->position++;
            return $field ? $row[ $field ] : $row;
        }

        return NULL;
    }

    # Перемещение указателя набора данных на указанную позицию
    function scroll( $offset, $mode = 'rel' )
    {
        # Проверим правильность режима скролла
        if( $mode != 'rel' && $mode != 'abs' )
        {
            throw new Storm_Exception( "Must specify scroll mode with 'abs' or 'rel' value, not '{$mode}'" );
        }

        # Проверим наличие данных
        $this->checkResult();

        # Если указан относительный режим - переведем смещение в абсолютный
        if( $mode == 'rel' )
        {
            $offset = $this->position + $offset;
        }

        # Пофиксим смещение
        if( $offset < 0 )
        {
            $offset = 0;
        }
        else if( $offset >= $this->num_rows )
        {
            $offset = $this->num_rows;
        }

        # Обновим позицию
        $this->position = $offset;

        # Выполним позиционирование
        @mysql_data_seek( $this->result, $this->position );
    }

    # Закрытие указателя данных, обнуление всех полей
    function close()
    {
        if( $this->result )
        {
            @mysql_free_result( $this->result );
        }
        $this->result = NULL;
        $this->position = NULL;
        $this->rows_affected = NULL;
        $this->num_rows = NULL;
    }

    /* Overloading полей класса
            rowcount - количество строк выбраных / измененных последним запросом
            position - текущая позиция курсора в наборе данных
    */
    function __get( $name )
    {
        # Количество записей
        if( $name == 'rowcount' )
        {
            if( $this->result )
            {
                return $this->num_rows;
            }
            else if( ! is_null( $this->rows_affected ) )
            {
                return $this->rows_affected;
            }
            $this->checkResult();
        }
        # Позиция
        else if( $name == 'position' )
        {
            $this->checkResult();
            return $this->position;
        }
    }
}

?>