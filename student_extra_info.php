<?php 
include("header.php");
//error_reporting(0);


// for permission
if($row=checkPermission($_SESSION["utype"],"student_reg")){ }
else{
  header("location:home.php");
}

$stu_id = $_COOKIE['sid'];

$stmt_stuname_list = $obj->con1->prepare("select name from student where sid=?");
$stmt_stuname_list->bind_param("i",$stu_id);
$stmt_stuname_list->execute();
$stuname_result = $stmt_stuname_list->get_result();
$stmt_stuname_list->close();
$stuname = mysqli_fetch_array($stuname_result);

// More Info
if(isset($_REQUEST['btnsubmit_moreinfo']))
{
  $nationality=$_REQUEST['nation'];
  $religion=$_REQUEST['religion'];
  $caste=$_REQUEST['caste'];
  $sub_caste=$_REQUEST['s-caste'];
  $category=$_REQUEST['category'];
  $language=$_REQUEST['language'];
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `more_info` (`nationality`, `religion`, `caste`, `sub_caste`, `category`, `language`, `st_id`) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssi",$nationality,$religion,$caste,$sub_caste,$category,$language,$stu_id);
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
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}
if(isset($_REQUEST['btnupdate_moreinfo']))
{
  $nationality=$_REQUEST['nation'];
  $religion=$_REQUEST['religion'];
  $caste=$_REQUEST['caste'];
  $sub_caste=$_REQUEST['s-caste'];
  $category=$_REQUEST['category'];
  $language=$_REQUEST['language'];
  $ttId=$_REQUEST['ttId'];
  try
  {
    $stmt = $obj->con1->prepare("UPDATE `more_info` SET `nationality` = ?,`religion` = ?,`caste` = ?,`sub_caste` = ?,`category` = ?,`language` = ? WHERE `sr_no` =? ");
    $stmt->bind_param("ssssssi",$nationality,$religion,$caste,$sub_caste,$category,$language,$ttId);
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
      header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:student_extra_info.php");
  }
}
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del_moreinfo")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from more_info where sr_no='".$_REQUEST["n_id"]."'");
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
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}


// Address 
if(isset($_REQUEST['btnsubmit_address']))
{
  $hm_no=$_REQUEST['flt_no'];
  $society=$_REQUEST['aptmnt'];
  $street=$_REQUEST['street'];
  $village=$_REQUEST['vil'];
  $post=$_REQUEST['post'];
  $sub_dist=$_REQUEST['s_dist'];
  $dist=$_REQUEST['dist'];
  $city=$_REQUEST['city'];
  $state=$_REQUEST['state'];
  $pin=$_REQUEST['p_code'];
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `multi_address` (`st_id`, `home_no`, `society`, `street`, `village`, `post_office`, `sub_district`, `district`, `city`, `state`, `pincode`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("issssssssss",$stu_id,$hm_no,$society,$street,$village,$post,$sub_dist,$dist,$city,$state,$pin);
    $Resp=$stmt->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in inserting! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) 
  {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }
  if($Resp)
  {
    setcookie("msg", "data",time()+3600,"/");
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}
if(isset($_REQUEST['btnupdate_address']))
{
  $id=$_REQUEST['ttId'];
  $hm_no=$_REQUEST['flt_no'];
  $society=$_REQUEST['aptmnt'];
  $street=$_REQUEST['street'];
  $village=$_REQUEST['vil'];
  $post=$_REQUEST['post'];
  $sub_dist=$_REQUEST['s_dist'];
  $dist=$_REQUEST['dist'];
  $city=$_REQUEST['city'];
  $state=$_REQUEST['state'];
  $pin=$_REQUEST['p_code'];
  try
  {
    $stmt = $obj->con1->prepare("update `multi_address` set home_no=?,society=?,street=?,village=?,post_office=?,sub_district=?,district=?,city=?,state=?,pincode=? where sr_no=?");
    $stmt->bind_param("ssssssssssi",$hm_no,$society,$street,$village,$post,$sub_dist,$dist,$city,$state,$pin,$id);
    $Resp=$stmt->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in updating! ". strtok($obj->con1-> error,  '('));
    }
    $stmt->close();
  } 
  catch(\Exception  $e) 
  {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }
  if($Resp)
  {
    setcookie("msg", "update",time()+3600,"/");
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del_address")
{
  try 
  {  
    $stmt_del = $obj->con1->prepare("delete from multi_address where sr_no='".$_REQUEST["n_id"]."'");
    $Resp=$stmt_del->execute();
    if(!$Resp)
    {
      throw new Exception("Problem in deleting! ". strtok($obj->con1-> error,  '('));
    }
    $stmt_del->close();
  } 
  catch(\Exception  $e) 
  {
    setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
  }
  if($Resp)
  {
    setcookie("msg", "data_del",time()+3600,"/");
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}


// Contact
if(isset($_REQUEST['btnsubmit_contact']))
{
  $stu_no = $_REQUEST['stu_no'];
  $alt_no = $_REQUEST['alt_no'];
  $stu_wha = $_REQUEST['stu_wha'];
  $father_no = $_REQUEST['father_no'];
  $mother_no = $_REQUEST['mother_no'];
  $guardian_no = $_REQUEST['guardian_no'];
  $relation = $_REQUEST['relation'];
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `contact_details` (`stid`, `stu_no`, `alt_no`, `stu_wha`, `father_no`, `mother_no`, `guardian_no`, `relation`) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("iiiiiiis",$stu_id,$stu_no,$alt_no,$stu_wha,$father_no,$mother_no,$guardian_no,$relation);
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
    setcookie("msg", "data",time()+3600,"/");
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}
if(isset($_REQUEST['btnupdate_contact']))
{
  $srno=$_REQUEST['ttId'];
  $stu_no = $_REQUEST['stu_no'];
  $alt_no = $_REQUEST['alt_no'];
  $stu_wha = $_REQUEST['stu_wha'];
  $father_no = $_REQUEST['father_no'];
  $mother_no = $_REQUEST['mother_no'];
  $guardian_no = $_REQUEST['guardian_no'];
  $relation = $_REQUEST['relation'];
  try
  {
    $stmt = $obj->con1->prepare("UPDATE `contact_details` SET `stu_no` = ?,`alt_no` = ?,`stu_wha` = ?,`father_no` = ?,`mother_no` = ?,`guardian_no` = ?,`relation` = ? WHERE `sr_no`=?");
    $stmt->bind_param("iiiiiisi",$stu_no,$alt_no,$stu_wha,$father_no,$mother_no,$guardian_no,$relation,$srno);
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
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:student_extra_info.php");
  }
}
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del_contact")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from `contact_details` where sr_no='".$_REQUEST["n_id"]."'");
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
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}



// Addmission
if(isset($_REQUEST['btnsubmit_addmi']))
{
  $type = $_REQUEST['type'];
  $source = $_REQUEST['source'];
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `admission`(`stid`,`admission_type`,`source`) VALUES (?,?,?)");
    $stmt->bind_param("sss",$stu_id,$type,$source);
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
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:student_extra_info.php");
  }
}
if(isset($_REQUEST['btnupdate_addmi']))
{
  $srno = $_REQUEST['ttId'];
  $type = $_REQUEST['type'];
  $source = $_REQUEST['source'];
  try
  {
    $stmt = $obj->con1->prepare("UPDATE `admission` SET `admission_type` = ?,`source` = ? WHERE `sr_no` =?");
    $stmt->bind_param("sss",$type,$source,$srno);
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
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del_addmi")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from admission where sr_no='".$_REQUEST["n_id"]."'");
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
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}


