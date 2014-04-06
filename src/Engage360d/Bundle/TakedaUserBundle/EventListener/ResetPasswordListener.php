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

class ResetPasswordListener implements EventSubscriberInterface
{
    private $mailer;
    private $tokenGenerator;
    private $router;
    private $templating;

    public function __construct($mailer, UrlGeneratorInterface $router, $templating)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
    }

    public static function getSubscribedEvents()
    {
        return array(
            Engage360dSecurityEvents::RESETTING_USER_PASSWORD => 'onResettingUserPassword',
        );
    }

    public function onResettingUserPassword(UserEvent $event)
    {
        $user = $event->getUser();
        $this->sendResetPasswordEmailMessage($user);
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
}
