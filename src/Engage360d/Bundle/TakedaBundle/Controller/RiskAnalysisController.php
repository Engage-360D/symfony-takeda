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

        if ($user && !$this->get('security.context')->isGranted('ROLE_DOCTOR')) {
            $lastTestResult = $user->getTestResults()->last();

            // Prohibit a user who had an incident from taking another test.
            if ($lastTestResult && $lastTestResult->wasThereIncident()) {
                return $this->redirect($this->generateUrl('engage360d_takeda_account_recommendations'));
            }

            // Allow a user to take a test only once a month.
            if ($lastTestResult && !$lastTestResult->hasMonthPassedSinceItWasCreated()) {
                return $this->redirect($this->generateUrl('engage360d_takeda_account_recommendations'));
            }
        }

        $regions = $this->getDoctrine()->getRepository('Engage360dTakedaBundle:Region\Region')->findAll();

        return $this->render('Engage360dTakedaBundle:RiskAnalysis:index.html.twig', array(
            'regions' => $regions,
            'user' => $user,
        ));
    }
}
