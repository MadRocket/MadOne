<?
class MadoneGuestbookImage extends StormModel {
	static function definition() {
		return array(
			'record'	=> new StormFkDbField( array( 'model' => 'MadoneGuestbookRecord', 'related' => 'photos' ) ),
			'image' 	 => new StormImageDbField( array(
				'path' => "/upload/images/showcase",
				'variants' => array(
					'large' => array( 'width' => 600, 'height' => 600 ),
					'small' => array( 'height' => 120 ),
					'cms'   => array( 'width' => 120, 'height' => 120, 'spacefill'=> true ),
				),
			) ),
			'position' => new StormIntegerDbField(),
		);
	}
	
	function afterSave( $new ) {
		if( $new && ! $this->position ) {
			$last = $this->getQuerySet()->filter( array( 'id__ne' => $this->id, 'record' => $this->record ) )->orderDesc( 'position' )->first();
			$this->position = $last ? $last->position + 1 : 1;
			$this->hiddenSave();
		}
	}
}
?>
