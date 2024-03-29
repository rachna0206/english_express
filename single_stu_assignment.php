<?php
include("header.php");
error_reporting(0);
// for permission
if($row=checkPermission($_SESSION["utype"],"student_assign")){ }
else{
	header("location:home.php");
}

$stmt_clist = $obj->con1->prepare("select * from course");
$stmt_clist->execute();
$course_res = $stmt_clist->get_result();
$stmt_clist->close();

$stmt_blist = $obj->con1->prepare("select * from books");
$stmt_blist->execute();
$res = $stmt_blist->get_result();
$stmt_blist->close();

$stmt_flist = $obj->con1->prepare("select * from faculty");
$stmt_flist->execute();
$faculty_res = $stmt_flist->get_result();
$stmt_flist->close();


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $stu_id = $_REQUEST['stu'];
  $batch_id = $_REQUEST['batch'];
  $book_id = $_REQUEST['book'];
  //$chap_id = $_REQUEST['chap'];
  $alloted_dt = $_REQUEST['dt'];
  $expected_dt = $_REQUEST['expec_dt'];
  $faculty_id = $_REQUEST['faculty'];
  $status = "pending";
  $work_type = $_REQUEST['type'];
  $explain_type = $_REQUEST['explain_type'];
  $stu_status='Incomplete';
  $j=0;
  foreach($_REQUEST['chap'] as $chap_id){
    foreach($_REQUEST['e'.$j] as $exer_id){
       $i=0;
       for($i=0;$i<sizeof($_REQUEST['es'.$exer_id]);$i++)
        {
          $exer_skill=$_REQUEST['es'.$exer_id][$i];
        
  try
  {
    
  	$stmt = $obj->con1->prepare("INSERT INTO `stu_assignment`(`stu_id`,`batch_id`,`book_id`,`chap_id`,`exercise_id`,`alloted_dt`,`expected_dt`,`faculty_id`,`status`,`work_type`,`explain_by_teacher`,`skill`,`stu_status`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
  	$stmt->bind_param("iiiiississsss",$stu_id,$batch_id,$book_id,$chap_id,$exer_id,$alloted_dt,$expected_dt,$faculty_id,$status,$work_type,$explain_type,$exer_skill,$stu_status);
  	$Resp=$stmt->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in inserting! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(Exception  $e) {
    setcookie("sql_error",urlencode($e->getMessage()),time()+3600,"/");
  }
  //"Assignment not alloted to already assigned students!!"
       }
    }
    $j++;
  }

    setcookie("msg", "data",time()+3600,"/");
    header("location:single_stu_assignment.php");
}


if(isset($_REQUEST["btn_modal_update"]))
{

  
  
  $exer_id=$_REQUEST['exe_id'];
  $stu_id=$_REQUEST['stu_id'];

  $stmt_exe=$obj->con1->prepare("select * from stu_assignment where exercise_id=? and stu_id=?");
  $stmt_exe->bind_param("ii",$exer_id,$stu_id);
  $stmt_exe->execute();
  $res_exe=$stmt_exe->get_result();
  $stmt_exe->close();
  $i=0;
  while($exer=mysqli_fetch_array($res_exe))
  {
    $faculty_status = $_REQUEST['faculty_status'.$i];
  
    $faculty_id = $_REQUEST['faculty_id'.$i];
    
    $comp_dt=$_REQUEST['completion_date'.$i];
    $work_type = $_REQUEST['work_type'.$i];
    $explain_type = $_REQUEST['explain_status'.$i];       
    try
    {
      
      $stmt = $obj->con1->prepare("update stu_assignment set status=?,faculty_id=?,completion_dt=?,work_type=?,explain_by_teacher=? where said=?");
      $stmt->bind_param("sisssi",$faculty_status,$faculty_id,$comp_dt,$work_type,$explain_type,$exer["said"]);
      $Resp=$stmt->execute();
      if(!$Resp)
      {
        throw new Exception("Problem in updating! ". strtok($obj->con1-> error,  '('));
      }
      $stmt->close();
    } 
    catch(Exception  $e) {
      setcookie("sql_error",urlencode($e->getMessage()),time()+3600,"/");
    }
    $i++;
  }
  //"Assignment not alloted to already assigned students!!"

    setcookie("msg", "update",time()+3600,"/");
    header("location:single_stu_assignment.php");
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
    header("location:single_stu_assignment.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:single_stu_assignment.php");
  }
}

?>

