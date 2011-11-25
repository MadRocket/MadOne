<?
/**
Бинарный оператор (два аргумента)
*/

class Storm_Qc_Op_Binary extends Storm_Qc_Op
{
    protected $left;    // Левый операнд
    protected $right;   // Правый операнд
    protected $sqlop;

    /**
        Конструктор
    */
    function __construct( Storm_Qc_Op $left, Storm_Qc_Op $right )
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
        Получение SQL
    */
    function getStormQueryParts( $model )
    {
        if( $this->left instanceof Storm_Qc_Op_Empty ) { return $this->right->getStormQueryParts( $model ); }
        if( $this->right instanceof Storm_Qc_Op_Empty ) { return $this->left->getStormQueryParts( $model ); }
    
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
