<?

class StormMysqlDbConnection extends StormDbConnection
{
    protected $handler = NULL;
    protected $cursor  = NULL;
    
    /**
        Подключение к базе данных.
        При имеющемся подключении не делает ничего.
        Возвращает true, генерирует Exception на неудачу.
    */
    function open()
    {
        // Есть соединение с базой?
        if( $this->handler )
        {
            // Он пингуется — все в порядке, ничего не делаем.
            if( @mysql_ping( $this->handler ) === true ) return true;

            // Не пингуется, соединимся заново, закрыв соединение
            $this->close();
            $this->open();
        }
        // Соединения нету, нужно установить
        else
        {
            // Соединяемся
            $handler = @mysql_connect(
                $this->host . ( $this->port ? ":{$this->port}" : '' ),
                $this->user,
                $this->password,
                true    // всегда открываем новое соединение
            );

            if( ! $handler )
            {
                throw new StormException( mysql_error() );
            }

            // Попробуем выбрать базу данных, проверим результат
            if( @mysql_select_db( $this->name, $handler ) !== true )
            {
                $error = mysql_error();
                mysql_close( $handler );
                throw new StormException( $error );
            }

            // Установим кодировку соединения
            if( $this->charset )
            {
                @mysql_query( "SET NAMES '{$this->charset}'", $handler );
                if( @mysql_error() )
                {
                    $error = mysql_error();
                    mysql_close( $handler );
                    throw new StormException( $error );
                }
            }

            // Все прошло хорошо - сохраним линк на соединение
            $this->handler = $handler;
            $this->cursor = NULL;
        }
    }

    /**
        Закрытие соединения
    */
    function close()
    {
        if( $this->handler ) @mysql_close( $this->handler );
        
        $this->handler = NULL;
        $this->cursor = NULL;
        
        return true;
    }

    /**
        Закавычивание строки
    */
    function escape( $string )
    {
        $this->open();
        return mysql_real_escape_string( $string, $this->handler );
    }
    
    /**
        Закавычивание имени объекта БД
    */
    function escapeName( $name )
    {
        if( mb_substr( $name, 0, 1, 'utf-8' ) == '`' && mb_substr( $name, mb_strlen( $name, 'utf-8' ) - 1, 1, 'utf-8' ) == '`' )
        {
            return $name;
        }

        return "`{$name}`";
    }
    
    /**
        Получение значения автоинкрементного поля, установенного базой
    */
    function getLastInsertId( StormDbCursor $cursor = NULL, $table='', $pkname='' )
    {
        return mysql_insert_id( $this->handler );
    }
    
    /**
        Получение handlerа соединения
    */
    function getHandler()
    {
        $this->open();
        return $this->handler;
    }
    
    /**
        Получение курсора соединения
    */
    function getCursor()
    {
        $this->open();

        if( ! $this->cursor ) $this->cursor = new StormMysqlDbCursor( $this );
        
        return $this->cursor;
    }    
}

?>
