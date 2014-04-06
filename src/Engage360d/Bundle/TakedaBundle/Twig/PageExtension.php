<?php

namespace Engage360d\Bundle\TakedaBundle\Twig;

use Engage360d\Bundle\PagesBundle\Entity\Page\Page;
use Engage360d\Bundle\PagesBundle\Entity\Category\Category;


/**
 * Extends Twig to provide some helper functions for the Menu.
 *
 */
class PageExtension extends \Twig_Extension
{
    protected $router;
    protected $converter;

    public function __construct($router, $converter)
    {
        $this->router = $router;
        $this->converter = $converter;
    }

    public function getFunctions()
    {
        return array(
            'takeda_page_url' => new \Twig_Function_Method($this, 'generatePageUrl'),
            'takeda_category_url' => new \Twig_Function_Method($this, 'generateCategoryUrl'),
            'takeda_page_blocks' => new \Twig_Function_Method($this, 'convertPageBlocks'),
        );
    }

    public function generatePageUrl(Page $page)
    {
        return $this->router->generate(
          'engage360d_takeda_main_category_page',
          array(
            'category' => $page->getCategory()->getUrl(),
            'url' => $page->getUrl()
          ));
    }

    public function generateCategoryUrl(Category $category)
    {
        return $this->router->generate(
          'engage360d_takeda_main_category',
          array(
            'url' => $category->getUrl()
          ));
    }
    
    public function convertPageBlocks(Page $page)
    {
        return $this->converter->toHtml($page->getBlocks());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'takeda_page';
    }
}
