<?php
include("header.php");
error_reporting(0);
//123s
// for permission
if($row=checkPermission($_SESSION["utype"],"student_report")){ }
else{
	header("location:home.php");
}

$stmt_slist = $obj->con1->prepare("select * from state where status='enable'");
$stmt_slist->execute();
$res = $stmt_slist->get_result();
$stmt_slist->close();


$stmt_state = $obj->con1->prepare("select * from state");
$stmt_state->execute();
$res_state = $stmt_state->get_result();
$stmt_state->close();

$stmt_city = $obj->con1->prepare("select * from city");
$stmt_city->execute();
$res_city = $stmt_city->get_result();
$stmt_city->close();

$stmt_course = $obj->con1->prepare("select * from course");
$stmt_course->execute();
$res_course = $stmt_course->get_result();
$stmt_course->close();


// generate random password
function password_generate($chars) 
{
  $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
  return substr(str_shuffle($data), 0, $chars);
}




// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $city=isset($_REQUEST['city'])?$_REQUEST['city']:"";
  $village=isset($_REQUEST['village'])?$_REQUEST['village']:"";
  $state=isset($_REQUEST['state'])?$_REQUEST['state']:"";
  $name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
  $contact=isset($_REQUEST['contact'])?$_REQUEST['contact']:"";
  
  $dt_from=isset($_REQUEST['dt_from'])?$_REQUEST['dt_from']:"";
  $dt_to=isset($_REQUEST['dt_to'])?$_REQUEST['dt_to']:"";
  $gender=isset($_REQUEST['gender'])?$_REQUEST['gender']:"";
  $course=isset($_REQUEST['course'])?$_REQUEST['course']:"";

  $city_str=($city!="")?"and s1.city = '".$city."'":"";
  $state_str=($state!="")?"and s1.state = '".$state."'":"";
  $name_str=($name!="")?"and s1.name like '%".$name."%'":"";
  $village_str=($village!="")?"and s1.village like '%".$village."%'":"";
  $contact_str=($contact!="")?"and s1.phone like '%".$contact."%'":"";
  
  $dt_fromstr=($dt_from!="")?"and s1.inquiry_dt>='".$dt_from."'":"";
  $dt_tostr=($dt_to!="")?"and s1.inquiry_dt<='".$dt_to."'":"";
  $genderstr=($gender!="")?"and s1.gender='".$gender."'":"";
  $coursestr=($course!="")?"and s1.courseid='".$course."'":"";  
  
  
  $stmt_list = $obj->con1->prepare("select * from student s1, course c1 ,stu_course sc where sc.course_id=c1.courseid and sc.stu_id=s1.sid and s1.status='inquiry' ".$city_str.$name_str.$contact_str.$dt_fromstr.$dt_tostr.$genderstr.$state_str.$village_str.$coursestr);
   
  $stmt_list->execute();
  $result = $stmt_list->get_result();
  
  $stmt_list->close();

}
else if(isset($_REQUEST["typ"]))
{
  if($_REQUEST['typ']=="today")
  {
    $dt_from=date('Y-m-d');
    $dt_to=date('Y-m-d');
    
    $stmt_list = $obj->con1->prepare("select * from student s1, course c1 ,stu_course sc where sc.course_id=c1.courseid and sc.stu_id=s1.sid and s1.status='inquiry' and s1.inquiry_dt='".date("Y-m-d")."' ");
  }
  
  $stmt_list->execute();
  $result = $stmt_list->get_result(); 
  $stmt_list->close();

}
?>

<h4 class="fw-bold py-3 mb-4">Enquiry Report</h4>

