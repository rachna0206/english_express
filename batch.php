<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"batch")){ }
else{
  header("location:home.php");
}

 $stmt_list = $obj->con1->prepare("select * from course order by courseid");
 $stmt_list->execute();
 $course_list = $stmt_list->get_result(); 
 $stmt_list->close();
 
 $stmt_list = $obj->con1->prepare("select * from faculty where designation!='Associate' and status='active' order by id");
 $stmt_list->execute();
 $faculty_list = $stmt_list->get_result();  
 $stmt_list->close();
 
 // assistant faculty1
 
 $stmt_list1 = $obj->con1->prepare("select * from faculty where designation!='Associate' and status='active' order by id");
 $stmt_list1->execute();
  
 $faculty_list1 = $stmt_list1->get_result();  
 
 $stmt_list1->close();

 // assistant faculty2
 $stmt_list2 = $obj->con1->prepare("select * from faculty where designation!='Associate' and status='active' order by id");
 $stmt_list2->execute();
   
 $faculty_list2 = $stmt_list2->get_result();  
 $stmt_list2->close();

 
 $stmt_list = $obj->con1->prepare("select * from branch order by id");
 $stmt_list->execute();
 $branch_list = $stmt_list->get_result(); 
 $stmt_list->close();
 
// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $name=$_REQUEST['name'];
  $course=$_REQUEST['courses'];
  $faculty=$_REQUEST['faculties'];
  $branch=$_REQUEST['branches'];
  $stdate=date('Y-m-d');
  $endate=date('Y-m-d');
  $stime=$_REQUEST['stime'];
  //$entime=$_REQUEST['entime'];
  $status=$_REQUEST['status'];
  $assist_faculty_1=$_REQUEST['assist_faculty1'];
  $assist_faculty_2=$_REQUEST['assist_faculty2'];
  $capacity=$_REQUEST["capacity"];

  $pic = $_FILES['logo']['name'];
  $p_path = $_FILES['logo']['tmp_name'];
  
  $mon=$tues=$wed=$thurs=$fri=$sat=$sun="n";
  if(isset($_REQUEST["mon"])){
    $mon="y";
  }
  if(isset($_REQUEST["tues"])){
    $tues="y";
  }
  if(isset($_REQUEST["wed"])){
    $wed="y";
  }
  if(isset($_REQUEST["thurs"])){
    $thurs="y";
  }
  if(isset($_REQUEST["fri"])){
    $fri="y";
  }
  if(isset($_REQUEST["sat"])){
    $sat="y";
  }
  if(isset($_REQUEST["sun"])){
    $sun="y";
  }

  //rename file for batch logo
  if ($_FILES["logo"]["name"] != "")
  {
    if(file_exists("batchLogo/" . $pic)) {
        $i = 0;
        $PicFileName = $_FILES["logo"]["name"];
        $Arr1 = explode('.', $PicFileName);

        $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("batchLogo/" . $PicFileName)) {
            $i++;
            $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
        }
   } 
   else {
        $PicFileName = $_FILES["logo"]["name"];
    }
  }
  
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `batch`(`name`, `course_id`,`faculty_id`,`branch_id`,`stdate`,`endate`,`stime`,`monday`,`tuesday`,`wednesday`,`thursday`,`friday`,`saturday`,`sunday`,`status`,`assist_faculty_1`,`assist_faculty_2`,`capacity`,`batch_logo`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
      $stmt->bind_param("siiisssssssssssiiss",$name,$course,$faculty,$branch,$stdate,$endate,$stime,$mon,$tues,$wed,$thurs,$fri,$sat,$sun,$status,$assist_faculty_1,$assist_faculty_2,$capacity,$PicFileName);
      $Resp=$stmt->execute();
    
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
    move_uploaded_file($p_path,"batchLogo/".$PicFileName);

    setcookie("msg", "data",time()+3600,"/");
      header("location:batch.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:batch.php");
  }
}

