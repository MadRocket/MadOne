<?

class PasswordModule extends Madone_Module {

    function handleAjaxRequest( $uri ) {

        try {

            $vars = Mad::vars();

            MadoneSession::getInstance()->reloadData();
            
            $user = MadoneSession::getInstance()->getUser();
            
            if( $user->password != md5( $vars['old_password'] ) ) {
                throw new Exception( 'Неправильный старый пароль.' );
            }
            
            else {
                $user->password = md5( $vars['new_password'] );
                $user->save();
                MadoneSession::getInstance()->reloadData();
            }

            return json_encode( array( 'success' => true ) );
        }
        catch( Exception $e ) {
            return json_encode( array( 'success' => false, 'message' => $e->getMessage() ) );
        }

        return null;
    }
}

?>