<?

/**
*	Ядро системы управления, отображающее сайт.
*/

class Madone {
	public static $version = '2.0 &alpha;';
    public static $release = 'Eureka';
    
    protected static $languages = array();
    protected static $language = null;
    protected static $langRegExp = null;
    
	protected static $developmentMode;
    
    /**
    *	Инициализация класса
    */
	static function init() {
		// Определим нужно ли влючать режим разработки
		self::detectDevelompentMode();
	
		// На инициализацию устанавливаем собственные обработчики ошибок и исключений
		self::setErrorHandlers();
		
		// Читаем и устанавливаем локаль		
		self::setLanguage();
		
    	// Если определен текущий язык — добавляем пре-суффикс шаблонов на его основе
    	if( self::$language ) {
    		Template::addSuffix( '.' . self::$language );
    	}
    	
    	// Если режим разработки — синхронизируем модели
		if( self::$developmentMode ) {
			StormCore::sync();
		}
	}
	
	static function install() {
		// Пользователь по умолчанию
		MadoneUsers()->create( array( 'login' => 'admin', 'password' => md5('admin') ) );
		
		// Приложения по умолчанию
		MadonePageTypes()->create(array( 'title' => 'Обычная страница (текст с картинками)', 'app_classname'  => 'TextPageApplication', 'enabled' => true, 'has_text' => true, 'has_meta' => true, 'has_subpages' => false, 'priority' => 1));
		MadonePageTypes()->create(array( 'title' => 'Новости', 'app_classname'  => 'NewsApplication', 'enabled' => true, 'has_text' => false, 'has_meta' => false, 'has_subpages' => false, 'priority' => 2));
		MadonePageTypes()->create(array( 'title' => 'Фотогалерея', 'app_classname'  => 'GalleryApplication', 'enabled' => true, 'has_text' => false, 'has_meta' => true, 'has_subpages' => true, 'priority' => 2));
		MadonePageTypes()->create(array( 'title' => 'Обратная связь', 'app_classname'  => 'FeedbackApplication', 'enabled' => true, 'has_text' => true, 'has_meta' => true, 'has_subpages' => true, 'priority' => 2));
		MadonePageTypes()->create(array( 'title' => 'Главная страница', 'app_classname'  => 'IndexPageApplication', 'enabled' => true, 'has_text' => true, 'has_meta' => true, 'has_subpages' => false, 'priority' => 1));
		
		// Модули по умолчанию
		MadoneModules()->create(array( 'title' => 'Страницы сайта', 'name' => 'pages', 'enabled' => true, 'classname' => 'PagesModule' ));
		MadoneModules()->create(array( 'title' => 'Новости', 'name' => 'news', 'enabled' => true, 'classname' => 'NewsModule' ));
		MadoneModules()->create(array( 'title' => 'Фотогалерея', 'name' => 'gallery', 'enabled' => true, 'classname' => 'GalleryModule' ));
		MadoneModules()->create(array( 'title' => 'Обратная связь', 'name' => 'feedback', 'enabled' => true, 'classname' => 'FeedbackModule' ));
		MadoneModules()->create(array( 'title' => 'Текстовые блоки', 'name' => 'text-blocks', 'enabled' => true, 'classname' => 'TextBlocksModule' ));
		MadoneModules()->create(array( 'title' => 'Модули', 'name' => 'modules', 'enabled' => true, 'classname' => 'ModulesModule' ));
		MadoneModules()->create(array( 'title' => 'Приложения', 'name' => 'apps', 'enabled' => true, 'classname' => 'ApplicationsModule' ));
	}
	
	/**
	*	Определение режима разработки
	*/
	static function detectDevelompentMode() {
		if( ! is_bool( self::$developmentMode ) ) {
			self::$developmentMode = $_SERVER['SERVER_ADDR'] === '127.0.0.1' ? true : false;
		}
	}
	
	static function developmentMode() {
		return self::$developmentMode;
	}
	
