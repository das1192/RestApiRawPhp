<?php
date_default_timezone_set("Asia/Dhaka");
class IncomeDetailsModel {
  // Properties
  private $conn;
  private $table_name = "income";

  // object properties
  public $incomename_id;
  public $amount;
 

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
              incomenameid=:incomeid,amount=:amount, date=:date, datetime=:datetime ";

  // prepare query
  $stmt = $this->conn->prepare($query);

  // sanitize
  //$this->IncomeName=htmlspecialchars(strip_tags($this->IncomeName));


  // bind values
  $stmt->bindParam(":incomeid", $this->incomename_id);
  $stmt->bindParam(":amount", $this->amount);
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