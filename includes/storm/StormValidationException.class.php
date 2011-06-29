<?

class StormValidationException extends StormException
{
    public $field = null;
    
    function __construct( $message, StormDbField $field = null )
    {
        if( $field ) $this->field = $field;

        parent::__construct( $message );
    }

}

?>
