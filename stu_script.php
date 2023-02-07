<?php
//script to add skill & course 
include("header.php");

$stmt_slist = $obj->con1->prepare("select * from student where courseid!=''");
$stmt_slist->execute();
$res = $stmt_slist->get_result();
$stmt_slist->close();
while($course=mysqli_fetch_array($res))
{
	$stmt_course = $obj->con1->prepare("INSERT INTO `stu_course`( `stu_id`, `course_id`) VALUES (?,?)");
	  $stmt_course->bind_param("ii", $course["sid"],$course["courseid"]);
	  $Resp_course=$stmt_course->execute();
	  $stmt_course->close();
}

$stmt_skill = $obj->con1->prepare("select * from student where skillid!=''");
$stmt_skill->execute();
$res_skill = $stmt_skill->get_result();
$stmt_skill->close();
while($skill=mysqli_fetch_array($res_skill))
{

	$stmt_skills = $obj->con1->prepare("INSERT INTO `stu_skills`(  `stu_id`, `skill_id`) VALUES (?,?)");
      $stmt_skills->bind_param("ii", $skill["sid"],$skill["skillid"]);
      $Resp_skill=$stmt_skills->execute();
      $stmt_skills->close();
}

include 'footer.php';
?>