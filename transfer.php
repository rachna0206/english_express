<?php
include("header.php");

// for permission
include_once("checkPer.php");
if($row=checkPermission($_SESSION["utype"],"transfer")){ }
else{
	header("location:home.php");
}

$dt=date("Y-m-d");

$stmt_slist1 = $obj->con1->prepare("select * from student where status='registered'");
$stmt_slist1->execute();
$student_res1 = $stmt_slist1->get_result();
$stmt_slist1->close();

$stmt_slist2 = $obj->con1->prepare("select * from student where status='registered'");
$stmt_slist2->execute();
$student_res2 = $stmt_slist2->get_result();
$stmt_slist2->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $old_stu_id=$_REQUEST['old_stu_id'];
  $new_stu_id=$_REQUEST['new_stu_id'];
  $transfer_dt=$_REQUEST['transfer_dt'];
  $reason=$_REQUEST['reason'];
  $status='transfered';
  $student_status="ongoing";

  try
  {
  foreach($_REQUEST['batch'] as $batch_id){

    $stmt = $obj->con1->prepare("INSERT INTO `transfer`(`old_stu_id`,`new_stu_id`,`transfer_dt`,`batch_id`,`reason`) VALUES (?,?,?,?,?)");
    $stmt->bind_param("iisis", $old_stu_id,$new_stu_id,$transfer_dt,$batch_id,$reason);
    $Resp=$stmt->execute();

    //updating batch assign table
    $stmt_upd = $obj->con1->prepare("update batch_assign set student_status=? where batch_id=? and student_id=?");
    $stmt_upd->bind_param("sii", $status,$batch_id,$old_stu_id);
    $Resp=$stmt_upd->execute();

    //check in batch assign for No Batch
    $stmt_batch_select = $obj->con1->prepare("select id,count(*) from batch_assign where student_id=? and batch_id=37");
    $stmt_batch_select->bind_param("i", $new_stu_id);
    $stmt_batch_select->execute();
    $res = $stmt_batch_select->get_result();
    $stmt_batch_select->close();
    $row = mysqli_fetch_array($res);
    if($row[1]>0)
    {
      $stmt_del = $obj->con1->prepare("delete from batch_assign where id='".$row["id"]."'");
      $Resp=$stmt_del->execute(); 
    }

    //insert into batch assign table
    $stmt_ins = $obj->con1->prepare("INSERT INTO `batch_assign`( `batch_id`, `student_id`,`student_status`) VALUES (?,?,?)");
    $stmt_ins->bind_param("iis", $batch_id,$new_stu_id,$student_status);
    $Resp=$stmt_ins->execute();
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
      header("location:transfer.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:transfer.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{  
  $id = $_REQUEST["n_id"];
  $old_stu_id = $_REQUEST['oid'];
  $new_stu_id = $_REQUEST['nid'];
  $batch_id = $_REQUEST['bid'];
  $ongoing_status = 'ongoing';
  $delete_status = 'deleted';
  $no_batch_id = '37';  

  try
  {
    $stmt_del = $obj->con1->prepare("delete from transfer where tid=?");
    $stmt_del->bind_param("i", $id);
    $Resp=$stmt_del->execute();

    //updating batch assign table for old student
    $stmt_upd1 = $obj->con1->prepare("update batch_assign set student_status=? where batch_id=? and student_id=?");
    $stmt_upd1->bind_param("sii", $ongoing_status,$batch_id,$old_stu_id);
    $Resp=$stmt_upd1->execute();

    //updating batch assign table for new student
/*    $stmt_upd2 = $obj->con1->prepare("update batch_assign set student_status=? where batch_id=? and student_id=?");
    $stmt_upd2->bind_param("sii", $delete_status,$batch_id,$new_stu_id);
    $Resp=$stmt_upd2->execute();  */
    //delete from batch assign table for new student
    $stmt = $obj->con1->prepare("delete from batch_assign where batch_id=? and student_id=?");
    $stmt->bind_param("ii", $batch_id,$new_stu_id);
    $Resp=$stmt->execute();

    // check if assigned in any batch
    $stmt_batch_select1 = $obj->con1->prepare("select id,count(*) from batch_assign where student_id=? and student_status!='deleted' and batch_id!=37");
    $stmt_batch_select1->bind_param("i", $new_stu_id);
    $stmt_batch_select1->execute();
    $res1 = $stmt_batch_select1->get_result();
    $stmt_batch_select1->close();
    $row1 = mysqli_fetch_array($res1);

    if($row1[1]==0){
        //check in batch assign for No Batch
        $stmt_batch_select2 = $obj->con1->prepare("select id,count(*) from batch_assign where student_id=? and batch_id=37");
        $stmt_batch_select2->bind_param("i", $new_stu_id);
        $stmt_batch_select2->execute();
        $res2 = $stmt_batch_select2->get_result();
        $stmt_batch_select2->close();
        $row2 = mysqli_fetch_array($res2);
        if($row2[1]==0)
        {
          $stmt_ins = $obj->con1->prepare("INSERT INTO `batch_assign`( `batch_id`, `student_id`,`student_status`) VALUES (?,?,?)");
          $stmt_ins->bind_param("iis", $no_batch_id,$new_stu_id,$ongoing_status);
          $Resp=$stmt_ins->execute();
        }
    }


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
    header("location:transfer.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:transfer.php");
  } 
}

?>

<script type="text/javascript">

  function getBatch(sid){
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=getBatch",
          data: "stu_id="+sid,
          cache: false,
          success: function(result){
            //alert(result);
            var res=result.split("@@@@@");
            alert(res[1]);
            $('#batch_list_div').html('');
            $('#batch_list_div').append(res[0]);
            $('#new_stu_id').html('');
            $('#new_stu_id').append(res[1]);       
            }
        });
  }

  </script>

