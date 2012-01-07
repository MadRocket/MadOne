<?

/**
Штормоядро!
Штуки, нужные для слаженной работы всех механизмов — связи между моделями, настройки соединения с базой и прочего.
Singleton, экземпляр можно получить вызовом StormCore::getInstance();
 */

class Storm_Core
{
    private static $instance = null; // Для реализации singleton
    private $utilities_registered = false;

    private $related; // массив связанных полей моделей
    private $metadata = array(); // массив метаданных моделей

    private $models; // массив зарегистрированных моделей
    private $querysets; // массив названий querysetов для моделей, ключ - имя модели

    private $backend;
    /**
     * @var Storm_Db_Mapper
     */
    private $mapper;

    /**
     * @var \Storm_Language
     */
    private $language; // текущий язык, включенный в ядре. Влияет на выборку и сохранение данных.
    private $languages; // массив доступных языков

    /************
    Общедоступные штуки.
     *********************/

    static public function load($path, $models, $locales)
    {
        // Настраиваем конфигурацию
        Storm_Config::$db_backend = "Storm_Db_Connection_" . Madone_Config::$Db['db_backend'];
        Storm_Config::$db_mapper = "Storm_Db_Mapper_" . Madone_Config::$Db['db_backend'];
        Storm_Config::$db_host = Madone_Config::$Db['db_host'];
        Storm_Config::$db_port = Madone_Config::$Db['db_port'];
        Storm_Config::$db_name = Madone_Config::$Db['db_name'];
        Storm_Config::$db_user = Madone_Config::$Db['db_user'];
        Storm_Config::$db_password = Madone_Config::$Db['db_password'];
        Storm_Config::$db_charset = Madone_Config::$Db['db_charset'];
        Storm_Config::$db_prefix = Madone_Config::$Db['db_prefix'];
        Storm_Config::$db_debug = Madone_Config::$Db['db_debug'];

        // Доступные локали
        Storm_Config::$locales = $locales;

        // Модели
        Storm_Config::$models = $models;

        // Регистрируем утилиты, и собственно этим и запускаем Storm в работу
        self::getInstance()->registerUtilities();
    }

    /**
    Синхронизация базы данных
     */
    static public function sync()
    {
        return self::getInstance()->syncdb();
    }

    /**
     * Доступ к объекту-синглтону
     * @static
     * @return Storm_Core
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Storm_Core();
        }

        return self::$instance;
    }

    /**
     * @static Утилита — получение текущего бэкенда БД
     * @return mixed
     */
    public static function getBackend()
    {
        return self::getInstance()->backend;
    }

    /**
    Получение текущего маппера БД
     */
    public static function getMapper()
    {
        return self::getInstance()->mapper;
    }

    /**
     *    Установка текущего языка Storm.
     *    Возвращает true или выбрасывает исключение.
     */
    public static function setLanguage(Storm_Language $language)
    {
        if ($language instanceof Storm_Language) {
            $language = $language->getName();
        }
        if (array_key_exists($language, self::getInstance()->languages)) {
            self::getInstance()->language = self::getInstance()->languages[$language];
            return true;
        }
        throw new Storm_Exception("Неизвестный язык '{$language}'");
    }

    /**
     * Получение текущего языка Storm.
     * @static
     * @throws Storm_Exception
     * @param null $name
     * @return Storm_Language
     */
    public static function getLanguage($name = null)
    {
        if (!$name) {
            return self::getInstance()->language;
        }
        if (array_key_exists($name, self::getInstance()->languages)) {
            return self::getInstance()->languages[$name];
        }
        throw new Storm_Exception("Неизвестный язык '{$name}'");
    }

    /**
     * Получение списка доступных языков Storm. Возвращает массив Storm_Language.
     * @static
     * @return array Storm_Language
     */
    public static function getAvailableLanguages()
    {
        return self::getInstance()->languages;
    }

    /*******************************
    Системные методы — обеспечивают работу Storm как единого целого
     ********************************/

