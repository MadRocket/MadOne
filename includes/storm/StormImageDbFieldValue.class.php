<?

class StormImageDbFieldValue extends StormFileDbFieldValue {
	protected $variants = array();
	
	function __construct( array $params = array() ) {
		parent::__construct( $params );
		if( array_key_exists('variants', $params) && is_array( $params['variants'] ) ) {
			// Проверим, чтобы в массиве variants не было поля original, оно зарезервировано для исходной картинки
			if( array_key_exists( 'original', $params['variants'] ) ) {
				throw new StormException( "Использование варианта изображения 'original' запрещено, это название зарезервировано для исходного изображения." );
			}
			$this->variants = $params['variants'];
		}
	}
	
	protected function prepareDatabaseValue() {
		$value = (object)array();
		foreach( array_keys( $this->variants ) as $variant ) {
			$value->$variant = (object)array(
				'width' => $this->value->$variant->width,
				'height' => $this->value->$variant->height,
				'size' => $this->value->$variant->size,
			);
		}
		$value->original = (object)array(
			'name' => $this->filename,
			'width' => $this->value->original->width,
			'height' => $this->value->original->height,
			'size' => $this->size,
		);
		return $value;
	}
	
	protected function loadDatabaseValue( $value ) {
		if( is_object( $value ) && property_exists( $value, 'original' ) ) {
			$this->filename = $value->original->name;
			$this->copyValue( $value );
		}
	}

	protected function copyValue( $source ) {
		if( is_object( $source ) ) {
			$this->value = (object)array();
			foreach( array_keys( $this->variants ) as $variant ) {
				$this->value->$variant = new StormImageVariant( $source->$variant );
			}
		}
	}

	protected function checkValue() {
		// Сохраним старое значение, потому что родительский метод модифицирует все
		$oldvalue = $this->value;
		parent::checkValue();
		if( $this->value ) {
			$this->copyValue( $oldvalue );
			foreach( array_keys( $this->variants ) as $variant ) {
				if( ! property_exists( $this->value, $variant ) ) {
					$this->value->$variant = new StormImageVariant();
				}
				$vobject = $this->value->$variant;
				if( ! $vobject->uri ) {
					$vobject->uri = "{$this->path}/{$variant}/{$this->filename}";
				}
				$vobject->selfCheck();
			}
			$this->value->original = new StormImageVariant();
			$this->value->original->uri = $this->uri;
			$this->value->original->size = $this->size;
			$this->value->original->selfCheck();
		}
	}

	protected function prepareSimplifiedValue() {
		$value = (object)array();
		foreach( array_keys( $this->variants ) as $variant ) {
			$value->$variant = (object)array(
				'width' => $this->value->$variant->width,
				'height' => $this->value->$variant->height,
				'size' => $this->value->$variant->size,
				'uri' => $this->value->$variant->uri,
			);
		}
		$value->original = (object)array(
			'width' => $this->value->original->width,
			'height' => $this->value->original->height,
			'size' => $this->size,
			'uri' => $this->value->original->uri,
		);
		return $value;
	}

	protected function removeFiles( $simplified ) {
		if( is_object( $simplified ) ) {
			foreach( get_object_vars( $simplified ) as $variant ) {
				$file = "{$_SERVER['DOCUMENT_ROOT']}/{$variant->uri}";
				if( file_exists( $file ) && is_writeable( $file ) && is_file( $file ) ) {
					unlink( $file );
				}
			}
		}
	}
	