// SWOT
if(isset($_REQUEST['btnsubmit_swot']))
{
  $Hobbies=$_REQUEST['Hobbies'];
  $Weakness=$_REQUEST['Weakness'];
  $Goal= $_REQUEST['Goal'];
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `swot`(`Hobbies`, `Weakness`,`Goal`,`stid`) VALUES (?,?,?,?)");
    $stmt->bind_param("sssi",$Hobbies,$Weakness,$Goal,$stu_id);
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
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}
if(isset($_REQUEST['btnupdate_swot']))
{
  $Hobbies=$_REQUEST['Hobbies'];
  $Weakness=$_REQUEST['Weakness'];
  $Goal=$_REQUEST['Goal'];
  $srno=$_REQUEST['ttsrno'];
  try
  {
    $stmt = $obj->con1->prepare("update swot set Hobbies=?,Weakness=?,Goal=? where srno=?");
    $stmt->bind_param("sssi",$Hobbies,$Weakness,$Goal,$srno );
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
      header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:student_extra_info.php");
  }
}
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del_swot")
{
  $srno=$_REQUEST['n_srno'];
  try
  { 
    $stmt_del = $obj->con1->prepare("delete from swot where srno=?");
    $stmt_del->bind_param("i",$srno); 
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
    header("location:student_extra_info.php");
  }
  else
  {
  setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}


// Family Education & Occupation
if(isset($_REQUEST['btnsubmit_family']))
{
  $relation=$_REQUEST['rel'];
  $occupation=$_REQUEST['occupation'];
  $playgroup=$_REQUEST['playgroup'];
  $nursery=$_REQUEST['nursery'];
  $junior_kg=$_REQUEST['junior_kg'];
  $senior_kg=$_REQUEST['senior_kg'];
  $std1=$_REQUEST['std1'];
  $std2=$_REQUEST['std2'];
  $till10=$_REQUEST['till10'];
  $std11=$_REQUEST['11_std'];
  $diploma=$_REQUEST['diploma'];
  $graduation=$_REQUEST['graduation'];
  $post_graduation=$_REQUEST['post_grad'];
  try
  {
    $stmt = $obj->con1->prepare("INSERT INTO `occupations`(`student_id`,`relation`,`occupation`,`playgroup`,`nursery`,`juniorkg`,`seniorkg`,`std1`,`std2`,`till10`,`std11`,`diploma`,`graduation`,`post_graduation`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("isssssssssssss", $stu_id,$relation,$occupation,$playgroup,$nursery,$junior_kg,$senior_kg,$std1,$std2,$till10,$std11,$diploma,$graduation,$post_graduation);
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
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  } 
}
if(isset($_REQUEST['btnupdate_family']))
{
  $relation=$_REQUEST['rel'];
  $occupation=$_REQUEST['occupation'];
  $playgroup=$_REQUEST['playgroup'];
  $nursery=$_REQUEST['nursery'];
  $junior_kg=$_REQUEST['junior_kg'];
  $senior_kg=$_REQUEST['senior_kg'];
  $std1=$_REQUEST['std1'];
  $std2=$_REQUEST['std2'];
  $till10=$_REQUEST['till10'];
  $std11=$_REQUEST['11_std'];
  $diploma=$_REQUEST['diploma'];
  $graduation=$_REQUEST['graduation'];
  $post_graduation=$_REQUEST['post_grad'];
  $id=$_REQUEST['ttId'];
  try
  {
    $stmt = $obj->con1->prepare("update occupations set student_id=?, relation=?, occupation=?, playgroup=?, nursery=?, juniorkg=?, seniorkg=?, std1=?, std2=?, till10=?, std11=?, diploma=?, graduation=?, post_graduation=? where oid=?");
    $stmt->bind_param("isssssssssssssi", $stu_id,$relation,$occupation,$playgroup,$nursery,$junior_kg,$senior_kg,$std1,$std2,$till10,$std11,$diploma,$graduation,$post_graduation,$id);
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
    header("location:student_extra_info.php");
  }
  else
  {
      setcookie("msg", "fail",time()+3600,"/");
      header("location:student_extra_info.php");
  }
}
// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del_family")
{  
  try
  {
    $stmt_del = $obj->con1->prepare("delete from occupations where oid='".$_REQUEST["n_id"]."'");
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
    header("location:student_extra_info.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
    header("location:student_extra_info.php");
  }
}
?>

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


<!-- back button-->
<!--    <button onclick="goBack()" class="btn btn-primary"><i class="tf-icons bx bx-left-arrow-alt"></i> Back
    </button> -->

<h4 class="fw-bold py-3 mb-4"><?php echo $stuname['name'] ?>'s Additional Information</h4>

<?php if($row["read_func"]=="y"){ ?>

<!-- Basic Layout -->
<div class="row">
  <div class="nav-align-top mb-4">
                    
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item">
        <button
          type="button"
          class="nav-link active"
          role="tab"
          data-bs-toggle="tab"
          data-bs-target="#navs-top-batch"
          aria-controls="navs-top-batch"
          aria-selected="true">
          Additional Information
        </button>
      </li>
    </ul>
    
    <div class="tab-content">
      <div class="tab-pane fade show active" id="navs-top-batch" role="tabpanel">
        <ul class="p-0 m-0">
          
          <!-- More Info -->
          <li class="d-flex mb-4 pb-1">
            <div class="accordion mt-3" id="accordionExample">
              <div class="card accordion-item active">
                <h2 class="accordion-header" id="headingOne">
                  <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">
                  MORE INFO
                  </button>
                </h2>
                <div id="accordionOne" class="accordion-collapse collapse show"data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    <div class="table-responsive">
                          <?php 
                            $stmt_mi_list = $obj->con1->prepare("select count(*) as count_mi from `more_info` where more_info.st_id=? order by sr_no desc");
                            $stmt_mi_list->bind_param("i",$stu_id);
                            $stmt_mi_list->execute();
                            $mi_result = $stmt_mi_list->get_result();
                            $stmt_mi_list->close();
                            $mi=mysqli_fetch_array($mi_result);
                            if($mi["count_mi"]==0){
                          ?>
                      <a href="javascript:addMoreInfo()" class="text-right"><i class="bx bxs-add-to-queue bx-sm"></i></a>
                          <?php
                            }
                          ?>                          
                      <table class="table table-striped table-borderless border-bottom">
                        <thead>
                          <tr>
                            <th class="text-nowrap">Srno</th>
                            <th class="text-nowrap">Nationality</th>
                            <th class="text-nowrap">Religion</th>
                            <th class="text-nowrap">Caste</th>
                            <th class="text-nowrap">Sub_Caste</th>
                            <th class="text-nowrap">Category</th>
                            <th class="text-nowrap">Language Spoken</th>
                            <th class="text-nowrap">Action</th>
                          </tr>
                        </thead>
                        <tbody id="vertical-example">
                          <?php 
                            $stmt_moreinfo_list = $obj->con1->prepare("select * from `more_info` where more_info.st_id=? order by sr_no desc");
                            $stmt_moreinfo_list->bind_param("i",$stu_id);
                            $stmt_moreinfo_list->execute();
                            $moreinfo_result = $stmt_moreinfo_list->get_result();
                            $stmt_moreinfo_list->close();
                            $i=1;
                            while($m=mysqli_fetch_array($moreinfo_result))
                            {
                          ?>
                          <tr>
                            <td class="text-nowrap"><?php echo $i?></td>
                            <td class="text-nowrap"><?php echo $m["nationality"]?></td>
                            <td class="text-nowrap"><?php echo $m["religion"]?></td>
                            <td class="text-nowrap"><?php echo $m["caste"]?></td>
                            <td class="text-nowrap"><?php echo $m["sub_caste"]?></td>
                            <td class="text-nowrap"><?php echo $m["category"]?></td>
                            <td class="text-nowrap"><?php echo $m["language"]?></td>
                            <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                            <td>
                            <?php if($row["upd_func"]=="y"){ ?>
                              <a href="javascript:editdata_moreinfo('<?php echo $m["nationality"]?>','<?php echo $m["religion"]?>','<?php echo $m["caste"]?>','<?php echo $m["sub_caste"]?>','<?php echo $m["category"]?>','<?php echo $m["language"]?>','<?php echo $m["sr_no"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                            <?php } if($row["del_func"]=="y"){ ?>
                              <a  href="javascript:deletedata_moreinfo('<?php echo $m["sr_no"]?>');"><i class="bx bx-trash me-1"></i> </a>
                            <?php } if($row["read_func"]=="y"){ ?>
                              <a href="javascript:viewdata_moreinfo('<?php echo $m["nationality"]?>','<?php echo $m["religion"]?>','<?php echo $m["caste"]?>','<?php echo $m["sub_caste"]?>','<?php echo $m["category"]?>','<?php echo $m["language"]?>','<?php echo $m["sr_no"]?>');">View</a>
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
                </div>
              </div>
            </div>
          </li>
        

          <!-- Address -->
          <li class="d-flex mb-4 pb-1">
            <div class="accordion mt-3" id="accordionExample">
              <div class="card accordion-item active">
                <h2 class="accordion-header" id="headingOne">
                  <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">
                  ADDRESS
                  </button>
                </h2>
                <div id="accordionOne" class="accordion-collapse collapse show"data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    <div class="table-responsive">
                      <a href="javascript:addAddress()" class="text-right"><i class="bx bxs-add-to-queue bx-sm"></i></a>
                      <table class="table table-striped table-borderless border-bottom">
                        <thead>
                          <tr>
                            <th class="text-nowrap">Srno</th>
                            <th class="text-nowrap">House/Flat Number</th>
                            <th class="text-nowrap">Society/Apartment</th>
                            <th class="text-nowrap">District</th>
                            <th class="text-nowrap">City</th>
                            <th class="text-nowrap">State</th>
                          </tr>
                        </thead>
                        <tbody id="vertical-example">
                          <?php 
                            $stmt_address_list = $obj->con1->prepare("select ma1.*,state_name,city_name from multi_address ma1, state s1, city c1 where ma1.city=c1.city_id and ma1.state=s1.state_id and st_id=? order by sr_no desc");
                            $stmt_address_list->bind_param("i",$stu_id);
                            $stmt_address_list->execute();
                            $address_result = $stmt_address_list->get_result();
                            $stmt_address_list->close();
                            $i=1;
                            while($addr=mysqli_fetch_array($address_result))
                            {
                          ?>
                          <tr>
                            <td class="text-nowrap"><?php echo $i?></td>
                            <td class="text-nowrap"><?php echo $addr["home_no"]?></td>
                            <td class="text-nowrap"><?php echo $addr["society"]?></td>
                            <td class="text-nowrap"><?php echo $addr["district"]?></td>
                            <td class="text-nowrap"><?php echo $addr["city_name"]?></td>
                            <td class="text-nowrap"><?php echo $addr["state_name"]?></td>
                            <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                            <td>
                            <?php if($row["upd_func"]=="y"){ ?>
                              <a href="javascript:editdata_address('<?php echo $addr["sr_no"]?>','<?php echo base64_encode($addr["st_id"])?>','<?php echo $addr["home_no"]?>','<?php echo $addr["society"]?>','<?php echo $addr["street"]?>','<?php echo $addr["village"]?>','<?php echo $addr["post_office"]?>','<?php echo $addr["sub_district"]?>','<?php echo $addr["district"]?>','<?php echo $addr["city"]?>','<?php echo $addr["state"]?>','<?php echo $addr["pincode"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                            <?php } if($row["del_func"]=="y"){ ?>
                              <a  href="javascript:deletedata_address('<?php echo $addr["sr_no"]?>');"><i class="bx bx-trash me-1"></i> </a>
                            <?php } if($row["read_func"]=="y"){ ?>
                              <a href="javascript:viewdata_address('<?php echo $addr["sr_no"]?>','<?php echo base64_encode($addr["st_id"])?>','<?php echo $addr["home_no"]?>','<?php echo $addr["society"]?>','<?php echo $addr["street"]?>','<?php echo $addr["village"]?>','<?php echo $addr["post_office"]?>','<?php echo $addr["sub_district"]?>','<?php echo $addr["district"]?>','<?php echo $addr["city"]?>','<?php echo $addr["state"]?>','<?php echo $addr["pincode"]?>');">View</a>
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
                </div>
              </div>
            </div>
          </li>


          <!-- Contact Details -->
          <li class="d-flex mb-4 pb-1">
            <div class="accordion mt-3" id="accordionExample">
              <div class="card accordion-item active">
                <h2 class="accordion-header" id="headingOne">
                  <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">
                  CONTACT DETAILS
                  </button>
                </h2>
                <div id="accordionOne" class="accordion-collapse collapse show"data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    <div class="table-responsive">
                          <?php 
                            $stmt_cont_list = $obj->con1->prepare("select count(*) as count_cont from contact_details c1 where c1.stid=? order by sr_no desc");
                            $stmt_cont_list->bind_param("i",$stu_id);
                            $stmt_cont_list->execute();
                            $cont_result = $stmt_cont_list->get_result();
                            $stmt_cont_list->close();
                            $cont=mysqli_fetch_array($cont_result);
                            if($cont["count_cont"]==0){
                          ?>
                      <a href="javascript:addContact()" class="text-right"><i class="bx bxs-add-to-queue bx-sm"></i></a>
                          <?php
                            }
                          ?>
                      <table class="table table-striped table-borderless border-bottom">
                        <thead>
                          <tr>
                            <th class="text-nowrap">Srno</th>
                            <th class="text-nowrap">Student Number</th>
                            <th class="text-nowrap">Father Number</th>
                            <th class="text-nowrap">Mother Number</th>
                            <th class="text-nowrap">Guardian Number</th>
                            <th class="text-nowrap">Action</th>
                          </tr>
                        </thead>
                        <tbody id="vertical-example">
                          <?php 
                            $stmt_contact_list = $obj->con1->prepare("select c1.* from contact_details c1 where c1.stid=? order by sr_no desc");
                            $stmt_contact_list->bind_param("i",$stu_id);
                            $stmt_contact_list->execute();
                            $contact_result = $stmt_contact_list->get_result();
                            $stmt_contact_list->close();
                            $i=1;
                            while($contact=mysqli_fetch_array($contact_result))
                            {
                          ?>
                          <tr>
                            <td class="text-nowrap"><?php echo $i?></td>
                            <td class="text-nowrap"><?php echo $contact["stu_no"]?></td>
                            <td class="text-nowrap"><?php echo $contact["father_no"]?></td>
                            <td class="text-nowrap"><?php echo $contact["mother_no"]?></td>
                            <td class="text-nowrap"><?php echo $contact["guardian_no"]?></td>
                            <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                            <td>
                            <?php if($row["upd_func"]=="y"){ ?>
                              <a href="javascript:editdata_contact('<?php echo $contact['sr_no'] ?>','<?php echo $contact['stu_no'] ?>','<?php echo $contact['alt_no'] ?>','<?php echo $contact['stu_wha'] ?>','<?php echo $contact['father_no'] ?>','<?php echo $contact['mother_no'] ?>','<?php echo $contact['guardian_no'] ?>','<?php echo $contact['relation'] ?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                            <?php } if($row["del_func"]=="y"){ ?>
                              <a  href="javascript:deletedata_contact('<?php echo $contact["sr_no"]?>');"><i class="bx bx-trash me-1"></i> </a>
                            <?php } if($row["read_func"]=="y"){ ?>
                              <a href="javascript:viewdata_contact('<?php echo $contact['sr_no'] ?>','<?php echo $contact['stu_no'] ?>','<?php echo $contact['alt_no'] ?>','<?php echo $contact['stu_wha'] ?>','<?php echo $contact['father_no'] ?>','<?php echo $contact['mother_no'] ?>','<?php echo $contact['guardian_no'] ?>','<?php echo $contact['relation'] ?>');">View</a>
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
                </div>
              </div>
            </div>
          </li>


          <!-- Admission Detail -->
          <li class="d-flex mb-4 pb-1">
            <div class="accordion mt-3" id="accordionExample">
              <div class="card accordion-item active">
                <h2 class="accordion-header" id="headingOne">
                  <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">
                  ADMISSION DETAIL
                  </button>
                </h2>
                <div id="accordionOne" class="accordion-collapse collapse show"data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    <div class="table-responsive">
                          <?php 
                            $stmt_ad_list = $obj->con1->prepare("select count(*) as count_ad from  admission a1 where a1.stid=? order by sr_no desc");
                            $stmt_ad_list->bind_param("i",$stu_id);
                            $stmt_ad_list->execute();
                            $ad_result = $stmt_ad_list->get_result();
                            $stmt_ad_list->close();
                            $ad=mysqli_fetch_array($ad_result);
                            if($ad["count_ad"]==0){
                          ?>
                      <a href="javascript:addAdmissionDetail()" class="text-right"><i class="bx bxs-add-to-queue bx-sm"></i></a>
                          <?php
                            }
                          ?>
                      <table class="table table-striped table-borderless border-bottom">
                        <thead>
                            <th class="text-nowrap">Srno</th>
                            <th class="text-nowrap">Admission Type</th>
                            <th class="text-nowrap">Source</th>
                            <th class="text-nowrap">Action</th>
                          </tr>
                        </thead>
                        <tbody id="vertical-example">
                          <?php 
                            $stmt_addmi_list = $obj->con1->prepare("select a1.* from  admission a1 where a1.stid=? order by sr_no desc");
                            $stmt_addmi_list->bind_param("i",$stu_id);
                            $stmt_addmi_list->execute();
                            $addmi_result = $stmt_addmi_list->get_result();
                            $stmt_addmi_list->close();
                            $i=1;
                            while($admission=mysqli_fetch_array($addmi_result))
                            {
                          ?>
                            <td class="text-nowrap"><?php echo $i?></td>
                            <td class="text-nowrap"><?php echo $admission["admission_type"]?></td>
                            <td class="text-nowrap"><?php echo $admission["source"]?></td>
                            <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                            <td>
                            <?php if($row["upd_func"]=="y"){ ?>
                              <a href="javascript:editdata_addmi('<?php echo $admission["sr_no"] ?>','<?php echo $admission["admission_type"] ?>','<?php echo $admission["source"] ?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                            <?php } if($row["del_func"]=="y"){ ?>
                              <a  href="javascript:deletedata_addmi('<?php echo $admission["sr_no"]?>');"><i class="bx bx-trash me-1"></i> </a>
                            <?php } if($row["read_func"]=="y"){ ?>
                              <a href="javascript:viewdata_addmi('<?php echo $admission["sr_no"] ?>','<?php echo $admission["admission_type"] ?>','<?php echo $admission["source"] ?>');">View</a>
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
                </div>
              </div>
            </div>
          </li>


          <!-- SWOT -->
          <li class="d-flex mb-4 pb-1">
            <div class="accordion mt-3" id="accordionExample">
              <div class="card accordion-item active">
                <h2 class="accordion-header" id="headingOne">
                  <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">
                  SWOT
                  </button>
                </h2>
                <div id="accordionOne" class="accordion-collapse collapse show"data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    <div class="table-responsive">
                      <a href="javascript:addSwot()" class="text-right"><i class="bx bxs-add-to-queue bx-sm"></i></a>
                      <table class="table table-striped table-borderless border-bottom">
                        <thead>
                          <tr>
                            <th class="text-nowrap">Srno</th>
                            <th class="text-nowrap">Hobbies</th>
                            <th class="text-nowrap">Weakness</th>
                            <th class="text-nowrap">Goal</th>
                            <th class="text-nowrap">Action</th>
                          </tr>
                        </thead>
                        <tbody id="vertical-example">
                          <?php 
                            $stmt_swot_list = $obj->con1->prepare("select * from swot where stid=? order by srno desc");
                            $stmt_swot_list->bind_param("i",$stu_id);
                            $stmt_swot_list->execute();
                            $swot_result = $stmt_swot_list->get_result();
                            $stmt_swot_list->close();
                            $i=1;
                            while($swot=mysqli_fetch_array($swot_result))
                            {
                          ?>
                          <tr>
                            <td class="text-nowrap"><?php echo $i?></td>
                            <td class="text-nowrap"><?php echo $swot["Hobbies"]?></td>
                            <td class="text-nowrap"><?php echo $swot["Weakness"]?></td>
                            <td class="text-nowrap"><?php echo $swot["Goal"]?></td>
                            <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                            <td>
                            <?php if($row["upd_func"]=="y"){ ?>
                              <a href="javascript:editdata_swot('<?php echo $swot["srno"]?>','<?php echo base64_encode($swot["Hobbies"])?>','<?php echo base64_encode($swot["Weakness"])?>','<?php echo $swot["Goal"] ?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                            <?php } if($row["del_func"]=="y"){ ?>
                              <a  href="javascript:deletedata_swot('<?php echo $swot["srno"]?>');"><i class="bx bx-trash me-1"></i> </a>
                            <?php } if($row["read_func"]=="y"){ ?>
                              <a href="javascript:viewdata_swot('<?php echo $swot["srno"]?>','<?php echo base64_encode($swot["Hobbies"])?>','<?php echo base64_encode($swot["Weakness"])?>','<?php echo $swot["Goal" ] ?>');">View</a>
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
                </div>
              </div>
            </div>
          </li>

          <!-- Family's Occupation & Education -->
          <li class="d-flex mb-4 pb-1">
            <div class="accordion mt-3" id="accordionExample">
              <div class="card accordion-item active">
                <h2 class="accordion-header" id="headingOne">
                  <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne">
                  FAMILY'S OCCUPATION AND EDUCATION
                  </button>
                </h2>
                <div id="accordionOne" class="accordion-collapse collapse show"data-bs-parent="#accordionExample">
                  <div class="accordion-body">
                    <div class="table-responsive">
                      <a href="javascript:addFamily()" class="text-right"><i class="bx bxs-add-to-queue bx-sm"></i></a>
                      <table class="table table-striped table-borderless border-bottom">
                        <thead>
                            <th class="text-nowrap">Srno</th>
                            <th class="text-nowrap">Realtion with Student</th>
                            <th class="text-nowrap">Occupation</th>
                            <th class="text-nowrap">Action</th>
                          </tr>
                        </thead>
                        <tbody id="vertical-example">
                          <?php 
                            $stmt_family_list = $obj->con1->prepare("select o.* from occupations o where o.student_id=? order by oid desc");
                            $stmt_family_list->bind_param("i",$stu_id);
                            $stmt_family_list->execute();
                            $family_result = $stmt_family_list->get_result();
                            $stmt_family_list->close();
                            $i=1;
                            while($o=mysqli_fetch_array($family_result))
                            {
                          ?> 
                            <td class="text-nowrap"><?php echo $i?></td>
                            <td class="text-nowrap"><?php echo $o["relation"]?></td>
                            <td class="text-nowrap"><?php echo $o["occupation"]?></td>
                            <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                            <td>
                            <?php if($row["upd_func"]=="y"){ ?>
                              <a href="javascript:editdata_family('<?php echo $o["oid"]?>','<?php echo $o["student_id"]?>','<?php echo $o["relation"]?>','<?php echo $o["occupation"]?>','<?php echo $o["playgroup"]?>','<?php echo $o["nursery"]?>','<?php echo $o["juniorkg"]?>','<?php echo $o["seniorkg"]?>','<?php echo $o["std1"]?>','<?php echo $o["std2"]?>','<?php echo $o["till10"]?>','<?php echo $o["std11"]?>','<?php echo $o["diploma"]?>','<?php echo $o["graduation"]?>','<?php echo $o["post_graduation"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                            <?php } if($row["del_func"]=="y"){ ?>
                              <a  href="javascript:deletedata_family('<?php echo $o["oid"]?>');"><i class="bx bx-trash me-1"></i> </a>
                            <?php } if($row["read_func"]=="y"){ ?>
                              <a href="javascript:viewdata_family('<?php echo $o["oid"]?>','<?php echo $o["student_id"]?>','<?php echo $o["relation"]?>','<?php echo $o["occupation"]?>','<?php echo $o["playgroup"]?>','<?php echo $o["nursery"]?>','<?php echo $o["juniorkg"]?>','<?php echo $o["seniorkg"]?>','<?php echo $o["std1"]?>','<?php echo $o["std2"]?>','<?php echo $o["till10"]?>','<?php echo $o["std11"]?>','<?php echo $o["diploma"]?>','<?php echo $o["graduation"]?>','<?php echo $o["post_graduation"]?>');">View</a>
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
                </div>
              </div>
            </div>
          </li>

        </ul>
      </div>
    </div>  
      
    </div>
  </div>

<?php } ?>

<!-- Modal for More Info -->
<div class="modal fade" id="modalCenter_moreinfo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Add Additional Information</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div id="more_info_modal">
    </div>
    </div>
  </div>
</div>
<!-- /modal-->

<!-- Modal for Address -->
<div class="modal fade" id="modalCenter_address" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Add Address</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div id="address_modal">
    </div>
    </div>
  </div>
</div>
<!-- /modal-->

<!-- Modal for Contact Details -->
<div class="modal fade" id="modalCenter_contact" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Add Contact Detail</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div id="contact_modal">
    </div>
    </div>
  </div>
</div>
<!-- /modal-->

<!-- Modal for Admission Details -->
<div class="modal fade" id="modalCenter_addmi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Add Admission Detail</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div id="addmission_modal">
    </div>
    </div>
  </div>
</div>
<!-- /modal-->

<!-- Modal for SWOT -->
<div class="modal fade" id="modalCenter_swot" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Add SWOT</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div id="swot_modal">
    </div>
    </div>
  </div>
</div>
<!-- /modal-->

<!-- Modal for Family Education & Occupation -->
<div class="modal fade" id="modalCenter_family" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCenterTitle">Add Family's Education & Occupation</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div id="family_modal">
    </div>
    </div>
  </div>
</div>
<!-- /modal-->




<script type="text/javascript">

//More Info
  function addMoreInfo(){
    $('#modalCenter_moreinfo').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=more_info_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#more_info_modal').html('');
        $('#more_info_modal').html(result);
      }
    }); 
  }
  function deletedata_moreinfo(id) {
    if(confirm("Are you sure to DELETE data?")) {
        var loc = "student_extra_info.php?flg=del_moreinfo&n_id=" + id;
        window.location = loc;
    }
  }
  function editdata_moreinfo(nationality,religion,caste,subcaste,category,language,sr_no) {
    $('#modalCenter_moreinfo').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=more_info_modal",
      cache: false,
      success: function(result){
        //  alert(result);
        $('#more_info_modal').html('');
        $('#more_info_modal').html(result);
        
        $('#nation').val(nationality);
        $('#religion').val(religion);
        $('#caste').val(caste);
        $('#s-caste').val(subcaste);
        $('#category').val(category);
        $('#language').val(language);
        if(language=="english"){
          $('#english').attr("checked","checked");
        }
        else if(language=="hindi"){
          $('#hindi').attr("checked","checked");
        }
        else if(language=="marathi"){
          $('#marathi').attr("checked","checked");
        }
        else if(language=="other"){
          $('#other').attr("checked","checked");
        }
        $('#ttId').val(sr_no);
        $('#btnsubmit_moreinfo').attr('hidden',true);
        $('#btnupdate_moreinfo').removeAttr('hidden');
        $('#btnsubmit_moreinfo').attr('disabled',true);
      }
    }); 
  }
  function viewdata_moreinfo(nationality,religion,caste,subcaste,category,language,sr_no) {      
    $('#modalCenter_moreinfo').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=more_info_modal",
      cache: false,
      success: function(result){
        //  alert(result);
        $('#more_info_modal').html('');
        $('#more_info_modal').html(result);
      
        $('#nation').val(nationality);
        $('#religion').val(religion);
        $('#caste').val(caste);
        $('#s-caste').val(subcaste);
        $('#category').val(category);
        $('#language').val(language);
        if(language=="english"){
          $('#english').attr("checked","checked");
        }
        else if(language=="hindi"){
          $('#hindi').attr("checked","checked");
        }
        else if(language=="marathi"){
          $('#marathi').attr("checked","checked");
        }
        else if(language=="other"){
          $('#other').attr("checked","checked");
        }
        $('#ttId').val(sr_no);
        $('#btnsubmit_moreinfo').attr('hidden',true);
        $('#btnupdate_moreinfo').attr('hidden',true);
        $('#btnsubmit_moreinfo').attr('disabled',true);
        $('#btnupdate_moreinfo').attr('disabled',true);
      }
    });
  }


// Address
  function cityList(state){
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=cityList",
      data: "state_id="+state,
      cache: false,
      success: function(result){
        $('#city').html('');
        $('#city').append(result);
      }
    });
  }
  function addAddress(){
    $('#modalCenter_address').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=address_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#address_modal').html('');
        $('#address_modal').html(result);
      }
    }); 
  }
  function deletedata_address(id) 
  {
    if(confirm("Are you sure to DELETE data?")) 
    {
      var loc = "student_extra_info.php?flg=del_address&n_id=" + id;
      window.location = loc;
    }
  }
  function editdata_address(srno,id,home,society,street,village,post,s_dist,dist,city,state,pin)
  {
    $('#modalCenter_address').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=address_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#address_modal').html('');
        $('#address_modal').html(result);
        
        $('#ttId').val(srno);
        $('#st_id').val(id);
        $('#flt_no').val(home);
        $('#aptmnt').val(society);
        $('#street').val(street);
        $('#vil').val(village);
        $('#post').val(post);
        $('#s_dist').val(s_dist);
        $('#dist').val(dist);
        $('#state').val(state);
        cityList(state);
        setTimeout(function() {
          $('#city').val(city);
        }, 100);
        $('#p_code').val(pin);
        $('#btnsubmit_address').attr('hidden',true);
        $('#btnupdate_address').removeAttr('hidden');
        $('#btnsubmit_address').attr('disabled',true);
      }
    });
  }
  function viewdata_address(srno,id,home,society,street,village,post,s_dist,dist,city,state,pin) 
  {
    $('#modalCenter_address').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=address_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#address_modal').html('');
        $('#address_modal').html(result);
        
        $('#ttId').val(srno);
        $('#st_id').val(id);
        $('#flt_no').val(home);
        $('#aptmnt').val(society);
        $('#street').val(street);
        $('#vil').val(village);
        $('#post').val(post);
        $('#s_dist').val(s_dist);
        $('#dist').val(dist);
        $('#state').val(state);
        cityList(state);
        setTimeout(function() {
          $('#city').val(city);
        }, 100);
        $('#p_code').val(pin);
        $('#btnsubmit_address').attr('hidden',true);
        $('#btnupdate_address').attr('hidden',true);
        $('#btnsubmit_address').attr('disabled',true);
        $('#btnupdate_address').attr('disabled',true);
      }
    });
  }


