<?php

namespace Services;

use BuriPHP\Settings;
use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperDate;
use Libraries\BuriPHP\Helpers\HelperValidate;
use Libraries\BuriPHP\Service;
use Libraries\Functions;

class Subscription extends Service
{
    public $session;

    public function __init()
    {
        $this->session = Functions::getSession();
    }

    private function verifyLabels($request, $update = false)
    {
        $labels = [];

        if (!$update && (!isset($request['userId']) || HelperValidate::isEmpty($request['userId']) && !is_numeric($request['userId']))) {
            $labels = HelperArray::append($labels, [
                'label' => 'userId',
                'message' => 'Selecciona la cuenta que se le asignará la suscripción.'
            ]);
        }

        if (!$update && isset($request['userId']) && count($this->repository->read(where: ['userId' => $request['userId']])) >= 1) {
            $labels = HelperArray::append($labels, [
                'label' => 'userId',
                'message' => 'Ya existe una suscripción para esta cuenta.'
            ]);
        }

        if (
            !isset($request['billingPeriod']) ||
            HelperValidate::isEmpty($request['billingPeriod'])
        ) {
            $labels = HelperArray::append($labels, [
                'label' => 'billingPeriod',
                'message' => 'Se requiere un periodo de facturación.'
            ]);
        }

        if (!HelperValidate::isEmpty($labels)) {
            return ['labels_errors' => $labels];
        } else {
            return false;
        }
    }

    /**
     * Create subscription
     * 
     * @param array $request
     */
    final public function create($request)
    {
        $request['price'] = (is_null($request['price']) || $request['price'] <= 0) ? 0 : (float) $request['price'];
        $request['status'] = (isset($request['status']) && $request['status'] == "on") ? 'active' : 'disabled';

        $verifyLabels = $this->verifyLabels($request);

        if ($verifyLabels != false) {
            return [400, $verifyLabels];
        }

        $object = [
            "userId" => (int) $request['userId'],
            "note" => !empty($request['note']) ? $request['note'] : null,
            "price" => $request['price'],
            "billingPeriod" => $request['billingPeriod'],
            "status" => $request['status'],
            'datePaymentUpdate' => HelperDate::getCurrentDateWithTimezone(Settings::$timeZone, true),
            'datePaymentNext' => HelperDate::addMonthsToDate(HelperDate::getCurrentDateWithTimezone(Settings::$timeZone, true), 1)
        ];

        $id = $this->repository->create($object);

        if (!is_numeric($id) || $id <= 0) {
            return [502, ['message' => 'Error al registrar el usuario.']];
        }

        return [200, ['content' => $this->getSubscription($request['userId'])[1]['content'], 'redirect' => '/dashboard/subscriptions']];
    }

    /**
     * Get list subscriptions
     */
    final public function getSubscriptions()
    {
        return [200, ['content' => $this->repository->read(where: ['order' => ['id' => 'DESC']])]];
    }

    /**
     * Get subscription by id default
     * 
     * @param int|string $user
     * @param string $by options available id, username, email
     */
    final public function getSubscription($user, $by = 'id')
    {
        $userService = $this->serviceShared('User', 'User');
        $user = $userService->getUser($user, $by)[1]['content'];

        $response = $this->repository->read(where: ['userId' => $user['userId']]);

        return [200, ['content' => isset($response[0]) ? $response[0] : $response]];
    }

    /**
     * Update subscription by id default
     * 
     * @param array $request
     * @param int|string $user
     * @param string $by options available id, username, email
     */
    final public function update($request, $user, $by)
    {
        $object = [];

        if (
            array_key_exists('note', $request) &&
            array_key_exists('price', $request) &&
            array_key_exists('billingPeriod', $request) &&
            array_key_exists('status', $request)
        ) {
            $verifyLabels = $this->verifyLabels($request, true);

            if ($verifyLabels != false) {
                return [400, $verifyLabels];
            }
        }

        if (array_key_exists('note', $request)) {
            $object['note'] = !empty($request['note']) ? $request['note'] : null;
        }

        if (array_key_exists('price', $request)) {
            $object['price'] = (is_null($request['price']) || $request['price'] <= 0) ? 0 : (float) $request['price'];
        }

        if (array_key_exists('billingPeriod', $request)) {
            $object['billingPeriod'] = $request['billingPeriod'];
        }

        if (array_key_exists('status', $request)) {
            $object['status'] = (isset($request['status']) && $request['status'] == "on" || $request['status'] == "active") ? 'active' : 'disabled';
        }

        $subscription = $this->getSubscription($user, $by)[1]['content'];

        $this->repository->update($object, $subscription['subscriptionId']);

        return [200, ['content' => $this->getSubscription($user, $by)[1]['content']]];
    }

    /**
     * Delete subscription by id default
     * 
     * @param int|string $user
     * @param string $by options available id, username, email
     */
    final public function delete($user, $by)
    {
        $subscription = $this->getSubscription($user, $by)[1]['content'];

        if (count($subscription) <= 0) {
            return [400, ['message' => 'La suscripción no existe.']];
        }

        $this->repository->delete($subscription['subscriptionId']);

        return [200, []];
    }
}
