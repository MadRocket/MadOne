<?
/**
    Встроенный модуль деавторизации.
    Просто сбрасывает сессию и редиректит в корень cms
*/
class Madone_Module_Auth_Admin extends Madone_Module {
    public function handleHtmlRequest() {
        Madone_Session::getInstance()->logout();
        header( "Location: /admin/", true );
        exit;
    }
}

?>