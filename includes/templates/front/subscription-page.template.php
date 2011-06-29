<? $this->startBuffering(); ?>

<? if( $this->mode == 'added' ): ?>
	<h1><?= $this->page->title ?></h1>
	<p>Ваш адрес добавлен в список рассылки.</p>
	<p>Спасибо за интерес к деятельности фонда!</p>
<? elseif( $this->mode == 'removed' ): ?>
	<h1><?= $this->page->title ?></h1>
	<p>Ваш адрес удален из списка рассылки.</p>
<? elseif( $this->mode == 'unsubscribe' ): ?>
	<h1><?= $this->page->title ?></h1>
	<p>Введите Ваш адрес электронной почты, чтобы отписаться от рассылки.</p>
	<form method="post" class="subscribe-form" action="<?= $this->page->uri ?>/save/">
		<input type="text" name="unsubscribe" value="" /> <button type="submit" name="submit">Удалить адрес из рассылки</button>
	</form>
<? else: ?>
<?= $this->page->text ?>
<form method="post" class="subscribe-form" action="<?= $this->page->uri ?>/save/">
	<input type="text" name="subscribe" value="" /> <button type="submit" name="submit">Подписаться</button>
</form>

<? endif ?>

<? $this->page->text = $this->stopBuffering(); ?>

<? $this->replaceWith( 'text-page' ); ?>