// Contact Details
  function addContact(){
    $('#modalCenter_contact').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=contact_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#contact_modal').html('');
        $('#contact_modal').html(result);
      }
    }); 
  }
  function deletedata_contact(id) {
    if(confirm("Are you sure to DELETE data?")) {
      var loc = "student_extra_info.php?flg=del_contact&n_id=" + id;
      window.location = loc;
    }
  }
  function editdata_contact(id,stu_no,alt_no,stu_wha,father_no,mother_no,guardian_no,relation) 
  {
    $('#modalCenter_contact').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=contact_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#contact_modal').html('');
        $('#contact_modal').html(result);

        $('#ttId').val(id);
        $('#stu_no').val(stu_no);
        $('#alt_no').val(alt_no);
        $('#stu_wha').val(stu_wha);
        $('#father_no').val(father_no);
        $('#mother_no').val(mother_no);
        $('#guardian_no').val(guardian_no);
        $('#relation').val(relation);
        $('#btnsubmit_contact').attr('hidden',true);
        $('#btnupdate_contact').removeAttr('hidden');
        $('#btnsubmit_contact').attr('disabled',true);
      }
    });    
  }
  function viewdata_contact(id,stu_no,alt_no,stu_wha,father_no,mother_no,guardian_no,relation) 
  {
    $('#modalCenter_contact').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=contact_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#contact_modal').html('');
        $('#contact_modal').html(result);

        $('#ttId').val(id);
        $('#stu_no').val(stu_no);
        $('#alt_no').val(alt_no);
        $('#stu_wha').val(stu_wha);
        $('#father_no').val(father_no);
        $('#mother_no').val(mother_no);
        $('#guardian_no').val(guardian_no);
        $('#relation').val(relation);
        $('#btnsubmit_contact').attr('hidden',true);
        $('#btnupdate_contact').attr('hidden',true);
        $('#btnsubmit_contact').attr('disabled',true);
        $('#btnupdate_contact').attr('disabled',true);
      }
    });
  }


