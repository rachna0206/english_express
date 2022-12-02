<?php
include("header.php");
error_reporting(0);
// for permission
if($row=checkPermission($_SESSION["utype"],"exercise_master")){ }
else{
	header("location:home.php");
}



$stmt_balist = $obj->con1->prepare("select * from batch");
$stmt_balist->execute();
$r1 = $stmt_balist->get_result();
$stmt_balist->close();

$baid = "";
if(isset($_COOKIE["baid"])){
	$baid = $_COOKIE["baid"];

	$stmt_slist = $obj->con1->prepare("select b.student_id, s.name from batch_assign b, student s where b.student_id=s.sid and b.batch_id=$baid");
	$stmt_slist->execute();
	$r2 = $stmt_slist->get_result();
	$stmt_slist->close();
	
	$stmt_flist = $obj->con1->prepare("SELECT f1.id , f1.name  FROM `batch` b1 , faculty f1 where b1.faculty_id = f1.id and b1.id=$baid union SELECT f2.id , f2.name  FROM `batch` b1 , faculty f2 where b1.assist_faculty_1 = f2.id and b1.id=$baid union SELECT f3.id , f3.name  FROM `batch` b1 , faculty f3 where b1.assist_faculty_2 = f3.id and b1.id=$baid");
	$stmt_flist->execute();
	$r4 = $stmt_flist->get_result();
	$stmt_flist->close();
}


$stmt_blist = $obj->con1->prepare("select * from books");
$stmt_blist->execute();
$res = $stmt_blist->get_result();
$stmt_blist->close();

$id = "";
if(isset($_COOKIE["bid"])){
	$id = $_COOKIE["bid"];

	$stmt_clist = $obj->con1->prepare("select * from chapter where book_id=$id");
	$stmt_clist->execute();
	$res1 = $stmt_clist->get_result();
	$stmt_clist->close();
}

$cid = "";
if(isset($_COOKIE["eid"])){
	$cid = $_COOKIE["eid"];
	
	$stmt_elist = $obj->con1->prepare("select eid,exer_name from exercise where book_id=$id and chap_id=$cid");
	$stmt_elist->execute();
	$r3 = $stmt_elist->get_result();
	$stmt_elist->close();
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  echo "in btnsubmit";
  $batch_id = $_REQUEST['batch'];
  $book_id = $_REQUEST['book'];
  $chap_id = $_REQUEST['chap'];
  $alloted_dt = $_REQUEST['dt'];
  $expected_dt = $_REQUEST['expec_dt'];
  $faculty_id = $_REQUEST['faculty'];
  $status = "pending";
  
  foreach($_REQUEST['s'] as $stu_id){
    
  	foreach($_REQUEST['e'] as $exer_id){
      
    
  try
  {
   
	$stmt = $obj->con1->prepare("INSERT INTO `stu_assignment`(`batch_id`,`stu_id`,`book_id`,`chap_id`,`name`,`alloted_dt`,`expected_dt`,`faculty_id`,`status`) VALUES (?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param("iiiisssis",$batch_id,$stu_id,$book_id,$chap_id,$exer_id,$alloted_dt,$expected_dt,$faculty_id,$status);
	$Resp=$stmt->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in inserting! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }
  
	}
  }
  
//  	setcookie("bid","",time()-360);
//	setcookie("baid","",time()-360);
//	setcookie("said","",time()-360);


  if($Resp)
  {
	  setcookie("msg", "data",time()+3600,"/");
      header("location:stu_assignment.php");
 }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:stu_assignment.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
/*  $book_id = $_REQUEST['book'];
  $ch_id = $_REQUEST['chap'];
  $num_exercise = $_REQUEST['num'];
  $eid = $_REQUEST['ttId'];

  try
  {
    $stmt = $obj->con1->prepare("update exercise set book_id=?, chap_id=?, exer_name=?, grammer=?, vocabulary=?, pronunciation=?, spelling=?, presentation=?, speaking=?, listening=?, writing=?, reading=? where eid=?");
	$stmt->bind_param("iissssssssssi", $book_id,$ch_id,$exer_name,$grammer,$vocabulary,$pronunciation,$spelling,$presentation,$speaking,$listening,$writing,$reading,$eid);
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
      header("location:stu_assignment.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:stu_assignment.php");
  }	*/
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from stu_assignment where said='".$_REQUEST["n_id"]."'");
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
    header("location:stu_assignment.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:stu_assignment.php");
  }
}

?>

<script type="text/javascript">

	function chapList(x){
		b = x.value;
		$('#book').val(b);
		document.cookie = "bid="+b;
		window.location = "stu_assignment.php";
	}
	function studList(batch_id){
		/*s = y.value;
		$('#batch').val(s);
		document.cookie = "baid="+s;
		window.location = "stu_assignment.php";*/
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=studList",
          data: "batch_id="+batch_id,
          cache: false,
          success: function(result){
           // alert(result);
            $('#stu_list_div').html('');
            $('#stu_list_div').append(result);
       
            }
        });

	}
	function exerList(z){
		e = z.value;
		$('#chap').val(e);
		document.cookie = "eid="+e;
		window.location = "stu_assignment.php";
	}

</script>

