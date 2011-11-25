<?
class MadoneGuestbookRecord extends Storm_Model {
    static function definition() {
		return array(
			'name'		=> new Storm_Db_Field_Char( array( 'localized' => false, 'maxlength' => 255, 'default' => 'Анонимный посетитель' ) ),
			'email'		=> new Storm_Db_Field_Char( array( 'localized' => false, 'maxlength' => 255 ) ),
			'text'		=> new Storm_Db_Field_Text( array( 'localized' => false ) ),
			'date'		=> new Storm_Db_Field_Datetime( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
			'enabled'	=> new Storm_Db_Field_Bool( array( 'default' => 1 ) ),
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
