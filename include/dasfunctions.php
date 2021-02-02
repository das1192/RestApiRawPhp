<?php
	
function redirect_to($new_location){
			header("Location: ".$new_location);
			exit;
		}



function param_check($param){


	$param = strtolower($param);

	if ( strpos($param, ';')  || strpos($param, 'delete')  || strpos($param, 'database') 
		|| strpos($param, 'alter') || strpos($param, 'truncate') || strpos($param, 'drop')
		 || strpos($param, 'column') )  { 
    //die($param);
	$param='';
    
}

return  $param;

}

function check_customer_login($user,$pass){

	$user =mysqli_real_escape_string($GLOBALS['con'],$user);
	$pass =mysqli_real_escape_string($GLOBALS['con'],$pass);
	$user =param_check($user);
	$pass =param_check($pass);

	$sql = "select * from customers where email=? and password=? ";

	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("ss", $user,$pass);
    $stmt->execute();
   	$res =  $stmt->get_result();
    $stmt->close();
	
	return $res;


}		




function stock_list_by_product_id_fetched($id){

$id = param_check($id);

$sql="Select *,(stockQty-sold)as remaining  from (SELECT s.product_id,p.model,p.img1,p.cat_id,p.sub_cat_id,p.price,p.active_status,ifnull(sum(s.quantity),0)as stockQty,
(SELECT  ifnull(sum(odl.product_qty),0) from order_details_list odl 
INNER JOIN order_details od on od.id =odl.order_id
where s.product_id = odl.product_id and od.order_status in ('completed','shipped') )as sold FROM `stock` s
INNER JOIN products p on p.id =s.product_id group by s.product_id ) as stockDetails
where active_status=1 and  product_id=?

GROUP by product_id";

	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
   	$res =  $stmt->get_result();
    $stmt->close();
	//var_dump($res->fetch_row());
	return $res->fetch_assoc();



}



function get_delivery_cost_by_amount($amount){

	$amount = param_check($amount);
	$sql = "select CASE WHEN COUNT(dc.delivery_cost) > 0 THEN dc.delivery_cost ELSE 0 END AS delivery_cost
from delivery_cost dc where ? >=min_amount and ?<=max_amount limit 1";
	
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("ss", $amount,$amount);
    $stmt->execute();
   	$res =  $stmt->get_result();
    $stmt->close();
	


	$row = $res->fetch_assoc();
	return $row['delivery_cost'];


}

function get_all_sub_cat_by_cat_id($id)
{	
	$id = mysqli_real_escape_string($GLOBALS['con'],$id);
	$id =param_check($id);
	$sql = "select * from sub_category where status=1 and cat_id=? order by id asc ";

	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
   	$res = $stmt->get_result();
    $stmt->close();
	
	return $res;

}


function get_all_products_by_cat_and_sub_id($cat_id,$sub_cat_id)
{	

	$cat_id =  mysqli_real_escape_string($GLOBALS['con'],$cat_id);
	$sub_cat_id =  mysqli_real_escape_string($GLOBALS['con'],$sub_cat_id);
	$sql = "select * from products where cat_id=? and sub_cat_id=? order by id desc ";
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("ss", $cat_id,$sub_cat_id);
    $stmt->execute();
   	$res = $stmt->get_result();
    $stmt->close();
	
	return $res;
	
	
}


//====================== for paging
function get_all_products_by_cat_and_sub_id_paging($cat_id,$sub_cat_id,$per_page,$offset)
{	

	$cat_id =  mysqli_real_escape_string($GLOBALS['con'],$cat_id);
	$sub_cat_id =  mysqli_real_escape_string($GLOBALS['con'],$sub_cat_id);
	$per_page =param_check($per_page);
	$offset = param_check($offset);
	$sql = "select * from products where cat_id=? and sub_cat_id=? and active_status=1 order by id desc ";
	$sql .="LIMIT ? ";
	$sql .="OFFSET ? ";
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("ssss", $cat_id,$sub_cat_id,$per_page,$offset);
    $stmt->execute();
   	$res = $stmt->get_result();
    $stmt->close();
	
	return $res;
	
}


