<?
/**
    Поле типа integer
*/
class StormIntegerDbField extends StormDbField
{
    /**
        Установка значения поля
    */
    function setValue( $string, $language = null )
    {
        if( ! is_null( $string ) )
        {
        
            // Проверяем значение, нехорошее значение вызывает exception
            if( ! is_numeric( $string ) )
            {
                throw new StormValidationException( "'$string' is not valid integer value", $this );
            }

            return parent::setValue( (int)$string, $language );
        }
        else
        {
            return parent::setValue( $string, $language );
        }
    }
}

?>
