<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserInterface;
use JMS\Serializer\SerializationContext;

class AccountController extends Controller
{
    public function dietAction($id)
    {
        $questions = $this->container->getParameter('engage360d_diet_questions');

        $testResult = $this->getUser()->getTestResults()->filter(function ($testResult) use ($id) {
            return $testResult->getId() == $id;
        })->first();

        if (!$testResult) {
            throw $this->createNotFoundException();
        }

        return $this->render('Engage360dTakedaBundle:Account:diet.html.twig', [
            'questions' => $questions,
            'testResult' => $testResult,
        ]);
    }

    public function recommendationsAction()
    {
        $user = $this->getUser();
        $testResults = array_map(
            [$this->get('engage360d_takeda.json_api_response'), 'getTestResultArray'],
            $user->getTestResults()->toArray()
        );

        if (count($testResults) === 0) {
          return $this->redirect($this->generateUrl('engage360d_takeda_risk_analysis'));
        }

        return $this->render('Engage360dTakedaBundle:Account:recommendations.html.twig', [
            'testResults' => $testResults,
        ]);
    }

    public function recommendationAction($id, $type)
    {
        if (!in_array(
            $type,
            [
                'bmi',
                'isSmoker',
                'arterialPressure',
                'cholesterolLevel',
                'isAddingExtraSalt',
                'physicalActivityMinutes',
            ]
        )) {
            throw $this->createNotFoundException();
        }

        $testResult = $this->getUser()->getTestResults()->filter(function ($testResult) use ($id) {
            return $testResult->getId() == $id;
        })->first();

        if (!$testResult) {
            throw $this->createNotFoundException();
        }

        $recommendations = $testResult->getRecommendations();
        $page = $recommendations['pages'][$type];

        if (!$page) {
            throw $this->createNotFoundException();
        }

        $pages = array_filter($recommendations['pages']);

        return $this->render('Engage360dTakedaBundle:Account:recommendations_one.html.twig', array(
            'testResult' => $testResult,
            'type' => $type,
            'pages' => $pages,
            'page' => $page,
        ));
    }

    public function settingsAction()
    {
        return $this->render('Engage360dTakedaBundle:Account:settings.html.twig', [
            'user' => $this->get('engage360d_takeda.json_api_response')->getUserArray($this->getUser()),
            'regions' => $this->getDoctrine()->getRepository('Engage360dTakedaBundle:Region\Region')->findAll()
        ]);
    }

    public function timelineAction()
    {
        $user = $this->getUser();

        if ($user->getTestResults()->isEmpty()) {
            return $this->redirect($this->generateUrl('engage360d_takeda_risk_analysis'));
        }

        if (!$this->get('security.context')->isGranted('ROLE_DOCTOR') && $user->getTestResults()->last()->wasThereIncident()) {
            return $this->redirect($this->generateUrl('engage360d_takeda_account_recommendations'));
        }

        $timelineManager = $this->get('engage360d_takeda.timeline_manager');
        $timelineManager->setUser($user);

        $serializer = $this->get('jms_serializer');

        $timeline = $timelineManager->getTimeline();
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        $timeline = $serializer->serialize($timeline, 'json', $context);

        $pills = array_map(
            [$this->get('engage360d_takeda.json_api_response'), 'getPillArray'],
            $user->getPills()->toArray()
        );
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        $pills = $serializer->serialize($pills, 'json', $context);

        return $this->render('Engage360dTakedaBundle:Account:timeline.html.twig', array(
            'timeline' => $timeline,
            'pills' => $pills,
        ));
    }

    public function reportsListAction()
    {
        $user = $this->getUser();
        if ($user->getTestResults()->isEmpty()) {
            return $this->redirect($this->generateUrl('engage360d_takeda_risk_analysis'));
        }

        if (!$this->get('security.context')->isGranted('ROLE_DOCTOR') && $user->getTestResults()->last()->wasThereIncident()) {
            return $this->redirect($this->generateUrl('engage360d_takeda_account_recommendations'));
        }

        $reportsManager = $this->get('engage360d_takeda.reports_manager');
        $reportsManager->init($user);

        return $this->render('Engage360dTakedaBundle:Account:reports.list.html.twig', [
            'list' => $reportsManager->getReportsList(),
        ]);
    }

    public function reportAction(Request $request)
    {
        $user = $this->getUser();
        if ($user->getTestResults()->isEmpty()) {
            return $this->redirect($this->generateUrl('engage360d_takeda_risk_analysis'));
        }

        if (!$this->get('security.context')->isGranted('ROLE_DOCTOR') && $user->getTestResults()->last()->wasThereIncident()) {
            return $this->redirect($this->generateUrl('engage360d_takeda_account_recommendations'));
        }

        $reportsManager = $this->get('engage360d_takeda.reports_manager');
        $reportsManager->init($user);

        $report = $reportsManager->getReport($request->get('reportType'));

        return $this->render('Engage360dTakedaBundle:Account:report.html.twig', [
            'report' => $report,
        ]);
    }

    public function logoutAction(Request $request)
    {
        $this->get('security.context')->setToken(null);
        $request->getSession()->invalidate();

        return $this->redirect($this->generateUrl('engage360d_takeda_main_mainpage'));
    }
}
