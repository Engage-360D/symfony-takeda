<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends Controller
{
    public function signinAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        if ($user instanceof UserInterface) {
            return $this->redirect($this->generateUrl('engage360d_takeda_account_recommendations'));
        }

        $regions = $this->getDoctrine()->getRepository('Engage360dTakedaBundle:Region\Region')->findAll();

        return $this->render('Engage360dTakedaBundle:Auth:signin.html.twig', array(
            'regions' => $regions,
        ));
    }
}
