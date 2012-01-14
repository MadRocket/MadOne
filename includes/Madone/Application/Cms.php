<?php

class Madone_Application_Cms
{
    protected $container;

    function __construct($container)
    {
        $this->container = $container;

        // Пользователя сессии нет
        if (!Madone_Session::getInstance()->getUser()) {
            $vars = Madone_Utilites::vars();

            // Проверим, не переданы ли переменные из формы авторизации, и авторизуем пользователя, если это так
            if (array_key_exists('_madone_login', $vars) && array_key_exists('_madone_password', $vars)) {
                // Авторизуем
                if (Madone_Session::getInstance()->login($vars['_madone_login'], $vars['_madone_password'])) {
                    // Запрос, чтобы получить uri cms
                    $request = new Madone_Request_Cms();

                    // Если в настройках есть модуль по умолчанию, и произведен вход в корень сайта — редиректим
                    if (Madone_Utilites::getUriPath() == $request->getCmsUri() &&
                            Madone_Session::getInstance()->getUser()->setting_module &&
                            Madone_Session::getInstance()->getUser()->setting_module->name
                    ) {
                        header("Location: {$request->getCmsUri()}/" . Madone_Session::getInstance()->getUser()->setting_module->name . '/', true);
                        exit;
                    }
                }
            }
        }

        // Включаем нужный язык Storm
        if (Madone_Session::getInstance()->language) {
            try {
                Storm_Core::setLanguage(Madone_Session::getInstance()->language);
            }
            catch (Storm_Exception $e) {
                // При установке языка произошла ошибка, следовательно язык плохой, инициализируем его языком по умолчанию
                Madone_Session::getInstance()->language = Storm_Core::getLanguage();
            }
        }
    }

    /**
     * Точка входа приложения
     * @return bool
     */
    function run()
    {
        // Запрос к административному разделу, он знает что к чему
        $request = new Madone_Request_Cms();

        // Проверим авторизованность пользователя, выдадим соответствующий ответ при отсутствии сессии
        if (!Madone_Session::getInstance()->getUser()) {
            if ($request->getType() == Madone_Request_Cms::MODULE_AJAX || $request->getType() == Madone_Request_Cms::MODEL) {
                print json_encode(array('success' => false, 'message' => 'Вы не авторизованы, или слишком долго бездейстовали. Пожалуйста, обновите страницу, Вам будет предложено ввести имя и пароль.'));
            }
            else {
                $twig = $this->container['template'];
                $twig->getLoader()->setPaths( array_merge(array("{$_SERVER['DOCUMENT_ROOT']}/includes/template/admin"), $twig->getLoader()->getPaths()) );
                $twig->loadTemplate('login-page.twig')->display(
                    array(
                        'login_attempt' => Madone_Session::getInstance()->getLoginAttempt(),
                        '_madone_login' => Madone_Utilites::vars('_madone_login')
                    )
                );
            }
            exit;
        }

        // Обрабатываем переключение языка
        if ($request->getType() == Madone_Request_Cms::LANG_SWITCH) {
            try {
                Storm_Core::setLanguage($request->getObjectName());
                Madone_Session::getInstance()->language = Storm_Core::getLanguage()->getName();
            }
            catch (Storm_Exception $e) {
            }
            $location = array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : $request->getCmsUri();
            header("Location: {$location}", true);
            exit;
        }

        // Обрабатываем MODEL
        else {
            if ($request->getType() == Madone_Request_Cms::MODEL) {
                $processor = new Storm_Processor();
                print $processor->process($request->getObjectName(), $request->getUri(), Madone_Utilites::vars());
                exit;
            }
        }

        // Остались MODULE запросы. Получим активный модуль
        $module_name = $request->getObjectName() ? $request->getObjectName() : 'dashboard';
        $module = $this->getModuleByName($module_name);

        // Имя есть, а модуль не нашелся? Непорядок, выдаем 404!
        if ($request->getObjectName() && !$module) {
            $this->container['response']->setContent( $this->container['template']->render('404.twig') );
            $this->container['response']->setStatusCode(404, "Страница не найдена");
            $this->container['response']->send();

            return false;
        }

        // В зависимости от типа запроса вызываем соответствующий обработчик
        if ($request->getType() == Madone_Request_Cms::MODULE_AJAX) {
            // ajax-запрос
            print $module ? $module->handleAjaxRequest($request->getUri()) : Madone_Core::show404(Madone_Utilites::getUriPath());
        }
        else {
            // Интерфейсный запрос — рисуем его в нашем интерфейсе :3
            $vars = array();

            $vars['menuItems'] = array();
            $vars['module'] = $request->getObjectName();
            $vars['cmsUri'] = $request->getCmsUri();

            // Получим модули системы, сформируем из них элементы меню
            foreach (Model_Modules(array('enabled' => 1))->order('title')->all() as $m) {
                $vars['menuItems'][] = array(
                    'title' => $m->title,
                    'uri' => "{$request->getCmsUri()}/{$m->name}/",
                    'selected' => $m->name == $request->getObjectName()
                );

                if ($m->name == $request->getObjectName()) {
                    $vars['title'] = $m->title;
                }
            }

            // Если есть активный модуль — рендерим его содержимое, иначе — welcome-страница
            if (!$module) {
                $module = new Madone_Module_Dashboard_Admin("/");
            }

            // Проверим наличие title, если нет — выбран встроенный модуль, и тайтл тоже нужно приготовить самостоятельно
            if (!array_key_exists('title', $vars)) {
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
            $vars['content'] = $module->respond($request->getUri());

            $twig = $this->container['template'];
            $twig->getLoader()->setPaths( array_merge(array("{$_SERVER['DOCUMENT_ROOT']}/includes/template/admin"), $twig->getLoader()->getPaths()) );
            print $twig->render('default.twig', $vars);
        }

        return true;
    }

    /**
     * Получение модуля по имени
     * @param $name - имя модуля
     * @return Madone_Module|null
     */
    protected function getModuleByName($name)
    {
        // Сначала проверяем встроенные модули административного интерфейса
        switch ($name) {
            case 'logout':
                return new Madone_Module_Auth_Admin($name, $this->container);

            case 'settings':
                return new Madone_Module_Settings_Admin($name, $this->container);

            case 'password':
                return new Madone_Module_Password_Admin($name, $this->container);
        }

        $classname = "Madone_Module_" . (ucfirst($name)) . "_Admin";
        if (class_exists($classname)) {
            return new $classname($name, $this->container);
        }
        else {
            // TODO: 404 ERROR
            // return Model_Modules(array('name' => $name, 'enabled' => true))->first();
        }
    }
}
