<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

require(__DIR__ . '/../ipgeobase.php/ipgeobase.php');

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Engage360d\Bundle\TakedaBundle\Entity\Institution;

class InstitutionsController extends Controller
{
    public function institutionsAction()
    {
        $gb = new \IPGeoBase();
        $ip = $this->getRequest()->getClientIp();
        $data = $gb->getRecord($ip);
        $city = $data ? iconv('CP1251', 'UTF-8', $data['city']) : null;

        $repo = $this->getDoctrine()->getRepository(Institution::REPOSITORY);
        $parsedTowns = $repo->findParsedTowns();
        $specializations = $repo->findSpecializations();

        if (in_array($city, $parsedTowns)) {
            $parsedTown = $city;
        } else {
            $parsedTown = $parsedTowns[0];
        }

        $results = $repo->filter($parsedTown, "");

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
                "lat" => $institution->getLat(),
                "lng" => $institution->getLng(),
                "priority" => $institution->getPriority(),
            ];
        }, $results);

        return $this->render('Engage360dTakedaBundle:Institutions:institutions.html.twig', array(
            'parsedTowns' => $parsedTowns,
            'parsedTown' => $parsedTown,
            'specializations' => $specializations,
            'results' => $results,
        ));
    }

    public function institutionAction($id)
    {
        $institution = $this->getDoctrine()->getRepository('Engage360dTakedaBundle:Institution')->findOneById($id);

        return $this->render('Engage360dTakedaBundle:Institutions:institution.html.twig', array(
            'institution' => $institution,
        ));
    }

    public function mapBlockAction()
    {
        $gb = new \IPGeoBase();
        $ip = $this->getRequest()->getClientIp();
        $data = $gb->getRecord($ip);
        $city = $data ? iconv('CP1251', 'UTF-8', $data['city']) : null;

        $repo = $this->getDoctrine()->getRepository(Institution::REPOSITORY);
        $parsedTowns = $repo->findParsedTowns();

        if (in_array($city, $parsedTowns)) {
            $parsedTown = $city;
        } else {
            $parsedTown = $parsedTowns[0];
        }

        $results = $repo->filter($parsedTown, "");

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
                "lat" => $institution->getLat(),
                "lng" => $institution->getLng(),
                "priority" => $institution->getPriority(),
            ];
        }, $results);

        return $this->render('Engage360dTakedaBundle:Institutions:mapBlock.html.twig', array(
            'parsedTown' => $parsedTown,
            'results' => $results,
        ));
    }
}
