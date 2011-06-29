<?
    require_once( "{$_SERVER['DOCUMENT_ROOT']}/includes/autoload.php" );
    
    /**
	 *   Проверка существования файла должна выполняться правилами Rewrite.
	 *   Считается, что файл существует, равно как и  переменная color.
     */
    
    
    // Получим исходные данные — файл картинки и цвет фона
    $filePath = Mad::vars('path');
	$width = Mad::vars( 'width' );
	$height = Mad::vars( 'height' );

    $resampledPath = preg_replace("/\.(\S{3,4})$/", ".{$width}x{$height}.$1", $filePath);
    
    $fullFilePath = "{$_SERVER['DOCUMENT_ROOT']}{$filePath}";
    $resampledFilePath = "{$_SERVER['DOCUMENT_ROOT']}{$resampledPath}";
    
    // Исходная картинка и результат    
    list( $oWidth, $oHeight ) = getimagesize( $fullFilePath );
    $imgSrc = imagecreatefromstring( file_get_contents($fullFilePath) );
    $imgDst = imagecreatetruecolor( $width, $height );
    
    // Копируем поверх исходную картинку
    imagecopyresampled( $imgDst, $imgSrc, 0, 0, 0, 0, $width, $height, $oWidth, $oHeight );
    
    // Получаем содержимое получившегося gif-файла
    ob_start();
	imagejpeg( $imgDst );
    $imageData = ob_get_clean();

    file_put_contents( $resampledFilePath, $imageData );

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