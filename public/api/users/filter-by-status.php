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

    /*
     * $status {ACTIVE|INACTIVE}
     */
    $status = Request::getAsString('status', true);

    $status = strtoupper($status);

    $users = User::findAllByStatus($status);

    JSONResponse::validResponse($users);
    return;


} catch (Exception $exception) {
    JSONResponse::exceptionResponse($exception);
}
