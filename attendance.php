<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"attendance")){ }
else{
	header("location:home.php");
}


$stmt_blist = $obj->con1->prepare("SELECT b1.* FROM batch b1, faculty f1,course c1, branch b2,batch_assign b3 where b1.faculty_id=f1.id and b1.branch_id=b2.id and c1.courseid=b1.course_id and b3.batch_id=b1.id and b1.id!=37 GROUP by b1.id order by id desc");
$stmt_blist->execute();
$res = $stmt_blist->get_result();
$stmt_blist->close();	

$batch_id="";
$dt=date("Y-m-d");

if(isset($_REQUEST['submit1']))
{
  $total = $_REQUEST["total"];
	//$atten = "p";
	/*foreach($_REQUEST['faculty_atten'] as $aid)
	{
		echo $aid."<br/>";	*/
	  try
	  {
      for($i=1;$i<$total;$i++){
        if(isset($_REQUEST["faculty_atten".$i])){
          $faculty_atten = "p";
        }
        else{
          $faculty_atten = "a";
        }
        $remark = $_REQUEST["remark".$i];
        $aid = $_REQUEST["atten_id".$i];
        $status = $_REQUEST["status".$i];
    		$stmt = $obj->con1->prepare("update attendance set faculty_attendance=?, remark=? where aid=?");
    		$stmt->bind_param("ssi", $faculty_atten,$remark,$aid);
    		$Resp=$stmt->execute();

        //get attendance data
        $stmt_attn = $obj->con1->prepare("select * from attendance where aid=?");
        $stmt_attn->bind_param("i", $aid);
        $Resp=$stmt_attn->execute();
        $res_attn = $stmt_attn->get_result()->fetch_assoc();
        $stmt_attn->close();

        if($status!="")
        {
          //update stu status in batch assign
          $stmt_batch = $obj->con1->prepare("update batch_assign set student_status=? where student_id=? and batch_id=?");
          $stmt_batch->bind_param("sii", $status,$res_attn["student_id"],$res_attn["batch_id"]);
          $Resp_batch=$stmt_batch->execute(); 
        }

        
      }
		if(!$Resp)
		{
		  throw new Exception("Problem in inserting! ". strtok($obj->con1-> error,  '('));
		}
		$stmt->close();
	  } 
	  catch(\Exception  $e) {
		setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
	  }
	

  if($Resp)
  {
	  setcookie("msg", "data",time()+3600,"/");
    header("location:attendance.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:attendance.php");
  }	
}

if(isset($_REQUEST['btnsubmit']))
{
	if(isset($_REQUEST["batch"])){
		$batch_id = $_REQUEST["batch"];
		if($batch_id==-1){
			$batch_id="";	
		}
	}
	if(isset($_REQUEST["dt"])){
		$dt = $_REQUEST["dt"];
	}
}

?>

<?php 

