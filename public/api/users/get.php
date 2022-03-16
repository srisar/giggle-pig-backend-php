<?php

declare( strict_types=1 );

use App\Core\Http\Auth;
use App\Core\Http\JSONResponse;
use App\Core\Http\Request;
use App\Models\User;

require_once "../../../bootstrap.php";

try {

    /*
     * Authenticate for incoming auth key
     * if no valid key is present, will return 401
     * */
    Auth::authenticateJWT( User::ROLES_ADMIN_MANAGER );


    $id = Request::getAsInteger( "id", true );


    $user = User::find( $id );

    if ( is_null( $user ) ) throw new Exception( "Invalid user" );

    // removing password_hash from going through api
    unset( $user->password_hash );

    JSONResponse::validResponse( [ "user" => $user ] );
    return;


} catch ( Exception $exception ) {
    JSONResponse::exceptionResponse( $exception );
}
