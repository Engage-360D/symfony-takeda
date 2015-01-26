<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;
use Engage360d\Bundle\TakedaBundle\Entity\Region\Region;

class RegionController extends TakedaJsonApiController
{
    const URI_REGIONS_ONE = '/api/v1/schemas/regions/one.json';
    const URI_REGIONS_LIST = '/api/v1/schemas/regions/list.json';

    /**
     * @Route("/regions", name="api_get_regions", methods="GET")
     */
    public function getRegionsAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $limit = $request->query->get("limit");
        $page = $request->query->get("page");

        $repository = $this->get('doctrine')->getRepository(Region::REPOSITORY);

        if ($page && $limit) {
            $regions = $repository->findSubset($page, $limit);
        } else {
            $regions = $repository->findAll();
        }

        $response = ["data" => []];
        foreach($regions as $region) {
            $response["data"][] = [
                "id" => (String) $region->getId(),
                "name" => $region->getName(),
            ];
        }

        return $response;
    }

    /**
     * @Route("/regions/{id}", name="api_get_one_region", methods="GET")
     */
    public function getRegionAction(Request $request, $id)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $region = $this->get('doctrine')->getRepository(Region::REPOSITORY)
            ->findOneById($id);

        if (!$region) {
            return $this->getErrorResponse(sprintf("Region with id = %s not found", $id), 404);
        }

        return ["data" => [
            "id" => (String) $id,
            "name" => $region->getName(),
        ]];
    }
}