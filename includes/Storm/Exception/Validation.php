<?

class Storm_Exception_Validation extends Storm_Exception
{
    public $field = null;
    
    function __construct( $message, Storm_Db_Field $field = null )
    {
        if( $field ) $this->field = $field;

        parent::__construct( $message );
    }

}

?>
