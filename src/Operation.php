<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger;

use Ocolin\EasySwagger\Errors\InvalidMethodException;

/**
 * Describes a single API operation on a path.
 */

class Operation extends File
{
    /**
     * @var array<string> A list of tags for API documentation control.
     * Tags can be used for logical grouping of operations by resources
     * or any other qualifier.
     */
    public array $tags;

    /**
     * @var string A short summary of what the operation does. For
     * maximum readability in the swagger-ui, this field SHOULD be
     * less than 120 characters.
     */
    public string $summary;

    /**
     * @var string A verbose explanation of the operation behavior.
     * GFM syntax can be used for rich text representation.
     */
    public string $description;

    /**
     * @var object Additional external documentation for this operation.
     */
    public object $externalDocs;

    /**
     * @var string Unique string used to identify the operation. The
     * id MUST be unique among all operations described in the API.
     * Tools and libraries MAY use the operationId to uniquely identify
     * an operation, therefore, it is recommended to follow common
     * programming naming conventions.
     */
    public string $operationId;

    /**
     * @var array<string> A list of MIME types the operation can consume.
     * This overrides the consumes definition at the Swagger Object.
     * An empty value MAY be used to clear the global definition.
     * Value MUST be as described under Mime Types.
     */
    public array $consumes;

    /**
     * @var array<string> A list of MIME types the operation can produce.
     * This overrides the produces definition at the Swagger Object.
     * An empty value MAY be used to clear the global definition. Value
     * MUST be as described under Mime Types.
     */
    public array $produces;

    /**
     * @var array<object> A list of parameters that are applicable for
     * this operation. If a parameter is already defined at the Path
     * Item, the new definition will override it, but can never remove
     * it. The list MUST NOT include duplicated parameters. A unique
     * parameter is defined by a combination of a name and location.
     * The list can use the Reference Object to link to parameters
     * that are defined at the Swagger Object's parameters. There can
     * be one "body" parameter at most.
     */
    public array $parameters = [];

    /**
     * @var object Required. The list of possible responses as they are
     * returned from executing this operation.
     */
    public object $responses;

    /**
     * @var array<string> The transfer protocol for the operation.
     * Values MUST be from the list: "http", "https", "ws", "wss".
     * The value overrides the Swagger Object schemes definition.
     */
    public array $schemes;

    /**
     * @var bool Declares this operation to be deprecated. Usage of
     * the declared operation should be refrained. Default value is
     * false.
     */
    public bool $deprecated;

    /**
     * @var array<object> A declaration of which security schemes are
     * applied for this operation. The list of values describes alternative
     * security schemes that can be used (that is, there is a logical
     * OR between the security requirements). This definition overrides
     * any declared top-level security. To remove a top-level security
     * declaration, an empty array can be used.
     */
    public array $security;

    public string $path;

/*
--------------------------------------------------------------------- */

    /**
     * @param string $path Path of endpoint.
     * @param string $method HTTP method.
     * @param object $file Swagger file data.
     * @throws InvalidMethodException
     */
    public function __construct( string $path, string $method, object $file )
    {
        if( !isset( $file->paths->$path->$method )) {
            throw new InvalidMethodException( message: "Operation for $method / $path not found.");
        }

        $raw = $file->paths->$path->$method;

        if( isset( $raw->tags )) { $this->tags = $raw->tags; }
        if( isset( $raw->summary )) { $this->summary = $raw->summary; }
        if( isset( $raw->description )) { $this->description = $raw->description; }
        if( isset( $raw->externalDocs )) { $this->externalDocs = $raw->externalDocs; }
        if( isset( $raw->operationId )) { $this->operationId = $raw->operationId; }
        if( isset( $raw->consumes )) { $this->consumes = $raw->consumes; }
        if( isset( $raw->produces )) { $this->produces = $raw->produces; }
        if( isset( $raw->parameters )) {
            $this->parameters = Parameter::get_Parameters( $raw->parameters );
        }
        if( isset( $raw->responses )) { $this->responses = $raw->responses; }
        if( isset( $raw->schemes )) { $this->schemes = $raw->schemes; }
        if( isset( $raw->deprecated )) { $this->deprecated = $raw->deprecated; }
        if( isset( $raw->security )) { $this->security = $raw->security; }
    }


/* BUILD PATH
--------------------------------------------------------------------- */

    /**
     * @param string $path
     * @param array<string,object> $parameters
     * @param array<string,mixed> $data
     * @return string
     */
    public static function build_Path(
        string $path,
         array $data
    ) : string
    {
        foreach( $data as $name => $value )
        {
            $path = str_replace(
                 search: '{' . $name .'}',
                replace: (string)$value,
                subject: $path
            );
        }

        return $path;
    }
}