<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\TakedaUserBundle\EventListener;

use Engage360d\Bundle\SecurityBundle\Engage360dSecurityEvents;
use Engage360d\Bundle\SecurityBundle\Event\UserEvent;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailConfirmationListener implements EventSubscriberInterface
{
    private $mailer;
    private $tokenGenerator;
    private $router;
    private $session;
    private $templating;
    private $container;

    public function __construct($mailer, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $router, SessionInterface $session, $templating, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->session = $session;
        $this->templating = $templating;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            Engage360dSecurityEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationSuccess(UserEvent $event)
    {
        $user = $event->getUser();

        $this->sendConfirmationEmailMessage($user);
        
        if ($event->isAuthenticate()) {
          $this->authenticateUser($user);
        }

        $this->session->set('engage360d_takeda_user_send_confirmation_email/email', $user->getEmail());
    }

    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $confirmationUrl = $this->router->generate(
          'engage360d_takeda_user_registration_confirm',
          array('token' => $user->getConfirmationToken()),
          true
        );

        $renderedTemplate = $this->templating
          ->render(
            'Engage360dTakedaUserBundle:Account:confirmation_email.txt.twig',
            array(
              'confirmationUrl' => $confirmationUrl,
              'user' => $user,
            )
        );

        $subject = "Confirm registration";
        $fromEmail = "test@test.ru";
        $body = $renderedTemplate;

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($user->getEmail())
            ->setBody($body);

        $this->mailer->send($message);
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
