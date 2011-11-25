<?
/**
    Абстрактный курсор базы данных
*/
abstract class Storm_Db_Cursor
{
    // Соединение, через которое работает этот курсор
    protected $connection = NULL;

    /**
        Конструктор
        $connection - бэкэнд, через который работает этот курсор
    */
    function __construct( Storm_Db_Connection $connection )
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
