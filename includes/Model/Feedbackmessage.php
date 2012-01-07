<?php
class Model_Feedbackmessage extends Storm_Model {
    static function definition() {
		return array(
			'name'		=> new Storm_Db_Field_Char( array( 'localized' => false, 'maxlength' => 255 ) ),
			'email'		=> new Storm_Db_Field_Char( array( 'localized' => false, 'maxlength' => 255 ) ),
			'text'		=> new Storm_Db_Field_Text( array( 'localized' => false ) ),
			'answermd5'	=> new Storm_Db_Field_Char( array( 'localized' => false, 'maxlength' => 32 ) ),
			'answer'	=> new Storm_Db_Field_Text( array( 'localized' => false, 'default' => '' ) ),
			'date'		=> new Storm_Db_Field_Datetime( array( 'default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true ) ),
			'enabled'	=> new Storm_Db_Field_Bool( array( 'default' => 1 ) ),
		);
    }
    
    function beforeSave() {
		if( ! $this->name ) {
    		$this->name = 'Анонимный посетитель';
    	}
    	// Проверим ответ, отправим пользователю уведомление
    	if( $this->email && $this->answer && md5( $this->answer ) != $this->answermd5 ) {

            Madone_Email::send('Ответ на Ваше сообщение', null, $this->email, $this->answer);

    		$this->answermd5 = md5( $this->answer );
    		$this->enabled = false;
    	}
    }
    
    function afterSave( $new ) {
    	// Уведомим админа о новом сообщении
        if( $new ) {
            Madone_Email::send('Письмо с сайта '.$_SERVER['SERVER_NAME'], null, Madone_Config::getInstance()->{'admin_email'}, $this->name.( $this->email ? " ".$this->email : '' )."\n\n".$this->text);
        }
    }
}
