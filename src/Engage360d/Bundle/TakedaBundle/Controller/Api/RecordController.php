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
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $catalogId = $request->query->get('catalogId');

        $repository = $this->get('doctrine')->getRepository(Record::REPOSITORY);
        if ($catalogId) {
          $records = $repository->findByCatalog($catalogId);
        } else {
          $records = $repository->findAll();
        }

        return [
            "data" => array_map([$this, 'getRecordArray'], $records),
        ];
    }

    /**
     * @Route("/records/{id}", name="api_get_records_one", methods="GET")
     */
    public function getRecordAction(Request $request, $id)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $repository = $this->get('doctrine')->getRepository(Record::REPOSITORY);
        $record = $repository->findOneById($id);

        if (!$record) {
            throw $this->createNotFoundException();
        }

        return [
            "data" => $this->getRecordArray($record),
        ];
    }
}
