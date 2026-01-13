<?php 
include('../header.php');
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_add_maintenance_cost.php');
if(!isset($_SESSION['objLogin'])){
	header("Location: " . WEB_URL . "logout.php");
	die();
}
$success = "none";
$m_title = '';
$m_date = '';
$m_amount = '0.00';
$m_details = '';
$m_location = '';
$m_month = 0;
$m_year = 0;
$branch_id = '';
$title = $_data['add_title_text'];
$button_text = $_data['save_button_text'];
$successful_msg = $_data['add_msg'];
$form_url = WEB_URL . "maintenance/add_maintenance_cost.php";
$id="";
$hdnid="0";

if(isset($_POST['txtMTitle'])){
	$location_value = isset($_POST['ddlLocation']) ? trim($_POST['ddlLocation']) : '';
	if($location_value === 'other'){
		$location_value = isset($_POST['txtLocationOther']) ? trim($_POST['txtLocationOther']) : '';
	}
	if(isset($_POST['hdn']) && $_POST['hdn'] == '0'){
		$sql = "INSERT INTO tbl_add_maintenance_cost(m_title, m_location, m_date, xmonth, xyear, m_amount, m_details,branch_id) values('$_POST[txtMTitle]','".$location_value."','$_POST[txtMDate]','$_POST[ddlMonth]','$_POST[ddlYear]','$_POST[txtMAmount]','$_POST[txtMDetails]','" . $_SESSION['objLogin']['branch_id'] . "')";
		mysqli_query($link,$sql);
		mysqli_close($link);
		$url = WEB_URL . 'maintenance/maintenance_cost_list.php?m=add';
		header("Location: $url");
	} else {
		$sql = "UPDATE `tbl_add_maintenance_cost` SET `m_title`='".$_POST['txtMTitle']."',`m_location`='".$location_value."',`m_date`='".$_POST['txtMDate']."',`xmonth`='".$_POST['ddlMonth']."',`xyear`='".$_POST['ddlYear']."',`m_amount`='".$_POST['txtMAmount']."',`m_details`='".$_POST['txtMDetails']."' WHERE mcid='".$_GET['id']."'";
		mysqli_query($link,$sql);
		$url = WEB_URL . 'maintenance/maintenance_cost_list.php?m=up';
		header("Location: $url");
	}
	$success = "block";
}

if(isset($_GET['id']) && $_GET['id'] != ''){
	$result = mysqli_query($link,"SELECT * FROM tbl_add_maintenance_cost where mcid = '" . $_GET['id'] . "'");
	if($row = mysqli_fetch_array($result)){
		$m_title = $row['m_title'];
		$m_location = $row['m_location'];
		$m_date = $row['m_date'];
		$m_amount = $row['m_amount'];
		$m_details = $row['m_details'];
		$m_month = $row['xmonth'];
		$m_year = $row['xyear'];
		$hdnid = $_GET['id'];
		$title = $_data['update_title_text'];
		$button_text = $_data['update_button_text'];
		$successful_msg = $_data['update_msg'];
		$form_url = WEB_URL . "maintenance/add_maintenance_cost.php?id=".$_GET['id'];
	}
}
	
?>
<!-- Content Header (Page header) -->

