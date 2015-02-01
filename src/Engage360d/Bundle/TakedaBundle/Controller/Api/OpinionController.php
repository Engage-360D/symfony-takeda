<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Opinion;

class OpinionController extends TakedaJsonApiController
{
    const URI_OPINIONS_ONE = '/api/v1/schemas/opinions/one.json';
    const URI_OPINIONS_LIST = '/api/v1/schemas/opinions/list.json';

    /**
     * @Route("/opinions", name="api_get_opinions", methods="GET")
     */
    public function getOpinionsAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

//        $limit = $request->query->get("limit");
//        $page = $request->query->get("page");
        $onlyActive = strtolower($request->query->get("onlyActive")) === "true";


        $repository = $this->get('doctrine')->getRepository(Opinion::REPOSITORY);
        $opinions = $repository->findAllForLast12Months($onlyActive);

        $response = [
            "links" => $this->getOpinionLink(),
            "data" => []
        ];

        foreach ($opinions as $opinion) {
            $response["data"][] = $this->getOpinionArray($opinion);
        }

        // TODO put this check into tests?
        $validator = $this->getSchemaValidatior(self::URI_OPINIONS_LIST, (object) $response);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 500);
        }

        return $response;
    }

    /**
     * @Route("/opinions/{id}", name="api_get_opinion", methods="GET")
     */
    public function getOpinionAction(Request $request, $id)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $repository = $this->get('doctrine')->getRepository(Opinion::REPOSITORY);
        $opinion = $repository->findOneById($id);

        if (!$opinion) {
            return $this->getErrorResponse(sprintf("Opinion article with id = %s not found", $id), 404);
        }

        $response = [
            "links" => $this->getOpinionLink(),
            "data" => $this->getOpinionArray($opinion),
            "linked" => [
                "experts" => [
                    $this->getExpertArray($opinion->getExpert())
                ]
            ]
        ];

        $validator = $this->getSchemaValidatior(self::URI_OPINIONS_ONE, (object) $response);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 500);
        }

        return $response;
    }
}