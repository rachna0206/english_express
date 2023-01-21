<?php
include("header.php");

// for permission
if($row=checkPermission($_SESSION["utype"],"exercise_master")){ }
else{
  header("location:home.php");
}


$stmt_blist = $obj->con1->prepare("select * from books");
$stmt_blist->execute();
$res = $stmt_blist->get_result();
$stmt_blist->close();

$id = "";
if(isset($_COOKIE["bid"])){
  $id = $_COOKIE["bid"];
  setcookie("bid","",time()-360);

  $stmt_clist = $obj->con1->prepare("select * from chapter where book_id=$id");
  $stmt_clist->execute();
  $res1 = $stmt_clist->get_result();
  $stmt_clist->close();
}
  
$stmt_slist = $obj->con1->prepare("select * from skill");
$stmt_slist->execute();
$res2 = $stmt_slist->get_result();
$stmt_slist->close();


// insert data
if(isset($_REQUEST['btnsubmit']))
{
  $book_id = $_REQUEST['book'];
  $ch_id = $_REQUEST['chap'];
  
  $num_exercise = $_REQUEST['field_cnt'];
  $grammer = "n";
  $vocabulary = "n";
  $pronunciation = "n";
  $spelling = "n";
  $presentation = "n";
  $speaking = "n";
  $listening = "n";
  $writing = "n";
  $reading = "n";
  $exer_no = $_REQUEST['exerno'];

  for($i=0;$i<$num_exercise;$i++)
  {
    
    $grammer = "n";
    $vocabulary = "n";
    $pronunciation = "n";
    $spelling = "n";
    $presentation = "n";
    $speaking = "n";
    $listening = "n";
    $writing = "n";
    $reading = "n";
    $exer_name = $_REQUEST['exer_name'.$i];
    if(isset($_REQUEST['grammer'.$i])){
      $grammer = "y";
    }
    if(isset($_REQUEST['vocabulary'.$i])){
      $vocabulary = "y";
    }
    if(isset($_REQUEST['pronunciation'.$i])){
      $pronunciation = "y";
    }
    if(isset($_REQUEST['spelling'.$i])){
      $spelling = "y";
    }
    if(isset($_REQUEST['presentation'.$i])){
      $presentation = "y";
    }
    if(isset($_REQUEST['speaking'.$i])){
      $speaking = "y";
    }
    if(isset($_REQUEST['listening'.$i])){
      $listening = "y";
    }
    if(isset($_REQUEST['writing'.$i])){
      $writing = "y";
    }
    if(isset($_REQUEST['reading'.$i])){
      $reading = "y";
    }
  
  
    try
    {
      if(isset($_REQUEST['exer_name'.$i]))
      {
        $stmt = $obj->con1->prepare("INSERT INTO `exercise`(`book_id`,`chap_id`,`exer_name`,`grammer`,`vocabulary`,`pronunciation`,`spelling`,`presentation`,`speaking`,`listening`,`writing`,`reading`,`exer_no`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("iissssssssssi",$book_id,$ch_id,$exer_name,$grammer,$vocabulary,$pronunciation,$spelling,$presentation,$speaking,$listening,$writing,$reading,$exer_no);
        $Resp=$stmt->execute();
          if(!$Resp)
          {
            throw new Exception("Problem in adding! ". strtok($obj->con1-> error,  '('));
          }
          $stmt->close();
      }
    } 
    catch(\Exception  $e) {
      setcookie("sql_error", urlencode($e->getMessage()),time()+3600,"/");
    }
  }


  if($Resp)
  {
    setcookie("msg", "data",time()+3600,"/");
      header("location:exercise.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:exercise.php");
  }
}

if(isset($_REQUEST['btnupdate']))
{
  $book_id = $_REQUEST['book'];
  $ch_id = $_REQUEST['chap'];
  $num_exercise = $_REQUEST['field_cnt'];
  $eid = $_REQUEST['ttId'];

  $grammer = "n";
  $vocabulary = "n";
  $pronunciation = "n";
  $spelling = "n";
  $presentation = "n";
  $speaking = "n";
  $listening = "n";
  $writing = "n";
  $reading = "n";
  $exer_no = $_REQUEST['exerno'];

  $exer_name = $_REQUEST['exer_name0'];
    if(isset($_REQUEST['grammer0'])){
      $grammer = "y";
    }
    if(isset($_REQUEST['vocabulary0'])){
      $vocabulary = "y";
    }
    if(isset($_REQUEST['pronunciation0'])){
      $pronunciation = "y";
    }
    if(isset($_REQUEST['spelling0'])){
      $spelling = "y";
    }
    if(isset($_REQUEST['presentation0'])){
      $presentation = "y";
    }
    if(isset($_REQUEST['speaking0'])){
      $speaking = "y";
    }
    if(isset($_REQUEST['listening0'])){
      $listening = "y";
    }
    if(isset($_REQUEST['writing0'])){
      $writing = "y";
    }
    if(isset($_REQUEST['reading0'])){
      $reading = "y";
    }

  try
  {
    $stmt = $obj->con1->prepare("update exercise set book_id=?, chap_id=?, exer_name=?, grammer=?, vocabulary=?, pronunciation=?, spelling=?, presentation=?, speaking=?, listening=?, writing=?, reading=?, exer_no=? where eid=?");
  $stmt->bind_param("iissssssssssii", $book_id,$ch_id,$exer_name,$grammer,$vocabulary,$pronunciation,$spelling,$presentation,$speaking,$listening,$writing,$reading,$exer_no,$eid);
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
      header("location:exercise.php");
  }
  else
  {
    setcookie("msg", "fail",time()+3600,"/");
      header("location:exercise.php");
  }
}

// delete data
if(isset($_REQUEST["flg"]) && $_REQUEST["flg"]=="del")
{
  try
  {
    $stmt_del = $obj->con1->prepare("delete from exercise where eid='".$_REQUEST["n_id"]."'");
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
    header("location:exercise.php");
  }
  else
  {
  setcookie("msg", "fail",time()+3600,"/");
    header("location:exercise.php");
  }
}

?>

<script type="text/javascript">

  function chapList(book){
    $.ajax({
          async: true,
          type: "POST",
          url: "ajaxdata.php?action=get_chapters",
          data: "book="+book,
          cache: false,
          success: function(result){
           
            $('#chap').html('');
            $('#chap').append(result);
       
            }
        });
  }

</script>

<h4 class="fw-bold py-3 mb-4">Exercise Master</h4>

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
                      <h5 class="mb-0">Add Exercise</h5>
                      
                    </div>
                    <div class="card-body">
                      <form method="post" >
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Book Name</label>
                          <select name="book" id="book" onChange="chapList(this.value)" class="form-control" required>
                            <option value="-1">Select Book</option>
                    <?php    
                        while($book=mysqli_fetch_array($res)){
              
                      ?>
                            <option value="<?php echo $book["bid"] ?>"><?php echo $book["bookname"] ?></option>
                    <?php
              
                      }
                    ?>
                </select>
                          <input type="hidden" name="ttId" id="ttId">
                        </div>
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Chapter Name</label>
                          <select name="chap" id="chap" class="form-control" required>
                            <option value="">Select Chapter</option>
                    <?php    
                        while($chap=mysqli_fetch_array($res1)){
              
                    ?>
                        <option value="<?php echo $chap["cid"] ?>"><?php echo $chap["chapter_name"] ?></option>
                    <?php
              
            }
          ?>
                </select>
                        </div>
                       
                       <!--  <div class="mb-3" id="num_div">
                          <label class="form-label" for="basic-default-fullname">Number of Exercise</label>
                          <input type="number" step="1" min="0" class="form-control" name="num" id="num" required onblur="create_field(this.value)"/>
                          
                        </div> -->
                        
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Exercise Number</label>
                          <input type="number" class="form-control" name="exerno" id="exerno" required />
                        </div>

                        <div class="mb-3">
                          <input type="hidden" name="field_cnt" id="field_cnt" value="1"/>
                          <label class="form-label" for="basic-default-fullname">Exercise Name</label>
                          <a href="javascript:create_field(this.value)" class="text-right"><i class="bx bxs-add-to-queue bx-sm"></i></a>
                          <input type="text" class="form-control" name="exer_name0" id="exer_name0" required />

                        </div>
                        
                        <div class="mb-3">
                           <input type="checkbox" name="grammer0" id="grammer0"/> Grammar
                           <input type="checkbox" name="vocabulary0" id="vocabulary0"/> Vocabulary
                           <input type="checkbox" name="pronunciation0" id="pronunciation0"/> Pronunciation
                           <input type="checkbox" name="spelling0" id="spelling0"/> Spelling
                           <input type="checkbox" name="presentation0" id="presentation0"/> Presentation
                           <input type="checkbox" name="speaking0" id="speaking0"/> Speaking
                           <input type="checkbox" name="listening0" id="listening0"/> Listening
                           <input type="checkbox" name="writing0" id="writing0"/> Writing
                           <input type="checkbox" name="reading0" id="reading0"/> Reading        
                        </div>
                        <div id="fields_div">
                        </div>
                        
                    <?php if($row["write_func"]=="y"){ ?>
                        <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary">Save</button>
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
                <h5 class="card-header">Exercise Records</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table" id="table_id">

                    <thead>
                      <tr>
                        <th>Srno</th>
                        <th>Exercise Number</th>
                        <th>Exercise Name</th>
                        <th>Book Name</th>
                        <th>Chapter Name</th>
                        <th>G</th>
                        <th>V</th>
                        <th>Pro</th>
                        <th>Spel</th>
                        <th>Pre</th>
                        <th>S</th>
                        <th>L</th>
                        <th>W</th>
                        <th>R</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php 
                        $stmt_list = $obj->con1->prepare("select e.*,b.bookname,c.chapter_name from exercise e, books b, chapter c where e.book_id=b.bid and e.chap_id=c.cid order by eid desc");
                        $stmt_list->execute();
                        $result = $stmt_list->get_result();
                        
                        $stmt_list->close();
                        $i=1;
                        while($e=mysqli_fetch_array($result))
                        {
                          ?>

                      <tr>
                        <td><?php echo $i?></td>
                        <td><?php echo $e["exer_no"] ?></td>
                        <td><?php echo $e["exer_name"] ?></td>
                        <td><?php echo $e["bookname"] ?></td>
                        <td><?php echo $e["chapter_name"] ?></td>
                        <td><?php if($e["grammer"]=="y"){ ?> &#9989 <?php } else{ ?> &#10060 <?php } ?></td>
                        <td><?php if($e["vocabulary"]=="y"){ ?> &#9989 <?php } else{ ?> &#10060 <?php } ?></td>
                        <td><?php if($e["pronunciation"]=="y"){ ?> &#9989 <?php } else{ ?> &#10060 <?php } ?></td>
                        <td><?php if($e["spelling"]=="y"){ ?> &#9989 <?php } else{ ?> &#10060 <?php } ?></td>
                        <td><?php if($e["presentation"]=="y"){ ?> &#9989 <?php } else{ ?> &#10060 <?php } ?></td>
                        <td><?php if($e["speaking"]=="y"){ ?> &#9989 <?php } else{ ?> &#10060 <?php } ?></td>
                        <td><?php if($e["listening"]=="y"){ ?> &#9989 <?php } else{ ?> &#10060 <?php } ?></td>
                        <td><?php if($e["writing"]=="y"){ ?> &#9989 <?php } else{ ?> &#10060 <?php } ?></td>
                        <td><?php if($e["reading"]=="y"){ ?> &#9989 <?php } else{ ?> &#10060 <?php } ?></td>
                        
                    <?php if($row["read_func"]=="y" || $row["upd_func"]=="y" || $row["del_func"]=="y"){ ?>
                        <td>
                        <?php if($row["upd_func"]=="y"){ ?>
                          <a href="javascript:editdata('<?php echo $e["eid" ]?>','<?php echo $e["book_id"] ?>','<?php echo $e["chap_id"] ?>','<?php echo $e["exer_no"] ?>','<?php echo $e["exer_name"] ?>','<?php echo $e["grammer"] ?>','<?php echo $e["vocabulary"] ?>','<?php echo $e["pronunciation"] ?>','<?php echo $e["spelling"] ?>','<?php echo $e["presentation"] ?>','<?php echo $e["speaking"] ?>','<?php echo $e["listening"] ?>','<?php echo $e["writing"] ?>','<?php echo $e["reading"] ?>');"><i class="bx bx-edit-alt me-1"></i> </a>
                        <?php } if($row["del_func"]=="y"){ ?>
              <a  href="javascript:deletedata('<?php echo $e["eid"]?>');"><i class="bx bx-trash me-1"></i> </a>
                        <?php } if($row["read_func"]=="y"){ ?>
                          <a href="javascript:viewdata('<?php echo $e["eid" ]?>','<?php echo $e["book_id"] ?>','<?php echo $e["chap_id"] ?>','<?php echo $e["exer_no"] ?>','<?php echo $e["exer_name"] ?>','<?php echo $e["grammer"] ?>','<?php echo $e["vocabulary"] ?>','<?php echo $e["pronunciation"] ?>','<?php echo $e["spelling"] ?>','<?php echo $e["presentation"] ?>','<?php echo $e["speaking"] ?>','<?php echo $e["listening"] ?>','<?php echo $e["writing"] ?>','<?php echo $e["reading"] ?>');">View</a>
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
  function deletedata(id) {

      if(confirm("Are you sure to DELETE data?")) {
          var loc = "exercise.php?flg=del&n_id=" + id;
          window.location = loc;
      }
  }
  function editdata(id,book,chap,exerno,nm,gra,voc,pron,spell,pres,speak,list,writ,read) {
      $('#ttId').val(id);
      $('#book').val(book);

      chapList(book);// get chapters from book
      $('#chap').val(chap);
      $('#num_div').hide();
      $('#num').removeAttr("required");
      $('#exerno').val(exerno);
      $('#exer_name0').val(nm);
      if(gra=="y"){
        $('#grammer0').attr("checked","checked");
      } else{
        $('#grammer0').removeAttr("checked");
      }
      if(voc=="y"){
        $('#vocabulary0').attr("checked","checked");
      } else{
        $('#vocabulary0').removeAttr("checked");
      }
      if(pron=="y"){
        $('#pronunciation0').attr("checked","checked");
      } else{
        $('#pronunciation0').removeAttr("checked");
      }
      if(spell=="y"){
        $('#spelling0').attr("checked","checked");
      } else{
        $('#spelling0').removeAttr("checked");
      }
      if(pres=="y"){
        $('#presentation0').attr("checked","checked");
      } else{
        $('#presentation0').removeAttr("checked");
      }
      if(speak=="y"){
        $('#speaking0').attr("checked","checked");
      } else{
        $('#speaking0').removeAttr("checked");
      }
      if(list=="y"){
        $('#listening0').attr("checked","checked");
      } else{
        $('#listening0').removeAttr("checked");
      }
      if(writ=="y"){
        $('#writing0').attr("checked","checked");
      } else{
        $('#writing0').removeAttr("checked");
      }
      if(read=="y"){
        $('#reading0').attr("checked","checked");
      } else{
        $('#reading0').removeAttr("checked");
      }
      $('#btnsubmit').attr('hidden',true);
		$('#btnupdate').removeAttr('hidden');
		$('#btnsubmit').attr('disabled',true);
  }
  function viewdata(id,book,chap,exerno,nm,gra,voc,pron,spell,pres,speak,list,writ,read) {
      $('#ttId').val(id);
      $('#book').val(book);

      chapList(book);// get chapters from book
      $('#chap').val(chap);
      $('#num_div').hide();
      $('#exerno').val(exerno);
      $('#exer_name0').val(nm);
      if(gra=="y"){
        $('#grammer0').attr("checked","checked");
      } else{
        $('#grammer0').removeAttr("checked");
      }
      if(voc=="y"){
        $('#vocabulary0').attr("checked","checked");
      } else{
        $('#vocabulary0').removeAttr("checked");
      }
      if(pron=="y"){
        $('#pronunciation0').attr("checked","checked");
      } else{
        $('#pronunciation0').removeAttr("checked");
      }
      if(spell=="y"){
        $('#spelling0').attr("checked","checked");
      } else{
        $('#spelling0').removeAttr("checked");
      }
      if(pres=="y"){
        $('#presentation0').attr("checked","checked");
      } else{
        $('#presentation0').removeAttr("checked");
      }
      if(speak=="y"){
        $('#speaking').attr("checked","checked");
      } else{
        $('#speaking').removeAttr("checked");
      }
      if(list=="y"){
        $('#listening0').attr("checked","checked");
      } else{
        $('#listening0').removeAttr("checked");
      }
      if(writ=="y"){
        $('#writing0').attr("checked","checked");
      } else{
        $('#writing0').removeAttr("checked");
      }
      if(read=="y"){
        $('#reading0').attr("checked","checked");
      } else{
        $('#reading0').removeAttr("checked");
      }
      $('#btnsubmit').attr('hidden',true);
            $('#btnupdate').attr('hidden',true);
      $('#btnsubmit').attr('disabled',true);

    }

  function create_field()
  {
    var field_count=$('#field_cnt').val();
    
    
    $('#fields_div').append('<div id="field_div_'+field_count+'"><div class="mb-3" ><label class="form-label" for="basic-default-fullname"> Exercise Name</label><a href="javascript:remove_field(\'field_div_'+field_count+'\')" class="text-right"><i class="bx bxs-message-square-minus bx-sm"></i></a><input type="text" class="form-control" name="exer_name'+field_count+'" id="exer_name'+field_count+'" required /></div><div class="mb-3"><input type="checkbox" name="grammer'+field_count+'" id="grammer'+field_count+'"/> Grammar <input type="checkbox" name="vocabulary'+field_count+'" id="vocabulary'+field_count+'"/> Vocabulary <input type="checkbox" name="pronunciation'+field_count+'" id="pronunciation'+field_count+'"/> Pronunciation <input type="checkbox" name="spelling'+field_count+'" id="spelling'+field_count+'"/> Spelling <input type="checkbox" name="presentation'+field_count+'" id="presentation'+field_count+'"/> Presentation <input type="checkbox" name="speaking'+field_count+'" id="speaking'+field_count+'"/> Speaking <input type="checkbox" name="listening'+field_count+'" id="listening'+field_count+'"/> Listening <input type="checkbox" name="writing'+field_count+'" id="writing'+field_count+'"/> Writing <input type="checkbox" name="reading'+field_count+'" id="reading'+field_count+'"/> Reading </div></div>');

   
    $('#field_cnt').val(parseInt(field_count)+1);

    //.remove() to remove div

  }

  function remove_field(div)
  {
    $('#'+div).remove();
  }


  /*function create_field(num)
  {
    var field_count=$('#field_cnt').val();
    console.log("num="+num);
    console.log("cnt="+field_count);
    if(field_count>1 && field_count<num)
    {
        num=parseInt(num-field_count)+parseInt(1);
        console.log("in if num="+num);
        i_cnt=parseInt(field_count);
        console.log("in if icnt="+i_cnt);
    }
    else
    {
      var i_cnt=1;
    }
    
    for(i=i_cnt;i<num;i++)
    {
      console.log("for called");
      $('#fields_div').append('<div class="mb-3"><label class="form-label" for="basic-default-fullname"> Exercise Name</label><input type="text" class="form-control" name="exer_name'+i+'" id="exer_name'+i+'" required /></div><div class="mb-3"><input type="checkbox" name="grammer'+i+'" id="grammer'+i+'"/> Grammer <input type="checkbox" name="vocabulary'+i+'" id="vocabulary'+i+'"/> Vocabulary <input type="checkbox" name="pronunciation'+i+'" id="pronunciation'+i+'"/> Pronunciation <input type="checkbox" name="spelling'+i+'" id="spelling'+i+'"/> Spelling <input type="checkbox" name="presentation'+i+'" id="presentation'+i+'"/> Presentation <input type="checkbox" name="speaking'+i+'" id="speaking'+i+'"/> Speaking <input type="checkbox" name="listening'+i+'" id="listening'+i+'"/> Listening <input type="checkbox" name="writing'+i+'" id="writing'+i+'"/> Writing <input type="checkbox" name="reading'+i+'" id="reading'+i+'"/> Reading </div>');

    }
    $('#field_cnt').val(num);


  }*/
</script>
<?php 
include("footer.php");
?>