<?php

namespace Engage360d\Bundle\TakedaUserBundle\Controller\Api;

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
use Engage360d\Bundle\TakedaTestBundle\Entity\TestResult;
use Engage360d\Bundle\JsonApiBundle\Controller\JsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;

class AccountController extends JsonApiController
{
    const URI_USER_PUT = '/api/v1/schemas/users/put.json';
    const URI_TEST_RESULTS_POST = '/api/v1/schemas/test-results/post.json';

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
            return $this->getErrorResponse("The expected content type is \"application/vnd.api+json\"", 400);
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

        foreach ($data->data as $property => $value) {
            $method = 'set' . ucfirst($property);
            $user->$method($value);
        }

        $em = $this->get('doctrine')->getEntityManager();
        $em->persist($user);
        $em->flush();

        $response = [
            "links" => [
                "tokens.user" => [
                    "href" => "https://cardiomagnyl.ru/api/v1/regions/{users.region}",
                    "type" => "regions"
                ]
            ],
            "data" => [
                "id" => (String) $user->getId(),
                "email" => $user->getEmail(),
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "birthday" => $user->getBirthday()->format(\DateTime::ISO8601),
                "vkontakteId" => $user->getVkontakteId(),
                "facebookId" => $user->getFacebookId(),
                "specializationExperienceYears" => $user->getSpecializationExperienceYears(),
                "specializationGraduationDate" => $user->getSpecializationGraduationDate(),
                "specializationInstitutionAddress" => $user->getSpecializationInstitutionAddress(),
                "specializationInstitutionName" => $user->getSpecializationInstitutionName(),
                "specializationInstitutionPhone" => $user->getSpecializationInstitutionPhone(),
                "specializationName" => $user->getSpecializationName(),
                "roles" => $user->getRoles(),
                "isEnabled" => $user->getEnabled(),
                "links" => [
                    "region" => $user->getRegion() ? (String) $user->getRegion()->getId() : null
                ]
            ]
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
            return $this->getErrorResponse("The expected content type is \"application/vnd.api+json\"", 400);
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

        $em = $this->get('doctrine')->getEntityManager();
        $em->persist($testResult);
        $em->flush();

        $response = [
            "data" => $this->prepareTestResult($testResult)
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
            return $this->getErrorResponse("The expected content type is \"application/vnd.api+json\"", 400);
        }

        $response = ["data" => []];

        foreach($user->getTestResults() as $result) {
            $response["data"][] = $this->prepareTestResult($result);
        }

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
            return $this->getErrorResponse("The expected content type is \"application/vnd.api+json\"", 400);
        }

        // TODO invalidate hwi and lexik tokens
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();

        return new JsonResponse("Logged out.", 200);
    }

    private function prepareTestResult($testResult)
    {
        return [
            "id" => (String) $testResult->getId(),
            "sex" => $testResult->getSex(),
            "birthday" => $testResult->getBirthday()->format(\DateTime::ISO8601),
            "growth" => $testResult->getGrowth(),
            "weight" => $testResult->getWeight(),
            "isSmoker" => $testResult->getIsSmoker(),
            "cholesterolLevel" => $testResult->getCholesterolLevel(),
            "isCholesterolDrugsConsumer" => $testResult->getIsCholesterolDrugsConsumer(),
            "hasDiabetes" => $testResult->getHasDiabetes(),
            "hadSugarProblems" => $testResult->getHadSugarProblems(),
            "isSugarDrugsConsumer" => $testResult->getIsSugarDrugsConsumer(),
            "arterialPressure" => $testResult->getArterialPressure(),
            "isArterialPressureDrugsConsumer" => $testResult->getIsArterialPressureDrugsConsumer(),
            "physicalActivityMinutes"  => $testResult->getPhysicalActivityMinutes(),
            "hadHeartAttackOrStroke" => $testResult->getHadHeartAttackOrStroke(),
            "isAddingExtraSalt" => $testResult->getIsAddingExtraSalt(),
            "isAcetylsalicylicDrugsConsumer" => $testResult->getIsAcetylsalicylicDrugsConsumer(),
            "bmi" => $testResult->getBmi(),
            "score" => $testResult->getScore(),
            "recommendations" => $testResult->getRecommendations()
        ];
    }
}