	/**
	*	Установка обработчиков ошибок и исключений.
	*/
	static function setErrorHandlers() {
		set_error_handler( array( "Madone", "fatal_error_handler" ), E_RECOVERABLE_ERROR | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE );
		set_exception_handler( array( "Madone", "uncaught_exception_handler" ) );
	}
	
	/**
	*	Включение правильной локали Storm.
	*/
	static function setLanguage() {
		// Наполним массив языков сайта
		self::$languages = array();
		self::$langRegExp = '';
		
		foreach( StormCore::getAvailableLanguages() as $language ) {
			if( ! array_key_exists( 'default', self::$languages ) ) {
				self::$languages['default'] = $language;
			} else {
				self::$languages[ $language->name ] = $language;
				self::$langRegExp .= self::$langRegExp ? "|{$language->name}" : $language->name;
			}
		}

		// Собственно проверка наличия языка
		if( self::$langRegExp ) {
			if( preg_match( "~^/(".self::$langRegExp.")(\W|$)~su", $_SERVER['REQUEST_URI'], $m ) ) {
				$needle = $m[1];
				self::$language = $m[1];
				if( $m[2] === '/' ) {
					$needle .= '/';
				}
				$_SERVER['REQUEST_URI'] = Mad::str_replace_once ( $needle, '', $_SERVER['REQUEST_URI'] );
				StormCore::setLanguage( self::$languages[ $m[1] ] );
			}
		} else {
			self::$language = null;	// default language
			StormCore::setLanguage( self::$languages['default'] );
		}

		// Выбираем локаль PHP в соответствии с локалью шторма
/* 		setlocale( LC_ALL, StormCore::getLanguage()->locale ); */
/* FIXME: все кроме LC_NUMERIC, иначе ломется StormFloatDbField (значение не правильно эскейпится и поэтому теряется действительная часть, это происходит потому, что в русском разделитель - запятая, а во все мире - точка) */
		setlocale( LC_COLLATE | LC_CTYPE | LC_MONETARY | LC_TIME | LC_MESSAGES, StormCore::getLanguage()->locale );
		// Устанавливаем внутреннюю кодировку мультибайтовых функций
		mb_internal_encoding( StormCore::getLanguage()->charset );
	}
	
    /**
    *	Обработка HTTP запроса к сайту.
    *	Основное метод, через который работают все страницы.
    *	Не возвращает ничего, текст сайта отправляется на стандартный вывод.
    */
    static function run() {
		ob_start( );
		
		// Тут будут условия фильтрации страниц    
		$filter = null;
		
		// Получаем текущий URI, выделяем из него имена каталогов/файлов
		$names = Mad::getUriPathNames();
		
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
			
			// Фильтр вложенных страниц приложения
			$filter = QAND( $filter, Q( array( 'type__has_subpages' => true ) ) );
			
			// Фильтр полного совпадения uri страниц без вложенных страниц приложения
			$filter = QOR( $filter, QAND( Q( array( 'uri' => $uri ) ), Q( array( 'type__has_subpages' => false ) ) ) );
		}
		// Путь пуст, выбираем главную страницу
		else {
			$filter = Q( array( 'lvl' => 1 ) );
		}
		
		// Выбираем страницы, сортируем так: сначала самые глубоко вложеные, среди одинаково вложенных — с большим приоритетом типа
		foreach( MadonePages()->filter( $filter )->filter( array( 'enabled' => true ) )->orderDesc( 'lvl' )->order( 'type__priority' )->follow( 1 )->all() as $p ) {
			// в $uri как раз оказывается полный uri запрошенной страницы :D
			// Отделим uri приложение внутри страницы
			$app_uri = mb_substr( $uri, mb_strlen( $p->uri, 'utf-8' ), mb_strlen( $uri, 'utf-8' ), 'utf-8' );
			
			// Запускаем приложение, соответствующее типу страницы, если оно отработало — завершаем работу
			if( $p->type->getApplicationInstance()->run( $p, $app_uri ) ) {
				print( self::postprocess( ob_get_clean() ) );
				return;
			}
			// продолжаем обработку среди всех выбранных приложений, попадающих в этот же uri
		}
		
