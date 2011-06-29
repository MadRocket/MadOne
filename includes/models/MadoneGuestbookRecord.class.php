<?
class MadoneGuestbookRecord extends StormModel {
    static function definition() {
		return array(
			'name'		=> new StormCharDbField( array( 'localized' => false, 'maxlength' => 255, 'default' => 'Анонимный посетитель' ) ),
			'email'		=> new StormCharDbField( array( 'localized' => false, 'maxlength' => 255 ) ),
			'text'		=> new StormTextDbField( array( 'localized' => false ) ),
			'date'		=> new StormDatetimeDbField( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
			'enabled'	=> new StormBoolDbField( array( 'default' => 1 ) ),
		);
    }

    function beforeSave() {
		if( ! $this->name ) {
    		$this->name = 'Анонимный посетитель';
    	}
    }
    
    function beforeDelete() {
		foreach( $this->photos->all() as $i ) {
    		$i->delete();
    	}
    }
}
?>
