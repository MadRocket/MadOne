<?
/**
 * ApplicationsModule class.
 *
 * Модуль управления модулями
 *
 * @extends Madone_Module
 */
class ApplicationsModule extends Madone_Module {

    function handleHtmlRequest( $uri ) {
		$apps = array();
		$messages = array();
		$newapps = array();
		
		// Проверим необходимые папки на доступность для записи
		if ( ! is_writeable( "{$_SERVER['DOCUMENT_ROOT']}/includes/applications/" ) ) {
			$messages[] = array('message' => "Каталог {$_SERVER['DOCUMENT_ROOT']}/includes/applications/ недоступен для записи!", 'fix' => "chmod 755 {$_SERVER['DOCUMENT_ROOT']}/includes/applications/" );
		}
		
		// Если папки доступны на запись
    	if(count($messages) == 0) {
	        // Получаем список приложений
			$apps = MadonePageTypes()->order('position')->all();    	
			
			// Найдем неустановленные модули в системе
			$apps_classnames = array();
			
			foreach($apps as $m) {
				$apps_classnames[] = $m->app_classname;
			}
			
			$system_apps = array("Madone_Application");
			
			foreach (scandir("{$_SERVER['DOCUMENT_ROOT']}/includes/applications/") as $item) { 
	            if ($item == '.' || $item == '..') {
	            	continue;
	            }
	            
	            if( preg_match("/^((.+)Application)\.class\.php$/", $item, $m) ) {
	            	if( ! in_array($m[1], $apps_classnames) && ! in_array($m[1], $system_apps)) {
	            		$str = file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/includes/applications/{$item}");
	            			            		
	            		preg_match("/ \* title = (.+)/", $str, $title);
	            		preg_match("/ \* has_text = (\d+)/", $str, $has_text);
	            		preg_match("/ \* has_meta = (\d+)/", $str, $has_meta);
	            		preg_match("/ \* has_subpages = (\d+)/", $str, $has_subpages);
	            		preg_match("/ \* priority = (\d+)/", $str, $priority);
	            		
	            		$newapps[] = array(
	            			'app_classname' => $m[1], 
	            			'title'    		=> trim($title[1]), 
	            			'has_text' 		=> intval($has_text[1]), 
	            			'has_meta' 		=> intval($has_meta[1]), 
	            			'has_subpages' 	=> intval($has_subpages[1]), 
	            			'priority' 		=> intval($priority[1])
	            		);
	            	}
	            }
			}

    	}
        
        return $this->getTemplate( 'index', array(
            'apps' => $apps,
            'messages' => $messages,
            'newapps' => $newapps
        ) );
    }
	
	
	function handleAjaxRequest( $uri ) {
		$names = Mad::getUriPathNames($uri);
		
		try {
			switch( $names[0] ) {
				case 'createFilesForInstance':
					if(is_integer( intval($names[1]) )) {
						$i = MadonePageTypes()->get( $names[1] );
						
						if($i && $this->createFilesForInstance($i)) {
							return json_encode( array('success' => true, 'message' => "Файлы успешно созданы") );
						}
						else {
							throw new Exception("Приложения с идентификатором {$names[1]} нет!");
						}
					}
					else {
						throw new Exception("Идентификатор приложения не является числом!");
					}
				break;
				
				case 'deleteFilesByObjectId':
					if(is_integer( intval($names[1]) )) {
						$i = MadonePageTypes()->get( $names[1] );
						
						if($i) {
							if($this->deleteFilesByObject($i) ) {
								return json_encode( array('success' => true, 'message' => "Файлы успешно удалены") );
							}
							else {
								throw new Exception("Удалить файлы не получилось!");
							}
						}
						else {
							throw new Exception("Приложения с идентификатором {$names[1]} нет!");
						}
					}
					else {
						throw new Exception("Идентификатор приложения не является числом!");
					}
				break;
				
				case 'deleteFilesByClassName':
					if($names[1]) {
						if($this->deleteFilesByClassName( $names[1] ) ) {
							return json_encode( array('success' => true, 'message' => "Файлы успешно удалены") );
						}
						else {
							throw new Exception("Удалить файлы не получилось!");
						}
					}
				break;
				
				case 'duplicate':
					if(is_integer( intval($names[1]) )) {
						$i = MadonePageTypes()->get( $names[1] );
						
						if($i) {
							return json_encode( array('success' => true, 'data' => $this->duplicateApp($i) ) );
						}
						else {
							throw new Exception("Приложения с идентификатором {$names[1]} нет!");
						}
					}
					else {
						throw new Exception("Идентификатор приложения не является числом!");
					}
				break;
			}		
		} 
		catch ( Exception $e ) {
			return json_encode( array( 'success' => false, 'message' => $e->getMessage()) );
		}
	}
		
	
	/**
	 * duplicateModule function.
	 * 
	 * Дублирование приложения со всеми файлами
	 *
	 * @access public
	 * @param mixed $instance - цель дублирования
	 * @return void
	 */
	function duplicateApp( $instance ) {		
		$clone = null;
		
		// Пробуем создать копию
		for($i = 0; ; $i++) {
			$rand = "r".rand(1, 100);

			try {
				$app_classname = preg_replace("/Application$/", "_{$rand}Application", $instance->app_classname);
				$clone = MadonePageTypes()->create(array( 
					'app_classname' => $app_classname, 
					'title' 		=> "{$instance->title}_{$rand}", 
					'enabled' 		=> false, 
					'priority' 		=> $instance->priority, 
					'has_text' 		=> $instance->has_text, 
					'has_meta' 		=> $instance->has_meta, 
					'has_subpages' 	=> $instance->has_subpages 
				));
				
				// Копируем файл класса
				if( file_exists("{$_SERVER['DOCUMENT_ROOT']}/includes/applications/{$instance->app_classname}.class.php") ) {
					if( ! file_exists("{$_SERVER['DOCUMENT_ROOT']}/includes/applications/{$clone->app_classname}.class.php") ) {
						copy("{$_SERVER['DOCUMENT_ROOT']}/includes/applications/{$instance->app_classname}.class.php", "{$_SERVER['DOCUMENT_ROOT']}/includes/applications/{$clone->app_classname}.class.php");
						
						// Залезем в файл и исправим там имя класса на новое
						$file_content = file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/includes/applications/{$clone->app_classname}.class.php");
						$file_content = preg_replace("/class $instance->app_classname /", "class $clone->app_classname ", $file_content);
						
						file_put_contents("{$_SERVER['DOCUMENT_ROOT']}/includes/applications/{$clone->app_classname}.class.php", $file_content);
					}
					else {
						throw new Exception("Файл с именем {$_SERVER['DOCUMENT_ROOT']}/includes/applications/{$clone->app_classname}.class.php уже существует!");
					}					
				}
								
				break;
			}
			catch( Exception $e ){
				// Если не удалось с 10и попыток создать копию, значит виноват не рандом
				if($i < 10) {
					if( $clone ) {
						$clone->delete();
						$clone = null;
					}
					
					continue;
				}
				else {
					throw $e;
				}
			}		
		}		
		
		return $clone->asArray();
	}
	
	
	/**
	 * deleteFilesByClassName function.
	 *
	 * Удалить файлы связанные с переданным именем классом
	 *
	 * @access public
	 * @param String $classname - имя класса
	 * @return $this
	 */
	function deleteFilesByClassName( $classname ) {
		$path = $_SERVER['DOCUMENT_ROOT'];
		
		if(file_exists("{$path}/includes/applications/{$classname}.class.php")) {
			unlink("{$path}/includes/applications/{$classname}.class.php");
		}
				
		return $this;
	}
	
