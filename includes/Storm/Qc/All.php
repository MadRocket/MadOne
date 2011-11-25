<?
/**
Класс полной выборки
*/

class Storm_Qc_All extends Storm_Qc_Op
{
    public $optree;

    function __construct( )
    {
        $this->optree = new Storm_Qc_Op_Empty();
    }

    function getStormQueryParts( $model )
    {
        return $this->optree->getStormQueryParts( $model );
    }
}


?>
