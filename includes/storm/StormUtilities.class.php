<?

class StormUtilities
{
    /*
    Заполняет ссылки вида %{foo} %{bar} значениями ['foo'] и ['bar'] из переданного массива.
    Ссылки вида %s заполняются значениями [0], [1], [2] ... по порядку из переданного массива.
    При отсутствии нужного элемента массива оставляет соответствующие ссылки нетронутыми.
    Возвращает обработанную строку
    */
    static function array_printf( $str = '', $vars = array() )
    {
        if( ! $str ) return '';
        
        if( count( $vars ) > 0 )
        {
            foreach( $vars as $k => $v )
            {
                if( is_numeric( $k ) )
                {
                    $str = preg_replace( '/%s/', $v, $str, 1 );
                }
                else
                {
                    $str = str_replace( "%{{$k}}", $v, $str );
                }
            }
        }
    
        return $str;
    }
    
}

?>
