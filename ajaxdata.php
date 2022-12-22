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
			
			if(mysqli_num_rows($exer_res)>0)
			{

			$i=0;
		  	while($exercise=mysqli_fetch_array($exer_res))
		  	{
					$html.='<div class="col-md-3"><input type="checkbox" name="e[]" id="exercise_'.$exercise["eid"].'" value="'.$exercise["eid"].'" onclick="getSkill(this.value,'.$i.')"/> '.$exercise["exer_name"].'</div>';
					$i++;

		  	}
		  }
		  else
		  {
		  	$html.='<div class="col-md-3"><input type="text" name="e" id="e" value="" readonly required class="form-control"/><span class="text-danger">No exercise found</span></div>';

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
	if($_REQUEST['action']=="otherFacultyList")
	{
		$html="";
		$batch_id=$_REQUEST["batch_id"];
		
		$stmt_flist = $obj->con1->prepare("select id,name from `faculty` where id not in (SELECT f1.id FROM `batch` b1 , faculty f1 where b1.faculty_id = f1.id and b1.id=? union SELECT f2.id FROM `batch` b1 , faculty f2 where b1.assist_faculty_1 = f2.id and b1.id=? union SELECT f3.id FROM `batch` b1 , faculty f3 where b1.assist_faculty_2 = f3.id and b1.id=?)");
		$stmt_flist->bind_param("iii",$batch_id,$batch_id,$batch_id);
		$stmt_flist->execute();
		$faculty_res = $stmt_flist->get_result();
		$stmt_flist->close();
		$html='<option value="">Select Other Faculty Name</option>';
	  	while($other_faculty=mysqli_fetch_array($faculty_res))
	  	{
			$html.= '<option value="'.$other_faculty["id"].'">'.$other_faculty["name"].'</option>';
	  	}
	  	echo $html;
	}
	if($_REQUEST['action']=="cityList")
	{
		$html="";
		$state_id=$_REQUEST["state_id"];
		$stmt_clist = $obj->con1->prepare("select * from city where state_id=? and status='active'");
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

	if($_REQUEST['action']=="getSkill")
	{
		$html="";
		$exercise_id=$_REQUEST["exercise_id"];
		$cnt=$_REQUEST["count"];
		$stmt_slist = $obj->con1->prepare("select * from exercise where eid=?");
		$stmt_slist->bind_param("i",$exercise_id);
  	$stmt_slist->execute();
  	$skill_res = $stmt_slist->get_result();
  	$stmt_slist->close();
		
		while($skill=mysqli_fetch_array($skill_res)){
		//	$html.=$skill["eid"];
  		if($skill["grammer"]=='y'){
  				$html.='<input type="checkbox" name="es'.$exercise_id.'[]" id="" value="grammer" checked/> Grammer ';
  		} if($skill["vocabulary"]=='y'){
  				$html.='<input type="checkbox" name="es'.$exercise_id.'[]" id="" value="vocabulary" checked/> Vocabulary ';
  		} if($skill["pronunciation"]=='y'){
  				$html.='<input type="checkbox" name="es'.$exercise_id.'[]" id="" value="pronunciation" checked/> Pronunciation ';
  		} if($skill["spelling"]=='y'){
  				$html.='<input type="checkbox" name="es'.$exercise_id.'[]" id="" value="spelling" checked/> Spelling ';
  		} if($skill["presentation"]=='y'){
  				$html.='<input type="checkbox" name="es'.$exercise_id.'[]" id="" value="presentation" checked/> Presentation ';
  		} if($skill["speaking"]=='y'){
  				$html.='<input type="checkbox" name="es'.$exercise_id.'[]" id="" value="speaking" checked/> Speaking ';
  		} if($skill["listening"]=='y'){
  				$html.='<input type="checkbox" name="es'.$exercise_id.'[]" id="" value="listening" checked/> Listening ';
  		} if($skill["writing"]=='y'){
  				$html.='<input type="checkbox" name="es'.$exercise_id.'[]" id="" value="writing" checked/> Writing ';
  		} if($skill["reading"]=='y'){
  				$html.='<input type="checkbox" name="es'.$exercise_id.'[]" id="" value="reading" checked/> Reading ';
  		}
  		
  	}
  	echo $html;
	}
	if($_REQUEST['action']=="get_expected_date")
	{
		$html="";
		$date=$_REQUEST["dt"];
		$days=$_REQUEST["days"];
    $date = strtotime($date);
    $date = strtotime("+".$days,$date);
    $html = date("Y-m-d",$date);
    echo $html;
	}
	// get notification
	if($_REQUEST['action']=="get_notification")
	{
		$html="";
		
		$stmt_clist = $obj->con1->prepare("select * from lead_notification l1,student s1 where l1.stu_id=s1.sid and  l1.status=1");
	 	$stmt_clist->execute();
  	$lead_notification = $stmt_clist->get_result();
  	$count=mysqli_num_rows($lead_notification);
  	$stmt_clist->close();
		$html='';
	  	while($noti=mysqli_fetch_array($lead_notification))
	  	{
				$html.= '<li><div class="d-flex flex-column"><a class="dropdown-item" href="javascript:removeNotification('.$noti["id"].')"><span class="align-middle">You have student follow up today <br/><small class="text-success fw-semibold">'.$noti["name"].' - '.$noti["phone"].'</small></span></a></div>	</li>';
	  	}
	  	echo $html."@@@@".$count."@@@@";
	}
	// remove notification
	if($_REQUEST['action']=="removenotification")
	{
		$html="";
		$id=$_REQUEST["id"];
		$stmt_list = $obj->con1->prepare("update lead_notification set `status`=0,`play_status`=0 where id=?");
		$stmt_list->bind_param("i",$id);
	 	$stmt_list->execute();
  	
  	$stmt_list->close();
	}


	// get play notification
	if($_REQUEST['action']=="get_Playnotification")
	{
		$html="";
		
		$stmt_clist = $obj->con1->prepare("select * from lead_notification l1,student s1 where l1.stu_id=s1.sid and  l1.play_status=1");
	 	$stmt_clist->execute();
  	$lead_notification = $stmt_clist->get_result();
  	$count=mysqli_num_rows($lead_notification);

  	$stmt_clist->close();
		$html='';
	  	while($noti=mysqli_fetch_array($lead_notification))
	  	{
				$ids.=$noti["id"].",";
	  	}
	  	echo $count."@@@@".rtrim($ids,",");
	}
	// remove play sound
	if ($_REQUEST["action"] == "removeplaysound") {

    $ids=explode(',',$_REQUEST["id"]);
  
   
    for($i=0;$i<sizeof($ids);$i++)
    {
      

      $stmt_clist = $obj->con1->prepare("UPDATE `lead_notification` SET `play_status`=0 WHERE id=?");
      $stmt_clist->bind_param("i",$ids[$i]);
		 	$stmt_clist->execute();
	  	$stmt_clist->close();
    }
    
}

	if($_REQUEST['action']=="getTaskFacultyList")
	{
		$html="";
		$task_id=$_REQUEST["task_id"];
		$stmt_tlist = $obj->con1->prepare("select staff_id from task_assign where task_id=?");
		$stmt_tlist->bind_param("i",$task_id);
  	$stmt_tlist->execute();
  	$res = $stmt_tlist->get_result();
  	$stmt_tlist->close();

  	$stmt_list = $obj->con1->prepare("select * from faculty order by id");
		$stmt_list->execute();
		$faculty_list = $stmt_list->get_result();  
		$stmt_list->close();
		
		$i=0;
		$fac = array();
		while($task_fac=mysqli_fetch_array($res))
  	{
  		$fac[$i++] = $task_fac["staff_id"];
  	}
  	
		while($faculty=mysqli_fetch_array($faculty_list)){
			//for($j=0;$j<sizeof($fac);$j++){
			
				if(in_array($faculty[0], $fac,TRUE)){
	  			$html.='<option value="'.$faculty[0].'" selected>'.$faculty[1].'</option>';
	  			//break;
	  		}
				else{
					$html.='<option value="'.$faculty[0].'">'.$faculty[1].'</option>';
					//break;
				}
		//	}
  	}
  	
  	echo $html;
	}

	if($_REQUEST['action']=="check_stu_roll")
	{
		$html="";
		$stu_roll=$_REQUEST["stu_roll"];
		$id=$_REQUEST['id'];
		if($id!="")
		{
			//echo "select * from student where user_id=? and sid!=?";
			
			$stmt_faculty = $obj->con1->prepare("select * from student where user_id=? and sid!=?");
			$stmt_faculty->bind_param("si",$stu_roll,$id);
		}
		else
		{
		
		$stmt_faculty = $obj->con1->prepare("select * from student where user_id=?");
		$stmt_faculty->bind_param("s",$stu_roll);
		
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
	
}


?>
