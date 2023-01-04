<?php 
include("header.php");
//error_reporting(0);

// for permission
if($row=checkPermission($_SESSION["utype"],"attendance_report")){ }
else{
  header("location:home.php");
}

$dt=date("Y-m-d");
if(isset($_REQUEST['btnsubmit']))
{
  $dt = $_REQUEST['dt'];
}

//batches qry
$stmt_batch = $obj->con1->prepare("select a1.*, b1.name, b1.stime, b1.capacity from attendance a1, batch b1 where dt=? and faculty_attendance='p' and a1.batch_id=b1.id group by a1.batch_id");
$stmt_batch->bind_param("s",$dt);
$stmt_batch->execute();
$result_batch = $stmt_batch->get_result();
$stmt_batch->close();

?>

<!-- back button-->
<!--    <button onclick="goBack()" class="btn btn-primary"><i class="tf-icons bx bx-left-arrow-alt"></i> Back
    </button> -->

<h4 class="fw-bold py-3 mb-4">Attendance Report Detail</h4>

<?php if($row["read_func"]=="y"){ ?>

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
    </ul>
                    <div class="tab-content">
                      <div class="tab-pane fade show active" id="navs-top-batch" role="tabpanel">
                        <ul class="p-0 m-0">
                          <form method="post">
                            <div class="mb-3">
                              <label class="form-label" for="basic-default-fullname">Date</label>
                              <input type="date" class="form-control" name="dt" id="dt" value="<?php echo $dt ?>"/>
                            </div>
                            <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Show</button>
                          </form>
                        <?php 
                        if(mysqli_num_rows($result_batch)>0)
                        {
                          while($batch_data=mysqli_fetch_array($result_batch))
                          {
                            $stmt_attendence = $obj->con1->prepare("select count(if(faculty_attendance='p',1,null)) as faculty_attendance,count(faculty_attendance) as total_attendance from attendance where batch_id=? and dt=?");
                            $stmt_attendence->bind_param("is",$batch_data["batch_id"],$dt); 
                            $stmt_attendence->execute();
                            $result_attendance = $stmt_attendence->get_result();
                            $attendence=mysqli_fetch_array($result_attendance);
                            $stmt_attendence->close();

                            $bid = $batch_data["batch_id"];

                            $stmt_attend = $obj->con1->prepare("select a1.*,DATE_FORMAT(a1.dt, '%d-%m-%Y') as a_dt,s1.name from attendance a1, student s1 where a1.student_id=s1.sid and batch_id=? and dt=?");
                            $stmt_attend->bind_param("is",$batch_data["batch_id"],$dt); 
                            $stmt_attend->execute();
                            $result_attend = $stmt_attend->get_result();
                            $stmt_attend->close();  
                        ?>
                          <li class="d-flex mb-4 pb-1">
                           
                            <div class="accordion mt-3" id="accordionExample">
                              <div class="card accordion-item active">
                                <h2 class="accordion-header" id="headingOne">
                                  <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">
                                  <?php echo $batch_data["name"]." - ".$batch_data["stime"]?>
                                  <span class="text-muted " style="margin-left:20%">Attendance-<?php echo $attendence["faculty_attendance"]?>
                                    /<?php echo $attendence["total_attendance"]?></span>
                                  <span class="text-muted " style="margin-left:10%">Capacity-<?php echo $attendence["total_attendance"]?>
                                    /<?php echo $batch_data["capacity"]?></span>
                                  </button>
                                </h2>

                                <div id="accordionOne" class="accordion-collapse collapse show"data-bs-parent="#accordionExample">
                                  <div class="accordion-body">
                                    <div class="table-responsive">
                                      <table class="table table-striped table-borderless border-bottom">
                                        <thead>
                                          <tr>
                                            <th class="text-nowrap">Date</th>
                                            <th class="text-nowrap">Student</th>
                                            <th class="text-nowrap text-center">Student Attendance</th>
                                            <th class="text-nowrap text-center">Faculty Attendance</th>
                                            <th class="text-nowrap text-center">Remark</th>
                                          </tr>
                                        </thead>
                                        <tbody id="vertical-example">
                                          <?php 
                                            while($attend=mysqli_fetch_array($result_attend))
                                            {
                                          ?>
                                              <tr>
                                            <td class="text-nowrap"><?php echo $attend["a_dt"]?></td>
                                            <td class="text-nowrap"><?php echo $attend["name"]?></td>
                                            <td align="center" style="color:<?php echo ($attend["stu_attendance"]=="p")?'green':'red' ?>"><?php echo ucfirst($attend["stu_attendance"])?></td>
                                            <td align="center" style="color:<?php echo ($attend["faculty_attendance"]=="p")?'green':'red' ?>"><?php echo ucfirst($attend["faculty_attendance"])?></td>
                                            <td>
                                              <div class="form-check d-flex justify-content-center">
                                              <?php echo ($attend["remark"]!="")?$attend["remark"]:"-"?>
                                              </div>
                                            </td>
                                          </tr>
                                          <?php
                                          }
                                          ?>
                                          
                                        </tbody>
                                      </table>
                                    </div>
                                    
                                  </div>
                                </div>
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
                      
                    </div>  
                      
                    </div>
                  </div>

<?php } ?>

<script type="text/javascript">
  function goBack() {
      // window.history.back();
      //window.location = "stu_report.php";
  }
</script>
<?php 

include ("footer.php");
?>