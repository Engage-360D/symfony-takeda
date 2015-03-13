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

        // Factors info page
        if (preg_match("/^factors\\/[^\\/]+/", $url, $matches)) {
            $categoryUrl = $matches[0];
            $factorsFooterMenu = [];
            $factorsArticles = [];

            $pages = $this->getDoctrine()
                ->getRepository(Page::REPOSITORY)
                ->findByUrlPart('/factors/');

            foreach ($pages as $p) {
                if (
                        strpos($p->getUrl(), $categoryUrl) === false &&
                        preg_match("/^\\/factors\\/[^\\/]+$/", $p->getUrl())
                ) {
                    $factorsFooterMenu[] = [
                        "url" => $p->getUrl(),
                        "title" => $p->getTitle(),
                    ];
                } else if (
                        strpos($p->getUrl(), $categoryUrl) !== false &&
                        $p->getUrl() !== '/' . $url
                ) {
                    $factorsArticles[] = [
                        "url" => $p->getUrl(),
                        "title" => $p->getTitle(),
                    ];
                }
            }

            return $this->render('Engage360dTakedaBundle:Page:factors.info.html.twig',
                [
                    'page' => $page,
                    'blocks' => $page->getPageBlocks(),
                    'factorsArticles' => $factorsArticles,
                    'factorsFooterMenu' => $factorsFooterMenu,
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