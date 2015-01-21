<?php

namespace Engage360d\Bundle\TakedaBundle\Handler;

use FOS\RestBundle\View\ExceptionWrapperHandlerInterface;

class ExceptionWrapperHandler implements ExceptionWrapperHandlerInterface
{
    public function wrap($data)
    {
        $exception = $data['exception'];

        return ["errors" => [
            [
                "code" => $exception->getStatusCode(),
                "title" => $exception->getMessage()
            ]
        ]];
    }
}