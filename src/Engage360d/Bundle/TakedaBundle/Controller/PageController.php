<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller
{
    public function categoryAction($url)
    {
        $category = $this->container
            ->get('engage360d_pages.manager.category')
            ->findByUrl($url);

        return $this->render('Engage360dTakedaBundle:Page:category.html.twig', array(
          'category' => $category,
          'pages' => $category->getPages(),
          'page' => $category->getPage(),
        ));
    }

    public function pageAction($category, $url)
    {
        $page = $this->container
            ->get('engage360d_pages.manager.page')
            ->findByUrl($url);

        return $this->render('Engage360dTakedaBundle:Page:page.html.twig', array(
          'category' => $page->getCategory(),
          'page' => $page,
        ));
    }
}