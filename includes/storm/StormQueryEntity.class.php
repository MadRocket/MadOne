<?
/**
    Класс описания сущности выборки. Описывает данные, которые выбираюся для модели, участвующей в запросе.
    Используется для упорядоченного хранения информации.
*/

class StormQueryEntity
{
    public $model = null;   // Имя модели
    public $fields = array();   // Массив полей для SQL-запроса
    public $path = array(); // Путь по полям от начальной модели запроса к _этой_ модели
    public $join = null;    // StormQueryJoin этой модели в общем запросе
    public $aliases = array();  // массив соответствия полей fields полям модели
    
    /**
        Конструктор.
    */
    function __construct( $model )
    {
        $this->model = $model;
    }
}

?>