<?php if($row["read_func"]=="y"){ ?>

<!-- Basic Layout -->
<div class="row">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
          
      </div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <div class="row">
            
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Name</label>
              <input type="text" class="form-control" name="name" id="name" value="<?php echo isset($_REQUEST['name'])?$_REQUEST['name']:""?>"  />
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Contact</label>
              <input type="text" class="form-control" name="contact" id="contact"  value="<?php echo isset($_REQUEST['contact'])?$_REQUEST['contact']:""?>"/>
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">State</label>
              <select class="form-control" name="state" id="state">
                <option value="">Select State</option>
                <?php 
                while($state=mysqli_fetch_array($res_state))
                {
                  ?>
                  <option value="<?php echo $state["state_id"]?>" <?php echo (isset($_REQUEST['state']) && $_REQUEST['state']==$state["state_id"])?"selected":""?>><?php echo $state["state_name"]?></option>

                  <?php

                }
                ?>
              </select>
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Village</label>
              <input type="text" class="form-control" name="village" id="village"  value="<?php echo isset($_REQUEST['village'])?$_REQUEST['village']:""?>"/>
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">City</label>
              <select class="form-control" name="city" id="city">
                <option value="">Select City</option>
                <?php 
                while($city=mysqli_fetch_array($res_city))
                {
                  ?>
                  <option value="<?php echo $city["city_id"]?>" <?php echo (isset($_REQUEST['city']) && $_REQUEST['city']==$city["city_id"])?"selected":""?>><?php echo $city["city_name"]?></option>

                  <?php

                }
                ?>
              </select>
              
            </div>
             <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Enquiry Date From</label>
              <input type="date" class="form-control" name="dt_from" id="dt_from"  value="<?php echo $dt_from?>"/>
              
            </div>
             <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Enquiry Date To</label>
              <input type="date" class="form-control" name="dt_to" id="dt_to" value="<?php echo $dt_to?>" />
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label d-block" for="basic-default-fullname">Gender</label>
                          
                <div class="form-check form-check-inline mt-3">
                    <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male"  <?php echo ($_REQUEST['gender']!="" && $_REQUEST['gender']=="male")?"checked":""?> >
                    <label class="form-check-label" for="inlineRadio1">Male</label>
                </div>
                <div class="form-check form-check-inline mt-3">
                    <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female"  <?php echo ($_REQUEST['gender']!="" && $_REQUEST['gender']=="female")?"checked":""?>>
                    <label class="form-check-label" for="inlineRadio1">Female</label>
                </div>
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Course</label>
              <select class="form-control" name="course" id="course">
                <option value="">Select Course</option>
                <?php 
                while($course=mysqli_fetch_array($res_course))
                {
                  ?>
                  <option value="<?php echo $course["courseid"]?>" <?php echo (isset($_REQUEST['course']) && $_REQUEST['course']==$course["courseid"])?"selected":""?>><?php echo $course["coursename"]?></option>

                  <?php

                }
                ?>
              </select>
              
            </div>
            
           
          </div>

          <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>
        
          <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='stu_report.php'">Cancel</button>

        </form>
      </div>
    </div>
  </div>
</div>

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
                        <th>Enrollment Date</th>
                        
                        
                        <th>Course</th>
                        <th>Action</th>
                        
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="grid">
                      <?php 
                     if(isset($_REQUEST['btnsubmit']) || isset($_REQUEST["typ"]))
                      {
                      
                        $i=1;
                        while($s=mysqli_fetch_array($result))
                        {
                          
                         
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        
                        <td><?php echo $s["name"]?></td>
                        
                        <td><?php echo $s["phone"]?></td>
                        <td><?php echo ($s["inquiry_dt"]!="0000-00-00")?date("d-m-Y", strtotime($s["inquiry_dt"])):"-"?></td>
                        
                        
                        <td><?php echo $s["coursename"]?></td>
                        <td ><a href="javascript:view_stu_data('<?php echo $s["sid"]?>')">View</a></td>
                        
                    
                          
                    <?php 
                    $i++;
                  } ?>
                      </tr>
                      <?php
                          
                        }
                        
                      ?>
                      
                    </tbody>
                  </table>
                </div>
              </div>

<?php } ?>

              <!--/ Basic Bootstrap Table -->
<script type="text/javascript">
  function view_stu_data(stu)
  {
    createCookie("enquiry_report_id",stu,1);
    window.open('enquiry_report_detail.php', '_blank');
    

  }
</script>

<?php 
	include("footer.php");
?>