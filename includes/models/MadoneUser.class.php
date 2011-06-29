<?
/**
    Модуль административного интерфейса Madone
*/

class MadoneUser extends StormModel
{
    static function definition()
    {
        return array
        (
            'login'           => new StormCharDbField( array( 'localized'=> false, 'maxlength' => 50, 'null' => false, 'index' => 'login' ) ),
            'password'        => new StormCharDbField( array( 'localized'=> false, 'maxlength' => 32, 'null' => false, 'index' => 'login' ) ),
        	'setting_module'  => new StormFkDbField( array( 'model' => 'MadoneModule', 'related' => 'users' ) ),
        );
    }
    
    function beforeSave()
    {
        if( MadoneUsers( array( 'login' => $this->login, 'id__ne' => $this->id ) )->count() )
        {
            throw new StormValidationException( "Login '{$this->login}' is already in use", $this->meta->getField( 'login' ) );
        }
        
        if( ! preg_match( '/^[a-f0-9]{32}$/i', $this->password ) )
        {
            $this->password = md5( $this->password );
        }
    }
}

?>
