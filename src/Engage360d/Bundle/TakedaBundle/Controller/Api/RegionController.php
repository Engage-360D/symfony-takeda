<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;
use Engage360d\Bundle\TakedaBundle\Entity\Region\Region;

class RegionController extends TakedaJsonApiController
{
    const URI_REGIONS_ONE =  'v1/schemas/regions/one.json';
    const URI_REGIONS_LIST = 'v1/schemas/regions/list.json';
    const URI_REGIONS_POST = 'v1/schemas/regions/post.json';
    const URI_REGIONS_PUT =  'v1/schemas/regions/put.json';

    /**
     * @Route("/regions", name="api_get_regions", methods="GET")
     */
    public function getRegionsAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $limit = $request->query->get("limit");
        $page = $request->query->get("page");

        $repository = $this->get('doctrine')->getRepository(Region::REPOSITORY);

        if ($page && $limit) {
            $regions = $repository->findSubset($page, $limit);
        } else {
            $regions = $repository->findAll();
        }

        return [
            "data" => array_map([$this, 'getRegionArray'], $regions)
        ];
    }

    /**
     * @Route("/regions/{id}", name="api_get_one_region", methods="GET")
     */
    public function getRegionAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $region = $this->get('doctrine')->getRepository(Region::REPOSITORY)
            ->findOneById($id);

        if (!$region) {
            throw $this->createNotFoundException();
        }

        return [
            "data" => $this->getRegionArray($region)
        ];
    }

    /**
     * @Route("/regions", name="api_post_region", methods="POST")
     */
    public function postRegionAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_REGIONS_POST);

        $region = $this->populateEntity(new Region(), $data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($region);
        $em->flush();

        return new JsonResponse(["data" => $this->getRegionArray($region)], 201);
    }

    /**
     * @Route("/regions/{id}", name="api_put_region", methods="PUT")
     */
    public function putRegionAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->getDoctrine()->getRepository(Region::REPOSITORY);
        $region = $repository->findOneById($id);

        if (!$region) {
            throw $this->createNotFoundException();
        }

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_REGIONS_PUT);

        $region = $this->populateEntity($region, $data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($region);
        $em->flush();

        return new JsonResponse(["data" => $this->getRegionArray($region)], 200);
    }

    /**
     * @Route("/regions/{id}", name="api_delete_region", methods="DELETE")
     */
    public function deleteRegionAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->getDoctrine()->getRepository(Region::REPOSITORY);
        $region = $repository->findOneById($id);

        if (!$region) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($region);
        $em->flush();

        return new \stdClass();
    }
}