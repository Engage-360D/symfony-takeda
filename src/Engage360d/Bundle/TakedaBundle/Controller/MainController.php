<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function graphBlockAction()
    {
        $data = array(
            array(
                'title' => 'Осложнения',
                'data' => array(
                    array(
                        'title' => 'Без профилактики',
                        'value' => rand(26, 50),
                        'style' => 'dashed',
                        'titlePosition' => 'right',
                        'valuePosition' => 'outside_right',
                    ),
                    array(
                        'title' => 'С профилактикой',
                        'value' => rand(1, 25),
                        'style' => 'solid',
                        'titlePosition' => 'center',
                        'valuePosition' => 'inside',
                    ),
                ),
            ),
            array(
                'title' => 'Инфаркт',
                'data' => array(
                    array(
                        'title' => 'Без профилактики',
                        'value' => rand(26, 50),
                        'style' => 'dashed',
                        'valuePosition' => 'outside_right',
                    ),
                    array(
                        'title' => 'С профилактикой',
                        'value' => rand(1, 25),
                        'style' => 'solid',
                        'valuePosition' => 'inside',
                    ),
                ),
            ),
            array(
                'title' => 'Инсульт',
                'data' => array(
                    array(
                        'title' => 'Без профилактики',
                        'value' => rand(26, 50),
                        'style' => 'dashed',
                        'valuePosition' => 'outside_right',
                    ),
                    array(
                        'title' => 'С профилактикой',
                        'value' => rand(1, 25),
                        'style' => 'solid',
                        'valuePosition' => 'inside',
                    ),
                ),
            ),
        );

        return $this->render('Engage360dTakedaBundle:Main:graphBlock.html.twig', array(
            'data' => $data
        ));
    }

    public function mainpageAction()
    {
        return $this->render('Engage360dTakedaBundle:Main:mainpage.html.twig');
    }
}