// Addmission Detail
  function addAdmissionDetail(){
    $('#modalCenter_addmi').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=addmission_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#addmission_modal').html('');
        $('#addmission_modal').html(result);
        }
    }); 
  }
  function deletedata_addmi(id) {
    if(confirm("Are you sure to DELETE data?")) {
        var loc = "student_extra_info.php?flg=del_addmi&n_id=" + id;
        window.location = loc;
    }
  }
  function editdata_addmi(srno,admission_type,source) {
    $('#modalCenter_addmi').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=addmission_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#addmission_modal').html('');
        $('#addmission_modal').html(result);

        $('#ttId').val(srno);
        $('#type').val(admission_type);
        $('#source').val(source);
        if(admission_type=="Non Organic"){
         $('#non_organic').attr("checked","checked");  
        } else if(admission_type=="Organic"){
         $('#organic').attr("checked","checked");
        } else if(admission_type=="Reference"){
         $('#refrence').attr("checked","checked"); 
        } else if(admission_type=="Old Student"){
         $('#student').attr("checked","checked"); 
        } else if(admission_type=="Next Course"){
         $('#next_course').attr("checked","checked"); 
        }
        if(source=="Google"){
         $('#google').attr("checked","checked");  
        } else if(source=="Facebook"){
         $('#facebook').attr("checked","checked"); 
        } else if(source=="Youtube"){
         $('#youtube').attr("checked","checked"); 
        } else if(source=="Instagram"){
         $('#instagram').attr("checked","checked"); 
        } else if(source=="Justdial"){
         $('#justdial').attr("checked","checked"); 
        } else if(source=="Road Hoardings"){
         $('#banner').attr("checked","checked"); 
        } else if(source=="Poster"){
         $('#poster').attr("checked","checked"); 
        } else if(source=="Brochure"){
         $('#brochure').attr("checked","checked"); 
        } else if(source=="Newspaper"){
         $('#newspaper').attr("checked","checked"); 
        } else if(source=="Rickshaw"){
         $('#rickshaw').attr("checked","checked"); 
        }
        $('#btnsubmit_addmi').attr('hidden',true);
        $('#btnupdate_addmi').removeAttr('hidden');
        $('#btnsubmit_addmi').attr('disabled',true);
      }
    });
  }
  function viewdata_addmi(srno,admission_type,source) {
    $('#modalCenter_addmi').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=addmission_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#addmission_modal').html('');
        $('#addmission_modal').html(result);

        $('#ttId').val(srno);
        if(admission_type=="Non Organic"){
         $('#non_organic').attr("checked","checked");  
        } else if(admission_type=="Organic"){
         $('#organic').attr("checked","checked"); 
        } else if(admission_type=="Reference"){
         $('#refrence').attr("checked","checked"); 
        } else if(admission_type=="Old Student"){
         $('#student').attr("checked","checked"); 
        } else if(admission_type=="Next Course"){
         $('#next_course').attr("checked","checked"); 
        }
        if(source=="Google"){
         $('#google').attr("checked","checked");  
        } else if(source=="Facebook"){
         $('#facebook').attr("checked","checked"); 
        } else if(source=="Youtube"){
         $('#youtube').attr("checked","checked"); 
        } else if(source=="Instagram"){
         $('#instagram').attr("checked","checked"); 
        } else if(source=="Justdial"){
         $('#justdial').attr("checked","checked"); 
        } else if(source=="Road Hoardings"){
         $('#banner').attr("checked","checked"); 
        } else if(source=="Poster"){
         $('#poster').attr("checked","checked"); 
        } else if(source=="Brochure"){
         $('#brochure').attr("checked","checked"); 
        } else if(source=="Newspaper"){
         $('#newspaper').attr("checked","checked"); 
        } else if(source=="Rickshaw"){
         $('#rickshaw').attr("checked","checked"); 
        }      
        $('#btnsubmit_addmi').attr('hidden',true);
        $('#btnupdate_addmi').attr('hidden',true);
        $('#btnsubmit_addmi').attr('disabled',true);
        $('#btnupdate_addmi').attr('disabled',true);
      }
    });    
  }


