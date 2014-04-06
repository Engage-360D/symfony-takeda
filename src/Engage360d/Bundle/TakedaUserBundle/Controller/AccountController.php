<?php

namespace Engage360d\Bundle\TakedaUserBundle\Controller;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserInterface;

class AccountController extends Controller
{
    public function accountAction()
    {
        $token = $this->container->get('security.context')->getToken();

        if (null === $token || null === $token->getUser() || false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
          return $this->render('Engage360dTakedaUserBundle:Account:login.html.twig');
        }

        return $this->render('Engage360dTakedaUserBundle:Account:account.html.twig', array(
            'user' => $token->getUser(),
        ));
    }

    public function confirmAction(Request $request, $token)
    {
        $userManager = $this->container
            ->get('engage360d_security.manager.user');

        $user = $userManager->findUserByConfirmationToken($token);
        
        if (null === $user) {
            return new NotFoundHttpException();
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $user->addRole("ROLE_CONFIRMED");

        $userManager->updateUser($user);

        $this->authenticateUser($user);

        $url = $this->container->get('router')->generate('engage360d_takeda_user_account');
        return new RedirectResponse($url);
    }

    public function successTokenAuthAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array("/"));
        $client->setAllowedGrantTypes(array("token"));
        $clientManager->updateClient($client);

        $params = $request->query->all(); 
        $params['client_id'] = $client->getPublicId();
        $params['client_secret'] = $client->getSecret();
        $params['grant_type'] = 'client_credentials';
        $params['redirect_uri'] = '/';
        $params['response_type'] = 'token';
        $request->query->replace($params);

        return $this->container
            ->get('fos_oauth_server.server')
            ->finishClientAuthorization(true, $user, $request, null);
    }

    protected function authenticateUser(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->container->get("security.context")->setToken($token);
     
        $request = $this->container->get("request");
        $event = new InteractiveLoginEvent($request, $token);
        $this->container->get("event_dispatcher")->dispatch("security.interactive_login", $event);
    }
}
