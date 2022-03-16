<?php

declare( strict_types=1 );

use App\Core\Http\Auth;
use App\Core\Http\JSONResponse;
use App\Core\Http\Request;
use App\Models\User;

require_once "../../../bootstrap.php";

$UPLOAD_SIZE = 2 * 1024 * 1024;

try {

    /*
     * Authenticate for incoming auth key
     * if no valid key is present, will return 401
     * */
    Auth::authenticateJWT( User::ROLES_ADMIN_MANAGER );

    $userId = Request::getAsInteger( "id", true );

    $user = User::find( $userId );

    if ( empty( $user ) ) throw new Exception( "Invalid user" );

    if ( $user->removeProfilePic() ) {
        JSONResponse::validResponse();
        return;
    }
    throw new Exception( "Failed to remove profile picture" );


} catch ( Exception $exception ) {
    JSONResponse::exceptionResponse( $exception );
}
