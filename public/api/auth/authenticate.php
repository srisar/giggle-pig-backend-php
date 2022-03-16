<?php

declare(strict_types=1);

use App\Core\Http\Auth;
use App\Core\Http\JSONResponse;
use App\Core\Http\Request;
use App\Models\AuthKey;
use App\Models\User;

require_once "../../../bootstrap.php";

try {

    Auth::authenticateJWT();

    JSONResponse::validResponse();
    exit;


} catch (Exception $exception) {
    JSONResponse::exceptionResponse($exception);
}