	/**
	 * deleteFilesByObject function.
	 * Удалить файлы связанные с переданным объектом
	 * @access public
	 * @param MadoneModule $instance - объект
	 * @return void
	 */
	function deleteFilesByObject( $instance ) {
		$path = $_SERVER['DOCUMENT_ROOT'];
		
		if(file_exists("{$path}/includes/applications/{$instance->app_classname}.class.php")) {
			unlink("{$path}/includes/applications/{$instance->app_classname}.class.php");
		}
				
		return true;
	}
	
	/**
	 * copyRecoursive function.
	 *
	 * Рекурсивное копирование каталога
	 *
	 * @access public
	 * @param string $source - исходный каталог
	 * @param string $dest - Имя нового каталога
	 * @param string $ignore_mask. (default: "/^(\.)$|^(\.\.)$/") - маска для игнорирования спец файлов, как то папок систем контроля версий и тд
	 * @return void
	 */
	function copyRecoursive( $source, $dest, $ignore_mask = "/^(\.)$|^(\.\.)$/" ) {
		if (! file_exists($dest) ) {
			mkdir($dest);
		}

		foreach( scandir($source) as $filename) {
			if(! preg_match($ignore_mask, $filename) ) {	
				if( ! is_dir($filename) || is_link($filename) ) {
					copy("{$source}/{$filename}", "{$dest}/{$filename}");
				}
				else {
					$this->copyRecoursive("{$source}/{$filename}", "{$dest}/{$filename}");
				}
			}
		}
	}
	
