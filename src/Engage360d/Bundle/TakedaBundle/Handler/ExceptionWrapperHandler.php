<?php

namespace Engage360d\Bundle\TakedaBundle\Handler;

use FOS\RestBundle\View\ExceptionWrapperHandlerInterface;

class ExceptionWrapperHandler implements ExceptionWrapperHandlerInterface
{
    public function wrap($data)
    {
        $exception = $data['exception'];

        $errors = [];

        // Check if exception text is json
        $messages = json_decode($exception->getMessage(), true);
        if (is_array($messages)) {
            foreach ($messages as $message) {
                if (isset($message["message"])) {
                    $errors[] = [
                        "code" => $exception->getStatusCode(),
                        "title" => $message["message"],
                    ];
                }
            }
        }

        if (empty($errors)) {
            $errors[] = [
                "code" => $exception->getStatusCode(),
                "title" => $exception->getMessage(),
            ];
        }

        return ["errors" => $errors];
    }
}