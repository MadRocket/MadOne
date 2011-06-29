<?
/**
    Встроенный модуль деавторизации.
    Просто сбрасывает сессию и редиректит в корень cms
*/
class LogoutModule extends AbstractModule {
    public function handleHtmlRequest() {
            MadoneSession::getInstance()->logout();
            header( "Location: {$this->cmsUri}/", true );
            exit;
    }
}

?>