<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\News;
use Melodia\CatalogBundle\Entity\Record;

class NewsController extends TakedaJsonApiController
{
    const URI_NEWS_ONE =  'v1/schemas/news/one.json';
    const URI_NEWS_LIST = 'v1/schemas/news/list.json';
    const URI_NEWS_POST = 'v1/schemas/news/post.json';
    const URI_NEWS_PUT =  'v1/schemas/news/put.json';

    /**
     * @Route("/news", name="api_get_news", methods="GET")
     */
    public function getNewsAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

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

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return $jsonApiResponse->getNewsListResource($news);
    }

    /**
     * @Route("/news/{id}", name="api_get_news_one", methods="GET")
     */
    public function getNewsOneAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->get('doctrine')->getRepository(News::REPOSITORY);
        $newsArticle = $repository->findOneById($id);

        if (!$newsArticle) {
            throw $this->createNotFoundException();
        }

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return $jsonApiResponse->getNewsResource($newsArticle);
    }

    /**
     * @Route("/news", name="api_post_news", methods="POST")
     */
    public function postNewsAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_NEWS_POST);

        $newsArticle = $this->populateEntity(new News(), $data, ["category" => Record::REPOSITORY]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($newsArticle);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getNewsResource($newsArticle), 201);
    }

    /**
     * @Route("/news/{id}", name="api_put_news", methods="PUT")
     */
    public function putNewsAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->getDoctrine()->getRepository(News::REPOSITORY);
        $newsArticle = $repository->findOneById($id);

        if (!$newsArticle) {
            throw $this->createNotFoundException();
        }

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_NEWS_PUT);

        $newsArticle = $this->populateEntity($newsArticle, $data, ["category" => Record::REPOSITORY]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($newsArticle);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getNewsResource($newsArticle), 200);
    }

    /**
     * @Route("/news/{id}", name="api_delete_news", methods="DELETE")
     */
    public function deleteNewsAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->getDoctrine()->getRepository(News::REPOSITORY);
        $newsArticle = $repository->findOneById($id);

        if (!$newsArticle) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($newsArticle);
        $em->flush();

        return new \stdClass();
    }
}