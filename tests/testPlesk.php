<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger\Tests;

use Ocolin\EasyEnv\LoadEnv;
use Ocolin\EasySwagger\Swagger;
use PHPUnit\Framework\TestCase;


class testPlesk extends TestCase
{

    public function testGet() : void
    {
        $client = new Swagger(
            api_file: __DIR__.  '/../testFiles/plesk.v2.json',
            env_prefix: 'PLESK',
        );

        $output = $client->path(
            path: '/clients',
        );

        $this->assertIsObject( actual: $output );
        $this->assertObjectHasProperty( propertyName: 'body', object: $output );
        $this->assertObjectHasProperty( propertyName: 'headers', object: $output );
        $this->assertObjectHasProperty( propertyName: 'status', object: $output );
        $this->assertObjectHasProperty( propertyName: 'status_message', object: $output );
        $this->assertIsArray( actual: $output->headers );
        $this->assertIsInt( actual: $output->status );
        $this->assertIsString( actual: $output->status_message );
        $this->assertIsArray( actual: $output->body );
    }


    public static function setUpBeforeClass(): void
    {
        new LoadEnv( files: __DIR__ . '/../testFiles/.env.plesk', append: true );
    }
}