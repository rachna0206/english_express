<?php 
ob_start();
include ("db_connect.php");
$obj=new DB_connect();
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);
session_start();
$batch_assign_qry="select * from batch_assign b1,batch b2 where b1.batch_id=b2.id and b2.status='ongoing' and ".strtolower(date('l'))."='y'";
$res_batch_assign=$obj->select($batch_assign_qry);
while($batch_assign=mysqli_fetch_array($res_batch_assign))
{
	$add_attendance="insert into attendance (`student_id`, `stu_attendance`, `faculty_attendance`, `remark`, `batch_id`, `dt`) VALUES ('".$batch_assign["student_id"]."','a','a','','".$batch_assign["batch_id"]."','".date('Y-m-d')."')";
	$res_attendance=$obj->insert($add_attendance);

}

?>