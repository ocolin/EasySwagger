<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger\Tests;

use Ocolin\EasyEnv\LoadEnv;
use Ocolin\EasySwagger\Swagger;
use PHPUnit\Framework\TestCase;


class testNetbox extends TestCase
{

    public function testGet() : void
    {
        $client = new Swagger(
            api_file: __DIR__.  '/../testFiles/netbox.v.2.0.json',
            env_prefix: 'NETBOX',
        );

        $output = $client->path(
            path: '/ipam/ip-addresses/',
            data: [ 'address' => '63.249.70.25' ]
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
        new LoadEnv( files:__DIR__ . '/../testFiles/.env.netbox', append: true );
    }
}