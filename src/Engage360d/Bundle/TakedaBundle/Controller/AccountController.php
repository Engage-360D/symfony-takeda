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
    public function logoutAction(Request $request)
    {
        $this->get('security.context')->setToken(null);
        $request->getSession()->invalidate();

        return $this->redirect($this->generateUrl('engage360d_takeda_main_mainpage'));
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
