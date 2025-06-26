<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger;

use Exception;
use Ocolin\EasySwagger\Errors\InvalidMethodException;
use Ocolin\EasySwagger\Errors\MissingPropsException;
use Ocolin\EasyEnv\LoadEnv;
use stdClass;
use GuzzleHttp\Exception\GuzzleException;
use Ocolin\EasySwagger\Errors\MissingJsonException;
use Ocolin\EasySwagger\Errors\LoadEnvException;
use Ocolin\EasySwagger\Errors\InvalidJsonException;


class Swagger
{
    /**
     * @var object Swagger data file.
     */
    public object $file;

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
     * @var array<string,string|object> Data for HTTP URI
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
     * @param string|null $base_uri Base URI of the API path.
     * @param string|null $api_file Path/name of Swagger json file.
     * @param string|null $env_file Path/name of environment file.
     * @param string|null $token    API token.
     * @param string|null $token_name Name of header to use for auth token.
     * @param string|null $auth_method Whether to use 'token' or 'basicauth' Auth method.
     * @param bool|null $standalone Use library as standalone instead of plugin.
     * @throws Exception
     */
    public function __construct(
        ?string $host        = null,
        ?string $base_uri    = null,
        ?string $api_file    = null,
        ?string $env_file    = null,
        ?string $token       = null,
        ?string $token_name  = null,
        ?string $auth_method = null,
        ?string $username    = null,
        ?string $password    = null,
         string $env_prefix  = 'SWAGGER',
          ?bool $standalone  = false,
            int $timeout     = 20
    )
    {
        if( $standalone === true ) {
            $this->load_ENV( $env_file );
        }

        $this->file = $this->load_JSON( file_name: $api_file );
        $host        = $host        ?? $_ENV[ $env_prefix . '_HOST' ] ?? null;
        $base_uri    = $base_uri    ?? $_ENV[ $env_prefix . '_BASE_URI' ] ?? null;
        $token       = $token       ?? $_ENV[ $env_prefix . '_TOKEN' ] ?? '';
        $username    = $username    ?? $_ENV[ $env_prefix . '_USERNAME' ] ?? '';
        $password    = $password    ?? $_ENV[ $env_prefix . '_PASSWORD' ] ?? '';
        $auth_method = $auth_method ?? $_ENV[ $env_prefix . '_AUTH_METHOD' ] ?? 'token';
        $token_name  = $token_name  ??
            $_ENV[ $env_prefix . '_TOKEN_NAME' ] ?? 'x-auth-token';

        if( $host === null ) {
            throw new MissingPropsException( message: 'Environment Prefix may be missing' );
        }

        $this->body = new stdClass();

        $auth = new Auth(
                  method: $auth_method,
                   token: $token,
            token_header: $token_name,
                username: $username,
                password: $password
        );

        $this->http = new HTTP(
                  auth: $auth,
              base_uri: $host . $base_uri,
               timeout: $timeout
        );
    }



/* QUERY BY API PATH
--------------------------------------------------------------------- */

    /**
     * @param string $path Swagger operation path (copy/paste from docs)
     * @param string $method HTTP method.
     * @param array<string,mixed> $data Optional data parameters.
     * @return object API output (headers, body, status code, status msg)
     * @throws GuzzleException
     * @throws InvalidMethodException
     */

    public function path(
         string $path,
         string $method = 'get',
          array $data   = []
    ) : object
    {
        $this->body   = new stdClass();
        $this->path   = $path;
        $this->query  = [];
        $this->method = strtolower( string: $method );
        $this->operation = new Operation(
              path: $this->path,
            method: $this->method,
              file: $this->file
        );

        $this->path = Operation::build_Path(
            path: $this->path,
            data: $data
        );

        if( !empty( $data ) ) {
            $this->sort_Input_Data( data: $data );
        }

        return $this->http->call(
            method: $this->method,
               uri: $this->path,
              body: $this->body,
             query: $this->query,
        );
    }



/* SORT THE INPUT ARRAY DATA INTO QUERY CATEGORIES
--------------------------------------------------------------------- */

    /**
     * Take the input parameters and assign them to Path, Query, or Body
     * based on the API operation specifics.
     *
     * @param array<string,object> $data input data for query.
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
     * @throws Exception
     */
    private function load_ENV( ?string $file_name ) : void
    {
        $file_name = $file_name ??  __DIR__ . '/../.env';
        try {
            new LoadEnv( files: $file_name, append: true);
        }
        catch( Exception $e ) {
            throw new LoadEnvException( message: $e->getMessage() );
        }
    }



/* LOAD JSON DATA
--------------------------------------------------------------------- */

    /**
     * @param string|null $file_name Swagger API file/path.
     * @return object JSON data.
     * @throws Exception
     */
    private function load_JSON( ?string $file_name ) : object
    {
        $file_name = $file_name ?? $_ENV['SWAGGER_FILE'];
        $contents = @file_get_contents( filename: $file_name );

        if( $contents === false ) {
            throw new MissingJsonException( message: "Client: Missing JSON file - {$file_name}" );
        }

        $json = json_decode( json: $contents );
        if( $json === null || $json === false ) {
            throw new InvalidJsonException( message: "Client: Failed to parse JSON string - {$file_name}" );
        }

        return (object)$json;
    }



/* HANDLE ERRORS
---------------------------------------------------------------------------- */

    /**
     * Temporary until ready to start handling errors.
     *
     * @param string $msg
     * @return Data
     */
    public static function error( string $msg ) : Data
    {
        return new Data(
            status: 520,
            status_message: $msg
        );
    }
}