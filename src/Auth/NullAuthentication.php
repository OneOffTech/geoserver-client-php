<?php

namespace OneOffTech\GeoServer\Auth;

use Psr\Http\Message\RequestInterface;
use OneOffTech\GeoServer\Contracts\Authentication as AuthenticationContract;

final class NullAuthentication implements AuthenticationContract
{

    /**
     * {@inheritdoc}
     */
    public function authenticate(RequestInterface $request)
    {
        return $request;
    }
}
