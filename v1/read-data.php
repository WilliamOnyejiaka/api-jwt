<?php
// ini_set("display_errors",1);
require '../vendor/autoload.php';
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Method: POST");
header("Content-Type: application/json;charset = utf-8");

include_once("../config/database.php");
include_once("../classes/Users.php");

$db = new Database();
$connection = $db->connect();
$user = new Users($connection);
// $data = array('jwt'=>"dsff");

if($_SERVER['REQUEST_METHOD'] == "POST") {
  // $data = json_decode(file_get_contents("php://input"));
  $all_headers = getallheaders();
  $token = $all_headers['Authorization'];
  // echo json_encode(array(
  //   'validation' => preg_match('/Bearer\s(\S+)/',$token,$matches),
  //   'jd' => $matches
  // ));
  $check_token = preg_match('/Bearer\s(\S+)/',$token,$matches);

  $jwt = $matches[1];

  if(!empty($jwt)) {

    try{
      $secret_key = "owt125";
      $decoded_data = JWT::decode($jwt, new Key($secret_key,"HS512"));
      $user_id = $decoded_data->data->id;
      echo json_encode(array(
        'status' => 1,
        'message' => "Got JWT",
        'user_data' => $decoded_data,
        'user_id' => $user_id
      ));
    }
    catch(\Firebase\JWT\ExpiredException $ex){
      http_response_code(500);
      echo json_encode(array(
        'status' => 0,
        'message' => $ex->getMessage()
      ));
    }
  }
}else {
  http_response_code(500);
  echo json_encode(array(
    'status' => 0,
    'message' => "Access Denied"
  ));
}


?>
