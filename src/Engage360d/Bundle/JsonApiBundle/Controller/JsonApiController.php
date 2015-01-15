<?php

namespace Engage360d\Bundle\JsonApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonApiController extends Controller
{
    protected function isContentTypeValid(Request $request)
    {
        return empty($request->request) || $request->headers->get('content-type') === 'application/vnd.api+json';
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
}
