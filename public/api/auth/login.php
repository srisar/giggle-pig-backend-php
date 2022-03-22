<?php
declare(strict_types=1);

use App\Core\Http\JSONResponse;
use App\Core\Http\Request;
use App\Core\Services\JWTService;
use App\Models\User;

require_once "../../../bootstrap.php";

try {

    $fields = [
        "username" => Request::getAsString("username"),
        "password" => Request::getAsString("password"),
    ];


    $loggedInUser = User::userExist($fields["username"], $fields["password"]);

    if (!is_null($loggedInUser)) {

        $jwt = JWTService::encode([
            "id" => $loggedInUser->id,
            "full_name" => $loggedInUser->full_name,
            "role" => $loggedInUser->role,
        ]);

        // user exists and valid
        JSONResponse::validResponse([
            "message" => "Login successful",
            "jwt" => $jwt,
            "id" => $loggedInUser->id,
            "username" => $loggedInUser->username,
            "email" => $loggedInUser->email,
            "full_name" => $loggedInUser->full_name,
            "role" => $loggedInUser->role,
            "profile_pic" => $loggedInUser->profile_pic,
        ]);
        exit;


    }
    throw new Exception("Invalid username or password");


} catch (Exception $exception) {
    JSONResponse::exceptionResponse($exception);
}