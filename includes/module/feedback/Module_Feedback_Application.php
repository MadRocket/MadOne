<?
/**
 * Module_Feedback_Application class.
 * @extends Madone_Application
 */
class Module_Feedback_Application extends Madone_Application {

    protected $routes = array(
        '/?' => 'index',
        'send' => 'send'
    );

    function index() {
        print $this->render('feedback/index.twig', array(
			'page' => $this->page,
			'mode' => null,
            'vars' => Mad::vars()
		));
		return true;
    }

    function send() {
        $vars = Mad::vars();
		$mode = null;

        if( array_key_exists( 'text', $vars ) && $vars['text'] ) {
            MadoneFeedbackMessages()->create( $vars );
            $mode = 'added';
        } else {
            $mode = 'notext';
        }

        print $this->render('feedback/index.twig', array(
			'page' => $this->page,
			'mode' => $mode,
            'vars' => Mad::vars()
		));
		return true;
    }
}

?>