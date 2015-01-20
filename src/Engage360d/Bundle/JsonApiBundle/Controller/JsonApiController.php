<?php

namespace Engage360d\Bundle\JsonApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonApiController extends Controller
{
    const CONTENT_TYPE = 'application/vnd.api+json';

    protected function isContentTypeValid(Request $request)
    {
        return $request->request->count() === 0 || $request->headers->get('content-type') === self::CONTENT_TYPE;
    }

    protected function getErrorResponse($data, $code)
    {
        if (is_string($data)) {
            $data = [["message" => $data]];
        }

        $errors  = [];
        foreach($data as $error) {
            $errors[] = [
                "code" => $code,
                "message" => $error['message'],
            ];
        }

        return new JsonResponse(["errors" => $errors], $code);
    }

    protected function getInvalidContentTypeResponse()
    {
        return $this->getErrorResponse(sprintf("The expected content type is \"%s\"", self::CONTENT_TYPE), 400);
    }

    protected function populateEntity($entity, $data, $mappings)
    {
        // TODO check that $entity is doctrine entity

        $links = [];
        if (isset($data->data->links)) {
            $links = $data->data->links;
            unset($data->data->links);
        }

        foreach ($data->data as $property => $value) {
            $method = 'set' . ucfirst($property);
            $entity->$method($value);
        }

        foreach ($links as $property => $value) {
            if (!isset($mappings[$property])) {
                continue;
            }

            $mappedEntity = $this->get('doctrine')
                ->getRepository($mappings[$property])
                ->findOneById($value);

            if (!$mappedEntity) {
                continue;
            }

            $method = 'set' . ucfirst($property);
            if (method_exists($entity, $method)) {
                $entity->$method($mappedEntity);
            }

            $method = 'add' . ucfirst($property);
            if (method_exists($entity, $method)) {
                $entity->$method($mappedEntity);
            }
        }

        return $entity;
    }
}
