<?php
// ini_set("display_errors",1);
require '../vendor/autoload.php';
use \Firebase\JWT\JWT;

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
  if(!empty($data->email) && !empty($data->password)){
    $user->email = $data->email;
    $user_data = $user->check_login();

    if(!empty($user_data)) {
      $password = $user_data['password'];

      if(password_verify($data->password, $password)){

        $iss = "localhost";
        $iat = time();
        // $nbf = $iat + 10;
        $nbf = $iat;
        $exp = $iat + 3600;
        $aud = "myusers";
        $user_arr_data = array(
          'id' => $user_data['id'],
          'name' => $user_data['name'],
          'email' =>$user_data['email']
        );

        $secret_key = "owt125";

        $payload = array(
          // 'iss' => $iss,
          'iat' => $iat,
          'nbf' => $nbf,
          'exp' => $exp,
          'aud' => $aud,
          'data' => $user_arr_data
        );

        $jwt = JWT::encode($payload,$secret_key,'HS512');

        http_response_code(200);
        echo json_encode(array(
          'status'=> 1,
          'jwt' => $jwt,
          'message' => "User logged in successfully"
          // 'server_name' => $_SERVER['HTTP_HOST']
        ));
      }else {
        http_response_code(404);
        echo json_encode(array(
          'status' => 0,
          'jwt' => $jwt,
          'message' => "Wrong password"
        ));
      }
    }else {
      http_response_code(404);
      echo json_encode(array(
        'status' => 0,
        'message' => "Wrong credientials"
      ));
    }
  }else {
    http_response_code(500);
    echo json_encode(array(
      'status' => 0,
      'message' => "All data needed"
    ));
  }
}else {
  http_response_code(503);
  echo json_encode(array(
    'status' => 0,
    'message' => "Access Denied"
  ));
}
?>
