<?php

namespace Engage360d\Bundle\JsonApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;

class JsonApiController extends Controller
{
    const CONTENT_TYPE = 'application/vnd.api+json';

    protected function isContentTypeValid(Request $request)
    {
        return $request->request->count() === 0 || $request->headers->get('content-type') === self::CONTENT_TYPE;
    }

    protected function assertContentTypeIsValid($request)
    {
        if (!$this->isContentTypeValid($request)) {
            throw new HttpException(400, sprintf("The expected content type is \"%s\"", self::CONTENT_TYPE));
        }
    }

    protected function getData($request)
    {
        $json = $request->getContent();

        return json_decode($json);
    }

    protected function assertDataMatchesSchema($data, $schema)
    {
        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            sprintf(
                // TODO set path to api folder in bundle's configuration
                'file://%s/../web/api/%s',
                $this->get('kernel')->getRootDir(),
                $schema
            )
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            throw new HttpException(400, json_encode($validator->getErrors()));
        }
    }

    protected function assertEntityIsValid($entity)
    {
        $validator = $this->get('validator');
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            throw new HttpException(400, (string) $errors);
        }
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
                "title" => $error['message'],
            ];
        }

        return new JsonResponse(["errors" => $errors], $code);
    }

    // TODO remove
    protected function getInvalidContentTypeResponse()
    {
        return $this->getErrorResponse(sprintf("The expected content type is \"%s\"", self::CONTENT_TYPE), 400);
    }

    protected function populateEntity($entity, $data, $mappings = [])
    {
        // TODO check that $entity is doctrine entity
        // TODO use explicit & before $entity

        $links = [];
        if (isset($data->data->links)) {
            $links = $data->data->links;
            unset($data->data->links);
        }

        foreach ($data->data as $property => $value) {
            $method = 'set' . ucfirst($property);
            if (method_exists($entity, $method)) {
                $entity->$method($value);
            }
        }

        foreach ($links as $property => $value) {
            if (!isset($mappings[$property])) {
                throw new NotFoundHttpException(sprintf("Link with name '%s' not found", $property));
            }

            $ids = is_string($value) ? [$value] : $value;
            foreach ($ids as $id) {
                $mappedEntity = $this->get('doctrine')
                    ->getRepository($mappings[$property])
                    ->findOneById($id);

                if (!$mappedEntity) {
                    throw new NotFoundHttpException(sprintf("Link '%s' with id '%s' not found", $property, $id));
                }

                $method = 'set' . ucfirst($property);
                if (method_exists($entity, $method)) {
                    $entity->$method($mappedEntity);
                }

                $method = 'add' . ucfirst(preg_replace("/s$/", "", $property));
                if (method_exists($entity, $method)) {
                    $entity->$method($mappedEntity);
                }
            }
        }

        return $entity;
    }

    protected function getBaseUrl()
    {
        return $this->container->getParameter('api.base_url');
    }
}
