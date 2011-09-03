<?php
/**
 * Новость
 */
class MadoneNews extends StormModel
{
    static function definition()
    {
        return array (
            'page' => new StormFkDbField(array('model' => 'MadonePage', 'related' => 'news')),
            
            'date' => new StormDatetimeDbField(array('default_callback' => 'return time();', 'format' => '%d.%m.%Y', 'index' => true)),
            'title' => new StormCharDbField(),
            'text' => new StormTextDbField(),
            'announce' => new StormTextDbField(),
            'enabled' => new StormBoolDbField(array('default' => 1, 'index' => true)),
        );
    }
}