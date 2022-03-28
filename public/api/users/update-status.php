<?php

declare(strict_types=1);

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
    Auth::authenticateJWT(User::ROLES_ADMIN_MANAGER);


    $id = Request::getAsInteger("id", true);
    $status = Request::getAsString("status", true);

    /* check if user exists */
    $user = User::find($id);
    if (is_null($user)) throw new Exception("Invalid user");


    if ($status == strtoupper('active')) {
        $user->activate();
    } else {
        $user->deactivate();
    }

    JSONResponse::validResponse();


} catch (Exception $exception) {
    JSONResponse::exceptionResponse($exception);
}