    /**
    Приватный конструктор — извне невозможно сконструировать экземпляр объекта.
     */
    private function __construct()
    {
        // Получим backend
        $this->backend = new Storm_Config::$db_backend(array
        (
            'host' => Storm_Config::$db_host,
            'port' => Storm_Config::$db_port,
            'name' => Storm_Config::$db_name,
            'user' => Storm_Config::$db_user,
            'password' => Storm_Config::$db_password,
            'charset' => Storm_Config::$db_charset,
        ));

        // Получим mapper
        $this->mapper = new Storm_Config::$db_mapper();

        // Зарегистрируем все известные из конфига модели
        foreach (Storm_Config::$models as $def)
        {
            if (is_array($def)) {
                $classname = $def[0];
                $querysetname = $def[1];
            }
            else
            {
                $classname = $def;
                $querysetname = "{$def}s";
            }

            // Запомним имя модели и querysetа
            $this->models[] = $classname;
            $this->querysets[$classname] = $querysetname;
        }

        // Сделаем пустой массив related
        $this->related = array();

        foreach ($this->models as $classname) {
            // Получим связи типа один-ко-многим, и сложим их в наше поле related.
            // Ключи в этом поле — модель, содержащая записи-ключи (one)
            foreach ($this->getStormOneToManyRelations($classname) as $relation) {
                $this->related[$relation->key_model][] = $relation;
            }
        }


        // Починим список локалей, если он не указан
        if (!is_array(Storm_Config::$locales)) {
            Storm_Config::$locales = array('ru_RU.UTF-8');
        }
        // Заполним список языков, выберем первый в качестве текущего
        $this->languages = array();
        foreach (Storm_Config::$locales as $locale) {
            $language = new Storm_Language($locale);
            $this->languages[$language->getName()] = $language;
            if (!$this->language) {
                $this->language = $language;
            }
        }
    }

    /**
    Приватная функция клонирования — клонирование недоступно извне
     */
    private function __clone()
    {

    }

    /**
    Проверка наличия модели в списках штормоядра
     */
    function checkModel($classname)
    {
        if (!in_array($classname, $this->models)) {
            throw new Storm_Exception("'{$classname}' model is not known by Storm_Core. Please add it to the \$models property of StormConfig class.");
        }
    }

    /**
    Получение метаданных модели
    Возвращает массив метаданных так, как он выглядит в свежесозданном экземпляре модели
     */
    /**
     * @param $classname
     * @return Storm_Model_Metadata
     */
    public function getStormModelMetadata($classname)
    {
        // Проверим, нет ли у нас готовой копии метаданных для этой модели
        if (!array_key_exists($classname, $this->metadata) || !$this->metadata[$classname]) {
            //Данных нет, их нужно получить
            $instance = new $classname();

            $this->metadata[$classname] = $instance->meta;
        }

        return $this->metadata[$classname];
    }

    /**
     * Получение списка связей типа один-ко-многим, определенных заданной моделью
     * Возвращает массив объектов
     * @param $classname
     * @return array Storm_Relation_Onetomany
     */
    private function getStormOneToManyRelations($classname)
    {

        // Получим поля модели
        $definition = call_user_func(array($classname, 'definition'));

        $relations = array();

        // Идем по полям циклом
        foreach ($definition as $fieldname => $fieldobject)
        {
            // Поле — ForeignKey?
            if ($fieldobject instanceof Storm_Db_Field_Fk) {
                $relations[] = new Storm_Relation_Onetomany($fieldobject->model, $fieldname, $classname, $fieldobject->related);
            }
        }

        return $relations;
    }

    /**
    Получение списка внешних связей модели.
    Возвращает массив, каждый элемент содержит ключи model, field и related.
    Возвращает пустой массив, если связей нет.
     */
    function getRelatedModels($class)
    {
        if (array_key_exists($class, $this->related) && is_array($this->related[$class]) && count($this->related[$class]) > 0) {
            return $this->related[$class];
        }

        return array();
    }

