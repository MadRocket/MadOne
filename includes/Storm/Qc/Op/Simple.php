<?

/**
 * Storm_Qc_Op_Simple class.
 * Простой оператор. Возвращает то, что передали в конструкторе.
 * @extends Storm_Qc_Op
 */
class Storm_Qc_Op_Simple extends Storm_Qc_Op
{
    protected $query_part = array();
    
    /**
        Конструктор
    */
    function __construct( $query_part )
    {
        $this->query_part = array
        (
            'joins'  => is_array( $query_part['joins'] ) ? $query_part['joins'] : array(),
            'where'  => $query_part['where'] ? "( {$query_part['where']} )" : '',
            'params' => is_array( $query_part['params'] ) ? $query_part['params'] : array(),
            'expressions' => array_key_exists( 'expressions', $query_part ) && is_array( $query_part['expressions'] ) ? $query_part['expressions'] : array(),
        );
    }

    /**
        Получение SQL
    */
    function getStormQueryParts( $model )
    {
        return $this->query_part;
    }
}

?>
