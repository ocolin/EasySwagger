<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger;

/**
 *  Class for storing API Auth information.
 */
class Auth
{
    /**
     * @param string $method Method of authentication. Token or basicauth.
     * @param string $token Token user for token based authentication.
     * @param string $token_header HTTP header name for auth token.
     * @param string|null $username Username for Basic Auth.
     * @param string|null $password Password for Basic Auth.
     */
    public function __construct(
        public  string $method = 'token',
        public  string $token = '',
        public  string $token_header = 'x-auth-token',
        public ?string $username = null,
        public ?string $password = null
    )
    {}
}