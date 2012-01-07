<?php
/**
 * http://sourceforge.net/projects/phpmorphy
 * @author \$Author$
 */
 
class Madone_Morphology {
	protected static $dictionaries = array();
	protected static $options = array();

	 function __construct() {
		// Подключаем phpMorphy, готовим опции
        require_once( dirname( __FILE__ )."/phpmorphy/phpmorphy-0.3.7/src/common.php" );

		self::$dictionaries = array(
			'ru' => array(
				'path'	=> dirname( __FILE__ )."/phpmorphy/phpmorphy-dicts/morphy-0.3.x-ru_RU-nojo-utf8",
				'lang'	=> 'ru_RU',
			),
		);

		self::$options = array(
			'storage'			=> PHPMORPHY_STORAGE_FILE,
			'predict_by_suffix'	=> true,
			'predict_by_db'		=> true,
			'graminfo_as_text'	=> false,
		);
	}

    /**
     * @param $lang
     * @return phpMorphy
     */
    public function get($lang) {
        return new phpMorphy(self::$dictionaries[ $lang ]['path'], self::$dictionaries[ $lang ]['lang'], self::$options);
    }

    /**
     * @static
     * @param $lang
     * @return bool
     */
    public static function checkLanguage($lang) {
        return array_key_exists($lang, self::$dictionaries);
    }
}
