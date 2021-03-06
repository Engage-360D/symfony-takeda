<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Melodia\CatalogBundle\Entity\Record;

class RecordController extends TakedaJsonApiController
{
    const URI_RECORDS_ONE =  'v1/schemas/records/one.json';
    const URI_RECORDS_LIST = 'v1/schemas/records/list.json';

    /**
     * @Route("/records", name="api_get_records", methods="GET")
     */
    public function getRecordsAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $catalogId = $request->query->get('catalogId');

        $repository = $this->get('doctrine')->getRepository(Record::REPOSITORY);
        if ($catalogId) {
          $records = $repository->findByCatalog($catalogId);
        } else {
          $records = $repository->findAll();
        }

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return $jsonApiResponse->getRecordListResource($records);
    }

    /**
     * @Route("/records/{id}", name="api_get_records_one", methods="GET")
     */
    public function getRecordAction(Request $request, $id)
    {
        $this->assertContentTypeIsValid($request);

        $repository = $this->get('doctrine')->getRepository(Record::REPOSITORY);
        $record = $repository->findOneById($id);

        if (!$record) {
            throw $this->createNotFoundException();
        }

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return $jsonApiResponse->getRecordResource($record);
    }
}
