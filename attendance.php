<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"attendance")){ }
else{
	header("location:home.php");
}


$stmt_blist = $obj->con1->prepare("select * from batch");
$stmt_blist->execute();
$res = $stmt_blist->get_result();
$stmt_blist->close();	

$batch_id="";
$dt=date("Y-m-d");

if(isset($_REQUEST['submit1']))
{
	$atten = "p";
	foreach($_REQUEST['faculty_atten'] as $aid)
	{
		echo $aid."<br/>";	
	  try
	  {
		$stmt = $obj->con1->prepare("update attendance set faculty_attendance=? where aid=?");
		$stmt->bind_param("si", $atten,$aid);
		$Resp=$stmt->execute();
		if(!$Resp)
		{
		  throw new Exception("Problem in inserting! ". strtok($obj->con1-> error,  '('));
		}
		$stmt->close();
	  } 
	  catch(\Exception  $e) {
		setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
	  }
	
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
					
					<button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>

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
                  ?>
                <div class="table-responsive text-nowrap">
                  <table class="table">
				  
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Student Name</th>
                        <th>Attendance by Student</th>
            						<th>Attendance by Faculty</th>
            						<th>Remark</th>
                      </tr>
                    </thead>
                    <form method="post"> 
                    <tbody class="table-border-bottom-0">
                      <?php
          					
          							
          						$stmt_list = $obj->con1->prepare("select a.*,s.name from attendance a, student s where a.student_id=s.sid and batch_id = '".$batch_id."' and dt ='".$dt."'");
          						$stmt_list->execute();
          						$result = $stmt_list->get_result();
          						$stmt_list->close();
          					  
          						$i=1;
                                  while($a=mysqli_fetch_array($result))
                                  {
                                ?>

                                <tr>
                                  <td><?php echo $i?></td>
                                  <td><?php echo $a["name"]?></td>
                      						<td style="color:<?php echo ($a["stu_attendance"]=="p")?'green':'red' ?>"><?php echo ucfirst($a["stu_attendance"])?></td>
                      						<td>
                                		<input type="checkbox" name="faculty_atten[]" value="<?php echo $a["aid"] ?>"  <?php echo ($a["faculty_attendance"]=="p")?"checked='checked'":""?>/>
                                    
                                  </td>
          						              <td><input type="text" class="form-control" name="r[]" id="" value="<?php echo $a["remark"] ?>"/></td>
                                </tr>
                                <?php
                                    $i++;
                                  }
          						
                      ?>

                      
                    </tbody>
                  </table>
                  <button type="submit" name="submit1" id="submit1" class="btn btn-primary">Submit</button>
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