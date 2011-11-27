<?php
/**
    Утилиты студии MadRocket
*/

class Madone_Utilites
{
    static private $vars = array();
    static private $showExceptionsTrace = true;
    
    /**
    * Инициализация класса
    */
    static function _init()
    {
        // Прочитаем массивы $_GET, $_POST и $_FILES в общий, сохраняя ссылки на оригинальные элементы оригинальных массивов
        foreach( $_GET as $k => $v )
        {
            self::$vars[ $k ] = & $_GET[ $k ];
        }
        foreach( $_POST as $k => $v )
        {
            self::$vars[ $k ] = & $_POST[ $k ];
        }

		foreach( $_FILES as $k => $v ) {
			if( is_array( $_FILES[ $k ]['error'] ) ) {
				self::$vars[ $k ] = array();
				for( $i=0; $i < count( $_FILES[ $k ]['error'] ); $i++ ) {
					$file = array();
					foreach( array_keys( $_FILES[ $k ] ) as $key ) {
						$file[ $key ] = & $_FILES[ $k ][ $key ][ $i ];
					}
					self::$vars[ $k ][] = $file;
				}
			} else {
				self::$vars[ $k ] = & $_FILES[ $k ];
			}
		}

        // Удалим лишние кавычки
        if( get_magic_quotes_gpc() )
        {
            foreach( self::$vars as & $v )
            {
                if( ! is_array( $v ) ) $v = stripcslashes( $v );
            }
        }
    }

    /**
    * Возвращает ссылку на массив входящих переменных.
    * Если передано имя — возвращает ссылку на конкретую переменную из него
    */
    static function & vars( $name = null )
    {
        if(! self::$vars) {
            self::_init();
        }
    	if($name) {
    		return self::$vars[ $name ];
    	}
    	else {
    		return self::$vars;
    	}
    }

    static function server($name = null) {
        if($name) {
            return $_SERVER[$name];
        }
        return $_SERVER;
    }

    /**
        Разворачивает массив в ассоциативный массив по определенному ключу каждой записи.
        Запись может быть массивом или объектом.
    */
    static function getAssocArray( array $source, $key )
    {
        $result = array();

        foreach( $source as $i )
        {
            $result[ is_object( $i ) ? $i->$key : $i[$key] ] = $i;
        }

        return $result;
    }

    /**
        Делает из линейного массива таблицу с заданным количством колонок — массив массивов
        Возвращает результат
    */
    static function getGrid( array $array, $columnsCount = 3 )
    {
        $rows = array();

        for( $offset = 0; $offset < count( $array ); $offset += $columnsCount )
        {
            $rows[] = array_slice( $array, $offset, $columnsCount );
        }

        return $rows;
    }

    /**
        Транслитерация строки
        Кириллические буквы меняются на латинские буквы, пробелы — на подчеркивания,
        цифры остаются без изменения, все остальное — удаляется.
    */
    static function getPathName( $str )
    {
        $trans = array(
            "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e",
            "ё"=>"yo","ж"=>"j","з"=>"z","и"=>"i","й"=>"i","к"=>"k",
            "л"=>"l","м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
            "с"=>"s","т"=>"t", "у"=>"y","ф"=>"f","х"=>"h","ц"=>"c",
            "ч"=>"ch","ш"=>"sh","щ"=>"sh","ы"=>"i","э"=>"e","ю"=>"u",
            "я"=>"ya","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
            "Е"=>"E", "Ё"=>"Yo","Ж"=>"J","З"=>"Z","И"=>"I","Й"=>"I",
            "К"=>"K", "Л"=>"L","М"=>"M","Н"=>"N","О"=>"O","П"=>"P",
            "Р"=>"R","С"=>"S","Т"=>"T","У"=>"Y","Ф"=>"F","Х"=>"H",
            "Ц"=>"C","Ч"=>"Ch","Ш"=>"Sh","Щ"=>"Sh","Ы"=>"I","Э"=>"E",
            "Ю"=>"U","Я"=>"Ya", " " => "_" );

        $str = str_replace( array_keys( $trans ), array_values( $trans ), trim( $str ) );
        $str = preg_replace( '/[^.a-zA-Z0-9_-]+/', '', $str );
        $str = preg_replace( '/_{2,}/', '_', $str );

        return mb_strtolower( $str, 'utf-8' );
    }

