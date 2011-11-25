<?
class MadoneGuestbookImage extends Storm_Model {
	static function definition() {
		return array(
			'record'	=> new Storm_Db_Field_Fk( array( 'model' => 'MadoneGuestbookRecord', 'related' => 'photos' ) ),
			'image' 	 => new Storm_Db_Field_File_Image( array(
				'path' => "/upload/images/showcase",
				'variants' => array(
					'large' => array( 'width' => 600, 'height' => 600 ),
					'small' => array( 'height' => 120 ),
					'cms'   => array( 'width' => 120, 'height' => 120, 'spacefill'=> true ),
				),
			) ),
			'position' => new Storm_Db_Field_Integer(),
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
