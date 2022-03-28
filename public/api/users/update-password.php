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
    Auth::authenticateJWT();


    $fields = [
        "id" => Request::getAsInteger("id", true),
        "new_password" => Request::getAsString("new_password", true),
    ];


    $user = User::find($fields["id"]);

    if (is_null($user)) throw new Exception("Invalid user");

    if (empty($fields['new_password'])) throw new Exception('Invalid password. Password cannot be empty.');

    /* if needed to validate current password */
    // if (!$user->validatePassword($fields["current_password"])) throw new Exception("Invalid current password");

    $result = $user->updatePassword($fields["new_password"]);

    if ($result) {
        JSONResponse::validResponse("Password updated");
        return;
    }


} catch (Exception $exception) {
    JSONResponse::exceptionResponse($exception);
}
