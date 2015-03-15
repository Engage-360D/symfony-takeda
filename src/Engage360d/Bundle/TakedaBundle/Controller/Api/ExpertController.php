<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Expert;

class ExpertController extends TakedaJsonApiController
{
    const URI_EXPERTS_ONE =  'v1/schemas/experts/one.json';
    const URI_EXPERTS_LIST = 'v1/schemas/experts/list.json';
    const URI_EXPERTS_POST = 'v1/schemas/experts/post.json';
    const URI_EXPERTS_PUT =  'v1/schemas/experts/put.json';

    /**
     * @Route("/experts", name="api_get_experts", methods="GET")
     */
    public function getExpertsAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->get('doctrine')->getRepository(Expert::REPOSITORY);
        $experts = $repository->findAll();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return $jsonApiResponse->getExpertListResource($experts);
    }

    /**
     * @Route("/experts/{id}", name="api_get_experts_one", methods="GET")
     */
    public function getExpertAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->get('doctrine')->getRepository(Expert::REPOSITORY);
        $expert = $repository->findOneById($id);

        if (!$expert) {
            throw $this->createNotFoundException();
        }

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return $jsonApiResponse->getExpertResource($expert);
    }

    /**
     * @Route("/experts", name="api_post_experts", methods="POST")
     */
    public function postExpertAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_EXPERTS_POST);

        $expert = $this->populateEntity(new Expert(), $data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($expert);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getExpertResource($expert), 201);
    }

    /**
     * @Route("/experts/{id}", name="api_put_experts", methods="PUT")
     */
    public function putExpertAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->get('doctrine')->getRepository(Expert::REPOSITORY);
        $expert = $repository->findOneById($id);

        if (!$expert) {
            throw $this->createNotFoundException();
        }

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_EXPERTS_PUT);

        $expert = $this->populateEntity($expert, $data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($expert);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return $jsonApiResponse->getExpertResource($expert);
    }

    /**
     * @Route("/experts/{id}", name="api_delete_experts", methods="DELETE")
     */
    public function deleteExpertAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->get('doctrine')->getRepository(Expert::REPOSITORY);
        $expert = $repository->findOneById($id);

        if (!$expert) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($expert);
        $em->flush();

        return new \stdClass();
    }
}