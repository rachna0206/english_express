<?php
include("header.php");
//error_reporting(0);
$today=date('Y-m-d');

// for permission
include_once("checkPer.php");
if($row=checkPermission($_SESSION["utype"],"lead_generation")){ }
else{
	header("location:home.php");
}


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


$stmt_associate = $obj->con1->prepare("select * from faculty where status='active'");
$stmt_associate->execute();
$res_associate = $stmt_associate->get_result();
$stmt_associate->close();

$stmt_slist = $obj->con1->prepare("select * from state where status='active'");
$stmt_slist->execute();
$res = $stmt_slist->get_result();
$stmt_slist->close();


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $sname=$_REQUEST['sname'];
  $email = $_REQUEST['email'];
  $gender=$_REQUEST['gender'];
  $house_no = $_REQUEST['house_no'];
  $society = $_REQUEST['society_name'];
  $village = $_REQUEST['village'];
  $landmark = $_REQUEST['landmark'];
  $city = $_REQUEST['city'];
  $state = $_REQUEST['state'];
  $pin_code = $_REQUEST['pin'];
  $education = $_REQUEST['education'];
  $contact=$_REQUEST['contact'];
  $guardian_contact = $_REQUEST['guardian_contact'];
  $birthdate = $_REQUEST['dob'];
  $inquiry_dt = $_REQUEST['inquiry_dt'];
  $followup_dt=$_REQUEST['followup_dt'];
  $skills = $_REQUEST['skills'];
  $course = $_REQUEST['course'];
  $remark =$_REQUEST['remark'];
  $stu_type = $_REQUEST['stu_type'];
  $status='inquiry';
  $associate=$_REQUEST['associate'];

  try
  {
     
    	$stmt = $obj->con1->prepare("INSERT INTO `student`(`name`, `email`, `gender`, `house_no`, `society_name`, `village`, `landmark`, `city`, `state`, `pin`, `education`, `stu_type`,`phone`, `guardian_phone`, `dob`, `inquiry_dt`, `followup_dt`, `status`, `remark`,`associate`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    	$stmt->bind_param("sssssssiisssssssssss", $sname,$email,$gender,$house_no,$society,$village,$landmark,$city,$state,$pin_code,$education,$stu_type,$contact,$guardian_contact,$birthdate,$inquiry_dt,$followup_dt,$status,$remark,$associate);
    	$Resp=$stmt->execute();
      $lastId = mysqli_insert_id($obj->con1);

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
	 
	
	  setcookie("msg", "data",time()+3600,"/");
      header("location:lead_generation.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:lead_generation.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $sname=$_REQUEST['sname'];
  $email = $_REQUEST['email'];
  $gender=$_REQUEST['gender'];
  $house_no = $_REQUEST['house_no'];
  $society = $_REQUEST['society_name'];
  $village = $_REQUEST['village'];
  $landmark = $_REQUEST['landmark'];
  $city = $_REQUEST['city'];
  $state = $_REQUEST['state'];
  $pin_code = $_REQUEST['pin'];
  $education = $_REQUEST['education'];
  $contact=$_REQUEST['contact'];
  $guardian_contact = $_REQUEST['guardian_contact'];
  $birthdate = $_REQUEST['dob'];
  $inquiry_dt = $_REQUEST['inquiry_dt'];
  $followup_dt=$_REQUEST['followup_dt'];
  $skills = $_REQUEST['skills'];
  $course = $_REQUEST['course'];
  $remark =$_REQUEST['remark'];
  $stu_type = $_REQUEST['stu_type'];
  $status='inquiry';
  $associate=$_REQUEST['associate'];
 
  $id = $_REQUEST['ttId'];
  
  try
  {
    
  	$stmt = $obj->con1->prepare("update student set name=?, email=?, gender=?, house_no=?, society_name=?, village=?, landmark=?, city=?, state=?, pin=?, education=?, stu_type=?,  phone=?, guardian_phone=?, dob=?, inquiry_dt=?, followup_dt=?, status=?,remark=?,associate=? where sid=?");
  	$stmt->bind_param("sssssssiisssssssssssi", $sname,$email,$gender,$house_no,$society,$village,$landmark,$city,$state,$pin_code,$education,$stu_type,$contact,$guardian_contact,$birthdate,$inquiry_dt,$followup_dt,$status,$remark,$associate,$id);
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
     header("location:lead_generation.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
    header("location:lead_generation.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  
  
  try
  {
	$stmt_del = $obj->con1->prepare("delete from student where sid='".$_REQUEST["n_id"]."'");
	$Resp=$stmt_del->execute();
  $stmt_del->close();

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
    
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }

    
  if($Resp)
  {
  	
	   setcookie("msg", "data_del",time()+3600,"/");
    header("location:lead_generation.php");
  }
  else
  {
	   setcookie("msg", "fail",time()+3600,"/");
    header("location:lead_generation.php");
  }
}

?>

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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h4 class="fw-bold py-3 mb-4">Lead Generation</h4>


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
                          <label class="form-label" for="basic-default-fullname">Inquiry Date</label>
                          <input type="date" class="form-control" name="inquiry_dt" id="inquiry_dt" required value="<?php echo date('Y-m-d')?>" />
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Follow up Date</label>
                          <input type="date" class="form-control" name="followup_dt" id="followup_dt" required value="<?php echo  date('Y-m-d', strtotime($today. ' + 2 days'));?>" />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Associate</label>
                          <select name="associate" id="associate" class="form-control" required>
                            <option value="">Select</option>
                            <?php while($associate=mysqli_fetch_array($res_associate)){ ?>
                                <option value="<?php echo $associate["id"] ?>"><?php echo $associate["name"] ?></option>
                            <?php } ?>
                          </select>
                        </div>
						            <div id="stu_course_div">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Course Enrolled</label>
                          <select name="course[]" id="course" class="form-control js-example-basic-multiple" required multiple="multiple">
                            <option value="">Select</option>
                            <?php while($c=mysqli_fetch_array($res2)){ ?>
                                <option value="<?php echo $c["courseid"] ?>"><?php echo $c["coursename"] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        
						
						            <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Skills</label>
                          <select name="skills[]" id="skills" class="form-control js-example-basic-multiple" required multiple="multiple">
                          	<option value="">Select</option>
          					         <?php while($s=mysqli_fetch_array($res1)){ ?>
                              		<option value="<?php echo $s["skid"] ?>"><?php echo $s["skills"] ?></option>
                              <?php }	?>
                          </select>
                        </div>
                      </div>
                        <!-- <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Status</label>
                          <select name="status" id="status" class="form-control" required>
                            <option value="">Select</option>
                             <option value="inquiry">Inquiry</option>
                             <option value="registered">Registered</option>
                          </select>
                        </div> -->
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Remarks</label>
                          <textarea name="remark" id="remark" class="form-control"></textarea>
                        </div>
                    <?php if($row["write_func"]=="y"){ ?>
						            <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>
                    <?php } if($row["upd_func"]=="y"){ ?>
                        <button type="submit" name="btnupdate" id="btnupdate" class="btn btn-primary " hidden>Update</button>
                    <?php } ?>
                       <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='lead_generation.php'">Cancel</button>

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
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Name</th>
                        
                        <th>Contact No.</th>
                        <th>Inquiry Date</th>
                        <th>Follow up Date</th>
                        
                        <th>Course</th>
                        
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        // $stmt_list = $obj->con1->prepare("select st.*,sk.skills,GROUP_CONCAT(c.coursename)as coursename from student as st, skill as sk, course as c where st.skillid=sk.skid and st.courseid=c.courseid  and st.status='inquiry'  GROUP by st.sid order by sid desc");

                        $stmt_list = $obj->con1->prepare("select st.*,GROUP_CONCAT(c.coursename)as coursename from student as st,course as c,stu_course sc where sc.course_id=c.courseid and sc.stu_id=st.sid  and st.status='inquiry'  GROUP by sc.stu_id order by sid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($s=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $s["name"]?></td>
                        
                        <td><?php echo $s["phone"]?></td>
                        <td><?php echo date("d-m-Y", strtotime($s["inquiry_dt"]))?></td>
                        <td><?php echo ($s["followup_dt"]!="")?date("d-m-Y", strtotime($s["followup_dt"])):"-"?></td>
                        
                        
                        <td><?php echo $s["coursename"]?></td>
                       
                        
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a  href="javascript:editdata('<?php echo $s["sid"]?>','<?php echo base64_encode($s["name"])?>','<?php echo base64_encode($s["email"])?>','<?php echo $s["gender"]?>','<?php echo base64_encode($s["house_no"])?>','<?php echo base64_encode($s["society_name"])?>','<?php echo base64_encode($s["village"])?>','<?php echo base64_encode($s["landmark"])?>','<?php echo $s["city"]?>','<?php echo $s["state"]?>','<?php echo base64_encode($s["pin"])?>','<?php echo base64_encode($s["education"])?>','<?php echo base64_encode($s["stu_type"])?>','<?php echo $s["phone"]?>','<?php echo $s["guardian_phone"]?>','<?php echo base64_encode($s["dob"])?>','<?php echo base64_encode($s["inquiry_dt"])?>','<?php echo base64_encode($s["followup_dt"])?>','<?php echo $s["skillid"]?>','<?php echo $s["courseid"]?>','<?php echo $s["status"]?>','<?php echo base64_encode($s["remark"])?>','<?php echo $s["associate"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
							           <a  href="javascript:deletedata('<?php echo $s["sid"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                        	<a  href="javascript:viewdata('<?php echo $s["sid"]?>','<?php echo base64_encode($s["name"])?>','<?php echo base64_encode($s["email"])?>','<?php echo $s["gender"]?>','<?php echo base64_encode($s["house_no"])?>','<?php echo base64_encode($s["society_name"])?>','<?php echo base64_encode($s["village"])?>','<?php echo base64_encode($s["landmark"])?>','<?php echo $s["city"]?>','<?php echo $s["state"]?>','<?php echo base64_encode($s["pin"])?>','<?php echo base64_encode($s["education"])?>','<?php echo base64_encode($s["stu_type"])?>','<?php echo $s["phone"]?>','<?php echo $s["guardian_phone"]?>','<?php echo base64_encode($s["dob"])?>','<?php echo base64_encode($s["inquiry_dt"])?>','<?php echo base64_encode($s["followup_dt"])?>','<?php echo $s["skillid"]?>','<?php echo $s["courseid"]?>','<?php echo $s["status"]?>','<?php echo base64_encode($s["remark"])?>','<?php echo $s["associate"]?>');">View</a>
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
  $(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});

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
          var loc = "lead_generation.php?flg=del&n_id="+id;
          window.location = loc;
      }
  }
  
  function editdata(id,name,email,gender,house_no,society,village,landmark,city,state,pin,education,stu_type,phone,gphone,dob,inquiry,followup,skill,course,status,remark,associate) {         
		$('#ttId').val(id);
		$('#sname').val(atob(name));
		$('#email').val(atob(email));
    if(gender=="female")
    {
      $('#gender_female').attr("checked","checked");  
    }
    else
    {
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
		}, 2000);
		$('#pin').val(atob(pin));
		$('#education').val(atob(education));
		$('#stu_type').val(atob(stu_type));
		
		$('#contact').val(phone);
		$('#guardian_contact').val(gphone);
    $('#dob').val(atob(dob));
    $('#inquiry_dt').val(atob(inquiry));
		$('#followup_dt').val(atob(followup));
		
		$('#remark').val(atob(remark));
		$('#status').val(status);
		$('#associate').val(associate);
			
		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').removeAttr('hidden');
		$('#btnsubmit').attr('disabled',true);
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
	
	function viewdata(id,name,email,gender,house_no,society,village,landmark,city,state,pin,education,stu_type,phone,gphone,dob,inquiry,followup,skill,course,status,remark,associate) {         
		$('#ttId').val(id);
		$('#sname').val(atob(name));
    $('#email').val(atob(email));
    if(gender=="female")
    {
      $('#gender_female').attr("checked","checked");  
    }
    else
    {
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
		
		$('#contact').val(phone);
    $('#guardian_contact').val(gphone);
    $('#dob').val(atob(dob));
		$('#inquiry_dt').val(atob(inquiry));
		$('#followup_dt').val(atob(followup));
		
		
		$('#remark').val(remark);
		$('#remark').val(atob(remark));
		$('#status').val(status);
		$('#associate').val(associate);
		
		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').attr('hidden',true);
		$('#btnsubmit').attr('disabled',true);
    $('#btnupdate').attr('disabled',true);
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
</script>

<?php 
	include("footer.php");
?>