<link href="assets/css/select2.min.css" rel="stylesheet" />
<script src="assets/js/select2.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});


  function getStudent(search_by,stu){
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=getStudent",
          data: "search_by="+search_by+"&stu="+stu,
          cache: false,
          success: function(result){
            //alert(result);
            var res=result.split("@@@@@");
            $('#stu').html('');
            $('#stu').append(res[0]);
            getStuBatch(res[1]);            
            }
        });
  }

  function getStuBatch(stu_id){
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=getStuBatch",
          data: "stu_id="+stu_id,
          cache: false,
          success: function(result){
            //alert(result);
            $('#batch').html('');
            $('#batch').append(result);

       
            }
        });
  }

  function bookList(course_id){

    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=bookList",
          data: "course_id="+course_id,
          cache: false,
          success: function(result){
           // alert(result);
            $('#book').html('');
            $('#book').append(result);
       
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
            $('#chap_list_div').html('');
       
            }
        });

	}
	function exerList(chap_id){
    var chap=$('#chap').val();
		$.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=exerList",
          data: "chap_id="+chap,
          cache: false,
          success: function(result){
            //alert(result);
            var res=result.split("@@@@@");
            $('#exer_list_div').html('');
            $('#exer_list_div').append(res[0]);
             if(res[1]==0)
              {
                $('#btnsubmit').attr('disabled',true);
              }
              else
              {
                $('#btnsubmit').attr('disabled',false);
              }
       
            }
        });
	}
  function getSkill(exer_id,count1,count2){
    if($('#exercise_'+exer_id).is(':checked')){
      $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=getSkill",
          data: "exercise_id="+exer_id,
          cache: false,
          success: function(result){
            
            //alert(result);
            $('#skill_list_div_'+count1+'_'+count2).append(result);
           
          }
        });
    }
    else{
     // alert("not");
      $('#skill_list_div_'+count1+'_'+count2).html('');
    }
  }

  function get_expected_date(day,dt)
  {
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=get_expected_date",
          data: "dt="+dt+"&days="+day,
          cache: false,
          success: function(result){
           
//            $('#expec_dt').html('');
//            $('#expec_dt').append(result);
            $('#expec_dt').val(result);
       
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
                        <input type="hidden" name="ttId" id="ttId">
                        
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Enter Roll No./Name</label>
                          <input type="text" class="form-control" name="search_value" id="search_value"/>
                        </div>

                        <div class="mb-3">
                        <label class="form-label d-block" for="basic-default-fullname">Search By :</label>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="search_by" id="roll_no" value="roll_no" onchange="getStudent(this.value,search_value.value)" required >
                            <label class="form-check-label" for="inlineRadio1">Roll Number</label>
                          </div>
                          <div class="form-check form-check-inline mt-3">
                            <input class="form-check-input" type="radio" name="search_by" id="name" value="name" onchange="getStudent(this.value,search_value.value)" required>
                            <label class="form-check-label" for="inlineRadio1">Name</label>
                          </div>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Student</label>
                          <select name="stu" id="stu" class="form-control" onchange="getStuBatch(this.value)" required>
                            <option value="">Select Student</option>
                          </select>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Batch Name</label>
                          <select name="batch" id="batch" class="form-control" required>
                            <option value="">Select Batch</option>
                          </select>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Date</label>
                          <input type="date" class="form-control" name="dt" id="dt" value="<?php echo date('Y-m-d') ?>" required />
                        </div>                        
                        
                       
                        <div class="mb-3" >
                          <label class="form-label" for="basic-default-fullname">Course Name</label>
                          <select name="course" id="course" onChange="bookList(this.value)" class="form-control" required>
                            <option value="">Select Course</option>
                        <?php 
                          while($course = mysqli_fetch_array($course_res)){
                        ?>
                            <option value="<?php echo $course['courseid'] ?>"><?php echo $course['coursename'] ?></option>
                        <?php
                          }
                        ?>
                          </select>
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Book Name</label>
                          <select name="book" id="book" onChange="chapList(this.value)" class="form-control" required>
                          	<option value="">Select Book</option>
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
                          <select name="chap[]" id="chap" onchange="exerList(this.value)" class="form-control js-example-basic-multiple" required multiple="multiple">
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
                            <div class="col-md-5" >
  		    		      <?php    
                    $i=0;
                      while($exer=mysqli_fetch_array($r3)){	
                    ?>
                    		<input type="checkbox" name="e[]" id="" onclick="getSkill(this.value,<?php echo $i?>)" value="<?php echo $exer["eid"] ?>"/> <?php echo $exer['exer_name'] ?>
                    <?php
                    $i++;
                    	}
					          ?>
                  </div>
                  <div class="col-md-7" >
                    
                  </div>
                    	</div>
                      </div>

                      <div class="mb-3">
                        <div id="skill_list_div">
                        </div>  
                      </div>

                      <div class="mb-3">
                        <label class="form-label d-block" for="basic-default-fullname">Type</label>
                        
                        <div class="form-check form-check-inline mt-3">
                          <input class="form-check-input" type="radio" name="type" id="hw" value="hw" checked required >
                          <label class="form-check-label" for="inlineRadio1">Home Work</label>
                        </div>
                        <div class="form-check form-check-inline mt-3">
                          <input class="form-check-input" type="radio" name="type" id="cw" value="cw" required>
                          <label class="form-check-label" for="inlineRadio1">Class Work</label>
                        </div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label d-block" for="basic-default-fullname">For Teachers</label>
                        
                        <div class="form-check form-check-inline mt-3">
                          <input class="form-check-input" type="radio" name="explain_type" id="not_explain" value="Not Explained" checked required >
                          <label class="form-check-label" for="inlineRadio1">Not Explained</label>
                        </div>
                        <div class="form-check form-check-inline mt-3">
                          <input class="form-check-input" type="radio" name="explain_type" id="half_explain" value="Half Explained" required>
                          <label class="form-check-label" for="inlineRadio1">Half Explained</label>
                        </div>
                        <div class="form-check form-check-inline mt-3">
                          <input class="form-check-input" type="radio" name="explain_type" id="explained" value="Explained" required>
                          <label class="form-check-label" for="inlineRadio1">Explained</label>
                        </div>
                      </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Date</label>
                          <input type="date" class="form-control" name="dt" id="dt" value="<?php echo date('Y-m-d') ?>" required />
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Number of Days</label>
                          <select name="days" id="days" onchange="get_expected_date(this.value,dt.value)" class="form-control" required>
                            <option value="">Select Number of Days</option>
                    <?php    
                      for($i=1;$i<=30;$i++){ 
                        if($i==1){
                    ?>
                        <option value="<?php echo $i ?> days"><?php echo $i ?> day</option>
                    <?php
                        } else{
                    ?>
                        <option value="<?php echo $i ?> days"><?php echo $i ?> days</option>
                    <?php
                        }
                      }
                    ?>
                          </select>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Expected Date</label>
                          <input type="date" class="form-control" name="expec_dt" id="expec_dt" required />
                        </div>

                        <div class="mb-3" id="comp_date_div" style="display:none">
                          <label class="form-label" for="basic-default-fullname">Completion Date</label>
                          <input type="date" class="form-control" name="com_dt" id="com_dt" />
                        </div>
                      
                        <div class="mb-3" id="faculty_remark_div" style="display:none">
                          <label class="form-label" for="basic-default-fullname">Faculty Remark</label>
                          <select name="status" id="status" class="form-control">
                            <option value="">Select Status</option>
                            <option value="Completed with Good Understanding">Completed with Good Understanding</option>
                            <option value="Completed with Average Understanding">Completed with Average Understanding</option>
                            <option value="Completed with No Understanding">Completed with No Understanding</option>
                          </select>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Faculty Name</label>
                          <div id="faculty_list_div">
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
                        </div>
                        
                    <?php if($row["write_func"]=="y"){ ?>
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
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
                        <th>Skill</th>
                        <th>Alloted Date</th>
                        <th>Expected Date</th>
                        <th>Faculty Name</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select sa.*, DATE_FORMAT(sa.alloted_dt, '%d-%m-%Y') as alloted, DATE_FORMAT(sa.expected_dt, '%d-%m-%Y') as expected, ba.name bname, s.name sname, b.bookname, c.chapter_name, e.exer_name, f.name fname,GROUP_CONCAT(sa.skill) as skill from stu_assignment sa, batch ba, books b, chapter c, student s, exercise e, faculty f where sa.batch_id= ba.id and sa.stu_id=s.sid and sa.book_id=b.bid and sa.chap_id=c.cid and sa.exercise_id=e.eid and sa.faculty_id=f.id group by sa.exercise_id,sa.stu_id order by said desc");
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
                        <td><?php echo $a["skill"] ?></td>
                        <td><?php echo $a["alloted"] ?></td>
                        <td><?php echo $a["expected"] ?></td>
                        <td><?php echo $a["fname"] ?></td>
                        
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                    	<td>
                        <a  href="javascript:editdata('<?php echo $a["said"]?>','<?php echo $a["sname"] ?>','<?php echo $a["exercise_id"] ?>','<?php echo $a["skill"]?>');"><i class="bx bx-edit me-1"></i> </a>
                        <a  href="javascript:print_data('<?php echo $a["said"]?>');"><i class="bx bx-printer me-1"></i> </a>
                        
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



<!-- Modal -->
<div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Assignment Update Page</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div id="stu_assign_modal">
      
    </div>
    </div>
  </div>
</div>

<!-- /modal-->
<?php } ?>
            <!-- / Content -->
<script type="text/javascript">
  function deletedata(id) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "stu_assignment.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(said,sname,exercise_id,skill)
  {
    $('#modalCenter').modal('toggle');
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=assignment_modal",
          data: "said="+said,
          cache: false,
          success: function(result){
          //  alert(result);
            $('#stu_assign_modal').html('');
            $('#stu_assign_modal').html(result);
       
            }
        });
    
  }
  $('#check_all_stu').on('click',function(){
        if(this.checked){
          console.log("checked");
            $('.checkbox').each(function(){
                this.checked = true;
            });
        }else{
          console.log("checked");
             $('.checkbox').each(function(){
                this.checked = false;
            });
        }
    });
  function print_data(id) {

    if(confirm("Do you want to print assignment?")) {
       $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=assign_printing",
          data: "said="+id,
          cache: false,
          success: function(result){
          //  alert(result);
            $('#stu_assign_modal').html('');
            $('#stu_assign_modal').html(result);
       
            }
        });
    }
  }
</script>
<?php 
include("footer.php");
?>