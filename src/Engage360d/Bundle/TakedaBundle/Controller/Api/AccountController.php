<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Engage360d\Bundle\TakedaBundle\Entity\TestResult;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Engage360d\Bundle\TakedaBundle\Entity\User\User;
use Engage360d\Bundle\TakedaBundle\Entity\Region\Region;
use Engage360d\Bundle\TakedaBundle\Entity\Pill;
use Engage360d\Bundle\SecurityBundle\Event\UserEvent;
use Engage360d\Bundle\SecurityBundle\Engage360dSecurityEvents;
use Engage360d\Bundle\TakedaBundle\Utils\Timeline;

class AccountController extends TakedaJsonApiController
{
    const URI_USER_PUT = 'v1/schemas/users/put.json';
    const URI_TEST_RESULTS_POST = 'v1/schemas/test-results/post.json';
    const URI_USER_RESET_PASSWORD_POST = 'v1/schemas/users/reset-password/post.json';
    const URI_TEST_RESULTS_SEND_EMAIL_POST = 'v1/schemas/test-results/send-email/post.json';
    const URI_PILLS_POST = 'v1/schemas/pills/post.json';
    const URI_PILLS_PUT = 'v1/schemas/pills/put.json';
    const URI_TIMELINE_TASKS_PUT = 'v1/schemas/timelines/tasks/put.json';
    const URI_INCIDENTS_POST = 'v1/schemas/incidents/post.json';
    const URI_REPORTS_SEND_EMAIL_POST = 'v1/schemas/reports/send-email/post.json';

