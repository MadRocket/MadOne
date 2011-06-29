<?
/**
Класс NOT
*/

class StormQCNOT extends StormQCOp
{
    function __construct( StormQCOp $op )
    {
        $this->optree = new StormQCNotOp( $op->optree );
    }
}


?>
