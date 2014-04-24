<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    public function searchAction(Request $request)
    {
        $searchQuery = $request->query->get('query');
        $elasticaResult = $this->get('fos_elastica.index')->search($searchQuery);


        return $this->render('Engage360dTakedaBundle:Search:resultspage.html.twig',
            array('elasticaResult' => $elasticaResult)
        );
    }
}