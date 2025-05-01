<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Query;
use Psr\Http\Message\ResponseInterface;

class HTTP
{
    /**
     * @var Client Guzzle HTTP Client.
     */
    private Client $client;

    /**
     * @var string|null Common URL used in all queries.
     */
    private ?string $base_uri;

    /**
     * @var array<string,string|int|float> HTTP Headers.
     */
    private array $headers;

    /**
     * @var Auth Object containing auth daya.
     */
    private Auth $auth;



/* CONSTRUCTOR
---------------------------------------------------------------------------- */

    /**
     * @param Auth $auth Authentication object.
     * @param Client|null $client Optional Guzzle pre-configured client.
     * @param string|null $base_uri URL of API.
     * @param bool $verify Verify SSL on connection.
     * @param bool $errors Report errors.
     */

    public function __construct(
           Auth $auth,
        ?Client $client     = null,
        ?string $base_uri   = null,
           bool $verify     = false,
           bool $errors     = false,

    )
    {
        $this->auth     = $auth;
        $this->base_uri = $base_uri;
        $this->headers  = $this->default_Headers();

        if( $client !== null ) {
            $this->client = $client;
            return;
        }

        if( $this->auth->method === 'basicauth' ) {
            $this->client = new Client([
                'base_uri'        => $this->base_uri,
                'verify'          => $verify,
                'http_errors'     => $errors,
                'auth'             => [ $this->auth->username, $this->auth->password ],
                'timeout'         => 20,
                'connect_timeout' => 20
            ]);
        }
        else{
            $this->headers[ $this->auth->token_header ] = $this->auth->token;
            $this->client = new Client([
                'base_uri'        => $this->base_uri,
                'verify'          => $verify,
                'http_errors'     => $errors,
                'timeout'         => 20,
                'connect_timeout' => 20
            ]);
        }
    }



/* CALL
---------------------------------------------------------------------------- */

    /**
     * Make an HTTP call to the Swagger based API server
     *
     * @param string $method HTTP method to call.
     * @param string $uri HTTP URI of API method.
     * @param object|array<string,mixed>|null $body API query payload.
     * @param array<string,mixed>|null $query URI parameters.
     * @param array<string,string|int|float> $headers Optional HTTP headers.
     * @return Data Object with API response data.
     * @throws GuzzleException
     */

    public function call(
                   string $method,
                   string $uri,
        object|array|null $body = null,
                   ?array $query = null,
                    array $headers = []
    ) : Data
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
            return new Data(
                status: 520,
                status_message: "Client: " . $e->getMessage(),
                body: $e->getTraceAsString()
            );
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
            'Content-type'    => 'application/json; charset=utf-8',
            'User-Agent'      => 'EasySwagger Client 2.0',
        ];
    }



/* RETURN HTTP RESPONSE RESULTS
---------------------------------------------------------------------------- */

    /**
     * @param ResponseInterface $request Guzzle Request object.
     * @return Data API data object
     */
    private static function return_Results( ResponseInterface $request ) : object
    {
        $output = new Data(
            status: $request->getStatusCode(),
            status_message: $request->getReasonPhrase(),
            headers: $request->getHeaders(),
            body: $request->getBody()->getContents()
        );

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