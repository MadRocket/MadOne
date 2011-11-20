<?php
/**
 * 
 * @author \$Author$
 */
 
class Module_Promo_Admin extends Madone_Module {
    function handleHtmlRequest($uri) {
        if(!empty($_POST['submit'])) {
            $promo = PromoImages()->create($_POST);
            $promo->image = empty($_FILES['image']) ? null : $_FILES['image'];
            $promo->save();
        }

        $items = PromoImages()->order('position')->all();
        return $this->getTemplate('index.twig', array('items' => $items));
    }
}

?>