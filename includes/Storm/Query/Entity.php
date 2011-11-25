<?
/**
    Класс описания сущности выборки. Описывает данные, которые выбираюся для модели, участвующей в запросе.
    Используется для упорядоченного хранения информации.
*/

class Storm_Query_Entity
{
    public $model = null;   // Имя модели
    public $fields = array();   // Массив полей для SQL-запроса
    public $path = array(); // Путь по полям от начальной модели запроса к _этой_ модели
    /**
     * этой модели в общем запросе
     * @var Storm_Query_Join
     */
    public $join = null;
    public $aliases = array();  // массив соответствия полей fields полям модели

    function __construct( $model )
    {
        $this->model = $model;
    }
}

?>