	/**
	 * deleteDirectory function.
	 * Рекурсивное удаление катлога
	 * @access public
	 * @param string $dir - имя каталога
	 * @return void
	 */
	function deleteDirectory($dir) { 
	    if ( ! file_exists($dir) ) {
	    	return true; 
	    } 
	    if (!is_dir($dir) || is_link($dir)) {
	    	return unlink($dir); 
	    } 
        foreach (scandir($dir) as $item) { 
            if ($item == '.' || $item == '..') {
            	continue;
            }
            if (! $this->deleteDirectory($dir . "/" . $item)) { 
                chmod($dir . "/" . $item, 0777); 
                if (! $this->deleteDirectory($dir . "/" . $item)) {
                	return false;
                }
            }; 
        } 
        return rmdir($dir); 
    }
	
	/**
	 * createFilesForInstance function.
	 * Создание сопутствующий файлов для бъекта модуля
	 * @access public
	 * @param MadoneModule $instance - объект
	 * @return void
	 */
	function createFilesForInstance( $instance ) {
		$path = $_SERVER['DOCUMENT_ROOT'];
            
        try {
        	// Проверим есть ли файл класса с таким именем
	    	if( @class_exists($instance->app_classname, true) ) {
				throw new Exception("3. Класс с именем {$instance->app_classname} уже существует! Придумайте другое имя!");
	    	}

			// Создадим файл с классом приложения
			if( ! file_exists("{$path}/includes/applications/{$instance->app_classname}.class.php") ) {
				if ($class_file = fopen("{$path}/includes/applications/{$instance->app_classname}.class.php", "a")) {
					$str = <<<EOT
<?

/**
 * $instance->app_classname class - $instance->title
 * 
 * @extends Madone_Application
 *
 * Default settings:
 * title = $instance->title
 * has_text = $instance->has_text
 * has_meta = $instance->has_meta
 * has_subpages = $instance->has_subpages
 * priority = $instance->priority
 */
 
class $instance->app_classname extends Madone_Application {
    function run( MadonePage \$page, \$uri = '' ) {
        return true;
    }
}

?>
EOT;
					fwrite($class_file, $str);
					fclose($class_file);
				}
				else {
					throw new Exception("Не удалось создать файл для класса! {$path}/includes/applications/{$instance->app_classname}.class.php");
				}
			}
			else {
				throw new Exception("Файл {$path}/includes/applications/{$instance->app_classname}.class.php уже существует!");
			}			
        }
        catch( Exception $e) {
        	throw $e;
        }
        
        return true;
	}
}

?>