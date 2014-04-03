<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function mainpageAction()
    {
        return $this->render('Engage360dTakedaBundle:Main:mainpage.html.twig');
    }
}
