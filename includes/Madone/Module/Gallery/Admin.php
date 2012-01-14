<?php

class Madone_Module_Gallery_Admin extends Madone_Module {
    protected $routes = array(
        '/[i:id]' => '_index'
    );

    function _index($id) {
        if ($page = Model_Pages()->get($id)) {
            $pagination = new Madone_Paginator($page->images->order('position')->order('id'), 20);
            $pagination->setContainer($this->container);

            return $this->getTemplate('index.twig', array(
                'paginator' => $pagination,
                'items' => $pagination->getObjects(),
                'page' => $page,
            ));
        }
        return null;
    }
}
