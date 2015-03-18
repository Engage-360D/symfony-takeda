<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Melodia\PageBundle\Entity\Page;

class ContentController extends Controller
{
    public function goodToKnowAction()
    {
        $page = $this->getDoctrine()->getRepository(Page::REPOSITORY)
              ->findByUrl($this->generateUrl($this->getRequest()->attributes->get('_route')));

        if (!$page) {
            throw $this->createNotFoundException();
        }

        $blocks = array_map(function($block) {
            return $block->getJsonDecoded();
        }, $page->getPageBlocks()->toArray());

        return $this->render('Engage360dTakedaBundle:Content:good-to-know.html.twig', [
            'blocks' => $blocks,
        ]);
    }

    public function aboutAction()
    {
        return $this->render('Engage360dTakedaBundle:Content:about.html.twig');
    }
}
