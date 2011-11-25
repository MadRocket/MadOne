<?
/**
����� NOT
*/

class Storm_Qc_Not extends Storm_Qc_Op
{
    function __construct( Storm_Qc_Op $op )
    {
        $this->optree = new Storm_Qc_Op_Not( $op->optree );
    }
}


?>
