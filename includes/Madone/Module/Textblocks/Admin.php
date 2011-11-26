<?

class Madone_Module_Textblocks_Admin extends Madone_Module {

    function handleHtmlRequest( $uri ) {

        return $this->getTemplate( 'index', array( 'items' => Model_TextBlocks()->orderAsc( 'name' )->all() ) );
    }
}

?>