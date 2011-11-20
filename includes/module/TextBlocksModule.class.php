<?

class TextBlocksModule extends Madone_Module {

    function handleHtmlRequest( $uri ) {

        return $this->getTemplate( 'index', array( 'items' => MadoneTextBlocks()->orderAsc( 'name' )->all() ) );
    }
}

?>