<h4 class="fw-bold py-3 mb-4">Student Assignment Master</h4>

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
                      <h5 class="mb-0">Add Assignment</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Batch Name</label>
                          <select name="batch" id="batch" onChange="studList(this.value)" class="form-control" required>
                          	<option value="">Select Batch</option>
                    <?php    
                        while($batch=mysqli_fetch_array($r1)){

							if($baid==$batch["id"]){
                    ?>
                    		<option value="<?php echo $batch["id"] ?>" selected="selected"><?php echo $batch["name"] ?></option>
                    <?php
							}else{
					?>
                            <option value="<?php echo $batch["id"] ?>"><?php echo $batch["name"] ?></option>
                    <?php
							}
						}
					?>
					      </select>
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Students</label>
                          <div id="stu_list_div">
                    <?php    
                        while($stu=mysqli_fetch_array($r2)){
					?>      
                          <input type="checkbox" id="stu_list" name="s[]" id="" value="<?php echo $stu["student_id"] ?>" checked="checked"/> <?php echo $stu["name"] ?>
                    <?php
						}
					?>
                    	</div>
                    </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Book Name</label>
                          <select name="book" id="book" onChange="chapList(this)" class="form-control" required>
                          	<option value="-1">Select Book</option>
                    <?php    
                        while($book=mysqli_fetch_array($res)){
							if($id==$book["bid"]){
                    ?>
                    		<option value="<?php echo $book["bid"] ?>" selected="selected"><?php echo $book["bookname"] ?></option>
                    <?php
							}else{
					?>
                            <option value="<?php echo $book["bid"] ?>"><?php echo $book["bookname"] ?></option>
                    <?php
							}
						}
					?>
					      </select>
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Chapter Name</label>
                          <select name="chap" id="chap" onchange="exerList(this)" class="form-control" required>
                          	<option value="">Select Chapter</option>
                    <?php    
                        while($chap=mysqli_fetch_array($res1)){
							if($cid==$chap["cid"]){	
                    ?>
                    		<option value="<?php echo $chap["cid"] ?>" selected="selected"><?php echo $chap["chapter_name"] ?></option>
                    <?php
							} else{
					?>
                    		<option value="<?php echo $chap["cid"] ?>"><?php echo $chap["chapter_name"] ?></option>
                    <?php
							}
						}
					?>
					      </select>
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Exercise Name</label><br/>
  		    		<?php    
                        while($exer=mysqli_fetch_array($r3)){	
                    ?>
                    		<input type="checkbox" name="e[]" id="" value="<?php echo $exer["eid"] ?>"/> <?php echo $exer['exer_name'] ?>
                    <?php
                    	}
					?>
                      </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Date</label>
                          <input type="date" class="form-control" name="dt" id="dt" required value="<?php echo date("Y-m-d")?>" />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Expected Date</label>
                          <input type="date" class="form-control" name="expec_dt" id="expec_dt" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Faculty Name</label>
                          <select name="faculty" id="faculty" class="form-control" required>
                          	<option value="">Select Faculty Name</option>   
		    		<?php    
                        while($f=mysqli_fetch_array($r4)){	
                    ?>
                    		<option value="<?php echo $f["id"] ?>"><?php echo $f["name"] ?></option>
                    <?php
                    	}
					?>
     					</select>

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
                <h5 class="card-header">Exercise Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Batch Name</th>
                        <th>Student Name</th>
                        <th>Book Name</th>
                        <th>Chapter Name</th>
                        <th>Exercise Name</th>
                        <th>Alloted Date</th>
                        <th>Expected Date</th>
                        <th>Faculty Name</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select sa.*, ba.name bname, s.name sname, b.bookname, c.chapter_name, e.exer_name, f.name fname  from stu_assignment sa, batch ba, books b, chapter c, student s, exercise e, faculty f where sa.batch_id= ba.id and sa.stu_id=s.sid and sa.book_id=b.bid and sa.chap_id=c.cid and sa.name=e.eid and sa.faculty_id=f.id order by said desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($a=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $a["bname"] ?></td>
                        <td><?php echo $a["sname"] ?></td>
                        <td><?php echo $a["bookname"] ?></td>
                        <td><?php echo $a["chapter_name"] ?></td>
                        <td><?php echo $a["exer_name"] ?></td>
                        <td><?php echo $a["alloted_dt"] ?></td>
                        <td><?php echo $a["expected_dt"] ?></td>
                        <td><?php echo $a["fname"] ?></td>
                        
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a href="javascript:editdata('<?php echo $a["said"] ?>','<?php echo $a["batch_id"] ?>','<?php echo $a["stu_id"] ?>','<?php echo $a["book_id"] ?>','<?php echo $a["chap_id"] ?>','<?php echo $a["name"] ?>','<?php echo $a["alloted_dt"] ?>','<?php echo $a["expected_dt"] ?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $a["said"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                        	<a href="javascript:viewdata('<?php echo $a["said"] ?>','<?php echo $a["batch_id"] ?>','<?php echo $a["stu_id"] ?>','<?php echo $a["book_id"] ?>','<?php echo $a["chap_id"] ?>','<?php echo $a["name"] ?>','<?php echo $a["alloted_dt"] ?>','<?php echo $a["expected_dt"] ?>');">View</a>
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
          var loc = "stu_assignment.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
</script>
<?php 
include("footer.php");
?>