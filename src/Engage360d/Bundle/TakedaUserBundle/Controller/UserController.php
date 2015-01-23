<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\TakedaUserBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Form\Exception\InvalidPropertyPathException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;

/**
 * Process form auth to oauth.
 *
 * @author Andrey Linko <AndreyLinko@gmail.com>
 */
class UserController extends Controller
{
    public function loginAction()
    {
        return $this->render('Engage360dTakedaUserBundle:User:login.html.twig', array());
    }


    public function registrationAction(Request $request)
    {
        $userManager = $this->container->get("engage360d_security.manager.user");
        $user = $userManager->createUser();
        $user->setEnabled(true);

        //$form = $this->container->get("engage360d_takeda_user.form.registration");
        //$form->setData($user);

        return $this->container->get('templating')->renderResponse('Engage360dTakedaUserBundle:User:registration.html.twig', array(
            //'form' => $form->createView(),
        ));
    }

    public function confirmAction(Request $request)
    {
    }
}
