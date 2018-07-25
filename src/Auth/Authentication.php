<?php

namespace OneOffTech\GeoServer\Auth;

use Psr\Http\Message\RequestInterface;
use Http\Message\Authentication\BasicAuth;
use OneOffTech\GeoServer\Contracts\Authentication as AuthenticationContract;

final class Authentication implements AuthenticationContract
{
    /**
     * @var Http\Message\Authentication\BasicAuth
     */
    private $auth;

    /**
     * @param string $app_secret
     * @param string $app_url
     */
    public function __construct($username, $password)
    {
        $this->auth = new BasicAuth($username, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(RequestInterface $request)
    {
        return $this->auth->authenticate($request);
    }
}