// SWOT
  function addSwot(){
    $('#modalCenter_swot').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=swot_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#swot_modal').html('');
        $('#swot_modal').html(result);
        }
    }); 
  }
  function deletedata_swot(srno) {
    if(confirm("Are you sure to DELETE data?")) {
        var loc = "student_extra_info.php?flg=del_swot&n_srno=" + srno;
        window.location = loc;
    }
  }
  function editdata_swot(srno,Hobbies,Weakness,Goal) {
    $('#modalCenter_swot').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=swot_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#swot_modal').html('');
        $('#swot_modal').html(result);
      
        $('#Hobbies').val(atob(Hobbies));
        $('#Weakness').val(atob(Weakness));
        $('#ttsrno').val(srno);
        $('#Goal').val(Goal);
        $('#btnsubmit_swot').attr('hidden',true);
        $('#btnupdate_swot').removeAttr('hidden');
        $('#btnsubmit_swot').attr('disabled',true);
      }
    });       
  }
  function viewdata_swot(srno,Hobbies,Weakness,Goal) {
    $('#modalCenter_swot').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=swot_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#swot_modal').html('');
        $('#swot_modal').html(result);
      
        $('#Hobbies').val(atob(Hobbies));
        $('#Weakness').val(atob(Weakness));
        $('#ttsrno').val(srno);
        $('#Goal').val(Goal);
        $('#btnsubmit_swot').attr('hidden',true);
        $('#btnupdate_swot').attr('hidden',true);
        $('#btnsubmit_swot').attr('disabled',true);
        $('#btnupdate_swot').attr('disabled',true);
      }
    });       
  }


