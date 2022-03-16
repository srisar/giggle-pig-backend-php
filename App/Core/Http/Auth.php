<?php


namespace App\Core\Http;


use App\Core\Services\JWTService;
use App\Models\AuthKey;
use App\Models\User;
use Exception;

class Auth
{
    /**
     * Tries to authenticate the incoming request with key
     * @param array $user_types - user role types which the incoming key
     *                            has access to.
     * @return bool
     */
    public static function authenticate(array $user_types = User::ROLES_ALL): bool
    {

        /* gets the auth key from request */
        $authKey = Request::getAuthKey();

        if (!empty($authKey)) {
            $authInstance = AuthKey::validateAuthKey($authKey);

            if (!empty($authInstance)) {
                if (in_array($authInstance->user->role, $user_types)) return true;
            }
        }

        JsonResponse::invalidResponse(["error" => "Authentication failed"], 401);
        die();

    }

    /**
     * Authenticate incoming request against issued JWT
     */
    public static function authenticateJWT(array $user_types = User::ROLES_ALL)
    {

        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            header('HTTP/1.0 400 Bad Request');
            JSONResponse::invalidResponse('Authorization header not found');
            exit;
        }

        $matches = [];

        if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            JSONResponse::invalidResponse('Token not found in request');
            exit;
        }


        $data = JWTService::decode($matches[1])->data;

        if (!in_array($data->role, $user_types)) {
            JSONResponse::invalidResponse('User not authorized');
            exit;
        }

    }

}
