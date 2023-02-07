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

// update data
if(isset($_REQUEST['btnupdate']))
{
  $book_id=$_REQUEST['book'];
  $chap_id=$_REQUEST['chap'];
  $faculty_id=$_REQUEST['faculty'];
  $copies=$_REQUEST['copies'];
  $dt=date('Y-m-d');
  $id=$_REQUEST['ttId'];
  $actual_printing=$_REQUEST['actual_print'];
  $from_inventory=$_REQUEST['from_inventory'];
  
  if($actual_printing==0 && $from_inventory==0){
    $status='pending';
  }else if(($actual_printing+$from_inventory)<$copies){
    $status='partial';
  } else if(($actual_printing+$from_inventory)==$copies){
    $status='completed';
  }
  
  try
  {  
	$stmt = $obj->con1->prepare("update printing set faculty_id=?, book_id=?, chap_id=?, copies=?, dt=?, status=? where pid=?");
	$stmt->bind_param("iiiissi", $faculty_id,$book_id,$chap_id,$copies,$dt,$status,$id);
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

if(isset($_REQUEST["btn_modal_update"]))
{
  $pid = $_REQUEST['pid'];
  $chap_id = $_REQUEST['chap_id'];
  $copies=$_REQUEST['copies'];
  $inventory_copies = $_REQUEST['inventory_copies'];
  $copies_to_be_printed = $_REQUEST['copies_to_be_printed'];
  $copies_from_inventory=$_REQUEST["copies_from_inventory"];

  if(($copies_to_be_printed+$copies_from_inventory)<$copies){
    $status='partial';
  } else if(($copies_to_be_printed+$copies_from_inventory)==$copies){
    $status='completed';
  }  
 
  try
  {
    $stmt_print = $obj->con1->prepare("update printing set actual_printing=actual_printing+?, from_inventory=from_inventory+?, status=? where pid=?");
    $stmt_print->bind_param("iisi",$copies_to_be_printed,$copies_from_inventory,$status,$pid);
    $Resp=$stmt_print->execute();
    $stmt_print->close();

    $stmt_inventory = $obj->con1->prepare("update printing_inventory set no_copies=no_copies-? where chap_id=?");
    $stmt_inventory->bind_param("ii",$copies_from_inventory,$chap_id);
    $Resp=$stmt_inventory->execute();
    $stmt_inventory->close();

    if(!$Resp)
    {
      throw new Exception("Problem in updating! ". strtok($obj->con1-> error,  '('));
    }
    
  } 
  catch(Exception  $e) {
    setcookie("sql_error",urlencode($e->getMessage()),time()+3600,"/");
  }
   
  setcookie("msg", "update",time()+3600,"/");
  header("location:assign_printing.php");
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{ 
  $id = $_REQUEST["n_id"]; 
  $chap_id = $_REQUEST['cid'];
  $copies_from_inventory = $_REQUEST['from_inventory'];
  $status = 'pending';
  $value_zero = '0';
  try
  {
    $stmt_print = $obj->con1->prepare("update printing set status=?, actual_printing=?, from_inventory=? where pid=?");
    $stmt_print->bind_param("siii",$status,$value_zero,$value_zero,$id);
    $Resp=$stmt_print->execute();
    $stmt_print->close();

    $stmt_inventory = $obj->con1->prepare("update printing_inventory set no_copies=no_copies+? where chap_id=?");
    $stmt_inventory->bind_param("ii",$copies_from_inventory,$chap_id);
    $Resp=$stmt_inventory->execute();
    $stmt_inventory->close();

    if(!$Resp)
    {
      throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
    }
  } 
  catch(\Exception  $e) {
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
  function checkCopies(p,copies_in_inventory,copies_to_be_printed,copies_to_be_used)
  {
    copies = parseInt(p);
    if(copies_in_inventory!=""){
      in_inventory = parseInt(copies_in_inventory);  
    } else {
      in_inventory = 0;  
    }
    
    to_be_printed = parseInt(copies_to_be_printed);
    if(copies_to_be_used!=""){
      to_be_used = parseInt(copies_to_be_used);
    } else{
      to_be_used = 0;
    }

    if(to_be_printed<0){
      $('#alert_div1').html('Enter Valid Number');
      $('#btn_modal_update').attr('disabled',true);
    }
    else if(to_be_used<0){
      $('#alert_div1').html('Enter Valid Number');
      $('#btn_modal_update').attr('disabled',true); 
    }
    else{
      $('#alert_div1').html('');
      $('#btn_modal_update').attr('disabled',false);
    }

    if((to_be_printed+to_be_used)>copies)
    {
      $('#alert_div').html('Enter Correct Quantity');
      $('#btn_modal_update').attr('disabled',true);
    }
    else
    {
      if(to_be_used>in_inventory)
      {
        $('#alert_div').html('Enter Correct Quantity');
        $('#btn_modal_update').attr('disabled',true);
      } else{
        $('#alert_div').html('');
        $('#btn_modal_update').attr('disabled',false);
      }
    } 
  }

  function checkCopiesOnUpdate(copies,actual_print,from_inventory){
    cop = parseInt(copies);
    print = parseInt(actual_print);
    inventory = parseInt(from_inventory);
    if((print+inventory)>cop){
      $('#copies_alert_div').html('Invalid Number');
      $('#btnupdate').attr('disabled',true);
      $('#btnupdate').attr('disabled',true);
    } else{
      $('#copies_alert_div').html('');
      $('#btnupdate').attr('disabled',false);
      $('#btnupdate').attr('disabled',false);
    }
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
  if($_COOKIE['msg']=="invalid_number_copies")
  {
  ?>

  <div class="alert alert-danger alert-dismissible" role="alert">
    Please Enter Correct Value Of Copies. 
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
                          <input type="hidden" name="actual_print" id="actual_print">
                          <input type="hidden" name="from_inventory" id="from_inventory">
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
                          <input type="number" class="form-control" name="copies" id="copies" onkeyup="checkCopiesOnUpdate(this.value,actual_print.value,from_inventory.value)" step="1" min="1" required />
                          <div id="copies_alert_div" class="text-danger"></div>
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
            						<th>Start Page</th>
                        <th>End Page</th>
                        <th>No. Of Copies</th>
                        <th>Actual Printing</th>
                        <th>From Inventory</th>
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
            						<td><?php echo $print["start_pg"]?></td>
                        <td><?php echo $print["end_pg"]?></td>
            						<td><?php echo $print["copies"]?></td>
                        <td><?php echo $print["actual_printing"]?></td>
                        <td><?php echo $print["from_inventory"]?></td>
                        <td><?php echo $print["a_dt"]?></td>
            						<td><?php if($row["upd_func"]=="y"){ 
            					if($print["status"]=="pending"){ ?>
							          <a href="javascript:changeStatus('<?php echo $print["pid"]?>');"><?php echo $print["status"] ?></a>
							<?php } else if($print["status"]=="partial"){  ?>
                        <a href="javascript:changeStatus('<?php echo $print["pid"]?>');"><?php echo $print["status"] ?></a>
              <?php } else{ echo $print["status"]; }
            								} ?>
            						</td>
                        
                   	<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ 
                                if($print["status"]=="pending" || $print["status"]=="partial"){ ?>
                        	<a href="javascript:editdata('<?php echo $print["pid"]?>','<?php echo $print["faculty_id"]?>','<?php echo $print["book_id"]?>','<?php echo $print["chap_id"]?>','<?php echo $print["copies"]?>','<?php echo $print["actual_printing"]?>','<?php echo $print["from_inventory"]?>','<?php echo $print["dt"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                		<?php } } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $print["pid"]?>','<?php echo $print["chap_id"]?>','<?php echo $print["from_inventory"]?>');"><i class="bx bx-trash me-1"></i> </a>
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

<!-- Modal -->
<div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Printing Update Page</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div id="printing_modal">
      
    </div>
    </div>
  </div>
</div>

<!-- /modal-->

			<?php } ?>  
			  
 <script type="text/javascript">
             $(document).ready( function () {
        $('#table_id_1').DataTable();
    } );
	
  function changeStatus(id){
    $('#modalCenter').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=printing_modal",
      data: "id="+id,
      cache: false,
      success: function(result){
      //  alert(result);
        $('#printing_modal').html('');
        $('#printing_modal').html(result);
   
        }
    });
  }

	function deletedata(id,cid,from_inventory) {
      if(confirm("Are you sure to DELETE data?")) {
          var loc = "assign_printing.php?flg=del&n_id="+id+"&cid="+cid+"&from_inventory="+from_inventory;
          window.location = loc;
      }
  }
	
	function editdata(id,fid,bid,cid,copies,actual_print,from_inventory,dt) {
		
    $('#ttId').val(id);
    $('#book').val(bid);
		chapList(bid);
		$('#copies').val(copies);
    $('#actual_print').val(actual_print);
    $('#from_inventory').val(from_inventory);
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