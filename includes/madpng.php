<?
    require_once( "{$_SERVER['DOCUMENT_ROOT']}/includes/autoload.php" );
    
    /*
    Проверка существования файла должна выполняться правилами Rewrite.
    Считается, что файл существует, равно как и  переменная color.
    */
    
    // Каталог, в котором будет храниться кеш отрендеренных файлов
    $cachePath = Config::$tmpPath. "/madpngCache";
    
    // Что делать, если кеширование невозможно? true - генерить налету, false - отдавать исходную картинку
    $processWithoutCache = true;
    
    // Получим исходные данные — файл картинки и цвет фона
    $filePath = Mad::getUriPath();
    $color = strtolower( Mad::vars( 'color' ) );

    $fullFilePath = "{$_SERVER['DOCUMENT_ROOT']}{$filePath}";
    
    // Тут должен будет лежать закешированный отренедеренный файл
    $cachedFilePath = "{$cachePath}{$filePath}/{$color}.gif";
    
    // Если файл есть — просто выдадем его и не будем париться дальше
    if( @file_exists( $cachedFilePath ) )
    {
        OutputImage( $cachedFilePath );
        exit;
    }
    
    // Проверим поддержку  форматов PNG и GIF в GD.
    if( ! ( imagetypes() & ( IMG_PNG | IMG_GIF ) ) )
    {
        // Поддержка нужных форматов отсутствует, просто выдаем исходный файл
        OutputImage( $fullFilePath );
        exit;
    }
    
    // Проверим возможность кеширования
    $canCache = @is_writeable( "{$cachePath}{$filePath}" ) || @mkdir( "{$cachePath}{$filePath}", 755, true );

    if( ! $canCache && ! $processWithoutCache )
    {
        OutputImage( $fullFilePath );
        exit;
    }
    
    // Исходная картинка и результат    
    list( $width, $height ) = getimagesize( $fullFilePath );
    $imgSrc = imagecreatefrompng( $fullFilePath );
    $imgDst = imagecreatetruecolor( $width, $height );
    
    // Выделяем цвет фона
    if( preg_match( '/(.{2})(.{2})(.{2})/', $color, $m ) )
    {
        $trColor = array( 'red' => hexdec( $m[1] ), 'green' => hexdec( $m[2] ), 'blue' => hexdec( $m[3] ) );
    }
    else
    {
        $trColor = array( 'red' => 255, 'green' => 255, 'blue' => 255 );
    }
    $trIndex = imagecolorallocate( $imgDst, $trColor['red'], $trColor['green'], $trColor['blue'] );
    
    // Заливаем результирующую картинку фоном
    imagefill( $imgDst, 0, 0, $trIndex );
    imagecolortransparent( $imgDst, $trIndex );
    
    // Копируем поверх исходную картинку
    imagecopyresampled( $imgDst, $imgSrc, 0, 0, 0, 0, $width, $height, $width, $height );
    
    // Конвертим truecolor в индексированные цвета
    imagetruecolortopalette( $imgDst, true, 255 );
    
    // Получаем содержимое получившегося gif-файла
    ob_start();
    imagegif( $imgDst );
    $imageData = ob_get_clean();

    // Кешируем, если можно
    if( $canCache )
    {
        file_put_contents( $cachedFilePath, $imageData );
    }
    
    // Выводим получившуюся картинку
    header( 'Content-Type: image/gif' );
    header( "Content-Length: " . strlen( $imageData ) );
    print $imageData;

    // Готово
    exit;

    /**
        Вывод изображения из файла
        $filename - имя файла, который нужно выдать браузеру
    */
    function OutputImage( $filename )
    {
        preg_match( '/\.([^.]+)$/', $filename, $m );

        $types = array( 'png' => 'image/png', 'jpe' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'gif' => 'image/gif' );
        $type = $types[ strtolower( $m[1] ) ];
        if( ! $type ) $type = 'image/jpeg';
        
        header("Content-Type: {$type}");
        header("Content-Length: " . filesize( $filename ) );
        
        readfile( $filename );
    }
?>