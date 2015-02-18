<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Engage360d\Bundle\TakedaBundle\Entity\Institution;

class InstitutionsController extends Controller
{
    public function institutionsAction()
    {
        $repo = $this->getDoctrine()->getRepository(Institution::REPOSITORY);
        $parsedTowns = $repo->findParsedTowns();
        $specializations = $repo->findSpecializations();
        $results = $repo->filter($parsedTowns[0], "");

        $results = array_map(function($institution) {
            return [
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
        }, $results);

        return $this->render('Engage360dTakedaBundle:Institutions:institutions.html.twig', array(
            'parsedTowns' => $parsedTowns,
            'specializations' => $specializations,
            'results' => $results,
        ));
        // if (!$this->isContentTypeValid($request)) {
        //     return $this->getInvalidContentTypeResponse();
        // }

        // $limit = $request->query->get("limit");
        // $page = $request->query->get("page");

        // $repository = $this->get('doctrine')->getRepository(Institution::REPOSITORY);

        // if ($page && $limit) {
        //     $institutions = $repository->findSubset($page, $limit);
        // } else {
        //     $institutions = $repository->findSubset(1, 100);
        // }

        // $response = ["data" => []];
        // foreach($institutions as $institution) {
        //     $response["data"][] = [
        //         "id" => (String) $institution->getId(),
        //         "name" => $institution->getName(),
        //         "specialization" => $institution->getSpecialization(),
        //         "address" => $institution->getAddress(),
        //         "googleAddress" => $institution->getGoogleAddress(),
        //         "region" => $institution->getRegion(),
        //         "parsedTown" => $institution->getParsedTown(),
        //         "parsedStreet" => $institution->getParsedStreet(),
        //         "parsedHouse" => $institution->getParsedHouse(),
        //         "parsedCorpus" => $institution->getParsedCorpus(),
        //         "parsedBuilding" => $institution->getParsedBuilding(),
        //         "parsedRegion" => $institution->getParsedRegion(),
        //     ];
        // }

        // return $response;
    }
}
