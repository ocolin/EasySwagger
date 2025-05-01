<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger;

class Parameter extends File
{
    /**
     * @var string Required. The name of the parameter. Parameter names
     * are case-sensitive.
     *
     * If in is "path", the name field MUST correspond to the associated
     * path segment from the path field in the Paths Object. See Path
     * Templating for further information.
     *
     * For all other cases, the name corresponds to the parameter
     * name used based on the in property.
     */
    public readonly string $name;

    /**
     * @var string Required. The location of the parameter. Possible
     * values are "query", "header", "path", "formData" or "body".
     */
    public readonly string $in;

    /**
     * @var string A brief description of the parameter. This could
     * contain examples of use. GFM syntax can be used for rich
     * text representation.
     */
    public string $description;

    /**
     * @var bool Determines whether this parameter is mandatory. If
     * the parameter is in "path", this property is required and
     * its value MUST be true. Otherwise, the property MAY be
     * included and its default value is false.
     */
    public bool $required;

    /**
     * @var object Required. The schema defining the type used
     * for the body parameter.
     */
    public object $schema;

    /**
     * @var string Required. The type of the parameter. Since the
     * parameter is not located at the request body, it is limited to
     * simple types (that is, not an object). The value MUST be one of
     * "string", "number", "integer", "boolean", "array" or "file".
     * If type is "file", the consumes MUST be either "multipart/form-data",
     * " application/x-www-form-urlencoded" or both and the parameter
     * MUST be in "formData".
     */
    public string $type;

    /**
     * @var string The extending format for the previously mentioned
     * type. See Data Type Formats for further details.
     */
    public string $format;

    /**
     * @var bool Sets the ability to pass empty-valued parameters.
     * This is valid only for either query or formData parameters
     * and allows you to send a parameter with a name only or an
     * empty value. Default value is false.
     */
    public bool $allowEmptyValue;

    /**
     * @var object Required if type is "array". Describes the type of
     * items in the array.
     */
    public object $items;

    /**
     * @var string Determines the format of the array if type array
     * is used. Possible values are:
     *
     * csv - comma separated values foo,bar.
     * ssv - space separated values foo bar.
     * tsv - tab separated values foo\tbar.
     * pipes - pipe separated values foo|bar.
     * multi - corresponds to multiple parameter instances instead
     * of multiple values for a single instance foo=bar&foo=baz.
     * This is valid only for parameters in "query" or "formData".
     * Default value is csv.
     */
    public string $collectionFormat;

    /**
     * @var mixed Declares the value of the parameter that the server
     * will use if none is provided, for example a "count" to control
     * the number of results per page might default to 100 if not
     * supplied by the client in the request. (Note: "default" has no
     * meaning for required parameters.) See
     * https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-6.2.
     * Unlike JSON Schema this value MUST conform to the defined type
     * for this parameter.
     */
    public mixed $default;

    /**
     * @var int See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.2
     */
    public int $maximum;

    /**
     * @var bool See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.2
     */
    public bool $exclusiveMaximum;

    /**
     * @var int See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.3
     */
    public int $minimum;

    /**
     * @var bool See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.3
     */
    public bool $exclusiveMinimum;

    /**
     * @var int See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.2.1
     */
    public int $maxLength;

    /**
     * @var int See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.2.2
     */
    public int $minLength;

    /**
     * @var string See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.2.3
     */
    public string $pattern;

    /**
     * @var int See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.3.2
     */
    public int $maxItems;

    /**
     * @var int See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.3.3
     */
    public int $minItems;

    /**
     * @var bool See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.3.4
     */
    public bool $uniqueItems;

    /**
     * @var string[] See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.5.1
     */
    public array $enum;

    /**
     * @var int See https://tools.ietf.org/html/draft-fge-json-schema-validation-00#section-5.1.1
     */
    public int $multipleOf;


/*
--------------------------------------------------------------------- */

    public function __construct( object $input )
    {
        $this->name = $input->name;
        $this->in = $input->in;

        if( isset( $input->description )) { $this->description = $input->description; }
        if( isset( $input->required )) { $this->required = (bool)$input->required; }
        if( isset( $input->schema )) { $this->schema = $input->schema; }
        if( isset( $input->type )) { $this->type = $input->type; }
        if( isset( $input->format )) { $this->format = $input->format; }
        if( isset( $input->items )) { $this->items = $input->items; }
        if( isset( $input->collectionFormat )) { $this->collectionFormat = $input->collectionFormat; }
        if( isset( $input->default )) { $this->default = $input->default; }
        if( isset( $input->maximum )) { $this->maximum = $input->maximum; }
        if( isset( $input->exclusiveMaximum )) { $this->exclusiveMaximum = $input->exclusiveMaximum; }
        if( isset( $input->minimum )) { $this->minimum = $input->minimum; }
        if( isset( $input->exclusiveMinimum )) { $this->exclusiveMinimum = $input->exclusiveMinimum; }
        if( isset( $input->maxLength )) { $this->maxLength = $input->maxLength; }
        if( isset( $input->minLength )) { $this->minLength = $input->minLength; }
        if( isset( $input->pattern )) { $this->pattern = $input->pattern; }
        if( isset( $input->maxItems )) { $this->maxItems = $input->maxItems; }
        if( isset( $input->minItems )) { $this->minItems = $input->minItems; }
        if( isset( $input->uniqueItems )) { $this->uniqueItems = $input->uniqueItems; }
        if( isset( $input->enum )) { $this->enum = $input->enum; }
        if( isset( $input->multipleOf )) { $this->multipleOf = $input->multipleOf; }
    }



/*
--------------------------------------------------------------------- */

    /**
     * @param array<object> $params
     * @return array<Parameter>
     */
    public static function get_Parameters( array $params ) : array
    {
        $output = [];
        foreach( $params as $param )
        {
            $output[] = new Parameter( input: $param );
        }

        return $output;
    }
}