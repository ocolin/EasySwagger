<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Ocolin\Env\EasyEnv;
use stdClass;

class Swagger
{
    /**
     * @var object Swagger data file.
     */
    public static object $file;

    /**
     * @var string URI of API host.
     */
    public string $host;

    /**
     * @var string URI path of API URL.
     */
    public string $path;

    /**
     * @var string HTTP method for API.
     */
    public string $method;

    /**
     * @var array Data for HTTP URI
     */
    public array $query = [];

    /**
     * @var object Data for HTTP body
     */
    public object $body;

    /**
     * @var Operation API Operation.
     */
    public Operation $operation;

    /**
     * @var HTTP HTTP client to call API with.
     */
    protected HTTP $http;


/* CONSTRUCTOR
---------------------------------------------------------------------------- */

    /**
     * @param string|null $host     Host name of API server.
     * @param string|null $api_file Path/name of Swagger json file.
     * @param string|null $base_uri Base URI of the API path.
     * @param string|null $env_file Path/name of environment file.
     * @param string|null $token    API token.
     * @param string|null $token_name Name of header to use for auth token.
     * @param bool|null $standalone Use library as standalone instead of plugin.
     * @throws Exception
     */
    public function __construct(
        ?string $host       = null,
        ?string $api_file   = null,
        ?string $base_uri   = null,
        ?string $env_file   = null,
        ?string $token      = null,
        ?string $token_name = null,
        ?bool $standalone   = false
    )
    {
        if( $standalone === true ) {
            $this->load_ENV( $env_file );
        }

        $host       = $host       ?? $_ENV['SWAGGER_HOST'];
        $base_uri   = $base_uri   ?? $_ENV['SWAGGER_BASE_URI'];
        $token      = $token      ?? $_ENV['SWAGGER_TOKEN'];
        $token_name = $token_name ?? 'x-auth-token';
        self::$file = $this->load_JSON( file_name: $api_file );
        $this->body = new stdClass();

        $this->http = new HTTP(
                 token: $token,
              base_uri: $host . $base_uri,
            token_name: $token_name,
        );
    }



/* QUERY BY API PATH
--------------------------------------------------------------------- */

    /**
     * @param string $path      Swagger operation path (copy/paste from docs)
     * @param string $method    HTTP method.
     * @param array $data       Optional data parameters.
     * @return object|array     API output
     * @throws GuzzleException
     */
    public function path(
         string $path,
         string $method = 'get',
          array $data   = []
    ) : object|array
    {
        $this->path   = $path;
        $this->query  = [];
        $this->method = strtolower( string: $method );
        $this->operation = new Operation(
              path: $this->path,
            method: $this->method
        );
        $this->path = Operation::build_Path(
                  path: $this->path,
            parameters: $this->operation->parameters,
                  data: $data
        );

        if( !empty( $data ) ) {
            $this->sort_Input_Data( data: $data );
        }

        try {
            return $this->http->call(
                method: $this->method,
                   uri: $this->path,
                  body: $this->body,
                 query: $this->query,
            )->body;
        }
        catch( GuzzleException $e ) {
            self::error( msg: $e->getMessage() );
            exit;
        }
    }



/* SORT THE INPUT ARRAY DATA INTO QUERY CATEGORIES
--------------------------------------------------------------------- */

    /**
     * Take the input parameters and assign them to Path, Query, or Body
     * based on the API operation specifics.
     *
     * @param array $data input data for query.
     * @return void
     */
    private function sort_Input_Data( array $data ) : void
    {
        foreach( $data as $name => $value )
        {
            $found = false;
            foreach( $this->operation->parameters as $param )
            {
                if( $name == $param->name AND $param->in == 'path' ) {
                    $found = true;
                    break;
                }

                if( $name == $param->name AND $param->in == 'query' ) {
                    $this->query[$name] = $value;
                    $found = true;
                    break;
                }

                if( str_starts_with( haystack: $name, needle: 'cf_' )) {
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



/* LOAD ENVIRONMENT VARIABLES
--------------------------------------------------------------------- */

    /**
     * @param string|null $file_name File/path with environment variables.
     * @return void
     */
    private function load_ENV( ?string $file_name ) : void
    {
        $file_name = $file_name ??  __DIR__ . '/../.env';
        try {
            EasyEnv::loadEnv( path: $file_name, append: true);
        } catch ( Exception $e) {
            self::error( msg: "Unable to load Environment variables: $file_name." );
            exit;
        }
    }



/* LOAD JSON DATA
--------------------------------------------------------------------- */

    /**
     * @param string|null $file_name Swagger API file/path.
     * @return object Swagger data object.
     */
    private function load_JSON( ?string $file_name ) : object
    {
        $file_name = $file_name ?? $_ENV['SWAGGER_FILE'];
        $contents = @file_get_contents( filename: $file_name );

        if( $contents === false ) {
            self::error( msg: "API JSON file '$file_name' not found." );
            exit;
        }

        $json = json_decode( json: $contents );
        if( $json === false ) {
            self::error( msg: "API is not valid JSON." );
            exit;
        }

        return (object)$json;
    }


/* HANDLE ERRORS
---------------------------------------------------------------------------- */

    /**
     * Temporary until ready to start handling errors.
     *
     * @param string $msg
     * @return void
     */
    public static function error( string $msg ) : void
    {
        fwrite( stream: STDERR, data: $msg . "\n" );
    }
}