<?php
/**
 * 
 * @author \$Author$
 */
 
class Module_Content_Application extends AbstractApplication {
    public function run(MadonePage $page, $uri) {
        $content = MadonePageContents(array('page' => $page))->first();
        return $this->render('index-page.twig', array('content' => $content));
    }
}