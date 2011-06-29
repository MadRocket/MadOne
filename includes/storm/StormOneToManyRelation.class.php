<?
/**
    Класс, описывающий связь между двумя моделями через ForeignKey.
    Это связь один-ко-многим, например, автомобиль и владелец.
    У владельца может быть несколько автомобилей, но у каждого автомобиля только один владелец.
    В данном случае модель Автомобиль выступает в роли источника, а модель Владелец в роли получателя.
    У модели получателя должно появиться специальное поле для быстрого доступа к данным из его источников.
    Эти сведения и хранятся экземпляром класса StormOneToManyRelation.
    Пример связи: new StormOneToManyRelation( 'Owner', 'owner_id', 'Car', 'cars' );
    В модели Owner будет поле cars для достаупа к RelatedStormQuerySetу с амтомобилями
    А у модели Car будет поле owner_id с идентификатором владельца
*/

class StormOneToManyRelation
{
    private $key_model;  // Модель, содержащая ключевые элементы, каждый из которых соответствует множеству элементов из set_model.
    private $key_field_name; // Имя поля в модели set_model, соответствующее идентификатору из модели key_model.
    private $set_model;  // Модель, содержащая множество элементов.
    private $related_queryset_name;  // Имя источника данных key_model, выдающего записи из set_model.

    /**
        Конструктор
        Просто инициализирует переденные переменные
    */
    public function __construct( $key_model, $key_field_name, $set_model, $related_queryset_name )
    {
        // Проверим, чтобы классы являлись моделями Storm
        foreach( array( $key_model, $set_model ) as $classname )
        {
            if( ! ( class_exists( $classname ) && is_subclass_of( $classname, 'StormModel' ) ) )
            {
                throw new StormException( "'{$classname}' is not a Storm model" );
            }
        }
        
        $this->key_model = $key_model;
        $this->key_field_name = $key_field_name;
        $this->set_model = $set_model;
        $this->related_queryset_name = $related_queryset_name;
    }
    
    final function __get( $name )
    {
        // Разрешаем получение имеющихся в объекте полей
        if( property_exists( $this, $name ) )
        {
            return $this->{ $name };
        }
    }
}



?>
