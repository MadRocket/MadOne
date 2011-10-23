<?php

class ContentModule extends AbstractModule {

    function handleHtmlRequest( $uri ) {
        $content = MadoneContents()->get();
        return $this->getTemplate( 'index.twig', array( 'content' => $content ) );
    }
}