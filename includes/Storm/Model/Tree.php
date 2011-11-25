<?
/**
    Ki powered модель Storm ^_^
*/

abstract class Storm_Model_Tree extends Storm_Model
{
    private $ki_integrity_check_enabled = true;
    private $ki_delete_correction_enabled = true;
    
    private $_children = null;  // Дочерние элементы узла для использования в выборке узлов в виде дерева

    /**
        Системное внедрение в definition — добавляем поля Ki-движка.
    */
    final function _definition()
    {
        return array
        (
            'lk'       => new Storm_Db_Field_Integer( array( 'null' => false, 'index' => 'Ki' ) ),
            'rk'       => new Storm_Db_Field_Integer( array( 'null' => false, 'index' => 'Ki' ) ),
            'lvl'      => new Storm_Db_Field_Integer( array( 'null' => false, 'index' => 'Ki' ) ),
        );
    }
    
    /**
        Обработка сохраненной записи — поддержка целостности.
    */
    final function _afterSave( $new )
    {
        if( $new )
        {
        
            $cursor = Storm_Core::getBackend()->cursor;
            $table = Storm_Core::getBackend()->escapeName( Storm_Core::getMapper()->getModelTable( $this->meta->name ) );
            // Вставка уже произошла, нужно освободить место для новой записи
            $cursor->execute( "UPDATE {$table} SET rk = rk + 2, lk = CASE WHEN lk >= %{lk} THEN lk + 2 ELSE lk END WHERE rk >= %{lk} AND {$this->meta->pkname} != %{pk}", array( 'lk' => $this->lk, 'pk' => $this->meta->pk ) );
        }

        $this->checkKiIntegrity();
    }
    
    /**
        Обработка удаленной записи — поддержка целостности и удаление вложенных записей.
    */
    final function _afterDelete()
    {
        $cursor = Storm_Core::getBackend()->cursor;
        $table = Storm_Core::getBackend()->escapeName( Storm_Core::getMapper()->getModelTable( $this->meta->name ) );

        // Удаляем вложенные узлы, если они есть
        if( $this->rk - $this->lk > 1 )
        {
            foreach( $this->getQuerySet()->filter( array( 'lk__ge' => $this->lk, 'rk__le' => $this->rk, 'lvl' => $this->lvl + 1 ) )->all() as $i )
            {
                $i->ki_integrity_check_enabled = false;  // отключаем проверку целостности
                $i->ki_delete_correction_enabled = false; // отключаем уплотнение дерева
                $i->delete();
            }
        }

        // Уплотняем дерево, если уплотнение не отключено
        if( $this->ki_delete_correction_enabled )
        {
            $cursor->execute( "UPDATE {$table} SET rk = rk - %{offset}, lk = CASE WHEN lk > %{rk} THEN lk - %{offset} ELSE lk END WHERE rk > %{rk}", array( 'offset' => $this->rk - $this->lk + 1, 'rk' => $this->rk ) );
        }

        $this->checkKiIntegrity();
    }
    
