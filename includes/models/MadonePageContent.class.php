<?php
/**
 * 
 * @author \$Author$
 */
 
class MadonePageContent extends StormModel {
    static function definition() {
        return array(
            'page' => new StormFkDbField(array('model' => 'MadonePage', 'related' => 'content' )),
            'content' => new StormTextDbField()
        );
    }
}

?>