<?php

namespace App\Controllers;

class BaseController 
{
    protected function filterParams($request, $response, $requestData, array $allowedInput)
    {
        if (!is_array($requestData)) {
            $response->getBody()->write('Invalid form data');
            return $response->withStatus(400);
        }

        foreach ($requestData as $key => $value) {
            if (!in_array($key, $allowedInput, true)) {
                $response->getBody()->write('Error');
                return $response;
            }
        }

        return null;
    }
}