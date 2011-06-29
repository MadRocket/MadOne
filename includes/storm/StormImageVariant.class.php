<?

/*
"original":	{"name":"norilsk_q.jpg","size":42241,"height":399,"width":600}
"large":	{"height":333,"width":500,"size":35221}
"small":	{"height":80,"width":120,"size":3741}
"cms":		{"height":120,"width":120,"size":4891}
*/

class StormImageVariant {
	public $uri;
	public $width;
	public $height;
	public $size;
	
	function __construct( $src = null ) {
		if( is_object( $src ) ) {
			foreach( get_object_vars( $src ) as $propname => $propvalue ) {
				if( property_exists( $this, $propname ) ) {
					$this->$propname = $propvalue;
				}
			}
		}
	}
	
	function selfCheck() {
		if( $this->uri && ( ! $this->size || ! $this->width || ! $this->height ) ) {
			$file = "{$_SERVER['DOCUMENT_ROOT']}/{$this->uri}";
			if( ! $this->size ) {
				$this->size = @filesize( $file );
			}
			if( ! $this->width || ! $this->height ) {
				list( $this->width, $this->height ) = @getimagesize( $file );
			}
		}
	}
	
	function getHTML( $attributes = array() ) {
		$attr = "";
		foreach( $attributes as $name => $value ) {
			$value = str_replace('"', '&quot;', $value);
			$attr .= "{$name}=\"{$value}\" ";
		}
		return "<img src=\"{$this->uri}\" width=\"{$this->width}\" height=\"{$this->height}\" {$attr}/>";
	}
}

?>