<?php

declare( strict_types=1 );

require_once "vendor/autoload.php";


use App\Core\Database\Database;
use App\Core\Http\Request;
use App\Core\Services\Storage;

Request::cors();

$db_config = [
    "HOST" => "localhost",
    "DATABASE" => "giggle_pig",
    "USERNAME" => "root",
    "PASSWORD" => "",
];

Database::init( $db_config );

/* set uploads root dir */
Storage::setUploadDir( __DIR__ . "/public/uploads" );
Storage::setMaxUploadSize( 5 * 1024 * 1024 ); // 5MB
