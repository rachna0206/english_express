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

$stmt_blist = $obj->con1->prepare("select * from books");
$stmt_blist->execute();
$res = $stmt_blist->get_result();
$stmt_blist->close();

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  
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
   
	$stmt = $obj->con1->prepare("INSERT INTO `stu_assignment`(`batch_id`,`stu_id`,`book_id`,`chap_id`,`exercise_id`,`alloted_dt`,`expected_dt`,`faculty_id`,`status`) VALUES (?,?,?,?,?,?,?,?,?)");
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

    setcookie("msg", "data",time()+3600,"/");
    header("location:stu_assignment.php");
}

if(isset($_REQUEST['btnupdate']))
{

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

	function studList(batch_id){

    	$.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=studList",
          data: "batch_id="+batch_id,
          cache: false,
          success: function(result){
            //alert(result);
            $('#stu_list_div').html('');
            $('#stu_list_div').append(result);
       
            }
        });
		
		$.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=facultyList",
          data: "batch_id="+batch_id,
          cache: false,
          success: function(result){
          //  alert(result);
            $('#faculty').html('');
            $('#faculty').append(result);
       
            }
        });

	}
	function chapList(book_id){

		$.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=chapList",
          data: "book_id="+book_id,
          cache: false,
          success: function(result){
           // alert(result);
            $('#chap').html('');
            $('#chap').append(result);
       
            }
        });

	}
	function exerList(chap_id){

		$.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=exerList",
          data: "chap_id="+chap_id,
          cache: false,
          success: function(result){
            //alert(result);
            $('#exer_list_div').html('');
            $('#exer_list_div').append(result);
       
            }
        });
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
                          	<option value="-1">Select Batch</option>
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
                          <div id="stu_list_div" class="row">
                    <?php    
                        while($stu=mysqli_fetch_array($r2)){
					?>      
                          <input type="checkbox" id="stu_list" name="s[]" value="<?php echo $stu["student_id"] ?>" checked="checked"/> <?php echo $stu["name"] ?>
                    <?php
						}
					?>
                    	</div>
                    </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Book Name</label>
                          <select name="book" id="book" onChange="chapList(this.value)" class="form-control" required>
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
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Chapter Name</label>
                          <select name="chap" id="chap" onchange="exerList(this.value)" class="form-control" required>
                          	<option value="">Select Chapter</option>
                            <div id="chap_list_div" >
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
                    		</div>
					      </select>
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Exercise Name</label><br/>
                          <div id="exer_list_div" class="row">
  		    		<?php    
                        while($exer=mysqli_fetch_array($r3)){	
                    ?>
                    		<input type="checkbox" name="e[]" id="" value="<?php echo $exer["eid"] ?>"/> <?php echo $exer['exer_name'] ?>
                    <?php
                    	}
					?>
                    	</div>
                      </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Date</label>
                          <input type="date" class="form-control" name="dt" id="dt" value="<?php echo date('Y-m-d') ?>" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Expected Date</label>
                          <input type="date" class="form-control" name="expec_dt" id="expec_dt" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Faculty Name</label>
                          <div id="faculty_list_div">
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
                        $stmt_list = $obj->con1->prepare("select sa.*, ba.name bname, s.name sname, b.bookname, c.chapter_name, e.exer_name, f.name fname  from stu_assignment sa, batch ba, books b, chapter c, student s, exercise e, faculty f where sa.batch_id= ba.id and sa.stu_id=s.sid and sa.book_id=b.bid and sa.chap_id=c.cid and sa.exercise_id=e.eid and sa.faculty_id=f.id order by said desc");
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
                        <?php if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $a["said"]?>');"><i class="bx bx-trash me-1"></i> </a>
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