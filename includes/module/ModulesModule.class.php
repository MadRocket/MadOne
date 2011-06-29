<?
/**
 * ModulesModule class.
 *
 * Модуль управления модулями
 *
 * @extends AbstractModule
 */
class ModulesModule extends AbstractModule {

    function handleHtmlRequest( $uri ) {
		$modules = array();
		$messages = array();
		$newmodules = array();
		
    	$path = "{$_SERVER['DOCUMENT_ROOT']}{$this->cmsUri}";
		
		// Проверим необходимые папки на доступность для записи
		if ( ! is_writeable( "{$path}/includes/modules/" ) ) {
			$messages[] = array('message' => "Каталог {$path}/includes/modules/ недоступен для записи!", 'fix' => "chmod 777 {$path}/includes/modules/" );
		}
		if ( ! is_writeable( "{$path}/templates/modules/" ) ) {
			$messages[] = array('message' => "Каталог {$path}/templates/modules/ недоступен для записи!", 'fix' => "chmod 777 {$path}/templates/modules/" );
		}
		
		// Если папки доступны на запись
    	if(count($messages) == 0) {
	        // Получаем список модулей
			$modules = MadoneModules()->order('position')->all();    	

			// Системые папки
			$system_modules = array("AbstractModule", "WelcomeModule", "SettingsModule", "PasswordModule", "LogoutModule", "HelpModule" );
			
			// Найдем неустановленные модули в системе
			$modules_classnames = array();
			
			foreach($modules as $m) {
				$modules_classnames[] = $m->classname;
			}
			
			foreach (scandir("{$path}/includes/modules/") as $item) { 
	            if ($item == '.' || $item == '..') {
	            	continue;
	            }
	            
	            if( preg_match("/^((.+)Module)\.class\.php$/", $item, $m) ) {
	            	if( ! in_array($m[1], $modules_classnames) && ! in_array($m[1], $system_modules)) {
	            		$newmodules[] = $m[1];
	            	}
	            }
			}

    	}
        
        return $this->getTemplate( 'index', array(
            'modules' => $modules,
            'messages' => $messages,
            'newmodules' => $newmodules
        ) );
    }
	
	
	function handleAjaxRequest( $uri ) {
		$names = Mad::getUriPathNames($uri);
		
		try {
			switch( $names[0] ) {
				case 'createFilesForInstance':
					if(is_integer( intval($names[1]) )) {
						$i = MadoneModules()->get( $names[1] );
						
						if($i && $this->createFilesForInstance($i)) {
							return json_encode( array('success' => true, 'message' => "Файлы успешно созданы") );
						}
						else {
							throw new Exception("Модуля с идентификатором {$names[1]} нет!");
						}
					}
					else {
						throw new Exception("Идентификатор модуля не является числом!");
					}
				break;
				
				case 'deleteFilesByObjectId':
					if(is_integer( intval($names[1]) )) {
						$i = MadoneModules()->get( $names[1] );
						
						if($i) {
							if($this->deleteFilesByObject($i) ) {
								return json_encode( array('success' => true, 'message' => "Файлы успешно удалены") );
							}
							else {
								throw new Exception("Удалить файлы не получилось!");
							}
						}
						else {
							throw new Exception("Модуля с идентификатором {$names[1]} нет!");
						}
					}
					else {
						throw new Exception("Идентификатор модуля не является числом!");
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
						$i = MadoneModules()->get( $names[1] );
						
						if($i) {
							return json_encode( array('success' => true, 'data' => $this->duplicateModule($i) ) );
						}
						else {
							throw new Exception("Модуля с идентификатором {$names[1]} нет!");
						}
					}
					else {
						throw new Exception("Идентификатор модуля не является числом!");
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
	 * Дублирование модуля со всеми файлами
	 *
	 * @access public
	 * @param mixed $instance - цель дублирования
	 * @return void
	 */
	function duplicateModule( $instance ) {		
		$clone = null;
		
		// Пробуем создать копию
		for($i = 0; ; $i++) {
			$rand = "r".rand(1, 100);

			try {
				$classname = preg_replace("/Module$/", "_{$rand}Module", $instance->classname);
				$clone = MadoneModules()->create( array( 'name' => "{$instance->name}_{$rand}", 'classname' => $classname, 'title' => "{$instance->title}_{$rand}", 'enabled' => false ) );
				
				// Копируем файлы
				$path = getcwd();
		
				// Файл класса
				if( file_exists("{$path}/includes/modules/{$instance->classname}.class.php") ) {
					if( ! file_exists("{$path}/includes/modules/{$clone->classname}.class.php") ) {
						copy("{$path}/includes/modules/{$instance->classname}.class.php", "{$path}/includes/modules/{$clone->classname}.class.php");
						
						// Залезем в файл и исправим там имя класса на новое
						$file_content = file_get_contents("{$path}/includes/modules/{$clone->classname}.class.php");
						$file_content = preg_replace("/class $instance->classname /", "class $clone->classname ", $file_content);
						
						file_put_contents("{$path}/includes/modules/{$clone->classname}.class.php", $file_content);
					}
					else {
						throw new Exception("Файл с именем {$path}/includes/modules/{$clone->classname}.class.php уже существует!");
					}					
				}
				
				// Шаблоны
				if(file_exists("{$path}/templates/modules/{$instance->classname}/")) {
					if(! file_exists("{$path}/templates/modules/{$clone->classname}/")) {
						$this->copyRecoursive("{$path}/templates/modules/{$instance->classname}/", "{$path}/templates/modules/{$clone->classname}/", "/^\.$|^\.\.$|^\.svn$|^\.git$|^CVS$|^\.DS_Store$|^\._.*$/");
					}
					else {
						throw new Exception("Директория {$path}/templates/modules/{$instance->classname}/ уже существует!"); 
					}
				}
				
				break;
			}
			catch( Exception $e ){
				// Если не удалось с 5и попыток создать копию, значит виноват не рандом
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
		$path = getcwd();
		
		if(file_exists("{$path}/includes/modules/{$classname}.class.php")) {
			unlink("{$path}/includes/modules/{$classname}.class.php");
		}
		
		if(file_exists("{$path}/templates/modules/{$classname}/")) {
			$this->deleteDirectory("{$path}/templates/modules/{$classname}/");
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
		$path = getcwd();
		
		if(file_exists("{$path}/includes/modules/{$instance->classname}.class.php")) {
			unlink("{$path}/includes/modules/{$instance->classname}.class.php");
		}
		
		if(file_exists("{$path}/templates/modules/{$instance->classname}/") && ! $this->deleteDirectory("{$path}/templates/modules/{$instance->classname}/")) {
		
			throw new Exception("При попытке удаления шаблоов модуля произошла ошибка");
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
		$path = getcwd();
            
        try {
        	// Проверим есть ли файл класса с таким именем
	    	if( @class_exists($instance->classname, true) ) {
				throw new Exception("3. Класс с именем {$instance->classname} уже существует! Придумайте другое имя!");
	    	}

			// Создадим файл с классом модуля
			if( ! file_exists("{$path}/includes/modules/{$instance->classname}.class.php") ) {
				if ($class_file = fopen("{$path}/includes/modules/{$instance->classname}.class.php", "a")) {
					$str = <<<EOT
<?php

/**
* $instance->classname class.
* 
* @extends AbstractModule
*/

class $instance->classname extends AbstractModule {

}

?>
EOT;
					fwrite($class_file, $str);
					fclose($class_file);
				}
				else {
					throw new Exception("Не удалось создать файл для класса! {$path}/includes/modules/{$instance->classname}.class.php");
				}
			}
			else {
				throw new Exception("Файл {$path}/includes/modules/{$instance->classname}.class.php уже существует!");
			}
			
			// Проверим наличие папки для шаблонов модуля
			if(! file_exists("{$path}/templates/modules/{$instance->classname}/")) {
				// Если папки нет мы ее создадим
				mkdir("{$path}/templates/modules/{$instance->classname}/", 0777, true);
			}
			
			// Проверим наличие файла для шаблона			
			if(! file_exists("{$path}/templates/modules/{$instance->classname}/index.template.php")) {
				// Файла нет - попробуем создать
				if( $template_file = fopen("{$path}/templates/modules/{$instance->classname}/index.template.php", "a") ) {
					fclose($template_file);
				}
				else {
					throw new Exception("Не удалось создать файл для шаблона! {$path}/templates/modules/{$instance->classname}/index.template.php");
				}
			}
			else {
				throw new Exception("Файл {$path}/templates/modules/{$instance->classname}/index.template.php уже существует!");
			}
        }
        catch( Exception $e) {
        	throw $e;
        }
        
        return true;
	}
}

?>