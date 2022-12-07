<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"branch")){ }
else{
	header("location:home.php");
}


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $bname=$_REQUEST['bname'];
  $contact=$_REQUEST['contact'];
  $address=$_REQUEST['address'];
  $location=$_REQUEST['location'];
  $capacity=$_REQUEST['capacity'];

  try
  {
	$stmt = $obj->con1->prepare("INSERT INTO `branch`(`name`, `address`, `phone`,`location`,`capacity`) VALUES (?,?,?,?,?)");
	$stmt->bind_param("ssssi", $bname,$address,$contact,$location,$capacity);
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
      header("location:branch.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:branch.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $bname=$_REQUEST['bname'];
  $contact=$_REQUEST['contact'];
  $address=$_REQUEST['address'];
  $location=$_REQUEST['location'];
  $id=$_REQUEST['ttId'];
  $capacity=$_REQUEST['capacity'];
 
  try
  {
	$stmt = $obj->con1->prepare("update branch set  name=?,address=?,phone=?,location=?,capacity=? where id=?");
	$stmt->bind_param("ssssii", $bname,$address,$contact,$location,$capacity,$id);
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
      header("location:branch.php");
  }
  else
  {
	  setcookie("msg", "fail",time()+3600,"/");
      header("location:branch.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from branch where id='".$_REQUEST["n_id"]."'");
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
    header("location:branch.php");
  }
  else
  {
	setcookie("msg", "fail",time()+3600,"/");
    header("location:branch.php");
  }
}

?>

<h4 class="fw-bold py-3 mb-4">Branch Master</h4>

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
                      <h5 class="mb-0">Add Branch</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Branch Name</label>
                          <input type="text" class="form-control" name="bname" id="bname" required />
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Contact No.</label>
                          <input type="tel" pattern="[0-9]{10}" class="form-control phone-mask" id="contact" name="contact"  required/>
                        </div>

                        <div class="form-group ">
                                        <label for="subject">Map Location</label>
                                    </div>

                                    <div id="type-selector" class="pac-controls">

                                    </div>


                                    <div id="pac-container">
                                        <input id="pac-input" type="text" ng-model="add"
                                               placeholder="Enter a location" class="form-control">
                                    </div>
                                    <div id="map_alert" style="color: red;font-style: italic"></div>

                                    <div class="form-group ">
                                        <div id="infowindow-content">
                                            <!-- <img src="" width="16" height="16" id="place-icon"> -->
                                            <span id="place-name" class="title"></span><br>
                                            <span id="place-address"></span>
                                        </div>
                                    </div>


                                    <div align="center" id="map" name="dvMap"
                                         style=" height: 300px;">
                                    </div>
                                    
                                    <input type="hidden" name="location" id="location" value="">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-message">Address</label>
                          <textarea id="address" name="address" class="form-control" required></textarea>
                        </div>

                        <div class="mb-3">
                          <label class="form-label" for="basic-default-company">Capacity</label>
                          <input type="number" class="form-control" id="capacity" name="capacity"  required/>
                        </div>
                        
                    <?php if($row["write_func"]=="y"){ ?>
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Submit</button>
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
                <h5 class="card-header">Branch Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">
                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Name</th>
                        <th>Contact No.</th>
                        <th>Address</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select * from branch order by id desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($branch=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $branch["name"]?></td>
                        <td><?php echo $branch["phone"]?></td>
                        <td><?php echo $branch["address"]?></td>
                        <td><?php echo ($branch["capacity"]!="")?$branch["capacity"]:"-"?></td>
                        
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                        	<a  href="javascript:editdata('<?php echo $branch["id"]?>','<?php echo base64_encode($branch["name"])?>','<?php echo $branch["phone"]?>','<?php echo base64_encode($branch["address"])?>','<?php echo $branch["location"]?>','<?php echo $branch["capacity"]?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
							<a  href="javascript:deletedata('<?php echo $branch["id"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                        	<a  href="javascript:viewdata('<?php echo $branch["id"]?>','<?php echo base64_encode($branch["name"])?>','<?php echo $branch["phone"]?>','<?php echo base64_encode($branch["address"])?>','<?php echo $branch["location"]?>','<?php echo $branch["capacity"]?>');">View</a>
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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBGpGP5g1Vpu00Ipsp0HTqQHyGtVWVgSxc&libraries=places&callback=initMap"
            async defer></script>
<script type="text/javascript">
    function initMap() {

            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: 21.1712287, lng: 72.8203939},
                zoom: 13
            });
            var card = document.getElementById('pac-card');
            var input = document.getElementById('pac-input');
            var types = document.getElementById('type-selector');
            var strictBounds = document.getElementById('strict-bounds-selector');

            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);
            google.maps.event.addListener(map, 'click', function (e) {


                infowindow.close();
                marker.setVisible(false);

                marker = new google.maps.Marker({
                    map: map,
                    draggable: false,
                    position: {lat: e.latLng.lat(), lng: e.latLng.lng()}
                });


                // If the place has a geometry, then present it on a map.

                marker.setVisible(true);

                //document.getElementById("lat").value = e.latLng.lat();
                document.getElementById("location").value = e.latLng.lat() + "," + e.latLng.lng();
                //F alert("Latitude: " + e.latLng.lat() + "\r\nLongitude: " + e.latLng.lng());

                document.getElementById("address").value = document.getElementById("pac-input").value;
                console.log("called 1");

            });

            var autocomplete = new google.maps.places.Autocomplete(input);

            // Bind the map's bounds (viewport) property to the autocomplete object,
            // so that the autocomplete requests use the current map bounds for the
            // bounds option in the request.
            autocomplete.bindTo('bounds', map);

            var infowindow = new google.maps.InfoWindow();
            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29)
            });

            autocomplete.addListener('place_changed', function () {

                infowindow.close();
                marker.setVisible(false);
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }

                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);  // Why 17? Because it looks good.
                }
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                const latitude = place.geometry.location.lat();
                const longitude = place.geometry.location.lng();
                document.getElementById("location").value = latitude + "," + longitude;

                var address = '';
                var add_obj = place.address_components;
                //  console.log(place.address_components);
                // console.log(place.address_components[1].types[0]);
                if (place.address_components) {
                    address = [
                        (place.address_components[0] && place.address_components

                            [0].short_name || ''),
                        (place.address_components[1] && place.address_components

                            [1].short_name || ''),
                        (place.address_components[2] && place.address_components

                            [2].short_name || '')
                    ].join(' ');


                }
                for (var i in add_obj) {
                    if (typeof add_obj[i] == 'object') {
                        // object
                        //   console.log(add_obj[i].types[0]+"--"+add_obj[i].long_name);
                        if (add_obj[i].types[0] == "sublocality_level_1") {
                            $('#area').val(add_obj[i].long_name);
                        }
                        if (add_obj[i].types[0] == "locality") {
                            $('#city').val(add_obj[i].long_name);
                        }
                        //
                        if (add_obj[i].types[0] == "postal_code") {
                            $('#pincode').val(add_obj[i].long_name);
                        }
                    }
                }

                document.getElementById("address").value = document.getElementById("pac-input").value;
                console.log("called 2");

                // get distance between 2 palces

               

                
            });


        }

  function deletedata(id) {
	  
      if(confirm("Are you sure to DELETE data?")) {
          var loc = "branch.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,name,phone,address,location,capacity) {
      
   //document.getElementById('bname').value=atob(name);
      $('#bname').val(atob(name));
      $('#ttId').val(id);
      $('#contact').val(phone);
      $('#address').val(atob(address));
      $('#location').val(location);
      $('#btnsubmit').attr('hidden',true);
      $('#btnupdate').removeAttr('hidden');
     $('#btnsubmit').attr('disabled',true);
     $('#capacity').val(capacity);
  }
  
  function viewdata(id,name,phone,address,location,capacity) {
      
	        $('#bname').val(atob(name));
            $('#ttId').val(id);
            $('#contact').val(phone);
            $('#address').val(atob(address));
            $('#location').val(location);
            $('#btnsubmit').attr('hidden',true);
            $('#btnupdate').attr('hidden',true);
			$('#btnsubmit').attr('disabled',true);
      $('#capacity').val(capacity);
  }
</script>
<?php 
include("footer.php");
?>