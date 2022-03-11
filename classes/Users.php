<?php
class Users {

  public $name;
  public $email;
  public $password;
  public $user_id;
  public $project_name;
  public $description;
  public $status;

  private $conn;
  private $users_tbl;
  private $projects_tbl;

  public function __construct($db){
    $this->conn = $db;
    $this->users_tbl = "tbl_users";
    $this->projects_tbl = "tbl_projects";
  }

  public function create_user(){
    $query = "INSERT INTO ".$this->users_tbl." SET name = ?,email = ?,password = ?";
    $obj = $this->conn->prepare($query);
    $obj->bind_param("sss",$this->name,$this->email,$this->password);
    if($obj->execute()){return true;}
    return false;
  }

  public function check_email() {
    $query = "SELECT * FROM ".$this->users_tbl." WHERE email = ?";
    $obj = $this->conn->prepare($query);
    $obj->bind_param("s",$this->email);
    if($obj->execute()) {
      $data = $obj->get_result();
      return $data->fetch_assoc();
    }
    return array();
  }

  public function check_login() {
    $query = "SELECT * FROM ".$this->users_tbl." WHERE email = ?";
    $obj = $this->conn->prepare($query);
    $obj->bind_param("s",$this->email);
    if($obj->execute()) {
      $data = $obj->get_result();
      return $data->fetch_assoc();
    }
    return array();
  }

  public function create_project(){
    $query = "INSERT INTO ".$this->projects_tbl." SET user_id = ?,name = ?,description = ?,status = ?";
    $obj = $this->conn->prepare($query);
    $this->project_name = htmlspecialchars(strip_tags($this->project_name));
    $this->description = htmlspecialchars(strip_tags($this->description));
    $this->status = htmlspecialchars(strip_tags($this->status));


    $obj->bind_param("isss",$this->user_id,$this->project_name,$this->description,$this->status);

    if($obj->execute()){return true;}
    return false;
  }

  public function list_projects(){
    $query = "SELECT * FROM ".$this->projects_tbl." ORDER BY id DESC";
    $obj = $this->conn->prepare($query);
    $obj->execute();
    return $obj->get_result();
  }

  public function get_user_projects(){
    $query = "SELECT * FROM ".$this->projects_tbl." WHERE user_id = ?";
    $obj = $this->conn->prepare($query);
    $obj->bind_param("i",$this->user_id);
    $obj->execute();
    return $obj->get_result();
  }
}

?>
