<?php
/**
 * IndexPageApplication class.
 * @extends Madone_Application
 */

class Madone_Module_Pages_Application extends Madone_Application {
    function index() {
        print $this->render("index-page.twig", array('page' => $this->page ));
        return true;
    }
}