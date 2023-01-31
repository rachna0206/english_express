<?php 
ob_start();
//include ("db_connect.php");
//$obj=new DB_connect();
date_default_timezone_set("Asia/Kolkata");
error_reporting(0);

session_start();
include("checkPer.php");

// for permission
if($row1=checkPermission($_SESSION["utype"],"notify")){ }



if(!isset($_SESSION["userlogin"]) )
{
    header("location:index.php");
}
$menu = array();
if($res=checkMainMenu($_SESSION["utype"])){
	$i=0;	
	while($row=mysqli_fetch_array($res)){

		$menu[$row[0]]=$row[0];
	}
}
else{ }

$frontdeskmenu=array("lead_generation.php","student_reg.php","batch.php","batch_assign.php");
$facultymenu=array("stu_assignment.php","attendance.php","assign_printing.php","printing_inventory.php");
$adminmenu=array("branch.php","course.php","skills.php","Book.php","chapter.php","city.php","exercise.php","faculty_reg.php","associate_reg.php","motivation.php","permissions.php","send_notification.php","state.php","transfer.php","task.php");
$reportmenu=array("stu_report.php","enquiry_report.php");
?>

<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Dashboard | English Express</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon_200.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- data tables -->
    <link rel="stylesheet" type="text/css" href="assets/vendor/DataTables/datatables.css">
     
    <!-- Row Group CSS -->
    <!-- <link rel="stylesheet" href="assets/vendor/datatables-rowgroup-bs5/rowgroup.bootstrap5.css"> -->
    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script type="text/javascript">
      function createCookie(name, value, days) {
        var expires;
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        } else {
            expires = "";
        }
        document.cookie = (name) + "=" + String(value) + expires + ";path=/ ";

    }

    function readCookie(name) {
        var nameEQ = (name) + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return (c.substring(nameEQ.length, c.length));
        }
        return null;
    }

    function eraseCookie(name) {
        createCookie(name, "", -1);
    }
    function get_dashboard_data(date)
    {
      createCookie("dash_date", date, 1);
     
      document.getElementById("dashboard_frm").submit(); 
    }

    $(function() {
   setInterval("get_notification()", 10000);

});


function get_notification() {

    $.ajax({
        async: true,
        url: 'ajaxdata.php?action=get_notification',
        type: 'POST',
        data: "",

        success: function (data) {
           // console.log(data);

            var resp=data.split("@@@@");
            $('#notification_list').html('');
            $('#notification_list').append(resp[0]);
            $('#notification_count').html('');
            $('#noti_count').html('');
            
            if(resp[1]>0) {
                $('#notification_count').append(resp[1]);

                $('#noti_count').append(resp[1]);
                
                playSound();
            }
            else
            {
                 $('#noti_count').append('');
                 $('#notification_list').hide();
            }
        }

    });
}
function removeNotification(id){

    $.ajax({
        async: true,
        type: "GET",
        url: "ajaxdata.php?action=removenotification",
        data:"id="+id,
        async: true,
        cache: false,
        timeout:50000,

        success: function(data){
            
            window.location = "lead_generation.php";
          

        }
    });
}
function playSound(){

    $.ajax({
        async: true,
        url: 'ajaxdata.php?action=get_Playnotification',
        type: 'POST',
        data: "",

        success: function (data) {
            // console.log(data);

            var resp=data.split("@@@@");

            if(resp[0]>0) {

                var mp3Source = '<source src="notif_sound.wav" type="audio/mpeg">';
                document.getElementById("sound").innerHTML='<audio autoplay="autoplay">' + mp3Source +  '</audio>';
                removeplaysound(resp[1]);
            }
        }

    });

}