		ob_end_clean();
		// Не сработало ни одно приложение — ничего не найдено
		Madone::show404();
    }

    /**
	* Завершение выполнения скрипта с кодом 404.
	* Мимикрия под Apache.
    */
    static function show404( $uri = null ) {
        if( is_null( $uri ) ) {
            $uri = $_SERVER['REQUEST_URI'];
        }
    
        header( "{$_SERVER['SERVER_PROTOCOL']} 404 Not Found", true, 404 );

        print new Template('default/system/404', array('uri' => $uri));
        
        return;
    }

	/**
	*	Обработчик фатальных ошибок.
	*/
	static function fatal_error_handler( $errno, $errstr, $errfile, $errline ) {
		// Сбрасываем все буферы вывода, которые есть
		while( ob_get_level() ) {
			ob_end_clean();
		}
		if( ! headers_sent() ) {
			header( "Content-Type: text/html;charset=utf-8" );
		}
		if( self::$developmentMode ) {
			if( strstr( $errstr, '<html>' ) === false ) {
				echo "<html><h2>" . $errstr . "</h2><h4>" . Mad::getPhpErrorName( $errno ) ." in {$errfile} on line {$errline}.</h4>";
				$trace = debug_backtrace(); array_shift( $trace );
				echo Mad::formatDebugTrace( $trace );
				echo "</html>";
			} else {
				echo $errstr;
			}
		} else {
			if( strstr( $errstr, '<html>' ) === false ) {
				echo new Template( 'fatal-error', array( 'message' => $errstr ) );
			} else {
				echo $errstr;
			}
		}
		exit;
	}

	/**
	*	Обработчик неперехваченных исключений.
	*/
	static function uncaught_exception_handler( $exception ) {
		// Сбрасываем все буферы вывода, которые есть
		while( ob_get_level() ) {
			ob_end_clean();
		}
		if( ! headers_sent() ) {
			header( "Content-Type: text/html;charset=utf-8" );
		}
		
		if( self::$developmentMode ) {
			echo "<html><h2>" . $exception->getMessage() . "</h2><h4>" . get_class( $exception ) ." in " . $exception->getFile() . " on line " . $exception->getLine() . ".</h4>";
			echo Mad::formatDebugTrace( $exception->getTrace() );
			echo "</html>";
		} else {
			echo new Template( 'fatal-error', array( 'message' => $exception->getMessage() ) );
		}
		exit;
	}
	
	/**
	*	Постобработка кода страницы
	*/
	static function postprocess( $text ) {
		// Добавляем lang-атрибут для текущего языка
		$text = str_replace( '<html', '<html lang="'. StormCore::getLanguage()->name .'"', $text );
		
		// Если включен язык, отличный от default — переписываем все ссылки кроме static
		if( self::$language ) {
/*
			// TODO: не удалять эту строку, пока не обкатаем на вялом VPS
			$text = str_repeat( $text, 10 );
*/
			$text = preg_replace( '~("/)((?!static|'.self::$langRegExp.')[^"]*")~S', '$1'.self::$language.'/$2', $text );
/*
			$text = preg_replace( '~("/)((?!static|'.self::$langRegExp.')[^"]*")~S', '$1'.self::$language.'/$2', $text, -1, $cnt );
			$end = microtime( 1 );
			printf( '%.9f %d, %9f', $end - $start, $cnt, ($end-$start)/$cnt );
*/
		}
		
		return $text;
	}
	
	static function getLanguages() {
		return preg_grep( '/default/', array_keys( self::$languages ), PREG_GREP_INVERT );
	}

	static function getLanguage() {
		return self::$language;
	}
	
	static function isCurrentLanguage( $name ) {
		return StormCore::getLanguage()->name == mb_strtolower( $name ) ? true : false;
	}
}

?>