<?php 
include('../header_emp.php');
include('../utility/common.php');
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_add_complain.php');
if(!isset($_SESSION['objLogin'])){
	header("Location: " . WEB_URL . "logout.php");
	die();
}
$success = "none";
$c_title = '';
$c_description = '';
$c_date = '';
$c_unit_no = 0;
$image_before_sol = "";
$title = $_data['text_2'];
$button_text = $_data['save_button_text'];
$successful_msg = $_data['text_8'];
$form_url = WEB_URL . "e_dashboard/addcomplain.php";
$id="";
$hdnid="0";

function NewGuid() {
    $s = strtoupper(md5(uniqid(rand(),true)));
    $guidText =
        substr($s,0,8) . '-' .
        substr($s,8,4) . '-' .
        substr($s,12,4). '-' .
        substr($s,16,4). '-' .
        substr($s,20);
    return $guidText;
}

if(isset($_POST['txtCTitle'])){
	$xmonth = date('m');
	$xyear = date('Y');
	$txt_title = isset($_POST['txtCTitle']) ? mysqli_real_escape_string($link, $_POST['txtCTitle']) : '';
	$txt_description = isset($_POST['txtCDescription']) ? mysqli_real_escape_string($link, $_POST['txtCDescription']) : '';
	$txt_date = isset($_POST['txtCDate']) ? mysqli_real_escape_string($link, $_POST['txtCDate']) : '';
	$unit_no = isset($_POST['ddlUnitNo']) ? (int)$_POST['ddlUnitNo'] : 0;
	$URL_IMAGE = "";
	if(isset($_POST['image_before_work']) && !empty($_POST['image_before_work'])){
		$fileImg_parts = explode(";base64,", str_replace(' ','+',$_POST['image_before_work']));
		$image_type_aux = explode("image/", $fileImg_parts[0]);
		$image_type = $image_type_aux[1];
		$image_base64 = base64_decode($fileImg_parts[1]);
		$filename = NewGuid() .'.'. $image_type;
		$results = ROOT_PATH . '/img/upload/' . $filename;
		$upload_file = file_put_contents($results, $image_base64);
		$URL_IMAGE = WEB_URL . 'img/upload/' . $filename;
	}

	if(isset($_POST['hdn']) && $_POST['hdn'] == '0'){
		$sql = "INSERT INTO tbl_add_complain(c_title,c_description,c_date,c_month,c_year,c_userid,c_unit_no,branch_id,job_status,assign_employee_id,solution,image_before_sol,complain_by,person_name,person_email,person_contact) values('".$txt_title."','".$txt_description."','".$txt_date."',".$xmonth.",".$xyear.",'".(int)$_SESSION['objLogin']['eid']."','".$unit_no."','" . $_SESSION['objLogin']['branch_id'] . "',0,'".(int)$_SESSION['objLogin']['eid']."','','".$URL_IMAGE."','employee','" . mysqli_real_escape_string($link, $_SESSION['objLogin']['e_name']) . "','" . mysqli_real_escape_string($link, $_SESSION['objLogin']['e_email']) . "','" . mysqli_real_escape_string($link, $_SESSION['objLogin']['e_contact']) . "')";
		mysqli_query($link,$sql);
		mysqli_close($link);
		$url = WEB_URL . 'e_dashboard/complain.php?m=add';
		header("Location: $url");
	} else {
		$sql_img = "";
		if($URL_IMAGE != ""){
			$sql_img = " , image_before_sol = '".$URL_IMAGE."' ";
		}
		$sql = "UPDATE `tbl_add_complain` SET `c_title`='".$txt_title."',`c_description`='".$txt_description."',`c_date`='".$txt_date."',`c_unit_no`='".$unit_no."' $sql_img WHERE complain_id='".$_GET['id']."' AND assign_employee_id = '".(int)$_SESSION['objLogin']['eid']."' AND branch_id = '" . (int)$_SESSION['objLogin']['branch_id'] . "'";
		mysqli_query($link,$sql);
		$url = WEB_URL . 'e_dashboard/complain.php?m=up';
		header("Location: $url");
	}
	$success = "block";
}

if(isset($_GET['id']) && $_GET['id'] != ''){
	$result = mysqli_query($link,"SELECT * FROM tbl_add_complain where complain_id = '" . $_GET['id'] . "' AND assign_employee_id = '" . (int)$_SESSION['objLogin']['eid'] . "' AND branch_id = '" . (int)$_SESSION['objLogin']['branch_id'] . "'");
	if($row = mysqli_fetch_array($result)){
		$c_title = $row['c_title'];
		$c_description = $row['c_description'];
		$c_date = $row['c_date'];
		$c_unit_no = $row['c_unit_no'];
		$hdnid = $_GET['id'];
		$title = $_data['text_2'];
		$button_text = $_data['update_button_text'];
		$image_before_sol = $row['image_before_sol'];
		$successful_msg = $_data['text_9'];
		$form_url = WEB_URL . "e_dashboard/addcomplain.php?id=".$_GET['id'];
	}
}
?>
<section class="content-header">
  <h1><?php echo $title;?></h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo WEB_URL?>/e_dashboard.php"><i class="fa fa-dashboard"></i><?php echo $_data['home_breadcam'];?></a></li>
    <li class="active"><?php echo $_data['text_2'];?></li>
    <li class="active"><?php echo $title;?></li>
  </ol>
