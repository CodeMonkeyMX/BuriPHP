<?php

namespace Repositories;

use Libraries\BuriPHP\Database;
use Libraries\BuriPHP\Helpers\HelperDate;
use Libraries\BuriPHP\Helpers\HelperDateTime;
use Libraries\BuriPHP\Repository;

class Subscription extends Repository
{
    private $table = 'SUBSCRIPTION';
    private $object = [
        'SUBSCRIPTION.ID (SUBSCRIPTION_ID)',
        'SUBSCRIPTION.USER_ID [Int]',
        'SUBSCRIPTION.BILLING_PERIOD',
        'SUBSCRIPTION.PRICE [Number]',
        'SUBSCRIPTION.STATUS',
        'SUBSCRIPTION.DATA [Object]',
        'SUBSCRIPTION.NOTE',
        'SUBSCRIPTION.DATE_PAYMENT_UPDATE',
        'SUBSCRIPTION.DATE_PAYMENT_NEXT',
        'SUBSCRIPTION.METHOD_PAYMENT',
        'SUBSCRIPTION.DATE_CREATED',
    ];

    /**
     * Create subscription
     */
    public function create($data)
    {
        $data['DATE_CREATED'] = HelperDateTime::getNowTimezone();
        $this->database->insert($this->table, Database::camelToSnake($data));

        return $this->database->id();
    }

    /**
     * Read subscription
     * 
     * @param array $args options availble id(int), object(array), where(array)
     */
    public function read(...$args)
    {
        $response = [];
        $object = $this->object;
        $where = [];

        $object[] = "USER.EMAIL";
        $object[] = "USER.NAME";
        $object[] = "USER.USERNAME";

        if (isset($args['object']) && is_array($args['object'])) {
            foreach ($args['object'] as $value) {
                $object[] = "{$this->table}." . Database::camelToSnake($value);
            }
        }

        if (isset($args['where']) && is_array($args['where'])) {
            foreach ($args['where'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $_key => $_value) {
                        if (!in_array(strtoupper($key), ["OR", "AND", "ORDER", "MATCH", "LIMIT", "GROUP", "HAVING"])) {
                            $key = "{$this->table}." . Database::camelToSnake($key);
                        } else {
                            $key = Database::camelToSnake($key);
                        }

                        $where[$key]["{$this->table}." . Database::camelToSnake($_key)] = $_value;
                    }
                } else {
                    if (!in_array(strtoupper($key), ["OR", "AND", "ORDER", "MATCH", "LIMIT", "GROUP", "HAVING"])) {
                        $where["{$this->table}." . Database::camelToSnake($key)] = $value;
                    } else {
                        $where[Database::camelToSnake($key)] = $value;
                    }
                }
            }
        }

        if (isset($args['id'])) {
            $where['ID'] = (int) $args['id'];
        }

        $this->database->select($this->table, [
            "[>]USER" => [
                "USER_ID" => "ID"
            ]
        ], $object, $where, function ($data) use (&$response) {
            $response[] = $data;
        });

        if (isset($args['id'])) {
            return (isset($response[0])) ? Database::snakeToCamel($response)[0] : [];
        } else {
            return Database::snakeToCamel($response);
        }
    }

    /**
     * Update subscription
     * 
     * @param array $data
     * @param string $whereValue
     * @param string $whereRow default ID
     */
    public function update($data, $whereValue, $whereRow = "ID")
    {
        $response = $this->database->update($this->table, Database::camelToSnake($data), [
            Database::camelToSnake($whereRow) => $whereValue
        ]);

        return $response->rowCount();
    }

    /**
     * Delete subscription
     * 
     * @param string $whereValue
     * @param string $whereRow default ID
     */
    public function delete($whereValue, $whereRow = "ID")
    {
        $response = $this->database->delete($this->table, [
            Database::camelToSnake($whereRow) => $whereValue
        ]);

        return $response->rowCount();
    }
}
