<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"chap_master")){ }
else{
	header("location:home.php");
}


$stmt_blist = $obj->con1->prepare("select * from books");
$stmt_blist->execute();
$res = $stmt_blist->get_result();
$stmt_blist->close();


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $book_id = $_REQUEST['book'];
  $ch_name = $_REQUEST['chname'];
  $start_pg = $_REQUEST['start_pg'];
  $end_pg = $_REQUEST['end_pg'];
  $chno = $_REQUEST['chno'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `chapter`(`chapter_no`,`chapter_name`,`start_pg`,`end_pg`,`book_id`) VALUES (?,?,?,?,?)");
	$stmt->bind_param("isiii",$chno,$ch_name,$start_pg,$end_pg,$book_id);
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
      header("location:chapter.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:chapter.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $book_id = $_REQUEST['book'];
  $ch_name = $_REQUEST['chname'];
  $start_pg = $_REQUEST['start_pg'];
  $end_pg = $_REQUEST['end_pg'];
  $chno = $_REQUEST['chno'];
  $id=$_REQUEST['ttId'];

  try
  {
    $stmt = $obj->con1->prepare("update chapter set chapter_no=?, chapter_name=?, start_pg=?, end_pg=?, book_id=? where cid=?");
	$stmt->bind_param("isiiii", $chno,$ch_name,$start_pg,$end_pg,$book_id,$id);
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
      header("location:chapter.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:chapter.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from chapter where cid='".$_REQUEST["n_id"]."'");
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
    header("location:chapter.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:chapter.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Chapters Master</h4>

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
                      <h5 class="mb-0">Add Chapter</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Book Name</label>
                          <select name="book" id="book" class="form-control" required>
                          	<option value="">Select</option>
                        
					<?php    
                        while($book=mysqli_fetch_array($res)){
                    ?>
                    		<option value="<?php echo $book["bid"] ?>"><?php echo $book["bookname"] ?></option>
                    <?php
						}
					?>
							
                          </select>
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Chapter Number</label>
                          <input type="number" class="form-control" name="chno" id="chno" required />
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Chapter Name</label>
                          <input type="text" class="form-control" name="chname" id="chname" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Starting Page</label>
                          <input type="number" step="1" class="form-control" name="start_pg" id="start_pg" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Ending Page</label>
                          <input type="number" step="1" class="form-control" name="end_pg" id="end_pg" required />
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
                <h5 class="card-header">Chapter Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Chapter Number</th>
                        <th>Chapter Name</th>
                        <th>Starting page</th>
                        <th>Ending page</th>
                        <th>Book Name</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select c.*,b.bookname from chapter as c,books as b where c.book_id=b.bid order by cid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($chap=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $chap["chapter_no"]?></td>
                        <td><?php echo $chap["chapter_name"]?></td>
                        <td><?php echo $chap["start_pg"]?></td>
                        <td><?php echo $chap["end_pg"]?></td>
                        <td><?php echo $chap["bookname"]?></td>
                        
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a href="javascript:editdata('<?php echo $chap["cid"]?>','<?php echo $chap["chapter_no"]?>','<?php echo base64_encode($chap["chapter_name"])?>','<?php echo $chap["start_pg"]?>','<?php echo $chap["end_pg"]?>','<?php echo $chap["book_id"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $chap["cid"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                        	<a href="javascript:viewdata('<?php echo $chap["cid"]?>','<?php echo $chap["chapter_no"]?>','<?php echo base64_encode($chap["chapter_name"])?>','<?php echo $chap["start_pg"]?>','<?php echo $chap["end_pg"]?>','<?php echo $chap["book_id"]?>');">View</a>
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
          var loc = "chapter.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,chno,chname,startpg,endpg,book) {
      $('#ttId').val(id);
      $('#chno').val(chno);
      $('#chname').val(atob(chname));
	  	$('#start_pg').val(startpg);
			$('#end_pg').val(endpg);
			$('#book').val(book);
			$('#btnsubmit').attr('hidden',true);
      $('#btnupdate').removeAttr('hidden');
			$('#btnsubmit').attr('disabled',true);

        }
  function viewdata(id,chno,chname,startpg,endpg,book) {
           
		   	$('#ttId').val(id);
        $('#chno').val(chno);
            $('#chname').val(atob(chname));
			$('#start_pg').val(startpg);
			$('#end_pg').val(endpg);
			$('#book').val(book);
			$('#btnsubmit').attr('hidden',true);
            $('#btnupdate').attr('hidden',true);
			$('#btnsubmit').attr('disabled',true);

        }
</script>
<?php 
include("footer.php");
?>