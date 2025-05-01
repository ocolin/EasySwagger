<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger\Tests;

use Ocolin\Env\EasyEnv;
use Ocolin\EasySwagger\Swagger;
use PHPUnit\Framework\TestCase;


class testCalix extends TestCase
{

    public function testGet() : void
    {
        // 877_E7_1
        $client = new Swagger(
            api_file: __DIR__.  '/../testFiles/calix.ap.v1.json',
            env_prefix: 'CALIX',
        );

        $output = $client->path(
            path: '/config/device',
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
        EasyEnv::loadEnv( path:__DIR__ . '/../testFiles/.env.calix', append: true );
    }
}