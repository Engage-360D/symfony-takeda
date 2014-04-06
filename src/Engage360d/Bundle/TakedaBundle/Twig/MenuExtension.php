<?php

namespace Engage360d\Bundle\TakedaBundle\Twig;

use Engage360d\Bundle\PagesBundle\Entity\Menu\Menu;

/**
 * Extends Twig to provide some helper functions for the Menu.
 *
 */
class MenuExtension extends \Twig_Extension
{
    protected $menuManager;
    protected $router;

    public function __construct($menuManager, $router)
    {
        $this->menuManager = $menuManager;
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            'takeda_menu'     => new \Twig_Function_Method($this, 'getMenu'),
            'takeda_menu_url' => new \Twig_Function_Method($this, 'generateUrl'),
        );
    }

    public function getMenu($root)
    {
        return $this->menuManager->getByRootName($root);
    }

    public function generateUrl(Menu $menu)
    {
        return $this->router->generate(
          'engage360d_takeda_main_category',
          array(
            'url' => $menu->getUrl()
          ));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'takeda_menu';
    }
}
