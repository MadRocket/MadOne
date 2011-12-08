<?php
/**
 * IndexPageApplication class.
 * @extends Madone_Application
 */

class Madone_Module_Pages_Application extends Madone_Application {
    function index() {
        $template = $this->page->template;
        if(! $template) {
            $template = $this->page->lvl == 1 ? "index.twig" : "default.twig";
        }
        print $this->render($template, array('page' => $this->page ));
        return true;
    }
}