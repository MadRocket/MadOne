<?

/**
    Запрашивалка данных Ki-модели.
    Отличается интересными методами для создания объектов.
*/

class StormKiQuerySet extends StormQuerySet
{
	static protected $joinNumber = 1;
    /**
        Конструктор.
        $model -  имя модели, для которой конструируется StormKiQuerySet.
    */
    function __construct( $model )
    {
        // Сконструируем все родительские штуки
        parent::__construct( $model );

    }
    
    /**
        Сортировка по положению в дереве
    */
    function kiOrder()
    {
        // Выставляем упорядочивание по левому ключу
        return $this->order( 'lk' );
    }

    /**
        Создание подузла, установка его первым
        $parent — объект или id родителя
        $params — данные для кинициализации объекта, как в конструкторе
    */
    function createFirstChild( $parent, array $params )
    {
        return $this->_create( $parent, 'first-child', $params );
    }

    /**
        Создание подузла, установка его последним
        $parent — объект или id родителя
        $params — данные для инициализации объекта, как в конструкторе
    */
    function createLastChild( $parent, array $params )
    {
        return $this->_create( $parent, 'last-child', $params );
    }

    /**
        Создание узла перед указаным
        $neighbour — объект или id соседнего узла
        $params — данные для инициализации объекта, как в конструкторе
    */
    function createBefore( $neighbour, array $params )
    {
        return $this->_create( $neighbour, 'before', $params );
    }

    /**
        Создание узла после указанного
        $neighbour — объект или id соседнего узла
        $params — данные для инициализации объекта, как в конструкторе
    */
    function createAfter( $neighbour, array $params )
    {
        return $this->_create( $neighbour, 'after', $params );
    }
    
    /**
        Создание корневого узла
        $params — данные для инициализации объекта, как в конструкторе
    */
    function createRoot( array $params )
    {
        return $this->_create( null, 'before', $params );
    }

    /**
        Создание объекта с Ki-сортировкой по переданным аргументам
        $anchor — ключевая запись, отностительно которой производится вставка новой. null — вставка корневой записи.
        $mode — режим вставки относительно $anchor: 'before', 'after', 'last-child', 'first-child'.
    */
    public function _create( $anchor, $mode, array $params )
    {
        // Проверим режим добавления
        if( ! in_array( $mode, array( 'before', 'after', 'last-child', 'first-child' ) ) ) throw new StormException( "Unknown StormKiModel creation mode '{$mode}'" );
        
        // Проверим ключевой узел
        if( ! $anchor )
        {
            // Создаем корень дерева — проверим, чтобы таблица была пуста
            if( $this->filter( array( 'lvl__eq' => 1 ) )->first() )
            {
                // В таблице уже есть элементы — корень создать не получится
                throw new StormException( "Root node of {$this->model} model already exists" );
            }
            
            $params['lk'] = 1;
            $params['rk'] = 2;
            $params['lvl'] = 1;
        }
        else
        {
            // Проверяем ключевую запись
            if( ! is_object( $anchor ) )
            {
                if( ! $record = $this->getNotCached( $anchor ) )
                {
                    throw new StormException( "Cannot get an anchor node with key='{$anchor}' for {$this->model} model" );
                }
                $anchor = $record;
            }
            else if( ! $anchor instanceof $this->model )
            {
                throw new StormException( "Cannot use ".get_class( $anchor )." class as an anchor for {$this->model} model" );
            }
            
            // Смотрим, куда вставляется новая запись
            switch( $mode )
            {
                case 'first-child':
					$params['lvl'] = $anchor->lvl + 1;
					$params['lk']  = $anchor->lk + 1;
					$params['rk']  = $anchor->lk + 2;
                    break;
                
                case 'last-child':
					$params['lvl'] = $anchor->lvl + 1;
					$params['lk']  = $anchor->rk;
					$params['rk']  = $anchor->rk + 1;
                    break;
                
                case 'before':
                    if( $anchor->lvl == 1 ) throw new StormException( "Cannot add nodes before a root node for {$this->model} model" );
					$params['lvl'] = $anchor->lvl;
					$params['lk']  = $anchor->lk;
					$params['rk']  = $anchor->lk + 1;
                    break;
                
                case 'after':
                    if( $anchor->lvl == 1 ) throw new StormException( "Cannot add nodes after a root node for {$this->model} model" );
					$params['lvl'] = $anchor->lvl;
					$params['lk']  = $anchor->rk + 1;
					$params['rk']  = $anchor->rk + 2;
                    break;
            }
        }

        // Создадим новый объект с переданными параметрами
        $obj = new $this->model( $params );

        // Сохраним его в БД и вернем
        $obj->save();

        return $obj;
    }