// Family's Eduction & Occupation
  function addFamily(){
    $('#modalCenter_family').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=family_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#family_modal').html('');
        $('#family_modal').html(result);
        }
    }); 
  }
  function deletedata_family(id) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "student_extra_info.php?flg=del_family&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata_family(id,sid,rel,occupation,playgroup,nursery,junior_kg,senior_kg,std1,std2,till10,std11,diploma,graduation,post_grad) {
    $('#modalCenter_family').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=family_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#family_modal').html('');
        $('#family_modal').html(result);

        $('#ttId').val(id);
        $('#student_id').val(sid);
        $('#rel').val(rel);
        $('#playgroup').val(playgroup);
        $('#nursery').val(nursery);
        $('#junior_kg').val(junior_kg);
        $('#senior_kg').val(senior_kg);
        $('#std1').val(std1);
        $('#std2').val(std2);
        $('#till10').val(till10);
        $('#11_std').val(std11);
        $('#diploma').val(diploma);
        $('#graduation').val(graduation);
        $('#post_grad').val(post_grad);
        $('#occupation').val(occupation);
        $('#btnsubmit_family').attr('hidden',true);
        $('#btnupdate_family').removeAttr('hidden');
        $('#btnsubmit_family').attr('disabled',true);
      }
    });    
  }
  function viewdata_family(id,sid,rel,occupation,playgroup,nursery,junior_kg,senior_kg,std1,std2,till10,std11,diploma,graduation,post_grad) {
    $('#modalCenter_family').modal('toggle');
    $.ajax({
      async: true,
      type: "POST",
      url: "ajaxdata.php?action=family_modal",
      cache: false,
      success: function(result){
      //  alert(result);
        $('#family_modal').html('');
        $('#family_modal').html(result);

        $('#ttId').val(id);
        $('#student_id').val(sid);
        $('#rel').val(rel);
        $('#playgroup').val(playgroup);
        $('#nursery').val(nursery);
        $('#junior_kg').val(junior_kg);
        $('#senior_kg').val(senior_kg);
        $('#std1').val(std1);
        $('#std2').val(std2);
        $('#till10').val(till10);
        $('#11_std').val(std11);
        $('#diploma').val(diploma);
        $('#graduation').val(graduation);
        $('#post_grad').val(post_grad);
        $('#occupation').val(occupation);
        $('#btnsubmit_family').attr('hidden',true);
        $('#btnupdate_family').attr('hidden',true);
        $('#btnsubmit_family').attr('disabled',true);
        $('#btnupdate_family').attr('disabled',true);
      }
    });   
  }

  function goBack() {
      // window.history.back();
      //window.location = "stu_report.php";
  }
</script>
<?php 

include ("footer.php");
?>