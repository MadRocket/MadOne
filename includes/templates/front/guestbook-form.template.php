<form method="post" action="<?= $this->page->uri ?>/send/" enctype="multipart/form-data">
	<div><label>Имя<br/><input type="text" name="name" class="text" value="<?= $this->HE( Mad::vars( 'name' ) ) ?>" /></label></div>
	<div><label>Email<br/><input type="text" name="email" class="text" value="<?= $this->HE( Mad::vars( 'email' ) ) ?>" /></label></div>
	<div><label>Текст сообщения<br><textarea name="text"><?= Mad::vars( 'text' ) ?></textarea></label></div>
	<? if( $this->maxphotos > 0 ): ?>
		<div><label>Ваши фотографии</label><br>
		<? for( $i = 0; $i < $this->maxphotos; $i++ ): ?>
			<input type="file" name="photos[]" /><br/>
		<? endfor ?>
		</div>
	<? endif ?>
	<div><label>Защитный код<br/>
	<?= MadCaptcha::create()->getImageHTML() ?><br>
	<?= MadCaptcha::create()->getInputHTML() ?>
	</label></div>
	<div><button type="submit" name="submit">Отправить</button></div>
</form>