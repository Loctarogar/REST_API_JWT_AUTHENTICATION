<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

//files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';

//get database connection
$database = new Database();
$db = $database->getConnection();

//instantiate user object
$user = new User($db);

//get posted data
$data = json_decode(file_get_contents("php://input"));
//get jwt
$jwt = isset($data->jwt) ? $data->jwt : "";
//try to decode if not empty
if($jwt){
    try{
        $decoded = JWT::decode($jwt, $key, ['HS256']);
        $user->firstname = $data->firstname;
        $user->lastname  = $data->lastname;
        $user->email     = $data->email;
        $user->password  = $data->password;
        $user->id        = $data->id;
        if($user->update()){
            $token = [
                "iss"  => $iss,
                "aud"  => $aud,
                "iat"  => $iat,
                "nbf"  => $nbf,
                "data" => [
                    "id"        => $user->id,
                    "firstname" => $user->firstname,
                    "lastname"  => $user->lastname,
                    "email"     => $user->email
                ]
            ];
            $jwt = JWT::encode($token, $key);
            //set response
            http_response_code(200);
            echo json_encode([
                "message" => "User was updated.",
                "jwt"     => $jwt
            ]);
        }else{
            http_response_code(401);
            echo json_encode([
                "message" => "Unable to upgrade user."
            ]);
        }
    }catch (Exception $e){
        http_response_code(401);
        echo json_encode([
            "message" => "Access denied.",
            "error"   => $e->getMessage()
        ]);
    }
}