function removeplaysound(ids) {

    $.ajax({
        async: true,
        type: "GET",
        url: "ajaxdata.php?action=removeplaysound",
        data:"id="+ids,
        async: true,
        cache: false,
        timeout:50000,

    });

}
    </script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="#" class="app-brand-link">
              
              <span class="app-brand-text demo menu-text fw-bolder ms-2">English Express</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item active">
              <a href="home.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>


            <!-- Forms & Tables -->
            <!-- <li class="menu-header small text-uppercase"><span class="menu-header-text">Masters</span></li> -->
            <!-- Forms -->
            <?php  if(in_array("lead_generation",$menu) || in_array("student_reg",$menu) || in_array("batch",$menu) || in_array("batch_assign",$menu))
            {
              ?>
            <li class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]),$frontdeskmenu)?"active open":"" ?> ">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Elements">Front Desk</div>
              </a>
              <ul class="menu-sub">
			  
      				<?php
                if(isset($menu["lead_generation"])=="lead_generation"){ ?>
                  <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="lead_generation.php"?"active":"" ?>">
                    <a href="lead_generation.php" class="menu-link">
                    <div data-i18n="course">Enquiries</div>
                    </a>
                  </li>
                  <?php }
                  if(isset($menu["student_reg"])=="student_reg"){ ?>
                  <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="student_reg.php"?"active":"" ?>">
                            <a href="student_reg.php" class="menu-link">
                              <div data-i18n="course">Admission</div>
                            </a>
                          </li>
                  <?php }

                    if(isset($menu["batch"])=="batch"){ ?>
                      <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="batch.php"?"active":"" ?>">
                                <a href="batch.php" class="menu-link">
                                  <div data-i18n="course">Batch</div>
                                </a>
                              </li>
                      <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="batch_assign.php"?"active":"" ?>">
                                <a href="batch_assign.php" class="menu-link">
                                  <div data-i18n="course">Batch Assign</div>
                                </a>
                              </li>
                      <?php }

                ?>
				
                <!-- <li class="menu-item">
                  <a href="forms-input-groups.html" class="menu-link">
                    <div data-i18n="Input groups">Input groups</div>
                  </a>
                </li> -->
              </ul>
            </li>
            <?php
            }
            ?>

            <?php  if(in_array("student_assign",$menu) || in_array("attendance",$menu) || in_array("printing",$menu) )
            {
              ?>

            <li class="menu-item  <?php echo in_array(basename($_SERVER["PHP_SELF"]),$facultymenu)?"active open":"" ?> ">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Elements">Faculty Controls</div>
              </a>
              <ul class="menu-sub">
                <?php 
                  if(isset($menu["student_assign"])=="student_assign"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="stu_assignment.php"?"active":"" ?>">
                        <a href="stu_assignment.php" class="menu-link">
                          <div data-i18n="course">Assignment Section</div>
                        </a>
                      </li>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="single_stu_assignment.php"?"active":"" ?>">
                <a href="single_stu_assignment.php" class="menu-link">
                  <div data-i18n="course">Single Student Assignment Section</div>
                </a>
              </li>
              <?php }
              if(isset($menu["attendance"])=="attendance"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="attendance.php"?"active":"" ?>">
                        <a href="attendance.php" class="menu-link">
                          <div data-i18n="course">Attendance</div>
                        </a>
                      </li>
              <?php }
              if(isset($menu["printing"])=="printing"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="assign_printing.php"?"active":"" ?>">
                        <a href="assign_printing.php" class="menu-link">
                          <div data-i18n="course">Printing Section</div>
                        </a>
                      </li>
                      <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="printing_inventory.php"?"active":"" ?>">
                        <a href="printing_inventory.php" class="menu-link">
                          <div data-i18n="course">Printing Inventory</div>
                        </a>
                      </li>
              <?php }

                ?>

              </ul>
            </li>
            <?php
            }
            ?>

            <?php  if(in_array("branch",$menu) || in_array("course_master",$menu) || in_array("skill_master",$menu) || in_array("book_master",$menu) || in_array("chap_master",$menu) || in_array("exercise_master",$menu) || in_array("faculty_reg",$menu) || in_array("motivation",$menu) || in_array("permission",$menu) || in_array("associate_reg",$menu) || in_array("task",$menu) || in_array("state",$menu) || in_array("city",$menu) || in_array("area",$menu) || in_array("notification",$menu) || in_array("transfer",$menu))
            {
              ?>

            <li class="menu-item <?php echo in_array(basename($_SERVER["PHP_SELF"]),$adminmenu)?"active open":"" ?> ">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Elements">Admin Controls</div>
              </a>
              <ul class="menu-sub">
                <?php 
                  if(isset($menu["branch"])=="branch"){ ?>
                      <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="branch.php"?"active":"" ?>">
                        <a href="branch.php" class="menu-link">
                          <div data-i18n="Branch">Branch Master</div>
                        </a>
                      </li>
              <?php } if(isset($menu["course_master"])=="course_master"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="course.php"?"active":"" ?>">
                        <a href="course.php" class="menu-link">
                          <div data-i18n="course">Course Master</div>
                        </a >
                      </li>
              <?php } if(isset($menu["skill_master"])=="skill_master"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="skills.php"?"active":"" ?>">
                        <a href="skills.php" class="menu-link">
                          <div data-i18n="course">Skill Master</div>
                        </a>
                      </li>
              <?php }   if(isset($menu["book_master"])=="book_master"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="Book.php"?"active":"" ?>">
                        <a href="Book.php" class="menu-link">
                          <div data-i18n="course">Books Master</div>
                        </a>
                      </li>
              <?php } if(isset($menu["chap_master"])=="chap_master"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="chapter.php"?"active":"" ?>">
                        <a href="chapter.php" class="menu-link">
                          <div data-i18n="course">Chapter Master</div>
                        </a>
                      </li>
              <?php } if(isset($menu["exercise_master"])=="exercise_master"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="exercise.php"?"active":"" ?>">
                        <a href="exercise.php" class="menu-link">
                          <div data-i18n="course">Exercise Master</div>
                        </a>
                      </li>
              <?php } 
              if(isset($menu["faculty_reg"])=="faculty_reg"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="faculty_reg.php"?"active":"" ?>">
                        <a href="faculty_reg.php" class="menu-link">
                          <div data-i18n="course">Staff Master</div>
                        </a>
                      </li>
              <?php }
                if(isset($menu["motivation"])=="motivation"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="motivation.php"?"active":"" ?>">
                        <a href="motivation.php" class="menu-link">
                          <div data-i18n="course">Motivation Master</div>
                        </a>
                      </li>
              <?php } if(isset($menu["permission"])=="permission"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="permissions.php"?"active":"" ?>">
                        <a href="permissions.php" class="menu-link">
                          <div data-i18n="course">Permission Master</div>
                        </a>
                      </li>
              <?php } if(isset($menu["associate_reg"])=="associate_reg"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="associate_reg.php"?"active":"" ?>">
                <a href="associate_reg.php" class="menu-link">
                <div data-i18n="course">Associates Master</div>
                </a>
              </li>
            <?php } if(isset($menu["task"])=="task"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="task.php"?"active":"" ?>">
                <a href="task.php" class="menu-link">
                <div data-i18n="course">Task Assign Master</div>
                </a>
              </li>
              <?php }
              if(isset($menu["transfer"])=="transfer"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="transfer.php"?"active":"" ?>">
                <a href="transfer.php" class="menu-link">
                <div data-i18n="course">Transfer Master</div>
                </a>
              </li>
              <?php
                }
                 if(isset($menu["state"])=="state"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="state.php"?"active":"" ?>">
                <a href="state.php" class="menu-link">
                <div data-i18n="course">State Master</div>
                </a>
              </li>
              <?php } if(isset($menu["city"])=="city"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="city.php"?"active":"" ?>">
                <a href="city.php" class="menu-link">
                <div data-i18n="course">City Master</div>
                </a>
              </li>
              <?php } if(isset($menu["area"])=="area"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="area.php"?"active":"" ?>">
                <a href="area.php" class="menu-link">
                <div data-i18n="course">Area Master</div>
                </a>
              </li>
              <?php }
               if(isset($menu["notification"])=="notification"){ ?>
              <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="send_notification.php"?"active":"" ?>">
                <a href="send_notification.php" class="menu-link">
                <div data-i18n="course">Notification Center</div>
                </a>
              </li>
              <?php }

                ?>

              </ul>
            </li>
            <?php

              }
          ?>

          <?php  if(in_array("student_report",$menu) || in_array("faculty_report",$menu) || in_array("attendance_report",$menu) || in_array("enquiry_report",$menu) )
            {
              ?>

            <li class="menu-item  <?php echo in_array(basename($_SERVER["PHP_SELF"]),$reportmenu)?"active open":"" ?> ">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Form Elements">Reports</div>
              </a>
              <ul class="menu-sub">
                
                <?php if(isset($menu["student_report"])=="student_report"){ ?>
                <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="stu_report.php"?"active":"" ?>">
                  <a href="stu_report.php" class="menu-link">
                  <div data-i18n="course">Student Report</div>
                  </a>
                </li>
                <?php } if(isset($menu["faculty_report"])=="faculty_report"){ ?>
                <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="faculty_report.php"?"active":"" ?>">
                  <a href="faculty_report.php" class="menu-link">
                  <div data-i18n="course">Staff Report</div>
                  </a>
                </li>
                <?php } if(isset($menu["attendance_report"])=="attendance_report"){ ?>
                <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="attendance_report_detail.php"?"active":"" ?>">
                  <a href="attendance_report_detail.php" class="menu-link">
                  <div data-i18n="course">Attendance Report</div>
                  </a>
                </li>
                <?php } ?>
                <li class="menu-item <?php echo basename($_SERVER["PHP_SELF"])=="enquiry_report.php"?"active":"" ?>">
                  <a href="enquiry_report.php" class="menu-link">
                  <div data-i18n="course">Enquiry Report</div>
                  </a>
                </li>

              </ul>
            </li>
            <?php
              }
            ?>
            
           
          </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar"
          >
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  <ul class="dropdown-menu ">
                    
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle"><?php echo ucfirst($_SESSION["username"])?></span>
                      </a>
                    </li>
                   
                    
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="logout.php">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                  <!-- <a class="dropdown-item" href="#">
                        <span class="d-flex align-items-center align-middle">
                          <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                          
                          <span class="flex-shrink-0 badge badge-center rounded-pill bg-success w-px-20 h-px-20">4</span>
                        </span>
                      </a> -->
                </div>
              </div>

              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                

              <?php if($row1["read_func"]=="y"){ ?>
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar">
                     <i class="bx bx-bell"></i>
                     <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20" id="noti_count"></span>
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end" id="notification_list" style="overflow-y: auto;height: 400px;">
                  </ul>
                </li>
              <?php } ?>


                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="assets/img/favicon_200.png" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    
                    <li>
                      <a class="dropdown-item" href="#">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle"><?php echo ucfirst($_SESSION["username"])?></span>
                      </a>
                    </li>
                   
                    
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="logout.php">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
              <!-- / User -->
              </ul>
            </div>
          </nav>
          <div id="sound"></div>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">