<?

class StormFileDbFieldValue extends StormDbFieldValue {
	protected $path;			// путь куда будут загружаться файлы (относительно DOCUMENT_ROOT)
	protected $source;			// источник нового файла, инициализируется при присваивании файлу нового значения
	protected $oldValues;		// Старое значение поля, чтобы удалить файлы при перезаписи
	
	public $filename;	// имя файла
	public $uri;	// uri файла, относительно $_SERVER['DOCUMENT_ROOT']
	public $size;	// размер файла

	/**
	*	Конструктор.
	*	Принимает параметры от конструктора поля, заполняет всякие штуки
	*/
	function __construct( array $params = array() ) {
		if( ! $params['path'] ) {
			throw new StormException( "Необходимо указать параметр 'path' — путь к каталогу для хранения файлов." );
		}
		
		$this->path = $params['path'];
		if( $this->path[0] != '/' ) {
			$this->path = "/{$this->path}";
		}
		
		$this->oldValues = array();
	}
	
	/**
	*	Получение значения поля для сохранения в базу данных
	*/
	function getForDatabase() {
		return $this->value ? json_encode( $this->prepareDatabaseValue() ) : null;
	}
	
	/**
	*	Подготовка значения для базы данных. Возвращает объект, который должен быть сохранен в БД.
	*/
	protected function prepareDatabaseValue() {
		return (object)array( 'filename' => $this->filename, 'size' => $this->size );
	}

	/**
	*	Инициализация поля значением из базы данных	
	*	$value — значение колонки в БД, полученное из запроса
	*/
	function setFromDatabase( $value ) {
		if( $value && $value = json_decode( $value, false ) ) {
			$this->loadDatabaseValue( $value );
		} else {
			$this->filename = null;
		}
		$this->checkValue();
	}
	
	/**
	*	Загрузка значения, полученного из базы данных
	*	$value — значение из БД, декодированное в объект
	*/
	protected function loadDatabaseValue( $value ) {
		foreach( get_object_vars( $value ) as $name => $value ) {
			$this->$name = $value;
		}
	}

	/**
	*	Получение упрощенной версии объекта
	*/	
	function getSimplified() {
		return $this->value ? $this->prepareSimplifiedValue() : null;
	}
	
	/**
	*	Подготовка значения для JSON. Возвращает объект.
	*/
	protected function prepareSimplifiedValue() {
		return (object)array( 'filename' => $this->filename, 'size' => $this->size, 'uri' => $this->uri );
	}
	
	/**
	*	Установка значения, пользовательские скрипты
	*/
	function set( $value ) {
		$this->loadValue( $value );
		$this->checkValue();
		return $this;
	}

	/**
	*	Разбор значения из пользовательских скриптов
	*/
	protected function loadValue( $value ) {
		// I. Значение пришло из $_FILES ------------------------------------------------
		if( is_array( $value ) && array_key_exists( 'error', $value ) && array_key_exists( 'name', $value ) && array_key_exists( 'tmp_name', $value ) ) {
			if( $value['error'] == UPLOAD_ERR_OK ) {
				// Все в порядке, файл успешно загружен
				$this->source = array( 'path' => $value['tmp_name'], 'name' => $value['name'] );
			} else if( $value['error'] == UPLOAD_ERR_NO_FILE ) {
				// Пользователь не указал файл и он не был загружен вообще, представим, что это NULL :D
				$this->loadValue( null );
			} else {
				// Что-то не в порядке или файл не был указан
				switch( $value['error'] ) {
					case UPLOAD_ERR_INI_SIZE:
						throw new StormException( "Файл слишком велик. Максимальный размер файла — ".ini_get( 'upload_max_filesize' )."." );
						break;
					case UPLOAD_ERR_FORM_SIZE:
						throw new StormException( "Файл слишком велик." );
						break;
					case UPLOAD_ERR_PARTIAL:
						throw new StormException( "Файл не был полностью загружен. Повторите загрузку." );
						break;
					case UPLOAD_ERR_NO_TMP_DIR:
						throw new StormException( "Отсутствует временный каталог для записи файла. Обратитесь в службу технической поддержки." );
						break;
					case UPLOAD_ERR_CANT_WRITE:
						throw new StormException( "Не удалось записать файл на диск. Обратитесь в службу технической поддержки." );
						break;
					case UPLOAD_ERR_CANT_WRITE:
						throw new StormException( "Не удалось записать файл на диск. Обратитесь в службу технической поддержки." );
						break;
					case UPLOAD_ERR_EXTENSION:
						throw new StormException( "Недопустимый тип файла. Используйте другой файл." );
						break;
					default: 
						throw new StormException( "При загрузке файла произошла неизвестная ошибка. Попробуйте загрузить файл заново или используйте другой файл. Обратитесь в службу технической поддержки, если ошибка не исчезает." );
						break;
				}
			}
		} else if( file_exists( "{$_SERVER['DOCUMENT_ROOT']}/{$value}" ) && is_file( "{$_SERVER['DOCUMENT_ROOT']}/{$value}" ) ) {
			// II. Значение — путь к файлу внутри DOCUMENT_ROOT, и этот файл существует ------------------------------------------------
			// Проверим, не вышли ли мы за DOCUMENT_ROOT
			$fileRealPath = realpath( "{$_SERVER['DOCUMENT_ROOT']}/{$value}" );
			$rootRealPath = realpath( $_SERVER['DOCUMENT_ROOT'] );

			if( substr_compare( $rootRealPath, $fileRealPath, 0, strlen( $rootRealPath ) ) == 0 ) {
				// Файл годный
				$fileinfo = pathinfo( "{$_SERVER['DOCUMENT_ROOT']}/{$value}" );
	    		$this->source = array( 'path' => "{$_SERVER['DOCUMENT_ROOT']}/{$value}", 'name' => $fileinfo['basename'] );
			} else {
				// Отаке детектед!
				throw new StormException( "Загрузка файла {$value} запрещена из соображений безопасности." );
			}
		} else if( is_null( $value ) ) {
			// III. Значение — null ------------------------------------------------
			if( $this->value ) {
				$this->oldValues[] = $this->prepareSimplifiedValue();
			}
			$this->filename = null;
			$this->source = null;
		} else {
			throw new StormValidationException( "Недопустимое значение для загрузки файла: '{$value}'." );
		}
	}
	
