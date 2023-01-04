<?php
include("header.php");
error_reporting(E_ALL);

// for permission
if($row=checkPermission($_SESSION["utype"],"faculty_report")){ }
else{
	header("location:home.php");
}

$stmt_desig = $obj->con1->prepare("select distinct(user_desig) from permissions");
$stmt_desig->execute();
$res_desig = $stmt_desig->get_result();
$stmt_desig->close();


if(isset($_REQUEST['btnsubmit']))
{
  $name=isset($_REQUEST['name'])?$_REQUEST['name']:"";
  $contact=isset($_REQUEST['contact'])?$_REQUEST['contact']:"";
  $desig=isset($_REQUEST['desig'])?$_REQUEST['desig']:"";
  
  $name_str=($name!="")?"and f1.name like '%".$name."%'":"";
  $contact_str=($contact!="")?"and f1.phone='".$contact."'":"";
  $desig_str=($desig!="")?"and f1.designation='".$desig."'":"";
  if($desig!="")
  {
     $stmt_list = $obj->con1->prepare("select * from faculty f1 where designation!='Associate' ".$name_str.$contact_str.$desig_str);
  }
  else
  {
     $stmt_list = $obj->con1->prepare("select * from faculty f1 where designation!='Associate' ".$name_str.$contact_str);
  }
  
  $stmt_list->execute();
  $result = $stmt_list->get_result();
  
  $stmt_list->close();

}
?>

<h4 class="fw-bold py-3 mb-4">Staff Report</h4>

<?php if($row["read_func"]=="y"){ ?>

<!-- Basic Layout -->
<div class="row">
  <div class="col-xl">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
          
      </div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Name</label>
              <input type="text" class="form-control" name="name" id="name" value="<?php echo isset($_REQUEST['name'])?$_REQUEST['name']:""?>"  />
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Contact</label>
              <input type="text" class="form-control" name="contact" id="contact"  value="<?php echo isset($_REQUEST['contact'])?$_REQUEST['contact']:""?>"/>
              
            </div>
            <div class="mb-3 col-md-3">
              <label class="form-label" for="basic-default-fullname">Designation</label>
              <select name="desig" id="desig" class="form-control"  >
                <option value="">Select</option>
                <?php
                while($designation=mysqli_fetch_array($res_desig))
                {
                  ?>
                  <option value="<?php echo $designation["user_desig"]?>" <?php echo (isset($_REQUEST['desig']) && $_REQUEST['desig']==$designation["user_desig"])?"selected":""?>><?php echo $designation["user_desig"]?></option>
                  <?php
                }
                ?>
              </select>
            </div>
            
          </div>

          <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>
        
          <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='faculty_report.php'">Cancel</button>

        </form>
      </div>
    </div>
  </div>
</div>

<!-- Basic Bootstrap Table -->
              <div class="card">
                <h5 class="card-header">Staff Records</h5>
               
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Contact No.</th>
                        <th>Designation</th>
                        <th>Action</th>
                        
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="grid">
                      <?php 
                     if(isset($_REQUEST['btnsubmit']))
                      {
                      
                      $i=1;
                        while($s=mysqli_fetch_array($result))
                        {
                      ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $s["uid"]?></td>
                        <td><?php echo $s["name"]?></td>
                        <td><?php echo $s["phone"]?></td>
                        <td><?php echo $s["designation"]?></td>
                        <td ><a href="javascript:view_faculty_data('<?php echo $s["id"]?>')">View</a></td>
                          
                      <?php
                          $i++; 
                        } 
                      ?>
                      </tr>
                      <?php
                        }
                      ?>
                      
                    </tbody>
                  </table>
                </div>
              </div>

<?php } ?>
              <!--/ Basic Bootstrap Table -->
<script type="text/javascript">
  function view_faculty_data(fid)
  {
    createCookie("faculty_report_id",fid,1);
    window.open('faculty_report_detail.php', '_blank');
  }
</script>

<?php 
	include("footer.php");
?>