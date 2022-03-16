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


    $fields = [
        "id" => Request::getAsInteger( "id", true ),
    ];

    $user = User::find( $fields["id"] );

    if(empty( $user)) throw new Exception("Invalid user");

    $result = $user->delete();


} catch ( Exception $exception ) {
    JSONResponse::exceptionResponse( $exception );
}