	/**
	*	Автоматически вызывается перед сохранением поля в БД
	*/
	function beforeSave() {
		// Если у нас есть новый источник данных — самое время обработать его
		if( $this->source ) {
			$this->oldValues[] = $this->getSimplified();
			$this->loadSource( $this->source );
			$this->checkValue();
			$this->source = null;
		}
		
		// Если есть старые значение — удалим их
		if( $this->oldValues ) {
			foreach( $this->oldValues as $old ) {
				$this->removeFiles( $old );
			}
			$this->oldValues = array();
		}
	}

	/**
	*	Перед удалением из БД надлежит почистить файлы
	*/
	function beforeDelete() {
		$this->removeFiles( $this->getSimplified() );
	}

	/**
	*	Загрузка файла и значения из указанного источника (скорее всего это $this->source)
	*/
	function loadSource( $source ) {
		// Проверим существование всех каталогов и доступность их для записи
		$path = "{$_SERVER['DOCUMENT_ROOT']}{$this->path}";

		if( ! @file_exists( $path ) ) {
			if(! @mkdir( $path, 0755, true ) ) {
				throw new StormException( "Невозможно создать каталог '{$path}' для сохранения файла." );
			}
		} else if( ! is_writeable( $path ) ) {
			throw new StormException( "Невозможно сохранить файл, запись в каталог '{$path}' запрещена." );
		}
		
		// Каталоги существуют и доступны для записи. Самое время попробовать прочитать данные.
		$data = file_get_contents( $source['path'] );
		if( $data === false ) {
			throw new StormException( "Невозможно прочитать файл '{$source['path']}'." );
		}

		// Сгенерируем имя нового файла
		$filename = $this->getFilename( $source['name'], $path );
					
		// Запишем новый файл
		$written = file_put_contents( "{$path}/{$filename}", $data );
		if( $written === false || $written != strlen( $data ) ) {
			throw new StormException( "Произошла ошибка при записи файла '{$path}/{$filename}'." );
		}
		
		// Если источник был загруженным файлом — удалим его
		if( is_uploaded_file( $source['path'] ) ) {
			unlink( $source['path'] );
		}

		$this->filename = $filename;
	}

	/**
	*	Удаление файлов переданного упрощенного вида значения
	*/	
	protected function removeFiles( $simplified ) {
		if( is_object( $simplified ) && $simplified->filename ) {
			$file = "{$_SERVER['DOCUMENT_ROOT']}/{$this->path}/{$simplified->filename}";
			if( file_exists( $file ) && is_writeable( $file ) ) {
				unlink( $file );
			}
		}
	}

	/**
	*	Проверка и приведение к правильному виду значения. Вызывается после работы с частями значения.
	*/
	protected function checkValue() {
		if( $this->filename ) {
			$this->uri = "{$this->path}/{$this->filename}";
			if( ! $this->size ) {
				$this->size = @filesize( "{$_SERVER['DOCUMENT_ROOT']}/{$this->uri}" );
			}
			$this->value = $this;
		} else {
			$this->filename = $this->uri = $this->size = $this->value = null;
		}
	}
	
	/**
	 * Получение правильного уникального имени файла
	 */
	protected function getFilename( $name, $path ) {
		// Сначала выполним транслитерацию
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

        $name = str_replace( array_keys( $trans ), array_values( $trans ), trim( $name ) );
        $name = preg_replace( '/[^.a-zA-Z0-9_\-]+/', '', $name );
        $name = preg_replace( '/_{2,}/', '_', $name );

		$filename = strtolower( $name );

		if( ! $filename ) {
			$filename = rand(0, 1000);
		}

		while( file_exists( "{$path}/{$filename}" ) ) {
			$filename = rand( 0, 10 ).$filename;
		}

		return $filename;
	}
	
	function beforeSync() {
		$path = "{$_SERVER['DOCUMENT_ROOT']}{$this->path}";
		if( ! @file_exists( $path ) ) {
			if(! @mkdir( $path, 0755, true ) ) {
				throw new StormException( "Не удалось создать каталог хранения файлов '{$path}'. Недостаточно привилегий доступа." );
			}
		} else if( ! is_writeable( $path ) && !( @chmod( $path, 0755 ) && is_writeable( $path ) ) ) {
			throw new StormException( "Запись в каталог '{$path}' запрещена." );
		}
    }
}

?>