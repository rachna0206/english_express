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

$stmt_batch = $obj->con1->prepare("select * from batch");
$stmt_batch->execute();
$res_batch = $stmt_batch->get_result();
$stmt_batch->close();

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


$stmt_slist = $obj->con1->prepare("select * from skill");
$stmt_slist->execute();
$res1 = $stmt_slist->get_result();
$stmt_slist->close();

$stmt_clist = $obj->con1->prepare("select * from course");
$stmt_clist->execute();
$res2 = $stmt_clist->get_result();
$stmt_clist->close();

$stmt_batch = $obj->con1->prepare("select * from batch");
$stmt_batch->execute();
$res_batch = $stmt_batch->get_result();
$stmt_batch->close();


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $userid=isset($_REQUEST['userid'])?$_REQUEST['userid']:"";
  $name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
  $contact=isset($_REQUEST['contact'])?$_REQUEST['contact']:"";
  $batch=isset($_REQUEST['batch'])?$_REQUEST['batch']:"";
  $dt_from=isset($_REQUEST['dt_from'])?$_REQUEST['dt_from']:"";
  $dt_to=isset($_REQUEST['dt_to'])?$_REQUEST['dt_to']:"";
  $gender=isset($_REQUEST['gender'])?$_REQUEST['gender']:"";
  $course=isset($_REQUEST['course'])?$_REQUEST['course']:"";

  $user_str=($userid!="")?"and s1.user_id like '%".$userid."%'":"";
  $name_str=($name!="")?"and s1.name like '%".$name."%'":"";
  $contact_str=($contact!="")?"and s1.phone like '%".$contact."%'":"";
  $batch_str=($batch!="")?"and b1.id='".$batch."'":"";
  $dt_fromstr=($dt_from!="")?"and s1.enrollment_dt>='".$dt_from."'":"";
  $dt_tostr=($dt_to!="")?"and s1.enrollment_dt<='".$dt_to."'":"";
  $genderstr=($gender!="")?"and s1.gender='".$gender."'":"";
  $coursestr=($course!="")?"and s1.courseid='".$course."'":"";
  if($batch!="")
  {


     $stmt_list = $obj->con1->prepare("select s1.*,c1.*,b1.name as batch_name,b1.stime from student s1, course c1,batch b1,batch_assign b2,stu_course sc where sc.course_id=c1.courseid and sc.stu_id=s1.sid and b2.batch_id=b1.id and b2.student_id=s1.sid ".$user_str.$name_str.$contact_str.$batch_str.$dt_fromstr.$dt_tostr.$genderstr.$coursestr);
  }
  else
  {

     $stmt_list = $obj->con1->prepare("select * from student s1, course c1 where s1.courseid=c1.courseid ".$user_str.$name_str.$contact_str.$dt_fromstr.$dt_tostr.$genderstr.$coursestr);
  }

  
 
  
  $stmt_list->execute();
  $result = $stmt_list->get_result();
  
  $stmt_list->close();

}
else if(isset($_REQUEST["typ"]))
{
  if($_REQUEST['typ']=="unassigned")
  {
    $stmt_list = $obj->con1->prepare("SELECT s1.*,b2.name as batch_name,b2.stime as batch_time FROM  student s1,batch_assign b1,batch b2 WHERE b1.student_id=s1.sid and b1.batch_id=b2.id  and b2.id=37 ");
  }
  if($_REQUEST['typ']=="new_admission")
  {
    $dt_from=date('Y-m-d');
    $dt_to=date('Y-m-d');
    $stmt_list = $obj->con1->prepare("select s1.*,c1.*,b1.name as batch_name,b1.stime from student s1, course c1,batch b1,batch_assign b2,stu_course sc where sc.course_id=c1.courseid and sc.stu_id=s1.sid and b2.batch_id=b1.id and b2.student_id=s1.sid and s1.enrollment_dt='".date("Y-m-d")."' ");

  }
  $stmt_list->execute();
  $result = $stmt_list->get_result(); 
  $stmt_list->close();

}
?>

<h4 class="fw-bold py-3 mb-4">Student Report</h4>

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
              <label class="form-label" for="basic-default-fullname">UserId (Roll No.)</label>
              <input type="text" class="form-control" name="userid" id="userid"  value="<?php echo isset($_REQUEST['userid'])?$_REQUEST['userid']:""?>"/>
              <input type="hidden" name="ttId" id="ttId">
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Name</label>
              <input type="text" class="form-control" name="name" id="name" value="<?php echo isset($_REQUEST['name'])?$_REQUEST['name']:""?>"  />
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Contact</label>
              <input type="text" class="form-control" name="contact" id="contact"  value="<?php echo isset($_REQUEST['contact'])?$_REQUEST['contact']:""?>"/>
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Batch</label>
              <select name="batch" id="batch" class="form-control"  >
                <option value="">Select</option>
                <?php
                while($batch=mysqli_fetch_array($res_batch))
                {
                  ?>
                  <option value="<?php echo $batch["id"]?>" <?php echo (isset($_REQUEST['batch']) && $_REQUEST['batch']==$batch["id"])?"selected":""?>><?php echo $batch["stime"]."-".$batch["name"]?></option>
                  <?php
                }
                ?>
                
                
              </select>
              
            </div>
             <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Registration Date From</label>
              <input type="date" class="form-control" name="dt_from" id="dt_from"  value="<?php echo $dt_from?>"/>
              
            </div>
             <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Registration Date To</label>
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
                        <th>Roll No.</th>
                        <th>Name</th>
                        
                        <th>Contact No.</th>
                        <th>Enrollment Date</th>
                        <th>Batch Time</th>
                        
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
                          
                          $stmt_batch = $obj->con1->prepare("select GROUP_CONCAT(b1.name,',') as batch_name,GROUP_CONCAT(b1.stime,',') as batch_time,b1.status,f1.name  from batch b1,batch_assign b2,faculty f1 where b2.batch_id=b1.id and b1.faculty_id=f1.id and b2.student_id=?");
                          $stmt_batch->bind_param("i",$s["sid"]); 
                          $stmt_batch->execute();
                          $result_batch = $stmt_batch->get_result();
                          $batch_data=mysqli_fetch_array($result_batch);
                          $stmt_batch->close();
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $s["user_id"]?></td>
                        <td><?php echo $s["name"]?></td>
                        
                        <td><?php echo $s["phone"]?></td>
                        <td><?php echo ($s["enrollment_dt"]!="0000-00-00")?date("d-m-Y", strtotime($s["enrollment_dt"])):"-"?></td>
                        <td><?php echo $batch_data["batch_name"]." - ".$batch_data["batch_time"]?></td>
                        
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
    createCookie("stu_report_id",stu,1);
    window.open('stu_report_detail.php', '_blank');
    

  }
</script>

<?php 
	include("footer.php");
?>