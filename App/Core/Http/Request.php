<?php

declare( strict_types=1 );

namespace App\Core\Http;

use Exception;

class Request
{

    public const REQUEST_GET = "GET";
    public const REQUEST_POST = "POST";

    public const METHOD_GET = "GET";
    public const METHOD_POST = "POST";
    public const METHOD_PUT = "PUT";
    public const METHOD_DELETE = "DELETE";
    public const METHOD_PATCH = "PATCH";

    /**
     * Returns the request method: GET, POST, PUT, DELETE and so on..
     * @return string
     */
    public static function method(): string
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public static function scheme(): string
    {
        return $_SERVER["REQUEST_SCHEME"];
    }

    public static function host(): string
    {
        return $_SERVER["HTTP_HOST"];
    }


    /**
     * Check if the request method can be accepted, if not throw an exception
     *
     * @param $acceptableMethod - one of the public const defined in Request class
     * @return bool
     * @throws Exception
     */
    public static function validateRequestMethod( $acceptableMethod ): bool
    {

        $method = Request::method();
        if ( $method == $acceptableMethod ) return true;

        throw new Exception( "$method not accepted" );
    }


    /**
     * Get raw param from the request for a given key, if require is set
     * then the key must be in the request, otherwise exception will be thrown
     *
     * @param string $key
     * @param bool $required - if true, and key is not found in the request,
     *                          will throw an exception
     * @return mixed
     * @throws Exception
     */
    private static function getParam( string $key, bool $required = false )
    {
        $axios = self::getAxiosData();
        $_REQUEST = array_merge( $axios, $_REQUEST );

        if ( isset( $_REQUEST[ $key ] ) ) {
            return $_REQUEST[ $key ];
        }

        if ( $required ) throw new Exception( "Field ($key) is required" );
        return null;
    }

    /**
     * Returns request parameter value as integer
     *
     * @param string $key
     * @param bool $required
     * @return int|null
     * @throws Exception
     */
    public static function getAsInteger( string $key, bool $required = false ): ?int
    {
        $data = self::getParam( $key, $required );

        if ( !empty( $data ) ) {
            if ( $data == "0" ) return 0;
            if ( filter_var( $data, FILTER_VALIDATE_INT ) ) {
                return (int)$data;
            }
        }
        return null;
    }

    /**
     * Returns request parameter value as float
     * @param string $key
     * @param bool $required
     * @return float|null
     * @throws Exception
     */
    public static function getAsFloat( string $key, bool $required = false ): ?float
    {
        $data = self::getParam( $key, $required );

        if ( !empty( $data ) ) {
            if ( $data == "0" ) return 0;
            if ( filter_var( $data, FILTER_VALIDATE_FLOAT ) ) {
                return (float)$data;
            }
        }
        return null;
    }

    /**
     * Returns request parameter value as string
     * @param string $key
     * @param bool $required
     * @return string|null
     * @throws Exception
     */
    public static function getAsString( string $key, bool $required = false ): ?string
    {
        $data = self::getParam( $key, $required );
        if ( !empty( $data ) ) {
            return filter_var( $data, FILTER_SANITIZE_STRING );
        }
        return null;
    }

    /**
     * @param string $key
     * @param bool $required
     * @return string|null
     * @throws Exception
     */
    public static function getAsRawString( string $key, bool $required = false ): ?string
    {
        $data = self::getParam( $key );

        if ( empty( $data ) ) throw new Exception( "Field ($key) is required" );
        return $data;
    }

    /**
     * @param string $key
     * @param bool $required
     * @return bool|null
     * @throws Exception
     */
    public static function getAsBoolean( string $key, bool $required = false ): ?bool
    {
        $data = self::getParam( $key, $required );

        if ( !is_null( $data ) ) {
            return filter_var( $data, FILTER_VALIDATE_BOOLEAN );
        }

        return null;
    }


    public static function getFile( string $key )
    {
        if ( isset( $_FILES[ $key ] ) ) return $_FILES[ $key ];
        return null;
    }

    /**
     * gets auth key from the request header
     * @param string $key - key header value
     * @return string
     */
    public static function getAuthKey( string $key = "HTTP_AUTH" ): string
    {
        if ( isset( $_SERVER[ $key ] ) ) {
            return $_SERVER[ $key ];
        }
        return "";
    }


    /**
     * Enable CORS support. This method can be hooked into bootstrap call
     */
    public static function cors()
    {

        // Allow from any origin
        if ( isset( $_SERVER["HTTP_ORIGIN"] ) ) {
            // Decide if the origin in $_SERVER["HTTP_ORIGIN"] is one
            // you want to allow, and if so:
            header( "Access-Control-Allow-Origin: {$_SERVER["HTTP_ORIGIN"]}" );
            header( "Access-Control-Allow-Credentials: true" );
            header( "Access-Control-Max-Age: 86400" );    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ( $_SERVER["REQUEST_METHOD"] == "OPTIONS" ) {

            if ( isset( $_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"] ) )
                // may also be using PUT, PATCH, HEAD etc
                header( "Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS" );

            if ( isset( $_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"] ) )
                header( "Access-Control-Allow-Headers: {$_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]}" );

            exit( 0 );
        }

    }


    /**
     * Enable Axios support: get data sent by axios into php.
     * By default, PHP handles form-data, but axios sends data as
     * raw request
     * @return array
     */
    private static function getAxiosData(): array
    {
        $data = json_decode( file_get_contents( "php://input" ), true );

        if ( !is_null( $data ) ) return $data;
        return [];
    }


}