function get_all_products_by_condition_paging($condition,$per_page,$offset)
{	

	$condition = param_check($condition);
	$per_page =param_check($per_page);
	$offset = param_check($offset);
	

	$sql = "select * from products where $condition and active_status=1 order by id desc ";
	$sql .="LIMIT ? ";
	$sql .="OFFSET ? ";
	

	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("ii",$per_page,$offset);

 
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	return $res;
}






//======================


function get_all_products_by_type($type)
{	

	$type =  mysqli_real_escape_string($GLOBALS['con'],$type);
	$type =param_check($type);
	
	$sql = "select * from products where product_status=? and active_status='1' order by id desc ";
	
	
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("s",$type);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	return $res;
}



function get_cat_name_by_id($cat_id)
{	
	$cat_id =  mysqli_real_escape_string($GLOBALS['con'],$cat_id);
	$sql = "select cat_name from category where id=? and status='1' limit 1 ";
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$cat_id);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	$row = $res->fetch_assoc();
	return $row['cat_name'];

}


function get_sub_cat_name_by_id($sub_cat_id)
{	
	$sub_cat_id =  mysqli_real_escape_string($GLOBALS['con'],$sub_cat_id);
	
	$sql = "select sub_cat_name from sub_category where id=? and status='1' limit 1 ";
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$sub_cat_id);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	$row = $res->fetch_assoc();
	
	return $row['sub_cat_name'];

}

function get_single_product_details_by_id($pid)
{	
	$pid = mysqli_real_escape_string($GLOBALS['con'],$pid);
	$sql = "SELECT *, CONCAT(product_name,' ',model)as name_model FROM products where id=? limit 1 ";
	
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$pid);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	$row = $res->fetch_assoc();
	return $row ;

}

function get_single_product_details_by_id_non_fetched($pid)
{	
	$pid = mysqli_real_escape_string($GLOBALS['con'],$pid);
	$sql = "SELECT *, CONCAT(product_name,' ',model)as name_model FROM products where id=? limit 1 ";
	
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$pid);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	return $res ;

}


function get_single_product_details_by_id_fetched($pid)
{	
	$pid = mysqli_real_escape_string($GLOBALS['con'],$pid);
	$sql = "SELECT *,p.id as pid, CONCAT(product_name,' ',model)as name_model FROM products p left join specification_details s on s.product_id= p.id where p.id=? limit 1 ";
	
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$pid);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	$row = $res->fetch_assoc();
	return $row ;


}



function get_all_specifications_by_product_id($pid){

	$pid = mysqli_real_escape_string($GLOBALS['con'],$pid);
	$sql = "SELECT * from specification_details where product_id=? ";
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$pid);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	
	return $res ;


}

function get_specification_name_by_id($id)
{	
	$id = mysqli_real_escape_string($GLOBALS['con'],$id);
	$sql = "select specification_name from specification where id=?  limit 1 ";
	
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$id);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	$row = $res->fetch_assoc();
	return $row['specification_name'];

}


function get_fetched_single_customer_data_by_id($id)
{	
	$id = mysqli_real_escape_string($GLOBALS['con'],$id);
	$sql = "select * from customers where id=?  limit 1 ";
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$id);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	$row = $res->fetch_assoc();
	return $row;

}


function get_all_orders_by_customer_id($id)
{	
	$id = mysqli_real_escape_string($GLOBALS['con'],$id);
	$sql = "select  *,od.id as orderId from order_details od left join bkash_payment bp on bp.order_id = od.id where od.customer_id=? order by od.id desc";

	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$id);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	

	return $res;

}

function get_fetched_order_details_data_by_order_id($id){

	$id = mysqli_real_escape_string($GLOBALS['con'],$id);
	$sql = "select *,od.id as od_id ,od.edate as od_edate,c.edate as c_edate from order_details od  inner Join customers c on od.customer_id = c.id where od.id=? ";
	
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$id);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	$row = $res->fetch_assoc();
	return $row;

}

function get_order_details_list_data_by_order_id($id){

	$id = mysqli_real_escape_string($GLOBALS['con'],$id);
	$sql = "select * from order_details_list odl inner Join products p on odl.product_id = p.id  where order_id=? ";
	
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$id);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	return $res;

}


function get_product_compare_items($ids){

	$id = mysqli_real_escape_string($GLOBALS['con'],$ids);
	$sql = "select * from products where Id In $ids ;";
	
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$id);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();
	
	return $res;

}

