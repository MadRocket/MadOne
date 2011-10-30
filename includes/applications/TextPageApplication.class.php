<?
/**
 * TextPageApplication class.
 * @extends Madone_Application
 */
class TextPageApplication extends Madone_Application {
    function index() {
        print $this->render('text-page.twig', array( 'page' => $this->page ));

        return true;
    }
}

?>