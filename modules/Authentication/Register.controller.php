<?php

namespace Controllers;

use Libraries\BuriPHP\Controller;
use Libraries\Responses;

class Register extends Controller
{
    public function register()
    {
        $request = $this->getPayload();

        if (isset($this->getGet()['redirect']) && !empty($this->getGet()['redirect'])) {
            $request['redirect'] = $this->getGet()['redirect'];
        }

        $response = $this->service->register($request);

        return Responses::response($response[0], $response[1]);
    }
}