	public function loadSource( $source ) {
		// Проверим каталоги на доступность, отсутствующие создадим
		$path = "{$_SERVER['DOCUMENT_ROOT']}{$this->path}";
		foreach( array_merge( array( '' ), array_keys( $this->variants ) ) as $name ) {
			$dir = "{$path}/{$name}";
			if( ! @file_exists( $dir ) ) {
				if(! @mkdir( $dir, 0755, true ) ) {
					throw new StormException( "Невозможно создать каталог '{$dir}' для сохранения изображения." );
				}
			} else if ( ! is_writeable( $dir ) ) {
				throw new StormException( "Невозможно сохранить изображение: запись в каталог '{$dir}' запрещена." );
			}
		}
		
		parent::loadSource( $source );
		
		$value = (object)array();
		try
		{
			$value->original = new StormImageVariant();
			$value->original->uri = "{$this->path}/{$this->filename}";
			$value->original->selfCheck();
	
			if( ! ( $value->original->width && $value->original->height ) ) {
				throw new StormException( "Файл '{$this->source['name']}' не является изображением." );
			}

			$path = "{$_SERVER['DOCUMENT_ROOT']}{$this->path}";
			foreach( $this->variants as $name => $options ) {
				$v = new StormImageVariant();
				list( $v->width, $v->height ) = $this->transformImage( "{$path}/{$this->filename}", "{$path}/{$name}/{$this->filename}", $options );
				$v->size = filesize( "{$path}/{$name}/{$this->filename}" );
				$value->$name = $v;
			}
		} catch( Exception $e ) {
			// Случилось что-то плохое - удаляем все файлы и отправляем сообщение дальше
			foreach( get_object_vars( $value ) as $name => $v ) {
				@unlink( $path.( $name == 'original' ? '' : "/{$name}" )."/{$this->filename}" );
			}
			throw $e;
		}
	}
	
	function beforeSync() {
		parent::beforeSync();
		foreach( array_keys( $this->variants ) as $name ) {
			$dir = "{$_SERVER['DOCUMENT_ROOT']}{$this->path}/{$name}";
			if( ! @file_exists( $dir ) ) {
				if(! @mkdir( $dir, 0755, true ) ) {
					throw new StormException( "Не удалось создать каталог хранения изображений '{$dir}'. Недостаточно привилегий доступа." );
				}
			} else if( ! is_writeable( $dir ) && !( @chmod( $dir, 0755 ) && is_writeable( $dir ) ) ) {
				throw new StormException( "Запись в каталог '{$dir}' запрещена." );
			}
		}
    }

	//========================================== Методы обаботки картинок ==========================================
	/**
	*	Проверка поддержки типа изображения.
	*/
	function checkImageFormat( $type ) {
		$typeCheck = array(
			IMAGETYPE_GIF	=> IMG_GIF,
			IMAGETYPE_JPEG	=> IMG_JPG,
			IMAGETYPE_PNG	=> IMG_PNG,
		);
		
		if( ! array_key_exists( $type, $typeCheck ) ) {
			throw new StormException( "Изображения в формате ". image_type_to_extension( $type, false ). " не поддерживаются." );
		}
		
		if( ! imagetypes() & $typeCheck[ $type ] ) {
			throw new StormException( "Изображения в формате ". image_type_to_extension( $type, false ). " не поддерживаются графической библиотекой сайта." );
		}
	
		return true;
	}
	
