<?
/**
 * Module_Feedback_Application class.
 * @extends Madone_Application
 */
class Madone_Module_Feedback_Application extends Madone_Application {

    protected $routes = array(
        '/?' => 'index',
        'send' => 'send'
    );

    function index() {
        print $this->render('feedback/index.twig', array(

			'mode' => null,
            'vars' => Madone_Utilites::vars()
		));
		return true;
    }

    function send() {
        $vars = Madone_Utilites::vars();
		$mode = null;

        if( array_key_exists( 'text', $vars ) && $vars['text'] ) {
            Model_Feedbackmessages()->create( $vars );
            $mode = 'added';
        } else {
            $mode = 'notext';
        }

        print $this->render('feedback/index.twig', array(

			'mode' => $mode,
            'vars' => Madone_Utilites::vars()
		));
		return true;
    }
}

?>