    /**
        Проверка всего дерева модели на соответствие правилам Ki.
    */
    function checkKiIntegrity()
    {
        if( ! $this->ki_integrity_check_enabled ) return true;
        
        try {
            $cursor = Storm_Core::getBackend()->cursor;
            $table = Storm_Core::getBackend()->escapeName( Storm_Core::getMapper()->getModelTable( $this->meta->name ) );
    
            // Левый ключ любого узла должен быть меньше его же правого ключа
            $cursor->execute( "SELECT {$this->meta->pkname} AS pk FROM {$table} WHERE lk >= rk" );
            $bad_keys = array_map( create_function( '$row', 'return $row["pk"];' ), $cursor->fetchAll() );
            if( count( $bad_keys ) ) throw new Storm_Exception( "Ki integrity check failed: got nodes with lk >= rk: ".join( ', ', $bad_keys )."." );
            
            // Наименьший левый ключ должен быть равен 1
            $cursor->execute( "SELECT lk FROM {$table} ORDER BY lk ASC LIMIT 1" );
            $min_lk = $cursor->fetchOne( 'lk' );
            if( $min_lk != 1 ) throw new Storm_Exception( "Ki integrity check failed, the least lk = {$min_lk} instead of 1." );
            
            // Наибольший правый ключ должен быть равен удвоенному количеству узлов
            $cursor->execute( "SELECT rk FROM {$table} ORDER BY rk DESC LIMIT 1" );
            $max_rk = $cursor->fetchOne( 'rk' );
            $cursor->execute( "SELECT count(*) as count FROM {$table}" );
            $count = $cursor->fetchOne( 'count' );
            if( $max_rk != $count * 2 ) throw new Storm_Exception( "Ki integrity check failed, the biggest rk = {$max_rk} instead of ".( $count * 2 )."." );
    
            // Разница между правым и левым ключом любого узла должны быть нечетным числом
            $cursor->execute( "SELECT {$this->meta->pkname} AS pk FROM {$table} WHERE MOD( (rk - lk), 2 ) = 0" );
            $bad_keys = array_map( create_function( '$row', 'return $row["pk"];' ), $cursor->fetchAll() );
            if( count( $bad_keys ) ) throw new Storm_Exception( "Ki integrity check failed, there are nodes with even (rk - lk): ".join( ', ', $bad_keys )."." );
            
            // Четность уровня вложенности любого узла должна совпадать с четностью левого ключа этого же узла
            $cursor->execute( "SELECT {$this->meta->pkname} AS pk FROM {$table} WHERE MOD( (lk - lvl), 2 ) = 1" );
            $bad_keys = array_map( create_function( '$row', 'return $row["pk"];' ), $cursor->fetchAll() );
            if( count( $bad_keys ) ) throw new Storm_Exception( "Ki integrity check failed, there are nodes with odd (lk - lvl): ".join( ', ', $bad_keys )."." );
            
            // Во всем дереве не должно быть ни одного повторяющегося значения ключа, как правого, так и левого
            $cursor->execute( "SELECT t1.{$this->meta->pkname} AS pk  FROM {$table} AS t1 INNER JOIN {$table} AS t2 ON t1.{$this->meta->pkname} != t2.{$this->meta->pkname} AND ( t1.lk = t2.lk OR t1.lk = t2.rk OR t1.rk = t2.lk OR t1.rk = t2.rk )" );
            $bad_keys = array_map( create_function( '$row', 'return $row[pk];' ), $cursor->fetchAll() );
            if( count( $bad_keys ) ) throw new Storm_Exception( "Ki integrity check failed, there are duplicated keys: ".join( ', ', $bad_keys )."." );
        }
        catch( Storm_Exception $e ) {
            // При проверке валидности обнаружена ошибка! Исправим индекс и выбросим exception заново :D
            $this->rebuildKiIndex();
            throw new Storm_Exception( $e->getMessage() . " Ki index was automatically rebuilt." );
        }

        return true;
    }
    
    /**
    *   Принудительное восстановление Ki-индексов.
    *   Максимально сохраняет имеющуюся структуру дерева.
    */
    function rebuildKiIndex() {
        // Воспользуемся тем, что метод tree всегда возвращает правильное дерево без разрывов
        $tree = $this->getQuerySet()->kiOrder()->tree();
        
        // Просто выполним reorder :D
        $this->getQuerySet()->reorder( $this->buildReorderTree( $tree[0] ) );
    }
    
    /**
    *   Создание дерева для reorder() на основе дерева от queryset-а
    */
    protected function buildReorderTree( $item ) {
        $node = array( 'id' => $item->id, 'children' => array() );
    
        foreach( $item->getChildren() as $child ) {
            $node['children'][] = $this->buildReorderTree( $child );
        }
    
        return $node;
    }
        
