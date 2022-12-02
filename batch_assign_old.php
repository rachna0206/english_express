<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"batch_assign")){ }
else{
	header("location:home.php");
}

$stmt_blist = $obj->con1->prepare("select * from branch");
$stmt_blist->execute();
$res = $stmt_blist->get_result();
$stmt_blist->close();

//  student list
$stmt_stu = $obj->con1->prepare("select * from student where sid not in (select student_id from batch_assign )");
$stmt_stu->execute();
$res_Stu = $stmt_stu->get_result();
$stmt_stu->close();

// assigned stu list
$stmt_stu2 = $obj->con1->prepare("select * from student s1,batch_assign b1 where b1.student_id=s1.sid");
$stmt_stu2->execute();
$res_Stu2 = $stmt_stu2->get_result();
$stmt_stu2->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $batch=$_REQUEST['batch'];
  
  $stu1=$_REQUEST['stu_list1'];
  $stu2=$_REQUEST['stu_list2'];

  try
  {
    for($i=0;$i<count($stu2);$i++)
    {
      // check if already exist in batch

      $stmt_check = $obj->con1->prepare("select * from batch_assign where batch_id=? and student_id=?");
      $stmt_check->bind_param("ii", $batch,$stu2[$i]);
      $stmt_check->execute();
      $stmt_check->store_result();
      $num_rows = $stmt_check->num_rows;
      $stmt_check->close();
      if($num_rows==0)
      {
        // insert if not exists
        $stmt = $obj->con1->prepare("INSERT INTO `batch_assign`(`batch_id`,`student_id`) VALUES (?,?)");
        $stmt->bind_param("ii", $batch,$stu2[$i]);
        $Resp=$stmt->execute();
      }

      
    }
    
    if(!$Resp)
    {
      throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
	  setcookie("msg", "data",time()+3600,"/");
      header("location:batch_assign.php");
  }
  else
  {
	   setcookie("msg", "fail",time()+3600,"/");
      header("location:batch_assign.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $bname=$_REQUEST['bname'];
  $contact=$_REQUEST['contact'];
  $address=$_REQUEST['address'];
  $location=$_REQUEST['location'];
  $id=$_REQUEST['ttId'];
 
  try
  {
    $stmt = $obj->con1->prepare("update branch set  name=?,address=?,phone=?,location=? where id=?");
	$stmt->bind_param("ssssi", $bname,$address,$contact,$location,$id);
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
      header("location:branch.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:branch.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
	$stmt_del = $obj->con1->prepare("delete from branch where id='".$_REQUEST["n_id"]."'");
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
    header("location:branch.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:branch.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Branch Master</h4>

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

<?php if($row["write_func"]=="y" || $row["upd_func"]=="y"){ ?> 
              <!-- Basic Layout -->
              <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Assign Batch</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="row">
                            <div class="mb-3 col-6">
                              <label class="form-label" for="basic-default-fullname">Branch </label>
                              
                              <input type="hidden" name="ttId" id="ttId">
                              <select name="branch" id="branch"  class="form-control" required onchange="get_batch(this.value)">
                                  <option value="">--Select Branch--</option>
                                  <?php    
                                        while($branch=mysqli_fetch_array($res)){
                                    ?>
                                            <option value="<?php echo $branch["id"] ?>"><?php echo $branch["name"] ?></option>
                                    <?php
                                        }
                                    ?>

                              </select>
                              
                            </div>
                            <div class="mb-3 col-6">
                              <label class="form-label" for="basic-default-company">Batch</label>
                              <select name="batch" id="batch"  class="form-control" required onchange="get_batch_data(this.value)" >
                                <option value="">--Select Batch--</option>
                              </select>
                            </div>
                        </div>
                        <div id="batch_data">
                        <div class="row" >
                            <div class="mb-3 col-6">
                              <label class="form-label" for="basic-default-fullname">Start Date </label>
                              <input type="date" class="form-control" name="start_date" id="start_date" required />
                              
                            </div>
                            <div class="mb-3 col-6">
                              <label class="form-label" for="basic-default-company">End Date</label>
                              <input type="date"  class="form-control " id="end_date" name="end_date"  required/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-6">
                              <label class="form-label" for="basic-default-fullname">Faculty</label>
                              <input type="text" class="form-control" name="start_date" id="start_date" required />
                              
                            </div>
                            <div class="mb-3 col-6">
                              <label class="form-label" for="basic-default-company">Strength</label>
                              <input type="text"  class="form-control " id="strength" name="strength"  />
                            </div>
                        </div>
                        
                        <div class="row" id="shared-lists">
                          <div class="mb-3 col-6">
                          <label>Student List</label>
                          
                          <select name="stu_list1[]" id="left-select"  class="mb-3 col-md-12" multiple  >
                                  <option value=""></option>
                                  <?php    
                                        while($stu=mysqli_fetch_array($res_Stu)){
                                    ?>
                                            <option value="<?php echo $stu["sid"] ?>" class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center"><?php echo $stu["name"] ?></option>
                                    <?php
                                        }
                                    ?>

                          </select>
                        </div>
                        <div class="mb-3 col-6">
                          <label>Student List</label>
                          <select name="stu_list2[]" id="right-select"  class="mb-3 col-md-12" multiple required >
                                  <option value=""></option>
                                  <?php    
                                        /*while($stu=mysqli_fetch_array($res_Stu2)){
                                    ?>
                                            <option value="<?php echo $stu["sid"] ?>" class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center"><?php echo $stu["name"] ?></option>
                                    <?php
                                        }*/
                                    ?>

                          </select>
                        </div>
                        </div>
                          
                          <!-- <ul class="list-group col" id="left-select1" style="height:300px;overflow-y:auto ;">
                            <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center" id="1">opt1 </li>
                            <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center" id="2">opt2 </li>
                            <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center" id="3">opt3 </li>
                            <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center" id="4">opt4 </li>

                            <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center" id="5">opt5 </li>
                            <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center" id="6">opt6 </li>
                            

                          </ul>
                          <ul class="list-group col" id="right-select" style="height:300px;overflow-y:auto ;">
                            <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center" id="7">opt7 </li>
                            <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center" id="8">opt8 </li>
                            <li class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center" id="9">opt9 </li>
                            
                            

                          </ul> -->
                        </div>

                        
                        
                    <?php if($row["write_func"]=="y"){ ?>
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>
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



<script type="text/javascript">
    function get_batch(branch)
    {
        $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=get_batch",
          data: "branch="+branch,
          cache: false,
          success: function(result){
           // alert(result);
            $('#batch').html('');
            $('#batch').append(result);
       
            }
        });
    }

    function get_batch_data(batch)
    {
        $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=get_batch_data",
          data: "batch="+batch,
          cache: false,
          success: function(result){
           // alert(result);
            $('#batch_data').html('');
            $('#batch_data').html(result);
       
            }
        });
    }
    

  function deletedata(id) {
	  
      if(confirm("Are you sure to DELETE data?")) {
          var loc = "branch.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,name,phone,address,location) {
      
	       //document.getElementById('bname').value=atob(name);
            $('#bname').val(atob(name));
            $('#ttId').val(id);
            $('#contact').val(phone);
            $('#address').val(atob(address));
            $('#location').val(location);
            $('#btnsubmit').attr('hidden',true);
            $('#btnupdate').removeAttr('hidden');
  }
</script>
<!-- Latest Sortable -->
  <script src="Sortable.js"></script>


  <script src="st/app.js"></script>
<?php 
include("footer.php");
?>