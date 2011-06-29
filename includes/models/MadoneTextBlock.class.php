<?
/**
    Текстовый блок сайта
*/

class MadoneTextBlock extends StormModel
{
    static private $preview_length = 100;

    static function definition()
    {
        return array
        (
            'name'         => new StormCharDbField( array( 'localized'=> false, 'maxlength' => 255, 'null' => false, 'index' => true ) ),
        	'text'         => new StormTextDbField(),
        	'preview'      => new StormCharDbField( array( 'maxlength' => self::$preview_length ) ),
            'enabled'      => new StormBoolDbField( array( 'default' => 1, 'index' => true ) ),
        );
    }
    
    function beforeSave()
    {
        if( MadoneTextBlocks( array( 'name' => $this->name, 'id__ne' => $this->id ) )->count() )
        {
            throw new StormValidationException( "Name '{$this->name}' is already in use", $this->meta->getField( 'name' ) );
        }
        $this->preview = Mad::getTextPreview( strip_tags( $this->text ), self::$preview_length );
    }
    
    function __toString()
    {
        return (string) ( $this->enabled ? $this->text : null );
    }
}

?>
