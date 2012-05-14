<?php

class Madone_Module_Feedback_Application extends Madone_Application {
    protected $routes = array(
        '/?' => 'index',
        'send' => 'send'
    );

    function index() {
        $this->page->text .= $this->form();
        $vars = Madone_Utilites::vars();
        $mode = null;

        if( array_key_exists( 'text', $vars ) && $vars['text'] ) {
            Model_Feedbackmessages()->create( $vars );
            $mode = 'added';
        }
        else {
            $mode = 'notext';
        }

        return $this->render('index.twig', array(
            'mode' => $mode,
            'vars' => Madone_Utilites::vars()
        ));
    }

    function form() {
        return $this->render('form.twig', array(
            'vars' => Madone_Utilites::vars()
        ));
    }
}