if(isset($_COOKIE["msg"]))
{
  if($_COOKIE["msg"]=="data")
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
  else if($_COOKIE["msg"]=="update")
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
  else if($_COOKIE["msg"]=="fail")
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
  else if($_COOKIE["msg"]=="data_del")
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

<?php if($row["read_func"]=="y"){ ?>
              <!-- Basic Layout -->
              <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Attendance</h5>
                      
                    </div>
                    <div class="card-body">

                      <form method="post" >

				  <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Batch</label>
                          <select name="batch" id="batch" class="form-control" required>
                          	<option value="">Select</option>
                        
					<?php    
                        while($batch=mysqli_fetch_array($res)){
							if($batch_id==$batch["id"]){
                    ?>
                    		<option value="<?php echo $batch["id"] ?>" selected="selected"><?php echo $batch["name"] ?></option>
                    <?php
							}
							else{
					?>
							<option value="<?php echo $batch["id"] ?>"><?php echo $batch["name"] ?></option>
					<?php 
							}
						}						
					?>
							
                          </select>
					</div>
					
				            <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Date</label>
                         <input type="date" class="form-control" name="dt" id="dt" value="<?php echo $dt ?>"/>
                        <!--  <input type="date" class="form-control" name="dt" id="dt" value="<?php echo date("Y-m-d") ?>"/> -->
                    </div>
                    <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Students</label><br>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="stu_type" id="stu_type_all" value="all"  <?php echo ($_REQUEST['stu_type']!="" && $_REQUEST['stu_type']=="all")?"checked":""?> >
                            <label class="form-check-label" for="inlineRadio1">All</label>
                          </div>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="stu_type" id="stu_type_current" value="current"  <?php echo ($_REQUEST['stu_type']!="" && $_REQUEST['stu_type']=="current")?"checked":""?> >
                            <label class="form-check-label" for="inlineRadio1">Current Students</label>
                          </div>
                    </div>
					
					<button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Show</button>

          </form>
      </div>
    </div>
    </div>
  
</div>
          

            <!-- Basic Bootstrap Table -->
              <div class="card">
                <?php

                if(isset($_REQUEST['btnsubmit']))
                {
                  $stu_type=($_REQUEST["stu_type"]!="" && $_REQUEST['stu_type']=="current")?"ongoing":"";
                  $stu_str=($stu_type!="")?" and b1.student_status='ongoing'":"";
                  ?>
                <div class="table-responsive text-nowrap">
                  <table class="table">
				  
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Student Name</th>
                        <th>Attendance by Student</th>
            						<th>Attendance by Faculty</th>
                        <th>Status</th>
            						<th>Remark</th>
                      </tr>
                    </thead>
                    <form method="post"> 
                    <tbody class="table-border-bottom-0">
                      <?php
          					
                    
          							
          						//$stmt_list = $obj->con1->prepare("select a.*,s.name from attendance a, student s where a.student_id=s.sid and batch_id = '".$batch_id."' and dt ='".$dt."'");
                      
                      $stmt_list = $obj->con1->prepare("select a.*,s.name from attendance a, student s,batch_assign b1 where a.student_id=s.sid and b1.student_id=a.student_id and a.batch_id = '".$batch_id."' and a.dt ='".$dt."'".$stu_str." group by a.student_id");
                      
          						$stmt_list->execute();
          						$result = $stmt_list->get_result();
          						$stmt_list->close();
          					  
          						$i=1;

                        if(mysqli_num_rows($result)==0){
                        ?>
                          <td valign="top" align="center" colspan="4" class="dataTables_empty">No data available in table</td>
                        <?php
                                      
                            }
                                  while($a=mysqli_fetch_array($result))
                                  {
                                    // get data from batch assign

                                    $stmt_batch_assign = $obj->con1->prepare("select * from batch_assign where student_id=? and batch_id=?");
                                    $stmt_batch_assign->bind_param("ii", $a["student_id"],$a["batch_id"]);
                                    $Resp_batch=$stmt_batch_assign->execute();
                                    $res_batch_assign = $stmt_batch_assign->get_result()->fetch_assoc();
                                    $stmt_batch_assign->close();
                                    
                                ?>

                                <tr>
                                  <td><?php echo $i?></td>
                                  <td><?php echo $a["name"]?></td>
                      						<td style="color:<?php echo ($a["stu_attendance"]=="p")?'green':'red' ?>"><?php echo ucfirst($a["stu_attendance"])?></td>
                      						<td>
                                		<input type="checkbox" name="faculty_atten<?php echo $i ?>" value="<?php echo $a["aid"] ?>"  <?php echo ($a["faculty_attendance"]=="p")?"checked='checked'":""?>/>
                                    <input type="hidden" name="atten_id<?php echo $i ?>" value="<?php echo $a["aid"] ?>">
                                    
                                  </td>
                                  <td><select class="form-control" name="status<?php echo $i?>">
                                    <option value="">Select Status</option>
                                    <option value="course_completed" <?php echo ($res_batch_assign["student_status"]=="course_completed")?"selected":""?>>Course Completed</option>
                                    <option value="batch_change" <?php echo ($res_batch_assign["student_status"]=="batch_change")?"selected":""?>>Batch Changed</option>
                                    <option value="on_hold" <?php echo ($res_batch_assign["student_status"]=="on_hold")?"selected":""?>>On Hold</option>
                                    <option value="dismiss" <?php echo ($res_batch_assign["student_status"]=="dismiss")?"selected":""?>>Dismiss</option>
                                    <option value="on_leave" <?php echo ($res_batch_assign["student_status"]=="on_leave")?"selected":""?>>On Leave</option>
                                    <option value="irregular" <?php echo ($res_batch_assign["student_status"]=="irregular")?"selected":""?>>Irregular</option>
                                    <option value="course_completed_with_exam" <?php echo ($res_batch_assign["student_status"]=="course_completed_with_exam")?"selected":""?>>Course Completed With Exam</option>
                                    <option value="course_completed_without_exam" <?php echo ($res_batch_assign["student_status"]=="course_completed_without_exam")?"selected":""?>>Course Completed Without Exam</option>
                                  </select></td>
          						              <td><input type="text" class="form-control" name="remark<?php echo $i ?>" id="" value="<?php echo $a["remark"] ?>"/></td>
                                </tr>
                                <?php
                                    $i++;
                                  }
          						
                      ?>

                      
                    </tbody>
                  </table>
                  <?php
                    if(mysqli_num_rows($result)!=0){
                  ?>
                  <input type="hidden" name="total" value="<?php echo $i ?>"> 
                    <button type="submit" name="submit1" id="submit1" class="btn btn-primary">Submit</button>

                  <?php
                    }
                  ?>
                  </form>
                </div>
                
                <?php

              }?>
              </div>
              <!--/ Basic Bootstrap Table -->


           <!-- / grid -->

        
  
<?php } ?>
           
<?php 
include("footer.php");
?>