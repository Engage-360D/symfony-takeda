<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\TakedaUserBundle\EventListener;

use Engage360d\Bundle\SecurityBundle\Engage360dSecurityEvents;
use Engage360d\Bundle\SecurityBundle\Event\FormEvent;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailConfirmationListener implements EventSubscriberInterface
{
    private $mailer;
    private $tokenGenerator;
    private $router;
    private $session;
    private $templating;

    public function __construct($mailer, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $router, SessionInterface $session, $templating)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->session = $session;
        $this->templating = $templating;
    }

    public static function getSubscribedEvents()
    {
        return array(
            Engage360dSecurityEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $user = $event->getForm()->getData();

        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $this->sendConfirmationEmailMessage($user);

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
            'Engage360dTakedaUserBundle:User:confirmation_email.txt.twig',
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
}
