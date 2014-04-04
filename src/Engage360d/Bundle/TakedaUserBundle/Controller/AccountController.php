<?php

namespace Engage360d\Bundle\TakedaUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
    public function accountAction()
    {
        return $this->render('Engage360dTakedaUserBundle:Account:account.html.twig');
    }
}
