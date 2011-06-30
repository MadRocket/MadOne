<? $this->startBuffering(); ?>
<form class="search" action="/search/" method="get">
	<input class="search-query" type="search" name="q" value="<?= $this->query ?>"><input class="submit" type="submit" name="submit" value="Искать">
</form>
<? if( $this->paginator ): ?>
<?
	$fulltextProcessor = new StormFulltextProcessor();
	
	$words = array();
	
	foreach( $fulltextProcessor->getAllForms( $this->query ) as $k => $w ) {
		foreach($w as $word) {
			$words[] = $word;
		}
	}
?>
<span class="search-result-count"><?= Mad::decline($this->paginator->count, 'По Вашему запросу ничего не найдено', 'Найдено %n совпадение', 'Найдено %n совпадения', 'Найдено %n совпадений') ?></span>
<ol class="search-results" start="<?= ($this->paginator->page-1) * 15 + 1 ?>">
	<? foreach($this->paginator->getObjects() as $entry): ?>
		<?  
			preg_match("~([^;\.\!\?\,]*)(".join("|", $words).")([^;\.\!\?\,]*)~siu", $entry->text, $m);
		?>
		<li>
			<a href="<?= $entry->uri ?>" class="search-title" href=""><?= $entry->title ?></a>
			<p><?= ( array_key_exists(1, $m) ? $m[1] : "" ) . ( array_key_exists(2, $m) ? "<em>{$m[2]}</em>" : "" ) . ( array_key_exists(3, $m) ? $m[3] : "" ) ?></p>
		</li>
	<? endforeach; ?>
</ol>
<? else: ?>
	<span class="search-result-count">По Вашему запросу ничего не найдено</span>
<? endif; ?>
<?= $this->paginator ?>

<? $this->page->text = $this->stopBuffering(); ?>
<? $this->replaceWith( 'text-page' ); ?>