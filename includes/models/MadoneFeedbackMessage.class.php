<?
class MadoneFeedbackMessage extends StormModel {
    static function definition() {
		return array(
			'name'		=> new StormCharDbField( array( 'localized' => false, 'maxlength' => 255 ) ),
			'email'		=> new StormCharDbField( array( 'localized' => false, 'maxlength' => 255 ) ),
			'text'		=> new StormTextDbField( array( 'localized' => false ) ),
			'answermd5'	=> new StormCharDbField( array( 'localized' => false, 'maxlength' => 32 ) ),
			'answer'	=> new StormTextDbField( array( 'localized' => false, 'default' => '' ) ),
			'date'		=> new StormDatetimeDbField( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
			'enabled'	=> new StormBoolDbField( array( 'default' => 1 ) ),
		);
    }
    
    function beforeSave() {
		if( ! $this->name ) {
    		$this->name = 'Анонимный посетитель';
    	}
    	// Проверим ответ, отправим пользователю уведомление
    	if( $this->email && $this->answer && md5( $this->answer ) != $this->answermd5 ) {
    	
			$mail = PhpMailerLibrary::create();
			$mail->AddAddress( $this->email );
			$mail->Subject = 'Ответ на Ваше сообщение';
			$mail->Body = $this->answer;
			$mail->Send();

    		$this->answermd5 = md5( $this->answer );
    		$this->enabled = false;
    	}
    }
    
    function afterSave( $new ) {
    	// Уведомим админа о новом сообщении
        if( $new ) {
			$mail = PhpMailerLibrary::create();
			$mail->AddAddress( Config::$i->{'admin_email'} );
			$mail->Subject = 'Письмо с сайта '.$_SERVER['SERVER_NAME'];
			$mail->Body = $this->name.( $this->email ? " ".$this->email : '' )."\n\n".$this->text;
			$mail->Send();
        }
    }
}
?>
