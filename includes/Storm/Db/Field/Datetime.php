<?
/**
    Поле типа varchar
*/
class Storm_Db_Field_Datetime extends Storm_Db_Field
{
	protected $valueClassname = 'Storm_Db_Field_Value_Datetime'; // Имя класса значений

    private function parseValue( $value )
    {
        if( ! is_null( $value ) )
        {
            if( is_numeric( $value ) ) return (int) $value;
            
            if( ( $time = strtotime( $value ) ) !== false ) return $time;
            
            throw new Storm_Exception_Validation( "Value '{$value}' is not valid Datetime value" );
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
