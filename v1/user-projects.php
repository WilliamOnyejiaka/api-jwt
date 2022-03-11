<?php

ini_set("display_errors",1);
// header("Access-Control-Allow-Origin: *");
// // header("Access-Control-Allow-Headers: *");
// header("Access-Control-Allow-Method: GET");

// header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
// header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
// header('Access-Control-Allow-Methods:  GET');
// header("Access-Control-Allow-Credentials: true");
// header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
  header("HTTP/1.1 200 OK");
  die();
}

require '../vendor/autoload.php';
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;




include_once("../config/database.php");
include_once("../classes/Users.php");

$db = new Database();
$connection = $db->connect();
$user = new Users($connection);

if($_SERVER['REQUEST_METHOD'] == "GET") {
  $all_headers = getallheaders();
  $token = $all_headers['Authorization'];
  if($token) {
    $check_token = preg_match('/Bearer\s(\S+)/', $token, $matches);

    $jwt = $matches[1];

    if (!empty($jwt)) {

      try {
        $secret_key = "owt125";
        $decoded_data = JWT::decode($jwt, new Key($secret_key, "HS512"));

        $user_id = $decoded_data->data->id;
        $user->user_id = $user_id;
        $projects = $user->get_user_projects();
        $user_data = array();

        if ($projects->num_rows > 0) {
          while ($rows = $projects->fetch_assoc()) {
            $user_data[] = array(
              'id' => $rows['id'],
              'name' => $rows['name'],
              'description' => $rows['description'],
              'status' => $rows['status'],
              'created_at' => $rows['created_at']
            );
          }

          http_response_code(200);
          echo json_encode(array(
            'status' => 1,
            'data' => $user_data
          ));
        } else {
          http_response_code(404);
          echo json_encode(array(
            'status' => 1,
            'data' => "No data"
          ));
        }
      } catch (\Firebase\JWT\ExpiredException $ex) {
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
      'message' => "Authorization"
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
