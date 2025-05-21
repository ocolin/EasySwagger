<?php

declare( strict_types = 1 );


namespace Ocolin\EasySwagger\Tests;

use Ocolin\EasyEnv\LoadEnv;
use Ocolin\EasySwagger\Swagger;
use PHPUnit\Framework\TestCase;

error_reporting(E_ALL);

class testUISP extends TestCase
{
    public function testGet() : void
    {
        $client = new Swagger(
            api_file: __DIR__.  '/../testFiles/uisp.v2.1.json',
            env_prefix: 'UISP',
        );

        $output = $client->path(
              path: '/devices',
            method: 'GET',
              data: [ 'type' => 'olt' ]
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
        new LoadEnv( files:__DIR__ . '/../testFiles/.env.uisp', append: true );
    }
}