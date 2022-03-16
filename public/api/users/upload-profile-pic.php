<?php

declare(strict_types=1);

use App\Core\Http\Auth;
use App\Core\Http\JSONResponse;
use App\Core\Http\Request;
use App\Core\Services\ImageProcessor;
use App\Core\Services\Uploader;
use App\Core\Resources\MimeTypes;
use App\Models\User;

require_once "../../../bootstrap.php";

$UPLOAD_SIZE = 2 * 1024 * 1024;

try {

    /*
     * Authenticate for incoming auth key
     * if no valid key is present, will return 401
     * */
    Auth::authenticateJWT(User::ROLES_ADMIN_MANAGER);

    $fileObject = Request::getFile("profile_pic");
    $userId = Request::getAsInteger("id", true);

    $user = User::find($userId);

    if (empty($user)) throw new Exception("Invalid user");

    $uploader = new Uploader($fileObject, $UPLOAD_SIZE, "images/users/", MimeTypes::IMAGE_MIMES);

    $processor = ImageProcessor::createImageObject($uploader);
    $processor->fit(250)->save("profile_" . $user->id . ".jpg", 85, "jpg");

    $user->profile_pic = $processor->getRelativePath();

    if ($user->updateProfilePic()) {
        JSONResponse::validResponse();
    }


} catch (Exception $exception) {
    JSONResponse::exceptionResponse($exception);
}
