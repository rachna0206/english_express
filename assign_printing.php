<?php
include("header.php");

// for permission
include_once("checkPer.php");
if($row=checkPermission($_SESSION["utype"],"printing")){ }
else{
	header("location:home.php");
}

$stmt_blist = $obj->con1->prepare("select * from books");
$stmt_blist->execute();
$book_res = $stmt_blist->get_result();
$stmt_blist->close();

$stmt_flist = $obj->con1->prepare("select id,name from faculty where designation!='Associate' and status='active'");
$stmt_flist->execute();
$faculty_res = $stmt_flist->get_result();
$stmt_flist->close();


if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="upd")
{
  try
  {
	$stmt = $obj->con1->prepare("update printing set status='completed' where pid='".$_REQUEST["id"]."'");
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
    header("location:assign_printing.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:assign_printing.php");
  }	
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $book_id=$_REQUEST['book'];
  $chap_id=$_REQUEST['chap'];
  $faculty_id=$_REQUEST['faculty'];
  $copies=$_REQUEST['copies'];
  $dt=date('Y-m-d');
  $status="pending";
   
  try
  {
    
  $stmt = $obj->con1->prepare("INSERT INTO `printing`(`faculty_id`,`book_id`,`chap_id`,`copies`,`dt`,`status`) VALUES (?,?,?,?,?,?)");
  $stmt->bind_param("iiiiss", $faculty_id,$book_id,$chap_id,$copies,$dt,$status);
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

  if($Resp)
  {
      setcookie("msg", "data",time()+3600,"/");          
      header("location:assign_printing.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:assign_printing.php");
  }
}


if(isset($_REQUEST['btnupdate']))
{
  $book_id=$_REQUEST['book'];
  $chap_id=$_REQUEST['chap'];
  $faculty_id=$_REQUEST['faculty'];
  $copies=$_REQUEST['copies'];
  $dt=date('Y-m-d');
  $status="pending";
  $id=$_REQUEST['ttId'];

  try
  {
	$stmt = $obj->con1->prepare("update printing set faculty_id=?, book_id=?, chap_id=? copies=?, dt=?, status=? where pid=?");
	$stmt->bind_param("iiiiissi", $faculty_id,$book_id,$chap_id,$dt,$copies,$id);
	$Resp=$stmt->execute();
	if(!$Resp)
  	{
        throw new Exception("Problem in updating! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }


  if($Resp)
  {
    setcookie("msg", "update",time()+3600,"/");
      header("location:assign_printing.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:assign_printing.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{  
  try
  {
    
    $stmt_del = $obj->con1->prepare("delete from printing where pid='".$_REQUEST["n_id"]."'");
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
    header("location:assign_printing.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:assign_printing.php");
  }
}

?>

<script type="text/javascript">
	function changeStatus(id){
		if(confirm("Is Printing Completed?")) {
			var loc = "assign_printing.php?flg=upd&id=" + id;
          	window.location = loc;
		}
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
	function getChapPageList(chap_id){

		$.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=getChapPageList",
          data: "chap_id="+chap_id,
          cache: false,
          success: function(result){
            $('#page_list_div').html('');
            $('#page_list_div').html(result);
            }
        });
	}
</script>

<h4 class="fw-bold py-3 mb-4">Printing</h4>

<?php 
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
  if($_COOKIE['msg']=="ex_not_found")
  {
  ?>

  <div class="alert alert-danger alert-dismissible" role="alert">
    Please select atleast one exercise
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
  </div>
  <script type="text/javascript">eraseCookie("msg")</script>
  <?php
  }

}
  
?>

<?php if($row["write_func"]=="y" || $row["upd_func"]=="y" || $row["read_func"]=="y"){ ?>
              <!-- Basic Layout -->
              <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Add Printing Task</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                    
                    <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Book Name</label>
                          <select name="book" id="book" onChange="chapList(this.value)" class="form-control" required>
                          	<option value="">Select Book</option>
                    <?php    
                        while($book=mysqli_fetch_array($book_res)){
					 ?>
                    		<option value="<?php echo $book["bid"] ?>"><?php echo $book["bookname"] ?></option>
                    <?php
						}
					?>
					      </select>
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Chapter Name</label>
                          <select name="chap" id="chap" onchange="getChapPageList(this.value)" class="form-control" required>
                          	<!-- <option value="">Select Chapter</option> -->
                          </select>
                        </div>
                        
                        <div id="page_list_div"></div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Number of Copies</label>
                          <input type="number" class="form-control" name="copies" id="copies" step="1" min="1" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Faculty Name</label>
                          <select name="faculty" id="faculty" class="form-control" required>
                          	<option value="">Select Faculty Name</option>   
		    		<?php    
                        while($faculty=mysqli_fetch_array($faculty_res)){	
                    ?>
                    		<option value="<?php echo $faculty["id"] ?>"><?php echo $faculty["name"] ?></option>
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


<?php if($row["read_func"]=="y"){ ?>
              <!-- Basic Layout -->
              <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Print Documents</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
           <!-- Basic Bootstrap Table -->
              <div class="card">
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Faculty</th>
                        <th>Book Name</th>
            						<th>Chapter Name</th>
            						<th>No. Of Copies</th>
                        <th>Start Page</th>
                        <th>End Page</th>
            						<th>Date</th>
            						<th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt1_list = $obj->con1->prepare("select p.*,DATE_FORMAT(p.dt, '%d-%m-%Y') as a_dt,f.name,b.bookname,c.chapter_name,c.start_pg,c.end_pg from printing p, faculty f, books b, chapter c where p.faculty_id=f.id and p.book_id=b.bid and p.chap_id=c.cid order by pid desc");
                        $stmt1_list->execute();
                        $result1 = $stmt1_list->get_result();
                        
                        $stmt1_list->close();
                        $i=1;
                        while($print=mysqli_fetch_array($result1))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $print["name"]?></td>
                        <td><?php echo $print["bookname"]?></td>
            						<td><?php echo $print["chapter_name"]?></td>
            						<td><?php echo $print["copies"]?></td>
                        <td><?php echo $print["start_pg"]?></td>
                        <td><?php echo $print["end_pg"]?></td>
            						<td><?php echo $print["a_dt"]?></td>
            						<td><?php if($row["upd_func"]=="y"){ 
            					if($print["status"]=="pending"){ ?>
							<a href="javascript:changeStatus('<?php echo $print["pid"]?>');"><?php echo $print["status"]?></a>
							<?php } else{ echo $print["status"]; }
									} ?>
						</td>
                        
                   	<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a href="javascript:editdata('<?php echo $print["pid"]?>','<?php echo $print["faculty_id"]?>','<?php echo $print["book_id"]?>','<?php echo $print["chap_id"]?>','<?php echo $print["copies"]?>','<?php echo $print["dt"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                		<?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $print["pid"]?>');"><i class="bx bx-trash me-1"></i> </a>
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

                      </form>
                    </div>
                  </div>
                </div>
                
              </div>
			<?php } ?>  
			  
 <script type="text/javascript">
             $(document).ready( function () {
        $('#table_id_1').DataTable();
    } );
	
	function deletedata(id) {
      if(confirm("Are you sure to DELETE data?")) {
          var loc = "assign_printing.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
	
	function editdata(id,fid,bid,cid,copies,dt) {
		$('#book').val(bid);
		chapList(bid);
		
		$('#copies').val(copies);
		$('#faculty').val(fid);
		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').removeAttr('hidden');
		$('#btnsubmit').attr('disabled',true);
    
    	setTimeout(function() {
  			//your code to be executed after 1 second
      		$('#chap').val(cid);
      		document.getElementById('exercise_'+eid).checked = true;
		}, 1000);
		
   }
</script>


<?php
include("footer.php");
?>