function find_email_address($email){

	$email = mysqli_real_escape_string($GLOBALS['con'],$email);
	$email = param_check($email);
	$sql = "select * from customers where email ='$email' ;";
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("s",$email);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res;

}

function get_all_promotions(){

	
	$sql = "select * from promotions order by edate desc ;";
	$stmt = $GLOBALS['con']->prepare($sql);    
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res;

}



function get_count_by_sql($sql){
	//$sql = mysqli_real_escape_string($GLOBALS['con'],$sql);
	
	$sql =param_check($sql);

	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	$row = $res->fetch_assoc();

	return $count = $row["count(*)"];

}

function get_all_sliders_by_type($type){



	$sql = "SELECT * FROM sliders Where status = 1 and type=? ";
	$stmt = $GLOBALS['con']->prepare($sql);

	//=======================
	/* bind parameters for markers */
    $stmt->bind_param("s", $type);

    /* execute query */
    $stmt->execute();

    /* bind result variables */
 //   $stmt->bind_result($res);

    /* fetch value */
   //   $stmt->fetch();

       /* GET RESULT */
   	$res =  $stmt->get_result();
    /* close statement */
    $stmt->close();
	

	return $res;

}

function get_all_category(){

	$sql = "SELECT * FROM category ";
	$stmt = $GLOBALS['con']->prepare($sql);


    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res;

}

function get_all_sub_category(){

	$sql = "SELECT * FROM sub_category ";
	$stmt = $GLOBALS['con']->prepare($sql);
   
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res;

}

function get_top_arrival_by_number($limit){

	$sql = "SELECT * FROM products where product_status='arrival' and active_status='1' LIMIT ? ";
	$stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$limit);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res;

}

function get_top_upcoming_by_number($limit){

	$sql = "SELECT * FROM products where product_status='upcoming' and active_status='1' LIMIT ? ";
    
    $stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$limit);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res;

}
function get_all_products_json(){

	$sql = "SELECT * FROM products p left join specification_details s on p.id = s.product_id   where  p.active_status='1' ";
    $stmt = $GLOBALS['con']->prepare($sql);
    
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res;

}
function get_all_products_json_by_id($id){

	$sql = "SELECT * FROM products p left join specification_details s on p.id = s.product_id   where  p.active_status='1' and p.id=? ";
    
    $stmt = $GLOBALS['con']->prepare($sql);
    $stmt->bind_param("i",$id);

    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res;

}


function get_all_series(){

	$sql =" SELECT *,(select count(*) from products where series_id = s.id and active_status='1' )as count FROM series s where s.status ='1' ";
    $stmt = $GLOBALS['con']->prepare($sql);
    
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res;
}

function get_new_arrival_fetched(){

	$sql =" SELECT * FROM products WHERE active_status='1' and product_status ='arrival' and show_arrival='1' order by id desc limit 1 ";
    $stmt = $GLOBALS['con']->prepare($sql);
    
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res->fetch_assoc();


}




function get_new_upcoming_fetched(){

	$sql =" SELECT * FROM products WHERE active_status='1' and product_status ='upcoming' and show_upcoming='1' order by id desc limit 1 ";
   
    $stmt = $GLOBALS['con']->prepare($sql);
    
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res->fetch_assoc();

}




function get_all_active_products(){

	$sql =" SELECT * FROM products WHERE active_status='1' order by id desc ";
    $stmt = $GLOBALS['con']->prepare($sql);
    
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	

	return $res;

}


function get_latest_product_fetched_by_condition($condition){

$condition = param_check($condition);	

$sql =" SELECT * FROM products WHERE $condition order by id desc limit 1 ";

    $stmt = $GLOBALS['con']->prepare($sql);
    
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res->fetch_assoc();

}
	
function get_promotional_products(){

 $sql =" (SELECT * FROM `products` WHERE product_status ='arrival'  order by id desc limit 2) 
UNION
(SELECT * FROM `products` WHERE product_status ='upcoming' order by id desc limit 2 );";
   
    $stmt = $GLOBALS['con']->prepare($sql);
    
    $stmt->execute();
     
   	$res = $stmt->get_result();
   	
    $stmt->close();

	return $res;


}	





?>