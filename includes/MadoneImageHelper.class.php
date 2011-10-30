<?php
/**
 * 
 * @author \$Author$
 */
 
class MadoneImageHelper {
    public function get($original, $output, $properties) {
        if(! is_file("{$_SERVER['DOCUMENT_ROOT']}{$output}")) {
            $dir = dirname("{$_SERVER['DOCUMENT_ROOT']}{$output}");
            if(! is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            self::transform($_SERVER['DOCUMENT_ROOT'].$original, "{$_SERVER['DOCUMENT_ROOT']}{$output}", $properties);
        }

        return $output;
    }

    /*
	 * Методы обаботки картинок
	 */

	/**
	 * Загрузка картинки в любом формате
	 * @return
	 * @param object $src
	 */
    static protected function load_image( $src )
    {
        $img = @imagecreatefromjpeg( $src );
        if( ! $img ) $img = @imagecreatefrompng( $src );
        if( ! $img ) $img = @imagecreatefromgif( $src );
        if( ! $img ) return false;

        return $img;
    }

    /**
     * Оверлей картинок
     * @return
     * @param $image картинка, на которую нужно наложить другую
     * @param $overlay картинка, которую следует наложить
     * @param string $position позиция overlay — 'left top', 'right bottom' 'center center' и т.п. комбинации ил left, top, bottom, right и center
     * @param integer $quality[optional] - качество, по умолчанию 85
     */
    static protected function overlay( $image, $overlay, $position, $quality = 85 )
    {
        # Узнаем размеры исходной картинки и ватермарка
        list( $iw, $ih ) = getimagesize( $image );
        list( $ow, $oh ) = getimagesize( $overlay );

        # Ничего не делаем, если оверлэй больше картинки
        if( $iw < $ow || $ih < $oh )
        {
			throw( new Exception( "small_image" ) );
        }

        # Посчитаем позицию наложения overlay.
        # По сути, нас интересуют только координаты $srcx и $srcy, потому что все остальное не меняется.
        list( $hpos, $vpos ) = explode( ' ', $position );

        switch( $hpos )
        {
            case 'left':    $ox = 0; break;
            case 'center':  $ox = ( $iw - $ow ) / 2; break;
            case 'right':   $ox = $iw - $ow; break;
            default:        throw( new Exception( "bad_position" ) );
        }

        switch( $vpos )
        {
            case 'top':    $oy = 0; break;
            case 'center': $oy = ( $ih - $oh ) / 2; break;
            case 'bottom': $oy = $ih - $oh; break;
            default:       throw( new Exception( "bad_position" ) );
        }

        # Прочитаем картинки
        $i = self::load_image( $image );
        $o = self::load_image( $overlay );

        # Сделаем временную картинку, на которую будем выполнять наложение, чтобы сохранить прозрачность
        $ri = imagecreatetruecolor( $iw, $ih );
        # Включим прозрачный фон картинки
        $bg = imagecolorallocatealpha($ri, 0, 0, 0, 0 );
        imagecolortransparent( $ri, $bg );
        # Включим alpha blengind
        imagealphablending( $ri, true );

        # Запишем картинку сначала оригинал, сверху — оверлэй
        imagecopyresampled( $ri, $i, 0, 0, 0, 0, $iw, $ih, $iw, $ih );
        imagecopyresampled( $ri, $o, $ox, $oy, 0, 0, $ow, $oh, $ow, $oh );

        # Сохраним
        if( preg_match( '/jpe?g$/i', $image ) )
        {
            @imagejpeg( $ri, $image, $quality );
        }
        else if( preg_match( '/gif$/i', $image ) )
        {
            @imagegif( $ri, $image );
        }
        else
        {
            @imagepng( $ri, $image );
        }

        imagedestroy( $i );
        imagedestroy( $o );
        imagedestroy( $ri );

        return true;
    }

	/**
	 * Изменение размеров изображения
	 *
	 *   Примеры указания размера:
     *    Размер        Полученое изображение
     *    ------        ---------------------
     *    '800x600'     ширина не более 800, высота не более 600, пропорционально исходному
     *    '*x150'       высота 150, ширина - пропорционально
     *    '800'         ширина и высота не более 800, пропорционально исходному
     *    '=800x600'    ширина равна 800, высота равна 600, обрезаное
     *    '=800'        ширина и высота равны 800, обрезаное
     *    '^800x600'    ширина равна 800 или высота равна 600, пропорционально исходному
     *    '^800'        ширина или высота равна 800, пропорционально исходному
	 *
	 *
	 * @return array массив (WIDTH, HEIGHT) с размерами полученного изображения или false при ошибке
	 * @param object $src - имя входного файла
	 * @param object $dst - имя выходного файла
	 * @param object $size - размеры и флаги, общий вид: [=|^]WIDTH[xHEIGHT]
	 * 						 Флаги  - взаимоисключающие:
	 * 						 = полное соответствие изображения указаным размерам (выполняется обрезание исходного)
	 * 						 ^ увеличение изображения при пропорциональном изменении размеров
	 * 						 Если HEIGTH не указана, принимается HEIGHT = WIDTH
	 * 						 Вместо HEIGHT или WIDTH можно указать *, это будет означать размер исходного изображения
	 *
	 * @param object $quality[optional] качество выходного файла
	 */
	static protected function transform( $src, $dst, $size, $quality = 85 )
    {
        $W = $H = $WDH = null;  # Требуемые высота и ширина, отношение требуемой высоты к требуемой ширине
        $w = $h = $wdh = null;  # Исходные высота и ширина, отношение исходной высоты к исходной ширине

        # Получим требуемые высоту и ширину
        if( preg_match( '/(\d+|\*)(?:[xх](\d+|\*)){0,1}/i', $size, $m ) )
        {
            $W = $m[1];
            $H = $m[2] ? $m[2] : $m[1];
        }
        if( !$W || !$H ) return false;

        # Прочитаем начальную картинку
        $file_content = file_get_contents($src);
        $isrc = imagecreatefromstring($file_content);
//        $isrc = @imagecreatefromjpeg( $src );
//        if( ! $isrc ) $isrc = @imagecreatefrompng( $src );
//        if( ! $isrc ) $isrc = @imagecreatefromgif( $src );
        if( ! $isrc ) {
        	preg_match( '/([^\/]+)$/', $src, $m );
            return "";
        	//throw new StormException( "Файл {$m[1]} не является изображением в формате png, jpg или gif." );
        }

        # Получим размеры исходного изображения
        list( $w, $h ) = getimagesize( $src );
        if( !$w || !$h ) return false;

        # Проверим на исходные размеры
        if( $W == '*' )
        {
            $W = $h < $H ? $w * ( $H / $h ) : $w;
        }
        else if( $H == '*' )
        {
            $H = $w < $W ? $h * ( $W / $w ) : $h;
        }

        # Посчитаем отношения ШИРИНА / ВЫСОТА
        $wdh = $w / $h;
        $WDH = $W / $H;

        # Размеры, до которых ресайзить картинку
        $rW = $rH = null;

        # Если в size указано = (равно), ресайзим картинку до полного соответствия размеров
        if( preg_match( '/=/', $size ) )
        {
            # х, y, ширина и высота области, до которой обрезается картинка
            list( $cx, $cy, $cw, $ch ) = array( 0, 0, $w, $h );

            if( preg_match( '/:(\d+)\/(\d+)$/', $size, $m ) )
            {
                $pW = $m[1] > 100 ? $m[1] % 100 : $m[1];
                $pH = $m[2] > 100 ? $m[2] % 100 : $m[2];
            }
            else
            {
                $pW = 50;
                $pH = 50;
            }

            if( $WDH >= $wdh )
            {
                # Требуемое отношение W / H больше имеющегося => нужно уменьшить ширину изображения
                $ch = $w / $WDH;
                $cy = ( ($h - $ch) / 100 ) * $pH;
            }
            else
            {
                # W/H < w/h => уменьшим высоту изображения
                $cw = $h * $WDH;
                $cx = ( ($w - $cw) / 100 ) * $pW;
            }
            # Округлим все в меньшую сторону
            list( $cx, $cy, $cw, $ch ) = array( floor($cx), floor($cy), floor($cw), floor($ch) );

            # Обрежем изображение
            $img = imagecreatetruecolor( $cw, $ch );
			imagealphablending( $img, false );
			imagesavealpha( $img, true );
			$transparent = imagecolorallocatealpha( $img, 255, 255, 255, 127 );
			imagefill( $img, 0, 0, $transparent );
            imagecopyresampled( $img, $isrc, 0, 0, $cx, $cy, $cw, $ch, $cw, $ch );

            # Уничтожим старое изображение и заменим его в памяти на отресайзенное
            imagedestroy( $isrc );
            $isrc = $img;

            # Установим размеры ресайза
            $rW = $W;
            $rH = $H;

            # Сохраним размеры текущей картинки
            $w = $cw;
            $h = $ch;
        }
        # Ресайзим до помещения картинки в указанные размеры с сохранением пропорций
        else
        {
            # Изменяем размеры, если изображение больше, чем требуется или указан флаг увеличения
            if( $w > $W || $h > $H || preg_match( '/\^/', $size ) )
            {
                # Вычислим множитель для пропорционального изменения размеров
                $scale = $WDH > $wdh ? $H / $h : $W / $w;

                $rW = floor( $w * $scale );
                $rH = floor( $h * $scale );
            }
            else
            {
                $rW = $w;
                $rH = $h;
            }
        }

        # Ресайз картинки через копирование
        $img = imagecreatetruecolor( $rW, $rH );

		imagealphablending( $img, false );
		imagesavealpha( $img, true );
		$transparent = imagecolorallocatealpha( $img, 255, 255, 255, 127 );
		imagefill( $img, 0, 0, $transparent );

        imagecopyresampled( $img, $isrc, 0, 0, 0, 0, $rW, $rH, $w, $h );

        $success = false;

        if( preg_match( '/jpe?g$/', $dst ) )
        {
            $success = @imagejpeg( $img, $dst, $quality );
        }
        else if( preg_match( '/gif$/', $dst ) )
        {
            $success = @imagegif( $img, $dst );
        }
        else
        {
            $success = @imagepng( $img, $dst );
        }

        @imagedestroy( $isrc );
        @imagedestroy( $img );

        if( ! $success ) {
        	preg_match( '/([^\/]+)$/', $src, $m );
			throw new StormException( "Ошибка при сохранении изображения {$m[1]}." );
        }

        return array( $rW, $rH );
    }
}

?>