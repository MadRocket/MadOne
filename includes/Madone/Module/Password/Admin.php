<?

class Madone_Module_Password_Admin extends Madone_Module
{

    function handleAjaxRequest($uri)
    {
        try {
            $vars = Madone_Utilites::vars();

            Madone_Session::getInstance()->reloadData();

            $user = Madone_Session::getInstance()->getUser();

            if ($user->password != md5($vars['old_password'])) {
                throw new Exception('Неправильный старый пароль.');
            }

            else {
                $user->password = md5($vars['new_password']);
                $user->save();
                Madone_Session::getInstance()->reloadData();
            }

            return json_encode(array('success' => true));
        }
        catch (Exception $e) {
            return json_encode(array('success' => false, 'message' => $e->getMessage()));
        }
    }
}

?>