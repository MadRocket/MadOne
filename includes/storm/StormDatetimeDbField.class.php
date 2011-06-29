<?
/**
    Поле типа varchar
*/
class StormDatetimeDbField extends StormDbField
{
	protected $valueClassname = 'StormDatetimeDbFieldValue'; // Имя класса значений

    private function parseValue( $value )
    {
        if( ! is_null( $value ) )
        {
            if( is_numeric( $value ) ) return (int) $value;
            
            if( ( $time = strtotime( $value ) ) !== false ) return $time;
            
            throw new StormValidationException( "Value '{$value}' is not valid Datetime value" );
        }
        
        return null;
    }


    /**
        Получение значения, отформатированного для запроса
    */
    static public function getCheckOpValue( $value )
    {
        if( $value = self::parseValue( $value ) ) return strftime( '%Y-%m-%d %H:%M:%S', $value );
        return $value;
    }
}

?>
