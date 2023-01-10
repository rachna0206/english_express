<?php
include("header.php");

// for permission
include_once("checkPer.php");
if($row=checkPermission($_SESSION["utype"],"task")){ }
else{
	header("location:home.php");
}


$stmt_list = $obj->con1->prepare("select * from faculty order by id");
$stmt_list->execute();
$faculty_list = $stmt_list->get_result();  
$stmt_list->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $task_details=$_REQUEST['task'];
  $task_time=$_REQUEST['task_time'];
  $mon=$tues=$wed=$thurs=$fri=$sat=$sun="n";
  
  if(isset($_REQUEST["mon"])){
    $mon="y";
  }
  if(isset($_REQUEST["tues"])){
    $tues="y";
  }
  if(isset($_REQUEST["wed"])){
    $wed="y";
  }
  if(isset($_REQUEST["thurs"])){
    $thurs="y";
  }
  if(isset($_REQUEST["fri"])){
    $fri="y";
  }
  if(isset($_REQUEST["sat"])){
    $sat="y";
  }
  if(isset($_REQUEST["sun"])){
    $sun="y";
  }

  $status = $_REQUEST['status'];

  try
  {
    // insert into task_assign table
  	$stmt = $obj->con1->prepare("INSERT INTO `task_master`(`task_detail`,`monday`,`tuesday`,`wednesday`,`thursday`,`friday`,`saturday`,`sunday`,`task_time`,`status`) VALUES (?,?,?,?,?,?,?,?,?,?)");
  	$stmt->bind_param("ssssssssss", $task_details,$mon,$tues,$wed,$thurs,$fri,$sat,$sun,$task_time,$status);
  	$Resp=$stmt->execute();
    $lastId = mysqli_insert_id($obj->con1);

    // insert into task_assign table
    foreach($_REQUEST['faculties'] as $staff_id){
      $stmt_task_assign = $obj->con1->prepare("INSERT INTO `task_assign`(`task_id`,`staff_id`) VALUES (?,?)");
      $stmt_task_assign->bind_param("ii", $lastId,$staff_id);
      $Resp=$stmt_task_assign->execute();
      $stmt_task_assign->close();
    }

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
    header("location:task.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:task.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $task_details=$_REQUEST['task'];
  $task_time=$_REQUEST['task_time'];
  $mon=$tues=$wed=$thurs=$fri=$sat=$sun="n";
  
  if(isset($_REQUEST["mon"])){
    $mon="y";
  }
  if(isset($_REQUEST["tues"])){
    $tues="y";
  }
  if(isset($_REQUEST["wed"])){
    $wed="y";
  }
  if(isset($_REQUEST["thurs"])){
    $thurs="y";
  }
  if(isset($_REQUEST["fri"])){
    $fri="y";
  }
  if(isset($_REQUEST["sat"])){
    $sat="y";
  }
  if(isset($_REQUEST["sun"])){
    $sun="y";
  }

  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];

  try
  {
    //update for task_master table
  	$stmt = $obj->con1->prepare("update task_master set task_detail=?, monday=?, tuesday=?, wednesday=?, thursday=?, friday=?, saturday=?, sunday=?, task_time=?, status=? where task_id=?");
  	$stmt->bind_param("ssssssssssi", $task_details,$mon,$tues,$wed,$thurs,$fri,$sat,$sun,$task_time,$status,$id);
  	$Resp=$stmt->execute();

    //update for task_assign table
    $stmt_del2 = $obj->con1->prepare("delete from task_assign where task_id='".$id."'");
    $Resp=$stmt_del2->execute();

    foreach($_REQUEST['faculties'] as $staff_id){

      $stmt_task_assign = $obj->con1->prepare("INSERT INTO `task_assign`(`task_id`,`staff_id`) VALUES (?,?)");
      $stmt_task_assign->bind_param("ii", $id,$staff_id);
      $Resp=$stmt_task_assign->execute();
      $stmt_task_assign->close();
    }    

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
      header("location:task.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:task.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{  
  try
  {
    // delete from task_master
    $stmt_del1 = $obj->con1->prepare("delete from task_master where task_id='".$_REQUEST["n_id"]."'");
    $Resp=$stmt_del1->execute();

    // delete from task_asign
    $stmt_del2 = $obj->con1->prepare("delete from task_assign where task_id='".$_REQUEST["n_id"]."'");
    $Resp=$stmt_del2->execute();

    if(!$Resp)
    {
      throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
    }
    $stmt_del1->close();
    $stmt_del2->close();
  } 
  catch(\Exception  $e) {
  // $error=  $e->getMessage();
  setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }
 
 
  if($Resp)
  {
    setcookie("msg", "data_del",time()+3600,"/");
    header("location:task.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:task.php");
  }
}

?>

<script type="text/javascript">

  function getTaskFacultyList(task_id){
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=getTaskFacultyList",
          data: "task_id="+task_id,
          cache: false,
          success: function(result){
            //alert(result);
            $('#faculties').html('');
            $('#faculties').append(result);
       
            }
        });
  }

</script>

<h4 class="fw-bold py-3 mb-4">Task Master</h4>

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

