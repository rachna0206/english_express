<?php 
include("header.php");
error_reporting(0);
$faculty_id=$_COOKIE['faculty_report_id'];

//faculty qry
$stmt_fac = $obj->con1->prepare("select * from faculty where id=?");
$stmt_fac->bind_param("i",$faculty_id); 
$stmt_fac->execute();
$result_fac = $stmt_fac->get_result()->fetch_assoc();
$stmt_fac->close();

//batches qry (for main faculty)
$stmt_batch1 = $obj->con1->prepare("select b1.id, b1.name, b1.stime from batch b1, faculty f1 where b1.faculty_id=f1.id and f1.id=? and b1.id!=37");
$stmt_batch1->bind_param("i",$faculty_id); 
$stmt_batch1->execute();
$result_batch1 = $stmt_batch1->get_result();
$stmt_batch1->close();

//batches qry (for assistant faculty 1)
$stmt_batch2 = $obj->con1->prepare("select b1.id, b1.name, b1.stime from batch b1, faculty f1 where b1.assist_faculty_1=f1.id and f1.id=? and b1.id!=37");
$stmt_batch2->bind_param("i",$faculty_id); 
$stmt_batch2->execute();
$result_batch2 = $stmt_batch2->get_result();
$stmt_batch2->close();

//batches qry (for assistant faculty 2)
$stmt_batch3 = $obj->con1->prepare("select b1.id, b1.name, b1.stime from batch b1, faculty f1 where b1.assist_faculty_2=f1.id and f1.id=? and b1.id!=37");
$stmt_batch3->bind_param("i",$faculty_id); 
$stmt_batch3->execute();
$result_batch3 = $stmt_batch3->get_result();
$stmt_batch3->close();

//assignment qry
$stmt_assign = $obj->con1->prepare("SELECT s1.*,c1.chapter_name,e1.exer_name,b1.bookname,GROUP_CONCAT(s1.skill) as skills FROM stu_assignment s1, chapter c1,exercise e1,books b1 where s1.chap_id=c1.cid and s1.exercise_id=e1.eid and s1.book_id=b1.bid and s1.faculty_id=? GROUP by e1.eid");

$stmt_assign->bind_param("i",$faculty_id); 
$stmt_assign->execute();
$result_assignment = $stmt_assign->get_result();
$stmt_assign->close();
?>

<!-- back button-->
    <button onclick="goBack()" class="btn btn-primary"><i class="tf-icons bx bx-left-arrow-alt"></i> Back
    </button>

<h4 class="fw-bold py-3 mb-4">Faculty Report Detail: <?php echo $result_fac["name"] ?></h4>



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
                        if(mysqli_num_rows($result_batch1)>0 || mysqli_num_rows($result_batch2)>0 || mysqli_num_rows($result_batch3)>0)
                        {
                          while($batch_data1=mysqli_fetch_array($result_batch1))
                          {
                            $stmt_strength = $obj->con1->prepare("select count(student_id) as s_count from batch_assign ba1, batch b1 where ba1.batch_id=b1.id and b1.faculty_id=? and ba1.batch_id=?");
                            $stmt_strength->bind_param("ii",$faculty_id,$batch_data1["id"]); 
                            $stmt_strength->execute();
                            $result_strength = $stmt_strength->get_result();
                            $strength=mysqli_fetch_array($result_strength);
                            $stmt_strength->close();
                        ?>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <h6 class="mb-0"><?php echo $batch_data1["name"]." - ".$batch_data1["stime"]?></h6>
                                <small class="mb-0">Main Faculty</small>
                              </div>
                              <div class="user-progress d-flex align-items-center gap-1">
                                <span class="text-muted">Strength-</span>
                                <h6 class="mb-0"><?php echo $strength["s_count"]?></h6>
                              </div>
                            </div>
                          </li>
                        <?php
                          }

                          while($batch_data2=mysqli_fetch_array($result_batch2))
                          {
                            $stmt_strength = $obj->con1->prepare("select count(student_id) as s_count from batch_assign ba1, batch b1 where ba1.batch_id=b1.id and ba1.batch_id=?");
                            $stmt_strength->bind_param("i",$batch_data2["id"]); 
                            $stmt_strength->execute();
                            $result_strength = $stmt_strength->get_result();
                            $strength=mysqli_fetch_array($result_strength);
                            $stmt_strength->close();
                        ?>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <h6 class="mb-0"><?php echo $batch_data2["name"]." - ".$batch_data2["stime"]?></h6>
                                <small class="mb-0">Assistant Faculty 1</small>
                              </div>
                              <div class="user-progress d-flex align-items-center gap-1">
                                <span class="text-muted">Strength-</span>
                                <h6 class="mb-0"><?php echo $strength["s_count"]?></h6>
                              </div>
                            </div>
                          </li>
                        <?php
                          }

                          while($batch_data3=mysqli_fetch_array($result_batch3))
                          {
                            $stmt_strength = $obj->con1->prepare("select count(student_id) as s_count from batch_assign ba1, batch b1 where ba1.batch_id=b1.id and b1.faculty_id=? and ba1.batch_id=?");
                            $stmt_strength->bind_param("ii",$faculty_id,$batch_data3["id"]); 
                            $stmt_strength->execute();
                            $result_strength = $stmt_strength->get_result();
                            $strength=mysqli_fetch_array($result_strength);
                            $stmt_strength->close();
                        ?>
                          <li class="d-flex mb-4 pb-1">
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                              <div class="me-2">
                                <h6 class="mb-0"><?php echo $batch_data3["name"]." - ".$batch_data3["stime"]?></h6>
                                <small class="mb-0">Assistant Faculty 2</small>
                              </div>
                              <div class="user-progress d-flex align-items-center gap-1">
                                <span class="text-muted">Strength-</span>
                                <h6 class="mb-0"><?php echo $strength["s_count"]?></h6>
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
                            $stmt_exe = $obj->con1->prepare("SELECT s1.*,c1.chapter_name,e1.exer_name FROM stu_assignment s1, chapter c1,exercise e1 where s1.chap_id=c1.cid and s1.exercise_id=e1.eid and s1.faculty_id=? and s1.exercise_id=? group by skill,exercise_id");
                            $stmt_exe->bind_param("ii",$faculty_id,$assignment_data["exercise_id"]); 
                            $stmt_exe->execute();
                            $result_exe = $stmt_exe->get_result();
                            $stmt_exe->close();
                          ?>
                          <li class="d-flex mb-4 pb-1">
                           
                              <div class=" d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2 row w-100">
                                  
                                  <h6 class=" d-block mb-1 text-italics">Chapter Name: <?php echo $assignment_data["chapter_name"]." ( ".$assignment_data["bookname"]." )"?> </h6>
                                  <h6 class="mb-1"><?php echo $assignment_data["exer_name"]?> <span  style="float: right;">Alloted Date: <?php echo $assignment_data["alloted_dt"]?>
                                  
                                </span></h6>
                                  
                                  <div class="table-responsive">
                                      <table class="table table-striped table-borderless border-bottom">
                                        <thead>
                                          <tr>
                                            <th class="text-nowrap">Skill</th>
                                          </tr>
                                        </thead>
                                        <tbody id="vertical-example">

                                          <?php 
                                          while($exercise=mysqli_fetch_array($result_exe))
                                          {
                                            ?>
                                            <tr>
                                              <td><?php echo $exercise["skill"]?></td>
                                            </tr>
                                            <?php
                                          }
                                          ?>
                                          </tbody>
                                        </table>
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
      window.location = "faculty_report.php";
  }
</script>
<?php 

include ("footer.php");
?>