// update data
if(isset($_REQUEST['btnupdate']))
{
  $name=$_REQUEST['name'];
  $course=$_REQUEST['courses'];
  $faculty=$_REQUEST['faculties'];
  $branch=$_REQUEST['branches'];
  $stdate=date('Y-m-d');
  $endate=date('Y-m-d');
  $stime=$_REQUEST['stime'];
  //$entime=$_REQUEST['entime'];
  $status=$_REQUEST['status'];
  $bid=$_REQUEST['ttbatchid'];
  $assist_faculty_1=$_REQUEST['assist_faculty1'];
  $assist_faculty_2=$_REQUEST['assist_faculty2'];
  $capacity=$_REQUEST["capacity"];

  $rpic = "";
  if(isset($_REQUEST['hlogo'])){
    $rpic= $_REQUEST['hlogo'];
  }
  $pic=$_FILES['logo']['name'];
  $p_path=$_FILES['logo']['tmp_name'];
  
  $mon=$tues=$wed=$thurs=$fri=$sat=$sun="n";
  if(isset($_REQUEST["mon"])){
    $mon="y";
  }
  if(isset($_REQUEST["tues"])){
    $tues="y";
  }
  if(isset($_REQUEST["wed"])){
    $wed="y";
  }
  if(isset($_REQUEST["thurs"])){
    $thurs="y";
  }
  if(isset($_REQUEST["fri"])){
    $fri="y";
  }
  if(isset($_REQUEST["sat"])){
    $sat="y";
  }
  if(isset($_REQUEST["sun"])){
    $sun="y";
  }

  if($pic!="")
  {
    if(file_exists("batchLogo/".$rpic)){
      unlink("batchLogo/".$rpic);  
    }

    //rename file for batch logo
    if ($_FILES["logo"]["name"] != "")
    {
      if(file_exists("batchLogo/" . $pic)) {
        $i = 0;
        $PicFileName = $_FILES["logo"]["name"];
        $Arr1 = explode('.', $PicFileName);
  
        $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("batchLogo/" . $PicFileName)) {
          $i++;
          $PicFileName = $Arr1[0] . $i . "." . $Arr1[1];
        }
       } 
       else {
        $PicFileName = $_FILES["logo"]["name"];
      }
    }
    
    move_uploaded_file($p_path,"batchLogo/".$PicFileName);
  }
  else
  {
    $PicFileName=$rpic;
  }

  try
  {
    $stmt = $obj->con1->prepare("update batch set name=?, course_id=?, faculty_id=?, branch_id=?, stdate=? , endate=?, stime=?,  monday=?, tuesday=?, wednesday=?, thursday=?, friday=? , saturday=?, sunday=?, status=?,assist_faculty_1=?,assist_faculty_2=?,capacity=?,batch_logo=? where id=?");
      $stmt->bind_param("siiisssssssssssiissi", $name,$course,$faculty,$branch,$stdate,$endate,$stime,$mon,$tues,$wed,$thurs,$fri,$sat,$sun,$status,$assist_faculty_1,$assist_faculty_2,$capacity,$PicFileName,$bid);
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
     header("location:batch.php");
  }
  else
  {
   setcookie("msg", "fail",time()+3600,"/");
     header("location:batch.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  $pic = $_REQUEST["pic"];

  try
  {
    $stmt_del = $obj->con1->prepare("delete from batch where id='".$_REQUEST["n_bid"]."'");
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
    if(file_exists("batchLogo/".$pic)){
      unlink("batchLogo/".$pic);
    }
  setcookie("msg", "data_del",time()+3600,"/");
    header("location:batch.php");
  }
  else
  {
  setcookie("msg", "fail",time()+3600,"/");
    header("location:batch.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Batches Master</h4>

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
                      <h5 class="mb-0">Add Batches</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" enctype="multipart/form-data" >
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Batch name</label>
                          <input type="text" class="form-control" name="name" id="name" required />
                          <input type="hidden"name="ttbatchid" id="ttbatchid" hidden="hidden">
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Course name</label>    
                          <select name="courses" id="courses" class="form-control" required>
                          <option value ="" >Select a course</option>
                          <!-- Courses start -->
                      <?php while($course=mysqli_fetch_array($course_list)){ ?>
                          <option value="<?php echo $course[0] ?>"><?php echo $course[1] ?></option>
                      <?php } ?>
                          </select>
                        </div>
                          <!-- Courses end-->
                          
                          <!-- Faculty start-->
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Faculty name</label>
                          <select name="faculties" id="faculties" class="form-control" required>
                          <option value="" >Select a Faculty</option>
                      <?php while($faculty=mysqli_fetch_array($faculty_list)){ ?>
                          <option value="<?php echo $faculty[0] ?>"><?php echo $faculty[1] ?></option>
                      <?php } ?>
                          </select>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Assistant Faculty 1</label>
                          <select name="assist_faculty1" id="assist_faculty1" class="form-control" required>
                          <option value="" >Select a Faculty</option>
                      <?php while($faculty=mysqli_fetch_array($faculty_list1)){ ?>
                          <option value="<?php echo $faculty[0] ?>"><?php echo $faculty[1] ?></option>
                      <?php } ?>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Assistant Faculty 2</label>
                          <select name="assist_faculty2" id="assist_faculty2" class="form-control" required>
                          <option value="" >Select a Faculty</option>
                      <?php while($faculty=mysqli_fetch_array($faculty_list2)){ ?>
                          <option value="<?php echo $faculty[0] ?>"><?php echo $faculty[1] ?></option>
                      <?php } ?>
                          </select>
                        </div>
                          <!-- Faculty end-->
                          
                          <!-- Branch start-->
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Branch name</label>    
                          <select name="branches" id="branches" class="form-control" required>
                          <option value="" >Select a Branch</option>
                      <?php while($branch=mysqli_fetch_array($branch_list)){ ?>
                          <option value="<?php echo $branch[0] ?>"><?php echo $branch[1] ?></option>
                      <?php } ?>
                          </select>
            </div>
                          <!-- Branch end-->
     
                        <!-- <div class="mb-3">
                         <label class="form-label" for="basic-default-company">Select a start date</label>
                         <input type="date" class="form-control" name="stdate" id="stdate" required/>
                        </div>
                         
                        <div class="mb-3">
                         <label class="form-label" for="basic-default-company">Select an end date</label>
                         <input type="date" class="form-control" name="endate" id="endate" required/>
                        </div> -->
                         
                        <div class="mb-3">
                         <label class="form-label" for="basic-default-company">Batch Time</label>
                         
                         <select name="stime" id="stime" class="form-control" required>
                            <option value="">Select</option>
                            <option value="8:00 am to 9:30 am">8:00 am to 9:30 am</option>
                            <option value="10:00 am to 11:30 pm">10:00 am to 11:30 am</option>
                            <option value="12:00 pm to 1:30 pm">12:00 pm to 1:30 pm</option>
                            <option value="1:00 pm to 2:30 pm">1:00 pm to 2:30 pm</option>
                            <option value="3:00 pm to 4:30 pm">3:00 pm to 4:30 pm</option>
                            <option value="5:00 pm to 6:30 pm">5:00 pm to 6:30 pm</option>
                            <option value="9:00 am to 12:00 pm Sunday">9:00 am to 12:00 pm Sunday</option>
                            

                          </select>
                        </div>
                         
                        
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Days</label><br>
                          <input type="hidden" name="stu_reg" id="stu_reg">
                           <input type="checkbox" name="mon" id="mon" value="Monday" checked /> Monday
                           <input type="checkbox" name="tues" id="tues" value="Tuesday" checked/> Tuesday
                           <input type="checkbox" name="wed" id="wed" value="Wednesday" checked/> Wednesday
                           <input type="checkbox" name="thurs" id="thurs" value="Thursday" checked/> Thursday
                           <input type="checkbox" name="fri" id="fri" value="Friday" checked/> Friday
                           <input type="checkbox" name="sat" id="sat" value="Saturday" checked/> Saturday
                           <input type="checkbox" name="sun" id="sun" value="Sunday" checked /> Sunday
                        </div>
                        
                        <div class="mb-3" id="s">
                          <label class="form-label" for="basic-default-fullname">Batch Status</label>
                          <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" type="radio" name="status" id="status_ongoing" value="ongoing" checked >
                                <label class="form-check-label" for="inlineRadio1">Ongoing</label>
                            </div>
                            <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" type="radio" name="status" id="status_soon" value="soon"  >
                                <label class="form-check-label" for="inlineRadio1">Soon</label>
                            </div>
                            <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" type="radio" name="status" id="status_completed" value="completed"  >
                                <label class="form-check-label" for="inlineRadio1">Completed</label>
                            </div>
                            <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" type="radio" name="status" id="status_canceled" value="canceled"  >
                                <label class="form-check-label" for="inlineRadio1">Cancelled</label>
                            </div>
                       </div>
                       <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Capacity</label>
                          <input type="number" class="form-control" name="capacity" id="capacity" required />
                          
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Batch Logo</label>
                          <input type="file" class="form-control" onchange="readURL(this)" name="logo" id="logo" required />
                          <img src="" name="PreviewImage" id="PreviewImage" width="100" height="100" style="display:none;">
                          <div id="imgdiv" style="color:red"></div>
                          <input type="hidden" name="hlogo" id="hlogo" />
                        </div>

                       
                         
                    <?php if($row["write_func"]=="y"){ ?>
                        <button type="submit" name="btnsubmit"id="btnsubmit" class="btn btn-primary">Submit</button>
          <?php } if($row["upd_func"]=="y"){ ?>
                        <button type="submit" name="btnupdate"id="btnupdate" class="btn btn-primary " hidden>Update</button>
                    <?php } ?>
                         <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='batch.php'">Cancel</button>

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
                <h5 class="card-header">Batch Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno.</th>
                        <th>Batch name</th>
                        <th>Course name</th>
                        <th>Faculty name</th>
                        <th>Branch name</th>
                       
                        <th>Time</th>
                        <th>Capacity</th>
                        <th>Strength</th>
                        
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        //$stmt_list = $obj->con1->prepare("SELECT b1.*,f1.name as faculty_name,b2.name as branch_name,c1.coursename,count(b3.student_id) as strength FROM batch b1, faculty f1,course c1, branch b2,batch_assign b3 where b1.faculty_id=f1.id and b1.branch_id=b2.id and c1.courseid=b1.course_id and b3.batch_id=b1.id and b1.id!=37 GROUP by b1.id order by id desc");
                        $stmt_list=$obj->con1->prepare("select tbl1.*,count(b3.student_id) as strength  from (SELECT b1.*,f1.name as faculty_name,b2.name as branch_name,c1.coursename FROM batch b1, faculty f1,course c1, branch b2 where b1.faculty_id=f1.id and b1.branch_id=b2.id and c1.courseid=b1.course_id and  b1.id!=37) as tbl1 left join batch_assign b3  on b3.batch_id=tbl1.id and b3.student_status='ongoing' GROUP by tbl1.id order by id desc");
                        $stmt_list->execute();
                        $batch_list = $stmt_list->get_result();
                        
                        $stmt_list->close();
            
                        $i=1;
                        while($batch=mysqli_fetch_array($batch_list))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $batch["name"]?></td>
                        <td><?php echo $batch["coursename"]?></td>
                          <td><?php echo $batch["faculty_name"]?></td>
                          <td><?php echo $batch["branch_name"]?></td>
                         
                          <td><?php echo $batch["stime"]?></td>
                          <td><?php echo ($batch["capacity"]!="")?$batch["capacity"]:"-"?></td>
                          <th><?php echo $batch["strength"]?></th>
                          
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                          <a  href="javascript:editdata('<?php echo $batch["id"]?>','<?php echo base64_encode($batch["name"])?>','<?php echo $batch["course_id"]?>','<?php echo $batch["faculty_id"]?>','<?php echo $batch["branch_id"]?>','<?php echo $batch["stime"]?>','<?php echo $batch["monday"]?>','<?php echo $batch["tuesday"]?>','<?php echo $batch["wednesday"]?>','<?php echo $batch["thursday"]?>','<?php echo $batch["friday"]?>','<?php echo $batch["saturday"]?>','<?php echo $batch["sunday"]?>','<?php echo $batch["status"]?>','<?php echo $batch["assist_faculty_1"]?>','<?php echo $batch["assist_faculty_2"]?>','<?php echo $batch["capacity"]?>','<?php echo $batch["batch_logo"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
              <a  href="javascript:deletedata('<?php echo $batch["id"]?>','<?php echo $batch["batch_logo"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                          <a  href="javascript:viewdata('<?php echo $batch["id"]?>','<?php echo base64_encode($batch["name"])?>','<?php echo $batch["course_id"]?>','<?php echo $batch["faculty_id"]?>','<?php echo $batch["branch_id"]?>','<?php echo $batch["stime"]?>','<?php echo $batch["monday"]?>','<?php echo $batch["tuesday"]?>','<?php echo $batch["wednesday"]?>','<?php echo $batch["thursday"]?>','<?php echo $batch["friday"]?>','<?php echo $batch["saturday"]?>','<?php echo $batch["sunday"]?>','<?php echo $batch["status"]?>','<?php echo $batch["assist_faculty_1"]?>','<?php echo $batch["assist_faculty_2"]?>','<?php echo $batch["capacity"]?>','<?php echo $batch["batch_logo"]?>');">View</a>
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

  function readURL(input) {
      if (input.files && input.files[0]) {
          var filename=input.files.item(0).name;

          var reader = new FileReader();
          var extn=filename.split(".");

           if(extn[1].toLowerCase()=="jpg" || extn[1].toLowerCase()=="jpeg" || extn[1].toLowerCase()=="png" || extn[1].toLowerCase()=="bmp") {
            reader.onload = function (e) {
                $('#PreviewImage').attr('src', e.target.result);
                  document.getElementById("PreviewImage").style.display = "block";
            };

            reader.readAsDataURL(input.files[0]);
            $('#imgdiv').html("");
            document.getElementById('btnsubmit').disabled = false;
      }
        else
        {
            $('#imgdiv').html("Please Select Image Only");
              document.getElementById('btnsubmit').disabled = true;
        }
    }
  }

  function deletedata(bid,blogo) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "batch.php?flg=del&n_bid=" + bid+"&pic="+blogo;
          window.location = loc;
      }
  }
  function editdata(id,name,course,faculty,branch,stime,mo,tu,we,th,fr,sa,su,status,assist_faculty1,assist_faculty2,capacity,blogo) {
        
       $('#name').focus();
       $('#name').val(atob(name));
       $('#courses').val(course);
       $('#faculties').val(faculty);
       $('#branches').val(branch);
      
       
       $('#stime').val(stime);
       
       $('#assist_faculty1').val(assist_faculty1);
       $('#assist_faculty2').val(assist_faculty2);
       $('#capacity').val(capacity);
       if(mo=="y"){
      $('#mon').attr("checked","checked");  
       } else{
      $('#mon').removeAttr("checked");
       }
       if(tu=="y"){
      $('#tues').attr("checked","checked");  
       } else{
      $('#tues').removeAttr("checked");
       }
       if(we=="y"){
      $('#wed').attr("checked","checked");  
       } else{
      $('#wed').removeAttr("checked");
       }
       if(th=="y"){
      $('#thurs').attr("checked","checked");  
       } else{
      $('#thurs').removeAttr("checked");
       }
       if(fr=="y"){
      $('#fri').attr("checked","checked");  
       } else{
      $('#fri').removeAttr("checked");
       }
       if(sa=="y"){
      $('#sat').attr("checked","checked");  
       } else{
      $('#sat').removeAttr("checked");
       }
       if(su=="y"){
      $('#sun').attr("checked","checked");  
       } else{
      $('#sun').removeAttr("checked");
       }       
       
       if(status=="ongoing"){
      $('#status_ongoing').attr("checked","checked");  
       }
       else if(status=="soon"){
      $('#status_soon').attr("checked","checked");  
       }
       else if(status=="completed"){
      $('#status_completed').attr("checked","checked");  
       }
       else if(status=="canceled"){
      $('#status_canceled').attr("checked","checked");  
       }

      $('#hlogo').val(blogo);
      $('#PreviewImage').show();
      $('#PreviewImage').attr('src','batchLogo/'+blogo);
      $('#logo').removeAttr('required');
       
           $('#ttbatchid').val(id);
      
           $('#btnsubmit').attr('hidden',true);
           $('#btnupdate').removeAttr('hidden');
		   $('#btnsubmit').attr('disabled',true);
   }
   
  function viewdata(id,name,course,faculty,branch,stime,mo,tu,we,th,fr,sa,su,status,assist_faculty1,assist_faculty2,capacity,blogo) {

       $('#name').focus();
       $('#name').val(atob(name));
       $('#courses').val(course);
       $('#faculties').val(faculty);
       $('#branches').val(branch);
       $('#assist_faculty1').val(assist_faculty1);
       $('#assist_faculty2').val(assist_faculty2);
       $('#capacity').val(capacity);
       $('#stime').val(stime);
       
       
       if(mo=="y"){
      $('#mon').attr("checked","checked");  
       } else{
      $('#mon').removeAttr("checked");
       }
       if(tu=="y"){
      $('#tues').attr("checked","checked");  
       } else{
      $('#tues').removeAttr("checked");
       }
       if(we=="y"){
      $('#wed').attr("checked","checked");  
       } else{
      $('#wed').removeAttr("checked");
       }
       if(th=="y"){
      $('#thurs').attr("checked","checked");  
       } else{
      $('#thurs').removeAttr("checked");
       }
       if(fr=="y"){
      $('#fri').attr("checked","checked");  
       } else{
      $('#fri').removeAttr("checked");
       }
       if(sa=="y"){
      $('#sat').attr("checked","checked");  
       } else{
      $('#sat').removeAttr("checked");
       }
       if(su=="y"){
      $('#sun').attr("checked","checked");  
       } else{
      $('#sun').removeAttr("checked");
       }       
       
       if(status=="ongoing"){
      $('#status_ongoing').attr("checked","checked");  
       }
       else if(status=="soon"){
      $('#status_soon').attr("checked","checked");  
       }
       else if(status=="completed"){
      $('#status_completed').attr("checked","checked");  
       }
       else if(status=="canceled"){
      $('#status_canceled').attr("checked","checked");  
       }

      $('#hlogo').val(blogo);
      $('#PreviewImage').show();
      $('#PreviewImage').attr('src','batchLogo/'+blogo);

//           $('#ttbatchid').val(id);
      
           $('#btnsubmit').attr('hidden',true);
           $('#btnupdate').attr('hidden',true);
		   $('#btnsubmit').attr('disabled',true);
   }

</script>
<?php 
include("footer.php");
?>