<?php

namespace Engage360d\Bundle\TakedaTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestController extends Controller
{
    public function getTestAction()
    {
        return $this->render('Engage360dTakedaTestBundle:Test:test.html.twig');
    }
    
    public function getTestResultRecommendationAction($id, $type)
    {
        if (!in_array($type, array('arterialPressure', 'bmi', 'cholesterolLevel', 'extraSalt', 'physicalActivity', 'smoking'))) {
            throw $this->createNotFoundException();
        }
      
        $testResult = $this->getDoctrine()->getManager()->getRepository('Engage360dTakedaTestBundle:TestResult')->find($id);

        if (!$testResult) {
            throw $this->createNotFoundException();
        }

        $recommendations = $testResult->getRecommendations();
        $page = $recommendations['pages'][$type];

        if (!$page) {
            throw $this->createNotFoundException();
        }
        
        $pages = array_filter($recommendations['pages']);

        return $this->render('Engage360dTakedaTestBundle:Test:test_result_recommendation.html.twig', array(
            'testResult' => $testResult,
            'type' => $type,
            'pages' => $pages,
            'page' => $page,
        ));
    }
}
