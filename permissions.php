<?php
include("header.php");
error_reporting(0);

// for permission
if($row=checkPermission($_SESSION["utype"],"permission")){ }
else{
	header("location:home.php");
}


// fill data for update
$utype="";
$per = array();
if(isset($_REQUEST["desig"]))
{
	$utype=$_REQUEST["desig"];	
	$stmt_ulist = $obj->con1->prepare("select * from permissions where user_desig='$utype'");
	$stmt_ulist->execute();
	$res = $stmt_ulist->get_result();
	$stmt_ulist->close();
	$i=0;
	while($row1=mysqli_fetch_array($res))
	{
		$per[$i++] = $row1[3];
		$per[$i++] = $row1[4];
		$per[$i++] = $row1[5];
		$per[$i++] = $row1[6];
		$per[$i++] = $row1[7];
		$per[$i++] = $row1[8];
	}
	//echo $i." value of i ";
}
// fill data for view
else if(isset($_REQUEST["designation"]))
{
	$utype=$_REQUEST["designation"];	
	$stmt_ulist = $obj->con1->prepare("select * from permissions where user_desig='$utype'");
	$stmt_ulist->execute();
	$res = $stmt_ulist->get_result();
	$stmt_ulist->close();
	$j=0;
	while($row1=mysqli_fetch_array($res))
	{
		$per[$j++] = $row1[3];
		$per[$j++] = $row1[4];
		$per[$j++] = $row1[5];
		$per[$j++] = $row1[6];
		$per[$j++] = $row1[7];
		$per[$j++] = $row1[8];
	}
	//echo $j." value of j";
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$user = $_REQUEST['user'];

  	$form_name = array("student_reg","faculty_reg","course_master","book_master","chap_master","skill_master","attendance","exercise_master","batch","batch_assign","student_assign","branch","printing","motivation","permission","notification","lead_generation","associate_reg","state","city");

	for($i=0;$i<20;$i++)
	{
		$formnm = $form_name[$i];
		  
		$read = "n";
		$write = "n";
		$delete = "n";
		$update = "n";
		$all = "n";
		$none = "n";
		  
		if(isset($_REQUEST['r'.$i])){
			$read = "y";
		}
		if(isset($_REQUEST['w'.$i])){
			$write = "y";
		}
		if(isset($_REQUEST['d'.$i])){
			$delete = "y";
		}
		if(isset($_REQUEST['u'.$i])){
			$update = "y";
		}
		if(isset($_REQUEST['a'.$i])){
			$all = "y";
		}
		if(isset($_REQUEST['n'.$i])){
			$none = "y";
		}
		 
	  try
	  {
		$stmt = $obj->con1->prepare("insert into permissions(user_desig,form_name,read_func,write_func,del_func,upd_func,all_func,none) values(?,?,?,?,?,?,?,?)");
		$stmt->bind_param("ssssssss", $user,$formnm,$read,$write,$delete,$update,$all,$none);
		$Resp=$stmt->execute();
		if(!$Resp)
		{
		  throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
		}
		$stmt->close();
	  } 
	  catch(\Exception  $e) {
		setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
	  }
	}


  if($Resp)
  {      
  	  setcookie("msg", "data",time()+3600,"/"); 
      header("location:permissions.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:permissions.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
	$user = $_REQUEST['user'];

  	$form_name = array("student_reg","faculty_reg","course_master","book_master","chap_master","skill_master","attendance","exercise_master","batch","batch_assign","student_assign","branch","printing","motivation","permission","notification","lead_generation","associate_reg","state","city");

	for($i=0;$i<20;$i++)
	{
		$formnm = $form_name[$i];
		  
		$read = "n";
		$write = "n";
		$delete = "n";
		$update = "n";
		$all = "n";
		$none = "n";
		  
		if(isset($_REQUEST['r'.$i])){
			$read = "y";
		}
		if(isset($_REQUEST['w'.$i])){
			$write = "y";
		}
		if(isset($_REQUEST['d'.$i])){
			$delete = "y";
		}
		if(isset($_REQUEST['u'.$i])){
			$update = "y";
		}
		if(isset($_REQUEST['a'.$i])){
			$all = "y";
		}
		if(isset($_REQUEST['n'.$i])){
			$none = "y";
		}
//		echo $read.$write.$delete.$update.$all.$none.$formnm.$user."<br/>";
		
	  try
	  {
		$stmt = $obj->con1->prepare("update permissions set read_func=?, write_func=?, del_func=?, upd_func=?, all_func=?, none=? where form_name=? and user_desig=?");
		$stmt->bind_param("ssssssss", $read,$write,$delete,$update,$all,$none,$formnm,$user);
		$Resp=$stmt->execute();
		if(!$Resp)
		{
		  throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
		}
		$stmt->close();
	  } 
	  catch(\Exception  $e) {
		setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
	  }
	}


  if($Resp)
  {
	  setcookie("msg", "update",time()+3600,"/");
      header("location:permissions.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:permissions.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
	$stmt_del = $obj->con1->prepare("delete from permissions where user_desig='".$_REQUEST["user"]."'");
	$Resp=$stmt_del->execute();
	if(!$Resp)
	{
      throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
    }
    $stmt_del->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
	setcookie("msg", "data_del",time()+3600,"/");
    header("location:permissions.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:permissions.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Permissions Master</h4>

<?php 
if(isset($_COOKIE["msg"]) )
{

  if($_COOKIE['msg']=="data")
  {

  ?>
  <div class="alert alert-primary alert-dismissible" role="alert">
    Data added succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
  if($_COOKIE['msg']=="update")
  {

  ?>
  <div class="alert alert-primary alert-dismissible" role="alert">
    Data updated succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
  if($_COOKIE['msg']=="data_del")
  {

  ?>
  <div class="alert alert-primary alert-dismissible" role="alert">
    Data deleted succesfully
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
  if($_COOKIE['msg']=="fail")
  {
  ?>

  <div class="alert alert-danger alert-dismissible" role="alert">
    An error occured! Try again.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }
}
  if(isset($_COOKIE["sql_error"]))
  {
    ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
      <?php echo urldecode($_COOKIE['sql_error'])?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
      </button>
    </div>

    <script type="text/javascript">eraseCookie("sql_error")</script>
    <?php
  }
?>

<?php if($row["write_func"]=="y" || $row["upd_func"]=="y" || $row["read_func"]=="y"){ ?>
              <!-- Basic Layout -->
              <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Permissions</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">User Designation</label>
                          <input type="text" class="form-control" name="user" id="user" value="<?php echo $utype ?>" required />
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                                             
						<div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Student Registration</label><br>
                          <input type="hidden" name="stu_reg" id="stu_reg">
                           <input type="checkbox" name="r0" id="r0" value="read" <?php if($per[0]=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w0" id="w0" value="write" <?php if($per[1]=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d0" id="d0" value="delete" <?php if($per[2]=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u0" id="u0" value="update" <?php if($per[3]=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a0" id="a0" value="all" <?php if($per[4]=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n0" id="n0" value="none" <?php if($per[5]=="y"){?> checked="checked" <?php } ?>/> None
                        </div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Staff Registration</label>
                          <input type="hidden" name="faculty_reg" id="faculty_reg"><br>
                           <input type="checkbox" name="r1" id="r1" value="read" <?php if(($per[6])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w1" id="w1" value="write" <?php if(($per[7])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d1" id="d1" value="delete" <?php if(($per[8])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u1" id="u1" value="update"<?php if(($per[9])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a1" id="a1" value="all" <?php if(($per[10])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n1" id="n1" value="none" <?php if(($per[11])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Course Master</label><br>
                          <input type="hidden" name="course_master" id="course_master">
						              <input type="checkbox" name="r2" id="r2" value="read" <?php if(($per[12])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w2" id="w2" value="write" <?php if(($per[13])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d2" id="d2" value="delete" <?php if(($per[14])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u2" id="u2" value="update" <?php if(($per[15])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a2" id="a2" value="all" <?php if(($per[16])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n2" id="n2" value="none" <?php if(($per[17])=="y"){?> checked="checked" <?php } ?>/> None
                        </div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Book Master</label><br>
                          <input type="hidden" name="book_master" id="book_master">
                           <input type="checkbox" name="r3" id="r3" value="read" <?php if(($per[18])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w3" id="w3" value="write" <?php if(($per[19])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d3" id="d3" value="delete" <?php if(($per[20])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u3" id="u3" value="update" <?php if(($per[21])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a3" id="a3" value="all" <?php if(($per[22])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n3" id="n3" value="none" <?php if(($per[23])=="y"){?> checked="checked" <?php } ?>/> None
						</div>                        
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Chapter Master</label>
                          <input type="hidden" name="chap_master" id="chap_master"><br>
                           <input type="checkbox" name="r4" id="r4" value="read" <?php if(($per[24])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w4" id="w4" value="write" <?php if(($per[25])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d4" id="d4" value="delete" <?php if(($per[26])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u4" id="u4" value="update" <?php if(($per[27])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a4" id="a4" value="all" <?php if(($per[28])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n4" id="n4" value="none" <?php if(($per[29])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Skill Master</label>
                          <input type="hidden" name="skill_master" id="skill_master"><br>
                           <input type="checkbox" name="r5" id="r5" value="read" <?php if(($per[30])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w5" id="w5" value="write" <?php if(($per[31])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d5" id="d5" value="delete" <?php if(($per[32])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u5" id="u5" value="update" <?php if(($per[33])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a5" id="a5" value="all" <?php if(($per[34])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n5" id="n5" value="none" <?php if(($per[35])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Attendance</label>
                          <input type="hidden" name="attendance" id="attendance"><br>
                           <input type="checkbox" name="r6" id="r6" value="read" <?php if(($per[36])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w6" id="w6" value="write" <?php if(($per[37])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d6" id="d6" value="delete" <?php if(($per[38])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u6" id="u6" value="update" <?php if(($per[39])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a6" id="a6" value="all" <?php if(($per[40])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n6" id="n6" value="none" <?php if(($per[41])=="y"){?> checked="checked" <?php } ?>/> None
						            </div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Exercise Master</label>
                          <input type="hidden" name="exercise_master" id="exercise_master"><br>
                           <input type="checkbox" name="r7" id="r7" value="read" <?php if(($per[42])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w7" id="w7" value="write" <?php if(($per[43])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d7" id="d7" value="delete" <?php if(($per[44])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u7" id="u7" value="update" <?php if(($per[45])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a7" id="a7" value="all" <?php if(($per[46])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n7" id="n7" value="none" <?php if(($per[47])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                      <!--  <hr>	-->
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Batch</label>
                          <input type="hidden" name="batch" id="batch"><br>
                           <input type="checkbox" name="r8" id="r8" value="read" <?php if(($per[48])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w8" id="w8" value="write" <?php if(($per[49])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d8" id="d8" value="delete" <?php if(($per[50])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u8" id="u8" value="update" <?php if(($per[51])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a8" id="a8" value="all" <?php if(($per[52])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n8" id="n8" value="none" <?php if(($per[53])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Batch Assignment</label><br>
                           <input type="checkbox" name="r9" id="r9" value="read" <?php if(($per[54])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w9" id="w9" value="write" <?php if(($per[55])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d9" id="d9" value="delete" <?php if(($per[56])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u9" id="u9" value="update" <?php if(($per[57])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a9" id="a9" value="all" <?php if(($per[58])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n9" id="n9" value="none" <?php if(($per[59])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Student Assignment</label><br>
                           <input type="checkbox" name="r10" id="r10" value="read" <?php if(($per[60])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w10" id="w10" value="write" <?php if(($per[61])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d10" id="d10" value="delete" <?php if(($per[62])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u10" id="u10" value="update" <?php if(($per[63])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a10" id="a10" value="all" <?php if(($per[64])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n10" id="n10" value="none" <?php if(($per[65])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Branch</label><br>
                           <input type="checkbox" name="r11" id="r11" value="read" <?php if(($per[66])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w11" id="w11" value="write" <?php if(($per[67])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d11" id="d11" value="delete" <?php if(($per[68])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u11" id="u11" value="update" <?php if(($per[69])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a11" id="a11" value="all" <?php if(($per[70])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n11" id="n11" value="none" <?php if(($per[71])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Printing</label><br>
                           <input type="checkbox" name="r12" id="r12" value="read" <?php if(($per[72])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w12" id="w12" value="write" <?php if(($per[73])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d12" id="d12" value="delete" <?php if(($per[74])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u12" id="u12" value="update" <?php if(($per[75])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a12" id="a12" value="all" <?php if(($per[76])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n12" id="n12" value="none" <?php if(($per[77])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Motivation</label><br>
                           <input type="checkbox" name="r13" id="r13" value="read" <?php if(($per[78])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w13" id="w13" value="write" <?php if(($per[79])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d13" id="d13" value="delete" <?php if(($per[80])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u13" id="u13" value="update" <?php if(($per[81])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a13" id="a13" value="all" <?php if(($per[82])=="y"){?> checked="checked" <?php } ?>/> All
                           <input type="checkbox" name="n13" id="n13" value="none" <?php if(($per[83])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Permission Page</label><br>
                           <input type="checkbox" name="r14" id="r14" value="read" <?php if(($per[84])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w14" id="w14" value="write" <?php if(($per[85])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d14" id="d14" value="delete" <?php if(($per[86])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u14" id="u14" value="update" <?php if(($per[87])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a14" id="a14" value="all" <?php if(($per[88])=="y"){?> checked="checked" <?php } ?>/>All
                           <input type="checkbox" name="n14" id="n14" value="none" <?php if(($per[89])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Notification Page</label><br>
                           <input type="checkbox" name="r15" id="r15" value="read" <?php if(($per[90])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w15" id="w15" value="write" <?php if(($per[91])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d15" id="d15" value="delete" <?php if(($per[92])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u15" id="u15" value="update" <?php if(($per[93])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a15" id="a15" value="all" <?php if(($per[94])=="y"){?> checked="checked" <?php } ?>/>All
                           <input type="checkbox" name="n15" id="n15" value="none" <?php if(($per[95])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Lead Generation Page</label><br>
                           <input type="checkbox" name="r16" id="r16" value="read" <?php if(($per[96])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w16" id="w16" value="write" <?php if(($per[97])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d16" id="d16" value="delete" <?php if(($per[98])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u16" id="u16" value="update" <?php if(($per[99])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a16" id="a16" value="all" <?php if(($per[100])=="y"){?> checked="checked" <?php } ?>/>All
                           <input type="checkbox" name="n16" id="n16" value="none" <?php if(($per[101])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Associate Master</label><br>
                           <input type="checkbox" name="r17" id="r17" value="read" <?php if(($per[102])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w17" id="w17" value="write" <?php if(($per[103])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d17" id="d17" value="delete" <?php if(($per[104])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u17" id="u17" value="update" <?php if(($per[105])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a17" id="a17" value="all" <?php if(($per[106])=="y"){?> checked="checked" <?php } ?>/>All
                           <input type="checkbox" name="n17" id="n17" value="none" <?php if(($per[107])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">State Master</label><br>
                           <input type="checkbox" name="r18" id="r18" value="read" <?php if(($per[108])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w18" id="w18" value="write" <?php if(($per[109])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d18" id="d18" value="delete" <?php if(($per[110])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u18" id="u18" value="update" <?php if(($per[111])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a18" id="a18" value="all" <?php if(($per[112])=="y"){?> checked="checked" <?php } ?>/>All
                           <input type="checkbox" name="n18" id="n18" value="none" <?php if(($per[113])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        <hr>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">City Master</label><br>
                           <input type="checkbox" name="r19" id="r19" value="read" <?php if(($per[114])=="y"){?> checked="checked" <?php } ?>/> Read
                           <input type="checkbox" name="w19" id="w19" value="write" <?php if(($per[115])=="y"){?> checked="checked" <?php } ?>/> Write
                           <input type="checkbox" name="d19" id="d19" value="delete" <?php if(($per[116])=="y"){?> checked="checked" <?php } ?>/> Delete
                           <input type="checkbox" name="u19" id="u19" value="update" <?php if(($per[117])=="y"){?> checked="checked" <?php } ?>/> Update
                           <input type="checkbox" name="a19" id="a19" value="all" <?php if(($per[118])=="y"){?> checked="checked" <?php } ?>/>All
                           <input type="checkbox" name="n19" id="n19" value="none" <?php if(($per[119])=="y"){?> checked="checked" <?php } ?>/> None
						</div>
                        
                        
                        <?php if($row["write_func"]=="y"){
							if($i==0 && $j==0){ ?>
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
                        <?php } } if($row["upd_func"]=="y"){ 
							if($i!=0){	?>
						<button type="submit" name="btnupdate" id="btnupdate" class="btn btn-primary">Update</button>
                        <?php } } ?>
                        <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='permissions.php'">Cancel</button>

                      </form>
                    </div>
                  </div>
                </div>
                
              </div>
              
<?php } ?>

<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>              
           <!-- grid -->

           <!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Permission Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>User Designation</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select pid,user_desig from permissions group by user_desig order by pid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        $stmt_list->close();
                        $i=1;
                        while($p=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $p["user_desig"]?></td>

                   	<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>                        
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a href="javascript:editdata('<?php echo $p["user_desig"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $p["user_desig"] ?>');"><i class="bx bx-trash me-1"></i> </a>
						<?php } if($row["read_func"]=="y"){ ?>
                        	<a href="javascript:viewdata('<?php echo $p["user_desig"]?>');">View</a>
                        <?php } ?>
						</td>
                   	<?php } ?>
                      </tr>
                      <?php
                          $i++;
                        }
                      ?>
                      
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Basic Bootstrap Table -->


           <!-- / grid -->
<?php } ?>

            <!-- / Content -->
<script type="text/javascript">

// for Student Registration  
  $('#a0').click(function(){
	 if($(this).is(':checked')){
	//	 $('#n0').prop('checked',false);
		 $('#r0,#w0,#d0,#u0').prop('checked',true);
		 $('#n0').prop('checked',false);
	 }
	 else{ $('#r0,#w0,#d0,#u0').prop('checked',false); }
  });
  $('#r0,#w0,#d0,#u0').click(function(){
	 if($(this).is(':checked')){ $('#n0').prop('checked',false);}
	 else{ $('#a0,#n0').prop('checked',false); }
  });
  $('#n0').click(function(){
	 if($(this).is(':checked')){ $('#r0,#w0,#d0,#u0,#a0').prop('checked',false); }
	 else{ }
  });  
  
// for Faculty Registration  
  $('#a1').click(function(){
	 if($(this).is(':checked')){
		 $('#n1').prop('checked',false);
		 $('#r1,#w1,#d1,#u1').prop('checked',true);
	 }
	 else{ $('#r1,#w1,#d1,#u1').prop('checked',false); }
  });  
  $('#r1,#w1,#d1,#u1').click(function(){
	 if($(this).is(':checked')){$('#n1').prop('checked',false); }
	 else{ $('#a1,#n1').prop('checked',false); }
  });
  $('#n1').click(function(){
	 if($(this).is(':checked')){ $('#r1,#w1,#d1,#u1,#a1').prop('checked',false); }
	 else{ }
  });  
  
// for Course Master  
  $('#a2').click(function(){
	 if($(this).is(':checked')){
		 $('#n2').prop('checked',false);
		 $('#r2,#w2,#d2,#u2').prop('checked',true);
	 }
	 else{ $('#r2,#w2,#d2,#u2').prop('checked',false); }
  });
  $('#r2,#w2,#d2,#u2').click(function(){
	 if($(this).is(':checked')){$('#n2').prop('checked',false); }
	 else{ $('#a2,#n2').prop('checked',false); }
  });
  $('#n2').click(function(){
	 if($(this).is(':checked')){ $('#r2,#w2,#d2,#u2,#a2').prop('checked',false); }
	 else{ }
  });

// for Book Master  
  $('#a3').click(function(){
	 if($(this).is(':checked')){
		 $('#n3').prop('checked',false);
		 $('#r3,#w3,#d3,#u3').prop('checked',true);
	 }
	 else{ $('#r3,#w3,#d3,#u3').prop('checked',false); }
  });
  $('#r3,#w3,#d3,#u3').click(function(){
	 if($(this).is(':checked')){$('#n3').prop('checked',false); }
	 else{ $('#a3,#n3').prop('checked',false); }
  });
  $('#n3').click(function(){
	 if($(this).is(':checked')){ $('#r3,#w3,#d3,#u3,#a3').prop('checked',false); }
	 else{ }
  });  

// for Chapter Master  
  $('#a4').click(function(){
	 if($(this).is(':checked')){
		 $('#n4').prop('checked',false);
		 $('#r4,#w4,#d4,#u4').prop('checked',true);
	 }
	 else{ $('#r4,#w4,#d4,#u4').prop('checked',false); }
  });
  $('#r4,#w4,#d4,#u4').click(function(){
	 if($(this).is(':checked')){ $('#n4').prop('checked',false); }
	 else{ $('#a4,#n4').prop('checked',false); }
  });
  $('#n4').click(function(){
	 if($(this).is(':checked')){ $('#r4,#w4,#d4,#u4,#a4').prop('checked',false); }
	 else{ }
  });  

// for Skill Master
  $('#a5').click(function(){
	 if($(this).is(':checked')){
	
		 $('#r5,#w5,#d5,#u5').prop('checked',true);
		 $('#n5').prop('checked',false);
	 }
	 else{ $('#r5,#w5,#d5,#u5').prop('checked',false); }
  });
  $('#r5,#w5,#d5,#u5').click(function(){
	 if($(this).is(':checked')){ $('#n5').prop('checked',false); }
	 else{ $('#a5,#n5').prop('checked',false); }
  });
  $('#n5').click(function(){
	 if($(this).is(':checked')){ $('#r5,#w5,#d5,#u5,#a5').prop('checked',false); }
	 else{ }
  });  

// for Attendance 
  $('#a6').click(function(){
	 if($(this).is(':checked')){
		 $('#n6').prop('checked',false);
		 $('#r6,#w6,#d6,#u6').prop('checked',true);
	 }
	 else{ $('#r6,#w6,#d6,#u6').prop('checked',false); }
  });
  $('#r6,#w6,#d6,#u6').click(function(){
	 if($(this).is(':checked')){$('#n6').prop('checked',false); }
	 else{ $('#a6,#n6').prop('checked',false); }
  });
  $('#n6').click(function(){
	 if($(this).is(':checked')){ $('#r6,#w6,#d6,#u6,#a6').prop('checked',false); }
	 else{ }
  });  

// for Assignment Pending
  $('#a7').click(function(){
	 if($(this).is(':checked')){
		 $('#n7').prop('checked',false);
		 $('#r7,#w7,#d7,#u7').prop('checked',true);
	 }
	 else{ $('#r7,#w7,#d7,#u7').prop('checked',false); }
  });
  $('#r7,#w7,#d7,#u7').click(function(){
	 if($(this).is(':checked')){$('#n7').prop('checked',false); }
	 else{ $('#a7,#n7').prop('checked',false); }
  });
  $('#n7').click(function(){
	 if($(this).is(':checked')){ $('#r7,#w7,#d7,#u7,#a7').prop('checked',false); }
	 else{ }
  });  

// for Batch 
  $('#a8').click(function(){
	 if($(this).is(':checked')){
		 $('#n8').prop('checked',false);
		 $('#r8,#w8,#d8,#u8').prop('checked',true);
	 }
	 else{ $('#r8,#w8,#d8,#u8').prop('checked',false); }
  });
  $('#r8,#w8,#d8,#u8').click(function(){
	 if($(this).is(':checked')){ $('#n8').prop('checked',false);}
	 else{ $('#a8,#n8').prop('checked',false); }
  });
  $('#n8').click(function(){
	 if($(this).is(':checked')){ $('#r8,#w8,#d8,#u8,#a8').prop('checked',false); }
	 else{ }
  });  

// for Batch Assignment  
  $('#a9').click(function(){
	 if($(this).is(':checked')){
		 $('#n9').prop('checked',false);
		 $('#r9,#w9,#d9,#u9').prop('checked',true);
	 }
	 else{ $('#r9,#w9,#d9,#u9').prop('checked',false); }
  });
  $('#r9,#w9,#d9,#u9').click(function(){
	 if($(this).is(':checked')){  $('#n9').prop('checked',false);}
	 else{ $('#a9,#n9').prop('checked',false); }
  });
  $('#n9').click(function(){
	 if($(this).is(':checked')){ $('#r9,#w9,#d9,#u9,#a9').prop('checked',false); }
	 else{ }
  });  

// for Student Assignment  
  $('#a10').click(function(){
	 if($(this).is(':checked')){
		 $('#n10').prop('checked',false);
		 $('#r10,#w10,#d10,#u10').prop('checked',true);
	 }
	 else{ $('#r10,#w10,#d10,#u10').prop('checked',false); }
  });
  $('#r10,#w10,#d10,#u10').click(function(){
	 if($(this).is(':checked')){ $('#n10').prop('checked',false);}
	 else{ $('#a10,#n10').prop('checked',false); }
  });
  $('#n10').click(function(){
	 if($(this).is(':checked')){ $('#r10,#w10,#d10,#u10,#a10').prop('checked',false); }
	 else{ }
  });  

// for Branch  
  $('#a11').click(function(){
	 if($(this).is(':checked')){
		 $('#n11').prop('checked',false);
		 $('#r11,#w11,#d11,#u11').prop('checked',true);
	 }
	 else{ $('#r11,#w11,#d11,#u11').prop('checked',false); }
  });
  $('#r11,#w11,#d11,#u11').click(function(){
	 if($(this).is(':checked')){ $('#n11').prop('checked',false);}
	 else{ $('#a11,#n11').prop('checked',false); }
  });
  $('#n11').click(function(){
	 if($(this).is(':checked')){ $('#r11,#w11,#d11,#u11,#a11').prop('checked',false); }
	 else{ }
  });  

// for Printing  
  $('#a12').click(function(){
	 if($(this).is(':checked')){
		 $('#n12').prop('checked',false);
		 $('#r12,#w12,#d12,#u12').prop('checked',true);
	 }
	 else{ $('#r12,#w12,#d12,#u12').prop('checked',false); }
  });
  $('#r12,#w12,#d12,#u12').click(function(){
	 if($(this).is(':checked')){ $('#n12').prop('checked',false);}
	 else{ $('#a12,#n12').prop('checked',false); }
  });
  $('#n12').click(function(){
	 if($(this).is(':checked')){ $('#r12,#w12,#d12,#u12,#a12').prop('checked',false); }
	 else{ }
  });  

// for Motivation
  $('#a13').click(function(){
	 if($(this).is(':checked')){
		 $('#n13').prop('checked',false);
		 $('#r13,#w13,#d13,#u13').prop('checked',true);
	 }
	 else{ $('#r13,#w13,#d13,#u13').prop('checked',false); }
  });
  $('#r13,#w13,#d13,#u13').click(function(){
	 if($(this).is(':checked')){ $('#n13').prop('checked',false); }
	 else{ $('#a13,#n13').prop('checked',false); }
  });
  $('#n13').click(function(){
	 if($(this).is(':checked')){ $('#r13,#w13,#d13,#u13,#a13').prop('checked',false); }
	 else{ }
  });  

// for Permission page
  $('#a14').click(function(){
	 if($(this).is(':checked')){
		 $('#n14').prop('checked',false);
		 $('#r14,#w14,#d14,#u14').prop('checked',true);
	 }
	 else{ $('#r14,#w14,#d14,#u14').prop('checked',false); }
  });
  $('#r14,#w14,#d14,#u14').click(function(){
	 if($(this).is(':checked')){ $('#n14').prop('checked',false);}
	 else{ $('#a14,#n14').prop('checked',false); }
  });
  $('#n14').click(function(){
	 if($(this).is(':checked')){ $('#r14,#w14,#d14,#u14,#a14').prop('checked',false); }
	 else{ }
  });  

// for Notification  
  $('#a15').click(function(){
	 if($(this).is(':checked')){
		 $('#r15,#w15,#d15,#u15').prop('checked',true);
		 $('#n15').prop('checked',false);
	 }
	 else{ $('#r15,#w15,#d15,#u15').prop('checked',false); }
  });
  $('#r15,#w15,#d15,#u15').click(function(){
	 if($(this).is(':checked')){ $('#n15').prop('checked',false);}
	 else{ $('#a15,#n15').prop('checked',false); }
  });
  $('#n15').click(function(){
	 if($(this).is(':checked')){ $('#r15,#w15,#d15,#u15,#a15').prop('checked',false); }
	 else{ }
  });  
// for Lead Generation  
  $('#a16').click(function(){
	 if($(this).is(':checked')){
		 $('#r16,#w16,#d16,#u16').prop('checked',true);
		 $('#n16').prop('checked',false);
	 }
	 else{ $('#r16,#w16,#d16,#u16').prop('checked',false); }
  });
  $('#r16,#w16,#d16,#u16').click(function(){
	 if($(this).is(':checked')){ $('#n16').prop('checked',false);}
	 else{ $('#a16,#n16').prop('checked',false); }
  });
  $('#n16').click(function(){
	 if($(this).is(':checked')){ $('#r16,#w16,#d16,#u16,#a16').prop('checked',false); }
	 else{ }
  });  
// for Associate Registration  
  $('#a17').click(function(){
	 if($(this).is(':checked')){
		 $('#r17,#w17,#d17,#u17').prop('checked',true);
		 $('#n17').prop('checked',false);
	 }
	 else{ $('#r17,#w17,#d17,#u17').prop('checked',false); }
  });
  $('#r17,#w17,#d17,#u17').click(function(){
	 if($(this).is(':checked')){ $('#n17').prop('checked',false);}
	 else{ $('#a17,#n17').prop('checked',false); }
  });
  $('#n17').click(function(){
	 if($(this).is(':checked')){ $('#r17,#w17,#d17,#u17,#a17').prop('checked',false); }
	 else{ }
  });  
// for State Master  
  $('#a18').click(function(){
	 if($(this).is(':checked')){
		 $('#r18,#w18,#d18,#u18').prop('checked',true);
		 $('#n18').prop('checked',false);
	 }
	 else{ $('#r18,#w18,#d18,#u18').prop('checked',false); }
  });
  $('#r18,#w18,#d18,#u18').click(function(){
	 if($(this).is(':checked')){ $('#n18').prop('checked',false);}
	 else{ $('#a18,#n18').prop('checked',false); }
  });
  $('#n18').click(function(){
	 if($(this).is(':checked')){ $('#r18,#w18,#d18,#u18,#a18').prop('checked',false); }
	 else{ }
  });  
// for City Master  
  $('#a19').click(function(){
	 if($(this).is(':checked')){
		 $('#r19,#w19,#d19,#u19').prop('checked',true);
		 $('#n19').prop('checked',false);
	 }
	 else{ $('#r19,#w19,#d19,#u19').prop('checked',false); }
  });
  $('#r19,#w19,#d19,#u19').click(function(){
	 if($(this).is(':checked')){ $('#n19').prop('checked',false);}
	 else{ $('#a19,#n19').prop('checked',false); }
  });
  $('#n19').click(function(){
	 if($(this).is(':checked')){ $('#r19,#w19,#d19,#u19,#a19').prop('checked',false); }
	 else{ }
  });  
  
    
  function deletedata(ud){
	  if(confirm("Do you really want to delete entries for "+ud)){
		  var loc = "permissions.php?flg=del&user=" + ud;
          window.location = loc;
	  }
  }
  function editdata(ud){
	$('#btnsubmit').attr('hidden',true);
	$('#btnupdate').removeAttr('hidden');
	$('#btnsubmit').attr('disabled',true);
  	window.location="permissions.php?desig="+ud;      
  }
  
  function viewdata(ud){
	$('#btnsubmit').attr('hidden',true);
	$('#btnupdate').attr('hidden',true);
	$('#btnsubmit').attr('disabled',true);
  	window.location="permissions.php?designation="+ud;      
  }
</script>


<?php 
include("footer.php");
?>