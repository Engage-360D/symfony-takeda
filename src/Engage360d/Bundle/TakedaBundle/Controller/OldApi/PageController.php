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
use Melodia\PageBundle\Entity\Page;
use Melodia\PageBundle\Form\Type\PageFormType;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Symfony\Component\Form\Form;

/**
 * Page controller
 *
 * @author Alexey Ryzhkov <alioch@yandex.ru>
 */
class PageController extends TakedaJsonApiController
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

        $response = [
            "links" => $this->getPageLink(),
            "data"  => array_map([$this, 'getPageArray'], $pages),
        ];

        return new JsonResponse($response, 200);
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

        $response = [
            "links" => $this->getPageLink(),
            "data"  => $this->getPageArray($page),
            "linked" => [
                "pageBlocks" => array_map([$this, 'getPageBlockArray'], $page->getPageBlocks()->toArray())
            ]
        ];

        return new JsonResponse($response, 200);
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
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return new JsonResponse($this->getErrorMessages($form), 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($page);
        $entityManager->flush();

        $response = [
            "links" => $this->getPageLink(),
            "data"  => $this->getPageArray($page),
            "linked" => [
                "pageBlocks" => array_map([$this, 'getPageBlockArray'], $page->getPageBlocks()->toArray())
            ]
        ];

        return new JsonResponse($response, 201);
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
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return new JsonResponse($this->getErrorMessages($form), 400);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($page);
        $entityManager->flush();

        $response = [
            "links" => $this->getPageLink(),
            "data"  => $this->getPageArray($page),
            "linked" => [
                "pageBlocks" => array_map([$this, 'getPageBlockArray'], $page->getPageBlocks()->toArray())
            ]
        ];

        return new JsonResponse($response, 200);
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

    protected function getErrorMessages(Form $form)
    {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        foreach ($form->getExtraData() as $key => $extraField) {
            $errors[$key] = "Extra field";
        }

        return $errors;
    }
}
