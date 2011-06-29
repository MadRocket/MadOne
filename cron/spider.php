<?
error_reporting( E_ALL );

global $_SERVER;
$_SERVER['DOCUMENT_ROOT'] = "/srv/www/sashenkin-right.com/www";
$_SERVER['SERVER_NAME']   = "www.sashenkin-right.com";
$_SERVER['SERVER_PROTOCOL'] = "HTTP/1.1";

require_once( "{$_SERVER['DOCUMENT_ROOT']}/includes/autoload.php" );
require_once( "{$_SERVER['DOCUMENT_ROOT']}/includes/storm/loader.php" );

$ready = array();		// Пройденные uri
$trash = array();		// Отсеянные 404 и редиректы
$process = array("/");	// В обработке

$starttime = time();

while(count($process) > 0) {	
	$uri = array_pop($process);
	flush();
			
	$_SERVER['REQUEST_URI'] = $uri;

	ob_start();
	Madone::init();
	Madone::run();
	$str = ob_get_clean();
	
	// Подозрения на редирект и 404
	if($str == "" || preg_match("~404 Not Found~su", $str)) {
		// Проверим заголовки
		$headers = get_headers("http://{$_SERVER['SERVER_NAME']}{$uri}");	
		foreach($headers as $h) {            
			if(preg_match("~Location: (?:http://{$_SERVER['SERVER_NAME']}/|/)(.*)~", $h, $new_location)) {
	            // Есть редирект - проверим, знаем ли мы про такой адрес
	            if(    array_key_exists("/{$new_location[1]}", $ready) === false
	                && in_array("/{$new_location[1]}", $process) === false ) {
	            
	                // Не знаем - добавим в очередь на обработку
	                $process[] = "/{$new_location[1]}";
	            }
				
				$trash[] = $uri;
				$ready[$uri] = 1;
				
				// Такой адрес мы знаем - продолжим главный цикл
				continue 2;
			}
			elseif(preg_match("~HTTP/1\.. 404 Not Found~", $h)) {
				// 404 ошибка - страницу сдледует пропустить
				$trash[] = $uri;
				$ready[$uri] = 1;			
				continue 2;
			}
		}	
	}
	
	$text = "";
	if(preg_match("~<body>(.+)</body>~s", $str , $body_text)) {

		$text = preg_replace("~(<!-- Madone noindex -->(.*?)<!-- /Madone noindex -->)~sui", "", $body_text[1]);
		$text = preg_replace("~(<!--(.*?)-->)~sui", "", $text);
		
		$text = strip_tags($text);
		$text = preg_replace( array("~&nbsp;|\t+|\n+~", "~&laquo;|&raquo;|&quot|«|»;~", "~&mdash;~"), array(" ", "", "-"), $text);
		$text = preg_replace( "~ +~", " ", $text);
	}

	$title = "";
	if(preg_match("~<title>(.+)</title>~s", $str , $title)) {
		$title = $title[1];
	}
	
	$record = MadoneSearchRecords(array('uri' => $uri))->first();
	if($record) {
		$record->title = $title;
		$record->text  = trim($text);
		$record->date  = time();
		$record->save();
	}
	else {
		MadoneSearchRecords()->create( array('title' => $title, 'text' => trim($text), 'uri' => $uri) );
	}
	
	// Отметим uri как пройденный
	$ready[ $uri ] = true;
	
	// Ищем ссылки на странице
	if( is_array($body_text) && array_key_exists(1, $body_text)) {
		$links_text = preg_replace("~(<!-- Madone nofollow -->(.*?)<!-- /Madone nofollow -->)~sui", "", $body_text[1]);
		$links_text = preg_replace("~(<!--(.*?)-->)~sui", "", $links_text);
		preg_match_all('/href=["\'](.+?)["\']/', $links_text, $new_uris);
		$new_uris = $new_uris[1];
		for($j = 0; $j < count($new_uris); $j++) {
			$uri2 = $new_uris[$j];
			
			if( preg_match( "~^/([^#]+)|http://{$_SERVER['SERVER_NAME']}/([^#]+)~", $uri2, $m2 ) === 1 
				&& array_key_exists("/{$m2[1]}", $ready) === false
				&& in_array("/{$m2[1]}", $process) === false
				&& ! preg_match('~^/static~', "/{$m2[1]}")
				&& "/{$m2[1]}" != '/'		
			) {
				$process[] = "/{$m2[1]}";
			}
		}	
	}	
}

// Удалим из пройденных отсеянные uri
$ready = array_keys($ready);
$ready = array_diff($ready, $trash);

// Создадим свеженький сайтмап
file_put_contents( "{$_SERVER['DOCUMENT_ROOT']}/sitemap.xml", new Template('sitemap.xml', array('server_name' => $_SERVER['SERVER_NAME'], 'uris' => $ready)) );

/* Удаляем старые страницы из базы */
$todel = MadoneSearchRecords( array( 'date__lt' => $starttime ) )->limit(1000);
while($todel) {
    foreach($todel as $n) {
    	$n->delete();
    }
    $todel = MadoneSearchRecords( array( 'date__lt' => $starttime ) )->limit(1000);
}

?>