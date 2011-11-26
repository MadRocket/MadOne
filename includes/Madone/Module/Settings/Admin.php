<?

class Madone_Module_Settings_Admin extends Madone_Module {

    function handleAjaxRequest( $uri ) {
        try {

            $names = Madone_Utilites::getUriPathNames( $uri );

            switch( $names[0] ) {
                case 'module':
                    $vars = Madone_Utilites::vars();

                    $user = Madone_Session::getInstance()->getUser();
                    $user->setting_module = (int)$vars['module'] > 0 ? (int)$vars['module'] : null;
                    $user->save();
                    
                    Madone_Session::getInstance()->reloadData();

                    return json_encode( array( 'success' => true ) );
            }
        }
        catch( Exception $e ) {
            return json_encode( array( 'success' => false, 'message' => $e->getMessage() ) );
        }

        return null;
    }
}

?>