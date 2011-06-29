<?
/**
    Абстрактное соединение с БД
*/
abstract class StormDbConnection
{
    // Переменные подключения к БД
    protected $host;
    protected $port;
    protected $name;
    protected $user;
    protected $password;
    protected $charset;

    // Простенький конструктор, копирующий параметры подключения из массива, переданного ему
    function __construct( array $options )
    {
        foreach( array( 'host', 'port', 'name', 'user', 'password', 'charset' ) as $k )
        {
            $this->$k = array_key_exists( $k, $options ) ? $options[ $k ] : NULL;
        }
    }

    // Подключение/ототключение от БД
    abstract function open();
    abstract function close();

    // Закавычивание строк, обычное и для имен объектов
    abstract function escape( $string );
    abstract function escapeName( $string );

    // Получение хэндлера соединения (может требоваться курсору или кому-то еще
    abstract function getHandler();

    // Получение курсора соединения с БД
    abstract function getCursor();

    // Получение значения автоинкрементного поля, установенное базой
    abstract function getLastInsertId( StormDbCursor $cursor = NULL, $table='', $pkname='' );

    // Сделаем псевдополя handler и cursor
    function __get( $name )
    {
        switch( $name )
        {
            case 'handler': return $this->getHandler();
            case 'cursor': return $this->getCursor();
        }
    }
}

?>
