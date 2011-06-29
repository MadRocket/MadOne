<?
/**
Бинарный оператор (два аргумента)
*/

class StormQCBinaryOp extends StormQCOp
{
    protected $left;    // Левый операнд
    protected $right;   // Правый операнд
    protected $sqlop;

    /**
        Конструктор
    */
    function __construct( StormQCOp $left, StormQCOp $right )
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
        Получение SQL
    */
    function getStormQueryParts( $model )
    {
        if( $this->left instanceof StormQCEmptyOp ) { return $this->right->getStormQueryParts( $model ); }
        if( $this->right instanceof StormQCEmptyOp ) { return $this->left->getStormQueryParts( $model ); }
    
        $l = $this->left->getStormQueryParts( $model );
        $r = $this->right->getStormQueryParts( $model );
        
        return array
        (
            'joins'  => array_merge( $l['joins'], $r['joins'] ),
            'where'  => "( {$l['where']} {$this->sqlop} {$r['where']} )",
            'params' => array_merge( $l['params'], $r['params'] ),
            'expressions' => array_merge( $l['expressions'], $r['expressions'] ),
        );
    }    
}


?>
