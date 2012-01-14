<?php

class Madone_Module_Textblocks_Admin extends Madone_Module {
    function index() {
        return $this->getTemplate( 'index.twig', array( 'items' => Model_TextBlocks()->orderAsc( 'name' )->all() ) );
    }
}