    /**
     * @Route("/account", name="api_get_account", methods="GET")
     */
    public function getAccountAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $user = $this->getUser();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getUserResource($user), 200);
    }

    /**
     * @Route("/account", name="api_post_account", methods="PUT")
     */
    public function putAccountAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_USER_PUT);

        $this->assertSocialCredentialsIsValid($data);

        $user = $this->getUser();

        $oldEmail = $user->getEmail();
        $user = $this->populateEntity($user, $data, ["region" => Region::REPOSITORY]);

        $em = $this->get('doctrine')->getManager();

        if ($oldEmail !== $user->getEmail()) {
            $duplicateUser = $em->getRepository(User::REPOSITORY)
                ->findOneBy(["email" => $user->getEmail()]);

            if ($duplicateUser) {
                return $this->getErrorResponse(sprintf("User with email '%s' already exists", $user->getEmail()), 400);
            }
        }

        $em->persist($user);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getUserResource($user), 200);
    }

    /**
     * @Route("/account/reset", name="api_post_account_reset", methods="POST")
     */
    public function postAccountResetAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $user = $this->getUser();

        $user->getTestResults()->clear();
        $user->getPills()->clear();

        $timelineManager = $this->get('engage360d_takeda.timeline_manager');
        $timelineManager->setUser($user);
        $timelineManager->removeTimeline();
        $user->setTimelineId(null);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse("Account has been reset.", 200);
    }

    /**
     * @Route("/account/test-results", name="api_post_account_test_results", methods="POST")
     */
    public function postAccountTestResultAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_TEST_RESULTS_POST);

        $user = $this->getUser();

        // Note, that a user with ROLE_ADMIN is always granted a role ROLE_DOCTOR.
        // See role_hierarchy in security settings.
        if (!$this->get('security.context')->isGranted('ROLE_DOCTOR')) {
            $lastTestResult = $user->getTestResults()->last();

            // Prohibit a user who had an incident from taking another test.
            if ($lastTestResult && $lastTestResult->wasThereIncident()) {
                return $this->getErrorResponse("You got an indicend. Go to the doctor, quick!", 409);
            }

            // Allow a user to take a test only once a month.
            if ($lastTestResult && !$lastTestResult->hasMonthPassedSinceItWasCreated()) {
                return $this->getErrorResponse("You're allowed to take the test only once a month.", 409);
            }
        }

        $testResult = new TestResult;
        foreach ($data->data as $property => $value) {
            $method = 'set' . ucfirst($property);
            $testResult->$method($value);
        }
        $testResult->setUser($user);

        $em = $this->get('doctrine')->getManager();
        $em->persist($testResult);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getTestResultResource($testResult), 201);
    }

    /**
     * @Route("/account/test-results", name="api_get_account_test_results", methods="GET")
     */
    public function getAccountTestResultAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $user = $this->getUser();
        $testResults = $user->getTestResults()->toArray();

        // TODO check whether $sinceDate is in ISO8601 format?
        $sinceDate = $request->query->get("sinceDate");
        $sinceId = $request->query->get("sinceId");

        if ($sinceDate) {
            $sinceDate = new \DateTime($sinceDate);
            $testResults = array_filter($testResults, function ($testResult) use ($sinceDate) {
                return $testResult->getCreatedAt()->format('U') >= $sinceDate->format('U');
            });
            $testResults = array_values($testResults);
        }

        if ($sinceId) {
            $testResults = array_filter($testResults, function ($testResult) use ($sinceId) {
                return $testResult->getId() >= (int) $sinceId;
            });
            $testResults = array_values($testResults);
        }

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getTestResultListResource($testResults), 200);
    }

    /**
     * @Route("/account/test-results/{id}/diet-questions", name="api_post_account_test_results_diet_questions", methods="GET")
     */
    public function getAccountTestResultDietQuestionsAction(Request $request, $id)
    {
        $questions = $this->container->getParameter('engage360d_diet_questions');

        return new JsonResponse($questions, 200);
    }

    /**
     * @Route("/account/test-results/{id}/diet-recommendations", name="api_post_account_test_results_diet_recommendations", methods="GET")
     */
    public function getAccountTestResultDietRecommendationsAction(Request $request, $id)
    {
        $user = $this->getUser();

        $testResult = $user->getTestResults()->filter(function ($elem) use ($id) {
            return $elem->getId() == $id;
        })->first();

        $questions = $this->container->getParameter('engage360d_diet_questions');
        $hasBad = false;
        $answers = $request->query->get('answers');
        if (!is_array($answers)) $answers = array();
        foreach ($questions['data'] as $question) {
          if (isset($answers[$question['id']]) && $answers[$question['id']] !== '1') {
            $hasBad = true;
          }
        }

        $messages = array();
        if ($testResult->getIsAddingExtraSalt()) {
          $messages[] = 'Потребление соли может приводить к повышению артериального давления. Ограничьте потребление соли и продуктов, богатых натрием. Старайтесь не досаливать пищу. Для улучшения вкусовых качеств пищи используйте различные травы, специи, лимонный сок, чеснок.';
        }
        if ($hasBad) {
          $messages[] = 'Здоровое питание способствует снижению уровня основных факторов риска сердечно-сосудистых заболеваний, связанных с атеросклерозом. Потребление жира при нормальном весе должно соответствовать для мужчин 75-90 г, для женщин - 50-65 г в сутки. Насыщенные жиры (животные, твердые растительные жиры) могут составлять 1/3 потребляемых жиров, остальные 2/3 жиров должны быть ненасыщенными, жидкими жирами. Это растительные масла (подсолнечное, оливковое, льняное) и жир рыбы. Ограничение потребления животных жиров ведет к снижению потребления содержащегося в них холестерина.';
        }
        if (count($messages) === 0) {
          $messages[] = 'Здоровое питание способствует снижению уровня основных факторов риска сердечно-сосудистых заболеваний, связанных с атеросклерозом. Потребление жира при нормальном весе должно соответствовать для мужчин 75-90 г, для женщин - 50-65 г в сутки. Насыщенные жиры (животные, твердые растительные жиры) могут составлять 1/3 потребляемых жиров, остальные 2/3 жиров должны быть ненасыщенными, жидкими жирами. Это растительные масла (подсолнечное, оливковое, льняное) и жир рыбы. Ограничение потребления животных жиров ведет к снижению потребления содержащегося в них холестерина.';
        }

        $banners = $this->container->getParameter('engage360d_diet_messages');
        $red = array();
        $blue = array();
        foreach ($banners['should_change'] as $id => $banner) {
          if (isset($answers[$id]) && $answers[$id] === "3") {
            $red[] = $banner;
          }
        }
        foreach ($banners['should_less'] as $id => $banner) {
          if (isset($answers[$id]) && $answers[$id] === "2") {
            $blue[] = $banner;
          }
        }

        return new JsonResponse(array(
          'data' => array(
            'messages' => $messages,
            'red' => $red,
            'blue' => $blue,
          )
        ), 200);
    }

    /**
     * @Route("/account/test-results/{id}/send-email", name="api_post_account_test_results_send_email", methods="POST")
     */
    public function getAccountTestResultSendEmailAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_TEST_RESULTS_SEND_EMAIL_POST);

        $user = $this->getUser();
        $testResult = $user->getTestResults()->filter(function ($elem) use ($id) {
            return $elem->getId() == $id;
        })->first();

        if (!$testResult) {
            throw $this->createNotFoundException();
        }

        // TODO consider to put this code into service
        $subject = "Test result";
        $fromEmail = $this->container->getParameter('mailer_sender_email');
        if (!$fromEmail) {
            throw new \RuntimeException("The mandatory parameter 'mailer_sender_email' is not set", 500);
        }
        $body = $this->renderView(
            'Engage360dTakedaBundle:Test:email__test_result.html.twig',
            ['testResult' => $testResult]
        );

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($data->data->email)
            ->setBody($body, 'text/html');

        // With spooling turned off Swift_SwiftException will be caught
        // by FOSRestBundle Exception Handler and formatted by ExceptionWrapperHandler
        $this->get('mailer')->send($message);

        return new JsonResponse(["data" => (object) []], 200);
    }

    /**
     * @Route("/account/test-results/{id}/pages/{recommendation}", name="api_get_account_test_results_pages_recommendations", methods="GET")
     */
    public function getAccountTestResultPagesRecommendationAction(Request $request, $id, $recommendation)
    {
        $this->assertContentTypeIsValid($request);

        $user = $this->getUser();
        $testResult = $user->getTestResults()->filter(function ($elem) use ($id) {
            return $elem->getId() == $id;
        })->first();

        if (!$testResult) {
            throw $this->createNotFoundException();
        }

        $recommendations = $testResult->getRecommendations();

        if (!isset($recommendations['pages'][$recommendation])) {
            throw $this->createNotFoundException();
        }

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        $page = $recommendations['pages'][$recommendation];
        $page = $jsonApiResponse->getPageRecommendationArray($page);
        $page['id'] = $id . "_" . $recommendation;

        return new JsonResponse(array(
          'data' => $page,
        ), 200);
    }

    /**
     * @Route("/account/reset-password", name="api_post_account_reset_password", methods="POST")
     */
    public function accountResetPasswordAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_USER_RESET_PASSWORD_POST);

        $user = $this->get('doctrine')->getManager()
            ->getRepository(User::REPOSITORY)
            ->findOneBy(["email" => $data->data->email]);

        if ($user) {
            $event = new UserEvent($user);
            $this->get('event_dispatcher')
                ->dispatch(Engage360dSecurityEvents::RESETTING_USER_PASSWORD, $event);
        }

        return new JsonResponse(["data" => []], 200);
    }

    /**
     * Sets hasDiabetes, hadHeartAttackOrStroke, hadBypassSurgery values of the
     * last TestResult to true.
     *
     * @Route("/account/incidents", name="api_post_account_incidents", methods="POST")
     */
    public function postAccountIncidentAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_INCIDENTS_POST);

        $user = $this->getUser();
        $lastTestResult = $user->getTestResults()->last();
        if (!$lastTestResult) {
            throw new HttpException(409, "First take the test.");
        }

        $this->populateEntity($lastTestResult, $data);

        $em = $this->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse(new \stdClass(), 201);
    }

    /**
     * @Route("/account/incidents", name="api_get_account_incidents", methods="GET")
     */
    public function getAccountIncidentsAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $user = $this->getUser();
        $lastTestResult = $user->getTestResults()->last();
        if (!$lastTestResult) {
            throw new HttpException(409, "First take the test.");
        }

        return [
            "data" => [
                "hadBypassSurgery" => $lastTestResult->getHadBypassSurgery(),
                "hadHeartAttackOrStroke" => $lastTestResult->getHadHeartAttackOrStroke(),
                "hasDiabetes" => $lastTestResult->getHasDiabetes(),
            ]
        ];
    }

    /**
     * @Route("/account/pills", name="api_get_account_pills", methods="GET")
     */
    public function getAccountPillsAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $user = $this->getUser();
        $pills = $user->getPills()->toArray();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getPillListResource($pills), 200);
    }

    /**
     * @Route("/account/pills", name="api_post_account_pills", methods="POST")
     */
    public function postAccountPillsAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_PILLS_POST);

        $pill = new Pill();
        $this->populateEntity($pill, $data);
        $pill->setUser($this->getUser());

        $em = $this->get('doctrine')->getManager();
        $em->persist($pill);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getPillResource($pill), 201);
    }

    /**
     * @Route("/account/pills/{id}", name="api_put_account_pills", methods="PUT")
     */
    public function putAccountPillsAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_PILLS_PUT);

        $user = $this->getUser();
        foreach ($user->getPills() as $p) {
            if ($p->getId() == $id) {
                $pill = $p;
            }
        }

        if (!isset($pill)) {
            return $this->createNotFoundException();
        }

        $this->populateEntity($pill, $data, $mappings = ["user" => User::REPOSITORY]);

        $em = $this->get('doctrine')->getManager();
        $em->persist($pill);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getPillResource($pill), 200);
    }

    /**
     * @Route("/account/pills/{id}", name="api_delete_account_pills", methods="DELETE")
     */
    public function deleteAccountPillAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $user = $this->getUser();

        $pill = null;
        foreach ($user->getPills()->toArray() as $p) {
            if ($p->getId() == $id) {
                $pill = $p;
            }
        }

        if (!$pill) {
            throw $this->createNotFoundException();
        }

        $user->removePill($pill);

        $em = $this->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse(new \stdClass(), 200);
    }

    /**
     * @Route("/account/timeline", name="api_get_account_timeline", methods="GET")
     */
    public function getAccountTimelineAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $user = $this->getUser();

        $timelineManager = $this->get('engage360d_takeda.timeline_manager');
        $timelineManager->setUser($user);

        $timeline = $timelineManager->getTimeline();
        unset($timeline["_id"]);

        return new JsonResponse($timeline, 200);
    }

    /**
     * @Route("/account/timeline/tasks/{id}", name="api_put_account_timeline", methods="PUT")
     */
    public function putAccountTimelineTaskAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_TIMELINE_TASKS_PUT);

        $user = $this->getUser();

        $timelineManager = $this->get('engage360d_takeda.timeline_manager');
        $timelineManager->setUser($user);

        $task = $timelineManager->updateTask($id, $data);

        return new JsonResponse(["data" => $task], 200);
    }

    /**
     * @Route("/account/reports/{reportType}/send-email", name="api_post_account_reports_send_email", methods="POST")
     */
    public function postAccountReportsSendEmailAction(Request $request, $reportType)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_REPORTS_SEND_EMAIL_POST);

        $user = $this->getUser();

        if (!$this->get('security.context')->isGranted('ROLE_DOCTOR') && $user->getTestResults()->last()->wasThereIncident()) {
            throw new HttpException(409, "Go to the doctor!");
        }

        $reportsManager = $this->get('engage360d_takeda.reports_manager');
        $reportsManager->init($user);

        $report = $reportsManager->getReport($reportType);

        // TODO consider to put this code into service
        $subject = "Report";
        $fromEmail = $this->container->getParameter('mailer_sender_email');
        if (!$fromEmail) {
            throw new \RuntimeException("The mandatory parameter 'mailer_sender_email' is not set", 500);
        }
        $body = $this->renderView(
            'Engage360dTakedaBundle:Account:email__report.html.twig',
            ['report' => $report]
        );

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($data->data->email)
            ->setBody($body, 'text/html');

        // With spooling turned off Swift_SwiftException will be caught
        // by FOSRestBundle Exception Handler and formatted by ExceptionWrapperHandler
        $this->get('mailer')->send($message);

        return new JsonResponse(["data" => (object) []], 200);
    }
}
