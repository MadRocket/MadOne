<?

/**
    Пустой оператор.
    Нужен для начальной инициализации дерева условий StormQuerySet-а.
*/

class StormQCEmptyOp extends StormQCOp
{
    /**
        Получение SQL
    */
    function getStormQueryParts( $model )
    {
        // То, что будем возвращать
        return array
        (
            'joins'  => array(),
            'where'  => '',
            'params' => array(),
            'expressions' => array(),
        );
    }
}

?>
