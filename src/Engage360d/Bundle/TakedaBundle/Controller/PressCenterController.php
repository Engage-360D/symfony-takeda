<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\News;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Opinion;

class PressCenterController extends Controller
{
    public function indexAction()
    {
        $news = $this->get('doctrine')->getRepository(News::REPOSITORY)
            ->findActiveForLast12Months();

        $opinions = $this->get('doctrine')->getRepository(Opinion::REPOSITORY)
            ->findActiveForLast12Months();

        // Actually, these are the latest articles of both types, i.e News and Opinions.
        // They'll rarely be this week latest articles.
        $weekSummary = array_merge(
            array_slice($news, 0, 2),
            array_slice($opinions, 0, 2)
        );
        // sort by createdAt DESC
        usort($weekSummary, function ($a, $b) {
            $timestampA = (int) $a->getCreatedAt()->format('U');
            $timestampB = (int) $b->getCreatedAt()->format('U');

            if ($timestampA === $timestampB) {
                return 0;
            }

            return $timestampB - $timestampA;
        });


        return $this->render('Engage360dTakedaBundle:PressCenter:index.html.twig',
            [
                "news" => $news,
                "opinions" => $opinions,
                "weekSummary" => $weekSummary,
            ]
        );
    }

    public function articleAction($articleType, $id)
    {
        $repository = $this->get('doctrine')->getRepository(
            $articleType === News::TYPE_NEWS ? News::REPOSITORY : Opinion::REPOSITORY
        );

        $article = $repository->findOneById($id);

        if (!$article || !$article->getIsActive()) {
            return $this->createNotFoundException();
        }

        return $this->render('Engage360dTakedaBundle:PressCenter:article.html.twig', ["article" => $article]);
    }
}
