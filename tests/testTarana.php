<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger\Tests;

use Ocolin\Env\EasyEnv;
use Ocolin\EasySwagger\Swagger;
use PHPUnit\Framework\TestCase;


class testTarana extends TestCase
{

    public function testGet() : void
    {
        $client = new Swagger(
            api_file: __DIR__.  '/../testFiles/tarana.v.3.0.1.json',
            env_prefix: 'TARANA',
        );

        $output = $client->path(
            path: '/v1/network/regions/{regionName}/markets/{marketName}/sites',
            data: [ 'regionName' => 'Cruzio Internet', 'marketName' => 'South Santa Cruz' ]
        );

        $this->assertIsObject( actual: $output );
        $this->assertObjectHasProperty( propertyName: 'body', object: $output );
        $this->assertObjectHasProperty( propertyName: 'headers', object: $output );
        $this->assertObjectHasProperty( propertyName: 'status', object: $output );
        $this->assertObjectHasProperty( propertyName: 'status_message', object: $output );
        $this->assertIsArray( actual: $output->headers );
        $this->assertIsInt( actual: $output->status );
        $this->assertIsString( actual: $output->status_message );
        $this->assertIsObject( actual: $output->body );
    }


    public static function setUpBeforeClass(): void
    {
        EasyEnv::loadEnv( path:__DIR__ . '/../testFiles/.env.tarana', append: true );
    }
}