	/**
	*	Загрузка и проверка параметров трансформации изображения.
	*	Используется методом transformImage
	*/
	function loadTransformOptions( $defaults, $overrides ) {
		$opts = array_merge( $defaults, $overrides );
		
		// Приведем в порядок типы параметров
		foreach( array( 'width', 'height', 'framethickness', 'padding' ) as $k ) {
			$opts[ $k ] = is_null( $opts[ $k ] ) ? null : (int) $opts[ $k ];
		}
		foreach( array( 'cropleft', 'croptop', 'watermarkleft', 'watermarktop', 'spacefillleft', 'spacefilltop' ) as $k ) {
			$opts[ $k ] = is_null( $opts[ $k ] ) ? null : abs( (float) $opts[ $k ] );
		}
		
		// Проверяем все
		foreach( array( 'cropleft', 'croptop', 'watermarkleft', 'watermarktop', 'spacefillleft', 'spacefilltop' ) as $k ) {
			if( $opts[ $k ] > 1 ) {
				$opts[ $k ] = 1;
			}
		}

		// Определим результирующий формат картинки
		$format = null;
		switch( strtolower( $opts['format'] ) ) {
		case 'jpg':
		case 'jpeg':
			$format = IMAGETYPE_JPEG;
			break;
		case 'gif':
			$format = IMAGETYPE_GIF;
			break;
		case 'png':
			$format = IMAGETYPE_PNG;
			break;
		default:
			throw new StormException( "Формат {$opts['format']} неизвестен. Используйте jpeg, png или gif." );
			break;
		}
		$this->checkImageFormat( $format );
		$opts['format'] = $format;

		// Цвета
		foreach( array( 'frame', 'background' ) as $k ) {
			$opts[ $k ] = is_null( $opts[ $k ] ) ? null : new RGB( $opts[ $k ] );
		}

		return $opts;
	}

