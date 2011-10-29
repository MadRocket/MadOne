<?
/**
 * TextPageApplication class.
 * @extends AbstractApplication
 */
class TextPageApplication extends AbstractApplication {
    function index() {
        print $this->render('text-page.twig', array( 'page' => $this->page ));

        return true;
    }
}

?>