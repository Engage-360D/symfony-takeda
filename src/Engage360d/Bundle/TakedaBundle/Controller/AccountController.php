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

class AccountController extends Controller
{
    public function recommendationsAction()
    {
        $user = $this->getUser();
        $testResults = array_map([$this, 'getTestResultArray'], $user->getTestResults()->toArray());

        return $this->render('Engage360dTakedaBundle:Account:recommendations.html.twig', [
            'testResults' => $testResults,
        ]);
    }

    public function logoutAction(Request $request)
    {
        $this->get('security.context')->setToken(null);
        $request->getSession()->invalidate();

        return $this->redirect($this->generateUrl('engage360d_takeda_main_mainpage'));
    }

    // TODO: refactor me please
    public function getTestResultArray($testResult)
    {
        $recommendations = $testResult->getRecommendations();
        unset($recommendations['pages']);

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
            "recommendations" => $recommendations,
        ];
    }

//    public function failureLoginAction(Request $request)
//    {
//        $response = array();
//        $request = $this->getRequest();
//        $session = $request->getSession();
//
//        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
//            $error = $request->attributes->get(
//                SecurityContext::AUTHENTICATION_ERROR
//            );
//        } else {
//            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
//        }
//
//        if ($error instanceof BadCredentialsException) {
//            $user = $this->container
//                ->get('engage360d_security.manager.user')
//                ->findUserByUsernameOrEmail($error->getToken()->getUsername());
//
//            if (null === $user) {
//                $response['username'] = $this->get('translator')->trans('error.invalid.username');
//            } else {
//                $response['password'] = $this->get('translator')->trans('error.invalid.password');
//            }
//        } else {
//            $response['error'] = $error->getMessage();
//        }
//
//        return new JsonResponse($response, 401);
//    }
//
//    protected function authenticateUser(UserInterface $user)
//    {
//        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
//        $this->container->get("security.context")->setToken($token);
//
//        $request = $this->container->get("request");
//        $event = new InteractiveLoginEvent($request, $token);
//        $this->container->get("event_dispatcher")->dispatch("security.interactive_login", $event);
//    }
}
