<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Post;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Engage360d\Bundle\TakedaBundle\Entity\TestResult;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;
use Engage360d\Bundle\TakedaBundle\Entity\User\User;
use Engage360d\Bundle\TakedaBundle\Entity\Region\Region;
use Engage360d\Bundle\TakedaBundle\Entity\Pill;
use Engage360d\Bundle\SecurityBundle\Event\UserEvent;
use Engage360d\Bundle\SecurityBundle\Engage360dSecurityEvents;
use Engage360d\Bundle\TakedaBundle\Utils\Timeline;

class AccountController extends TakedaJsonApiController
{
    const URI_USER_PUT = '/api/v1/schemas/users/put.json';
    const URI_TEST_RESULTS_POST = '/api/v1/schemas/test-results/post.json';
    const URI_USER_RESET_PASSWORD_POST = '/api/v1/schemas/users/reset-password/post.json';
    const URI_TEST_RESULTS_SEND_EMAIL_POST = '/api/v1/schemas/test-results/send-email/post.json';
    const URI_PILLS_POST = '/api/v1/schemas/pills/post.json';
    const URI_PILLS_PUT = '/api/v1/schemas/pills/put.json';
    const URI_TIMELINE_TASKS_PUT = '/api/v1/schemas/timelines/tasks/put.json';

    /**
     * @Route("/account", name="api_get_account", methods="GET")
     */
    public function getAccountAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return new JsonResponse("Unauthorized", 401);
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $response = [
            "links" => $this->getUsersRegionLink(),
            "data" => $this->getUserArray($user)
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/account", name="api_post_account", methods="PUT")
     */
    public function putAccountAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return new JsonResponse("Unauthorized", 401);
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $json = $request->getContent();
        $data = json_decode($json);

        // TODO put all occurrences of this code into getSchemaValidator($shcemaFile, $data) method
        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_USER_PUT
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
        }

        if (isset($data->data->facebookId)) {
            $accessToken = isset($data->data->facebookToken) ? $data->data->facebookToken : "";

            if ($data->data->facebookId !== $this->getFacebookId($accessToken)) {
                return $this->getErrorResponse("Provided facebookToken is not valid", 400);
            }
        }

        if (isset($data->data->vkontakteId)) {
            $accessToken = isset($data->data->vkontakteToken) ? $data->data->vkontakteToken : "";


            if (!$this->isVkontakteCredentialsValid($data->data->vkontakteId, $accessToken)) {
                return $this->getErrorResponse("Provided vkontakteId (or vkontakteToken) is not valid", 400);
            }
        }

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

        $response = [
            "links" => $this->getUsersRegionLink(),
            "data" => $this->getUserArray($user)
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/account/test-results", name="api_post_account_test_results", methods="POST")
     */
    public function postAccountTestResultAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return new JsonResponse("Unauthorized", 401);
        }

        // Note, that a user with ROLE_ADMIN is always granted a role ROLE_DOCTOR.
        // See role_hierarchy in security settings.
        if (!$this->get('security.context')->isGranted('ROLE_DOCTOR')) {
            // Prohibit a user who had a heart attack or stroke from taking another test.
            $lastTestResult = $user->getTestResults()->last();
            if ($lastTestResult && $lastTestResult->getHadHeartAttackOrStroke()) {
                return $this->getErrorResponse("Go to the doctor, quick!", 409);
            }

            // Allow a user to take a test only once a month.
            foreach ($user->getTestResults()->toArray() as $result) {
                if ($result->getCreatedAt()->diff(new \DateTime())->m === 0) {
                    return $this->getErrorResponse("You're allowed to take the test only once a month.", 409);
                }
            }
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $json = $request->getContent();
        $data = json_decode($json);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_TEST_RESULTS_POST
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
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

        $response = [
            "data" => $this->getTestResultArray($testResult)
        ];

        return new JsonResponse($response, 201);
    }

    /**
     * @Route("/account/test-results", name="api_get_account_test_results", methods="GET")
     */
    public function getAccountTestResultAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return new JsonResponse("Unauthorized", 401);
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

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

        $response = [
            "data" => array_map([$this, 'getTestResultArray'], $testResults)
        ];

        return new JsonResponse($response, 200);
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
        $user = $this->get('security.context')->getToken()->getUser();

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
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return new JsonResponse("Unauthorized", 401);
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $json = $request->getContent();
        $data = json_decode($json);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_TEST_RESULTS_SEND_EMAIL_POST
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
        }

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
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return new JsonResponse("Unauthorized", 401);
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

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

        $page = $recommendations['pages'][$recommendation];
        $page = $this->getPageRecommendationArray($page);
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
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $json = $request->getContent();
        $data = json_decode($json);

        // TODO put all occurrences of this code into getSchemaValidator($shcemaFile, $data) method
        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_USER_RESET_PASSWORD_POST
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
        }

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
     * @Route("/account/pills", name="api_get_account_pills", methods="GET")
     */
    public function getAccountPillsAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $user = $this->get('security.context')->getToken()->getUser();
        $pills = $user->getPills()->toArray();

        $response = [
            "links" => $this->getPillLink(),
            "data" => array_map([$this, 'getPillArray'], $pills),
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/account/pills", name="api_post_account_pills", methods="POST")
     */
    public function postAccountPillsAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $json = $request->getContent();
        $data = json_decode($json);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_PILLS_POST
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
        }

        $pill = new Pill();
        $this->populateEntity($pill, $data);
        $pill->setUser($this->getUser());

        $em = $this->get('doctrine')->getManager();
        $em->persist($pill);
        $em->flush();

        $response = [
            "links" => $this->getPillLink(),
            "data" => $this->getPillArray($pill),
        ];

        return new JsonResponse($response, 201);
    }

    /**
     * @Route("/account/pills/{id}", name="api_put_account_pills", methods="PUT")
     */
    public function putAccountPillsAction(Request $request, $id)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $json = $request->getContent();
        $data = json_decode($json);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_PILLS_PUT
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
        }

        $user = $this->get('security.context')->getToken()->getUser();
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

        $response = [
            "links" => $this->getPillLink(),
            "data" => $this->getPillArray($pill),
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/account/pills/{id}", name="api_delete_account_pills", methods="DELETE")
     */
    public function deleteAccountPillAction(Request $request, $id)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $user = $this->get('security.context')->getToken()->getUser();

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

        return new JsonResponse(null, 200);
    }

    /**
     * @Route("/account/timeline", name="api_get_account_timeline", methods="GET")
     */
    public function getAccountTimelineAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $user = $this->get('security.context')->getToken()->getUser();

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
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $json = $request->getContent();
        $data = json_decode($json);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_TIMELINE_TASKS_PUT
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
        }

        $user = $this->get('security.context')->getToken()->getUser();

        $timelineManager = $this->get('engage360d_takeda.timeline_manager');
        $timelineManager->setUser($user);

        $task = $timelineManager->updateTask($id, $data);

        return new JsonResponse(["data" => $task], 200);
    }
}
