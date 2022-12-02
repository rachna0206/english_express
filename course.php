<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"course_master")){ }
else{
	header("location:home.php");
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $coursename=$_REQUEST['coursename'];
  $duration=$_REQUEST['duration'];
  $fee=$_REQUEST['fee'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `course`(`coursename`, `duration`,`course_fee`) VALUES (?,?,?)");
	$stmt->bind_param("sss", $coursename,$duration,$fee);
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


  if($Resp)
  {
      setcookie("msg", "data",time()+3600,"/");
	  header("location:course.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:course.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $coursename=$_REQUEST['coursename'];
  $duration=$_REQUEST['duration'];
  $courseid=$_REQUEST['ttcourseid'];
  $fee=$_REQUEST['fee'];

  try
  {
	$stmt = $obj->con1->prepare("update course set  coursename=?,duration=?,course_fee=? where courseid=?");
	$stmt->bind_param("sssi", $coursename,$duration,$fee,$courseid);
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


  if($Resp)
  {
	  setcookie("msg", "update",time()+3600,"/");
      header("location:course.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:course.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
	$stmt_del = $obj->con1->prepare("delete from course where courseid='".$_REQUEST["n_courseid"]."'");
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
    header("location:course.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:course.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Course Master</h4>

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
                      <h5 class="mb-0">Add Course</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Course name</label>
                          <input type="text" class="form-control" name="coursename" id="coursename" required />
                          <input type="hidden"name="ttcourseid" id="ttcourseid" hidden="hidden">
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Duration of the course</label>
                          <input type="text" class="form-control duration-mask" id="duration" name="duration" required="required"/>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Course Fee</label>
                          <input type="text" class="form-control duration-mask" id="fee" name="fee" required="required" value="0" />
                        </div>
                        
                    <?php if($row["write_func"]=="y"){ ?>
                        <button type="submit" name="btnsubmit"id="btnsubmit" class="btn btn-primary">Submit</button>
					<?php } if($row["upd_func"]=="y"){ ?>
                        <button type="submit" name="btnupdate"id="btnupdate" class="btn btn-primary " hidden>Update</button>
                    <?php } ?>
                        <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='course.php'">Cancel</button>

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
                <h5 class="card-header">Course List</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Course name</th>
                        <th>Duration of course</th>
                        <th>Course Fee</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from course order by courseid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($course=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $course["coursename"]?></td>
                        <td><?php echo $course["duration"]?></td>
                        <td><?php echo $course["course_fee"]?></td>
                        
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a  href="javascript:editdata('<?php echo $course["courseid"]?>','<?php echo base64_encode($course["coursename"])?>','<?php echo base64_encode($course["duration"])?>','<?php echo $course["course_fee"] ?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $course["courseid"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                        	<a  href="javascript:viewdata('<?php echo $course["courseid"]?>','<?php echo base64_encode($course["coursename"])?>','<?php echo base64_encode($course["duration"])?>','<?php echo $course["course_fee"] ?>');">View</a>
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
  function deletedata(courseid) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "course.php?flg=del&n_courseid=" + courseid;
          window.location = loc;
      }
  }
  function editdata(courseid,coursename,duration,fee) {
           
           document.getElementById('coursename').value=atob(coursename);
		   document.getElementById('duration').value=atob(duration);// js
		   //$('#duration').val=atob(duration);// jquery
            //$('#coursename').val(atob(coursename));
            $('#ttcourseid').val(courseid);
            $('#fee').val(fee);
            $('#btnsubmit').attr('hidden',true);
            $('#btnupdate').removeAttr('hidden');
			$('#btnsubmit').attr('disabled',true);
  }
  function viewdata(courseid,coursename,duration,fee) {
           
           document.getElementById('coursename').value=atob(coursename);
		   document.getElementById('duration').value=atob(duration);// js
		   //$('#duration').val=atob(duration);// jquery
            //$('#coursename').val(atob(coursename));
            $('#ttcourseid').val(courseid);
            $('#fee').val(fee);
            $('#btnsubmit').attr('hidden',true);
            $('#btnupdate').attr('hidden',true);
			$('#btnsubmit').attr('disabled',true);
  }
</script>
<?php 
include("footer.php");
?>