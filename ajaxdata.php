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
		
		$html="";
		 $branch=$_REQUEST['branch'];
		
		$stmt_batch = $obj->con1->prepare("select * from batch where id!=37 and branch_id=?");
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
		$stmt_batch = $obj->con1->prepare("select f1.name as faculty_name, f2.name as assist_faculty1, f3.name assist_faculty2 , b1.* from batch b1,faculty f1, faculty f2 , faculty f3 where b1.faculty_id=f1.id and b1.assist_faculty_1=f2.id and b1.assist_faculty_2=f3.id and b1.id=? ");
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

		// total strength
		$stmt_list10 = $obj->con1->prepare("select s1.* from student s1,batch_assign b1 where b1.student_id=s1.sid and s1.status='registered' and b1.batch_id=? and b1.student_status='ongoing'");
		$stmt_list10->bind_param("i",$batch);
		$stmt_list10->execute();
		$stu_capacity = $stmt_list10->get_result()->num_rows;  
		$stmt_list10->close();

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
                  <input type="text"  class="form-control " id="strength" name="strength" value="'.$stu_capacity.'"  />
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

	if($_REQUEST['action']=="getStudent")
	{
		$html="";
		$html_batch="";
		$search_by=$_REQUEST["search_by"];
		$stu_detail=$_REQUEST["stu"];

		if($search_by=="roll_no")
	  {
	    $stmt_list = $obj->con1->prepare("select sid,name,user_id from student where user_id like '%".$stu_detail."%'");
	  }
	  else if($search_by=="name")
	  {
	    $stmt_list = $obj->con1->prepare("select sid,name,user_id from student where name like '%".$stu_detail."%'");
	  }
	  
	  $stmt_list->execute();
	  $result = $stmt_list->get_result();
	  $stmt_list->close();
	  $f=0;
	  $stu_id=0;
	  if(mysqli_num_rows($result)>0){
		  while($stu=mysqli_fetch_array($result)){
			  $f++;
			  if ($f==1) {
			  	$stu_id=$stu['sid'];
		  	}
		  	$html.= '<option value="'.$stu['sid'].'">'.$stu['user_id']."-".$stu['name'].'</option>';
	    }
   	}
	  else{
	  	$html.='<div class="col-md-3"><span class="text-danger">No Student Found</span></div>';
	  }

	  echo $html."@@@@@".$stu_id;
	}

	if($_REQUEST['action']=="getStuBatch")
	{
		$html="";
		$stu_id=$_REQUEST["stu_id"];

		$stmt_list = $obj->con1->prepare("select b.id,b.name from batch_assign ba, batch b where ba.batch_id=b.id and batch_id!=37 and student_id=".$stu_id);
	  $stmt_list->execute();
	  $result = $stmt_list->get_result();
	  $stmt_list->close();
	  if(mysqli_num_rows($result)>0){
		  while($batch=mysqli_fetch_array($result)){
			  $html.= '<option value="'.$batch['id'].'">'.$batch['name'].'</option>';
	     }
	  }
	  else{
	  	$html.='<div class="col-md-3"><span class="text-danger">No Batch Found</span></div>';
	  }


	  echo $html;
	}

	if($_REQUEST['action']=="studList")
	{
		$html="";
		$html_book="";
		$coursename="";
		$html_course="";
		$batch_id=$_REQUEST["batch_id"];
		$stmt_clist = $obj->con1->prepare("select b.student_id, s.name from batch_assign b, student s where b.student_id=s.sid and b.batch_id=?");
		$stmt_clist->bind_param("i",$batch_id);
	  $stmt_clist->execute();
	  $stu_res = $stmt_clist->get_result();
	  $stmt_clist->close();
	  while($students=mysqli_fetch_array($stu_res))
	  {

	  	$html.='<div class="col-md-3"><input type="checkbox" id="stu_list" name="s[]" id="" value="'.$students["student_id"].'" class="checkbox" /> '.$students["name"].'</div>';

	  }

	  	  // course list
	  $stmt_course = $obj->con1->prepare("select * from course ");

	  $stmt_course->execute();
	  $res_course = $stmt_course->get_result();
	  $stmt_course->close();
	  $html_course='<option value="">Select Course</option>';
	  while($course=mysqli_fetch_array($res_course))
	  {

	  	$html_course.='<option value="'.$course["courseid"].'">'.$course["coursename"].'</option>';

	  
	  }
	  echo $html."@@@@@".$html_course;
	}

	 
	if($_REQUEST['action']=="bookList")
	{
		$html_book="";
		$course_id=$_REQUEST["course_id"];


		// books list
	  $stmt_blist = $obj->con1->prepare("SELECT b2.*,c1.coursename FROM course c1,books b2 WHERE b2.courseid=c1.courseid and c1.courseid=?");
		$stmt_blist->bind_param("i",$course_id);
	  $stmt_blist->execute();
	  $book_res = $stmt_blist->get_result();
	  $stmt_blist->close();
	  $html_book='<option value="">Select Book</option>';
	  while($books=mysqli_fetch_array($book_res))
	  {
	  	

	  	$html_book.='<option value="'.$books["bid"].'">'.$books["bookname"].'</option>';

	  }

	  
	  echo $html_book;
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
	if($_REQUEST['action']=="getChapPageList")
	{
		$html="";
		$chap_id=$_REQUEST["chap_id"];
		$stmt_plist = $obj->con1->prepare("select start_pg, end_pg from chapter where cid=?");
		$stmt_plist->bind_param("i",$chap_id);
  	$stmt_plist->execute();
  	$pg_res = $stmt_plist->get_result()->fetch_assoc();
  	$stmt_plist->close();
		//$html='<option value="">Select Chapter</option>';
  	$html='<div class="row" >
                <div class="mb-3 col-6">
                  <label class="form-label" for="basic-default-fullname">Start Page</label>
                  <input type="text" class="form-control" name="start_pg" id="start_pg" value="'.$pg_res["start_pg"].'" readonly />
                </div>
                <div class="mb-3 col-6">
                  <label class="form-label" for="basic-default-company">End Page</label>
                  <input type="text"  class="form-control " id="end_pg" name="end_pg"  value="'.$pg_res["end_pg"].'" readonly />
                </div>
            </div>';
  	echo $html;
	}
	
	if($_REQUEST['action']=="exerList")
	{
		$html="";
		$chap_id=explode(",",$_REQUEST["chap_id"]);
		for($j=0;$j<count($chap_id);$j++)
		{

		
		$stmt_elist = $obj->con1->prepare("select eid,exer_name from exercise where chap_id=?");
		$stmt_elist->bind_param("i",$chap_id[$j]);
	  	$stmt_elist->execute();
	  	$exer_res = $stmt_elist->get_result();
	  	$stmt_elist->close();
			
			if(mysqli_num_rows($exer_res)>0)
			{

			$i=0;
		  	while($exercise=mysqli_fetch_array($exer_res))
		  	{
					$html.='<div class="col-md-4"><input type="checkbox" name="e[]" id="exercise_'.$exercise["eid"].'" value="'.$exercise["eid"].'" onclick="getSkill(this.value,'.$j.','.$i.')"/> '.$exercise["exer_name"].'
						<div id="skill_list_div_'.$j.'_'.$i.'"></div>
					</div>';
					$i++;

		  	}
		  }
		  else
		  {
		  	$html.='<div class="col-md-3"><span class="text-danger">No exercise found</span></div>';

		  }
		}

	  	echo $html."@@@@@".mysqli_num_rows($exer_res);
	  
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
		
		$stmt_flist = $obj->con1->prepare("select id,name from `faculty` where designation!='Associate' and status='active' and id not in (SELECT f1.id FROM `batch` b1 , faculty f1 where b1.faculty_id = f1.id and b1.id=? union SELECT f2.id FROM `batch` b1 , faculty f2 where b1.assist_faculty_1 = f2.id and b1.id=? union SELECT f3.id FROM `batch` b1 , faculty f3 where b1.assist_faculty_2 = f3.id and b1.id=?)");
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

	if($_REQUEST['action']=="set_banner")
	{
		$typ=$_REQUEST["val"];
		$stmt_banner = $obj->con1->prepare("update motivation set `banner_display`= CASE
    WHEN (`type` = '".$typ."') THEN 'on'
    WHEN (`type` != '".$typ."') THEN 'off'
END ");
		$stmt_banner->bind_param("ss",$typ,$typ);
		$stmt_banner->execute();
		$res = $stmt_banner->get_result();
		$stmt_banner->close();


	}
	if($_REQUEST['action']=="assignment_modal")
	{
		$said=$_REQUEST["said"];
		
		$html="";
		// get stu assignment data

		//select sa.*, DATE_FORMAT(sa.alloted_dt, '%d-%m-%Y') as alloted, DATE_FORMAT(sa.expected_dt, '%d-%m-%Y') as expected, ba.name bname, s.name sname, b.bookname, c.chapter_name, e.exer_name, f.name fname,GROUP_CONCAT(sa.skill) as skill from stu_assignment sa, batch ba, books b, chapter c, student s, exercise e, faculty f where sa.batch_id= ba.id and sa.stu_id=s.sid and sa.book_id=b.bid and sa.chap_id=c.cid and sa.exercise_id=e.eid and sa.faculty_id=f.id 

		$stmt_assign = $obj->con1->prepare("select s1.*,s2.name,c1.chapter_name,b2.bookname,b1.name as batch_name from stu_assignment s1,student s2,batch b1,books b2,chapter c1 where s1.stu_id=s2.sid and s1.batch_id=b1.id and s1.book_id=b2.bid and s1.chap_id=c1.cid and s1.said=?");
		$stmt_assign->bind_param("i",$said);
		$stmt_assign->execute();
		$assign_data = $stmt_assign->get_result()->fetch_assoc();
		$stmt_assign->close();

		// exercise list

		
		$stmt_exe=$obj->con1->prepare("select * from stu_assignment where exercise_id=? and stu_id=?");
		$stmt_exe->bind_param("ii",$assign_data["exercise_id"],$assign_data["stu_id"]);
		$stmt_exe->execute();
		$res_exe=$stmt_exe->get_result();
		$stmt_exe->close();
		
	
		$html='<form  method="post"><div class="modal-body" ><div class="row">
		<input type="hidden" name="exe_id" value="'.$assign_data["exercise_id"].'">
		<input type="hidden" name="stu_id" value="'.$assign_data["stu_id"].'"/>
          <div class="col mb-3">
            <label for="nameWithTitle" class="form-label">Student Name</label>
            <input type="text" id="sname" name="sname" class="form-control" readonly value="'.$assign_data['name'].'"/>
          </div>
          
          <div class="col mb-3">
            <label for="nameWithTitle" class="form-label">Batch Name</label>
            <input type="text" id="batch" name="batch" class="form-control" readonly value="'.$assign_data['batch_name'].'"/>
          </div>
          <div class="col mb-0">
            <label for="emailWithTitle" class="form-label">Book Name</label>
            <input type="text" id="book" name="book" class="form-control" readonly value="'.$assign_data['bookname'].'"/>
          </div>
        </div>
        <div class="row g-2">
          
          <div class="col mb-0">
            <label for="dobWithTitle" class="form-label">Chapter Name</label>
            <input type="text" id="chapter" name="chapter" class="form-control" readonly value="'.$assign_data['chapter_name'].'"/>
          </div>
          <div class="col mb-0">
            <label for="dobWithTitle" class="form-label">Alloted Date</label>
            <input type="date" id="alloted_dt" name="alloted_dt" class="form-control" readonly value="'.$assign_data['alloted_dt'].'"/>
          </div>
          <div class="col mb-0">
            <label for="dobWithTitle" class="form-label">Expected Completion Date</label>
            <input type="date" id="completion_date" name="completion_date" class="form-control" readonly value="'.$assign_data['expected_dt'].'" />
          </div>
        </div>
        
        <div class="row g-2">
          <div class="col mb-0">
            <label for="emailWithTitle" class="form-label">Student Status</label>
            <input type="text" id="stu_status" name="stu_status" class="form-control" readonly value="'.$assign_data['stu_status'].'"/>
          </div>
          
        </div>';
        $i=0;
        while($skills=mysqli_fetch_array($res_exe))
        {

        
		    //faculty list
				$stmt_faculty= $obj->con1->prepare("select * from faculty");
				$stmt_faculty->execute();
				$res_faculty= $stmt_faculty->get_result();
				$stmt_faculty->close();
        $html.='<div class="card-body">
  			<legend>Skill Name: '.$skills['skill'].'</legend>
        <div class="row g-2">
          <div class="col mb-0">
            <label for="emailWithTitle" class="form-label">Faculty Remark</label>
            <select name="faculty_status'.$i.'" id="faculty_status'.$i.'" class="form-control">
              <option value="">Select Status</option>
              <option value="Completed with Good Understanding" '.($skills["status"]=="Completed with Good Understanding"?"selected":"").'>Completed with Good Understanding</option>
              <option value="Completed with Average Understanding" '.($skills["status"]=="Completed with Average Understanding"?"selected":"").'>Completed with Average Understanding</option>
              <option value="Completed with No Understanding" '.($skills["status"]=="Completed with No Understanding"?"selected":"").'>Completed with No Understanding</option>
            </select>
          </div>
          <div class="col mb-0">
            <label for="dobWithTitle" class="form-label">Faculty Name</label>
            <select name="faculty_id'.$i.'" id="faculty_id'.$i.'" class="form-control" required>
              <option value="">Select Faculty Name</option>';   
              while($faculty=mysqli_fetch_array($res_faculty))
              {
              	$html.='<option value="'.$faculty["id"].'" '.($skills["faculty_id"]==$faculty["id"]?"selected='selected'":"").'>'.$faculty["name"].'</option>';
              }
     
            $html.='</select>
          </div>
        </div>

        <div class="row g-2">
          <div class="col mb-0">
            <label for="emailWithTitle" class="form-label">Completion Date</label>
            <input type="date" id="completion_date'.$i.'" name="completion_date'.$i.'" class="form-control" value="'.$skills['completion_dt'].'"/>
          </div>
          <div class="col mb-0">
            <label for="dobWithTitle" class="form-label">Work Type</label><br>
            <input type="radio" id="work_hw'.$i.'" name="work_type'.$i.'" value="hw" '.($skills["work_type"]=="hw"?"checked":"").'/> Home Work
            <input type="radio" id="work_cw'.$i.'" name="work_type'.$i.'" value="cw" '.($skills["work_type"]=="cw"?"checked":"").'/> Class Work
          </div>
        </div>
        <div class="row g-2">
          <div class="col mb-0">
            <label for="emailWithTitle" class="form-label">Explaination Status</label><br>
            <input type="radio" id="not_explained'.$i.'" name="explain_status'.$i.'" value="Not Explained" '.($skills["explain_by_teacher"]=="Not Explained"?"checked":"").'/> Not Explained
            <input type="radio" id="half_explained'.$i.'" name="explain_status'.$i.'" value="Half Explained" '.($skills["explain_by_teacher"]=="Half Explained"?"checked":"").'/> Half Explained
            <input type="radio" id="explained'.$i.'" name="explain_status'.$i.'" value="Explained" '.($skills["explain_by_teacher"]=="Explained"?"checked":"").'/> Explained
          </div>
          
         
      </div>
      </div>
        <hr class="m-0">';
        $i++;
        }	

      
      $html.='<div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="btn_modal_update">Save Changes</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        </form>
      </div>';
      echo $html;
	}
	if($_REQUEST['action']=="get_stu_skills")
	{ 	
		$stu_id=$_REQUEST['stu_id'];
		$html="";
		$stmt_slist = $obj->con1->prepare("select * from skill");
		$stmt_slist->execute();
		$res1 = $stmt_slist->get_result();
		$stmt_slist->close();

		$stmt_clist = $obj->con1->prepare("select * from course");
		$stmt_clist->execute();
		$res2 = $stmt_clist->get_result();
		$stmt_clist->close();

		// stu skills
		$stmt_slist_Stu = $obj->con1->prepare("select GROUP_CONCAT(skill_id) as skill from stu_skills where stu_id=?");
		$stmt_slist_Stu->bind_param("i",$stu_id);
		$stmt_slist_Stu->execute();
		$res_Stu = $stmt_slist_Stu->get_result();
		$stu_Skills=mysqli_fetch_array($res_Stu);
		$stmt_slist_Stu->close();
		$skills = explode(",", $stu_Skills["skill"]);

		// stu_ course
		$stmt_course= $obj->con1->prepare("select GROUP_CONCAT(course_id) as course from stu_course where stu_id=?");
		$stmt_course->bind_param("i",$stu_id);
		$stmt_course->execute();
		$res_course = $stmt_course->get_result();
		$stu_course=mysqli_fetch_array($res_course);
		$stmt_course->close();
		$courses = explode(",", $stu_course["course"]);
		

			$html.='<div class="mb-3">
        <label class="form-label" for="basic-default-fullname">Course Enrolled</label>
        <select name="course[]" id="course" class="form-control js-example-basic-multiple" required multiple="multiple">
          <option value="">Select</option>';

           while($c=mysqli_fetch_array($res2)){

           	if (in_array($c['courseid'], $courses)) {
           		$html.='<option value="'.$c["courseid"].'" selected="selected">'.$c["coursename"].'</option>';
           	}
           	else
           	{

              $html.='<option value="'.$c["courseid"].'" >'.$c["coursename"].'</option>';
           	}
           } 
        $html.='</select>
      </div>
      <div class="mb-3">
        <label class="form-label" for="basic-default-fullname">Skills</label>
        <select name="skills[]" id="skills" class="form-control js-example-basic-multiple" required multiple="multiple">
        	<option value="">Select</option>';

				 while($s=mysqli_fetch_array($res1)){ 

				 	if (in_array($s['skid'], $skills)) {
				 		$html.='<option value="'.$s["skid"].'" selected="selected">'. $s["skills"].'</option>';
				 	}
				 	else
				 	{
				 		$html.='<option value="'.$s["skid"].'" >'. $s["skills"].'</option>';
				 	}
  				
  			}	
        $html.='</select>
      </div>';
      echo $html;
	}
	if($_REQUEST['action']=="assign_printing")
	{
		$assignment_id=$_REQUEST["said"];
		echo "select * from stu_assignment where said=$assignment_id";
		$stmt_assignment = $obj->con1->prepare("select * from stu_assignment where said=?");
		$stmt_assignment->bind_param("i",$assignment_id);
		$stmt_assignment->execute();
		$res_assignment = $stmt_assignment->get_result();
		$assigment=mysqli_fetch_array($res_assignment);
		$stmt_assignment->close();
		$copies=1;
		$status='pending';
		// insert into printing
		$stmt = $obj->con1->prepare("INSERT INTO `printing`(`faculty_id`,`book_id`,`chap_id`,`copies`,`status`) VALUES (?,?,?,?,?)");
	  $stmt->bind_param("iiiis", $assigment["faculty_id"],$assigment["book_id"],$assigment["chap_id"],$copies,$status);
	  $Resp=$stmt->execute();
	  $stmt->close();

	}

	if($_REQUEST['action']=="getBatch")
	{
		$batch_list="";
		$stu_list="";
		$stu_id=$_REQUEST["stu_id"];

		$stmt_list = $obj->con1->prepare("select b1.id,b1.name from batch_assign ba1, batch b1 where ba1.batch_id=b1.id and ba1.student_status!='transfered' and student_id=?");
		$stmt_list->bind_param("i",$stu_id);
  	$stmt_list->execute();
  	$batch_res = $stmt_list->get_result();
  	$stmt_list->close();

  	$stmt_slist = $obj->con1->prepare("select * from student where sid!=? and status='registered'");
		$stmt_slist->bind_param("i",$stu_id);
		$stmt_slist->execute();
		$student_res = $stmt_slist->get_result();
		$stmt_slist->close();
		
		if(mysqli_num_rows($batch_res)>0)
		{
	  	while($batch=mysqli_fetch_array($batch_res))
	  	{
				$batch_list.='<input type="checkbox" name="batch[]" id="" value="'.$batch["id"].'"/> '.$batch["name"];
	  	}
	  }
	  else
	  {
	  	$batch_list.='<div class="col-md-3"><span class="text-danger">No Batch Found</span></div>';
	  }

	  $stu_list.='<option value="">Select Student</option>';
	  while($stu=mysqli_fetch_array($student_res))
  	{
			$stu_list.='<option value="'.$stu['sid'].'">'.$stu['user_id']."-".$stu['name']."-".$stu['phone'].'</option>';
  	}

		echo $batch_list."@@@@@".$stu_list;
 	}
	
}


?>
