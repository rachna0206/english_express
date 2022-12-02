<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"book_master")){ }
else{
	header("location:home.php");
}


$stmt_list = $obj->con1->prepare("select * from course order by courseid");
$stmt_list->execute();
$result = $stmt_list->get_result();
$stmt_list->close();

						
// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $bookname=$_REQUEST['bookname'];
  $courseid=$_REQUEST['courses'];
  $avail=$_REQUEST['avail'];
  $copies=$_REQUEST['copies'];
  $remarks=$_REQUEST['remarks'];
  
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `books`(`bookname`, `courseid`,`avail`,`copies`,`remarks`) VALUES (?,?,?,?,?)");
  	$stmt->bind_param("sisis", $bookname,$courseid,$avail,$copies,$remarks);
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
      header("location:Book.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:Book.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
 echo $bookname=$_REQUEST['bookname'];
 echo $courseid=$_REQUEST['courses'];
 echo $avail=$_REQUEST['avail'];
 echo $copies=$_REQUEST['copies'];
 echo $remarks=$_REQUEST['remarks'];
 echo $bid=$_REQUEST['ttbookid'];
 
  try
  {
	$stmt = $obj->con1->prepare("update books set  bookname=?,courseid=?,avail=?,copies=?,remarks=? where bid=?");
	$stmt->bind_param("sisisi", $bookname,$courseid,$avail,$copies,$remarks,$bid);
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
     header("location:Book.php");
  }
  else
  {
	 setcookie("msg", "fail",time()+3600,"/");
     header("location:Book.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    
    $stmt_del = $obj->con1->prepare("delete from books where bid='".$_REQUEST["n_bid"]."'");
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
    header("location:Book.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:Book.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Books Master</h4>

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
                      <h5 class="mb-0">Add Books</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="mb-3">
                   <label class="form-label" for="basic-default-fullname">Book name</label>
                          <input type="text" class="form-control" name="bookname" id="bookname" required />
                          <input type="hidden"name="ttbookid" id="ttbookid" hidden="hidden">
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Course name</label>    
                          <select name="courses" id="courses" class="form-control" required>
                          <option value="" >Select a course</option>
                          <?php
                           while($course=mysqli_fetch_array($result)){
                          ?>
                          	<option value="<?php echo $course[0] ?>"><?php echo $course[1] ?></option>
                          <?php
							}
						  ?>
                          </select>
						</div>
                          
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Availability</label><br>
                           <input type="radio" value="yes" name="avail" id="avail_y" required /> Available
                          <input type="radio" value="no" name="avail" id="avail_n" required /> Not available 
                        </div>
                        <div class="mb-3">
                         <label class="form-label" for="basic-default-company">No. of copies</label>
                         <input type="number" min="1" class="form-control" name="copies" id="copies" required />
                        </div>
                         
                        <div class="mb-3">
                         <label class="form-label" for="basic-default-company">Kindly leave a Remark</label>
                         <textarea class="form-control" name="remarks" id="remarks"  rows="5" cols="20"></textarea>
                        </div>

                    <?php if($row["write_func"]=="y"){ ?>
                        <button type="submit" name="btnsubmit"id="btnsubmit" class="btn btn-primary">Submit</button>
					<?php } if($row["upd_func"]=="y"){ ?>
                        <button type="submit" name="btnupdate"id="btnupdate" class="btn btn-primary " hidden>Update</button>
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
                <h5 class="card-header">Book Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Book id</th>
                        <th>Book name</th>
                        <th>Course id</th>
                        <th>Availability</th>
                        <th>No. of copies</th>
                        
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from books b1,course c1 where b1.courseid=c1.courseid order by b1.bid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($books=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $books["bookname"]?></td>
                        <td><?php echo $books["coursename"]?></td>
                          <td><?php echo $books["avail"]?></td>
                          <td><?php echo $books["copies"]?></td>
                          
                          
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a  href="javascript:editdata('<?php echo $books["bid"]?>','<?php echo base64_encode($books["bookname"])?>','<?php echo $books["courseid"]?>','<?php echo $books["avail"]?>','<?php echo $books["copies"]?>','<?php echo base64_encode($books["remarks"])?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $books["bid"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                        	<a  href="javascript:viewdata('<?php echo $books["bid"]?>','<?php echo base64_encode($books["bookname"])?>','<?php echo $books["courseid"]?>','<?php echo $books["avail"]?>','<?php echo $books["copies"]?>','<?php echo base64_encode($books["remarks"])?>');">View</a>
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
  function deletedata(bid) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "Book.php?flg=del&n_bid=" + bid;
          window.location = loc;
      }
  }
  function editdata(bid,bookname,courseid,avail,copies,remarks) {
           
        document.getElementById('bookname').value=atob(bookname);
		   document.getElementById('remarks').value=atob(remarks);
		   $('#copies').val(copies);
		   $('#courses').val(courseid);
		  
		   
       $('#ttbookid').val(bid);
       if(avail=="no")
       {
          $('#avail_n').attr("checked","checked");  
       }
       else 
       {
          $('#avail_y').attr("checked","checked");
       }
        
       $('#btnsubmit').attr('hidden',true);
       $('#btnupdate').removeAttr('hidden');
	   $('#btnsubmit').attr('disabled',true);
  }
  
  function viewdata(bid,bookname,courseid,avail,copies,remarks) {
           
        document.getElementById('bookname').value=atob(bookname);
		   document.getElementById('remarks').value=atob(remarks);
		   $('#copies').val(copies);
		   $('#courses').val(courseid);
		  
		   
       $('#ttbookid').val(bid);
       if(avail=="no")
       {
          $('#avail_n').attr("checked","checked");  
       }
       else 
       {
          $('#avail_y').attr("checked","checked");
       }
        
       $('#btnsubmit').attr('hidden',true);
       $('#btnupdate').attr('hidden',true);
	   $('#btnsubmit').attr('disabled',true);
  }
</script>
<?php 
include("footer.php");
?>