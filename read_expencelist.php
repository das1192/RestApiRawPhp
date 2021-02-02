<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include('include/dbcon.php');
include_once 'expencemodel.php';
  
// instantiate database and product object
$database = new Database();
$db = $database->getConnection();
  
// initialize object
$incomes = new ExpenceModel($db);
  
// read products will be here
$stmt = $incomes->read();
$num = $stmt->rowCount();
  
// check if more than 0 record found
if($num>0){
  
    // products array
    $incomenames_arr=array();
    $incomenames_arr["data"]=array();
  
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
  
        $item=array(
            "id" => $id,
            "ExpenseName" => $ExpenseName,
         
        );
  
        array_push($incomenames_arr["data"], $item);
    }
    
    $incomenames_arr["message"]="data found";
    $incomenames_arr["success"]="1";
    // set response code - 200 OK
    http_response_code(200);
  
    // show products data in json format
    echo json_encode($incomenames_arr);
}
else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no products found
    echo json_encode(
        array("message" => "No products found.","data"=>[],"success"=>0)
    );
}

?>