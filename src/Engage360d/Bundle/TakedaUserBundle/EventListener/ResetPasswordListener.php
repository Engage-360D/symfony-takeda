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

class ResetPasswordListener implements EventSubscriberInterface
{
    private $mailer;
    private $tokenGenerator;
    private $router;
    private $templating;
    private $container;

    public function __construct($mailer, UrlGeneratorInterface $router, $templating, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            Engage360dSecurityEvents::RESETTING_USER_PASSWORD => 'onResettingUserPassword',
            Engage360dSecurityEvents::RESET_USER_PASSWORD_SUCCESS => 'onResetUserPasswordSuccess',
        );
    }

    public function onResettingUserPassword(UserEvent $event)
    {
        $user = $event->getUser();
        $this->sendResetPasswordEmailMessage($user);
    }

    public function onResetUserPasswordSuccess(UserEvent $event)
    {
        $this->authenticateUser($event->getUser());
    }

    public function sendResetPasswordEmailMessage(UserInterface $user)
    {
        $resettingUrl = $this->router->generate(
          'engage360d_takeda_user_reset_password',
          array('token' => $user->getConfirmationToken()),
          true
        );

        $renderedTemplate = $this->templating
          ->render(
            'Engage360dTakedaUserBundle:Account:resetting_email.txt.twig',
            array(
              'resettingUrl' => $resettingUrl,
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
