<?php

namespace Engage360d\Bundle\TakedaTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestController extends Controller
{
    public function getTestAction()
    {
        return $this->render('Engage360dTakedaTestBundle:Test:test.html.twig');
    }
}