    /**
    *   Создание новой записи. Запись создается первой дочерней корня или корневой, если корневой записи нет.
    */
    function create( array $params ) {
        // Выберем корневой элемент
        $root = $this->filterLevel( 1 )->first();
        // Если корень не найден, новая запись станет корнем не смотря на режим first-child
        return $this->_create( $root, 'first-child', $params );
    }

    /**
        Получение узлов определенного уровня вложенности
        $a — конкретный уровень (если не указан $b) или левая граница диапазона
        $b — правая граница диапазона, границы включаются в выборку
        $a = 0 означает «начиная с первого уровня»
        $b = 0 означает «до самого последнего уровня»
    */
    function queryLevel( $embrace, $a, $b = null )
    {
        $params = null;
    
        // Не указан $b — выбираем конкретный уровень
        if( is_null( $b ) )
        {
            $params['lvl__eq'] = $a;
        }
        else
        {
            if( $a ) $params['lvl__ge'] = $a;
            if( $b ) $params['lvl__le'] = $b;
        }
        
        if( ! count( $params ) ) throw new StormException( "Bad filterLevel() arguments" );
        
        return $embrace ? $this->embrace( $params ) : $this->filter( $params );
    }
    
    function filterLevel( $a, $b = null )
    {
        return $this->queryLevel( false, $a, $b );
    }
    
    function embraceLevel( $a, $b = null )
    {
        return $this->queryLevel( true, $a, $b );
    }

    /**
        Фильтрация или включение узлов
        $item — опорный узел
        $where — SQL условие выборки
        $embrace — включить выбранные узлы, если false — выполняется фильтрация выбраных узлов
    */
    
    private function queryItems( $item, $where, $embrace = false )
    {
        if( is_object( $item ) ) $item = $item->meta->getPkValue();
        
        // Клонируем себя, чтобы остаться неизменным
        $next = clone $this;
        
        // Добавляем параметров следующему в цепочке StormQuerySet-у
        
        $table = StormCore::getMapper()->getModelTable( $this->model );
        $alias = StormCore::getMapper()->getModelAlias( $this->model );
        $item_alias = "{$alias}__ki__{$item}__".self::$joinNumber++;
        $pk = StormCore::getInstance()->getStormModelMetadata( $this->model )->pkname;
        
        $qc = new StormQCSIMPLE( 
        new StormQCSimpleOp( array
        (
            'joins' => array( new StormQueryJoin( 'INNER', $table, $item_alias, "{$item_alias}.{$pk} = %s" ) ),
            'where' => StormUtilities::array_printf( $where, array( 'alias' => $alias, 'item_alias' => $item_alias ) ),
            'params' => array( $item ),
        ) ) );

        $next->qc = $next->qc ? ( $embrace ? QOR( $qc, $next->qc ) : QAND( $qc, $next->qc ) ) : $qc;
        
        // Возвращаем следующий объект
        return $next;
    }

    /**
        Получение родительской ветки
    */
    function filterParents( $item )
    {
        return $this->queryItems( $item, '%{alias}.lk < %{item_alias}.lk AND %{alias}.rk > %{item_alias}.rk', false );
    }

    /**
        Получение дочерних узлов
    */
    function filterChildren( $item )
    {
        return $this->queryItems( $item, '%{alias}.lk > %{item_alias}.lk AND %{alias}.rk < %{item_alias}.rk', false );
    }

    /**
        Получение родительской ветки
    */
    function embraceParents( $item )
    {
        return $this->queryItems( $item, '%{alias}.lk < %{item_alias}.lk AND %{alias}.rk > %{item_alias}.rk', true );
    }

    /**
        Получение дочерних узлов
    */
    function embraceChildren( $item )
    {
        return $this->queryItems( $item, '%{alias}.lk > %{item_alias}.lk AND %{alias}.rk < %{item_alias}.rk', true );
    }
    
