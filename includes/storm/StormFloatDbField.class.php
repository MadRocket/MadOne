<?
/**
    ���� ���� float
*/
class StormFloatDbField extends StormDbField
{
    /**
     * setValue function.
     * ��������� �������� ����
     * @access public
     * @param mixed $string
     * @return void
     */
    function setValue( $string, $language = null )
    {
        if( ! is_null( $string ) )
        {
        
            // ��������� ��������, ��������� �������� �������� exception
            if( ! is_numeric( $string ) )
            {
                throw new StormValidationException( "'$string' is not valid float value", $this );
            }

            return parent::setValue( (float)$string, $language );
        }
        else
        {
            return parent::setValue( $string, $language );
        }
    }
}

?>
