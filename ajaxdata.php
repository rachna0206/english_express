<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);
include("db_connect.php");
$obj=new DB_Connect();
if(isset($_REQUEST['action']))
{
	if($_REQUEST['action']=="get_batch")
	{
		echo "here";
		$html="";
		 $branch=$_REQUEST['branch'];
		
		$stmt_batch = $obj->con1->prepare("select * from batch where branch_id=?");
		$stmt_batch->bind_param("i",$branch);
		$stmt_batch->execute();
		$res = $stmt_batch->get_result();
		$stmt_batch->close();

		$html='<option value="">--Select Batch--</option>';
	              while($batch=mysqli_fetch_array($res))
	              {
	              	$html.='<option value="'.$batch["id"].'">'.$batch["name"].'</option>';
	              }
  	echo $html;
	}

	if($_REQUEST['action']=="get_batch_data")
	{
		$html="";
		$batch=$_REQUEST['batch'];
		$stmt_batch = $obj->con1->prepare("select f1.name as faculty_name, f2.name as assist_faculty1, f3.name assist_faculty2 , b1.* from batch b1,faculty f1, faculty f2 , faculty f3 where b1.faculty_id=f1.id and b1.assist_faculty_1=f2.id and b1.assist_faculty_2=f3.id and b1.id=?");
		$stmt_batch->bind_param("i",$batch);
		$stmt_batch->execute();
		$batch_data = $stmt_batch->get_result()->fetch_assoc();
		$stmt_batch->close();

		//  student list
		
		$stmt_stu = $obj->con1->prepare("select * from student where sid not in (select student_id from batch_assign where batch_id=?)");
		$stmt_stu->bind_param("i",$batch);
		$stmt_stu->execute();
		$res_Stu = $stmt_stu->get_result();
		$stmt_stu->close();

		// assigned stu list
		
		$stmt_stu2 = $obj->con1->prepare("select * from student s1,batch_assign b1 where b1.student_id=s1.sid and b1.batch_id=?");
		$stmt_stu2->bind_param("i",$batch);
		$stmt_stu2->execute();
		$res_Stu2 = $stmt_stu2->get_result();
		$num_stu=mysqli_num_rows($res_Stu2);
		$stmt_stu2->close();

		$html='<div class="row" >
                <div class="mb-3 col-6">
                  <label class="form-label" for="basic-default-fullname">Start Date </label>
                  <input type="date" class="form-control" name="start_date" id="start_date" value="'.$batch_data["stdate"].'" readonly />
                  
                </div>
                <div class="mb-3 col-6">
                  <label class="form-label" for="basic-default-company">End Date</label>
                  <input type="date"  class="form-control " id="end_date" name="end_date"  value="'.$batch_data["endate"].'" readonly />
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-6">
                  <label class="form-label" for="basic-default-fullname">Faculty</label>
                  <input type="text" class="form-control" name="faculty" id="faculty" value="'.$batch_data["faculty_name"].'" readonly />
                  
                </div>
                <div class="mb-3 col-6">
                  <label class="form-label" for="basic-default-fullname">Assistant Faculty1</label>
                  <input type="text" class="form-control" name="assist_faculty1" id="assist_faculty1" value="'.$batch_data["assist_faculty1"].'" readonly />
                  
                </div>
              </div>
              <div class="row">
              <div class="mb-3 col-6">
                  <label class="form-label" for="basic-default-fullname">Assistant Faculty2</label>
                  <input type="text" class="form-control" name="assist_faculty2" id="assist_faculty2" value="'.$batch_data["assist_faculty2"].'" readonly />
                  
                </div>
                <div class="mb-3 col-6">
                  <label class="form-label" for="basic-default-company">Strength</label>
                  <input type="text"  class="form-control " id="strength" name="strength" value="'.$num_stu.'"  />
                </div>
            </div>
            <div class="row" id="shared-lists">
              <div class="mb-3 col-5">
              <label>Student List</label>
              
              <select name="stu_list1[]" id="stu_list1"  class="mb-3 col-md-12" multiple  >
                      <option value=""></option>';
                         
                            while($stu=mysqli_fetch_array($res_Stu)){
                        
                                $html.='<option value="'.$stu["sid"].'" class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">'.$stu["name"].'</option>';
                        
                            }
                        

              $html.='</select>
              </div>
              <div class="col-2 ">
                <div class="card-body">
                  <button class="btn btn-md  btn-outline-primary" type="button" onclick="add_Student()"><i class="bx bx-right-arrow-alt"></i></button>
                </div>
                <div class="card-body">
                  <button class="btn btn-md btn-outline-primary" type="button"  onclick="remove_Student()"><i class="bx bx-left-arrow-alt"></i></button>
                </div>
              </div>
            <div class="mb-3 col-5">
              <label>Assigned student List</label>
              <select name="stu_list2[]" id="stu_list2"  class="mb-3 col-md-12" multiple  >
                      <option value=""></option>';
                      
                            while($stu=mysqli_fetch_array($res_Stu2)){
                        
                                $html.='<option value="'.$stu["sid"].'" class="list-group-item drag-item cursor-move d-flex justify-content-between align-items-center">'.$stu["name"].'</option>';
                        
                            }
                       

              $html.='</select>
            </div>';
  	echo $html;
	}
	if($_REQUEST['action']=="check_userid")
	{
		$html="";
		$userid=$_REQUEST["userid"];
		$id=$_REQUEST['id'];
		if($id!="")
		{
			
			$stmt_faculty = $obj->con1->prepare("select * from faculty where uid=? and id!=?");
			$stmt_faculty->bind_param("si",$userid,$id);
		}
		else
		{

			$stmt_faculty = $obj->con1->prepare("select * from faculty where uid=?");
			$stmt_faculty->bind_param("s",$userid);
		}
		
		$stmt_faculty->execute();
		$res = $stmt_faculty->get_result();
		$stmt_faculty->close();
		if(mysqli_num_rows($res)>0)
		{
			$html=1;
		}
		else
		{
			$html=0;
		}

		echo $html;
	}

	if($_REQUEST['action']=="get_faculty")
	{
		$html="";
		$batch=$_REQUEST["batch"];
		$stmt_batch = $obj->con1->prepare("select f1.name as faculty_name, f2.name as assist_faculty1, f3.name assist_faculty2 , b1.* from batch b1,faculty f1, faculty f2 , faculty f3 where b1.faculty_id=f1.id and b1.assist_faculty_1=f2.id and b1.assist_faculty_2=f3.id and b1.id=?");
		$stmt_batch->bind_param("i",$batch);
		$stmt_batch->execute();
		$batch_data = $stmt_batch->get_result()->fetch_assoc();
		$stmt_batch->close();


		$html='<div class="row">

							<div class="mb-3 col-4">
                  <label class="form-label" for="basic-default-fullname">Faculty</label>
                  <input type="text" class="form-control" name="faculty" id="faculty" value="'.$batch_data["faculty_name"].'" readonly />
                  
              </div>
              <div class="mb-3 col-4">
                  <label class="form-label" for="basic-default-fullname">Assistant Faculty 1</label>
                  <input type="text" class="form-control" name="assist_faculty1" id="assist_faculty1" value="'.$batch_data["assist_faculty1"].'" readonly />
                  
                </div>
              <div class="mb-3 col-4">
                  <label class="form-label" for="basic-default-fullname">Assistant Faculty 2</label>
                  <input type="text" class="form-control" name="assist_faculty2" id="assist_faculty2" value="'.$batch_data["assist_faculty2"].'" readonly />
                  
                </div>
            </div>';


		echo $html;
	}

	if($_REQUEST['action']=="get_chapters")
	{
		$html="";
		$book=$_REQUEST["book"];
		$stmt_clist = $obj->con1->prepare("select * from chapter where book_id=?");
		$stmt_clist->bind_param("i",$book);
	  $stmt_clist->execute();
	  $chap_res = $stmt_clist->get_result();
	  $stmt_clist->close();
	  while($chapters=mysqli_fetch_array($chap_res))
	  {

	  	$html.='<option value="'.$chapters["cid"].'">'.$chapters["chapter_name"].'</option>';

	  }
	  echo $html;
	}
	if($_REQUEST['action']=="studList")
	{
		$html="";
		$batch_id=$_REQUEST["batch_id"];
		$stmt_clist = $obj->con1->prepare("select b.student_id, s.name from batch_assign b, student s where b.student_id=s.sid and b.batch_id=?");
		$stmt_clist->bind_param("i",$batch_id);
	  $stmt_clist->execute();
	  $stu_res = $stmt_clist->get_result();
	  $stmt_clist->close();
	  while($students=mysqli_fetch_array($stu_res))
	  {

	  	$html.='<div class="col-md-3"><input type="checkbox" id="stu_list" name="s[]" id="" value="'.$students["student_id"].'" checked="checked"/> '.$students["name"].'</div>';

	  }
	  echo $html;
	}
	if($_REQUEST['action']=="chapList")
	{
		$html="";
		$book_id=$_REQUEST["book_id"];
		$stmt_clist = $obj->con1->prepare("select * from chapter where book_id=?");
		$stmt_clist->bind_param("i",$book_id);
	  	$stmt_clist->execute();
	  	$chap_res = $stmt_clist->get_result();
	  	$stmt_clist->close();
			$html='<option value="">Select Chapter</option>';
	  	while($chapter=mysqli_fetch_array($chap_res))
	  	{
			$html.= '<option value="'.$chapter["cid"].'">'.$chapter["chapter_name"].'</option>';
	  	}
	  	echo $html;
	}
	if($_REQUEST['action']=="exerList")
	{
		$html="";
		$chap_id=$_REQUEST["chap_id"];
		$stmt_elist = $obj->con1->prepare("select eid,exer_name from exercise where chap_id=?");
		$stmt_elist->bind_param("i",$chap_id);
	  	$stmt_elist->execute();
	  	$exer_res = $stmt_elist->get_result();
	  	$stmt_elist->close();
			$i=0;
			if(mysqli_num_rows($exer_res)>0)
			{

			
		  	while($exercise=mysqli_fetch_array($exer_res))
		  	{
					$html.=' <input type="checkbox" name="e[]" id="exercise_'.$exercise["eid"].'" value="'.$exercise["eid"].'"/> '.$exercise["exer_name"];
					$i++;

		  	}
		  }
		  else
		  {
		  	$html.=' <input type="text" name="e" id="e" value="" readonly required class="form-control"/><span class="text-danger">No exercise found</span>';

		  }

	  	echo $html;
	  
	}
	if($_REQUEST['action']=="facultyList")
	{
		$html="";
		$batch_id=$_REQUEST["batch_id"];
		
		$stmt_flist = $obj->con1->prepare("SELECT f1.id , f1.name  FROM `batch` b1 , faculty f1 where b1.faculty_id = f1.id and b1.id=? union SELECT f2.id , f2.name  FROM `batch` b1 , faculty f2 where b1.assist_faculty_1 = f2.id and b1.id=? union SELECT f3.id , f3.name  FROM `batch` b1 , faculty f3 where b1.assist_faculty_2 = f3.id and b1.id=?");
		$stmt_flist->bind_param("iii",$batch_id,$batch_id,$batch_id);
		$stmt_flist->execute();
		$faculty_res = $stmt_flist->get_result();
		$stmt_flist->close();
		$html='<option value="">Select Faculty</option>';
	  	while($faculty=mysqli_fetch_array($faculty_res))
	  	{
			$html.= '<option value="'.$faculty["id"].'">'.$faculty["name"].'</option>';
	  	}
	  	echo $html;
	}
	if($_REQUEST['action']=="cityList")
	{
		$html="";
		$state_id=$_REQUEST["state_id"];
		$stmt_clist = $obj->con1->prepare("select * from city where state_id=?");
		$stmt_clist->bind_param("i",$state_id);
	  	$stmt_clist->execute();
	  	$city_res = $stmt_clist->get_result();
	  	$stmt_clist->close();
			$html='<option value="">Select City</option>';
	  	while($city=mysqli_fetch_array($city_res))
	  	{
			$html.= '<option value="'.$city["city_id"].'">'.$city["city_name"].'</option>';
	  	}
	  	echo $html;
	}
	
}


?>

<!-- <script src="Sortable.js"></script>
<script src="st/app.js"></script> -->