<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;

class RiskAnalysisController extends Controller
{
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!($user instanceof UserInterface)) {
            $user = null;
        }

        // Prohibit a user who had a heart attack or stroke from taking another test.
        $lastTestResult = $user->getTestResults()->last();
        if ($lastTestResult && $lastTestResult->getHadHeartAttackOrStroke()) {
            return $this->redirect($this->generateUrl('engage360d_takeda_account_recommendations'));
        }

        $regions = $this->getDoctrine()->getRepository('Engage360dTakedaBundle:Region\Region')->findAll();

        return $this->render('Engage360dTakedaBundle:RiskAnalysis:index.html.twig', array(
            'regions' => $regions,
            'user' => $user,
        ));
    }
}