    /**
    Получение параметров для внешних вызовов
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return null;
    }

    /**
    Синхронизация базы данных
    Принимает массив имен моделей, которые следует синхронизировать
    Выполняется со следующими ограничениями:
    1. Отсутствующие модели создаются.
    2. Отсутствующие поля существующих моделей создаются.
    3. Тип полей не проверяется вообще, нужно пересоздать поле - удаляй его из БД и синхронизируй еще раз.
    4. Индексы создаются автоматически, но не удаляются.
     */
    private function syncdb()
    {
        // Прогоним событие «перед синхронизацией модели»
        $this->triggerFieldHandler('beforeSync');

        // Получим список имеющихся в БД таблиц
        $tables = $this->mapper->getTableList($this->backend->cursor);

        // Пройдемся по списку моделей и создадим их
        foreach ($this->models as $model)
        {
            $table = $this->mapper->getModelTable($model);

            // Проверим наличие таблицы
            if (array_search($table, $tables) !== false) {
                // Получим колонки базы данных (которые уже есть) и колонки модели (которые должны быть)
                $dbColumns = $this->mapper->getColumnList($this->backend->cursor, $model);
                $modelColumns = $this->mapper->getModelColumnList($model);

                // Сверим колонки и создадим отсутствующие
                foreach ($modelColumns as $column => $definitionSql) {
                    if (!in_array($column, $dbColumns)) {
                        $this->backend->cursor->execute($definitionSql);
                    }
                }
            }
            else
            {
                // Таблицы нет - создадим её
                $this->backend->cursor->execute($this->mapper->getTableCreationSql($model));
            }
        }

        // Пройдемся по списку моделей и создадим индексы
        foreach ($this->models as $model)
        {
            // Получим желаемые и имеющиеся индексы
            $desired = $this->mapper->getModelIndexes($model);
            $existing = $this->mapper->getIndexList($this->backend->cursor, $model);

            // Сверяем все циклом
            foreach ($desired as $idx)
            {
                // Несуществующее создаем
                if (!array_key_exists($idx->getName(), $existing)) {
                    $this->backend->cursor->execute($this->mapper->getIndexCreationSql($idx));
                }
            }
        }
    }

    function triggerFieldHandler($handler)
    {
        foreach ($this->models as $model) {
            foreach ($this->getStormModelMetadata($model)->getFields() as $field) {
                $field->beforeSync();
            }
        }
    }

    /**
    Создание функций для быстрого доступа к моделям, StormQueryCheck-ам и прочим прелестям.
    В этом методе все является читерством и извращением в той или иной степени, но что делать? Унылый PHP уныл.
     */
    function registerUtilities()
    {
        if (!$this->utilities_registered) {
            // Быстрое создание Storm_Qc
            function Q($params)
            {
                return new Storm_Qc($params);
            }

            function QOR($left, $right)
            {
                return new Storm_Qc_Or($left, $right);
            }

            function QAND($left, $right)
            {
                return new Storm_Qc_And($left, $right);
            }

            function QNOT($op)
            {
                return new Storm_Qc_Not($op);
            }

            // Быстрое создание Storm_Queryset
            function Storm_Queryset($model)
            {
                return (class_exists($model) && is_subclass_of($model, 'Storm_Model_Tree')) ? new Storm_Queryset_Tree($model) : new Storm_Queryset($model);
            }

            // Утилиты для получения моделей и их Storm_Queryset-ов
            if (Storm_Config::$dev || !is_file(__DIR__ . '/bootstrap.php')) {
                // В режиме разработки создаем библиотеку быстрых функций
                $bootstrap = fopen(__DIR__ . '/bootstrap.php', 'w');
                $code = '<?php ';
                foreach ($this->querysets as $model => $set)
                {
                    // Определим класс Storm_Queryset-а для этой модели
                    $queryset = (class_exists($model) && is_subclass_of($model, 'Storm_Model_Tree')) ? 'Storm_Queryset_Tree' : 'Storm_Queryset';

                    $code .=
                            "
	                function {$model}( \$params = null )
	                {
	                    return \$params ? new {$model}( \$params ) : new {$model}();
	                }

	                function {$set}( \$params = null )
	                {
	                    \$qs = new {$queryset}( '{$model}' );
	                    return \$params ? \$qs->filter( \$params ) :  \$qs;
	                }
	                ";
                }
                fwrite($bootstrap, $code);
            }
            require_once __DIR__ . '/bootstrap.php';

            $this->utilities_registered = true;
        }
    }
}

?>
