<?php
include("header.php");
//123s
// for permission
if($row=checkPermission($_SESSION["utype"],"student_reg")){ }
else{
	header("location:home.php");
}

$stmt_slist = $obj->con1->prepare("select * from state where status='enable'");
$stmt_slist->execute();
$res = $stmt_slist->get_result();
$stmt_slist->close();


// generate random password
function password_generate($chars) 
{
  $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
  return substr(str_shuffle($data), 0, $chars);
}


$stmt_slist = $obj->con1->prepare("select * from skill");
$stmt_slist->execute();
$res1 = $stmt_slist->get_result();
$stmt_slist->close();

$stmt_clist = $obj->con1->prepare("select * from course");
$stmt_clist->execute();
$res2 = $stmt_clist->get_result();
$stmt_clist->close();

$stmt_batch = $obj->con1->prepare("select b1.*,c1.coursename from batch b1,course c1 where b1.course_id=c1.courseid");
$stmt_batch->execute();
$res_batch = $stmt_batch->get_result();
$stmt_batch->close();


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $sname=$_REQUEST['sname'];
  $email = $_REQUEST['email'];
  $gender=$_REQUEST['gender'];
  $password = $_REQUEST['password'];
  $house_no = $_REQUEST['house_no'];
  $society = $_REQUEST['society_name'];
  $village = $_REQUEST['village'];
  $landmark = $_REQUEST['landmark'];
  $city = $_REQUEST['city'];
  $state = $_REQUEST['state'];
  $pin_code = $_REQUEST['pin'];
  $education = $_REQUEST['education'];
  $stu_type = $_REQUEST['stu_type'];
  $userid = $_REQUEST['userid'];
  $guard_name = $_REQUEST['guard_name'];
  $contact = $_REQUEST['contact'];
  $guardian_contact = $_REQUEST['guardian_contact'];
  $birthdate = $_REQUEST['dob'];
  $inquiry_dt = $_REQUEST['inquiry_dt'];
  $enrollment_dt = $_REQUEST['enrollment_dt'];
  $btime = $_REQUEST['btime'];

  $skills =$_REQUEST['skills'];
  $course = $_REQUEST['course'];
  $pic = $_FILES['profile_pic']['name'];
  $p_path = $_FILES['profile_pic']['tmp_name'];
  $aadhar = $_FILES['aadhar']['name'];
  $a_path = $_FILES['aadhar']['tmp_name'];
  $status=$_REQUEST['status'];
  $student_status="ongoing";

	//rename file for profile pic
	if ($_FILES["profile_pic"]["name"] != "")
  	{
      if(file_exists("studentProfilePic/" . $pic)) {
          $i = 0;
          $PicFileName = $_FILES["profile_pic"]["name"];
          $Arr1 = explode('.', $PicFileName);

          $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
          while (file_exists("studentProfilePic/" . $PicFileName)) {
              $i++;
              $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
          }
	   } 
	   else {
          $PicFileName = $_FILES["profile_pic"]["name"];
      }
  	}

	//rename file for aadhar card
	if ($_FILES["aadhar"]["name"] != "")
  	{
      if (file_exists("studentAadharCard/" . $aadhar)) {
          $j = 0;
          $AadFileName = $_FILES["aadhar"]["name"];
          $Arr2 = explode('.', $AadFileName);

          $AadFileName = $Arr2[0] . $j . "." . $Arr2[1];
          while (file_exists("studentAadharCard/" . $AadFileName)) {
              $j++;
              $AadFileName = $Arr2[0] . $j . "." . $Arr2[1];
          }
	   } 
	   else {
          $AadFileName = $_FILES["aadhar"]["name"];
      }
  	}

  try
  {
  	$stmt = $obj->con1->prepare("INSERT INTO `student`(`name`, `email`,`gender`, `house_no`, `society_name`, `village`, `landmark`, `city`, `state`, `pin`, `education`, `stu_type`, `guard_name`, `user_id`, `password`, `phone`, `guardian_phone`, `dob`, `inquiry_dt`, `enrollment_dt`, `pic`, `aadhar`,`status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
  	$stmt->bind_param("sssssssiissssssssssssss", $sname,$email,$gender,$house_no,$society,$village,$landmark,$city,$state,$pin_code,$education,$stu_type,$guard_name,$userid,$password,$contact,$guardian_contact,$birthdate,$inquiry_dt,$enrollment_dt,$PicFileName,$AadFileName,$status);
  	$Resp=$stmt->execute();
    $lastId = mysqli_insert_id($obj->con1);

  // insert into batch assign
    $stmt_batch_assign = $obj->con1->prepare("INSERT INTO `batch_assign`( `batch_id`, `student_id`,`student_status`) VALUES (?,?,?)");
    $stmt_batch_assign->bind_param("iis", $btime,$lastId,$student_status);
    $Resp=$stmt_batch_assign->execute();
    
    $stmt_batch_assign->close();


    // insert into skills 
    for($i=0;$i<count($skills);$i++)
    {
      $stmt_skills = $obj->con1->prepare("INSERT INTO `stu_skills`(  `stu_id`, `skill_id`) VALUES (?,?)");
      $stmt_skills->bind_param("ii", $lastId,$skills[$i]);
      $Resp_skill=$stmt_skills->execute();
      $stmt_skills->close();
    }
    

    // insert into courses 
    for($i=0;$i<count($course);$i++)
    {
      $stmt_course = $obj->con1->prepare("INSERT INTO `stu_course`( `stu_id`, `course_id`) VALUES (?,?)");
      $stmt_course->bind_param("ii", $lastId,$course[$i]);
      $Resp_course=$stmt_course->execute();
      $stmt_course->close();
    } 

	if(!$Resp)
	{
      throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
	  move_uploaded_file($p_path,"studentProfilePic/".$PicFileName);
	  move_uploaded_file($a_path,"studentAadharCard/".$AadFileName);
	
	  setcookie("msg", "data",time()+3600,"/");
      header("location:student_reg.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:student_reg.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $sname=$_REQUEST['sname'];
  $email = $_REQUEST['email'];
  $gender=$_REQUEST['gender'];
  $password = $_REQUEST['password'];
  $house_no = $_REQUEST['house_no'];
  $society = $_REQUEST['society_name'];
  $village = $_REQUEST['village'];
  $landmark = $_REQUEST['landmark'];
  $city = $_REQUEST['city'];
  $state = $_REQUEST['state'];
  $pin_code = $_REQUEST['pin'];
  $education = $_REQUEST['education'];
  $stu_type = $_REQUEST['stu_type'];
  $userid = $_REQUEST['userid'];
  $guard_name = $_REQUEST['guard_name'];
  $contact = $_REQUEST['contact'];
  $guardian_contact = $_REQUEST['guardian_contact'];
  $birthdate = $_REQUEST['dob'];
  $inquiry_dt = $_REQUEST['inquiry_dt'];
  $enrollment_dt = $_REQUEST['enrollment_dt'];
  $btime = $_REQUEST['btime'];
  $skills = $_REQUEST['skills'];
  $course = $_REQUEST['course'];
  $rpp= $_REQUEST['hprofile_pic'];
  $raadhar= $_REQUEST['haadhar'];
  $pp=$_FILES['profile_pic']['name'];
  $srcpp=$_FILES['profile_pic']['tmp_name'];
  $aadhar=$_FILES['aadhar']['name'];
  $srcaadhar=$_FILES['aadhar']['tmp_name'];
  $id = $_REQUEST['ttId'];
  $status=$_REQUEST['status'];
  $student_status="ongoing";
  if($pp!=""){
	unlink("studentProfilePic/".$rpp);	
	
	//rename file for profile pic
	if ($_FILES["profile_pic"]["name"] != "")
  	{
      if(file_exists("studentProfilePic/" . $pic)) {
          $i = 0;
          $PicFileName = $_FILES["profile_pic"]["name"];
          $Arr1 = explode('.', $PicFileName);

          $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
          while (file_exists("studentProfilePic/" . $PicFileName)) {
              $i++;
              $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
          }
	   } 
	   else {
          $PicFileName = $_FILES["profile_pic"]["name"];
      }
  	}
	
	move_uploaded_file($srcpp,"studentProfilePic/".$PicFileName);
  }
  else{
	$PicFileName=$rpp;
  }

  if($aadhar!=""){
	unlink("studentAadharCard/".$raadhar);

	//rename file for aadhar card
	if ($_FILES["aadhar"]["name"] != "")
  	{
      if (file_exists("studentAadharCard/" . $aadhar)) {
          $j = 0;
          $AadFileName = $_FILES["aadhar"]["name"];
          $Arr2 = explode('.', $AadFileName);

          $AadFileName = $Arr2[0] . $j . "." . $Arr2[1];
          while (file_exists("studentAadharCard/" . $AadFileName)) {
              $j++;
              $AadFileName = $Arr2[0] . $j . "." . $Arr2[1];
          }
	   } 
	   else {
          $AadFileName = $_FILES["aadhar"]["name"];
      }
  	}

	move_uploaded_file($srcaadhar,"studentAadharCard/".$AadFileName);	
  }
  else{
	$AadFileName=$raadhar;
  }

  try
  {
    $stmt = $obj->con1->prepare("update student set name=?, email=?, gender=?, house_no=?, society_name=?, village=?, landmark=?, city=?, state=?, pin=?, education=?, stu_type=?, guard_name=?, user_id=?, password=?, phone=?, guardian_phone=?, dob=?, inquiry_dt=?, enrollment_dt=?,  pic=?, aadhar=?,`status`=? where sid=?");
  	$stmt->bind_param("sssssssiissssssssssssssi", $sname,$email,$gender,$house_no,$society,$village,$landmark,$city,$state,$pin_code,$education,$stu_type,$guard_name,$userid,$password,$contact,$guardian_contact,$birthdate,$inquiry_dt,$enrollment_dt,$PicFileName,$AadFileName,$status,$id);
  	$Resp=$stmt->execute();

    // update skills

    $del_skill_stmt=$obj->con1->prepare("delete from stu_skills where stu_id=?");//delete old skills first
    $del_skill_stmt->bind_param("i",$id);
    $del_skill_stmt->execute();
    $del_skill_stmt->close();

    //add new skills
    for($i=0;$i<count($skills);$i++)
    {
      $stmt_skills = $obj->con1->prepare("INSERT INTO `stu_skills`(  `stu_id`, `skill_id`) VALUES (?,?)");
      $stmt_skills->bind_param("ii", $id,$skills[$i]);
      $Resp_skill=$stmt_skills->execute();
      $stmt_skills->close();
    }


    //update courses
    $del_course_stmt=$obj->con1->prepare("delete from stu_course where stu_id=?");//delete old course first
    $del_course_stmt->bind_param("i",$id);
    $del_course_stmt->execute();
    $del_course_stmt->close();

    // insert new courses 
    for($i=0;$i<count($course);$i++)
    {
      $stmt_course = $obj->con1->prepare("INSERT INTO `stu_course`( `stu_id`, `course_id`) VALUES (?,?)");
      $stmt_course->bind_param("ii", $id,$course[$i]);
      $Resp_course=$stmt_course->execute();
      $stmt_course->close();
    } 



   // echo "select id,count(*) from batch_assign where student_id=? and batch_id=!37";
    // check if student is assigned in any batch
    
    $stmt_batch_select = $obj->con1->prepare("select id,count(*) from batch_assign where student_id=? and batch_id=37");
    $stmt_batch_select->bind_param("i", $id);
    $stmt_batch_select->execute();
    $res = $stmt_batch_select->get_result();
    $stmt_batch_select->close();
    $row = mysqli_fetch_array($res);
    if($row[1]==0)
    {
      //echo "INSERT INTO `batch_assign`( `batch_id`, `student_id`) VALUES ( '$btime','$id')";
      // insert into batch assign
      $stmt_batch_assign = $obj->con1->prepare("INSERT INTO `batch_assign`( `batch_id`, `student_id`,`student_status`) VALUES (?,?,?)");
      $stmt_batch_assign->bind_param("iis", $btime,$id,$student_status);
      $Resp_batch=$stmt_batch_assign->execute();
      $stmt_batch_assign->close();
    }
    else
    {
      
      // update batch assign
      $stmt_batch = $obj->con1->prepare("update batch_assign set batch_id=? where student_id=?");
      $stmt_batch->bind_param("ii",$btime, $id);
      $Resp_batch=$stmt_batch->execute();
      $stmt_batch->close();
    }
    
	if(!$Resp)
	{
      throw new Exception("Problem in update! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
	  setcookie("msg", "update",time()+3600,"/");
    header("location:student_reg.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
     header("location:student_reg.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  $pic = $_REQUEST["pic"];
  $adc = $_REQUEST["adc"];
  
  try
  {
    $stmt_del = $obj->con1->prepare("delete from student where sid='".$_REQUEST["n_id"]."'");
  	$Resp=$stmt_del->execute();

    // delete from batch_assign
    $stmt_del_batch = $obj->con1->prepare("delete from batch_assign where student_id='".$_REQUEST["n_id"]."'");
    $Resp_batch=$stmt_del_batch->execute();
    $stmt_del_batch->close();

    //delete from course
    $del_course_stmt=$obj->con1->prepare("delete from stu_course where stu_id=?");
    $del_course_stmt->bind_param("i",$_REQUEST["n_id"]);
    $del_course_stmt->execute();
    $del_course_stmt->close();
    // delete from skills
    $del_skill_stmt=$obj->con1->prepare("delete from stu_skills where stu_id=?");
    $del_skill_stmt->bind_param("i",$_REQUEST["n_id"]);
    $del_skill_stmt->execute();
    $del_skill_stmt->close();
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
  	if(file_exists("studentProfilePic/".$pic)){
		unlink("studentProfilePic/".$pic);
	}
  	if(file_exists("studentAadharCard/".$adc)){
		unlink("studentAadharCard/".$adc);
  	}
	setcookie("msg", "data_del",time()+3600,"/");
    header("location:student_reg.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:student_reg.php");
  }
}

?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">

  function cityList(state){
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=cityList",
          data: "state_id="+state,
          cache: false,
          success: function(result){
           
            $('#city').html('');
            $('#city').append(result);
       
            }
        });
  }

</script>

<h4 class="fw-bold py-3 mb-4">Admission</h4>


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
                      <h5 class="mb-0">Add Student</h5>
                      
                      
                    </div>
                    <div class="card-body">
                      <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Name</label>
                          <input type="text" class="form-control" name="sname" id="sname" required />
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Email</label>
                          <input type="text" class="form-control" name="email" id="email" required />
                        </div>
                        <div class="mb-3">
                          <label class="form-label d-block" for="basic-default-fullname">Gender</label>
                          
                        <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male" required >
                            <label class="form-check-label" for="inlineRadio1">Male</label>
                        </div>
                        <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female" required>
                            <label class="form-check-label" for="inlineRadio1">Female</label>
                        </div>
                        </div>
						
                          <label class="form-label" for="basic-default-message">Address :</label>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Flat / House No.</label>
                          <input type="text" class="form-control" name="house_no" id="house_no" required />
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Society Name</label>
                          <input type="text" class="form-control" name="society_name" id="society_name" required />
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Village</label>
                          <input type="text" class="form-control" name="village" id="village" />
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Landmark</label>
                          <input type="text" class="form-control" name="landmark" id="landmark" />
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">State</label>
                          <select name="state" id="state" onchange="cityList(this.value)" class="form-control" required>
                          	<option value="">Select State</option>
                    <?php    
                        while($state=mysqli_fetch_array($res)){
                    ?>
                    		<option value="<?php echo $state["state_id"] ?>"><?php echo $state["state_name"] ?></option>
                    <?php
						}
					?>
					      </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">City</label>
                          <select name="city" id="city" class="form-control" required>
                            <option value="">Select City</option>
                		  </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Pin Code</label>
                          <input type="text" class="form-control" name="pin" id="pin" required />
                        </div>
                        
						<div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Education</label>
                          <input type="text" class="form-control" name="education" id="education" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Student's Level</label>
                          <select name="stu_type" id="stu_type" class="form-control" required>
                          	<option value="">Select</option>
                            <option value="good">Good</option>
                            <option value="average">Average</option>
                            <option value="poor">Poor</option>
                          </select>
                        </div>
						
						<div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">User ID (Roll No.)</label>
                          <input type="text" class="form-control" name="userid" id="userid" required onkeyup ="check_stu_roll(this.value)" />
                          <div id="stu_alert_div" class="text-danger"></div>
                        </div>
                        
						<div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Password</label>
                          <input type="text" min="6" class="form-control" name="password" id="password" required value="<?php echo password_generate(6)?>" />
                        </div>
						
						<div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Guardian Name</label>
                          <input type="text" class="form-control" name="guard_name" id="guard_name" required />
                        </div>
						
						<div class="mb-3">
                          <label class="form-label" for="basic-default-company">Contact No.</label>
                          <input type="tel" pattern="[0-9]{10}" class="form-control phone-mask" id="contact" name="contact"  required/>
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Guardian's Contact No.</label>
                          <input type="tel" pattern="[0-9]{10}" class="form-control phone-mask" id="guardian_contact" name="guardian_contact"  required/>
                        </div>
						
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Date of Birth</label>
                          <input type="date" class="form-control" name="dob" id="dob" required/>
                        </div>
                        
						            <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Inquiry Date</label>
                          <input type="date" class="form-control" name="inquiry_dt" id="inquiry_dt" required value="<?php echo date('Y-m-d')?>" />
                        </div>
						
						            <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Enrollment Date</label>
                          <input type="date" class="form-control" name="enrollment_dt" id="enrollment_dt" required />
                        </div>
						
						            <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Batch Timing</label>
                          
                          <select name="btime" id="btime" class="form-control" required onchange="get_faculty(this.value)">
                            <option value="">Select</option>
                            <?php
                            while($batch=mysqli_fetch_array($res_batch))
                            {
                              ?>
                              <option value="<?php echo $batch["id"]?>"><?php echo $batch["stime"]."-".$batch["name"]."-".$batch["coursename"]?></option>
                              <?php
                            }
                            ?>
                            
                            
                          </select>
                        </div>
                        <div id="faculty_div">

                          
                        </div>
                        <div id="stu_course_div">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Course Enrolled</label>
                          <select name="course[]" id="course" class="form-control js-example-basic-multiple" required multiple="multiple">
                            <option value="">Select All</option>
                            <?php while($c=mysqli_fetch_array($res2)){ ?>
                                <option value="<?php echo $c["courseid"] ?>"><?php echo $c["coursename"] ?></option>
                            <?php } ?>
                          </select>
                        </div>
						
						            <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Skills</label>
                          <select name="skills[]" id="skills" class="form-control js-example-basic-multiple" required multiple="multiple">
                          	<option value="">Select All</option>
					<?php while($s=mysqli_fetch_array($res1)){ ?>
                    		<option value="<?php echo $s["skid"] ?>"><?php echo $s["skills"] ?></option>
                    <?php }	?>
                          </select>
                        </div>
                      </div>
						
						<div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Profile Pic</label>
                          <input type="file" class="form-control" onchange="readURL_p(this)" name="profile_pic" id="profile_pic" required />
                          <img src="" name="PreviewImageP" id="PreviewImageP" width="100" height="100" style="display:none;">
                          <div id="imgdiv" style="color:red"></div>
                          <input type="hidden" name="hprofile_pic" id="hprofile_pic" />
                        </div>
						
						<div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Aadhar Card</label>
                          <input type="file" class="form-control" onchange="readURL_a(this)" name="aadhar" id="aadhar" required />
                          <img src="" name="PreviewImageA" id="PreviewImageA" width="100" height="100" style="display:none;">
                          <div id="imgac" style="color:red"></div>
                          <input type="hidden" name="haadhar" id="haadhar" /> 
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Status</label>
                          <select name="status" id="status" class="form-control" required>
                            <option value="">Select</option>
                             <option value="inquiry">Inquiry</option>
                             <option value="registered">Registered</option>
                          </select>
                        </div>
						
                    <?php if($row["write_func"]=="y"){ ?>
						            <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>
                    <?php } if($row["upd_func"]=="y"){ ?>
                        <button type="submit" name="btnupdate" id="btnupdate" class="btn btn-primary " hidden>Update</button>
                    <?php } ?>
                       <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='student_reg.php'">Cancel</button>

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
                <h5 class="card-header">Student Records</h5>
                <div class="card-header">
               
<div class="form-check form-check-inline">
    <input class="form-check-input" type="radio" name="stu_option" id="opt_reg" value="reg" onchange="get_stu_grid(this.value)" <?php echo (!isset($_COOKIE["stu_reg_opt"]) || $_COOKIE['stu_reg_opt']=="reg")?"checked":""?>>
    <label class="form-check-label" for="inlineRadio1">Registered</label>
</div>
<div class="form-check form-check-inline ">
    <input class="form-check-input" type="radio" name="stu_option" id="opt_inquiry" value="inquiry" onchange="get_stu_grid(this.value)" <?php echo (isset($_COOKIE["stu_reg_opt"]) && $_COOKIE['stu_reg_opt']=="inquiry")?"checked":""?>>
    <label class="form-check-label" for="inlineRadio1">Inquiry</label>
</div>
 <div class="form-check form-check-inline ">
    <input class="form-check-input" type="radio" name="stu_option" id="opt_all" value="all" <?php echo (isset($_COOKIE["stu_reg_opt"]) && $_COOKIE['stu_reg_opt']=="all")?"checked":""?> onchange="get_stu_grid(this.value)">
    <label class="form-check-label" for="inlineRadio1">All</label>
</div>
</div>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Roll No.</th>
                        <th>Name</th>
                        
                        <th>Contact No.</th>
                        <th>Enrollment Date</th>
                        <th>Batch Time</th>
                        
                        <th>Course</th>
                        <th>Status</th>
                        <th>Actions</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="grid">
                      <?php 
                      if(isset($_COOKIE["stu_reg_opt"]) && $_COOKIE['stu_reg_opt']=="all")
                      {
                        $stmt_list = $obj->con1->prepare("select st.*,GROUP_CONCAT(c.coursename)as coursename,GROUP_CONCAT(b2.stime) as batch_time,b2.name as batch_name,b2.id as bid from student as st, course as c,batch_assign b1,batch b2,stu_course sc  where sc.course_id=c.courseid and sc.stu_id=st.sid   and b1.batch_id=b2.id and b1.student_id=st.sid and st.status='registered'  GROUP by st.sid       UNION
select st.*,GROUP_CONCAT(c.coursename)as coursename,'-' as batch_time,'-' as batch_name,1 as bid from student as st,course as c,stu_course sc where sc.course_id=c.courseid and sc.stu_id=st.sid  and st.status='inquiry'  GROUP by sc.stu_id order by sid desc");
                      }
                      else if(isset($_COOKIE["stu_reg_opt"]) && $_COOKIE['stu_reg_opt']=="inquiry")
                      {
                        $stmt_list = $obj->con1->prepare("select st.*,GROUP_CONCAT(c.coursename)as coursename from student as st,course as c,stu_course sc where sc.course_id=c.courseid and sc.stu_id=st.sid  and st.status='inquiry'  GROUP by sc.stu_id order by sid desc");
                      }
                      else
                      {
                        
                        $stmt_list = $obj->con1->prepare("select st.*,GROUP_CONCAT(c.coursename)as coursename,GROUP_CONCAT(b2.stime) as batch_time,b2.name as batch_name,b2.id as bid from student as st, course as c,batch_assign b1,batch b2,stu_course sc where    sc.stu_id=st.sid and sc.course_id=c.courseid  and b1.batch_id=b2.id and b1.student_id=st.sid  and st.status='registered' GROUP by st.sid");
                      }
                        
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($s=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $s["user_id"]?></td>
                        <td><?php echo $s["name"]?></td>
                        
                        <td><?php echo $s["phone"]?></td>
                        <td><?php echo ($s["enrollment_dt"]!="0000-00-00")?date("d-m-Y", strtotime($s["enrollment_dt"])):"-"?></td>
                        <td><?php echo $s["batch_time"].'-'.$s["batch_name"]?></td>
                        
                        <td><?php echo $s["coursename"]?></td>
                        <td style="color:<?php echo ($s["status"]=='inquiry')?'red':''?>">
                          <?php 
                          if($s["status"]!="")
                          {
                            echo $s["status"];
                          }
                          else
                          {
                            echo "-";
                          }
                          
                        ?></td>
                        
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a  href="javascript:editdata('<?php echo $s["sid"]?>','<?php echo base64_encode($s["name"])?>','<?php echo base64_encode($s["email"])?>','<?php echo $s["gender"]?>','<?php echo base64_encode($s["password"])?>','<?php echo base64_encode($s["house_no"])?>','<?php echo base64_encode($s["society_name"])?>','<?php echo base64_encode($s["village"])?>','<?php echo base64_encode($s["landmark"])?>','<?php echo $s["city"]?>','<?php echo $s["state"]?>','<?php echo base64_encode($s["pin"])?>','<?php echo base64_encode($s["education"])?>','<?php echo base64_encode($s["stu_type"])?>','<?php echo base64_encode($s["user_id"])?>','<?php echo base64_encode($s["guard_name"])?>','<?php echo $s["phone"]?>','<?php echo $s["guardian_phone"]?>','<?php echo base64_encode($s["dob"])?>','<?php echo base64_encode($s["inquiry_dt"])?>','<?php echo base64_encode($s["enrollment_dt"])?>','<?php echo base64_encode($s["bid"])?>','<?php echo $s["skillid"]?>','<?php echo $s["courseid"]?>','<?php echo base64_encode($s["pic"])?>','<?php echo base64_encode($s["aadhar"])?>','<?php echo $s["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $s["sid"]?>','<?php echo base64_encode($s["pic"])?>','<?php echo base64_encode($s["aadhar"])?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                        	<a  href="javascript:viewdata('<?php echo $s["sid"]?>','<?php echo base64_encode($s["name"])?>','<?php echo base64_encode($s["email"])?>','<?php echo $s["gender"]?>','<?php echo base64_encode($s["password"])?>','<?php echo base64_encode($s["house_no"])?>','<?php echo base64_encode($s["society_name"])?>','<?php echo base64_encode($s["village"])?>','<?php echo base64_encode($s["landmark"])?>','<?php echo $s["city"]?>','<?php echo $s["state"]?>','<?php echo base64_encode($s["pin"])?>','<?php echo base64_encode($s["education"])?>','<?php echo base64_encode($s["stu_type"])?>','<?php echo base64_encode($s["user_id"])?>','<?php echo base64_encode($s["guard_name"])?>','<?php echo $s["phone"]?>','<?php echo $s["guardian_phone"]?>','<?php echo base64_encode($s["dob"])?>','<?php echo base64_encode($s["inquiry_dt"])?>','<?php echo base64_encode($s["enrollment_dt"])?>','<?php echo base64_encode($s["bid"])?>','<?php echo $s["skillid"]?>','<?php echo $s["courseid"]?>','<?php echo base64_encode($s["pic"])?>','<?php echo base64_encode($s["aadhar"])?>','<?php echo $s["status"]?>');">View</a>
                        <?php } ?>
                        </td>
                    <?php } ?>
                        <td>  
                    <?php if($s["status"]=='registered'){ ?>
                          <a href="javascript:addMoreInfo('<?php echo $s["sid"]?>')">Add More Info</a>
                    <?php } ?>
                        </td>
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

  function addMoreInfo(id) {
      document.cookie = "sid="+id;
      var loc = "student_extra_info.php";
      window.location = loc;
  }

  $(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});
  function check_stu_roll(stu_roll)
  {
    
    var id=$('#ttId').val();
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=check_stu_roll",
          data: "stu_roll="+stu_roll+"&id="+id,
          cache: false,
          success: function(result){
            if(result>0)
            {
              $('#stu_alert_div').html('Roll No. already exists');
              document.getElementById('btnsubmit').disabled = true;
              document.getElementById('btnupdate').disabled = true;
              
            }
            else
            {
              $('#stu_alert_div').html('');
              document.getElementById('btnsubmit').disabled = false;
              document.getElementById('btnupdate').disabled = false;
              
            }
           
            
       
            }
        });
  }
  function get_stu_grid(opt) {
    
    createCookie("stu_reg_opt",opt,1);
   // createCookie("stu_opt_grid","1",1);
    window.location=window.location.href;
  }

	function readURL_p(input) {
	    if (input.files && input.files[0]) {
    	    var filename=input.files.item(0).name;

        	var reader = new FileReader();
	        var extn=filename.split(".");

           if(extn[1].toLowerCase()=="jpg" || extn[1].toLowerCase()=="jpeg" || extn[1].toLowerCase()=="png" || extn[1].toLowerCase()=="bmp") {
    		    reader.onload = function (e) {
            		$('#PreviewImageP').attr('src', e.target.result);
          			  document.getElementById("PreviewImageP").style.display = "block";
		        };

        		reader.readAsDataURL(input.files[0]);
	    	    $('#imgdiv').html("");
    	    	document.getElementById('btnsubmit').disabled = false;
			}
    		else
	    	{
			      $('#imgdiv').html("Please Select Image Only");
      			  document.getElementById('btnsubmit').disabled = true;
    		}
		}
	}

	function readURL_a(input) {
	    if (input.files && input.files[0]) {
    	    var filename=input.files.item(0).name;

        	var reader = new FileReader();
	        var extn=filename.split(".");

           if(extn[1].toLowerCase()=="jpg" || extn[1].toLowerCase()=="jpeg" || extn[1].toLowerCase()=="png" || extn[1].toLowerCase()=="bmp") {
    		    reader.onload = function (e) {
            		$('#PreviewImageA').attr('src', e.target.result);
          			  document.getElementById("PreviewImageA").style.display = "block";
		        };

        		reader.readAsDataURL(input.files[0]);
	    	    $('#imgac').html("");
    	    	document.getElementById('btnsubmit').disabled = false;
			}
    		else
	    	{
			      $('#imgac').html("Please Select Image Only");
      			  document.getElementById('btnsubmit').disabled = true;
    		}
		}
	}

  function deletedata(id,pi,ac) {
      if(confirm("Are you sure to DELETE data?")) {
          var loc = "student_reg.php?flg=del&n_id="+id+"&pic="+atob(pi)+"&adc="+atob(ac);
          window.location = loc;
      }
  }
  
  function editdata(id,name,email,gender,password,house_no,society,village,landmark,city,state,pin,education,stu_type,userid,gname,phone,gphone,dob,inquiry,enrollment,btime,skill,course,pic,aadhar,status) {         
		$('#ttId').val(id);
    $('#sname').focus();
		$('#sname').val(atob(name));
		$('#email').val(atob(email));
		$('#house_no').val(atob(house_no));
		$('#society_name').val(atob(society));
		$('#village').val(atob(village));
		$('#landmark').val(atob(landmark));
		$('#state').val(state);
		
    cityList(state);
		setTimeout(function() {
      		$('#city').val(city);
		}, 1000);
      	$('#pin').val(atob(pin));
		$('#education').val(atob(education));
		$('#stu_type').val(atob(stu_type));
		$('#userid').val(atob(userid));
    if(password=="")
    {
      var pass=generatePassword();
      
      $('#password').val(pass);
    }
    else
    {
      $('#password').val(atob(password));
    }

		$('#guard_name').val(atob(gname));
		$('#contact').val(phone);
		$('#guardian_contact').val(gphone);
		$('#dob').val(atob(dob));
		$('#inquiry_dt').val(atob(inquiry));
		$('#enrollment_dt').val(atob(enrollment));
		$('#btime').val(atob(btime));
		$('#skills').val(skill);
		$('#course').val(course);
    $('#status').val(status);
		$('#hprofile_pic').val(atob(pic));
		$('#haadhar').val(atob(aadhar));
		$('#PreviewImageP').show();
		$('#PreviewImageP').attr('src','studentProfilePic/'+atob(pic));		
		$('#profile_pic').removeAttr('required');
		
		$('#PreviewImageA').show();
		$('#PreviewImageA').attr('src','studentAadharCard/'+atob(aadhar));
		$('#aadhar').removeAttr('required');
    if(gender=="female")
      {
        //document.getElementById('gender_female').checked=true;
        $('#gender_female').attr("checked","checked");  
      }
      else
      {
        //document.getElementById('gender_male').checked=true;
        $('#gender_male').attr("checked","checked");  
      }
		
		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').removeAttr('hidden');

    // get skills & course
     $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=get_stu_skills",
          data: "stu_id="+id,
          cache: false,
          success: function(result){
           
            $('#stu_course_div').html('');
            $('#stu_course_div').append(result);
            $('.js-example-basic-multiple').select2();
            }
        });

	}
	
	function viewdata(id,name,email,gender,password,house_no,society,village,landmark,city,state,pin,education,stu_type,userid,gname,phone,gphone,dob,inquiry,enrollment,btime,skill,course,pic,aadhar,status) {         
		$('#ttId').val(id);
		$('#sname').val(atob(name));
		$('#email').val(atob(email));
		if(gender=="female")
	  {
		//document.getElementById('gender_female').checked=true;
		$('#gender_female').attr("checked","checked");  
	  }
	  else
	  {
		//document.getElementById('gender_male').checked=true;
		$('#gender_male').attr("checked","checked");  
	  }
		$('#house_no').val(atob(house_no));
		$('#society_name').val(atob(society));
		$('#village').val(atob(village));
		$('#landmark').val(atob(landmark));
		$('#state').val(state);
		cityList(state);
		setTimeout(function() {
      		$('#city').val(city);
		}, 1000);
      	$('#pin').val(atob(pin));
		$('#education').val(atob(education));
		$('#stu_type').val(atob(stu_type));
		$('#userid').val(atob(userid));
    if(password=="")
    {
      var pass=generatePassword();
      console.log("pass="+pass);
      $('#password').val(pass);
    }
    else
    {
      $('#password').val(atob(password));
    }
		
		$('#guard_name').val(atob(gname));
		$('#contact').val(phone);
		$('#guardian_contact').val(gphone);
		$('#dob').val(atob(dob));
		$('#inquiry_dt').val(atob(inquiry));
		$('#enrollment_dt').val(atob(enrollment));
		$('#btime').val(atob(btime));
		$('#skills').val(skill);
		$('#course').val(course);
    $('#status').val(status);
		$('#hprofile_pic').val(atob(pic));
		$('#haadhar').val(atob(aadhar));
		$('#PreviewImageP').show();
		$('#PreviewImageP').attr('src','studentProfilePic/'+atob(pic));		
		$('#profile_pic').removeAttr('required');
		
		$('#PreviewImageA').show();
		$('#PreviewImageA').attr('src','studentAadharCard/'+atob(aadhar));
		$('#aadhar').removeAttr('required');
		
		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').attr('hidden',true);
		$('#btnsubmit').attr('disabled',true);
	}
  function get_faculty(batch)
  {
      $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=get_faculty",
          data: "batch="+batch,
          cache: false,
          success: function(result){
           
            $('#faculty_div').html('');
            $('#faculty_div').append(result);
       
            }
        });
  }
  function generatePassword() {
    var length = 6,
        charset = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz",
        retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    return retVal;
}

$('#skills').on("select2:select", function (e) { 
    
           var data = e.params.data.text;
           if(data=='Select All'){
            $("#skills > option").prop("selected","selected");
            var sel_all = $('#skills option[value=""]');
            sel_all.prop('selected', false);
            $("#skills").trigger("change");
           }
      });
$('#course').on("select2:select", function (e) { 
    
           var data = e.params.data.text;
           if(data=='Select All'){
            $("#course > option").prop("selected","selected");
            var sel_all = $('#course option[value=""]');
            sel_all.prop('selected', false);
            $("#course").trigger("change");
           }
      });
</script>

<?php 
	include("footer.php");
?>