<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Melodia\PageBundle\Entity\Page;


class PageController extends Controller
{
    public function indexAction($url)
    {
        $page = $this->getDoctrine()->getRepository(Page::REPOSITORY)
            ->findByUrl($url);

        if (!$page) {
            throw $this->createNotFoundException();
        }

        // Disease info page
        $diseaseFooterMenu = [];
        if (preg_match("/^disease\\/.+/", $url)) {
            $pages = $this->getDoctrine()
                ->getRepository(Page::REPOSITORY)
                ->findByUrlPart('/disease/');
            foreach ($pages as $p) {
                if (preg_match("/disease\\/[^\\/]+$/", $p->getUrl()) && strpos($p->getUrl(), $url) === false) {
                    $diseaseFooterMenu[] = [
                        "url" => $p->getUrl(),
                        "title" => $p->getTitle(),
                    ];
                }
            }
            return $this->render('Engage360dTakedaBundle:Page:disease.info.html.twig',
                [
                    'page' => $page,
                    'blocks' => $page->getPageBlocks(),
                    'diseaseFooterMenu' => $diseaseFooterMenu,
                ]
            );
        }

        // About pages
        if (preg_match("/^about\\/.+/", $url)) {
            return $this->render('Engage360dTakedaBundle:Page:about.html.twig',
                [
                    'page' => $page,
                    'blocks' => $page->getPageBlocks(),
                ]
            );
        }

        return $this->render('Engage360dTakedaBundle:Page:index.html.twig',
            [
                'page' => $page,
                'blocks' => $page->getPageBlocks(),
            ]
        );
    }
}