    /**
        Перемещение узла вместе с его подузлами на новое место
        $new_place — новый левый ключ узла - 1
        $new_level — новый уровень вложенности узла
    */
    protected function moveAt( $new_place, $new_level )
    {
        // Проверяем возможность перемещения: мы не можем переместить узел в него же
        if( $new_place >= $this->lk && $new_place <= $this->rk ) throw new Storm_Exception( "Cannot move node into itself" );
        
        // Cмещение ключей перемещаемого узла
        $tree_offset = $this->rk - $this->lk + 1;
        // Смещение уровня
        $level_offset = $new_level - $this->lvl;
        
        // Курсор БД
        $cursor = Storm_Core::getBackend()->cursor;
        
        // Наша таблица
        $table = Storm_Core::getBackend()->escapeName( Storm_Core::getMapper()->getModelTable( $this->meta->name ) );

        // Перемещаем вниз по дереву
        if( $this->rk < $new_place )
        {
            // Определяем смещение ключей для дерева
            $offset = $new_place - $this->lk + 1 - $tree_offset;
            
            // Переносим узел и одновременно обновляем дерево
            $cursor->execute( "UPDATE {$table} SET
            lk = CASE WHEN rk <= %{rk} THEN lk + %{offset} ELSE CASE WHEN lk > %{rk} THEN lk - %{tree_offset} ELSE lk END END,
            lvl = CASE WHEN rk <= %{rk} THEN lvl + %{level_offset} ELSE lvl END,
            rk = CASE WHEN rk <= %{rk} THEN rk + %{offset} ELSE CASE WHEN rk <= %{new_place} THEN rk - %{tree_offset} ELSE rk END END
            WHERE rk > %{lk} AND lk <= %{new_place}",
            array( 'rk' => $this->rk, 'lk' => $this->lk, 'offset' => $offset, 'tree_offset' => $tree_offset, 'level_offset' => $level_offset, 'new_place' => $new_place )
            );
        }

        // Перемещаем вверх по дереву
        else 
        {
            // Определяем смещение ключей для дерева
            $offset = $new_place - $this->lk + 1;
    
            // Переносим узел и одновременно обновляем дерево
            $cursor->execute( "UPDATE {$table} SET
            rk = CASE WHEN lk >= %{lk} THEN rk + %{offset} ELSE CASE WHEN rk < %{lk} THEN rk + %{tree_offset} ELSE rk END END,
            lvl = CASE WHEN  lk >= %{lk} THEN lvl + %{level_offset} ELSE lvl END,
            lk = CASE WHEN lk >= %{lk} THEN lk + %{offset} ELSE CASE WHEN lk > %{new_place} THEN lk + %{tree_offset} ELSE lk END END
            WHERE rk > %{new_place} AND lk < %{rk}",
            array( 'rk' => $this->rk, 'lk' => $this->lk, 'offset' => $offset, 'tree_offset' => $tree_offset, 'level_offset' => $level_offset, 'new_place' => $new_place )
            );
        }
        
        // Проверим целостность
        $this->checkKiIntegrity();

        // Перечитаем даные узла из базы
        $this->copyFrom( $this->getQuerySet()->getNotCached( $this->meta->getPkValue() ) );
        
        // Если модель имеет обработчики before/afterSave — прогоним сохранение каждой изменившейся записи
        if( method_exists( $this, 'beforeSave' ) || method_exists( $this, 'afterSave' ) )
        {
            // Получим условие выборки, аналогично условиям обновления выше
            $cond = $this->rk < $new_place ? array( 'rk__ge' => $this->lk, 'lk__le' => $new_place ) : array( 'rk__ge' => $new_place, 'lk__le' => $this->lk );
            foreach( $this->getQuerySet()->filter( $cond )->all() as $i )
            {
                $i->ki_integrity_check_enabled = false; // Отключим проверку целостности, чтобы не убивать базу запросами
                $i->save();
            }
        }        
    }
    
    /**
        Проверка и инициализация узла
    */
    protected function getObject( $node )
    {
        if( ! is_object( $node ) )
        {
            $qs = new Storm_Queryset( $this->meta->name );
        
            if( ! $record = $qs->get( $node ) )
            {
                throw new Storm_Exception( "Cannot get an anchor node with key='{$node}' for {$this->meta->name} model" );
            }
            $node = $record;
        }
        else if( ! $node instanceof $this->meta->name )
        {
            throw new Storm_Exception( "Cannot use ".get_class( $node )." class as an anchor for {$this->meta->name} model" );
        }
        
        return $node;
    }
    
