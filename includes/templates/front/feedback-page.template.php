<? $this->startBuffering(); ?>

<? if( $this->mode == 'added' ): ?>
	<h1><?= $this->page->title ?></h1>
	<p>Ваше сообщение отправлено, спасибо!</p>
<? else: ?>
	<?= $this->page->text ?>
	<? if( $this->mode == 'notext' ): ?><p style="color:red;">Пожалуйста, введите текст сообщения.</p><? endif ?>
	<form method="post" action="<?= $this->page->uri ?>/send/">
		<div><label>Имя<br/><input type="text" name="name" class="text" style="width: 350px;" value="<?= $this->HE( Mad::vars( 'name' ) ) ?>" /></label></div>
		<div><label>Email<br/><input type="text" name="email" class="text" style="width: 350px;" value="<?= $this->HE( Mad::vars( 'email' ) ) ?>" /></label></div>
		<div><label>Текст сообщения<br><textarea name="text" style="width: 350px; height: 150px"><?= Mad::vars( 'text' ) ?></textarea></label></div>
		<div><button type="submit" name="submit">Отправить</button></div>
	</form>
<? endif ?>

<? $this->page->text = $this->stopBuffering(); ?>

<? $this->replaceWith( 'text-page' ); ?>