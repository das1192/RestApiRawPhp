<?php
date_default_timezone_set("Asia/Dhaka");
class ExpenceModel {
  // Properties
  private $conn;
  private $table_name = "expensenames";

  // object properties
  public $id;
  public $ExpenceName;
 

  // constructor with $db as database connection
  public function __construct($db){
      $this->conn = $db;
  }


  // read products
function read(){
  
  // select all query
  $query = "SELECT
             *
          FROM
              " . $this->table_name . " ";

  // prepare query statement
  $stmt = $this->conn->prepare($query);

  // execute query
  $stmt->execute();

  return $stmt;
}



function create(){
  $datetime = date("Y-m-d H:i:s");
  $date = date("Y-m-d");
  // query to insert record
  $query = "INSERT INTO
              " . $this->table_name . "
          SET
              ExpenseName=:ename, date=:date, datetime=:datetime ";

  // prepare query
  $stmt = $this->conn->prepare($query);

  // sanitize
  $this->ExpenseName=htmlspecialchars(strip_tags($this->ExpenseName));


  // bind values
  $stmt->bindParam(":ename", $this->ExpenseName);
  $stmt->bindParam(":date", $date);
  $stmt->bindParam(":datetime", $datetime);

  // execute query
  if($stmt->execute()){
      return true;
  }

  return false;
    
}





}
?> 