<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Melodia\PageBundle\Entity\PageBlock;


class PageBlockController extends TakedaJsonApiController
{
    const URI_PAGE_BLOCKS_ONE  = 'v1/schemas/page-blocks/one.json';
    const URI_PAGE_BLOCKS_LIST = 'v1/schemas/page-blocks/list.json';
    const URI_PAGE_BLOCKS_POST = 'v1/schemas/page-blocks/post.json';
    const URI_PAGE_BLOCKS_PUT  = 'v1/schemas/page-blocks/put.json';

    /**
     * @Route("/page-blocks", name="api_get_page_blocks", methods="GET")
     */
    public function getPageBlocksAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $page  = (int) $request->query->get("page");
        $limit = (int) $request->query->get("limit");

        $repository = $this->getDoctrine()
            ->getRepository(PageBlock::REPOSITORY);

        if ($page && $limit) {
            $pageBlocks = $repository->findSubset($page, $limit);
        } else {
            $pageBlocks = $repository->findAll();
        }

        return new JsonResponse([
            "data"  => array_map([$this, 'getPageBlockArray'], $pageBlocks),
        ], 200);
    }

    /**
     * @Route("/page-blocks/{id}", name="api_get_page_block", methods="GET")
     */
    public function getPageBlockAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $pageBlock = $this->getDoctrine()
            ->getRepository(PageBlock::REPOSITORY)
            ->findOneBy(["id" => $id]);

        if (!$pageBlock) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse([
            "data"  => $this->getPageBlockArray($pageBlock),
        ], 200);
    }

    /**
     * @Route("/page-blocks", name="api_post_page_block", methods="POST")
     */
    public function postPageBlockAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_PAGE_BLOCKS_POST);

        $pageBlock = $this->populateEntity(new Pageblock(), $data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($pageBlock);
        $em->flush();

        return new JsonResponse(["data" => $this->getPageBlockArray($pageBlock)], 201);
    }

    /**
     * @Route("/page-blocks/{id}", name="api_put_page_block", methods="PUT")
     */
    public function putPageBlockAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_PAGE_BLOCKS_PUT);

        $pageBlock = $this->getDoctrine()
            ->getRepository(PageBlock::REPOSITORY)
            ->findOneBy(["id" => $id]);

        if (!$pageBlock) {
            throw $this->createNotFoundException();
        }

        $this->populateEntity($pageBlock, $data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($pageBlock);
        $em->flush();

        return new JsonResponse(["data" => $this->getPageBlockArray($pageBlock)], 200);
    }

    /**
     * @Route("/page-blocks/{id}", name="api_delete_page_block", methods="DELETE")
     */
    public function deletePageBlockAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $pageBlock = $this->getDoctrine()
            ->getRepository(PageBlock::REPOSITORY)
            ->findOneBy(["id" => $id]);

        if (!$pageBlock) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($pageBlock);
        $em->flush();

        return new JsonResponse(new \stdClass, 200);
    }
}