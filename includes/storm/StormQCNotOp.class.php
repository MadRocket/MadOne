<?

/**
    Оператор — отрицание
*/

class StormQCNotOp extends StormQCOp
{
    protected $op;

    /**
        Конструктор
    */
    function __construct( StormQCOp $op )
    {
        $this->op = $op;
    }

    /**
        Получение SQL
    */
    function getStormQueryParts( $model )
    {
        // Получим SQL-параметры оператора, которые мы отрицаем
        $r = $this->op->getStormQueryParts( $model );

        // И собственно, отрицаем его условия выборки
        $r['where'] = "( NOT {$r['where']} )";

        return $r;
    }
}

?>