<?php if($row["write_func"]=="y" || $row["upd_func"]=="y" || $row["read_func"]=="y"){ ?>
              <!-- Basic Layout -->
              <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Add Task</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Task Details</label>
                          <input type="text" class="form-control" name="task" id="task" required />
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Time</label>
                          <input type="time" class="form-control" name="task_time" id="task_time" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Days</label><br>
                          <input type="hidden" name="stu_reg" id="stu_reg">
                           <input type="checkbox" name="mon" id="mon" value="Monday" checked /> Monday
                           <input type="checkbox" name="tues" id="tues" value="Tuesday" checked/> Tuesday
                           <input type="checkbox" name="wed" id="wed" value="Wednesday" checked/> Wednesday
                           <input type="checkbox" name="thurs" id="thurs" value="Thursday" checked/> Thursday
                           <input type="checkbox" name="fri" id="fri" value="Friday" checked/> Friday
                           <input type="checkbox" name="sat" id="sat" value="Saturday" checked/> Saturday
                           <input type="checkbox" name="sun" id="sun" value="Sunday" checked /> Sunday
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Faculty name</label>
                          <select name="faculties[]" id="faculties" multiple class="form-control" required>
                      <?php while($faculty=mysqli_fetch_array($faculty_list)){ ?>
                          <option value="<?php echo $faculty[0] ?>"><?php echo $faculty[1] ?></option>
                      <?php } ?>
                          </select>
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
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
                    <?php  } if($row["upd_func"]=="y"){ ?>
						<button type="submit" name="btnupdate" id="btnupdate" class="btn btn-primary " hidden>Update</button>
                    <?php } ?>
                        <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location.reload()">Cancel</button>

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
                <h5 class="card-header">Skills Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Task</th>
                        <th>Task Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from task_master order by task_id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($t=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $t["task_detail"]?></td>
                        <td><?php echo $t["task_time"]?></td>
                        <td><?php echo $t["status"]?></td>
                        
                   	<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                    <?php  if($row["upd_func"]=="y"){ ?>
                      	<a href="javascript:editdata('<?php echo $t["task_id"]?>','<?php echo base64_encode($t["task_detail"])?>','<?php echo base64_encode($t["task_time"])?>','<?php echo $t["status"]?>','<?php echo $t["monday"]?>','<?php echo $t["tuesday"]?>','<?php echo $t["wednesday"]?>','<?php echo $t["thursday"]?>','<?php echo $t["friday"]?>','<?php echo $t["saturday"]?>','<?php echo $t["sunday"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                		<?php } if($row["del_func"]=="y"){ ?>
          							<a  href="javascript:deletedata('<?php echo $t["task_id"]?>');"><i class="bx bx-trash me-1"></i> </a>
						        <?php } if($row["read_func"]=="y"){ ?>
                      	<a href="javascript:viewdata('<?php echo $t["task_id"]?>','<?php echo base64_encode($t["task_detail"])?>','<?php echo base64_encode($t["task_time"])?>','<?php echo $t["status"]?>','<?php echo $t["monday"]?>','<?php echo $t["tuesday"]?>','<?php echo $t["wednesday"]?>','<?php echo $t["thursday"]?>','<?php echo $t["friday"]?>','<?php echo $t["saturday"]?>','<?php echo $t["sunday"]?>');">View</a>
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
  function deletedata(id) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "task.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(tid,task_detail,time,status,mo,tu,we,th,fr,sa,su) {
    
    $('#ttId').val(tid);
    $('#task').val(atob(task_detail));
    $('#task_time').val(atob(time));
    
    if(status=="active")
    {
      $('#active').attr("checked","checked"); 
    }
    else if(status=="inactive")
    {
      $('#inactive').attr("checked","checked"); 
    }

    if(mo=="y"){
      $('#mon').attr("checked","checked");  
    } else{
      $('#mon').removeAttr("checked");
    }
    if(tu=="y"){
      $('#tues').attr("checked","checked");  
    } else{
      $('#tues').removeAttr("checked");
    }
    if(we=="y"){
      $('#wed').attr("checked","checked");  
    } else{
      $('#wed').removeAttr("checked");
    }
    if(th=="y"){
      $('#thurs').attr("checked","checked");  
    } else{
      $('#thurs').removeAttr("checked");
    }
    if(fr=="y"){
      $('#fri').attr("checked","checked");  
    } else{
      $('#fri').removeAttr("checked");
    }
    if(sa=="y"){
      $('#sat').attr("checked","checked");  
    } else{
      $('#sat').removeAttr("checked");
    }
    if(su=="y"){
      $('#sun').attr("checked","checked");  
    } else{
      $('#sun').removeAttr("checked");
    }

    getTaskFacultyList(tid);

		$('#btnsubmit').attr('hidden',true);
    $('#btnupdate').removeAttr('hidden');
		$('#btnsubmit').attr('disabled',true);

  }
	function viewdata(tid,task_detail,time,status,mo,tu,we,th,fr,sa,su) {
	   
		$('#ttId').val(tid);
    $('#task').val(atob(task_detail));
    $('#task_time').val(atob(time));
    
    if(status=="active")
    {
      $('#active').attr("checked","checked"); 
    }
    else if(status=="inactive")
    {
      $('#inactive').attr("checked","checked"); 
    }

    if(mo=="y"){
      $('#mon').attr("checked","checked");  
    } else{
      $('#mon').removeAttr("checked");
    }
    if(tu=="y"){
      $('#tues').attr("checked","checked");  
    } else{
      $('#tues').removeAttr("checked");
    }
    if(we=="y"){
      $('#wed').attr("checked","checked");  
    } else{
      $('#wed').removeAttr("checked");
    }
    if(th=="y"){
      $('#thurs').attr("checked","checked");  
    } else{
      $('#thurs').removeAttr("checked");
    }
    if(fr=="y"){
      $('#fri').attr("checked","checked");  
    } else{
      $('#fri').removeAttr("checked");
    }
    if(sa=="y"){
      $('#sat').attr("checked","checked");  
    } else{
      $('#sat').removeAttr("checked");
    }
    if(su=="y"){
      $('#sun').attr("checked","checked");  
    } else{
      $('#sun').removeAttr("checked");
    }

    getTaskFacultyList(tid);

		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').attr('hidden',true);
		$('#btnsubmit').attr('disabled',true);
	}
</script>
<?php 
include("footer.php");
?>