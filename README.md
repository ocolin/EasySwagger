# EasySwagger

This is a library for creating quick REST clients for APIs using Swagger. 

# How it works

You will need to point to a Swagger JSON file containing the Swagger rules, properties, etc. You also need to provide the URI information as well as authentication credentials.

Once you have these, you can past the end point URI into the path, as well as any parameters needed to make your request. Any URL query parameters will be replaced with properties in your data object or array.

## Constructor properties

- $host - The API server name. Example: https://api.server.com
- $api_file - Path to your Swagger JSON config file. Example: __DIR __ . '/api.v1.json'
- $base_uri - Directory path to the REST API root on server. Example: /api/v1/.
- $env_file - File path to your environment file. This is if you want to use environment variables instead of constructor arguments.
- $token - Your authentication token if your API uses a token in the HTTP header.
- $token_name - The name of the HTTP header to use for your auth token if your API uses them.
- $auth_method - Method of authentication. 'token' for HTTP header tokens, or 'basicauth' for using HTTP Basic Auth.
- $username - Username for HTTP Basic Auth
- $password - Password for HTTP Basic Auth
- $env_prefix - Because a program may use this library for multiple API servers at once, the different servers can be specified with different environment variable prefixes. See the section on Environment variables.
- $standalone - If you are using this library as a stand along program, this will check for a local .env file to load variables from.

## Environment variables

Instead of passing constructor arguments to this library, you can instead load them as environment variables. To handle working with multiple REST APIs, the variable names require a prefix to seperate them from each other. So for example you can have multiple host names in the same environment using different prefixes. In the examples we will use the prefix 'SWAGGER', but you can specify the prefix using 
the argument $env_prefix. 'SWAGGER' is then default prefix if you do not specify one. So if you only are using one API and don't need a prefix, you can name them similar to this example:

- SWAGGER_FILE - File path to your Swagger JSON config file. Example "/dir/api.v2.1.json".
- SWAGGER_HOST - The hostname of the REST server. Example: https://api.host.com.
- SWAGGER_BASE_URI - The Directory of the URI to the API root. Example:  /api/v1/.
- SWAGGER_TOKEN - The authentication token for the server. This is used for token based authentication. It is not used with Basic Auth.
- SWAGGER_TOKEN_NAME - The HTTP Header name to use for your token (or key). This is only used for token based auth. 
- SWAGGER_AUTH_METHOD - Specify if you are using 'token' based authentication, or 'basicauth' for HTTP Basic Auth style authentication.
- SWAGGER_USERNAME - The username for HTTP Basic Auth authentication.
- SWAGGER_PASSWORD - The password for HTTP Basic Auth authentication.

### Example of envrionment variables with a prefix and their defaults:

 - PREFIX_FILE - Required via env or constructor
 - PREFIX_HOST - Required via env or constructor
 - PREFIX_BASE_URI - Not needed for every API
 - PREFIX_AUTH_METHOD - Defaults to 'token' mode
 - PREFIX_TOKEN_NAME - Defaults to 'x-auth-token'

## Examples

### Usage Example using constructor arguments

```
$client = new Swagger(
    host: 'https://api.server.com',
    base_uri: '/api/v1/',
    api_file: __DIR__ . '/mySwagger.api.json',
    token: 'KJHsdhjkjh987sjskjh',
    token_name: 'X-Auth-Key',
);
```

### Usage example using environment for construction

```
// SET UP ENVIRONMENT VARIABLES
$_ENV['MYAPI_HOST'] = 'https://api.server.com';
$_ENV['MYAPI_BASE_URI'] = '/dir/to/api/';
$_ENV['MYAPI_FILE'] = /dir/nameOfSwaggerFile.jon';
$_ENV['MYAPI_TOKEN'] = 'LKSJdkjskjd898hJHdkjjfh87';
$_ENV['MYAPI_TOKEN_NAME'] = 'X-Auth-Key';

$client = new Swagger( env_prefix: 'MYAPI' );
```

### Usage example of loading environment from calling app.

```
$client = new Swagger( env_prefix: 'MYAPI' );
```

### Usage example of loading specified environment file.

```
$client = new Swagger(
    env_file: '/dir/to/my/.env'
    env_prefix: 'MYAPI',
);
```

### Usage example using Basic Auth via constructor arguments

```
$client = new Swagger(
    host: 'https://api.server.com',
    base_uri: '/api/v1/',
    api_file: __DIR__ . '/mySwagger.api.json',
    auth_method: 'basicauth',
    username: 'bob@bob.com',
    password: 'kjhsdfs7sbjh3JKHG1'
);
```

### Usage 1 example of calling an endpoint

```
$output = $client->path(
      path: '/devices',
    method: 'GET',
      data: [ 'type' => 'olt' ]
);
```

### Usage 2 example of calling an endpoint

```
$output = $client->path(
      path: '/devices/{id}',
    method: 'GET',
        data: [ 
            'type' => 'olt',
            'id' => 'KJHjsk87JJH821jJqkslgh-98asjhCB'
        ]
);
```