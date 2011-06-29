<?
/**
 * Товар интернет-витрины
 */

class MadoneShowcaseItem extends StormModel {
    static function definition() {
        return array (
			'title'				=> new StormCharDbField( array( 'maxlength' => 255, 'default' => 'Новая позиция', 'fulltext' => true ) ),
			'section'			=> new StormFkDbField(array( 'model' => 'MadoneShowcaseSection', 'related' => 'items' ) ),
			'description'		=> new StormTextDbField( array( 'fulltext' => true ) ),
			'short_description' => new StormTextDbField(),
			'price'				=> new StormFloatDbField( array( 'index' => true ) ),
			'in_stock'			=> new StormIntegerDbField(array('default' => 0)),
			'enabled'			=> new StormBoolDbField( array( 'localized' => true, 'default' => 0, 'index' => true ) ),
			
			// Meta
			'date_added'		=> new StormDatetimeDbField( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
			'date_modified'		=> new StormDatetimeDbField( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
			'linked_items'		=> new StormTextDbField(array('localized' => false)),
			'views_counter'		=> new StormIntegerDbField(array('default' => 0)),
			'added_to_cart_counter'	=> new StormIntegerDbField(array('default' => 0)),
        );
    }

	function beforeSave() {
		$this->date_modified = time();
	}

    function beforeDelete() {
    	foreach( $this->images->all() as $i ) {
    		$i->delete();
    	}
    }
    
    function view() {
    	$this->views_counter = $this->views_counter + 1;
    	$this->hiddenSave();
    }

    function cart() {
    	$this->added_to_cart_counter = $this->added_to_cart_counter + 1;
    	$this->hiddenSave();
    }
}

?>
