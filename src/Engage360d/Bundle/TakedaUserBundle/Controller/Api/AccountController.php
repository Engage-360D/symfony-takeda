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
use Engage360d\Bundle\RestBundle\Controller\RestController;
use Engage360d\Bundle\TakedaTestBundle\Entity\TestResult;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AccountController extends RestController
{
    /**
     * @Route("/account", name="api_post_account", methods="PUT")
     */
    public function putAccountAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            return new JsonResponse("Unauthorized", 401);
        }

        $json = $request->getContent();
        $data = json_decode($json, true);

        if (!isset($data['users'])) {
            return new JsonResponse("Invalid request", 400);
        }

        foreach ($data['users'] as $property => $value) {
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
            "users" => [
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

        $json = $request->getContent();
        $data = json_decode($json, true);

        if (!isset($data['testResults'])) {
            return new JsonResponse("Invalid request", 400);
        }

        $testResult = new TestResult;
        foreach ($data['testResults'] as $property => $value) {
            $method = 'set' . ucfirst($property);
            $testResult->$method($value);
        }
        $testResult->setUser($user);

        $em = $this->get('doctrine')->getEntityManager();
        $em->persist($testResult);
        $em->flush();

        $response = [
            "testResults" => $this->prepareTestResult($testResult)
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

        $response = ["testResults" => []];

        foreach($user->getTestResults() as $result) {
            $response["testResults"][] = $this->prepareTestResult($result);
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