<section class="content-header">
  <h1><?php echo $title;?></h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo WEB_URL?>/dashboard.php"><i class="fa fa-dashboard"></i><?php echo $_data['home_breadcam'];?></a></li>
    <li class="active"><?php echo $_data['maintenance_cost'];?></li>
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
        <h3 class="box-title"><?php echo $_data['m_cost_entry_form'];?></h3>
      </div>
      <form onSubmit="return validateMe();" action="<?php echo $form_url; ?>" method="post" enctype="multipart/form-data">
        <div class="box-body row">
          <?php
		  $unit_options = array();
		  $result_loc = mysqli_query($link,"SELECT unit_no FROM tbl_add_unit WHERE branch_id = " . (int)$_SESSION['objLogin']['branch_id'] . " ORDER BY unit_no ASC");
		  while($row_loc = mysqli_fetch_array($result_loc)){
		  	$unit_options[] = $row_loc['unit_no'];
		  }
		  $is_other_location = ($m_location !== '' && !in_array($m_location, $unit_options, true));
		  ?>
          <div class="form-group col-md-12">
            <label for="txtMDate"><span class="errorStar">*</span> <?php echo $_data['date'];?> :</label>
            <input type="text" name="txtMDate" value="<?php echo $m_date;?>" id="txtMDate" class="form-control datepicker"/>
          </div>
          <div class="form-group col-md-6">
            <label for="ddlMonth"><span class="errorStar">*</span> <?php echo $_data['month'];?> :</label>
            <select name="ddlMonth" id="ddlMonth" class="form-control">
              <option value="">--<?php echo $_data['select_month'];?>--</option>
              <?php 
				$result_unit = mysqli_query($link,"SELECT * FROM tbl_add_month_setup order by m_id ASC");
				while($row_unit = mysqli_fetch_array($result_unit)){?>
              	<option <?php if($m_month == $row_unit['m_id']){echo 'selected';}?> value="<?php echo $row_unit['m_id'];?>"><?php echo $row_unit['month_name'];?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label for="ddlYear"><span class="errorStar">*</span> <?php echo $_data['year'];?> :</label>
            <select name="ddlYear" id="ddlYear" class="form-control">
              <option value="">--<?php echo $_data['select_year'];?>--</option>
              <?php for($i=2023;$i<=date('Y');$i++){?>
              <option <?php if($m_year == $i){echo 'selected';}?> value="<?php echo $i;?>"><?php echo $i;?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label for="txtMTitle"><span class="errorStar">*</span> <?php echo $_data['text_1'];?> :</label>
            <input type="text" name="txtMTitle" value="<?php echo $m_title;?>" id="txtMTitle" class="form-control" />
          </div>
          <div class="form-group col-md-6">
            <label for="ddlLocation"><span class="errorStar">*</span> <?php echo $_data['text_4'];?> :</label>
            <select name="ddlLocation" id="ddlLocation" class="form-control">
              <option value="">--<?php echo $_data['text_5'];?>--</option>
              <?php foreach($unit_options as $unit_no){ ?>
                <option <?php if(!$is_other_location && $m_location == $unit_no){echo 'selected';}?> value="<?php echo $unit_no;?>"><?php echo $unit_no;?></option>
              <?php } ?>
              <option <?php if($is_other_location){echo 'selected';}?> value="other"><?php echo $_data['text_6'];?></option>
            </select>
          </div>
          <div class="form-group col-md-12" id="locationOtherWrap" style="display:<?php echo $is_other_location ? 'block' : 'none'; ?>;">
            <label for="txtLocationOther"><span class="errorStar">*</span> <?php echo $_data['text_7'];?> :</label>
            <input type="text" name="txtLocationOther" value="<?php echo $is_other_location ? $m_location : '';?>" id="txtLocationOther" class="form-control" />
          </div>
          <div class="form-group col-md-6">
            <label for="txtMAmount"><span class="errorStar">*</span> <?php echo $_data['text_2'];?> :</label>
            <div class="input-group">
              <input type="text" name="txtMAmount" value="<?php echo $m_amount;?>" id="txtMAmount" class="form-control" />
              <div class="input-group-addon"> <?php echo CURRENCY;?> </div>
            </div>
          </div>
          <div class="form-group col-md-12">
            <label for="txtMDetails"><span class="errorStar">*</span> <?php echo $_data['text_3'];?> :</label>
            <textarea name="txtMDetails" id="txtMDetails" class="form-control"><?php echo $m_details;?></textarea>
          </div>
        </div>
		<div class="box-footer">
          <div class="form-group pull-right">
            <button type="submit" name="button" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php echo $button_text; ?></button>
            <a class="btn btn-warning" href="<?php echo WEB_URL; ?>maintenance/maintenance_cost_list.php"><i class="fa fa-reply"></i> <?php echo $_data['back_text'];?></a> </div>
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
	function validateMe() {
		if($("#txtMDate").val() == ''){
			alert("<?php echo $_data['v1']; ?>");
			$("#txtMDate").focus();
			return false;
		}
		else if($("#ddlMonth").val() == ''){
			alert("<?php echo $_data['v2']; ?>");
			$("#ddlMonth").focus();
			return false;
		}
		else if($("#ddlYear").val() == ''){
			alert("<?php echo $_data['v3']; ?>");
			$("#ddlYear").focus();
			return false;
		}
		else if($("#txtMTitle").val() == ''){
			alert("<?php echo $_data['v4']; ?>");
			$("#txtMTitle").focus();
			return false;
		}
		else if($("#ddlLocation").val() == ''){
			alert("<?php echo $_data['v7']; ?>");
			$("#ddlLocation").focus();
			return false;
		}
		else if($("#ddlLocation").val() == 'other' && $("#txtLocationOther").val() == ''){
			alert("<?php echo $_data['v8']; ?>");
			$("#txtLocationOther").focus();
			return false;
		}
		else if($("#txtMAmount").val() == ''){
			alert("<?php echo $_data['v5']; ?>");
			$("#txtMAmount").focus();
			return false;
		}
		else if($("#txtMDetails").val() == ''){
			alert("<?php echo $_data['v6']; ?>");
			$("#txtMDetails").focus();
			return false;
		}
		else{
			return true;
		}
	}

	$("#ddlLocation").change(function(){
		if($(this).val() === 'other'){
			$("#locationOtherWrap").show();
		} else {
			$("#locationOtherWrap").hide();
		}
	});
</script>
<?php include('../footer.php'); ?>
