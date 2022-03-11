<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Method: POST");
header("Content-Type: application/json;charset = utf-8");

include_once("../config/database.php");
include_once("../classes/Users.php");

$db = new Database();
$connection = $db->connect();
$user = new Users($connection);

if($_SERVER['REQUEST_METHOD'] == "POST"){
  $data = json_decode(file_get_contents("php://input"));
  if(!empty($data->name) && !empty($data->password) &&  !empty($data->email)){
    $user->name = $data->name;
    $user->email = $data->email;
    $user->password = password_hash($data->password, PASSWORD_DEFAULT);
    $check_email = $user->check_email();

    if(!empty($check_email)){
      http_response_code(400);
      echo json_encode(array(
        'status' => 1,
        'message' => "User already exits,try another email"
      ));
    }else {
      if($user->create_user()){
        http_response_code(200);
        echo json_encode(array(
          'status' => 1,
          'message' => "User has been created successfully"
        ));
      }else{
        http_response_code(500);
        echo json_encode(array(
          'status' => 0,
          'message' => "Failed to create user"
        ));
      }
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
