<?php
include("header.php");
/*function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler('exceptions_error_handler');*/

// for permission
include_once("checkPer.php");
if($row=checkPermission($_SESSION["utype"],"skill_master")){ }
else{
	header("location:home.php");
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $skill=$_REQUEST['skill'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `skill`(`skills`) VALUES (?)");
	$stmt->bind_param("s", $skill);
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
      header("location:skills.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:skills.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $skill=$_REQUEST['skill'];
  $id=$_REQUEST['ttId'];

  try
  {
	$stmt = $obj->con1->prepare("update skill set skills=? where skid=?");
	$stmt->bind_param("si", $skill,$id);
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
      header("location:skills.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:skills.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{  
  try
  {
    
    $stmt_del = $obj->con1->prepare("delete from skill where skid='".$_REQUEST["n_id"]."'");
    $Resp=$stmt_del->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
      //throw new Exception("Problem in deleting! ". $obj->con1-> error);
    }
    $stmt_del->close();
  } 
  catch(\Exception  $e) {
   // $error=  $e->getMessage();
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }
 
 
  if($Resp)
  {
    setcookie("msg", "data_del",time()+3600,"/");
    header("location:skills.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:skills.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Skills Master</h4>

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
                      <h5 class="mb-0">Add Skills</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Skills</label>
                          <input type="text" class="form-control" name="skill" id="skill" required />
                          <input type="hidden" name="ttId" id="ttId">
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
                        <th>Skills</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from skill order by skid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($s=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $s["skills"]?></td>
                        
                   	<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a href="javascript:editdata('<?php echo $s["skid"]?>','<?php echo base64_encode($s["skills"])?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                		<?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $s["skid"]?>');"><i class="bx bx-trash me-1"></i> </a>
						<?php } if($row["read_func"]=="y"){ ?>
                        	<a href="javascript:viewdata('<?php echo $s["skid"]?>','<?php echo base64_encode($s["skills"])?>');">View</a>
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
          var loc = "skills.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,skill) {
           
         //  document.getElementById('bname').value=atob(name);
            //$('#bname').val(atob(name));
            $('#ttId').val(id);
            $('#skill').val(atob(skill));
			$('#btnsubmit').attr('hidden',true);
            $('#btnupdate').removeAttr('hidden');
			$('#btnsubmit').attr('disabled',true);

        }
	function viewdata(id,skill) {
	   
		$('#ttId').val(id);
		$('#skill').val(atob(skill));
		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').attr('hidden',true);
		$('#btnsubmit').attr('disabled',true);
	}
</script>
<?php 
include("footer.php");
?>