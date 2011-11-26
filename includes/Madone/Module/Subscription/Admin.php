<?
class Madone_Module_Subscription_Admin extends Madone_Module {
	function handleHtmlRequest( $uri ) {
		$paginator = new Madone_Paginator( Model_SubscriptionRecipients()->orderDesc( 'date' ), 'core/paginator', 20 );
		if( ! $paginator->getObjects() && $paginator->getPage() > 1 ) {
			$path = $paginator->getPageCount() ? "page{$paginator->getPageCount()}/" : "";
			header(  "Location: {$this->uri}/{$path}", true );
			exit;
		}
		return $this->getTemplate( 'index', array( 'paginator' => $paginator ) );
	}
		
	function handleAjaxRequest( $uri ) {
		try {
			$vars = Madone_Utilites::vars();

			$subscribers = Model_SubscriptionRecipients( array( 'enabled' => 1 ) )->all();

			$emails = array();
			foreach($subscribers as $s) {
				$emails[] = $s->email;
			}
			
			if( ! $emails ) {
				throw new Exception( "В списке рассылки нет активных подписчиков." );
			}

			$mail = Outer_Email::create();
			
			$mail->AddAddress( array_shift( $emails ) );
			foreach( $emails as $rcpt ) {
				$mail->AddBCC( $rcpt );
			}
			$mail->Body = new Template( 'subscription-email-text', $vars );
			$mail->Subject = $vars['title'];
			
			if( array_key_exists( 'attachments', $vars ) && is_array( $vars['attachments'] ) && $vars['attachments'] ) {
				$attachments = array();
				
				foreach( $vars['attachments'] as $i ) {
					list( $id, $name ) = explode( ':', $i, 2 );
					$attachments[ $id ] = $name;
				}
				
				$files = Model_TempFiles( array( 'id__in' => array_keys( $attachments ) ) )->all();
				
				foreach( $files as $tmpfile ) {
					$mail->AddAttachment(
						"{$_SERVER['DOCUMENT_ROOT']}/{$tmpfile->file->uri}",
						$attachments[ $tmpfile->id ],
						'base64',
						$this->getMimeContentType( $tmpfile->file->uri )
					);
				}
			}
			
			$mail->Send();

			// Почистим хранилище временных файлов
			foreach( $files as $tmpfile ) {
				$tmpfile->delete();
			}

			return json_encode( array( 'success' => true ) );

		} catch( Exception $e ) {
			return json_encode( array( 'success' => false, 'message' => $e->getMessage() ) );
		}
		
		return null;
	}
	
	private function getQEncodedHeaderValue( $string ) {
		$enc = 'KOI8-R';
		return  "=?{$enc}?Q?".join( '', array_map( create_function( '$char', 'return "=".dechex( ord( $char ) );' ), str_split( iconv("UTF-8", $enc, $string ) ) ) )."?=";
	}
	
    private function getMimeContentType( $filename ) {
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        else {
            return 'application/octet-stream';
        }
    }

}

?>