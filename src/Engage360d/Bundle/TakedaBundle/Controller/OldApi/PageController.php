<?php

/*
 * This file is part of the Melodia Page Bundle
 *
 * (c) Alexey Ryzhkov <alioch@yandex.ru>
 */

namespace Engage360d\Bundle\TakedaBundle\Controller\OldApi;

use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Engage360d\Bundle\RestBundle\Controller\RestController;
use Melodia\PageBundle\Entity\Page;
use Melodia\PageBundle\Form\Type\PageFormType;
use Symfony\Component\Form\Form;

/**
 * Page controller
 *
 * @author Alexey Ryzhkov <alioch@yandex.ru>
 */
class PageController extends RestController
{
    /**
     * @ApiDoc(
     *  section="Page",
     *  description="Получение всех страниц.",
     *  filters={
     *      {
     *          "name"="page",
     *          "dataType"="integer",
     *          "default"=1,
     *          "required"=false
     *      },
     *      {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "default"="inf",
     *          "required"=false
     *      },
     *      {
     *          "name"="showOnlyActive",
     *          "dataType"="0|1",
     *          "default"=1,
     *          "required"=false
     *      }
     *  }
     * )
     */
    public function getPagesAction(Request $request)
    {
        $page = $request->query->get('page') ?: 1;
        // By default this method returns all records
        $limit = $request->query->get('limit') ?: 0;
        $showOnlyActive = $request->query->get('showOnlyActive') !== null ? (int) $request->query->get('showOnlyActive') : 1;

        // Check filters' format
        if (!is_numeric($page) || !is_numeric($limit) || !in_array($showOnlyActive, array(0, 1))) {
            return new JsonResponse(null, 400);
        }

        $where = $showOnlyActive ? 'isActive = true' : '';

        $pages = $this->getDoctrine()->getRepository(Page::REPOSITORY)
            ->findSubset($page, $limit, array(), $where);

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getPageListResource($pages), 200);
    }

    /**
     * @ApiDoc(
     *  section="Page",
     *  description="Получение детальной информации об одной странице.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+"
     *      }
     *  }
     * )
     */
    public function getPageAction($id)
    {
        $page = $this->getDoctrine()->getRepository(Page::REPOSITORY)
            ->findOneBy(array('id' => $id));

        if (!$page) {
            return new JsonResponse(null, 404);
        }

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getPageResource($page), 200);
    }

    /**
     * @ApiDoc(
     *  section="Page",
     *  description="Создание новой страницы.",
     *  input="Melodia\PageBundle\Form\Type\PageFormType",
     *  output="Melodia\PageBundle\Entity\Page"
     * )
     */
    public function postPageAction(Request $request)
    {
        $page = new Page();

        $form = $this->createForm(new PageFormType(), $page);
        $form->submit($request->request->all()['data']);

        if (!$form->isValid()) {
            return new JsonResponse($this->getErrorMessages($form), 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($page);
        $entityManager->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getPageResource($page), 201);
    }

    /**
     *
     * @ApiDoc(
     *  section="Page",
     *  description="Изменение страницы.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+"
     *      }
     *  },
     *  input="Melodia\PageBundle\Form\Type\PageFormType",
     *  output="Melodia\PageBundle\Entity\Page"
     * )
     */
    public function putPageAction($id, Request $request)
    {
        $page = $this->getDoctrine()->getRepository(Page::REPOSITORY)
            ->findOneBy(array('id' => $id));

        if (!$page) {
            return new JsonResponse(null, 404);
        }

        $form = $this->createForm(new PageFormType(), $page);
        $form->submit($request->request->all()['data']);

        if (!$form->isValid()) {
            return new JsonResponse($this->getErrorMessages($form), 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($page);
        $entityManager->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getPageResource($page), 200);
    }

    /**
     * @ApiDoc(
     *  section="Page",
     *  description="Удаление страницы.",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+"
     *      }
     *  }
     * )
     */
    public function deletePageAction($id)
    {
        $page = $this->getDoctrine()->getRepository(Page::REPOSITORY)
            ->findOneBy(array('id' => $id));

        if (!$page) {
            return new JsonResponse(null, 404);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($page);
        $entityManager->flush();

        return new JsonResponse(new \stdClass(), 200);
    }
}
