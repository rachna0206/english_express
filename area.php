<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"area")){ }
else{
	header("location:home.php");
}

$stmt_state = $obj->con1->prepare("select * from state where status='enable'");
$stmt_state->execute();
$res_state = $stmt_state->get_result();
$stmt_state->close();

$stmt_clist = $obj->con1->prepare("select * from city where status='enable'");
$stmt_clist->execute();
$res = $stmt_clist->get_result();
$stmt_clist->close();


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $city_id = $_REQUEST['city_id'];
  $area_name = $_REQUEST['area'];
  $status = $_REQUEST['status'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `area`(`area_name`,`city_id`,`status`) VALUES (?,?,?)");
	$stmt->bind_param("sis",$area_name,$city_id,$status);
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
    //  header("location:area.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
    //  header("location:area.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $city_id = $_REQUEST['city_id'];
  $area_name = $_REQUEST['area'];
  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];

  try
  {
    
    $stmt = $obj->con1->prepare("update area set area_name=?, city_id=?, status=? where id=?");
  	$stmt->bind_param("sisi", $area_name,$city_id,$status,$id);
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
      header("location:area.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
    header("location:area.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from area where id='".$_REQUEST["n_id"]."'");
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
    header("location:area.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:area.php");
  }
}

?>

<script type="text/javascript">
function get_city(state){
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=cityList",
          data: "state_id="+state,
          cache: false,
          success: function(result){
           
            $('#city_id').html('');
            $('#city_id').append(result);
       
            }
        });
  }
  </script>

<h4 class="fw-bold py-3 mb-4">Area Master</h4>

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
                      <h5 class="mb-0">Add Area</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <input type="hidden" name="ttId" id="ttId">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">State</label>
                          <select name="state_id" id="state_id" class="form-control" onchange="get_city(this.value)" required >
                            <option value="">Select State</option>
                    <?php    
                        while($city=mysqli_fetch_array($res_state)){
                    ?>
                        <option value="<?php echo $city["state_id"] ?>"><?php echo $city["state_name"] ?></option>
                    <?php
            }
          ?>
                </select>
                         
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">City</label>
                          <select name="city_id" id="city_id" class="form-control" required>
                          	<option value="">Select City</option>
                    <?php    
                        while($city=mysqli_fetch_array($res)){
                    ?>
                    		<option value="<?php echo $city["city_id"] ?>"><?php echo $city["city_name"] ?></option>
                    <?php
						}
					?>
					      </select>
                          
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Area</label>
                          <input type="text" class="form-control" name="area" id="area" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label d-block" for="basic-default-fullname">Status</label>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="enable" value="enable" checked required >
                            <label class="form-check-label" for="inlineRadio1">Enable</label>
                          </div>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="status" id="disable" value="disable" required>
                            <label class="form-check-label" for="inlineRadio1">Disable</label>
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
           
<?php } ?>

<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
           <!-- grid -->

           <!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Area Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Area</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select a.*,c.city_name,c.state_id from area a, city c where a.city_id=c.city_id order by a.id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($area=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $area["area_name"]?></td>
                        <td><?php echo $area["city_name"]?></td>
                        <td><?php echo $area["status"]?></td>
                        
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a href="javascript:editdata('<?php echo $area["id"]?>','<?php echo $area["city_id"]?>','<?php echo base64_encode($area["area_name"])?>','<?php echo $area["status"]?>','<?php echo $area["state_id"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $area["id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                        	<a href="javascript:viewdata('<?php echo $area["id"]?>','<?php echo $area["city_id"]?>','<?php echo base64_encode($area["area_name"])?>','<?php echo $area["status"]?>','<?php echo $area["state_id"]?>');">View</a>
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
          var loc = "area.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,city_id,aname,status,state) {
           
		  $('#ttId').val(id);
      $('#state_id').val(state);
      get_city(state);
      setTimeout(function() {
        $('#city_id').val(city_id);
      }, 1000);
      
			$('#area').val(atob(aname));
			if(status=="enable")
		  {
				$('#enable').attr("checked","checked");	
	   	}
	   	else if(status=="disable")
		  {
				$('#disable').attr("checked","checked");	
		  }
			
			$('#btnsubmit').attr('hidden',true);
      $('#btnupdate').removeAttr('hidden');
			$('#btnsubmit').attr('disabled',true);

  }
  function viewdata(id,city_id,aname,status,state) {
           
		  $('#ttId').val(id);
      $('#state_id').val(state);
      get_city(state);
      setTimeout(function() {
        $('#city_id').val(city_id);
      }, 1000);
      
      $('#area').val(atob(aname));
			if(status=="enable")
		   	{
				$('#enable').attr("checked","checked");	
		   	}
		   	else if(status=="disable")
		   	{
				$('#disable').attr("checked","checked");	
		   	}
			
			$('#btnsubmit').attr('hidden',true);
            $('#btnupdate').attr('hidden',true);
			$('#btnsubmit').attr('disabled',true);

        }
        
</script>
<?php 
include("footer.php");
?>