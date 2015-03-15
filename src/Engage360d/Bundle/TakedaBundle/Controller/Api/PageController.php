<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Melodia\PageBundle\Entity\Page;
use Melodia\PageBundle\Entity\PageBlock;


class PageController extends TakedaJsonApiController
{
    const URI_PAGES_ONE  = 'v1/schemas/pages/one.json';
    const URI_PAGES_LIST = 'v1/schemas/pages/list.json';
    const URI_PAGES_POST = 'v1/schemas/pages/post.json';
    const URI_PAGES_PUT  = 'v1/schemas/pages/put.json';

    /**
     * @Route("/pages", name="api_get_pages", methods="GET")
     */
    public function getPagesAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $page  = (int) $request->query->get("page");
        $limit = (int) $request->query->get("limit");

        $repository = $this->getDoctrine()
            ->getRepository(Page::REPOSITORY);

        if ($page && $limit) {
            $pages = $repository->findSubset($page, $limit);
        } else {
            $pages = $repository->findAll();
        }

        return new JsonResponse([
            "links" => $this->getPageLink(),
            "data"  => array_map([$this, 'getPageArray'], $pages),
        ], 200);
    }

    /**
     * @Route("/pages/{id}", name="api_get_page", methods="GET")
     */
    public function getPageAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $page = $this->getDoctrine()
            ->getRepository(Page::REPOSITORY)
            ->findOneBy(["id" => $id]);

        if (!$page) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse([
            "data"  => $this->getPageArray($page),
        ], 200);
    }

    /**
     * @Route("/pages", name="api_post_page", methods="POST")
     */
    public function postPageAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_PAGES_POST);

        $page = $this->populateEntity(new Page(), $data, ["pageBlocks" => PageBlock::REPOSITORY]);

        $this->assertEntityIsValid($page);

        $em = $this->getDoctrine()->getManager();
        $em->persist($page);
        $em->flush();

        return new JsonResponse(["data" => $this->getPageArray($page)], 201);
    }

    /**
     * @Route("/pages/{id}", name="api_put_page", methods="PUT")
     */
    public function putPageAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_PAGES_PUT);

        $page = $this->getDoctrine()
            ->getRepository(Page::REPOSITORY)
            ->findOneBy(["id" => $id]);

        if (!$page) {
            throw $this->createNotFoundException();
        }

        $this->populateEntity($page, $data, ["pageBlocks" => PageBlock::REPOSITORY]);

        $this->assertEntityIsValid($page);

        $em = $this->getDoctrine()->getManager();
        $em->persist($page);
        $em->flush();

        return new JsonResponse(["data" => $this->getPageArray($page)], 200);

    }

    /**
     * @Route("/pages/{id}", name="api_delete_page", methods="DELETE")
     */
    public function deletePageAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $page = $this->getDoctrine()
            ->getRepository(Page::REPOSITORY)
            ->findOneBy(["id" => $id]);

        if (!$page) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($page);
        $em->flush();

        return new JsonResponse(new \stdClass, 200);
    }
}