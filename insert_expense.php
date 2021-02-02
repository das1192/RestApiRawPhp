<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
include('include/dbcon.php');
include_once 'expenseDetailsmodel.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$incomes = new ExpenseDetailsModel($db);
  
// read products will be here
// get posted data

$name_id = $_POST['expenseid'];
$amount = $_POST['amount'];
  
// make sure data is not empty
if(
    !empty($name_id) &&  !empty($amount)
){
  
    // set product property values
    $incomes->name_id = $name_id;
    $incomes->amount  = $amount;
  
    // create the product
    if($incomes->create()){
  
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(array("message" => "জমা  টি  সম্পূর্ণ  হয়েছে","success"=>1));
    }
  
    // if unable to create the product, tell the user
    else{
  
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "জমা  টি  ব্যর্থ  হয়েছে ","success"=>0));
    }
}
  
// tell the user data is incomplete
else{
  
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "জমা  টি  ব্যর্থ  হয়েছে .","success"=>0));
}

?>