<h4 class="fw-bold py-3 mb-4">Transfer Master</h4>

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
                      <h5 class="mb-0">Add Transfer Details</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <input type="hidden" name="ttId" id="ttId">
                        
                        <div class="mb-3" >
                          <label class="form-label" for="basic-default-fullname">Old Student</label>
                          <select name="old_stu_id" id="old_stu_id" onchange="getBatch(this.value)" class="form-control" required>
                            <option value="">Select Student</option>
                        <?php 
                          while($stu = mysqli_fetch_array($student_res1)){
                        ?>
                            <option value="<?php echo $stu['sid'] ?>"><?php echo $stu['user_id']."-".$stu['name']."-".$stu['phone'] ?></option>
                        <?php
                          }
                        ?>
                          </select>
                        </div>

                        <div class="mb-3" >
                          <label class="form-label" for="basic-default-fullname">New Student</label>
                          <select name="new_stu_id" id="new_stu_id" class="form-control" required>
                            <option value="">Select Student</option>
                        <?php 
                          while($stu = mysqli_fetch_array($student_res2)){
                        ?>
                            <option value="<?php echo $stu['sid'] ?>"><?php echo $stu['user_id']."-".$stu['name']."-".$stu['phone'] ?></option>
                        <?php
                          }
                        ?>
                          </select>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Batch</label><br/>
                          <div id="batch_list_div"> 
                          </div>
                      </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Transfer Date</label>
                          <input type="date" class="form-control" name="transfer_dt" id="transfer_dt" value="<?php echo $dt ?>" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Reason</label>
                          <textarea class="form-control" name="reason" id="reason" value="<?php echo $dt ?>" required></textarea>
                        </div>
                        
                    <?php if($row["write_func"]=="y"){ ?>
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
                    <?php } if($row["upd_func"]=="y"){ ?>
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
                        <th>Old Student Name</th>
                        <th>New Student Name</th>
                        <th>Batch</th>
                        <th>Transfer Date</th>
                        <th>Reason</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php
                      //select t1.*, DATE_FORMAT(t1.transfer_dt, '%d-%m-%Y') as dt, s1.name as old_stu, s2.name as new_stu, b1.name as batch_name, b1.id as batch_id from transfer t1, student s1, student s2, batch_assign ba1, batch b1 where t1.old_stu_id=s1.sid and t1.new_stu_id=s2.sid and ba1.batch_id=b1.id and ba1.student_id=s1.sid and ba1.student_status='transfered' order by tid desc
                        $stmt_list = $obj->con1->prepare("select t1.*, DATE_FORMAT(t1.transfer_dt, '%d-%m-%Y') as dt, s1.name as old_stu, s2.name as new_stu, b1.name as batch_name, b1.id as batch_id from transfer t1, student s1, student s2, batch b1 where t1.old_stu_id=s1.sid and t1.new_stu_id=s2.sid and t1.batch_id=b1.id order by tid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($trans=mysqli_fetch_array($result))
                        {
                         ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $trans["old_stu"]?></td>
                        <td><?php echo $trans["new_stu"]?></td>
                        <td><?php echo $trans["batch_name"]?></td>
                        <td><?php echo $trans["dt"]?></td>
                        <td><?php echo $trans["reason"]?></td>

                   	<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                		<?php if($row["del_func"]=="y"){ ?>
							            <a  href="javascript:deletedata('<?php echo $trans["tid"]?>','<?php echo $trans["old_stu_id"]?>','<?php echo $trans["new_stu_id"]?>','<?php echo $trans["batch_id"]?>');"><i class="bx bx-trash me-1"></i> </a>
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
  function deletedata(id,old_id,new_id,batch_id) {

      if(confirm("Are you sure to DELETE data?")) {
          //alert("id="+id+"oid="+old_id+"nid="+new_id);
          var loc = "transfer.php?flg=del&n_id="+id+"&oid="+old_id+"&nid="+new_id+"&bid="+batch_id;
          window.location = loc;
      }
  }
  function editdata(tid,old_id,new_id,dt) {
    $('#ttId').val(tid);
    $('#old_stu_id').val(atob(old_id));
    $('#new_stu_id').val(atob(new_id));
    $('#transfer_dt').val(atob(dt));
		$('#btnsubmit').attr('hidden',true);
    $('#btnupdate').removeAttr('hidden');
		$('#btnsubmit').attr('disabled',true);
  }
	function viewdata(tid,old_id,new_id,dt) {
	  $('#ttId').val(tid);
    $('#old_stu_id').val(atob(old_id));
    $('#new_stu_id').val(atob(new_id));
    $('#transfer_dt').val(atob(dt));
		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').attr('hidden',true);
		$('#btnsubmit').attr('disabled',true);
	}
</script>
<?php 
include("footer.php");
?>