    /**
        Получение стоящих рядом узлов
    */
    private function querySiblingItems( $item, $where, $parent_where, $embrace = false )
    {
        if( is_object( $item ) ) $item = $item->meta->getPkValue();
        
        // Клонируем себя, чтобы остаться неизменным
        $next = clone $this;
        
        // Добавляем параметров следующему в цепочке StormQuerySet-у
        
        $table = StormCore::getMapper()->getModelTable( $this->model );
        $alias = StormCore::getMapper()->getModelAlias( $this->model );
        $item_alias = "{$alias}__ki__{$item}__".self::$joinNumber++;
        $parent_alias = "{$alias}__ki__parent__{$item}__".self::$joinNumber++;
        $pk = StormCore::getInstance()->getStormModelMetadata( $this->model )->pkname;
        
        $qc = new StormQCSIMPLE( 
        new StormQCSimpleOp( array
        (
            'joins' => array
                     (
                        new StormQueryJoin( 'INNER', $table, $item_alias, "{$item_alias}.{$pk} = %s" ),
                        new StormQueryJoin( 'INNER', $table, $parent_alias, "{$parent_alias}.lk <= {$item_alias}.lk AND {$parent_alias}.rk >= {$item_alias}.rk AND ". StormUtilities::array_printf( $parent_where, array( 'alias' => $alias, 'item_alias' => $item_alias, 'parent_alias' => $parent_alias ) ) ),
                     ),
            'where' => StormUtilities::array_printf( $where, array( 'alias' => $alias, 'item_alias' => $item_alias, 'parent_alias' => $parent_alias ) ),
            'params' => array( $item ),
        ) ) );

        $next->qc = $next->qc ? ( $embrace ? QOR( $qc, $next->qc ) : QAND( $qc, $next->qc ) ) : $qc;
        
        // Возвращаем следующий объект
        return $next;
    }
    
    /**
        Получение элемента и его соседей на том же уровне
    */
    function filterSiblings( $item )
    {
        return $this->querySiblingItems( $item, '( %{alias}.lk > %{parent_alias}.lk AND %{alias}.rk < %{parent_alias}.rk AND %{alias}.lvl = %{item_alias}.lvl )', '%{parent_alias}.lvl = %{item_alias}.lvl - 1', false );
    }

    /**
        Получение элемента, его соседей на том же уровне и всех детей соседей и самого элемента
    */
    function filterSiblingsAndChildren( $item )
    {
        return $this->querySiblingItems( $item, '( %{alias}.lk > %{parent_alias}.lk AND %{alias}.rk < %{parent_alias}.rk )', '%{parent_alias}.lvl = %{item_alias}.lvl - 1', false );
    }

    /**
        Получение элемента и его соседей на том же уровне
    */
    function embraceSiblings( $item )
    {
        return $this->querySiblingItems( $item, '( %{alias}.lk > %{parent_alias}.lk AND %{alias}.rk < %{parent_alias}.rk AND %{alias}.lvl = %{item_alias}.lvl )', '%{parent_alias}.lvl = %{item_alias}.lvl - 1', true );
    }

    /**
        Получение элемента, его соседей на том же уровне и всех детей соседей и самого элемента
    */
    function embraceSiblingsAndChildren( $item )
    {
        return $this->querySiblingItems( $item, '( %{alias}.lk > %{parent_alias}.lk AND %{alias}.rk < %{parent_alias}.rk )', '%{parent_alias}.lvl = %{item_alias}.lvl - 1 ', true );
    }

    /**
        Получение ветки, в которой участвует узел
    */
    function filterBranch( $item )
    {
        return $this->querySiblingItems( $item, '( %{alias}.lk > %{parent_alias}.lk AND %{alias}.rk < %{parent_alias}.rk )', '%{parent_alias}.lvl = 2', false );
    }

    /**
        Получение ветки, в которой участвует узел
    */
    function embraceBranch( $item )
    {
        return $this->querySiblingItems( $item, '( %{alias}.lk > %{parent_alias}.lk AND %{alias}.rk < %{parent_alias}.rk )', '%{parent_alias}.lvl = 2', true );
    }

