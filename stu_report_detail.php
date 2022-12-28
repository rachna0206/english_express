<?php 
include("header.php");
error_reporting(0);
$stu_id=$_COOKIE['stu_report_id'];

//stu qry
$stmt_stu = $obj->con1->prepare("select * from student where sid=?");
$stmt_stu->bind_param("i",$stu_id); 
$stmt_stu->execute();
$result_stu = $stmt_stu->get_result()->fetch_assoc();
$stmt_stu->close();

//batches qry
$stmt_batch = $obj->con1->prepare("select b1.id,b1.name as batch_name,b1.stime as batch_time,b1.status,f1.name as faculty from batch b1,batch_assign b2,faculty f1 where b2.batch_id=b1.id and b1.faculty_id=f1.id and b2.student_id=?");
$stmt_batch->bind_param("i",$stu_id); 
$stmt_batch->execute();
$result_batch = $stmt_batch->get_result();
$stmt_batch->close();

//assignment qry
$stmt_assign = $obj->con1->prepare("SELECT s1.*,c1.chapter_name,e1.exer_name,GROUP_CONCAT(s1.skill) as skills FROM stu_assignment s1, chapter c1,exercise e1 where s1.chap_id=c1.cid and s1.exercise_id=e1.eid and s1.stu_id=? GROUP by e1.eid");

$stmt_assign->bind_param("i",$stu_id); 
$stmt_assign->execute();
$result_assignment = $stmt_assign->get_result();
$stmt_assign->close();
?>

<!-- back button-->
    <button onclick="goBack()" class="btn btn-primary"><i class="tf-icons bx bx-left-arrow-alt"></i> Back
    </button>

<h4 class="fw-bold py-3 mb-4">Student Report Detail: <?php echo $result_stu["name"]."-".$result_stu["user_id"]?></h4>



<!-- Basic Layout -->
<div class="row">
  <div class="nav-align-top mb-4">
   
                    
                    
    <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
      <button
        type="button"
        class="nav-link active"
        role="tab"
        data-bs-toggle="tab"
        data-bs-target="#navs-top-batch"
        aria-controls="navs-top-batch"
        aria-selected="true"
      >
        Batches
      </button>
    </li>
    <li class="nav-item">
      <button
        type="button"
        class="nav-link"
        role="tab"
        data-bs-toggle="tab"
        data-bs-target="#navs-top-assignment"
        aria-controls="navs-top-assignment"
        aria-selected="false"
      >
        Assignments
      </button>
    </li>
    
  </ul>
                    <div class="tab-content">
                      <div class="tab-pane fade show active" id="navs-top-batch" role="tabpanel">
                        <ul class="p-0 m-0">
                        <?php 
                        if(mysqli_num_rows($result_batch)>0)
                        {


                          while($batch_data=mysqli_fetch_array($result_batch))
                          {
                            
                            $stmt_attendence = $obj->con1->prepare("select count(if(faculty_attendance='p',1,null)) as faculty_attendance,count(faculty_attendance) as total_attendance from attendance where batch_id=? and student_id=?");
                            $stmt_attendence->bind_param("ii",$batch_data["id"],$stu_id); 
                            $stmt_attendence->execute();
                            $result_attendance = $stmt_attendence->get_result();
                            
                            $attendence=mysqli_fetch_array($result_attendance);
                            $stmt_attendence->close();

                        ?>
                          <li class="d-flex mb-4 pb-1">
                           
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                
                                <h6 class="mb-0"><?php echo $batch_data["batch_name"]." - ".$batch_data["batch_time"]?></h6>
                                <small class="text-muted d-block mb-1"><?php echo $batch_data["faculty"]?></small>
                              </div>
                              <div class="user-progress d-flex align-items-center gap-1">
                                <span class="text-muted">Attendance-</span>
                                <h6 class="mb-0"><?php echo $attendence["faculty_attendance"]?></h6>
                                <span class="text-muted">/<?php echo $attendence["total_attendance"]?></span>
                              </div>
                            </div>
                          </li>
                        <?php
                          }
                        }
                        else
                        {
                          ?>
                          <li class="d-flex mb-4 pb-1">
                            No Batch Allocated
                          </li>
                          <?php
                        }
                        ?>
                    </ul>
                      </div>
                      <div class="tab-pane fade" id="navs-top-assignment" role="tabpanel">
                        <ul class="p-0 m-0">
                        <?php 
                        if(mysqli_num_rows($result_assignment)>0)
                        {

                        
                        
                          while($assignment_data=mysqli_fetch_array($result_assignment))
                          {
                            $stmt_exe = $obj->con1->prepare("SELECT s1.*,c1.chapter_name,e1.exer_name FROM stu_assignment s1, chapter c1,exercise e1 where s1.chap_id=c1.cid and s1.exercise_id=e1.eid and s1.stu_id=? and  s1.exercise_id=?");
                            $stmt_exe->bind_param("ii",$stu_id,$assignment_data["exercise_id"]); 
                            $stmt_exe->execute();
                            $result_exe = $stmt_exe->get_result();
                            
                            
                            $stmt_exe->close();
                            

                          ?>
                          <li class="d-flex mb-4 pb-1">
                           
                              <div class=" d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2 row">
                                  
                                  <h6 class="mb-1"><?php echo $assignment_data["exer_name"]?> <span class="text-muted" style="float: right;">Alloted Date-<?php echo $assignment_data["alloted_dt"]?>
                                  
                                </span></h6>
                                  <h6 class="text-muted d-block mb-1"><?php echo $assignment_data["chapter_name"]?> </h6>

                                  <?php 
                                  while($exercise=mysqli_fetch_array($result_exe))
                                  {
                                    ?>
                                    <small class="text-muted d-block mb-1 col-6"><?php echo $exercise["skill"]?>  <span>Student Status-<?php echo $exercise["stu_status"]?></span></small>
                                    <small class="text-muted d-block mb-1 col-6 ">  <span class="text-right"><?php echo $exercise["status"]?></span></small>
                                    <?php
                                  }
                                  ?>
                                  
                                </div>

                                <!-- <div class="user-progress d-flex align-items-center gap-1">

                                  <span class="text-muted">Status-</span>
                                  
                                  <span class="text-muted"><?php echo $assignment_data["status"]?></span>
                                </div> -->

                              </div>
                            </li>
                        <?php
                          }
                        }
                        else
                        {
                          ?>
                          <li class="d-flex mb-4 pb-1">
                            No Assignment Allocated
                          </li>
                          <?php
                        }
                        ?>
                    </ul>
                      </div>
                      
                    </div>  
                      
                    </div>
                  </div>


<script type="text/javascript">
  function goBack() {
      // window.history.back();
      window.location = "stu_report.php";
  }
</script>
<?php 

include ("footer.php");
?>