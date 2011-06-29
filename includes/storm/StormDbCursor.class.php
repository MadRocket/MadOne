<?
/**
    Абстрактный курсор базы данных
*/
abstract class StormDbCursor
{
    // Соединение, через которое работает этот курсор
    protected $connection = NULL;

    /**
        Конструктор
        $connection - бэкэнд, через который работает этот курсор
    */
    function __construct( StormDbConnection $connection )
    {
        $this->connection = $connection;
    }

    abstract function execute( $query, array $args = array() );

    abstract function scroll( $offset, $mode = 'rel' );

    abstract function fetchAll();
    abstract function fetchMany( $count );
    abstract function fetchOne( $field = null );

    abstract function close();

    // Обязательно должен быть определен __get для полей position и rowcount
    abstract function __get( $name );
}


?>
