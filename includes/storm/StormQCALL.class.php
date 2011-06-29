<?
/**
Класс полной выборки
*/

class StormQCALL extends StormQCOp
{
    public $optree;

    function __construct( )
    {
        $this->optree = new StormQCEmptyOp();
    }

    function getStormQueryParts( $model )
    {
        return $this->optree->getStormQueryParts( $model );
    }
}


?>
