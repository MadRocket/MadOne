<?

/**
*	Цвет. Используется полем ImageDbField.
*	Разбирает значение вида '#56f0ad' и представляет его в десятеричном виде через $color->r, $color->g, $color->b.
*/

class RGB {
	public $r, $g, $b;
	function __construct( $string ) {
		if( preg_match( '/^([0-9a-f]{1,2})([0-9a-f]{1,2})([0-9a-f]{1,2})$/i', $string, $m ) ) {
			array_shift( $m );
			foreach( $m as & $v ) {
				if( strlen( $v ) < 2 ) {
					$v = str_repeat( $v, 2 );
				}
				$v = hexdec( $v );
			}
			list( $this->r, $this->g, $this->b ) = $m;
		} else {
			throw new StormException( "Неправильный цвет: {$string}." );
		}
	}
}

?>