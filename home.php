<?php
include("header.php");

include_once("checkPer.php");
if($row=checkPermission($_SESSION["utype"],"dashboard")){ }

$date=isset($_COOKIE["dash_date"])?$_COOKIE['dash_date']:date('Y-m-d');

// total faculty
$stmt_list = $obj->con1->prepare("select * from faculty ");
$stmt_list->execute();
$faculty = $stmt_list->get_result()->num_rows;	
$stmt_list->close();

// total stu reg
$stmt_list2 = $obj->con1->prepare("select * from student  where `status`='registered'");
$stmt_list2->execute();
$students = $stmt_list2->get_result()->num_rows;	
$stmt_list2->close();

// total capacity 
$stmt_list3 = $obj->con1->prepare("select sum(capacity) as capacity from batch");
$stmt_list3->execute();
$res_capacity = $stmt_list3->get_result();	
$capacity=$res_capacity->fetch_assoc();
$stmt_list3->close();

// total stu capacity
$stmt_list10 = $obj->con1->prepare("select s1.* from student s1,batch_assign b1 where b1.student_id=s1.sid and s1.status='registered' and b1.batch_id!=37 and b1.student_status='ongoing'");
$stmt_list10->execute();
$stu_capacity = $stmt_list10->get_result()->num_rows;  
$stmt_list10->close();


// pending printing task
$stmt_list4 = $obj->con1->prepare("select * from printing where `status`='pending'");
$stmt_list4->execute();
$printing = $stmt_list4->get_result()->num_rows;	
$stmt_list4->close();

//today's lead
$stmt_list5 = $obj->con1->prepare("select * from student where inquiry_dt='".$date."' and status='inquiry' ");
$stmt_list5->execute();
$lead = $stmt_list5->get_result()->num_rows;  
$stmt_list5->close();

//today's registered


$stmt_list6 = $obj->con1->prepare("select * from student where enrollment_dt='".$date."' and status='registered' ");
$stmt_list6->execute();
$registered = $stmt_list6->get_result()->num_rows;  
$stmt_list6->close();


//today's present stu
$stmt_list7 = $obj->con1->prepare("select * from attendance where dt='".$date."' and faculty_attendance='p' ");
$stmt_list7->execute();
$present = $stmt_list7->get_result()->num_rows;  
$stmt_list7->close();

//today's absent stu

$stmt_list8 = $obj->con1->prepare("select * from attendance where dt='".$date."' and faculty_attendance='a'  ");
$stmt_list8->execute();
$absent = $stmt_list8->get_result()->num_rows;  
$stmt_list8->close();


//total unassigned stu
$stmt_list9 = $obj->con1->prepare("SELECT * FROM  student s1,batch_assign b1,batch b2 WHERE b1.student_id=s1.sid and b1.batch_id=b2.id  and b2.id=37 ");
$stmt_list9->execute();
$unassigned = $stmt_list9->get_result()->num_rows;  
$stmt_list9->close();
?>
<!-- <div class="row">
	<div class="col-lg-12 mb-4 order-0">
		<div class="card">
			<div class="d-flex align-items-end row">
			  <div class="col-sm-7">
			    <div class="card-body">
			      <h5 class="card-title text-primary">Congratulations John! 🎉</h5>
			      <p class="mb-4">
			        You have done <span class="fw-bold">72%</span> more sales today. Check your new badge in
			        your profile.
			      </p>

			      <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a>
			    </div>
			  </div>
			  <div class="col-sm-5 text-center text-sm-left">
			    <div class="card-body pb-0 px-0 px-md-4">
			      <img
			        src="assets/img/illustrations/man-with-laptop-light.png"
			        height="140"
			        alt="View Badge User"
			        data-app-dark-img="illustrations/man-with-laptop-dark.png"
			        data-app-light-img="illustrations/man-with-laptop-light.png"
			      />
			    </div>
			  </div>
			</div>
		</div>
	</div>
