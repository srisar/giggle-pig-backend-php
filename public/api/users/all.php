<?php

declare(strict_types=1);

use App\Core\Http\Auth;
use App\Core\Http\JSONResponse;
use App\Models\User;

require_once "../../../bootstrap.php";

try {

    /*
     * Authenticate for incoming auth key
     * if no valid key is present, will return 401
     * */
//    Auth::authenticate( User::ROLES_ADMIN_MANAGER );

    Auth::authenticateJWT(User::ROLES_ADMIN_MANAGER);

    $users = User::findAll();

    JSONResponse::validResponse($users);
    return;


} catch (Exception $exception) {
    JSONResponse::exceptionResponse($exception);
}