    /**
        Проверка валидности имени пути
    */
    static function isPathName( $str )
    {
        return preg_match( '/^[.a-zA-Z0-9_-]+$/', $str );
    }

    /**
        Получение превью переданного текста не более указанной длины, не разрезая слова
    */
    static function getTextPreview( $str, $maxlen )
    {
        if( mb_strlen( $str, 'utf-8' ) > $maxlen )
        {
            // Получаем подстроку на один символ больше, чем нужно
            $str = mb_substr( $str, 0, $maxlen + 1, 'utf-8' );

            // Делаем замену с конца
            $str = preg_replace( '/[\s?!.,:;]+[^\s?!.,:;]*$/', '', $str );
        }

        return $str;
    }

    /**
        Получение uri path текущей страницы, на основе $_SERVER[REQUEST_URI], или любой другой переданной переменной
        Возвращает путь в виде /levelone/leveltwo/levelthree, или /levelone/leveltwo/levelthree.html
        Последний слеш удаляется, кроме первого (вырожденный path = '/').
    */
    static function getUriPath( $source = null )
    {
        if( is_null( $source ) )
        {
            $source = $_SERVER['REQUEST_URI'];
        }

        if( preg_match( '/^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/', $source, $m ) )
        {
            return mb_strlen( $m[5], 'utf-8' ) > 1 ? preg_replace( '/\/$/', '', $m[5] ) : $m[5];
        }

        return null;
    }

    /**
        Разбор uri path на части, возвращает массив элементов пути.
    */
    static function getUriPathNames( $path = null )
    {
        if( is_null( $path ) )
        {
            $path = self::getUriPath();
        }

        $path = preg_replace( '/\/+$|^\/+/', '', trim( $path ) );

        if( $path == '' ) return array();

        return preg_split( '/\//', $path );
    }

	static function decline( $num, $zero, $one, $two, $many )
    {
        $nmod10 = $num % 10;
        $nmod100 = $num % 100;

		if( ! $num ) return preg_replace("/%n/", $num, $zero);

        if( ( $num == 1) || ( $nmod10 == 1 && $nmod100 != 11 ) ) return preg_replace("/%n/", $num, $one);

        if( $nmod10 > 1 && $nmod10 < 5 && $nmod100 != 12 && $nmod100 != 13 && $nmod100 != 14 ) return preg_replace("/%n/", $num, $two);

        return preg_replace("/%n/", $num, $many);
    }
    
    /**
    *	Замена функции file_exists, использующая include_path (по умолчанию — не использующая ;)
    */
	static function file_exists( $filename, $use_include_path = false ) {
		if( ! $use_include_path ) {
			return file_exists( $filename );
		}		
		foreach( explode( PATH_SEPARATOR, get_include_path() ) as $path ) {
			if( $path && $path[ strlen( $path ) - 1 ] != '/' ) {
				$path .= '/';
			}
			if( file_exists( "{$path}{$filename}" ) ) {
				return true;
			}
		}
		return false;
	}
	
