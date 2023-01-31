<?php 
ob_start();
include ("db_connect.php");
$obj=new DB_connect();
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);
session_start();


$stmt_list = $obj->con1->prepare("select * from student where `status`='inquiry' and followup_dt='".date("Y-m-d")."'");
$stmt_list->execute();
$lead_res = $stmt_list->get_result();	
$stmt_list->close();


while($lead_row=mysqli_fetch_array($lead_res))
{
	$status=1;
	$playstatus=1;

	$stmt_list = $obj->con1->prepare("INSERT INTO `lead_notification`( `stu_id`, `status`, `play_status`) VALUES(?,?,?)");
	$stmt_list->bind_param("iii",$lead_row["sid"],$status,$playstatus);
	$stmt_list->execute();
	
	$stmt_list->close();

}

?>