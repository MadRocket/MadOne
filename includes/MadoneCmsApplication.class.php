<?

class MadoneCmsApplication
{
    function __construct()
    {
        // Пользователя сессии нет
        if (!MadoneSession::getInstance()->getUser()) {
            $vars = Mad::vars();

            // Проверим, не переданы ли переменные из формы авторизации, и авторизуем пользователя, если это так
            if (array_key_exists('_madone_login', $vars) && array_key_exists('_madone_password', $vars)) {
                // Авторизуем
                if (MadoneSession::getInstance()->login($vars['_madone_login'], $vars['_madone_password'])) {
                    // Запрос, чтобы получить uri cms
                    $request = new MadoneCmsRequest();

                    // Если в настройках есть модуль по умолчанию, и произведен вход в корень сайта — редиректим
                    if (Mad::getUriPath() == $request->getCmsUri() &&
                        MadoneSession::getInstance()->getUser()->setting_module &&
                        MadoneSession::getInstance()->getUser()->setting_module->name
                    ) {
                        header("Location: {$request->getCmsUri()}/" . MadoneSession::getInstance()->getUser()->setting_module->name . '/', true);
                        exit;
                    }
                }
            }
        }

        // Включаем нужный язык Storm
        if (MadoneSession::getInstance()->language) {
            try {
                StormCore::setLanguage(MadoneSession::getInstance()->language);
            }
            catch (StormException $e) {
                // При установке языка произошла ошибка, следовательно язык плохой, инициализируем его языком по умолчанию
                MadoneSession::getInstance()->language = StormCore::getLanguage();
            }
        }
    }

    /**
    Точка входа приложения
     */
    function run()
    {
        // Запрос к административному разделу, он знает что к чему
        $request = new MadoneCmsRequest();

        // Проверим авторизованность пользователя, выдадим соответствующий ответ при отсутствии сессии
        if (!MadoneSession::getInstance()->getUser()) {
            if ($request->getType() == MadoneCmsRequest::MODULE_AJAX || $request->getType() == MadoneCmsRequest::MODEL) {
                print json_encode(array('success' => false, 'message' => 'Вы не авторизованы, или слишком долго бездейстовали. Пожалуйста, обновите страницу, Вам будет предложено ввести имя и пароль.'));
            }
            else {
                $twig = Madone::twig("{$_SERVER['DOCUMENT_ROOT']}/includes/template/admin");
                $twig->loadTemplate('login-page.twig')->display(
                    array(
                        'login_attempt' => MadoneSession::getInstance()->getLoginAttempt(),
                        '_madone_login' => Mad::vars('_madone_login')
                    )
                );
            }
            exit;
        }

        // Обрабатываем переключение языка
        if ($request->getType() == MadoneCmsRequest::LANG_SWITCH) {
            try {
                StormCore::setLanguage($request->getObjectName());
                MadoneSession::getInstance()->language = StormCore::getLanguage()->getName();
            }
            catch (StormException $e) {
            }
            $location = array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : $request->getCmsUri();
            header("Location: {$location}", true);
            exit;
        }

        // Обрабатываем MODEL
        else {
            if ($request->getType() == MadoneCmsRequest::MODEL) {
                $processor = new StormRestProcessor();
                print $processor->process($request->getObjectName(), $request->getUri(), Mad::vars());
                exit;
            }
        }

        // Остались MODULE запросы. Получим активный модуль
        $module = $this->getModuleByName($request->getObjectName());

        // Имя есть, а модуль не нашелся? Непорядок, выдаем 404!
        if ($request->getObjectName() && !$module) {
            Madone::show404(Mad::getUriPath());
            return false;
        }

        // В зависимости от типа запроса вызываем соответствующий обработчик
        if ($request->getType() == MadoneCmsRequest::MODULE_AJAX) {
            // ajax-запрос
            print $module ? $module->handleAjaxRequest($request->getUri()) : Madone::show404(Mad::getUriPath());
        }
        else {
            // Интерфейсный запрос — рисуем его в нашем интерфейсе :3
            $vars = array();

            $vars['menuItems'] = array();
            $vars['module'] = $request->getObjectName();
            $vars['cmsUri'] = $request->getCmsUri();

            // Получим модули системы, сформируем из них элементы меню
//            foreach (MadoneModules(array('enabled' => 1))->order('position')->all() as $m) {
//                $vars['menuItems'][] = array(
//                    'title' => $m->title,
//                    'uri' => "{$request->getCmsUri()}/{$m->name}/",
//                    'selected' => $m->name == $request->getObjectName()
//                );
//
//                if ($m->name == $request->getObjectName()) {
//                    $vars['title'] = $m->title;
//                }
//            }
            $modules_dir = "{$_SERVER['DOCUMENT_ROOT']}/includes/module";
            foreach(scandir($modules_dir) as $dir) {
                if($dir == '..' || $dir == '.') {
                    continue;
                }

                if(is_dir("{$_SERVER['DOCUMENT_ROOT']}/includes/module/{$dir}") && class_exists("Module_".ucfirst($dir)."_Service")) {
                    $serviceClass = "Module_".ucfirst($dir)."_Service";

                    $vars['menuItems'][] = array(
                        'title' => $serviceClass::getTitle(),
                        'uri' => "{$request->getCmsUri()}/{$dir}/",
                        'selected' => $dir == $request->getObjectName()
                    );
                }
            }

            // Если есть активный модуль — рендерим его содержимое, иначе — welcome-страница
            if (!$module) {
                $module = new Module_Dashboard_Service("/");
            }

            // Проверим наличие title, если нет — выбран встроенный модуль, и тайтл тоже нужно приготовить самостоятельно
            if (! array_key_exists('title', $vars)) {
                switch ($request->getObjectName()) {
                    case 'settings':
                        $vars['title'] = 'Настройка';
                        break;
                    case 'password':
                        $vars['title'] = 'Cмена пароля';
                        break;
                    case 'help':
                        $vars['title'] = 'Помощь';
                        break;
                    default:
                        $vars['title'] = 'Панель управления';
                        break;
                }
            }

            // Позволим модулю обработать запрос и вернуть контент
            $vars['content'] =  $module->handleHtmlRequest( $request->getUri() );

            $twig = Madone::twig(array(
                "{$_SERVER['DOCUMENT_ROOT']}/includes/template/admin"
            ));
            print $twig->render('default.twig', $vars);
        }

        return true;
    }

    /**
    Получение модуля по имени
    $name - имя модуля
    Возвращает объект-модуль или null, если таковой не обнаружен
     */
    protected function getModuleByName($name)
    {
        // Сначала проверяем встроенные модули административного интерфейса
        switch ($name) {
            case 'logout':
                return new LogoutModule($name);

            case 'settings':
                return new SettingsModule($name);

            case 'password':
                return new PasswordModule($name);

            case 'help':
                return new HelpModule($name);
        }

        if( class_exists("Module_".ucfirst($name)."_Admin") ) {
            $module = "Module_".ucfirst($name)."_Admin";
            return new $module($name);
        }

        else {
            return MadoneModules(array('name' => $name, 'enabled' => true))->first();
        }
    }
}

?>