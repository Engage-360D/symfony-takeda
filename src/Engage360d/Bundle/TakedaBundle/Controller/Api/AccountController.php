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
use Engage360d\Bundle\SecurityBundle\Event\UserEvent;
use Engage360d\Bundle\SecurityBundle\Engage360dSecurityEvents;

class AccountController extends TakedaJsonApiController
{
    const URI_USER_PUT = '/api/v1/schemas/users/put.json';
    const URI_TEST_RESULTS_POST = '/api/v1/schemas/test-results/post.json';

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
        $user = $this->populateEntity($user, $data);

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

        // TODO tune voter to not interfere with security context
        // and use
        // !$this->get('security.context')->isGranted('ROLE_DOCTOR')
        if (!(in_array('ROLE_DOCTOR', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles())) && count($user->getTestResults()) > 0) {
            return new JsonResponse("Test already passed by user.", 400);
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

        $response = [
            "data" => array_map([$this, 'getTestResultArray'], $user->getTestResults()->toArray())
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/account/reset-password", name="api_post_account_reset_password", methods="POST")
     */
    public function accountResetPasswordAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return new JsonResponse("Unauthorized", 401);
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $event = new UserEvent($user);
        $this->get('event_dispatcher')
            ->dispatch(Engage360dSecurityEvents::RESETTING_USER_PASSWORD, $event);

        return new JsonResponse("The password has been reset.", 200);
    }
}
