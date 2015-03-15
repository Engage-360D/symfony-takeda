<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Form\Exception\InvalidPropertyPathException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\SecurityExtraBundle\Annotation\Secure;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use HWI\Bundle\OAuthBundle\Controller\ConnectController;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\VkontakteResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\FacebookResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GoogleResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\OdnoklassnikiResourceOwner;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

class SecurityController extends ConnectController
{
    public function connectSuccessAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        // TODO refactor
        $user = [
            "id" => (String) $user->getId(),
            "email" => $user->getEmail(),
            "firstname" => $user->getFirstname(),
            "lastname" => $user->getLastname(),
            "birthday" => $user->getBirthday()->format(\DateTime::ISO8601),
            "vkontakteId" => $user->getVkontakteId(),
            "facebookId" => $user->getFacebookId(),
            "odnoklassnikiId" => $user->getOdnoklassnikiId(),
            "googleId" => $user->getGoogleId(),
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
        ];

        return $this->container->get('templating')->renderResponse(
            'Engage360dTakedaBundle:Security:connect.success.html.twig',
            ["data" => ["user" => $user]]
        );
    }

    public function connectFailureAction(Request $request)
    {
        $auth = [];

        $error = $this->getErrorForRequest($request);

        if ($error instanceof AccountNotLinkedException) {
            $userInformation = $this
                ->getResourceOwnerByName($error->getResourceOwnerName())
                ->getUserInformation($error->getRawToken());
            $resourceOwner = $this->getResourceOwnerByName($error->getResourceOwnerName());

            if ($resourceOwner instanceof FacebookResourceOwner) {
                $auth['facebookId'] = $userInformation->getUsername();
                $auth['facebookToken'] = $error->getRawToken()['access_token'];
            } else if ($resourceOwner instanceof VkontakteResourceOwner) {
                $auth['vkontakteId'] = (string)$userInformation->getUsername();
                $auth['vkontakteToken'] = $error->getRawToken()['access_token'];
            } else if ($resourceOwner instanceof OdnoklassnikiResourceOwner) {
                $auth['odnoklassnikiId'] = (string)$userInformation->getUsername();
                $auth['odnoklassnikiToken'] = $error->getRawToken()['access_token'];
            } else if ($resourceOwner instanceof GoogleResourceOwner) {
                $auth['googleId'] = (string)$userInformation->getUsername();
                $auth['googleToken'] = $error->getRawToken()['access_token'];
            }
        }

        $regions = $this->container->get('doctrine')->getRepository('Engage360dTakedaBundle:Region\Region')->findAll();

        return $this->container->get('templating')->renderResponse(
            'Engage360dTakedaBundle:Security:connect.failure.html.twig',
            ['user' => null, 'auth' => $auth, 'regions' => $regions]
        );
    }

    /**
     * Connects a user to a given account if the user is logged in and connect is enabled.
     *
     * @param Request $request The active request.
     * @param string  $service Name of the resource owner to connect to.
     *
     * @throws \Exception
     *
     * @return Response
     *
     * @throws NotFoundHttpException if `connect` functionality was not enabled
     * @throws AccessDeniedException if no user is authenticated
     */
    public function connectServiceAction(Request $request, $service)
    {
        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!$hasUser) {
            //redirect to register
            throw new AccessDeniedException('Cannot connect an account.');
        }
        // Get the data from the resource owner
        $resourceOwner = $this->getResourceOwnerByName($service);
        $session = $request->getSession();
        $key = $request->query->get('key', time());
        if ($resourceOwner->handles($request)) {
            $accessToken = $resourceOwner->getAccessToken(
                $request,
                $this->generate('hwi_oauth_connect_service', array('service' => $service), true)
            );
            // save in session
            $session->set('_hwi_oauth.connect_confirmation.'.$key, $accessToken);
        } else {
            $accessToken = $session->get('_hwi_oauth.connect_confirmation.'.$key);
        }
        $userInformation = $resourceOwner->getUserInformation($accessToken);
        $currentToken = $this->container->get('security.context')->getToken();
        $currentUser  = $currentToken->getUser();
        $this->container
            ->get('engage360d_security.oauth.provider')
            ->connect($currentUser, $userInformation);
        $url = $this->container->get('router')->generate('engage360d_oauth_connect_success');
        return new RedirectResponse($url);
    }

    /**
     * Override HWIOAuthBundle:Connect:redirectToService method
     * in order to use our parameter default_target_path.
     *
     * @param Request $request
     * @param string  $service
     *
     * @return RedirectResponse
     */
    public function redirectToServiceAction(Request $request, $service)
    {
        $authorizationUrl = $this->container->get('hwi_oauth.security.oauth_utils')->getAuthorizationUrl($request, $service);

        // Check for a return path and store it before redirect
        if ($request->hasSession()) {
            // initialize the session for preventing SessionUnavailableException
            $session = $request->getSession();
            $session->start();

            $providerKey = $this->container->getParameter('hwi_oauth.firewall_name');
            $sessionKey = '_security.' . $providerKey . '.target_path';

            $defaultTargetPath = $this->container->getParameter('engage360d.oauth.default_target_path');
            if ($defaultTargetPath) {
                $session->set($sessionKey, $defaultTargetPath);
            } else {
                throw new ParameterNotFoundException('engage360d.oauth.default_target_path');
            }
        }

        return new RedirectResponse($authorizationUrl);
    }
}
