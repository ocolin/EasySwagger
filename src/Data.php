<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger;

class Data
{
    /**
     * @param int $status
     * @param string $status_message
     * @param array<string,string|string[]> $headers
     * @param mixed|null $body
     */
    public function __construct(
           public int $status,
        public string $status_message,
         public array $headers = [],
         public mixed $body = null,
    )
    {}
}