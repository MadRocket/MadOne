<?

class SettingsModule extends Madone_Module {

    function handleAjaxRequest( $uri ) {
        try {

            $names = Mad::getUriPathNames( $uri );

            switch( $names[0] ) {
                case 'module':
                    $vars = Mad::vars();

                    $user = MadoneSession::getInstance()->getUser();
                    $user->setting_module = (int)$vars['module'] > 0 ? (int)$vars['module'] : null;
                    $user->save();
                    
                    MadoneSession::getInstance()->reloadData();

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