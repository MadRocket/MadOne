<?
/**
 *	$cal = new MadCalendar( $time );
 *	$events = MadoneEvents( array( 'date__ge' => $cal->begin, 'date__le' => $cal->end, 'enabled' => true ) )->all();
 *	$cal->attachObjects( $events, 'date' );
 */
class MadCalendar {
	public $weeks;
	public $begin;
	public $end;
	
	protected $dayMap;

	function __construct( $time = null ) {
		if( ! $time ) {
			$time = time();
		}

		$month = date( 'n', $time );
		$year = date( 'Y', $time );
		$firstDay = mktime( 0, 0, 0, $month, 1, $year );
		
		// Определим количество дней до первого понедельника месяца
		$daysBeforeMonday = date( 'N', $firstDay ) - 1;
		
		// Сгенерируем стартовую дату календаря — первый день месяца и назад до понедельника :3
		$start = strtotime( strftime( "%F %T - {$daysBeforeMonday} days", $firstDay ) );
		
		// Посчитаем количество недель в календаре
		$dayCount = date( 't', $time );
		$lastDay = mktime( 23, 59, 59, $month, $dayCount, $year );
		
		$rowCount = ceil( ( $dayCount + $daysBeforeMonday ) / 7 );
		
		// Нагенерируем недели
		$this->weeks = array();
		$this->dayMap = array();
		
		for( $date = $start, $row = 0; $row < $rowCount; $row++ ) {
			for( $wday = 0; $wday < 7; $wday++, $date = strtotime( "+1 day", $date ) ) {
				$key = strftime( "%F", $date );
				$this->dayMap[ $key ] = (object) array( 'date' => $date, 'day' => strftime( "%e", $date ), 'wday' => $wday + 1,  'off' => $wday >= 5, 'objects' => array(), 'extra' => ( $date < $firstDay || $date > $lastDay )   );
				$this->weeks[ $row ][ $wday ] = & $this->dayMap[ $key ];
			}
		}
		// Запомним начало и окончание		
		$this->begin = $start;
		$this->end = $date;
	}
	
	/**
	*	Прикрепление к календарю моделей из переданного массива.
	*	У каждого дня появляется поле objects, представляющее собой массив
	*/
	function attachObjects( array $objects, $dateFieldName ) {
		foreach( $objects as $obj ) {
			if( ! is_null( $obj->$dateFieldName ) ) {
				$this->dayMap[ $obj->$dateFieldName->date ]->objects[] = $obj;
			}
		}
	}
}





?>