    /**
        Перемещение узла в дочерние другого, первым или последним
        $parent — узел-родитель
        $place, 0 — первым, 1 — последним.
    */
    protected function putAsChild( $parent, $place = 1 )
    {
        // Получим родительский узел
        $parent = $this->getObject( $parent );
        
        // Перемещаем
        return  $this->moveAt( $place ? $parent->rk - 1 : $parent->lk, $parent->lvl + 1 );
    }
    
    /**
        Перемещение узла в соседние указанному
        $neighbour — соседний узел
        $place, 0 — перед соседом, 1 — после соседа
    */
    protected function putAsNeighbour( $neighbour, $place )
    {
        // Получим соседний узел
        $neighbour = $this->getObject( $neighbour );

        // Проверим, не перемещаем ли до/после главной страницы
        if( $neighbour->lvl == 1 ) throw new Storm_Exception( "Cannot put node ".( $place ? "after" : "before" )." a root node for {$this->meta->name} model" );

        // Перемещаем
        return  $this->moveAt( $place ? $neighbour->rk : $neighbour->lk - 1, $neighbour->lvl );
    }

    /**
        Поместить в качестве первого ребенка $parent
    */    
    function putAsFirstChild( $parent )
    {
        $this->putAsChild( $parent, 0 );
    }

    /**
        Поместить в качестве последнего ребенка $parent
    */    
    function putAsLastChild( $parent )
    {
        $this->putAsChild( $parent, 1 );
    }

    /**
        Поместить перед узлом $neighbour
    */    
    function putBefore( $neighbour )
    {
        $this->putAsNeighbour( $neighbour, 0 );
    }

    /**
        Поместить после узла $neighbour
    */    
    function putAfter( $neighbour )
    {
        $this->putAsNeighbour( $neighbour, 1 );
    }
    
    /**
        Представление объекта в виде строки - Ki-powered generic-овая версия.
        Отображает объект в виде Имя_модели(имя_первичного_ключа: значение_первичного_ключа, lk: ключ, rk: ключ, lvl: уровень)
    */
    function __toString()
    {
        return "{$this->meta->name} ({$this->meta->pkname}: ". ( is_null( $this->{ $this->meta->pkname } ) ? 'NULL' : $this->{ $this->meta->pkname } ).', '
        .'lk: '.( is_null( $this->lk ) ? 'NULL' : $this->lk ).', '
        .'rk: '.( is_null( $this->rk ) ? 'NULL' : $this->rk ).', '
        .'lvl: '.( is_null( $this->lvl ) ? 'NULL' : $this->lvl ).')';
    }
    
    /**
        Получение QuerySet-а объектов этой модели
    */
    function getQuerySet()
    {
        return new Storm_Queryset_Tree( $this->meta->name );
    }
    
    /**
        Сохранение с выключенной проверкой целостности Ki-индексов
    */
    function saveWithoutCheck()
    {
        $this->ki_integrity_check_enabled = false;
        $this->save();
        $this->ki_integrity_check_enabled = true;
    }

    function hiddenSaveWithoutCheck()
    {
        $this->ki_integrity_check_enabled = false;
        $this->hiddenSave();
        $this->ki_integrity_check_enabled = true;
    }
    
    /**
        Установка дочерних узлов
    */
    function addChild( $node )
    {
        if( ! is_array( $this->_children ) ) throw new Storm_Exception( "Cannot set children of not children-enabled object"  );
        $this->_children[] = $node;
    }
    
    function enableChildren()
    {
        $this->_children = array();
    }
    
    /**
        Получение дочерних узлов
    */
    function getChildren()
    {
        if( ! is_array( $this->_children ) ) throw new Storm_Exception( "Cannot get children of not children enabled object. Consider using ->tree() method of QuerySet to get objects with children."  );
        return $this->_children;
    }
    
    /**
    *   Возврат объекта в виде массива
    */
    function asArray( $full = false ) {
        $result = parent::asArray( $full );
        
        if( is_array( $this->_children ) ) {
        
            $result['children'] = array();
        
            foreach( $this->_children as $child ) {
                $result['children'][] = $child->asArray( $full );
            }
        }
    
        return $result;
    }
}

?>