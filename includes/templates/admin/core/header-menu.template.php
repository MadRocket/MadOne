<span class="system-menu"><span><span class="first"><a href="http://<?= $_SERVER['SERVER_NAME']?>/">Перейти на сайт</a></span>

<?
	$langs = array_values( StormCore::getAvailableLanguages() );
?>
<? if( count( $langs ) > 1 ): ?>
	<span class="lang">
		<? 	
			$nextLang = null;
			
			if($langs[count($langs) - 1] == StormCore::getLanguage()) {
				// Если язык последний, будем переключаться на первый 
				$nextLang = $langs[0];
			} 
			else {
				for($i = 0; $i < count($langs); $i++) {
					$iLang = $langs[$i];
					if($iLang == StormCore::getLanguage()) {
						$nextLang = $langs[$i+1];
						break;
					}
				}
			}
		?>
		
		<? if($nextLang): ?>
		<a href="<?= $this->cmsUri ?>/switchlanguage/<?= $nextLang->getName() ?>/" title="Текущий язык <?= StormCore::getLanguage()->getName() ?>"><img src="/cms/static/i/icons/flags/<?= StormCore::getLanguage()->getName() ?>.gif"></a>
		<? endif; ?>
	<? foreach( $langs as $lang ): ?>
		<? if( $lang == StormCore::getLanguage() ): ?>
		<a><?= ucfirst( $lang->getName() ) ?></a>
		<? else: ?>
		<a href="<?= $this->cmsUri ?>/switchlanguage/<?= $lang->getName() ?>/"><?= ucfirst( $lang->getName() ) ?></a>
		<? endif ?>
	<? endforeach ?>
	</span>
<? endif ?>
<span><a href="/cms/password/">Смена пароля</a></span><span><a href="/cms/settings/">Настройка</a></span><span class="last"><a href="/cms/logout/">Выход</a></span></span></span>