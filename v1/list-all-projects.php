<?php
ini_set("display_errors",1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Method: GET");

include_once("../config/database.php");
include_once("../classes/Users.php");

$db = new Database();
$connection = $db->connect();
$user = new Users($connection);

if($_SERVER['REQUEST_METHOD'] == "GET"){
  $projects = $user->list_projects();
  $projects_arr = array();
  if($projects->num_rows > 0){
    while($rows = $projects->fetch_assoc()) {
      $projects_arr[] = array(
        'id' => $rows['id'],
        'name' => $rows['name'],
        'user_id' => $rows['user_id'],
        'description' => $rows['description'],
        'status' => $rows['status'],
        'created_at' => $rows['created_at']
      );

    }
    http_response_code(200);
    echo json_encode(array(
      'status' => 1,
      'data' => $projects_arr
    ));
  }else {
    http_response_code(404);
    echo json_encode(array(
      'status' => 0,
      'message' => "No Projects"
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