    /**
        Переупорядочивание всего дерева в соответствии с переданным образцом
        $order — массив, определяющий новый порядок дерева
        ключ — идентификатор узла, значение — уровень вложенности
    */
    function reorder( array $order_tree )
    {
        // Выберем все узлы, которые у нас есть
        $qs = new StormKiQuerySet( $this->model );
        $old = $qs->follow(2)->kiOrder()->all();
        
        $new = $this->getKies( $order_tree );
        
        // Проверим совпадение размера массивов
        if( count( $old ) != count( $new ) )
        {
            throw new StormException( "New order items count does not match with items count in the database" );
        }
        
        $cursor = StormCore::getBackend()->cursor;
        $table = StormCore::getBackend()->escapeName( StormCore::getMapper()->getModelTable( $this->model ) );

        // Просто идем по массиву старых записей и обновляем ключи
        // Обновляем БД напрямую, чтобы не генерить лишних запросов и к работе save() моделей ключи были уже в новом состоянии
        foreach( $old as $i )
        {
            if( ! array_key_exists( $i->id, $new ) ) throw new StormException( "New order item set does not match with one stored in the database" );
            $cursor->execute( "UPDATE {$table} SET rk = %{rk}, lk = %{lk}, lvl = %{lvl} WHERE id = %{id}", $new[ $i->id ] );
            $i->lvl = $new[ $i->id ]['lvl'];
            $i->lk = $new[ $i->id ]['lk'];
            $i->rk = $new[$i->id]['rk'];
        }

        // Время сохранить модели
        foreach( $old as $i )
        {
            $i->saveWithoutCheck();
        }

        // Проверяем целостность Ki индексов
        $old[0]->checkKiIntegrity();
        
        return true;
    }
    
    /**
        Получение ключей Ki для переданного дерева узлов
        каждый элемент имеет уникальный id и опциональный массив children — такие же узлы, как и он
        Возвращает хэш элементов вида id => array( lk => L, rk => R, lvl => LV )
    */
    private function getKies( $tree )
    {
        // Пройдем по дереву и расставим Ki
        $this->setItemKi( $tree );

        // Тут накопим результат        
        $kies = array();
        foreach( $this->getFlatListFromTree( $tree ) as $i )
        {
            $kies[$i['id']] = array( 'id' => $i['id'], 'lk' => $i['lk'], 'rk' => $i['rk'], 'lvl' => $i['lvl'] );
        }
        
        return $kies;
    }
    /**
        Рекурсивная расстановка Ki для дерева
        $item — корень дерева
        $ki — начальное значение Ki, не указывать при вызове
        Возвращает конечное значение ki дерева
    */    
    private function setItemKi( & $item, $ki = 1 )
    {
        $item['lk'] = $ki++;
        
        if( array_key_exists('children', $item) && $item['children'] )
        {
            foreach( $item['children'] as & $c )
            {
                $ki = $this->setItemKi( $c, $ki );
            }
        }

        $item['rk'] = $ki++;
        
        return $ki;
    }

    /**
        Получение плоского списка из дерева
        $tree корень дерева
        Возвращает массив с ключами id, lvl, lk, rk
    */
    private function getFlatListFromTree( $item, $lvl = 1 )
    {
        $list = array();
        
        $list[] = array( 'id' => $item['id'], 'lvl' => $lvl, 'lk' => $item['lk'], 'rk' => $item['rk'] );
        
        if( array_key_exists('children', $item) && $item['children'] )
        {
            foreach( $item['children'] as $c )
            {
                $list = array_merge( $list, $this->getFlatListFromTree( $c, $lvl + 1 ) );
            }
        }

        return $list;
    }
    
    /**
        Получение выбранных элементов в виде дерева — заполняет дочерние элементы узлов, позже можно вызывать их методы getChildren()
        Возвращает массив узлов
    */
    function tree()
    {
        $nodes = $this->kiOrder()->all();
        
        if( count( $nodes ) == 0 ) return array();
        
        $model = $this->model;
        $root = new $model();
        $root->lvl = $nodes[0]->lvl - 1;
        $root->enableChildren();
        
        $stack = array();
        $parent = $root;
        
        foreach( $nodes as $n )
        {
            $n->enableChildren();
            if( $n->lvl == $parent->lvl + 1 )
            {
                $parent->addChild( $n );
            }
            else if( $n->lvl > $parent->lvl + 1 )
            {
                array_push( $stack, $parent );
                $children = $parent->getChildren();
                $parent = $children[ count( $children ) - 1 ];
                $parent->addChild( $n );
            }
            else if( $n->lvl < $parent->lvl + 1 )
            {
                while( $n->lvl < $parent->lvl + 1 && $stack )
                {
                    $parent = array_pop( $stack );
                }
                $parent->addChild( $n );
            }
        }

        return $root->getChildren();
    }
}

?>