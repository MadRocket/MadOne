<?
/**
* Язык шторма — по сути содержит локаль в удобном виде.
*/
class StormLanguage {
	protected $name, $country, $charset, $locale, $key;	// $key - ключ, отличающий эту локаль от других, используется в именовании полей в БД и других похожих вещах
	
	public function __construct( $locale ) {
		// Разберем переданную локаль
		if( preg_match( '/^(\w+)_(\w+)\.([^.]+)$/', $locale, $m ) ) {
			$this->locale	= $locale;
			$this->name 	= mb_strtolower( $m[1] );
			$this->country	= mb_strtolower( $m[2] );
			$this->charset	= mb_strtolower( $m[3] );
			$this->key		= str_replace( '-', '', "{$this->name}{$this->country}_{$this->charset}" );
		} else {
			throw new StormException( "Не удается распознать локаль '{$locale}'. Используйте корректную локаль, например, ru_RU.UTF-8." );
		}
	}

	public function __toString() {
		return (string) $this->key;
	}

    public function getName() {
        return $this->name;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
?>