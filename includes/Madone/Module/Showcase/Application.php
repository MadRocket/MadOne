<?
/**
 * ShowcaseApplication class.
 * 
 * @extends Madone_Application
 *
 * Default settings:
 * title = Каталог
 * has_text = 0
 * has_meta = 1
 * has_subpages = 1
 * priority = 2
 */
class Madone_Module_Showcase_Application extends Madone_Application {
    protected $routes = array(
        '/?' => 'index',
        '/[*:slug]/view[i:id]' => 'item',
        '/[*:slug]' => 'category',
    );

    public function index() {
        $items = Model_Showcaseitems( array( 'enabled' => true, 'special' => true ) )->all();
        $items = array_chunk($items, 2);
        print $this->render('showcase/index.twig', array('page' => $this->page, 'items' => $items));
    }

    public function category($slug) {
        $section = Model_Showcasesections( array( 'enabled' => true, 'uri' => "/{$slug}" ) )->first();
        if( ! ( $section ) ) {
            return false;
        }

        $items = Model_Showcaseitems( array( 'section' => $section, 'enabled' => true ) )->all();
        $items = array_chunk($items, 2);

        print $this->render('showcase/category.twig', array('page' => $this->page, 'section' => $section, 'items' => $items));
    }

    public function item($slug, $id) {
        $section = Model_Showcasesections( array( 'enabled' => true, 'uri' => "/{$slug}" ) )->first();
        $item = Model_Showcaseitems( array( 'section' => $section, 'pk' => $id, 'enabled' => true ) )->first();

        if( ! ( $item && $section ) ) {
            return false;
        }

        print $this->render('showcase/item.twig', array('page' => $this->page, 'section' => $section, 'item' => $item));
    }
}

?>