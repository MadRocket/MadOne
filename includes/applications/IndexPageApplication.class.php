<?
/**
 * IndexPageApplication class.
 * @extends AbstractApplication
 */

class IndexPageApplication extends AbstractApplication {
    function index() {
        print $this->render("index-page.twig", array('page' => $this->page ));
        return true;
    }
}

?>