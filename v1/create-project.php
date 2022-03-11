<?php
ini_set("display_errors",1);

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

if($_SERVER['REQUEST_METHOD'] == "POST") {
  $data = json_decode(file_get_contents("php://input"));
  $all_headers = getallheaders();
  $token = $all_headers['Authorization'];
  $check_token = preg_match('/Bearer\s(\S+)/',$token,$matches);
  $jwt = $matches[1];

  if(!empty($data->name) && !empty($data->description) && !empty($data->status)){

    if(!empty($jwt)) {

      try{
        $secret_key = "owt125";
        $decoded_data = JWT::decode($jwt, new Key($secret_key,"HS512"));
        $user_id = $decoded_data->data->id;

        $user->user_id = $user_id;
        $user->project_name = $data->name;
        $user->description = $data->description;
        $user->status = $data->status;

        if($user->create_project()) {
          http_response_code(200);
          echo json_encode(array(
            'status' => 1,
            'message' => "Project created successfully"
          ));
        }else {
          http_response_code(500);
          echo json_encode(array(
            'status' => 0,
            'message' => "Failed to create project"
          ));
        }
      }
      catch(\Firebase\JWT\ExpiredException $ex){
        http_response_code(500);
        echo json_encode(array(
          'status' => 0,
          'message' => $ex->getMessage()
        ));
      }
    }else {
      http_response_code(500);
      echo json_encode(array(
        'status' => 0,
        'message' => "Token required"
      ));
    }
  }else {
    http_response_code(500);
    echo json_encode(array(
      'status' => 0,
      'message' => "All values needed"
    ));
  }
}else {
  http_response_code(500);
  echo json_encode(array(
    'status' => 0,
    'message' => "Access Denied"
  ));
}

?>
