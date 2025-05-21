<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger\Tests;

use Ocolin\EasyEnv\Errors\EasyEnvFileHandleError;
use Ocolin\EasySwagger\Errors\InvalidMethodException;
use Ocolin\EasyEnv\LoadEnv;
use Ocolin\EasySwagger\Swagger;
use PHPUnit\Framework\TestCase;
use Ocolin\EasySwagger\Errors\LoadEnvException;
use Ocolin\EasySwagger\Errors\MissingJsonException;
use Ocolin\EasySwagger\Errors\InvalidJsonException;
use Ocolin\EasySwagger\Errors\MissingPropsException;

class testBad extends TestCase
{
    public function testBadEnv() : void
    {
        $this->expectException( LoadEnvException::class );
        $client = new Swagger(
            env_file: 'kjhsdjfhsd.env',
            standalone: true,
        );
    }

    public function testBadJsonFile() : void
    {
        $this->expectException( MissingJsonException::class );
        $client = new Swagger( api_file: 'kjhsdjfhsd' );
    }



    public function testBadJsonData() : void
    {
        $this->expectException( InvalidJsonException::class );
        $client = new Swagger(
            api_file: __DIR__.  '/../testFiles/empty.json',
        );
    }

    public function testBadEnvPrefix() : void
    {
        $this->expectException( MissingPropsException::class );
        $client = new Swagger(
            api_file: __DIR__.  '/../testFiles/uisp.v2.1.json',
            env_prefix: 'asdfsd'
        );
    }

    public function testBadHttpRequest() : void
    {
        $this->expectException( InvalidMethodException::class );
        $client = new Swagger(
            host: 'http://localhost',
            base_uri: '/asdfs/',
            api_file: __DIR__.  '/../testFiles/uisp.v2.1.json',
            env_prefix: 'UISP'
        );

        $output = $client->path(
            path: 'asdfsd',
            method: 'GET'
        );
    }


    public static function setUpBeforeClass(): void
    {
        new LoadEnv( files:__DIR__ . '/../testFiles/.env.tarana', append: true );
    }
}