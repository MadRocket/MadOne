<?

class TextBlocksModule extends AbstractModule {

    function handleHtmlRequest( $uri ) {

        return $this->getTemplate( 'index', array( 'items' => MadoneTextBlocks()->orderAsc( 'name' )->all() ) );
    }
}

?>