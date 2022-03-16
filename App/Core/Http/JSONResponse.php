<?php


namespace App\Core\Http;


use Exception;

class JSONResponse
{
    private array $payload;
    private int $statusCode;


    public function __construct( $data, $statusCode = 200 )
    {
        $this->payload = $data;
        $this->statusCode = $statusCode;
    }

    public function response()
    {
        header( "Access-Control-Allow-Origin: *" );
        header( "Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS" );
        header( "Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Origin, Methods" );
        header( "Access-Control-Allow-Credentials: true" );
        header( "Access-Control-Max-Age: 3600" );
        header( "Content-Type: application/json; charset=UTF-8" );

        http_response_code( $this->statusCode );
        echo json_encode( [ "payload" => $this->payload, "status" => $this->statusCode ] );
    }

    /**
     * response with invalid message
     * @param array|string $payload - by default, string, if array then
     *                                it is passed as raw array
     * @param int $status_code
     */
    public static function invalidResponse( $payload = "Invalid request", int $status_code = 400 )
    {
        if ( !is_array( $payload ) ) {
            $response = new JSONResponse( [ "data" => $payload ], $status_code );
        } else {
            $response = new JSONResponse( $payload, $status_code );
        }

        $response->response();
    }

    public static function validResponse( $payload = "Success" )
    {
        if ( !is_array( $payload ) ) {
            $response = new JSONResponse( [ "data" => $payload ], 200 );
        } else {
            $response = new JSONResponse( $payload, 200 );
        }

        $response->response();
    }

    /**
     * Response for exception handling
     * @param Exception $exception
     */
    public static function exceptionResponse( Exception $exception )
    {
        self::invalidResponse( [ "error" => $exception->getMessage() ] );
    }
}
