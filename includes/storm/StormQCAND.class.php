<?
/**
Класс AND
*/

class StormQCAND extends StormQC
{
    function __construct( StormQCOp $left, StormQCOp $right )
    {
        $this->optree = new StormQCAndOp( $left->optree, $right->optree );
    }
}


?>
