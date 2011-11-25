<?
/**
����� OR
*/

class Storm_Qc_Or extends Storm_Qc
{
    function __construct( Storm_Qc_Op $left, Storm_Qc_Op $right )
    {
        $this->optree = new Storm_Qc_Op_Binary_Or( $left->optree, $right->optree );
    }
}


?>
