<?php
declare(strict_types=1);

use App\Core\Http\JSONResponse;
use App\Core\Http\Request;

include "../../bootstrap.php";


$data1 = [1, 2, 3, 4]; // list
$data2 = ["apple" => "A red juicy fruit", "ball" => "small round object"]; // map

$students = [
    1 => [
        "name" => "David",
        "age" => 12,
        "subject" => "ICT"
    ],
    2 => [
        "name" => "Birla",
        "age" => 12,
        "subject" => "Science"
    ],
    3 => [
        "name" => "Mathumitha",
        "age" => 12,
        "subject" => "English"
    ],
];

try {

    $id = Request::getAsInteger("id");

//    JSONResponse::validResponse(["id" => $id]);

    if (isset($students[$id])) {
        $found = $students[$id];
        JSONResponse::validResponse($found);
        exit;
    }

    throw new Exception("Id not found");


} catch (Exception $exception) {
    JSONResponse::exceptionResponse($exception);
}