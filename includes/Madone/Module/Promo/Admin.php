<?php
/**
 * 
 * @author \$Author$
 */
 
class Madone_Module_Promo_Admin extends AbstractModule {
    function handleHtmlRequest($uri) {
        if(array_key_exists('submit', $_POST)) {
            if(array_key_exists('id', $_POST)) {
                if( $_FILES['image']['name'] ) {
                    $_POST['image'] = $_FILES['image'];
                }

                $staff = PromoImages()->get($_POST['id']);
                $staff->copyFrom($_POST);
                $staff->save();
            }
            else {
                $_POST['image'] = $_FILES['image'];
                $staff = PromoImages()->create($_POST);
            }

            header("Location: {$this->uri}");
            return true;
        }

        $items = PromoImages()->order('position')->all();
        return $this->getTemplate('index.twig', array('items' => $items));
    }
}