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
}
