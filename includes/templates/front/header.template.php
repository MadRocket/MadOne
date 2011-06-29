<? if( Madone::getLanguages() ): ?>
	<div style="float:right;margin: 0 40px 0 -100px;"><a href='/'>ru</a> <? foreach( Madone::getLanguages() as $lang ): ?> <a href="/<?= $lang ?>/"><?= $lang ?></a><? endforeach ?></div>
<? endif ?>
<? if( $this->page->lvl > 1 ):?>
	<a href="/"><?= Config::$site_title ?></a>
<? else: ?>
	<?= Config::$site_title ?>
<? endif ?>
— <span style="text-transform:lowercase;"><?= $this->page->title ?></span>
