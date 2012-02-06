<?php
/**
 * Ядро системы управления, отображающее сайт.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Assetic\Factory\AssetFactory;

class Madone_Core extends Pimple {
    protected $languages = array();
    protected $language = null;
    protected $langRegExp = null;
    protected $developmentMode = true;

    public function __construct() {
        $this['config'] = $this->share(function($container){
            return new Madone_Config();
        });

        $this['request'] = $this->share(function ($container) {
            return Request::createFromGlobals();
        });

        $this['response'] = $this->share(function($container) {
            return new Response( );
        });

        $this['storm_dir'] = "{$_SERVER['DOCUMENT_ROOT']}/includes/storm";

        $this['assets_dir'] = "{$_SERVER['DOCUMENT_ROOT']}/static";

        $this['template_dir'] = "{$_SERVER['DOCUMENT_ROOT']}/includes/template";
        $this['template_cache_dir'] = "{$_SERVER['DOCUMENT_ROOT']}/cache/template";

        $this['template'] = function($container) {
            $path = array("{$container['template_dir']}/_default", $container['template_dir']);

//            $twig = new Twig_Environment(new Twig_Loader_Filesystem($path), array('cache' => $container['template_cache_dir']));
            $twig = new Twig_Environment(new Twig_Loader_Filesystem($path));

            $twig->addExtension(new Twig_Extensions_Extension_Text());
            $twig->addExtension(new Twig_Extensions_Extension_Debug());

            $factory = new AssetFactory($container['assets_dir']);
            $twig->addExtension(new \Assetic\Extension\Twig\AsseticExtension( $factory ));

            $twig->addGlobal('madone', $container);
            // TODO: Twig functions <img src="{{ "/path/to/image.jpg"|image_transform("=220x400") }}" />
            // Take a look at avalanche123/AvalancheImagineBundle
    //        $twig->addFunction('image_transform', new Twig_Function_Method());
            $twig->addGlobal('madone_image_helper', new Madone_Helper_Image());

            return $twig;
        };

        $models = $this['config']->models;
        $locales = $this['config']->locales;

        Storm_Core::load($this['storm_dir'], $models, $locales);

		// Определим нужно ли влючать режим разработки
		$this->detectDevelompentMode();

		// На инициализацию устанавливаем собственные обработчики ошибок и исключений
		$this->setErrorHandlers();

		// Читаем и устанавливаем локаль
		$this->setLanguage();

    	// Если определен текущий язык — добавляем пре-суффикс шаблонов на его основе
    	if( $this->language ) {
            // TODO: Template::addSuffix( '.' . $this->language );
    	}

    	// Если режим разработки — синхронизируем модели
		if( $this->getDevelopmentMode() ) {
			Storm_Core::sync();
		}
    }

	/**
	 *	Определение режима разработки
	 */
	protected function detectDevelompentMode() {
		if( ! is_bool( $this->getDevelopmentMode() ) ) {
			$this->developmentMode = $_SERVER['SERVER_ADDR'] === '127.0.0.1' ? true : false;
		}
	}
	
	protected function getDevelopmentMode() {
		return $this->developmentMode;
	}
	
	/**
	 *	Установка обработчиков ошибок и исключений.
	 */
	protected function setErrorHandlers() {
		set_error_handler( array( "Madone_Core", "fatal_error_handler" ), E_RECOVERABLE_ERROR | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE );
		set_exception_handler( array( "Madone_Core", "uncaught_exception_handler" ) );
	}
	
	/**
	 *	Включение правильной локали Storm.
	 */
	protected function setLanguage() {
		// Наполним массив языков сайта
		$this->languages = array();
        $this->langRegExp = '';
		
		foreach( Storm_Core::getAvailableLanguages() as $language ) {
            /**
             * @var $language Storm_Language
             */
			if( ! array_key_exists( 'default', $this->languages ) ) {
				$this->languages['default'] = $language;
			} else {
				$this->languages[ $language->getName() ] = $language;
				$this->langRegExp .= $this->langRegExp ? "|{$language->getName()}" : $language->getName();
			}
		}

		// Собственно проверка наличия языка
		if( $this->langRegExp ) {
			if( preg_match( "~^/(".$this->langRegExp.")(\W|$)~su", $_SERVER['REQUEST_URI'], $m ) ) {
				$needle = $m[1];
				$this->language = $m[1];
				if( $m[2] === '/' ) {
					$needle .= '/';
				}
				$_SERVER['REQUEST_URI'] = Madone_Utilites::str_replace_once ( $needle, '', $_SERVER['REQUEST_URI'] );
				Storm_Core::setLanguage( $this->languages[ $m[1] ] );
			}
		} else {
			$this->language = null;	// default language
			Storm_Core::setLanguage( $this->languages['default'] );
		}

		// Выбираем локаль PHP в соответствии с локалью шторма
/* 		setlocale( LC_ALL, StormCore::getLanguage()->getLocale() ); */
/* FIXME: все кроме LC_NUMERIC, иначе ломется Storm_Db_Field_Float (значение не правильно эскейпится и поэтому теряется действительная часть, это происходит потому, что в русском разделитель - запятая, а во все мире - точка) */
		setlocale( LC_COLLATE | LC_CTYPE | LC_MONETARY | LC_TIME | LC_MESSAGES, Storm_Core::getLanguage()->getLocale() );
		// Устанавливаем внутреннюю кодировку мультибайтовых функций
		mb_internal_encoding( Storm_Core::getLanguage()->getCharset() );
	}

    /**
     *	Обработка HTTP запроса к сайту.
     *	Основное метод, через который работают все страницы.
     *	Не возвращает ничего, текст сайта отправляется на стандартный вывод.
     */
    public function run() {
		// Тут будут условия фильтрации страниц
		$filter = null;
		
		// Получаем текущий URI, выделяем из него имена каталогов/файлов
        $path = $this['request']->getPathInfo();
		$names = Madone_Utilites::getUriPathNames($path);
		/*
		Если есть имена - пытаемся выбрать одну из внутренних страниц сайта
		Например, условия выборки для страницы /news/archive/2008 получатся такие:
		
		( ( uri = /news ИЛИ uri = /news/archive ИЛИ uri = /news/archive/2008 ) И type->has_subpages = true )
		ИЛИ
		( uri = /news/archive/2008 И type->has_subpages = true)
		
		Условиям могут соответствовать несколько страниц, и это очень хорошо и правильно,
		главное — верно упорядочить результат :3
		*/
		$uri = '';
		if( $names ) {
			// Циклом наращиваем путь от корня вглубь, добавляем условия на выборку страниц с таким путем
			foreach( $names as $name ) {
				$uri = "{$uri}/{$name}";
				$q = Q( array( 'uri' => $uri ) );
				$filter = $filter ? QOR( $filter, $q ) : $q;
			}
		}
		// Путь пуст, выбираем главную страницу
		else {
			$filter = Q( array( 'lvl' => 1 ) );
		}

		// Выбираем страницы, сортируем так: сначала самые глубоко вложеные, среди одинаково вложенных — с большим приоритетом типа
		foreach( Model_Pages()->filter( $filter )->filter( array( 'enabled' => true ) )->orderDesc( 'lvl' )->follow( 1 )->all() as $p ) {
			// в $uri как раз оказывается полный uri запрошенной страницы :D
			// Отделим uri приложение внутри страницы
			$app_uri = mb_substr( $uri, mb_strlen( $p->uri, 'utf-8' ), mb_strlen( $uri, 'utf-8' ), 'utf-8' );
			
			// Запускаем приложение, соответствующее модулю, если оно отработало — завершаем работу
            $app_response = null;
            $app_classname = "Madone_Module_{$p->module}_Application";
            if(class_exists($app_classname)) {
                $app = new $app_classname($this);
                /**
                 * @var $app Madone_Application
                 */
                $app_response = $app->run($p, $app_uri);

                if($app_response) {
                    $this['response']->setContent( $app_response );
                    $this['response']->send();

    				return;
    			}
            }
			// продолжаем обработку среди всех выбранных приложений, попадающих в этот же uri
		}

		// Не сработало ни одно приложение — ничего не найдено
        $this['response']->setContent( $this['template']->render('404.twig', array('uri' => $uri)) );
        $this['response']->setStatusCode(404, "Страница не найдена");
        $this['response']->send();
    }

    /**
     * Обработчик фатальных ошибок
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
	function fatal_error_handler( $errno, $errstr, $errfile, $errline ) {
		// Сбрасываем все буферы вывода, которые есть
		while( ob_get_level() ) {
			ob_end_clean();
		}
		if( ! headers_sent() ) {
			header( "Content-Type: text/html;charset=utf-8" );
		}
		if( $this->getDevelopmentMode() ) {
			if( strstr( $errstr, '<html>' ) === false ) {
				echo "<html><h2>" . $errstr . "</h2><h4>" . Madone_Utilites::getPhpErrorName( $errno ) ." in {$errfile} on line {$errline}.</h4>";
				$trace = debug_backtrace(); array_shift( $trace );
				echo Madone_Utilites::formatDebugTrace( $trace );
				echo "</html>";
			} else {
				echo $errstr;
			}
		} else {
			if( strstr( $errstr, '<html>' ) === false ) {
                echo $this['template']->render( 'error.twig', array( 'message' => $errstr ) );
			} else {
				echo $errstr;
			}
		}
		exit;
	}

    /**
     * Обработчик неперехваченных исключений
     * @param Exception $exception
     */
    function uncaught_exception_handler( $exception ) {
		// Сбрасываем все буферы вывода, которые есть
		while( ob_get_level() ) {
			ob_end_clean();
		}
		if( ! headers_sent() ) {
			header( "Content-Type: text/html;charset=utf-8" );
		}
		
		if( $this->getDevelopmentMode() ) {
			echo "<html><h2>" . $exception->getMessage() . "</h2><h4>" . get_class( $exception ) ." in " . $exception->getFile() . " on line " . $exception->getLine() . ".</h4>";
			echo Madone_Utilites::formatDebugTrace( $exception->getTrace() );
			echo "</html>";
		}
        else {
            echo $this['template']->render('error.twig', array( 'message' => $exception->getMessage() ));
		}
		exit;
	}

	function getLanguages() {
		return preg_grep( '/default/', array_keys( $this->languages ), PREG_GREP_INVERT );
	}

	function getLanguage() {
		return $this->language;
	}
	
	function isCurrentLanguage( $name ) {
		return Storm_Core::getLanguage()->getName() == mb_strtolower( $name ) ? true : false;
	}

    /**
     * TODO: Make an Twig extension for these methods
     */

    /**
     * Получение текстового блока
     * @param $name
     * @return Model_Textblock
     */
    function getBlock($name) {
        return Model_Textblocks()->getOrCreate(array('name' => $name));
    }

    function getApp($classname) {
        if(class_exists($classname)) {
            return new $classname();
        }
        else {
            throw new Exception("Class {$classname} doesn't exists!");
        }
    }

    function getTree($qs_name) {
        $qs = new Storm_Queryset_Tree( $qs_name );
        return $qs;
    }
}
