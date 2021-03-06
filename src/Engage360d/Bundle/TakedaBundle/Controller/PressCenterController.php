<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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

        $opinions = array_map(function ($opinion) {
            // TODO refactor
            return [
                "id" => (string) $opinion->getId(),
                "title" => $opinion->getTitle(),
                "content" => strip_tags($opinion->getContent()),
                "isActive" => $opinion->getIsActive(),
                "viewsCount" => $opinion->getViewsCount(),
                "createdAt" => $opinion->getCreatedAt(),
                "uri" => $this->get('router')->generate(
                        'engage360d_takeda_press_center_article',
                        [
                            "articleType" => $opinion->getType(),
                            "id" => $opinion->getId(),
                        ]
                ),
                "links" => [
                    "expert" => [
                        "id" => (string) $opinion->getExpert()->getId(),
                        "photoUri" => $opinion->getExpert()->getPhotoUri(),
                        "name" => $opinion->getExpert()->getName(),
                        "description" => $opinion->getExpert()->getDescription(),
                    ]
                ],
            ];
        }, $opinions);

        $news = array_map(function ($article) {
            // TODO refactor
            return [
                "id" => (string) $article->getId(),
                "title" => $article->getTitle(),
                "content" => strip_tags($article->getContent()),
                "isActive" => $article->getIsActive(),
                "createdAt" => $article->getCreatedAt(),
                "uri" => $this->get('router')->generate(
                        'engage360d_takeda_press_center_article',
                        [
                            "articleType" => $article->getType(),
                            "id" => $article->getId(),
                        ]
                    ),
                "links" => [
                    "category" => [
                        "id" => (string) $article->getCategory()->getId(),
                        "data" => $article->getCategory()->getData(),
                        "keyword" => $article->getCategory()->getKeyword(),
                    ]
                ],
            ];
        }, $news);

        return $this->render('Engage360dTakedaBundle:PressCenter:index.html.twig',
            [
                "news" => $news,
                "opinions" => $opinions,
                "weekSummary" => $weekSummary,
            ]
        );
    }

    public function articleAction(Request $request, $articleType, $id)
    {
        if (!in_array($articleType, [News::TYPE_NEWS, News::TYPE_OPINION])) {
            throw $this->createNotFoundException();
        }

        $repository = $this->get('doctrine')->getRepository(
            $articleType === News::TYPE_NEWS ? News::REPOSITORY : Opinion::REPOSITORY
        );

        $article = $repository->findOneById($id);

        if (!$article || !$article->getIsActive()) {
            return $this->createNotFoundException();
        }

        $recentArticles = $repository->findLastFourExceptOne($id);

        // Increment views counter.
        // Allow only one view per session.
        $session = $request->getSession();
        if (!$session->get('is_seen_article_' . $id)) {
            $article->setViewsCount($article->getViewsCount() + 1);
            $session->set('is_seen_article_' . $id, true);

            $em = $this->get('doctrine')->getManager();
            $em->persist($article);
            $em->flush();
        }

        return $this->render(
            sprintf('Engage360dTakedaBundle:PressCenter:article_%s.html.twig', $articleType),
            [
                "article" => $article,
                "recentArticles" => $recentArticles,
            ]
        );
    }
}
