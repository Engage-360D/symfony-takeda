<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Engage360d\Bundle\TakedaBundle\Entity\Institution;

class InstitutionsController extends TakedaJsonApiController
{
    /**
     * @Route("/institution-specializations", name="api_get_institution_specializations", methods="GET")
     */
    public function getInstitutionSpecializations()
    {
        $repo = $this->getDoctrine()->getRepository(Institution::REPOSITORY);
        $specializations = $repo->findSpecializations();

        $specializations = array_map(function($p) {
            return ['id' => $p];
        }, $specializations);

        return [
            'data' => $specializations,
        ];
    }

    /**
     * @Route("/institution-parsed-towns/{lat},{lng}", name="api_get_institution_parsed_town", methods="GET")
     */
    public function getInstitutionParsedTown($lat, $lng)
    {
        $repo = $this->getDoctrine()->getRepository(Institution::REPOSITORY);
        $parsedTowns = $repo->findParsedTownByCoords($lat, $lng);

        return [
            'data' => ['id' => $parsedTowns],
        ];
    }

    /**
     * @Route("/institution-parsed-towns", name="api_get_institution_parsed_towns", methods="GET")
     */
    public function getInstitutionParsedTowns()
    {
        $repo = $this->getDoctrine()->getRepository(Institution::REPOSITORY);
        $parsedTowns = $repo->findParsedTowns();

        $parsedTowns = array_map(function($p) {
            return ['id' => $p];
        }, $parsedTowns);

        return [
            'data' => $parsedTowns,
        ];
    }

    /**
     * @Route("/institutions", name="api_get_institutions", methods="GET")
     */
    public function getInstitutionsAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

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
                "lat" => $institution->getLat(),
                "lng" => $institution->getLng(),
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
        $this->assertContentTypeIsValid($request);

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
            "lat" => $institution->getLat(),
            "lng" => $institution->getLng(),
            "priority" => $institution->getPriority(),
        ]];
    }
}
