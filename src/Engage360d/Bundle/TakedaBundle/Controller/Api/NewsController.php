<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\News;

class NewsController extends TakedaJsonApiController
{
    const URI_NEWS_ONE = '/api/v1/schemas/news/one.json';
    const URI_NEWS_LIST = '/api/v1/schemas/news/list.json';

    /**
     * @Route("/news", name="api_get_news", methods="GET")
     */
    public function getNewsAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        // TODO validate parameters?

//        $limit = $request->query->get("limit");
//        $page = $request->query->get("page");

        // strtolower(null) returns string(0) ""
        $onlyActive = strtolower($request->query->get("onlyActive")) === "true";
        $category = $request->query->get("category");
        $date = $request->query->get("date");

        $repository = $this->get('doctrine')->getRepository(News::REPOSITORY);

        if ($category) {
            $news = $repository->findAllByCategory($category, $onlyActive);
        } else if ($date) {
            $news = $repository->findAllByDate($date, $onlyActive);
        } else {
            $news = $repository->findAllForLast12Months($onlyActive);
        }

        $response = [
            "links" => $this->getNewsLink(),
            "data" => []
        ];

        foreach ($news as $article) {
            $response["data"][] = $this->getNewsArray($article);
        }

        // TODO put this check into tests?
        $validator = $this->getSchemaValidatior(self::URI_NEWS_LIST, (object) $response);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 500);
        }

        return $response;
    }

    /**
     * @Route("/news/{id}", name="api_get_news_one", methods="GET")
     */
    public function getNewsOneAction(Request $request, $id)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $repository = $this->get('doctrine')->getRepository(News::REPOSITORY);
        $article = $repository->findOneById($id);

        if (!$article) {
            return $this->getErrorResponse(sprintf("News article with id = %s not found", $id), 404);
        }

        $response = [
            "links" => $this->getNewsLink(),
            "data" => $this->getNewsArray($article),
            "linked" => [
                "records" => [
                    $this->getRecordArray($article->getCategory())
                ]
            ]
        ];

        $validator = $this->getSchemaValidatior(self::URI_NEWS_ONE, (object) $response);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 500);
        }

        return $response;
    }
}