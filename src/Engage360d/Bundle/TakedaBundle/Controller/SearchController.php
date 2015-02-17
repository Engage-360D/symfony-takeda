<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;

class SearchController extends Controller
{
    public function indexAction()
    {
        $keys = array(
            'opinion' => 'Engage360dTakedaBundle:PressCenter\Opinion',
            'news' => 'Engage360dTakedaBundle:PressCenter\News',
            'institution' => 'Engage360dTakedaBundle:Institution',
        );

        $searchQuery = $this->getRequest()->query->get('query');

        $result = array();

        foreach ($keys as $key => $repoName) {
            $result[$key] = [];
            $elasticaResult = $this->get('fos_elastica.index.website.'.$key)->search($searchQuery);
            foreach ($elasticaResult as $item) {
                $repo = $this->getDoctrine()->getRepository($repoName);
                $result[$key][] = array(
                    'id' => $item->getId(),
                    'type' => $key,
                    'source' => $item->getSource(),
                    'entity' => $repo->findOneById($item->getId()),
                );
            }
        }

        return $this->render('Engage360dTakedaBundle:Search:index.html.twig', array(
            'searchQuery' => $searchQuery,
            'result' => $result,
        ));
    }
}
