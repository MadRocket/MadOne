<?

class Storm_Db_Field_Value_File_Flv extends Storm_Db_Field_Value_File {
	protected $ffmpeg	= null;	// Путь к программе ffmpeg для преобразования видео и генерации скриншотов
	public $width	= 320;	// Ширина видео
	public $height	= 240;	// Высота видео
	public $preview;
	public $priview_uri;
	
	function __construct( array $params = array() ) {
		parent::__construct( $params );
		if( ! $params['ffmpeg'] ) {
			throw new Storm_Exception( "Необходимо указать параметр 'ffmpeg' — путь к утилите ffmpeg для преобразования видеофайлов." );
		}
		
		foreach( array( 'ffmpeg', 'width', 'height' ) as $property ) {
			$this->$property = $params[ $property ];
		}
	}

	protected function loadValue( $value ) {
		// Прочитаем значение словны мы — FileDbField
		$result = parent::loadValue( $value );
		// Если есть источник нового файла — подменим его на сконвертированный
		if( $this->source ) {
			// Временный файл
			$tmpFname = tempnam( null, 'mvf' );
			
			$convertCmd = "%{ffmpeg} -i %{source} -y -ar 22050 -ab 56k -b 200k -r 12 -f flv -s %{width}x%{height} -ac 1 %{destination} 1>/dev/null 2>&1";
			
			$options = array(
				'ffmpeg'		=> $this->ffmpeg,
				'width'			=> $this->width,
				'height'		=> $this->height,
				'source'		=> $this->source['path'],
				'destination'	=> $tmpFname,
			);
			
			exec( Storm_Utilities::array_printf( $convertCmd, $options ), $output, $retcode );
			
			// 1 - ошибка, 0 - все в порядке
			if( $retcode ) {
				throw new Exception( 'Ошибка преобразования видеофайла. Видимо, файл имеет неподдерживаемый формат. Используйте другой файл.' );
			}
			// Подменим источник на наш файл, а расширение переправим на flv
			$this->source['path'] = $tmpFname;
			$this->source['name'] = preg_replace( '/[^.]+$/', 'flv', $this->source['name'] );
		}

		return $result;
    }
	
	protected function checkValue() {
		parent::checkValue();
		if( ! is_null( $this->value ) ) {
			if( property_exists( $this, 'preview' ) ) {
				$this->preview_uri = "{$this->path}/{$this->preview}";
			}
		}
	}

	protected function prepareSimplifiedValue() {
		$value = parent::prepareSimplifiedValue();
		if( $this->value ) {
			foreach( array( 'width', 'height', 'preview', 'preview_uri' ) as $property ) {
				if( property_exists( $this->value, $property ) ) {
					$value->$property = $this->value->$property;
				}
			}
		}
		return $value;
	}

	protected function removeFiles( $simplified ) {
		if( is_object( $simplified ) && property_exists( $simplified, 'preview' ) && $simplified->preview ) {
			$file = "{$_SERVER['DOCUMENT_ROOT']}/{$this->path}/{$simplified->preview}";
			if( file_exists( $file ) && is_writeable( $file ) ) {
				unlink( $file );
			}
		}
		return parent::removeFiles( $simplified );
	}
	
	function beforeSave() {
		$return = parent::beforeSave();
		
		if( ! is_null( $this->value ) ) {
			// Сгенерим скриншот
			$screenshotCmd = "%{ffmpeg} -i %{source} -y -f mjpeg -ss 0 -sameq -t 0.001 -s %{width}*%{height} %{destination} 1>/dev/null 2>&1";
			
			$options = array(
				'ffmpeg'		=> $this->ffmpeg,
				'width'			=> $this->width,
				'height'		=> $this->height,
				'source'		=> "{$_SERVER['DOCUMENT_ROOT']}/{$this->path}/" . $this->value->filename,
				'destination'	=> preg_replace( '/[^.]+$/', 'jpg', "{$_SERVER['DOCUMENT_ROOT']}/{$this->path}/{$this->value->filename}" ),
			);
			
			exec( Storm_Utilities::array_printf( $screenshotCmd, $options ), $output, $retcode );
			
			// 1 - ошибка, 0 - все в порядке
			if( $retcode ) {
				throw new Exception( 'Ошибка преобразования видеофайла. Видимо, файл имеет неподдерживаемый формат. Используйте другой файл.' );
			}
			
			$this->value->preview = preg_replace( '/[^.]+$/', 'jpg', $this->value->filename );

			$this->checkValue();
		}
		
		return $return;
	}

	protected function prepareDatabaseValue() {
		$value = parent::prepareDatabaseValue();
		if( $value ) {
			$value->preview = $this->preview;
		}
		return $value;
	}
}

?>