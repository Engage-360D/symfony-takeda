<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\TakedaBundle\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Engage360d\Bundle\SecurityBundle\Engage360dSecurityEvents;
use Engage360d\Bundle\SecurityBundle\Event\UserEvent;

class ResetPasswordSubscriber implements EventSubscriberInterface
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
        );
    }

    public function onResettingUserPassword(UserEvent $event)
    {
        $user = $event->getUser();

        $token = $this->container->get('fos_user.util.token_generator')->generateToken();
        $newPlainPasswod = substr($token, 0, rand(10, 15));

        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $password = $encoder->encodePassword($newPlainPasswod, $user->getSalt());

        $user->setPassword($password);

        $subject = "Resetting password";
        $fromEmail = $this->container->getParameter('mailer_sender_email');
        if (!$fromEmail) {
            throw new \RuntimeException("The mandatory parameter 'mailer_sender_email' is not set", 500);
        }
        $body = $this->templating->render(
            'Engage360dTakedaBundle:Account:email__reset_password.txt.twig',
            array(
                'newPlainPassword' => $newPlainPasswod,
                'user' => $user,
            )
        );

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($user->getEmail())
            ->setBody($body);

        // With spooling turned off Swift_SwiftException will be caught
        // by FOSRestBundle Exception Handler and formatted by ExceptionWrapperHandler
        $this->mailer->send($message);

        $em = $this->container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
    }
}