</div> -->

 
<?php if($row["read_func"]=="y"){ ?>

<div class="row">
  <div class="navbar-nav-right d-flex align-items-center mb-3" id="navbar-collapse">
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <i class="bx bx-calendar fs-4 lh-0"></i>
        <form method="post" id="dashboard_frm">
        <input type="date" class="form-control border-0 shadow-none" name="dash_date" id="dash_date" onchange="get_dashboard_data(this.value)" value="<?php echo isset($_COOKIE['dash_date'])?$_COOKIE['dash_date']:date('Y-m-d')?>">
        <input type="submit" name="dash_submit" class="d-none">
      </form>
      </div>
    </div>
  </div>
    <div class="col-lg-12 col-md-12 order-1">
      <div class="row">
        <div class="col-lg-3 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                    src="assets/img/icons/unicons/chart-success.png"
                    alt="chart success"
                    class="rounded"
                  />
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt3"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                    <a class="dropdown-item" href="stu_report.php?typ=unassigned">View More</a>
                    
                  </div>
                </div>
              </div>
              <span class="fw-semibold d-block mb-1">Updation Left</span>
              <h3 class="card-title mb-2"><?php echo $unassigned?></h3>
              
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <span class="avatar-initial rounded bg-label-info"> <i class="bx bxs-chalkboard"></i></span>
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt3"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                    <a class="dropdown-item" href="branch.php">View More</a>
                    
                  </div>
                </div>
              </div>
              <span class="fw-semibold d-block mb-1">Total Capacity</span>
              <h3 class="card-title mb-2"><?php echo $stu_capacity."/".$capacity["capacity"]?></h3>
              
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <span class="avatar-initial rounded bg-label-primary"> <i class="bx bxs-user-account"></i></span>
                </div>
                <div class="dropdown">
                  <button class="btn p-0" type="button" id="cardOpt4"  data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                    <a class="dropdown-item" href="student_reg.php">View More</a>
                    
                  </div>
                </div>
              </div>
              <span class="d-block mb-1">Total Students</span>
              <h3 class="card-title text-nowrap mb-2"><?php echo $students?></h3>
              
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">

                  <span class="avatar-initial rounded bg-label-primary"> <i class="bx bx-printer"></i></span>
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt1"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt1">
                    <a class="dropdown-item" href="assign_printing.php">View More</a>
                    
                  </div>
                </div>
              </div>
              <span class="fw-semibold d-block mb-1">Pending Printing Task</span>
              <h3 class="card-title mb-2"><?php echo $printing?></h3>
              
            </div>
          </div>
        </div>



        <div class="col-lg-3 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <span class="avatar-initial rounded bg-label-secondary"> <i class="bx bx-user-plus"></i></span>
                </div>
                <div class="dropdown">
                  <button class="btn p-0" type="button" id="cardOpt4"  data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                    <a class="dropdown-item" href="enquiry_report.php?typ=today">View More</a>
                    
                  </div>
                </div>
              </div>
              <span class="d-block mb-1">Today's Inquiry</span>
              <h3 class="card-title text-nowrap mb-2"><?php echo $lead?></h3>
              
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <span class="avatar-initial rounded bg-label-info"> <i class="bx bxs-user-plus"></i></span>
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt1"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt1">
                    <a class="dropdown-item" href="stu_report.php?typ=new_admission">View More</a>
                    
                  </div>
                </div>
              </div>
              <span class="fw-semibold d-block mb-1">New Admission</span>
              <h3 class="card-title mb-2"><?php echo $registered?></h3>
              
            </div>
          </div>
        </div>


        <div class="col-lg-3 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <span class="avatar-initial rounded bg-label-success"> <i class="bx bx2 bxs-user-check"></i></span>
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt1"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt1">
                    <a class="dropdown-item" href="attendance_report_detail.php">View More</a>
                    
                  </div>
                </div>
              </div>
              <span class="fw-semibold d-block mb-1">Total Present</span>
              <h3 class="card-title mb-2"><?php echo $present?></h3>
              
            </div>
          </div>
        </div>


        <div class="col-lg-3 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <span class="avatar-initial rounded bg-label-danger"> <i class="bx bx2 bxs-user-x"></i></span>
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt1"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt1">
                    <a class="dropdown-item" href="attendance_report_detail.php">View More</a>
                    
                  </div>
                </div>
              </div>
              <span class="fw-semibold d-block mb-1">Total Absent</span>
              <h3 class="card-title mb-2"><?php echo $absent?></h3>
              
            </div>
          </div>
        </div>

      </div>
    </div>
</div>    

<?php } ?>   

<script type="text/javascript">
  // Use datepicker on the date inputs
/*$("#dash_date").datepicker({
  dateFormat: 'dd-mm-yyyy',
  onSelect: function(dateText, inst) {
    $(inst).val(dateText); // Write the value in the input
  }
});*/



</script>




<?php 
include("footer.php");
?>