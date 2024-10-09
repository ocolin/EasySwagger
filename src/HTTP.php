<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Query;
use Ocolin\Env\EasyEnv;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class HTTP
{
    /**
     * @var Client Guzzle HTTP Client
     */
    private Client $client;

    /**
     * @var string Common URL used in all queries
     */
    private string $base_uri;

    /**
     * @var array<string,string|int|float>
     */
    private array $headers;

    /**
     * @var string Token used for API authentication
     */
    private string $token;

    /**
     * @var string Header name for auth token.
     */
    private string $token_name;


/* CONSTRUCTOR
---------------------------------------------------------------------------- */

    /**
     * @throws Exception
     */
    public function __construct(
         string $token,
        ?Client $client     = null,
        ?string $base_uri   = null,
         string $token_name = 'x-auth-token',
           bool $verify     = false,
           bool $errors     = false,

    )
    {
        $this->base_uri   = $base_uri;
        $this->token      = $token;
        $this->token_name = $token_name;
        $this->headers    = $this->default_Headers();
        $this->client     = $client ?? new Client([
            'base_uri'        => $this->base_uri,
            'verify'          => $verify,
            'http_errors'     => $errors,
            'timeout'         => 20,
            'connect_timeout' => 20
        ]);
    }



/* CALL
---------------------------------------------------------------------------- */

    /**
     * Make an HTTP call to the Swagger based API server
     *
     * @param string $method
     * @param string $uri
     * @param object|array|null $body
     * @param array<string,mixed>|null $query
     * @param array<string,string|int|float> $headers
     * @return object
     * @throws GuzzleException
     */

    public function call(
                   string $method,
                   string $uri,
        object|array|null $body = null,
                   ?array $query = null,
                    array $headers = []
    ) : object
    {
        $this->headers = array_merge( $this->headers, $headers );
        $options = [ 'headers' => $this->headers ];

        $uri = ltrim( string: $uri, characters: '/' );

        if( $query !== null ) { $options['query'] = Query::build( $query ); }
        if( !empty( (array)$body )) { $options['body']  = json_encode( $body ); }

        try {
            $request = $this->client->request(
                 method: $method,
                    uri: $uri,
                options: $options
            );
        }
        catch ( Exception $e ) {
            Swagger::error( $e->getMessage() );
            exit;
        }

        return self::return_Results( request: $request );
    }




/* CREATE DEFAULT REQUEST HEADERS
---------------------------------------------------------------------------- */

    /**
     * @return array<string, string|int|float>
     */
    private function default_Headers() : array
    {
        return [
            $this->token_name => $this->token,
            'Content-type'    => 'application/json; charset=utf-8',
            'User-Agent'      => 'API Client 1.0',
        ];
    }



/* RETURN HTTP RESPONSE RESULTS
---------------------------------------------------------------------------- */

    /**
     * @param ResponseInterface $request Guzzle Request object.
     * @return stdClass API data object
     */
    private static function return_Results( ResponseInterface $request ) : object
    {

        $output = new stdClass();
        $output->status = $request->getStatusCode();
        $output->headers = $request->getHeaders();
        $output->status_message = $request->getReasonPhrase();
        $output->body = $request->getBody()->getContents();

        if(
            isset( $output->headers['Content-Type'] ) AND
            str_contains(
                haystack: $output->headers['Content-Type'][0],
                needle: 'application/json'
            )
        ) {
            $output->body = json_decode( json: $output->body );
        }

        if( $output->body == null ) { $output->body = []; }

        return $output;
    }
}