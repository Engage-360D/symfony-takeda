<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Opinion;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Expert;

class OpinionController extends TakedaJsonApiController
{
    const URI_OPINIONS_ONE =  'v1/schemas/opinions/one.json';
    const URI_OPINIONS_LIST = 'v1/schemas/opinions/list.json';
    const URI_OPINIONS_POST = 'v1/schemas/opinions/post.json';
    const URI_OPINIONS_PUT =  'v1/schemas/opinions/put.json';

    /**
     * @Route("/opinions", name="api_get_opinions", methods="GET")
     */
    public function getOpinionsAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

//        $limit = $request->query->get("limit");
//        $page = $request->query->get("page");
        $onlyActive = strtolower($request->query->get("onlyActive")) === "true";

        $repository = $this->getDoctrine()->getRepository(Opinion::REPOSITORY);
        $opinions = $repository->findAllForLast12Months($onlyActive);

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return $jsonApiResponse->getOpinionListResource($opinions);
    }

    /**
     * @Route("/opinions/{id}", name="api_get_opinion", methods="GET")
     */
    public function getOpinionAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->getDoctrine()->getRepository(Opinion::REPOSITORY);
        $opinion = $repository->findOneById($id);

        if (!$opinion) {
            throw $this->createNotFoundException();
        }

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return $jsonApiResponse->getOpinionResource($opinion);
    }

    /**
     * @Route("/opinions", name="api_post_opinion", methods="POST")
     */
    public function postOpinionAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_OPINIONS_POST);

        $opinion = $this->populateEntity(new Opinion(), $data, ["expert" => Expert::REPOSITORY]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($opinion);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getOpinionResource($opinion), 201);
    }

    /**
     * @Route("/opinions/{id}", name="api_put_opinion", methods="PUT")
     */
    public function putOpinionAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->getDoctrine()->getRepository(Opinion::REPOSITORY);
        $opinion = $repository->findOneById($id);

        if (!$opinion) {
            throw $this->createNotFoundException();
        }

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_OPINIONS_POST);

        $opinion = $this->populateEntity($opinion, $data, ["expert" => Expert::REPOSITORY]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($opinion);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getOpinionResource($opinion), 200);
    }

    /**
     * @Route("/opinions/{id}", name="api_delete_opinion", methods="DELETE")
     */
    public function deleteOpinionAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->getDoctrine()->getRepository(Opinion::REPOSITORY);
        $opinion = $repository->findOneById($id);

        if (!$opinion) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($opinion);
        $em->flush();

        return new \stdClass();
    }
}