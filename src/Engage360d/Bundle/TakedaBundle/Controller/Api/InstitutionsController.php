<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;
use Engage360d\Bundle\TakedaBundle\Entity\Institution;

class InstitutionsController extends TakedaJsonApiController
{
    // const URI_REGIONS_ONE = '/api/v1/schemas/regions/one.json';
    // const URI_REGIONS_LIST = '/api/v1/schemas/regions/list.json';

    /**
     * @Route("/institutions", name="api_get_institutions", methods="GET")
     */
    public function getInstitutionsAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $parsedTown = $request->query->get("parsedTown");
        $specialization = $request->query->get("specialization");

        $repository = $this->get('doctrine')->getRepository(Institution::REPOSITORY);

        if ($parsedTown || $specialization) {
            $institutions = $repository->filter($parsedTown, $specialization);
        } else {
            $institutions = $repository->filter('', '', 100);
        }

        $response = ["data" => []];
        foreach($institutions as $institution) {
            $response["data"][] = [
                "id" => (String) $institution->getId(),
                "name" => $institution->getName(),
                "specialization" => $institution->getSpecialization(),
                "address" => $institution->getAddress(),
                "googleAddress" => $institution->getGoogleAddress(),
                "region" => $institution->getRegion(),
                "parsedTown" => $institution->getParsedTown(),
                "parsedStreet" => $institution->getParsedStreet(),
                "parsedHouse" => $institution->getParsedHouse(),
                "parsedCorpus" => $institution->getParsedCorpus(),
                "parsedBuilding" => $institution->getParsedBuilding(),
                "parsedRegion" => $institution->getParsedRegion(),
                "priority" => $institution->getPriority(),
            ];
        }

        return $response;
    }

    /**
     * @Route("/institutions/{id}", name="api_get_one_institution", methods="GET")
     */
    public function getInstitutionAction(Request $request, $id)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $institution = $this->get('doctrine')->getRepository(Institution::REPOSITORY)
            ->findOneById($id);

        if (!$institution) {
            throw $this->createNotFoundException();
        }

        return ["data" => [
            "id" => (String) $institution->getId(),
            "name" => $institution->getName(),
            "specialization" => $institution->getSpecialization(),
            "address" => $institution->getAddress(),
            "googleAddress" => $institution->getGoogleAddress(),
            "region" => $institution->getRegion(),
            "parsedTown" => $institution->getParsedTown(),
            "parsedStreet" => $institution->getParsedStreet(),
            "parsedHouse" => $institution->getParsedHouse(),
            "parsedCorpus" => $institution->getParsedCorpus(),
            "parsedBuilding" => $institution->getParsedBuilding(),
            "parsedRegion" => $institution->getParsedRegion(),
        ]];
    }
}
