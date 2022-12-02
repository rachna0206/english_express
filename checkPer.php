<?php

	include ("db_connect.php");
	$obj = new DB_connect();

	function checkPermission($usrtyp,$pagenm)
	{
		global $obj;
		$qr = $obj->con1->prepare("select * from permissions where user_desig='$usrtyp' and form_name='$pagenm' and none='n'");
		$qr->execute();
		$res = $qr->get_result();
		$qr->close();
		$row = mysqli_fetch_array($res);
		return $row;
	}
	
	function checkMainMenu($usrtyp)
	{
		
		global $obj;
		$qr = $obj->con1->prepare("select form_name from permissions where user_desig='$usrtyp' and (read_func='y' or write_func='y' or del_func='y' or upd_func='y') and  none='n'");
		$qr->execute();
		$res = $qr->get_result();
		$qr->close();
		return $res;
	}
	
	

?>