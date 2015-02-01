<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Expert;

class ExpertController extends TakedaJsonApiController
{
    const URI_EXPERTS_ONE = '/api/v1/schemas/experts/one.json';
    const URI_EXPERTS_LIST = '/api/v1/schemas/experts/list.json';

    /**
     * @Route("/experts", name="api_get_experts", methods="GET")
     */
    public function getExpertsAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $repository = $this->get('doctrine')->getRepository(Expert::REPOSITORY);
        $experts = $repository->findAll();

        $response = [
            "data" => []
        ];

        foreach ($experts as $expert) {
            $response["data"][] = $this->getExpertArray($expert);
        }

        // TODO put this check into tests?
        $validator = $this->getSchemaValidatior(self::URI_EXPERTS_LIST, (object) $response);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 500);
        }

        return $response;
    }

    /**
     * @Route("/experts/{id}", name="api_get_experts_one", methods="GET")
     */
    public function getExpertAction(Request $request, $id)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $repository = $this->get('doctrine')->getRepository(Expert::REPOSITORY);
        $expert = $repository->findOneById($id);

        if (!$expert) {
            return $this->getErrorResponse(sprintf("Expert with id = %s not found", $id), 404);
        }

        $response = [
            "data" => $this->getExpertArray($expert),
        ];

        $validator = $this->getSchemaValidatior(self::URI_EXPERTS_ONE, (object) $response);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 500);
        }

        return $response;
    }
}