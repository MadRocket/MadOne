<?
/**
    Класс для хранения join-ов таблиц
*/

class Storm_Query_Join
{
    public $type = 'inner';
    public $table = null;
    public $alias = null;
    public $condition = null;
    
    function __construct( $type, $table, $alias, $condition )
    {
        if( !( $type == 'inner' || $type == 'left' || $type == 'INNER' || $type == 'LEFT' ) ) {
            throw new Storm_Exception( "Invalid join type: {$type}" );
        }

        $this->type = $type;
        $this->table = $table;
        $this->alias = $alias;
        $this->condition = $condition;
    }
    
    function __toString()
    {
        if( ! ( $this->type && $this->table && $this->alias && $this->condition ) ) {
            throw new Storm_Exception( "Incomplete join usage attempt" );
        }

        return "{$this->type} JOIN {$this->table} AS {$this->alias} ON {$this->condition}";
    }
}

?>
