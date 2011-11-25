<?
/**
 * Товар интернет-витрины
 */

class MadoneShowcaseItem extends Storm_Model
{
    static function definition()
    {
        return array(
            'title' => new Storm_Db_Field_Char(array('maxlength' => 255, 'default' => 'Новая позиция', 'fulltext' => true)),
            'section' => new Storm_Db_Field_Fk(array('model' => 'MadoneShowcaseSection', 'related' => 'items')),
            'description' => new Storm_Db_Field_Text(array('fulltext' => true)),
            'short_description' => new Storm_Db_Field_Text(),
            'price' => new Storm_Db_Field_Float(array('index' => true)),
            'in_stock' => new Storm_Db_Field_Integer(array('default' => 0)),
            'special' => new Storm_Db_Field_Bool(array('localized' => false, 'default' => 0, 'index' => true)),
            'enabled' => new Storm_Db_Field_Bool(array('localized' => true, 'default' => 0, 'index' => true)),
            'position' => new Storm_Db_Field_Integer(),

            // Meta
            'date_added' => new Storm_Db_Field_Datetime(array('default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true)),
            'date_modified' => new Storm_Db_Field_Datetime(array('default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true)),
            'linked_items' => new Storm_Db_Field_Text(array('localized' => false)),
            'views_counter' => new Storm_Db_Field_Integer(array('default' => 0)),
            'added_to_cart_counter' => new Storm_Db_Field_Integer(array('default' => 0)),
        );
    }

    public function getImages()
    {
        return $this->images->order('position')->all();
    }

    public function getFirstImage()
    {
        return $this->images->order('position')->first();
    }

    function beforeSave()
    {
        $this->date_modified = time();
    }

    function beforeDelete()
    {
        foreach ($this->images->all() as $i) {
            $i->delete();
        }
    }

    function afterSave($new)
    {
        if ($new && !$this->position) {
            $last = $this->getQuerySet()->filter(array('id__ne' => $this->id, 'section' => $this->section))->orderDesc('position')->first();
            $this->position = $last ? $last->position + 1 : 1;
            $this->hiddenSave();
        }
    }

    function view()
    {
        $this->views_counter = $this->views_counter + 1;
        $this->hiddenSave();
    }

    function cart()
    {
        $this->added_to_cart_counter = $this->added_to_cart_counter + 1;
        $this->hiddenSave();
    }
}