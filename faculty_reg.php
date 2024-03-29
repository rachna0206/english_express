<?php
include("header.php");

// for permission
//include("checkPer.php");
if($row=checkPermission($_SESSION["utype"],"faculty_reg")){ }
else{
	header("location:home.php");
}


$stmt_dlist = $obj->con1->prepare("select user_desig from permissions group by user_desig");
$stmt_dlist->execute();
$res = $stmt_dlist->get_result();
$stmt_dlist->close();

$stmt_slist = $obj->con1->prepare("select * from state where status='enable'");
$stmt_slist->execute();
$state_res = $stmt_slist->get_result();
$stmt_slist->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
	$fname=$_REQUEST['fname'];
	$contact=$_REQUEST['contact'];
	$email=$_REQUEST['email'];
	$gender=$_REQUEST['gender'];
	$qualification=$_REQUEST['qualification'];
	$desig = $_REQUEST['designation'];
	$userid=$_REQUEST['userid'];
	$password=$_REQUEST['password'];
	$house_no = $_REQUEST['house_no'];
  	$society = $_REQUEST['society_name'];
  	$village = $_REQUEST['village'];
  	$landmark = $_REQUEST['landmark'];
  	$city = $_REQUEST['city'];
  	$state = $_REQUEST['state'];
  	$pin_code = $_REQUEST['pin'];
	$dob=$_REQUEST['dob'];
	$pp=$_FILES['pp']['name'];
	$srcpp=$_FILES['pp']['tmp_name'];
	$adhar=$_FILES['adhar']['name'];
	$srcadhar=$_FILES['adhar']['tmp_name'];
  $status = $_REQUEST['status'];
  $firm_name = "english_express";
	
	//rename file for profile pic
	if ($_FILES["pp"]["name"] != "")
  	{
      if(file_exists("faculty_pic/" . $pp)) {
          $i = 0;
          $PicFileName = $_FILES["pp"]["name"];
          $Arr1 = explode('.', $PicFileName);

          $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
          while (file_exists("faculty_pic/" . $PicFileName)) {
              $i++;
              $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
          }
	   } 
	   else {
          $PicFileName = $_FILES["pp"]["name"];
      }
  	}
	
	//rename file for aadhar card
	if ($_FILES["adhar"]["name"] != "")
  	{
      if (file_exists("adhar_pic/" . $adhar)) {
          $i = 0;
          $MainFileName = $_FILES["adhar"]["name"];
          $Arr = explode('.', $MainFileName);

          $MainFileName = $Arr[0] . $i . "." . $Arr[1];
          while (file_exists("adhar_pic/" . $MainFileName)) {
              $i++;
              $MainFileName = $Arr[0] . $i . "." . $Arr[1];
          }
	 } else {
          $MainFileName = $_FILES["adhar"]["name"];
       }
  	}

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `faculty`(`name`, `phone`, `email`, `gender`, `qualification`, `designation`, `uid`, `password`, `house_no`, `society_name`, `village`, `landmark`, `city`, `state`, `pin`, `dob`, `profilepic`, `adhar`, `firm_name`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param("ssssssssssssiissssss",$fname,$contact,$email,$gender,$qualification,$desig,$userid,$password,$house_no,$society,$village,$landmark,$city,$state,$pin_code,$dob,$PicFileName,$MainFileName,$firm_name,$status);
	$Resp=$stmt->execute();
	$insert_id = mysqli_insert_id($obj->con1);
  	if(!$Resp)
	{
      throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
	  move_uploaded_file($srcpp,"faculty_pic/".$PicFileName);
	  move_uploaded_file($srcadhar,"adhar_pic/".$MainFileName);
	  
	  setcookie("msg", "data",time()+3600,"/");    
      header("location:faculty_reg.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:faculty_reg.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  	$fname=$_REQUEST['fname'];
	$contact=$_REQUEST['contact'];
	$email=$_REQUEST['email'];
	$gender=$_REQUEST['gender'];
	$qualification=$_REQUEST['qualification'];
	$desig = $_REQUEST['designation'];
	$userid=$_REQUEST['userid'];
	$password=$_REQUEST['password'];
	$house_no = $_REQUEST['house_no'];
  	$society = $_REQUEST['society_name'];
  	$village = $_REQUEST['village'];
  	$landmark = $_REQUEST['landmark'];
  	$city = $_REQUEST['city'];
  	$state = $_REQUEST['state'];
  	$pin_code = $_REQUEST['pin'];
  	$dob=$_REQUEST['dob'];
	$rpp= $_REQUEST['hpp'];
	$radhar= $_REQUEST['hadhar'];
	$pp=$_FILES['pp']['name'];
	$srcpp=$_FILES['pp']['tmp_name'];
	$adhar=$_FILES['adhar']['name'];
	$srcadhar=$_FILES['adhar']['tmp_name'];
  $id=$_REQUEST['ttId'];
  $status = $_REQUEST['status'];
  
	
	if($pp!="")
	{
		unlink("faculty_pic/".$rpp);	
		
		//rename file for profile pic
		if ($_FILES["pp"]["name"] != "")
		{
		  if(file_exists("faculty_pic/" . $pp)) {
			  $i = 0;
			  $PicFileName = $_FILES["pp"]["name"];
			  $Arr1 = explode('.', $PicFileName);
	
			  $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
			  while (file_exists("faculty_pic/" . $PicFileName)) {
				  $i++;
				  $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
			  }
		   } 
		   else {
			  $PicFileName = $_FILES["pp"]["name"];
		  }
		}
		
		move_uploaded_file($srcpp,"faculty_pic/".$PicFileName);
	}
	else
	{
		$PicFileName=$rpp;
	}
	
	
	if($adhar!="")
	{
		unlink("adhar_pic/".$radhar);
			
		//rename file for aadhar card
		if ($_FILES["adhar"]["name"] != "")
		{
		  if (file_exists("adhar_pic/" . $adhar)) {
			  $i = 0;
			  $MainFileName = $_FILES["adhar"]["name"];
			  $Arr = explode('.', $MainFileName);
	
			  $MainFileName = $Arr[0] . $i . "." . $Arr[1];
			  while (file_exists("adhar_pic/" . $MainFileName)) {
				  $i++;
				  $MainFileName = $Arr[0] . $i . "." . $Arr[1];
			  }
		 } else {
			  $MainFileName = $_FILES["adhar"]["name"];
		   }
		}
		
		move_uploaded_file($srcadhar,"adhar_pic/".$MainFileName);	
	}
	else
	{
		$MainFileName=$radhar;
	}

  try
  {
	$stmt = $obj->con1->prepare("update faculty set  name=?, phone=?, email=?, gender=?, qualification=?, designation=?, uid=?, password=?, house_no=?, society_name=?, village=?, landmark=?, city=?, state=?, pin=?, dob=?, profilepic=?, adhar=?, status=? where id=?");
	$stmt->bind_param("ssssssssssssiisssssi",$fname,$contact,$email,$gender,$qualification,$desig,$userid,$password,$house_no,$society,$village,$landmark,$city,$state,$pin_code,$dob,$PicFileName,$MainFileName,$status,$id);
	$Resp=$stmt->execute();
	$rows =$stmt->affected_rows;
	if(!$Resp)
	{
      throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($rows>0)
  {
	  setcookie("msg", "update",time()+3600,"/");
      header("location:faculty_reg.php");
  }
  else
  {   
      setcookie("msg", "fail",time()+3600,"/");
	  header("location:faculty_reg.php");
  }
}
// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  $pic = $_REQUEST["pic"];
  $adc = $_REQUEST["adc"];
  
  try
  {
	$stmt_del = $obj->con1->prepare("delete from faculty where id='".$_REQUEST["n_id"]."'");
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
	if(file_exists("faculty_pic/".$pic)){
		unlink("faculty_pic/".$pic);
	}
  	if(file_exists("adhar_pic/".$adc)){
		unlink("adhar_pic/".$adc);
  	}
	setcookie("msg", "data_del",time()+3600,"/");
    header("location:faculty_reg.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:faculty_reg.php");
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


<h4 class="fw-bold py-3 mb-4">Staff Registration</h4>

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
                      <h5 class="mb-0">Add Staff</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Faculty Name</label>
                          <input type="text" class="form-control" name="fname" id="fname" required />
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Contact No.</label>
                          <input type="tel" pattern="[0-9]{10}" class="form-control phone-mask" id="contact" name="contact"  required/>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-email">Email</label>
                          <input type="text" id="email" name="email" class="form-control" 
                              aria-label="email" aria-describedby="basic-default-email2" required/>
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
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Qualification</label>
                          <input type="text" class="form-control" name="qualification" id="qualification" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Designation</label>
                          <select name="designation" id="designation" class="form-control" required>
                          	<option value="">Select</option>
					<?php    
                        while($d=mysqli_fetch_array($res)){
                    ?>
                    		<option value="<?php echo $d["user_desig"] ?>"><?php echo $d["user_desig"] ?></option>
                    <?php
						}
					?>
                          </select>                        
                       </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">User Id</label>
                          <input type="text" class="form-control" name="userid" id="userid" required onblur="check_userid(this.value)" onchange="check_userid(this.value)" />
                          <div id="user_alert" class="text-danger"></div>
                          
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Password</label>
                          <input type="password" class="form-control" name="password" id="password" required />
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
                        while($state=mysqli_fetch_array($state_res)){
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
                          <label class="form-label" for="basic-default-fullname">Date of Birth</label>
                          <input type="date" class="form-control" name="dob" id="dob" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Profile Pic</label>
                          <input type="file" class="form-control" onchange="readPP(this)" name="pp" id="pp" required="required"/>
                          <img src="" name="PreviewPP" id="PreviewPP" width="100" height="100" style="display:none;">
                          <div id="imgpp" style="color:red"></div>
                          <input type="hidden"  name="hpp" id="hpp" />
                          
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Adhar Card</label>
                          <input type="file" class="form-control" onchange="readURL(this)" name="adhar" id="adhar" required="required"/>
                          <img src="" name="PreviewImage" id="PreviewImage" width="100" height="100" style="display:none;">
                          <div id="imgdiv" style="color:red"></div>
                          <input type="hidden"  name="hadhar" id="hadhar" />
                        </div>

                        <div class="mb-3">
                          <label class="form-label d-block" for="basic-default-fullname">Status</label>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="active" value="active" checked required >
                            <label class="form-check-label" for="inlineRadio1">Active</label>
                          </div>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="inactive" value="inactive" required>
                            <label class="form-check-label" for="inlineRadio1">Inactive</label>
                          </div>
                        </div>
                        
                    <?php if($row["write_func"]=="y"){ ?>
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>
					<?php } if($row["upd_func"]=="y"){ ?>
                        <button type="submit" name="btnupdate" id="btnupdate" class="btn btn-primary " hidden>Update</button>
                    <?php } ?>
                        <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='faculty_reg.php'">Cancel</button>
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
                <h5 class="card-header">Staff Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Name</th>
                        <th>Contact No.</th>
                        <th>Designation</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from faculty where designation!='Associate' order by id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($faculty=mysqli_fetch_array($result))
                        {
                          ?>


                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $faculty["name"]?></td>
                        <td><?php echo $faculty["phone"]?></td>
                        <td><?php echo $faculty["designation"]?></td>
                        
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a  href="javascript:editdata('<?php echo $faculty["id"]?>','<?php echo $faculty["name"]?>','<?php echo $faculty["phone"]?>','<?php echo $faculty["email"]?>','<?php echo $faculty["gender"]?>','<?php echo $faculty["qualification"]?>','<?php echo $faculty["designation"]?>','<?php echo $faculty["uid"]?>','<?php echo $faculty["password"]?>','<?php echo base64_encode($faculty["house_no"])?>','<?php echo base64_encode($faculty["society_name"])?>','<?php echo base64_encode($faculty["village"])?>','<?php echo base64_encode($faculty["landmark"])?>','<?php echo $faculty["city"]?>','<?php echo $faculty["state"]?>','<?php echo base64_encode($faculty["pin"])?>','<?php echo $faculty["dob"]?>','<?php echo $faculty["profilepic"]?>','<?php echo $faculty["adhar"]?>','<?php echo $faculty["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $faculty["id"]?>','<?php echo base64_encode($faculty["profilepic"])?>','<?php echo base64_encode($faculty["adhar"])?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                        	<a  href="javascript:viewdata('<?php echo $faculty["id"]?>','<?php echo $faculty["name"]?>','<?php echo $faculty["phone"]?>','<?php echo $faculty["email"]?>','<?php echo $faculty["gender"]?>','<?php echo $faculty["qualification"]?>','<?php echo $faculty["designation"]?>','<?php echo $faculty["uid"]?>','<?php echo $faculty["password"]?>','<?php echo base64_encode($faculty["house_no"])?>','<?php echo base64_encode($faculty["society_name"])?>','<?php echo base64_encode($faculty["village"])?>','<?php echo base64_encode($faculty["landmark"])?>','<?php echo $faculty["city"]?>','<?php echo $faculty["state"]?>','<?php echo base64_encode($faculty["pin"])?>','<?php echo $faculty["dob"]?>','<?php echo $faculty["profilepic"]?>','<?php echo $faculty["adhar"]?>','<?php echo $faculty["status"]?>');">View</a>
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

  function check_userid(userid)
  {
    var id=$('#ttId').val();
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=check_userid",
          data: "userid="+userid+"&id="+id,
          cache: false,
          success: function(result){
            
            if(result==1)
            {
              $('#user_alert').html('Userid already exists!');
              document.getElementById('btnsubmit').disabled = true;
              document.getElementById('btnupdate').disabled = true;
            }
            else
            {
              $('#user_alert').html('');
              document.getElementById('btnsubmit').disabled = false;
              document.getElementById('btnupdate').disabled = false;
            }

            
       
            }
        });
  }

function readURL(input) {

    if (input.files && input.files[0]) {
        var filename=input.files.item(0).name;

        var reader = new FileReader();
        var extn=filename.split(".");

           if(extn[1].toLowerCase()=="jpg" || extn[1].toLowerCase()=="jpeg" || extn[1].toLowerCase()=="png" || extn[1].toLowerCase()=="bmp") {
        reader.onload = function (e) {
            $('#PreviewImage').attr('src', e.target.result);
            document.getElementById("PreviewImage").style.display = "block";

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
function readPP(input) {

    if (input.files && input.files[0]) {
        var filename=input.files.item(0).name;

        var reader = new FileReader();
        var extn=filename.split(".");

           if(extn[1].toLowerCase()=="jpg" || extn[1].toLowerCase()=="jpeg" || extn[1].toLowerCase()=="png" || extn[1].toLowerCase()=="bmp") {
        reader.onload = function (e) {
            $('#PreviewPP').attr('src', e.target.result);
            document.getElementById("PreviewPP").style.display = "block";
			
        };

        reader.readAsDataURL(input.files[0]);
        $('#imgpp').html("");
        document.getElementById('btnsubmit').disabled = false;
       

    }
    else
    {
      $('#imgpp').html("Please Select Image Only");
	  document.getElementById('btnsubmit').disabled = true;
    }

  }
}

  function deletedata(id,pi,ac) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "faculty_reg.php?flg=del&n_id=" + id+"&pic="+atob(pi)+"&adc="+atob(ac);
          window.location = loc;
      }
  }
  function editdata(id,name,phone,email,gender,qualification,designation,uid,password,house_no,society,village,landmark,city,state,pin,dob,pp,adhar,status) {
      $('#fname').focus(); 
     	$('#ttId').val(id);
      $('#fname').val(name);
      $('#contact').val(phone);
			$('#email').val(email);
      $('#user_alert').html('');
			//$('#gender').val(gender);
			console.log("gender="+gender);
			if(gender=="female")
			{
				$('#gender_female').prop("checked","checked");	
			}
			else
			{
				$('#gender_male').prop("checked","checked");	
			}
			$('#qualification').val(qualification);
			$('#designation').val(designation);
			$('#userid').val(uid);
			$('#password').val(password);
			$('#house_no').val(atob(house_no));
			$('#society_name').val(atob(society));
			$('#village').val(atob(village));
			$('#landmark').val(atob(landmark));
			$('#state').val(state);
			cityList(state);
			cityList(state);
			setTimeout(function() {
				$('#city').val(city);
			}, 1000);
			$('#pin').val(atob(pin));
			$('#dob').val(dob);
			$('#hpp').val(pp);
			$('#hadhar').val(adhar);
			$('#PreviewPP').show();
			$('#PreviewPP').attr('src','faculty_pic/'+pp);
			$('#PreviewImage').show();
			$('#PreviewImage').attr('src','adhar_pic/'+adhar);
			$('#pp').removeAttr('required');
			$('#adhar').removeAttr('required');
      if(status=="active"){
        $('#active').attr("checked","checked"); 
      }
      else if(status=="inactive"){
        $('#inactive').attr("checked","checked"); 
      }

      $('#btnsubmit').attr('hidden',true);
      $('#btnupdate').removeAttr('hidden');
			$('#btnsubmit').attr('disabled',true);
            
        }
		
		function viewdata(id,name,phone,email,gender,qualification,designation,uid,password,house_no,society,village,landmark,city,state,pin,dob,pp,adhar,status) {
    $('#fname').focus(); 
           	$('#ttId').val(id);
            $('#fname').val(name);
            $('#contact').val(phone);
			$('#email').val(email);
			console.log("gender="+gender);
			if(gender=="female")
			{
				$('#gender_female').prop("checked","checked");	
			}
			else
			{
				$('#gender_male').prop("checked","checked");	
			}
			$('#qualification').val(qualification);
			$('#designation').val(designation);
			$('#userid').val(uid);
			$('#password').val(password);
			$('#house_no').val(atob(house_no));
			$('#society_name').val(atob(society));
			$('#village').val(atob(village));
			$('#landmark').val(atob(landmark));
			$('#state').val(state);
			cityList(state);
			cityList(state);
			setTimeout(function() {
				$('#city').val(city);
			}, 1000);
			$('#pin').val(atob(pin));
			$('#dob').val(dob);
			$('#hpp').val(pp);
			$('#hadhar').val(adhar);
			$('#PreviewPP').show();
			$('#PreviewPP').attr('src','faculty_pic/'+pp);
			$('#PreviewImage').show();
			$('#PreviewImage').attr('src','adhar_pic/'+adhar);
			$('#pp').removeAttr('required');
			$('#adhar').removeAttr('required');

      if(status=="active"){
        $('#active').attr("checked","checked"); 
      }
      else if(status=="inactive"){
        $('#inactive').attr("checked","checked"); 
      }

      $('#btnsubmit').attr('hidden',true);
      $('#btnupdate').attr('hidden',true);
			$('#btnsubmit').attr('disabled',true);   
        }
</script>
<?php 
include("footer.php");
?>