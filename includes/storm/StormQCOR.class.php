<?
/**
Класс OR
*/

class StormQCOR extends StormQC
{
    function __construct( StormQCOp $left, StormQCOp $right )
    {
        $this->optree = new StormQCOrOp( $left->optree, $right->optree );
    }
}


?>