</section>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div align="right" style="margin-bottom:1%;"></div>
    <div class="box box-success">
      <div class="box-header">
        <h3 class="box-title"><?php echo $_data['text_4'];?></h3>
      </div>
      <form onSubmit="return validateMe();" action="<?php echo $form_url; ?>" method="post" enctype="multipart/form-data">
        <div class="box-body">
          <div class="form-group">
            <label for="txtCTitle"><span style="color:red;">*</span> <?php echo $_data['text_5'];?> :</label>
            <input type="text" name="txtCTitle" value="<?php echo $c_title;?>" id="txtCTitle" class="form-control" />
          </div>
          <div class="form-group">
            <label for="txtCDescription"><span style="color:red;">*</span> <?php echo $_data['text_6'];?> :</label>
            <textarea name="txtCDescription" id="txtCDescription" class="form-control"><?php echo $c_description;?></textarea>
          </div>
          <div class="form-group">
            <label for="ddlUnitNo"><span style="color:red;">*</span> <?php echo $_data['text_11'];?> :</label>
            <select name="ddlUnitNo" id="ddlUnitNo" class="form-control">
              <option value="">--<?php echo $_data['text_12'];?>--</option>
              <?php
				  $result_unit = mysqli_query($link,"SELECT uid, unit_no FROM tbl_add_unit WHERE branch_id = " . (int)$_SESSION['objLogin']['branch_id'] . " ORDER BY unit_no ASC");
					while($row_unit = mysqli_fetch_array($result_unit)){?>
              <option <?php if($c_unit_no == $row_unit['uid']){echo 'selected';}?> value="<?php echo $row_unit['uid'];?>"><?php echo $row_unit['unit_no'];?></option>
              <?php } ?>
            </select>
          </div>
        <div class="form-group">
                <label for="Prsnttxtarea">Image Before Work :</label>
                <a target="_blank" href="<?php echo !empty($image_before_sol) ? $image_before_sol : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png'; ?>">
                    <img class="form-control output" src="<?php echo !empty($image_before_sol) ? $image_before_sol : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png' ; ?>" style="height:100px;width:100px;"/>
                </a>
                <input type="hidden" name="img_exist" value="<?php echo $image_before_sol; ?>" />
                <input type="hidden" id="image_before_work" name="image_before_work" value="" />
             <div class="form-group">
                 <span class="btn btn-file btn btn-default"><?php echo !empty($_data['upload_image']) ? $_data['upload_image'] : '' ;?>
                <input type="file" name="uploaded_file" onchange="loadFile_1(event)" />
                </span>
            </div>
        </div>          
          <div class="form-group">
            <label for="txtCDate"><span style="color:red;">*</span> <?php echo $_data['text_7'];?> :</label>
            <input type="text" name="txtCDate" value="<?php echo $c_date;?>" id="txtCDate" class="form-control datepicker"/>
          </div>
        </div>
		<div class="box-footer">
          <div class="form-group pull-right">
            <button type="submit" name="button" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php echo $button_text; ?></button>
            <a class="btn btn-warning" href="<?php echo WEB_URL; ?>e_dashboard/complain.php"><i class="fa fa-reply"></i> <?php echo $_data['back_text'];?></a> </div>
        </div>
        <input type="hidden" value="<?php echo $hdnid; ?>" name="hdn"/>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
var loadFile_1 = function(event) {
	jQuery('.output').attr('src',URL.createObjectURL(event.target.files[0]));

    var selectedfile = event.target.files;
    if (selectedfile.length > 0) {
      var imageFile = selectedfile[0];
      var fileReader = new FileReader();
      fileReader.onload = function(fileLoadedEvent) {
        var srcData = fileLoadedEvent.target.result;
        document.getElementById("image_before_work").value = srcData;
      }
      fileReader.readAsDataURL(imageFile);
    }
};
function validateMe(){
	if($("#txtCTitle").val() == ''){
		alert("<?php echo $_data['r1']; ?>");
		$("#txtCTitle").focus();
		return false;
	}
	else if($("#txtCDescription").val() == ''){
		alert("<?php echo $_data['r2']; ?>");
		$("#txtCDescription").focus();
		return false;
	}
	else if($("#ddlUnitNo").val() == ''){
		alert("<?php echo $_data['r4']; ?>");
		$("#ddlUnitNo").focus();
		return false;
	}
	else if($("#txtCDate").val() == ''){
		alert("<?php echo $_data['r3']; ?>");
		$("#txtCDate").focus();
		return false;
	}
	else{
		return true;
	}
}
</script>
<?php include('../footer.php'); ?>
