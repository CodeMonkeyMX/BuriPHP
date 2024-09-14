<?php

namespace Controllers;

use Libraries\BuriPHP\Controller;
use Libraries\Responses;

class Subscription extends Controller
{
    public function create()
    {
        $request = $this->getPayload();

        $response = $this->service->create($request);

        return Responses::response($response[0], $response[1]);
    }

    public function list()
    {
        $response = $this->service->getSubscriptions();

        return Responses::response($response[0], $response[1]);
    }

    public function get()
    {
        $user = $this->getParams()['user'];
        $by = isset($this->getGet()['by']) ? $this->getGet()['by'] : 'id';

        $response = $this->service->getSubscription($user, $by);

        return Responses::response($response[0], $response[1]);
    }

    public function update()
    {
        $user = $this->getParams()['user'];
        $by = isset($this->getGet()['by']) ? $this->getGet()['by'] : 'id';

        $response = $this->service->update($this->getPayload(), $user, $by);

        return Responses::response($response[0], $response[1]);
    }

    public function delete()
    {
        $user = $this->getParams()['user'];
        $by = isset($this->getGet()['by']) ? $this->getGet()['by'] : 'id';

        $response = $this->service->delete($user, $by);

        return Responses::response($response[0], $response[1]);
    }
}
