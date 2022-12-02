<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"motivation")){ }
else{
	header("location:home.php");
}

// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $type = $_REQUEST['type'];
  $status = $_REQUEST['status'];
  if($type=="video"){  
    $vid = $_REQUEST['vid'];
	
  try
  {
	$stmt = $obj->con1->prepare("insert into `motivation` (`type`,`link`,`status`) values(?,?,?)");
    $stmt->bind_param("sss", $type,$vid,$status);
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


  }
  else if($type=="image"){
  	$img_name = $_FILES['img']['name'];
	$path = $_FILES['img']['tmp_name'];
	
	
	//rename file
	  if ($_FILES["img"]["name"] != "")
	  {
	  	if(file_exists("banner/".$img_name)) {
		$i = 0;
		$img = $_FILES["img"]["name"];
		$Arr1 = explode('.', $img);
	
		$img = $Arr1[0] . $i . "." . $Arr1[1];
			while (file_exists("banner/".$img)) {
				$i++;
				$img = $Arr1[0] . $i . "." . $Arr1[1];
			}
		} 
		else {
			$img = $_FILES["img"]["name"];
		}
	  }

	  try
	  {
		$stmt = $obj->con1->prepare("insert into `motivation` (`type`,`link`,`status`) values(?,?,?)");
		$stmt->bind_param("sss", $type,$img,$status);
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


	if($Resp){
		move_uploaded_file($path,"banner/".$img);
	}
  }

  
  if($Resp)
  {
	 setcookie("msg", "data",time()+3600,"/");
     header("location:motivation.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:motivation.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $type = $_REQUEST['type'];
  $status = $_REQUEST['status'];
  $id=$_REQUEST['ttId'];


if($type=="video"){  
    $vid = $_REQUEST['vid'];
  
	  try
	  {
		$stmt = $obj->con1->prepare("update `motivation`set type=?,link=?,status=? where mid=?");
		$stmt->bind_param("sssi", $type,$vid,$status,$id);
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
	

  }
  else if($type=="image" ){
    
    $img_name = $_FILES['img']['name'];
    $path = $_FILES['img']['tmp_name'];

  if($_FILES['img']['size'] != 0 )
  {
  
  
  //rename file
    if ($_FILES["img"]["name"] != "")
    {
      
      if(file_exists("banner/".$img_name)) {
        
      $i = 0;
      $img = $_FILES["img"]["name"];
      $Arr1 = explode('.', $img);
    
      $img = $Arr1[0] . $i . "." . $Arr1[1];
        while (file_exists("banner/".$img)) {
          $i++;
          $img = $Arr1[0] . $i . "." . $Arr1[1];
        }
      } 
      else
      {
        $img = $_FILES["img"]["name"];
      }
      
    }
  
    $stmt = $obj->con1->prepare("update `motivation`set type=?,link=?,status=? where mid=?");
    $stmt->bind_param("sssi", $type,$img,$status,$id);
     move_uploaded_file($path,"banner/".$img);
  }
  else
  {
    $stmt = $obj->con1->prepare("update `motivation`set type=?,status=? where mid=?");
    $stmt->bind_param("ssi", $type,$status,$id);
  }   
	
	  try
	  {
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
  }
  else
  {
	  try
	  {
		$stmt = $obj->con1->prepare("update `motivation`set status=? where mid=?");
		$stmt->bind_param("si",$status,$id);
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
  }

  
  if($Resp)
  {
	 setcookie("msg", "update",time()+3600,"/");
     header("location:motivation.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:motivation.php");
  }
 
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  $pic = $_REQUEST["pic"];
  
  try
  {
	$stmt_del = $obj->con1->prepare("delete from motivation where mid='".$_REQUEST["n_id"]."'");
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
	if(file_exists("banner/".$pic)){
		unlink("banner/".$pic);
	}
	setcookie("msg", "data_del",time()+3600,"/");
    header("location:motivation.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:motivation.php");
  }
}

?>

<script type="text/javascript">
	function selectType(t){
		
    if(t=="video")
    {
      $('#video_div').show();
      $('#img_div').hide();
      $('#vid').attr("required",true); 
      $('#img').removeAttr("required"); 
        
    }
    else
    {
      $('#img_div').show();
      $('#video_div').hide();
      $('#vid').attr("required",true); 
      $('#img').removeAttr("required"); 
    }
    

	}
</script>

<h4 class="fw-bold py-3 mb-4">Motivational Banner Master</h4>

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
                      <h5 class="mb-0">Add Motivational Banner</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        
					
                        
                        <div class="mb-3">
                            
                              <label class="form-label d-block" for="basic-default-fullname">Select Type</label>
                              <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" type="radio" name="type" id="video" value="video" onchange="selectType(this.value)"  >
                                <label class="form-check-label" for="inlineRadio1">Video</label>
                              </div>
                              <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" type="radio" name="type" id="image" value="image" onchange="selectType(this.value)"  checked>
                                <label class="form-check-label" for="inlineRadio1">Image</label>
                              </div>
                       </div>
                       
                  
                       
                       <div class="mb-3" id="img_div" >
                          <label class="form-label" for="basic-default-fullname">Upload Image</label>
                          <input type="file" class="form-control" onchange="readURL(this)" name="img" id="img" required />
                          <img src="" name="PreviewImage" id="PreviewImage" width="100" height="100" style="display:none;">
                          <div id="imgdiv" style="color:red"></div>
                          <input type="hidden" name="himg" id="himg" /> 
                       </div>

                       
                       
                 
                        
                       <div class="mb-3" id="video_div" style="display:none">
                          <label class="form-label" for="basic-default-fullname">Link for Video</label>
                          <input type="text" class="form-control" name="vid" id="vid"/>
                       </div>
                        
                        <div class="mb-3" id="s">
                          <label class="form-label" for="basic-default-fullname">Status</label>
                         <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" type="radio" name="status" id="status_enabled" value="enabled" checked >
                                <label class="form-check-label" for="inlineRadio1">Enabled</label>
                              </div>
                                <div class="form-check form-check-inline mt-3">
                                 <input class="form-check-input" type="radio" name="status" id="status_disabled" value="disabled"  >
                                <label class="form-check-label" for="inlineRadio1">Disabled</label>
                              </div>
                       </div>
                       
                       
                
                            
                    <?php if($row["write_func"]=="y"){ ?>
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
                    <?php } if($row["upd_func"]=="y"){ ?>
						<button type="submit" name="btnupdate" id="btnupdate" class="btn btn-primary " hidden>Update</button>
                    <?php } ?>
                        <button type="reset" name="btncancel" id="btncancel" class="btn btn-secondary" onclick="window.location='motivation.php'">Cancel</button>

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
                <h5 class="card-header">Skills Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Type</th>
                        <th>Link/Image</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from motivation order by mid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($m=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $m["type"]?></td>
                        <td><?php if($m["type"]=="video")
                        { 
                          ?>

								            <a href="<?php echo $m["link"]; ?>" target="_blank"><?php echo $m["link"];  ?></a>
                            <?php
						            }
                         else if($m["type"]=="image"){ 							
							?> <img src="banner/<?php echo $m["link"] ?>" height="100" width="100"/>
                            <?php } ?>
                        </td>
                        <td><?php echo $m["status"]?></td>
                        
                   	<?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a href="javascript:editdata('<?php echo $m["mid"]?>','<?php echo $m["type"]?>','<?php echo base64_encode($m["link"])?>','<?php echo $m["status"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
                			<a  href="javascript:deletedata('<?php echo $m["mid"]?>','<?php echo $m["type"]?>','<?php echo base64_encode($m["link"])?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
					    	<a href="javascript:viewdata('<?php echo $m["mid"]?>','<?php echo $m["type"]?>','<?php echo base64_encode($m["link"])?>','<?php echo $m["status"]?>');">View</a>
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

  function deletedata(id,type,pic) {
      if(confirm("Are you sure to DELETE data?")) {
          var loc = "motivation.php?flg=del&n_id="+id+"&type="+type+"&pic="+atob(pic);
          window.location = loc;
      }
  }
  function editdata(id,type,link,status) {           
		$('#ttId').val(id);
		if(type=="video"){
			$('#video').attr("checked","checked");	
      
      $('#img').removeAttr("required");  
      $('#vid').val(atob(link));  
      $('#video_div').show();
      $('#img_div').hide();
		}
		else
		{
			$('#image').attr("checked","checked");	
      
      $('#img').removeAttr("required");  
      $('#vid').removeAttr("required");  
      $('#PreviewImage').show();
      $('#PreviewImage').attr("src","banner/"+atob(link));  
      //$('#img').val(link);
      $('#video_div').hide();
      $('#img_div').show();
		}
		
		if(status=="enabled")
    {
      $('#status_enabled').attr("checked","checked");  
    }
    else
    {
      $('#status_disabled').attr("checked","checked");  
    }

		
		
		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').removeAttr('hidden');
		$('#btnsubmit').attr('disabled',true);
  }
  
  function viewdata(id,type,link,status) {           
		$('#ttId').val(id);
		if(type=="video"){
			$('#video').attr("checked","checked");	
      
      $('#vid').removeAttr("required");  
      $('#vid').val(atob(link));  
      $('#video_div').show();
      $('#img_div').hide();
		}
		else
		{
			$('#image').attr("checked","checked");	
      
      $('#img').removeAttr("required");  
      $('#PreviewImage').show();
      $('#PreviewImage').attr("src","banner/"+atob(link));  
      //$('#img').val(link);
      $('#video_div').hide();
      $('#img_div').show();
		}
		
		if(status=="enabled")
    {
      $('#status_enabled').attr("checked","checked");  
    }
    else
    {
      $('#status_disabled').attr("checked","checked");  
    }

		
		
		$('#btnsubmit').attr('hidden',true);
		$('#btnupdate').attr('hidden',true);
		$('#btnsubmit').attr('disabled',true);
  }
</script>
<?php 
include("footer.php");
?>