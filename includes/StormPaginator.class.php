<?php
/**
Постраничный навигатор по StormQuerySet-ам.
Использует шаблон, переданный в конструкторе.
Имеет магию приведения к строке для вывода HTML-кода.
 */
class StormPaginator
{
    private $linkmax = 10; // Максимальное количество выводимых на страницу ссылок
    private $query; // StormQuerySet получения записей
    private $uri; // uri страницы, %{page} заменяется на номер страницы
    private $size; // размер страницы (количество выводимых одновременно позиций)
    private $page; // текущая выбранная страница

    private $pages; // массив страниц в специальном формате

    private $count; // Количество объектов всего
    private $objects; // Объекты

    function __construct(StormQuerySet $query, $size, $uri = null)
    {
        $this->query = $query;
        $this->size = $size;

        // Не указан uri - получим текущий
        if (!$uri) {
            $uri = Mad::getUriPath();
        }

        // Если в uri не встречается %{page} - сделаем его самостоятельно
        if (mb_strstr($uri, '%{page}', false, 'utf-8') === false) {
            if ($parts = Mad::getUriPathNames($uri)) {
                if (preg_match('/^page\d+$/', $parts[count($parts) - 1])) {
                    array_pop($parts);
                }
            }

            $parts[] = 'page%{page}';

            $uri = '/' . join('/', $parts) . '/';
        }

        $this->uri = $uri;

        $this->detectPage();
        $this->fetch();
    }

    public function fetch()
    {
                // Собственно страницы
        $this->pages = array();

        // Количество страниц вообще
        $pcnt = ceil($this->getCount() / $this->size);

        // Поправим номер страницы
        if ($this->page > $pcnt || $pcnt < 1) {
            $this->page = 1;
        }

        if ($pcnt > 1) {
            for ($i = 1; $i <= $pcnt; $i++)
            {
                $pg = array('title' => $i);
                if ($this->page != $i) {
                    $pg['uri'] = StormUtilities::array_printf($this->uri, array('page' => $i));
                }
                $this->pages[] = $pg;
            }

            # Если страниц больше, чем требуется - вырежем куски, чтобы вставить красивые три точечки :)
            if (count($this->pages) > $this->linkmax) {
                $b1 = $b2 = $m1 = $m2 = $e1 = $e2 = null; # Границы срезов - начало, середина, конец
                $p1 = array(); # Срезы массива страниц
                $p2 = array();
                $p3 = array();
                $use_el1 = $use_el2 = $use_m = false; # Включение '...' слева, '...' справа и середины

                $use_m = 1;

                $b1 = 0;
                $b2 = $this->linkmax / 2 < count($this->pages) ? $this->linkmax / 2 - 1 : count($this->pages) - 1;
                $m1 = $this->page - ceil($this->linkmax / 4);
                $m2 = $this->page + ceil($this->linkmax / 4 - 2);
                $e1 = count($this->pages) - $this->linkmax / 2 < 0 ? 0 : count($this->pages) - $this->linkmax / 2;
                $e2 = count($this->pages) - 1;

                if ($m1 > $e1 - 1) {
                    $use_m = 0;
                }
                else {
                    if ($m1 < $b2 + 1) {
                        $m1 = $b2 + 1;
                    }
                }
                if ($m2 < $b2 + 1) {
                    $use_m = 0;
                }
                else {
                    if ($m2 > $e1 - 1) {
                        $m2 = $e1 - 1;
                    }
                }

                if ($m1 > $b2 + 1) {
                    $use_el1 = 1;
                }
                if ($m2 < $e1 - 1) {
                    $use_el2 = 1;
                }

                $p1 = array_slice($this->pages, $b1, $b2 - $b1 + 1);
                if ($use_m) {
                    $p2 = array_slice($this->pages, $m1, $m2 - $m1 + 1);
                }
                $p3 = array_slice($this->pages, $e1, $e2 - $e1 + 1);

                if ($use_el1) {
                    $p1[] = array('title' => '...', 'ellipsis' => 1);
                }
                if ($use_el2) {
                    array_unshift($p3, array('title' => '...', 'ellipsis' => 1));
                }

                $this->pages = array_merge($p1, $p2, $p3);
            }

            # Добавим ссылки на предыдущий и следующий элементы
            if ($this->page > 1) {
                array_unshift($this->pages, array('left' => 1, 'uri' => StormUtilities::array_printf($this->uri, array('page' => $this->page - 1))));
            }

            if ($this->page < $pcnt) {
                $this->pages[] = array('right' => 1, 'uri' => StormUtilities::array_printf($this->uri, array('page' => $this->page + 1)));
            }

        }
    }

    /**
    Приведение объекта к строке — заменим его вызовом fetch(), весьма пригодится.
     */
    public function __toString()
    {
        return "123";
    }

    public function getPageCount()
    {
        return ceil($this->getCount() / $this->size);
    }

    public function getCount()
    {
        if (is_null($this->count)) {
            $this->count = $this->query->count();
        }
        return $this->count;
    }

    public function getObjects()
    {
        if (is_null($this->objects)) {
            $this->objects = $this->query->limit($this->size, $this->size * ($this->page - 1));
        }
        return $this->objects;
    }

    private function detectPage()
    {
        $this->page = 1;

        if (preg_match('/page(\d+)$/', Mad::getUriPath(), $m) && (int)$m[1] > 0) {
            $this->page = (int)$m[1];
        }
    }

    /**
     * Выдача объекта в виде массива
     * @return
     */
    function asArray($full = false)
    {
        $paginator = array();

        $objects = $this->getObjects();

        $paginator['html'] = $this->__toString();

        foreach ($objects as $o) {
            $paginator['objects'][] = $o->asArray($full);
        }

        $paginator['count'] = $this->getCount();
        $paginator['pageCount'] = ceil($this->getCount() / $this->size);

        foreach (array('query', 'uri', 'size', 'page', 'pages') as $i) {
            $paginator[$i] = $this->$i;
        }

        return $paginator;
    }

    public function setLinkmax($linkmax)
    {
        $this->linkmax = $linkmax;
    }

    public function getLinkmax()
    {
        return $this->linkmax;
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    public function getPages()
    {
        return $this->pages;
    }

    public function hasPages() {
        return count($this->pages) > 0 ? true : false;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getUri()
    {
        return $this->uri;
    }
}

?>