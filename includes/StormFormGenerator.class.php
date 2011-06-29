<?php


/**
 * Класс создания прототипа формы для модели Storm
 *
 * Использование:
 * print StormFormGenerator::forModel('MadoneGalleryImage');
 */
class StormFormGenerator {

	public static function forModel( $modelName ) {
		$model = new $modelName;
		
		ob_start();
		
		print "<div id=\"{$modelName}-form\" class=\"a-unit-form\" style=\"display: block;\">\n";
		
		$definition = $model->definition();
		
		foreach($definition as $name => $def) {
			print "\t<div class=\"block\">";
			
			if($def instanceof StormTextDbField) {
				print "<label>{$name}</label><textarea name=\"{$name}\"></textarea>";
			}
			elseif($def instanceof StormCharDbField) {
				print "<label>{$name}</label><input name=\"{$name}\" type=\"text\" class=\"width-100\" />";
			}
			elseif($def instanceof StormIntegerDbField) {
				print "<label>{$name}</label><input name=\"{$name}\" type=\"text\" class=\"width-100\" />";
			}
			elseif($def instanceof StormFloatDbField) {
				print "<label>{$name}</label><input name=\"{$name}\" type=\"text\" class=\"width-100\" />";
			}
			elseif($def instanceof StormEnumDbField) {
				print "<label>{$name}</label><select name=\"{$name}\"></select>";
			}
			elseif($def instanceof StormBoolDbField) {
				print "<label><input name=\"{$name}\" type=\"checkbox\" /> &mdash; {$name}</label>";
			}
			elseif($def instanceof StormDatetimeDbField) {
				print "<label>{$name}</label><input name=\"{$name}\" type=\"text\" datepicker=\"yes\" class=\"width-100\" />";
			}
			elseif($def instanceof StormImageDbField) {
				print "<button class=\"upload-{$name} small-styled-button\"><b><b>Загрузить изображение</b></b></button>\n";
				print "<img class=\"{$name}-preview\" src=\"\" style=\"display: none;\" />";
			}
			elseif($def instanceof StormFileDbField) {
				print "<button class=\"upload-{$name} small-styled-button\"><b><b>Загрузить файл</b></b></button>";
				print "<div class=\"{$name}-filename\"></div>";
			}
			

			print "</div>\n";
		}
			
		print "\t<div class=\"block\"><button class=\"submit small-styled-button\"><b><b>Сохранить</b></b></button><button class=\"cancel small-styled-button\"><b><b>Отмена</b></b></button></div>\n";
		print "</div>\n\n";
		
		return ob_get_clean();
	}

}

?>