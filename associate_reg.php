<?php
include("header.php");

// for permission
include_once("checkPer.php");
if($row=checkPermission($_SESSION["utype"],"associate_reg")){ }
else{
	header("location:home.php");
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $associate_name = $_REQUEST['associate'];
  $contact_no = $_REQUEST['contact'];
  $firm_name = $_REQUEST['firm'];
  $house_no = $_REQUEST['house_no'];
  $society = $_REQUEST['society_name'];
  $village = $_REQUEST['village'];
  $landmark = $_REQUEST['landmark'];
  $city = $_REQUEST['city'];
  $state = $_REQUEST['state'];
  $pin_code = $_REQUEST['pin'];
  $status = $_REQUEST['status'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `associate`(`associate_name`, `contact_no`, `firm_name`, `house_no`, `society_name`, `village`, `landmark`, `city`, `state`, `pin`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param("sssssssiiss", $associate_name,$contact_no,$firm_name,$house_no,$society,$village,$landmark,$city,$state,$pin_code,$status);
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
      header("location:associate_reg.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:associate_reg.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $associate_name = $_REQUEST['associate'];
  $contact_no = $_REQUEST['contact'];
  $firm_name = $_REQUEST['firm'];
  $house_no = $_REQUEST['house_no'];
  $society = $_REQUEST['society_name'];
  $village = $_REQUEST['village'];
  $landmark = $_REQUEST['landmark'];
  $city = $_REQUEST['city'];
  $state = $_REQUEST['state'];
  $pin_code = $_REQUEST['pin'];
  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];

  try
  {
	$stmt = $obj->con1->prepare("update associate set associate_name=?, contact_no=?, firm_name=?, house_no=?, society_name=?, village=?, landmark=?, city=?, state=?, pin=?, status=? where aid=?");
	$stmt->bind_param("sssssssiissi", $associate_name,$contact_no,$firm_name,$house_no,$society,$village,$landmark,$city,$state,$pin_code,$status,$id);
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
      header("location:associate_reg.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:associate_reg.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{  
  try
  {
    
    $stmt_del = $obj->con1->prepare("delete from associate where aid='".$_REQUEST["n_id"]."'");
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
    header("location:associate_reg.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:associate_reg.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Associate Master</h4>

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
                      <h5 class="mb-0">Add Associate</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Associate Name</label>
                          <input type="text" class="form-control" name="associate" id="associate" required />
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Contact Number</label>
                          <input type="text" class="form-control" name="contact" id="contact" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Firm Name</label>
                          <input type="text" class="form-control" name="firm" id="firm" required />
                        </div>
                        
                          <label class="form-label" for="basic-default-message">Address :</label>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Flat / House No.</label>
                          <input type="text" class="form-control" name="house_no" id="house_no" required />
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Society Name</label>
                          <input type="text" class="form-control" name="society_name" id="society_name" required />
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Village</label>
                          <input type="text" class="form-control" name="village" id="village" required />
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Landmark</label>
                          <input type="text" class="form-control" name="landmark" id="landmark" required />
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">State</label>
                          <select name="state" id="state" onchange="cityList(this.value)" class="form-control" required>
                          	<option value="">Select State</option>
                    <?php    
                        while($state=mysqli_fetch_array($res)){
                    ?>
                    		<option value="<?php echo $state["state_id"] ?>"><?php echo $state["state_name"] ?></option>
                    <?php
						}
					?>
					      </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">City</label>
                          <select name="city" id="city" class="form-control" required>
                            <option value="">Select City</option>
		                  </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Pin Code</label>
                          <input type="text" class="form-control" name="pin" id="pin" required />
                        </div>                        
                        
                        <div class="mb-3">
                          <label class="form-label d-block" for="basic-default-fullname">Status</label>
                          
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="active" value="active" required >
                            <label class="form-check-label" for="inlineRadio1">Active</label>
                          </div>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="inactive" value="inactive" required>
                            <label class="form-check-label" for="inlineRadio1">Inactive</label>
                          </div>
                         
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
           
<?php  } ?>

<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
           <!-- grid -->

           <!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Associate Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Associate Name</th>
                        <th>Contact Number</th>
                        <th>Firm Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from associate order by aid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($a=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $a["associate_name"]?></td>
                        <td><?php echo $a["contact_no"]?></td>
                        <td><?php echo $a["firm_name"]?></td>
                        <td><?php echo $a["status"]?></td>
                        
                   	<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a href="javascript:editdata('<?php echo $a["aid"]?>','<?php echo base64_encode($a["associate_name"])?>','<?php echo base64_encode($a["contact_no"])?>','<?php echo base64_encode($a["firm_name"])?>','<?php echo base64_encode($a["house_no"])?>','<?php echo base64_encode($a["society_name"])?>','<?php echo base64_encode($a["village"])?>','<?php echo base64_encode($a["landmark"])?>','<?php echo $a["city"]?>','<?php echo $a["state"]?>','<?php echo base64_encode($a["pin"])?>','<?php echo $a["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                		<?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $a["aid"]?>');"><i class="bx bx-trash me-1"></i> </a>
						<?php } if($row["read_func"]=="y"){ ?>
                        	<a href="javascript:viewdata('<?php echo $a["aid"]?>','<?php echo base64_encode($a["associate_name"])?>','<?php echo base64_encode($a["contact_no"])?>','<?php echo base64_encode($a["firm_name"])?>','<?php echo base64_encode($a["house_no"])?>','<?php echo base64_encode($a["society_name"])?>','<?php echo base64_encode($a["village"])?>','<?php echo base64_encode($a["landmark"])?>','<?php echo $a["city"]?>','<?php echo $a["state"]?>','<?php echo base64_encode($a["pin"])?>','<?php echo $a["status"]?>');">View</a>
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
          var loc = "associate_reg.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,assoc_name,contact_no,firm_name,house_no,society,village,landmark,city,state,pin,status) {
           
            $('#ttId').val(id);
            $('#associate').val(atob(assoc_name));
			$('#contact').val(atob(contact_no));
			$('#firm').val(atob(firm_name));
			$('#house_no').val(atob(house_no));
			$('#society_name').val(atob(society));
			$('#village').val(atob(village));
			$('#landmark').val(atob(landmark));
			$('#state').val(state);
			cityList(state);
			cityList(state);
			setTimeout(function() {
				$('#city').val(city);
			}, 1000);
			$('#pin').val(atob(pin));
			$('#status').val(atob(status));
			if(status=="active")
			{
				$('#active').attr("checked","checked");	
			}
			else if(status=="inactive")
			{
				$('#inactive').attr("checked","checked");	
			}
			$('#btnsubmit').attr('hidden',true);
			$('#btnsubmit').attr('disabled',true);
            $('#btnupdate').removeAttr('hidden');

        }
	function viewdata(id,assoc_name,contact_no,firm_name,house_no,society,village,landmark,city,state,pin,status) {
	   
			$('#ttId').val(id);
            $('#associate').val(atob(assoc_name));
			$('#contact').val(atob(contact_no));
			$('#firm').val(atob(firm_name));
			$('#house_no').val(atob(house_no));
			$('#society_name').val(atob(society));
			$('#village').val(atob(village));
			$('#landmark').val(atob(landmark));
			$('#state').val(state);
			cityList(state);
			cityList(state);
			setTimeout(function() {
				$('#city').val(city);
			}, 1000);
			$('#pin').val(atob(pin));
			$('#status').val(atob(status));
			if(status=="active")
			{
				$('#active').attr("checked","checked");	
			}
			else if(status=="inactive")
			{
				$('#inactive').attr("checked","checked");	
			}
		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').attr('hidden',true);
		$('#btnsubmit').attr('disabled',true);
	}
</script>
<?php 
include("footer.php");
?>