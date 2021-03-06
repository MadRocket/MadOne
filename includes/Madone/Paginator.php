<?php
/**
 * Постраничный навигатор по Storm_Queryset-ам.
 * Использует шаблон, переданный в конструкторе.
 * Имеет магию приведения к строке для вывода HTML-кода.
 *
 * TODO: Добавить обработку наличия GET параметров
 */
class Madone_Paginator
{
    private $linkmax = 10; // Максимальное количество выводимых на страницу ссылок
    private $query; // Storm_Queryset получения записей
    private $uri; // uri страницы, %{page} заменяется на номер страницы
    private $size; // размер страницы (количество выводимых одновременно позиций)
    private $page; // текущая выбранная страница

    private $pages; // массив страниц в специальном формате

    private $count; // Количество объектов всего
    private $objects; // Объекты

    protected $container;

    function __construct(Storm_Queryset $query, $size, $uri = null)
    {
        $this->query = $query;
        $this->size = $size;
        $this->uri = Madone_Utilites::getUriPath();

        $this->detectPage();
//        $this->fetch();
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
            for ($i = 1; $i <= $pcnt; $i++) {
                $pg = array('title' => $i);
                if ($this->page != $i) {
                    $pg['uri'] = $this->uri."/?page=".$i;
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
                array_unshift($this->pages, array('left' => 1, 'uri' =>  $this->uri."/?page=".($this->page - 1) ));
            }

            if ($this->page < $pcnt) {
                $this->pages[] = array('right' => 1, 'uri' =>  $this->uri."/?page=".($this->page + 1) );
            }

        }

        $template = $this->container['template'];
        $template->getLoader()->setPaths(array_merge(array("{$_SERVER['DOCUMENT_ROOT']}/includes/template/_default"), $template->getLoader()->getPaths()));
        return $template->loadTemplate('pager.twig')->render(array('pager' => $this));
    }

    /**
     * Приведение объекта к строке — заменим его вызовом fetch(), весьма пригодится.
     * @return string
     */
    public function __toString()
    {
        return (string)$this->fetch();
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

        if(array_key_exists('page', $_GET)) {
            $this->page = $_GET['page'];
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

    public function setContainer($contaner)
    {
        $this->container = $contaner;
    }

    public function getContainer()
    {
        return $this->container;
    }
}