	/**
	*	Форматированние трейса, полученного из Exception->getTrace() или debug_backtrace().
	*	Возвращает трейс в виде HTML-таблицы.
	*/
	static function formatDebugTrace( array $rawTrace ) {
		$traces = array();
		$num = count( $rawTrace );
		
		if( ! $rawTrace ) {
			return '';
		}
		
		$dump = '<style>.trace {border:none;border-collapse:collapse;margin:0 0 1em 0;} .trace td, .trace th {border:1px solid #000;padding:5px 7px;}</style>';
		
		foreach( $rawTrace as $trace ) {
			$call = '';
			
			if( array_key_exists( 'object', $trace ) && is_object( $trace['object'] ) && method_exists( $trace['object'], 'toTraceString' ) ) {
				$call .= $trace['object']->toTraceString() . "{$trace['type']}";
			} else if( array_key_exists( 'class', $trace ) ) {
				$call .= "{$trace['class']}{$trace['type']}";
			}
			
			$call .= $trace['function'];
			
			if( array_key_exists( 'args', $trace ) ) {
				$args = array();
				foreach( $trace['args'] as $arg ) {
					if( is_null( $arg ) ) {
						$args[] = 'NULL';
					} elseif( is_object( $arg ) ) {
						if( method_exists( $arg, 'toTraceString' ) ) {
							$args[] = $arg->toTraceString();
						} else {
							$args[] = get_class( $arg );
						}
					} elseif( is_array( $arg ) ) {
						$args[] =  print_r( $arg, true );
					} else {
						$args[] =  "'{$arg}'";
					}
				}
				$call .= '('. ( $args ? ' '.join( ', ', $args ).' ' : '' ) .')';
			}
			
			if( array_key_exists( 'file', $trace ) ) {
				$trace['file'] = str_replace( $_SERVER['DOCUMENT_ROOT'], '', $trace['file'] );
			} else {
				$trace['file'] = '';
			}
			
			if( ! array_key_exists( 'line', $trace ) ) {
				$trace['line'] = '';
			}
			
			$traces[] = "<tr><td>{$num}</td><td>{$call}</td><td>{$trace['file']}</td><td>{$trace['line']}</td></tr>";
			$num--;
		}
		
		$dump .= '<table class="trace"><tr><th>#</th><th>Call</th><th>Source file</th><th>Line</th></tr>' . join( "", $traces ) . "</table></body></html>";
		
		return $dump;
	}
	
	static function str_replace_once($needle , $replace , $haystack){
		// Looks for the first occurence of $needle in $haystack
		// and replaces it with $replace.
		$pos = strpos($haystack, $needle);
		if ($pos === false) {
			// Nothing found
		    return $haystack;
		}
		return substr_replace($haystack, $replace, $pos, strlen($needle));
	} 			
		
	/**
	* Получение символьного имени ошибки php. Используется в обработчике ошибок.
	*/
	static function getPhpErrorName( $errno ) {
		$errors = array(
			E_ERROR				=> 'E_ERROR',
			E_WARNING			=> 'E_WARNING',
			E_PARSE				=> 'E_PARSE',
			E_NOTICE			=> 'E_NOTICE',
			E_CORE_ERROR		=> 'E_CORE_ERROR',
			E_CORE_WARNING		=> 'E_CORE_WARNING',
			E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
			E_COMPILE_WARNING	=> 'E_COMPILE_WARNING',
			E_USER_ERROR		=> 'E_USER_ERROR',
			E_USER_WARNING		=> 'E_USER_WARNING',
			E_USER_NOTICE		=> 'E_USER_NOTICE',
			E_STRICT			=> 'E_STRICT',
			E_RECOVERABLE_ERROR	=> 'E_RECOVERABLE_ERROR',
		);
		return array_key_exists( $errno, $errors ) ? $errors[ $errno ] : $errno;
	}

	/**
    *   Перевод чего-то в JSON-безопасную форму.
    *   $something можут быть строкой, массивом, массивом объектов, QuerySet-ом, моделью %D
    */
    static function getJsonSafe( $something ) {
    
        $result = null;
    
        if( is_object( $something ) ) {

            if( $something instanceof Storm_Model ) {
                $result = $something->asArray( true );
                
            } elseif( $something instanceof Storm_Queryset ) {
                $result = self::getJsonSafe( $something->all() );

            } else {
                throw new Exception( "Cannot make ".get_class( $something )." object JSON-safe." );
            }
        } elseif( is_array( $something ) ) {
            $result = array();
            foreach( $something as $k => $v ) {
                $result[ $k ] = self::getJsonSafe( $v );
            }

        } else {
            $result = $something;
        }
        
        return $result;
    }
}

?>