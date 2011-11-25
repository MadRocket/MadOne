<?
/**
    Текстовый блок сайта
*/

class MadoneTextBlock extends Storm_Model
{
    static private $preview_length = 100;

    static function definition()
    {
        return array
        (
            'name'         => new Storm_Db_Field_Char( array( 'localized'=> false, 'maxlength' => 255, 'null' => false, 'index' => true ) ),
        	'text'         => new Storm_Db_Field_Text(),
        	'preview'      => new Storm_Db_Field_Char( array( 'maxlength' => self::$preview_length ) ),
            'enabled'      => new Storm_Db_Field_Bool( array( 'default' => 1, 'index' => true ) ),
        );
    }
    
    function beforeSave()
    {
        if( MadoneTextBlocks( array( 'name' => $this->name, 'id__ne' => $this->id ) )->count() )
        {
            throw new Storm_Exception_Validation( "Name '{$this->name}' is already in use", $this->meta->getField( 'name' ) );
        }
        $this->preview = Mad::getTextPreview( strip_tags( $this->text ), self::$preview_length );
    }
    
    function __toString()
    {
        return (string) ( $this->enabled ? $this->text : null );
    }
}

?>
