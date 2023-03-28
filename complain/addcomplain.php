<?php 
include('../header.php');
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
$c_month = '';
$c_year = '';
$c_userid = '';
$image_before_sol = "";
$branch_id = '';
$title = $_data['text_1'];
$button_text = $_data['save_button_text'];
$successful_msg = $_data['text_8'];
$form_url = WEB_URL . "complain/addcomplain.php";
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
	if(isset($_POST['hdn']) && $_POST['hdn'] == '0'){
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
	    
		$sql = "INSERT INTO tbl_add_complain(c_title,c_description, c_date, c_month,c_year,c_userid,branch_id,image_before_sol) values('$_POST[txtCTitle]','$_POST[txtCDescription]','$_POST[txtCDate]',$xmonth,$xyear,'".(int)$_SESSION['objLogin']['aid']."','" . $_SESSION['objLogin']['branch_id'] . "','".$URL_IMAGE."')";
		//echo $sql;
		//die();
		mysqli_query($link,$sql);
		mysqli_close($link);
		$url = WEB_URL . 'complain/complainlist.php?m=add';
		header("Location: $url");
		
	}else{
	    
        if(isset($_POST['image_before_work']) && !empty($_POST['image_before_work'])){
            $fileImg_parts = explode(";base64,", str_replace(' ','+',$_POST['image_before_work']));
            $image_type_aux = explode("image/", $fileImg_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($fileImg_parts[1]);
            $filename = NewGuid() .'.'. $image_type;
            $results = ROOT_PATH . '/img/upload/' . $filename;
            $upload_file = file_put_contents($results, $image_base64);
            $URL_IMAGE = WEB_URL . 'img/upload/' . $filename;
            $sql_img = " , image_before_sol = '".$URL_IMAGE."' ";
		 }else {
		     $sql_img = "";
		 }	    
	    
		$sql = "UPDATE `tbl_add_complain` SET `c_title`='".$_POST['txtCTitle']."',`c_description`='".$_POST['txtCDescription']."',`c_date`='".$_POST['txtCDate']."' $sql_img WHERE complain_id='".$_GET['id']."'";
		//echo $sql;
		//die();
		mysqli_query($link,$sql);
		$url = WEB_URL . 'complain/complainlist.php?m=up';
		header("Location: $url");
	}
	$success = "block";
}

if(isset($_GET['id']) && $_GET['id'] != ''){
	$result = mysqli_query($link,"SELECT * FROM tbl_add_complain where complain_id = '" . $_GET['id'] . "'");
	if($row = mysqli_fetch_array($result)){
		$c_title = $row['c_title'];
		$c_description = $row['c_description'];
		$c_date = $row['c_date'];
		$hdnid = $_GET['id'];
		$title = $_data['text_1_1'];
		$button_text = $_data['update_button_text'];
		$image_before_sol = $row['image_before_sol'];
		$successful_msg = $_data['text_9'];
		$form_url = WEB_URL . "complain/addcomplain.php?id=".$_GET['id'];
	}
	
	//mysqli_close($link);

}	
?>
<!-- Content Header (Page header) -->

<section class="content-header">
  <h1><?php echo $title;?></h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo WEB_URL?>dashboard.php"><i class="fa fa-dashboard"></i><?php echo $_data['home_breadcam'];?></a></li>
    <li class="active"><?php echo $_data['text_2'];?></li>
    <li class="active"><?php echo $title;?></li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
<!-- Full Width boxes (Stat box) -->
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
                <label for="Prsnttxtarea">Image Before Work :</label>
                <?php //if(!empty($image_after_work)){ ?>
                <a target="_blank" href="<?php echo !empty($image_before_sol) ? $image_before_sol : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png'; ?>">
                    <img class="form-control output" src="<?php echo !empty($image_before_sol) ? $image_before_sol : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png' ; ?>" style="height:100px;width:100px;"/>
                </a>
                <?php //} ?>
                <input type="hidden" name="img_exist" value="<?php echo $image_before_sol; ?>" />
                <input type="hidden" id="image_before_work" name="image_before_work" value="" />
             <div class="form-group">
                 <span class="btn btn-file btn btn-default"><?php echo !empty($_data['upload_image']) ? $_data['upload_image'] : '' ;?>
                <input type="file" name="uploaded_file" onchange="loadFile_1(event,<?php echo $hdnid; ?>)" />
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
            <a class="btn btn-warning" href="<?php echo WEB_URL; ?>complain/complainlist.php"><i class="fa fa-reply"></i> <?php echo $_data['back_text'];?></a> </div>
        </div>
        <input type="hidden" value="<?php echo $hdnid; ?>" name="hdn"/>
      </form>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
</div>
<!-- /.row -->
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
