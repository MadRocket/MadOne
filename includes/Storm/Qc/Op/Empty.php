<?

/**
    Пустой оператор.
    Нужен для начальной инициализации дерева условий Storm_Queryset-а.
*/

class Storm_Qc_Op_Empty extends Storm_Qc_Op
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
