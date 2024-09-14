<?php

namespace Services;

use BuriPHP\Settings;
use Firebase\JWT\JWT;
use Libraries\BuriPHP\Helpers\HelperArray;
use Libraries\BuriPHP\Helpers\HelperCrypt;
use Libraries\BuriPHP\Helpers\HelperDate;
use Libraries\BuriPHP\Helpers\HelperServer;
use Libraries\BuriPHP\Helpers\HelperSession;
use Libraries\BuriPHP\Helpers\HelperString;
use Libraries\BuriPHP\Helpers\HelperValidate;
use Libraries\BuriPHP\Service;

class Login extends Service
{
    private function verifyLabels($request)
    {
        $labels = [];

        if (
            !isset($request['email']) ||
            HelperValidate::isEmpty($request['email']) ||
            !HelperValidate::isEmail($request['email'])
        ) {
            $labels = HelperArray::append($labels, [
                'label' => 'email',
                'message' => 'Escribe el correo electrónico con el que te registraste.'
            ]);
        }

        if (
            !isset($request['password']) ||
            HelperValidate::isEmpty($request['password']) ||
            strlen($request['password']) < 8
        ) {
            $labels = HelperArray::append($labels, [
                'label' => 'password',
                'message' => 'La contraseña no es válida.'
            ]);
        }

        if (!HelperValidate::isEmpty($labels)) {
            return ['labels_errors' => $labels];
        } else {
            return false;
        }
    }

    final public function login($request)
    {
        $verifyLabels = $this->verifyLabels($request);

        if ($verifyLabels != false) {
            return [400, $verifyLabels];
        }

        $userService = $this->serviceShared('User', 'User');
        $user = $userService->getUser($request['email'], 'email', true)[1]['content'];

        if (count($user) <= 0) {
            return [400, [
                'labels_errors' => [
                    [
                        'label' => 'email',
                        'message' => 'Esté correo electrónico no está registrado.'
                    ]
                ]
            ]];
        }

        $password = explode(':', $user['password']);
        $checkPassword = (HelperCrypt::createHash('sha1', $request['password'] . $password[1]) === $password[0]) ? true : false;

        if (!$checkPassword) {
            return [400, [
                'labels_errors' => [
                    [
                        'label' => 'password',
                        'message' => 'La contraseña es incorrecta.'
                    ]
                ]
            ]];
        }

        $session = $this->setSession(
            userId: $user['userId'],
            email: $user['email'],
            name: $user['name'],
            username: $user['username'],
        );

        if (isset($request['redirect'])) {
            $session['redirect'] = base64_decode($request['redirect']);
        } else {
            // $session['redirect'] = '/user/' . $user['username'];
            $session['redirect'] = '/';
        }

        return [200, $session];
    }

    final public function setSession(...$arg)
    {
        $sessionVersion = '1.0';
        $timeNow = HelperDate::getCurrentDateWithTimezone(Settings::$timeZone, true);
        $timeExpiration = HelperDate::addMinutesToDate($timeNow, 4320); // 3 Días (1440(24hrs) * 3(Días) = 4320)
        $tokenId = HelperCrypt::createHash('sha1', HelperString::random(16));

        $user = [
            'userId' => (int) $arg['userId'],
            'email' => $arg['email'],
            'name' => $arg['name'],
            'username' => $arg['username'],
        ];

        $payload = [
            'iss' => HelperServer::getDomainHttp(),
            'aud' => HelperServer::getDomainHttp(),
            'nbf' => strtotime($timeNow),
            'iat' => strtotime($timeNow),
            'exp' => strtotime($timeExpiration),
            'jti' => $tokenId,
            'user' => $user,
            'sessionVersion' => $sessionVersion,
        ];

        $jwt = JWT::encode($payload, Settings::$secret, 'HS256');

        if (HelperValidate::ajaxRequest()) {
            HelperSession::setValue('authorization', $jwt);
        }

        return [
            'authorization' => [
                'type' => 'Bearer',
                'token' => $jwt
            ],
            'user' => $user
        ];
    }
}
