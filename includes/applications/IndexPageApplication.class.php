<?php
/**
 * IndexPageApplication class.
 * @extends Madone_Application
 */

class IndexPageApplication extends Madone_Application {
    function index() {
        print $this->render("index-page.twig", array('page' => $this->page ));
        return true;
    }
}