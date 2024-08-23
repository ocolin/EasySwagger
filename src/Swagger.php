<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger;

use Exception;
use Ocolin\Env\EasyEnv;
use stdClass;

class Swagger extends File
{

    public readonly string $host;

    public string $path;

    public readonly string $method;

    public Operation $operation;

    public array $query = [];

    public object $body;

    protected HTTP $http;

/*
---------------------------------------------------------------------------- */

    public function __construct(
        ?string $host     = null,
        ?string $api_file = null,
        ?string $env_file = null
    )
    {
        if( $host === null OR $api_file === null ) {
            $this->load_ENV( file_name: $env_file );
        }
        $this->host = $host ?? $_ENV['UISP_API_HOST'];
        self::$file = $this->load_JSON( file_name: $api_file );
        $this->body = new stdClass();
        $this->http = new HTTP();
    }



/*
--------------------------------------------------------------------- */

    public static function call_By_Id(
         string $id,
        ?string $host     = null,
        ?string $env_file = null,
        ?string $api_file = null,
         ?array $data     = null
    ) : self
    {
        $swagger = new self(
                host: $host,
            api_file: $api_file,
            env_file: $env_file
        );
        $swagger->get_Path_From_Id( $id );

        return $swagger;
    }



/*
--------------------------------------------------------------------- */

    public static function call_By_Path(
         string $path,
         string $method,
        ?string $host     = null,
        ?string $env_file = null,
        ?string $api_file = null,
         ?array $data     = null
    ) : object
    {
        $swagger = new self(
                host: $host,
            api_file: $api_file,
            env_file: $env_file
        );
        $swagger->path = $path;
        $swagger->method = $method;
        $swagger->operation = new Operation(
              path: $swagger->path,
            method: $swagger->method
        );
        $swagger->path = Operation::build_Path(
            path: $swagger->path,
            parameters:  $swagger->operation->parameters,
            data: $data
        );

        if( $data !== null ) {
            $swagger->sort_Input_Data( data: $data );
        }

        return $swagger->http->call(
            method: $swagger->method,
            uri: $swagger->path,
            body: $swagger->body,
            query: $swagger->query
        );
    }



/*
--------------------------------------------------------------------- */

    public function get_Path_From_Id( string $id ) : void
    {
        foreach( self::$file->paths as $pname => $fpath )
        {
            foreach( $fpath as $mname => $method )
            {
                if( $method->operationId === $id ) {
                    $this->method = $mname;
                    $this->path = $pname;
                    return;
                }
            }
        }

        self::error( msg: "Operation '$id' was not found" );
    }


/*
--------------------------------------------------------------------- */

    private function sort_Input_Data( array $data ) : void
    {
        foreach( $data as $name => $value )
        {
            $found = false;
            foreach( $this->operation->parameters as $param )
            {
                if( $name == $param->name AND $param->in == 'query' ) {
                    $this->query[$name] = $value;
                    $found = true;
                    break;
                }
            }

            if( !$found ) {
                $this->body->$name = $value;
            }
        }
    }

/*
--------------------------------------------------------------------- */

    private function load_ENV( ?string $file_name = null ) : void
    {
        $file_name = $file_name ??  __DIR__ . '/../.env';
        try {
            EasyEnv::loadEnv( path: $file_name, append: true);
        } catch ( Exception $e) {
            self::error( msg: "Unable to load Environment variables: $file_name." );
        }
    }



/*
--------------------------------------------------------------------- */

    private function load_JSON( ?string $file_name = null ) : object
    {
        // SOME OF THIS IS SLOPPY. CLEAN UP LATER
        $file_name = $file_name ?? $_ENV['API_FILE'];
        $contents = @file_get_contents( filename: $file_name );

        if( $contents === false ) {
            self::error( msg: "API JSON file '$file_name' not found." );
            exit();
        }
        else {
            $json = json_decode( json: $contents );
            if( $json === false ) {
                self::error( msg: "API is not valid JSON." );
                exit();
            }
        }

        return (object)$json;
    }


}