	function transformImage( $src, $dst, array $args ) {
		// Аргументы по умолчанию. Переопределяются переданным параметром $args.
		$opts = array(
			'width'			=> null,	// Ширина результата
			'height'		=> null,	// Высота результата

			'crop'			=> false,	// Флаг обрезания картинок до заданных размеров. Не имеет смысла при указании только ширины или только высоты
			'cropleft'		=> 0.5,		// Соотношение отрезанного слева к отрезанному справа, от 0 до 1
			'croptop'		=> 0.5,		// Соотношение отрезанного сверху к отрезанному снизу, от 0 до 1
			
			'proportional'	=> true,	// Пропорциональное вписывание в полные размеры на уменьшение или увеличение
			'spacefill'		=> false,	// Заливать пустое место при пропорциональном ресайзе цветом фона
			'spacefillleft'	=> 0.5,		// Положение картинки в залитом прямоугольнике, от 0 до 1, 0 — слева, 1 — справа, 0.5 — посередине
			'spacefilltop'	=> 0.5,		// Положение картинки в залитом прямоугольнике, от 0 до 1, 0 — сверху, 1 — снизу, 0.5 — посередине

			'frame'			=> null,	// Цвет рамки вида 'fff' или '3a4b5c', null — нет рамки
			'framethickness'=> 1,		// Ширина рамки, пиксели

			'background'	=> null,	// Цвет фона вида 'fff' или '3a4b5c', null - прозрачный для png и gif, белый для jpeg
			
			'watermark'		=> null, 	// путь к файлу водяного знака на сервере относительно document root
			'watermarkleft'	=> 1.0,		// положение водяного знака на картинке, от 0 до 1, 0 — слева, 1 - справа, 0.5 - посередине
			'watermarktop'	=> 1.0,		// положение водяного знака на картинке, от 0 до 1, 0 — сверху, 1 - снизу, 0.5 - посередине

			'format'		=> 'jpeg',	// Формат результата — /jpe?g|png|gif/i
			
			'padding'		=> null,	// Отступ внутри картинки, будет просвечивать background
			
			'jpegquality'	=> 90,		// Качество вывода JPEG, от 0 до 100
			'pngcompression'=> 1,		// Компрессия PNG, от 0 до 9
		);
		
		try {
			extract( $this->loadTransformOptions( $opts, $args ) );
		} catch( StormException $e ) {
			throw new StormException( "Ошибка изменения размеров изображения {$src}. ".$e->getMessage() );
		}
		
		// Проверим файл
		if( ! file_exists( $src ) ) {
			throw new StormException( "Файл {$src} не существует." );
		}
		if( ! is_readable( $src ) ) {
			throw new StormException( "Файл {$src} не доступен для чтения." );
		}
		
		// Прочитаем данные исходного файла
		list( $oriW, $oriH, $oriFormat ) = getimagesize( $src );
		
		if( ! $oriW || ! $oriH ) {
			throw new StormException( "При чтении параметров изображения {$src} произошла ошибка. Файл не является изображением или поврежден." );
		}
		
		// Проверим поддержку формата картинки
		try {
			$this->checkImageFormat( $oriFormat );
		} catch( Exception $e ) {
			throw( $e );
		}
		
		/*
		У нас есть проверенные настройки, размеры исходной картинки и уверенность, что она в формате jpeg,
		gif или png и этот формат поддерживается нашей сборкой GD.
		
		Теперь нужно на основании исходных и желаемых размеров, min и max а так же настроек crop, stretch и scale
		расчитать параметры $srcX, $srcY, $srcW, $srcH, $dstW, $dstH для создания нового изборажения
		путем копирования части исходного с помощью imagecopyresampled(). $dstX и $dstY по понятным соображениям равны 0.
		*/
		$srcX = $srcY = $srcW = $srcH = $dstW = $dstH = $diW = $diH = null; // Это то, что мы будем расчитывать
		$dstX = $dstY = 0;
		
		list( $resW, $resH ) = array( $width, $height );

		// Считаем размеры результирующей картинки
		if( $resW && $resH ) {
			// Указаны ширина и высота
			if( $crop || ! $proportional ) {
				$dstW = $resW;
				$dstH = $resH;
			} else {
				// Обрезать нельзя, помещаем картинку в указанный размер
				$dstWH = $resW / $resH;
				$oriWH = $oriW / $oriH;
                $scale = $dstWH > $oriWH ? $resH / $oriH : $resW / $oriW;
                if( $dstWH < $oriWH ) {
					$dstW = $resW;
	                $dstH = round( $oriH * $scale );
                } else {
	                $dstW = round( $oriW * $scale );
					$dstH = $resH;
                }
                
                if( $spacefill ) {
                	if( $dstWH < $oriWH ) {
                		$dstY = round( ( $resH - $dstH ) * (float)$spacefillleft );
                		$diH = $resH;
                	} else {
                		$dstX = round( ( $resW - $dstW ) * (float)$spacefilltop );
                		$diW = $resH;
                	}
                }
			}
		} else if( $resW ) {
			$dstW = $resW;
			$dstH = $proportional ? round( $dstW / $oriW * $oriH ) : $oriH;
		} else if( $resH ) {
			$dstH = $resH;
			$dstW = $proportional ? round( $dstH / $oriH * $oriW ) : $oriW;
		} else {
			$dstH = $oriH;
			$dstW = $oriW;
		}
		
		// Выполняем обрезание картинки, если это требуется
		if( $crop && $resW && $resH ) {
			$dstWH = $resW / $resH;
			$oriWH = $oriW / $oriH;
            if( $dstWH >= $oriWH ) {
            	$srcX = 0;
            	$srcW = $oriW;
                $srcH = round( $oriW / $dstWH );
				$srcY = round( ( $oriH - $srcH ) * (float)$croptop );
            } else {
            	$srcY = 0;
            	$srcH = $oriH;
                $srcW = round( $oriH * $dstWH );
                $srcX = round( ( $oriW - $srcW ) * (float)$cropleft );
			}
		} else {
			$srcX = $srcY = 0;
			$srcW = $oriW;
			$srcH = $oriH;
		}
		
		// Целевая картинка
        $img = imagecreatetruecolor( $diW ? $diW : $dstW , $diH ? $diH : $dstH );
		imagesavealpha( $img, true );
		
		// Определимся с цветом фона и его прозрачностью
		if( $background ) {
			$bgcolor = imagecolorallocatealpha( $img, $background->r, $background->g, $background->b, 0 );
			imagealphablending( $img, true );
		} else if( $format == IMAGETYPE_JPEG ) {
			$bgcolor = imagecolorallocatealpha( $img, 255, 255, 255, 0 );
			imagealphablending( $img, true );
		} else {
			$bgcolor = imagecolorallocatealpha( $img, 255, 255, 255, 127 );
			imagealphablending( $img, false );
			if( $format == IMAGETYPE_GIF ) {
				imagecolortransparent( $img, $bgcolor );
			}
		}
		
		// Зальем фон
		imagefill( $img, 0, 0, $bgcolor );
		
		// Нарисуем рамку
		if( $frame && (int) $framethickness ) {
			$framecolor = imagecolorallocate( $img, $frame->r, $frame->g, $frame->b );
			imagefilledrectangle($img, 0, 0, $framethickness - 1, ( $diH ? $diH : $dstH ) - 1, $framecolor);
			imagefilledrectangle($img, $framethickness, 0, ( $diW ? $diW : $dstW ) - 1, $framethickness - 1, $framecolor);
			imagefilledrectangle($img, ( $diW ? $diW : $dstW ) - $framethickness, $framethickness, ( $diW ? $diW : $dstW ) - 1, ( $diH ? $diH : $dstH ) - 1, $framecolor);
			imagefilledrectangle($img, $framethickness, ( $diH ? $diH : $dstH ) - $framethickness, ( $diW ? $diW : $dstW ) - $framethickness - 1, ( $diH ? $diH : $dstH ), $framecolor);
			if( ! $diW ) {
				$diW = $dstW;
			}
			if( ! $diH ) {
				$diH = $dstH;
			}
			$dstX += $framethickness;
			$dstY += $framethickness;
			$dstW -= 2 * $framethickness;
			$dstH -= 2 * $framethickness;
		}
		
        // Ресайз картинки
        $isrc = imagecreatefromstring( file_get_contents( $src ) );

		// Если нет фона и выходная картинка gif, используем imagecopyresized для сохранения прозрачности
		if( $format == IMAGETYPE_GIF && ! $background ) {
			imagecopyresized( $img, $isrc, $dstX + $padding, $dstY  + $padding, $srcX, $srcY, $dstW - $padding * 2, $dstH - $padding * 2, $srcW, $srcH );		
		} else {
			imagecopyresampled( $img, $isrc, $dstX + $padding, $dstY  + $padding, $srcX, $srcY, $dstW - $padding * 2, $dstH - $padding * 2, $srcW, $srcH );
		}
		
		// Выполняем наложение оверлея
		if( $watermark ) {
			// Проверим файл
			if( ! file_exists( $watermark ) ) {
				throw new StormException( "Файл водяного знака  {$watermark} не существует." );
			}
			if( ! is_readable( $watermark ) ) {
				throw new StormException( "Файл водяного знака {$watermark} недоступен для чтения." );
			}
			
			// Прочитаем данные исходного файла
			list( $watW, $watH, $watFormat ) = getimagesize( $watermark );
			
			if( ! $watW || ! $watH ) {
				throw new StormException( "При чтении параметров водяного знака {$watermark} произошла ошибка. Файл не является изображением или поврежден." );
			}
			
			// Проверим поддержку формата картинки
			try {
				$this->checkImageFormat( $watFormat );
			} catch( Exception $e ) {
				throw( $e );
			}
			
			$wX = round( ( $dstW - $watW ) * $watermarkleft );
			$wY = round( ( $dstH - $watH ) * $watermarktop );
			
			$wimg = imagecreatefromstring( file_get_contents( $watermark ) );
			imagecopy( $img, $wimg, $dstX + $wX, $dstX + $wY, 0, 0, $watW, $watH );
		}

		switch( $format ) {
		case IMAGETYPE_JPEG:
			imagejpeg( $img, $dst, $jpegquality );
			break;
		case IMAGETYPE_GIF:
				imagegif( $img, $dst );
			break;
		case IMAGETYPE_PNG:
			imagepng( $img, $dst, $pngcompression );
			break;
		}
		
		return array( ( $diW ? $diW : $dstW ), ( $diH ? $diH : $dstH ) );
	}
}

?>