<?php

declare( strict_types=1 );

use App\Core\Http\Auth;
use App\Core\Http\JSONResponse;
use App\Core\Http\Request as Request;
use App\Core\Resources\MimeTypes;
use App\Core\Services\ImageProcessor;
use App\Core\Services\Uploader;

require_once "../../../bootstrap.php";

$MAX_UPLOAD_SIZE = 1.0 * 1024 * 1024; // 5 MB in bytes

try {

    /*
     * Authenticate for incoming auth key
     * if no valid key is present, will return 401
     * */
    Auth::authenticate();

    /* gets the file from request */
    $file = Request::getFile( "file" );

    /* validate there is a file uploaded */
    if ( empty( $file ) ) throw new Exception( "No file uploaded" );

    /* create new uploader instance to move the uploaded file to desired location */
    $uploader = new Uploader( $file, $MAX_UPLOAD_SIZE, "images", MimeTypes::IMAGE_MIMES );

    /* store the uploaded file */
    // $path = $uploader->storeUploadFile( "image", true );

    $processor = ImageProcessor::createImageObject( $uploader );
    $processor->resize( 1000, 1000, true, true )->save( "hello.jpg" );

    $path = $processor->getRelativePath();


    /* return stored relative path back as response */
    JSONResponse::validResponse( $path );
    return;

} catch ( Exception $exception ) {
    JSONResponse::exceptionResponse( $exception );
}
