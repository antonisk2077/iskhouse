<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
if(isset($_SESSION['login_type']) && (int)$_SESSION['login_type'] == 3){
	include('../header_emp.php');
} else {
	include('../header.php');
}
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_add_maintenance_cost.php');
if(!isset($_SESSION['objLogin'])){
	header("Location: " . WEB_URL . "logout.php");
	die();
}
$success = "none";
$m_date = '';
$m_qty = 1;
$m_amount = '0.00';
$m_details = '';
$m_location = '';
$branch_id = '';
$m_transfer_to = '';
$title = $_data['add_title_text'];
$button_text = $_data['save_button_text'];
$successful_msg = $_data['add_msg'];
$form_url = WEB_URL . "maintenance/add_maintenance_cost.php";
$id="";
$hdnid="0";
$is_employee = isset($_SESSION['login_type']) && $_SESSION['login_type'] == '3';
$is_super_admin = isset($_SESSION['login_type']) && $_SESSION['login_type'] == '5';
function resolveBranchId($link, $is_super_admin, $is_employee){
	if($is_super_admin){
		return 0;
	}
	if(!empty($_SESSION['objLogin']['branch_id'])){
		return (int)$_SESSION['objLogin']['branch_id'];
	}
	if($is_employee && !empty($_SESSION['objLogin']['eid'])){
		$result_branch = mysqli_query($link,"SELECT branch_id FROM tbl_add_employee WHERE eid = " . (int)$_SESSION['objLogin']['eid']);
		if($row_branch = mysqli_fetch_array($result_branch)){
			return (int)$row_branch['branch_id'];
		}
	}
	return 0;
}
if(!$is_super_admin){
	$branch_id = resolveBranchId($link, $is_super_admin, $is_employee);
}

function deriveMonthYear($date_value) {
	$date_value = trim((string)$date_value);
	if($date_value === ''){
		return array(0, 0);
	}
	$date_obj = DateTime::createFromFormat('d/m/Y', $date_value);
	if(!$date_obj){
		$date_obj = DateTime::createFromFormat('Y-m-d', $date_value);
	}
	if(!$date_obj){
		try {
			$date_obj = new DateTime($date_value);
		} catch (Exception $e) {
			return array(0, 0);
		}
	}
	return array((int)$date_obj->format('n'), (int)$date_obj->format('Y'));
}

if(!$is_employee){
	header("Location: " . WEB_URL . "maintenance/maintenance_cost_list.php");
	die();
}

if(isset($_POST['txtMDate'])){
	$location_value = isset($_POST['ddlLocation']) ? trim($_POST['ddlLocation']) : '';
	if($location_value === 'other'){
		$location_value = isset($_POST['txtLocationOther']) ? trim($_POST['txtLocationOther']) : '';
	}
	$branch_value = isset($_POST['ddlBranch']) ? (int)$_POST['ddlBranch'] : 0;
	if($branch_value == 0 && !empty($_SESSION['objLogin']['branch_id'])){
		$branch_value = (int)$_SESSION['objLogin']['branch_id'];
	}
	if($branch_value == 0 && $location_value !== '' && $location_value !== 'other'){
		$result_unit_branch = mysqli_query($link,"SELECT branch_id FROM tbl_add_unit WHERE unit_no = '" . mysqli_real_escape_string($link, $location_value) . "' LIMIT 1");
		if($row_unit_branch = mysqli_fetch_array($result_unit_branch)){
			$branch_value = (int)$row_unit_branch['branch_id'];
		}
	}
	$m_qty_value = isset($_POST['txtMQty']) ? (int)$_POST['txtMQty'] : 0;
	list($month_value, $year_value) = deriveMonthYear($_POST['txtMDate']);
	if(isset($_POST['hdn']) && $_POST['hdn'] == '0'){
		$created_by = $is_employee ? (int)$_SESSION['objLogin']['eid'] : 0;
		$transfer_to = isset($_POST['txtMTransferTo']) ? mysqli_real_escape_string($link, $_POST['txtMTransferTo']) : '';
		$sql = "INSERT INTO tbl_add_maintenance_cost(m_title, m_location, m_qty, m_date, xmonth, xyear, m_amount, m_details, m_status, m_created_by, m_transfer_to, branch_id) values('".$location_value."','".$location_value."','".$m_qty_value."','$_POST[txtMDate]','".$month_value."','".$year_value."','$_POST[txtMAmount]','$_POST[txtMDetails]','submitted','" . $created_by . "','".$transfer_to."','" . $branch_value . "')";
		mysqli_query($link,$sql);
		mysqli_close($link);
		$url = WEB_URL . 'maintenance/maintenance_cost_list.php?m=add';
		header("Location: $url");
	} else {
		$transfer_to = isset($_POST['txtMTransferTo']) ? mysqli_real_escape_string($link, $_POST['txtMTransferTo']) : '';
		$sql = "UPDATE `tbl_add_maintenance_cost` SET `m_title`='".$location_value."',`m_location`='".$location_value."',`m_qty`='".$m_qty_value."',`m_date`='".$_POST['txtMDate']."',`xmonth`='".$month_value."',`xyear`='".$year_value."',`m_amount`='$_POST[txtMAmount]',`m_details`='$_POST[txtMDetails]',`m_transfer_to`='".$transfer_to."',`branch_id`='".$branch_value."' WHERE mcid='".$_GET['id']."'";
		mysqli_query($link,$sql);
		$url = WEB_URL . 'maintenance/maintenance_cost_list.php?m=up';
		header("Location: $url");
	}
	$success = "block";
}

if(isset($_GET['id']) && $_GET['id'] != ''){
	$result = mysqli_query($link,"SELECT * FROM tbl_add_maintenance_cost where mcid = '" . $_GET['id'] . "'");
	if($row = mysqli_fetch_array($result)){
		if(isset($row['m_status']) && $row['m_status'] !== 'submitted'){
			header("Location: " . WEB_URL . "maintenance/maintenance_cost_list.php");
			die();
		}
		$m_location = $row['m_location'];
		$m_date = $row['m_date'];
		$m_qty = (int)$row['m_qty'];
		$m_amount = $row['m_amount'];
		$m_details = $row['m_details'];
		$m_transfer_to = $row['m_transfer_to'];
		$branch_id = $row['branch_id'];
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
		  $result_loc = mysqli_query($link,"SELECT u.unit_no, u.branch_id, b.branch_name FROM tbl_add_unit u left join tblbranch b on b.branch_id = u.branch_id ORDER BY u.unit_no ASC");
		  while($row_loc = mysqli_fetch_array($result_loc)){
		  	$unit_options[] = $row_loc;
		  }
		  $is_other_location = ($m_location !== '' && !in_array($m_location, array_column($unit_options, 'unit_no'), true));
		  ?>
          <div class="form-group col-md-12">
            <label for="txtMDate"><span class="errorStar">*</span> <?php echo $_data['date'];?> :</label>
            <input type="text" name="txtMDate" value="<?php echo $m_date;?>" id="txtMDate" class="form-control datepicker"/>
          </div>
          <div class="form-group col-md-6">
            <label for="ddlBranch"><span class="errorStar">*</span> <?php echo $_data['text_9'];?> :</label>
            <select name="ddlBranch" id="ddlBranch" class="form-control">
              <option value="">--<?php echo $_data['text_10'];?>--</option>
              <?php 
				$result_branch = mysqli_query($link,"SELECT * FROM tblbranch WHERE b_status = 1 order by branch_name ASC");
				while($row_branch = mysqli_fetch_array($result_branch)){?>
              	<option <?php if($branch_id == $row_branch['branch_id']){echo 'selected';}?> value="<?php echo $row_branch['branch_id'];?>"><?php echo $row_branch['branch_name'];?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group col-md-6">
            <label for="ddlLocation"><span class="errorStar">*</span> <?php echo $_data['text_4'];?> :</label>
            <select name="ddlLocation" id="ddlLocation" class="form-control">
              <option value="">--<?php echo $_data['text_5'];?>--</option>
              <?php foreach($unit_options as $unit_row){ ?>
                <option data-branch="<?php echo (int)$unit_row['branch_id'];?>" <?php if(!$is_other_location && $m_location == $unit_row['unit_no']){echo 'selected';}?> value="<?php echo $unit_row['unit_no'];?>"><?php echo $unit_row['unit_no'];?></option>
              <?php } ?>
              <option <?php if($is_other_location){echo 'selected';}?> value="other"><?php echo $_data['text_6'];?></option>
            </select>
          </div>
          <div class="form-group col-md-12" id="locationOtherWrap" style="display:<?php echo $is_other_location ? 'block' : 'none'; ?>;">
            <label for="txtLocationOther"><span class="errorStar">*</span> <?php echo $_data['text_7'];?> :</label>
            <input type="text" name="txtLocationOther" value="<?php echo $is_other_location ? $m_location : '';?>" id="txtLocationOther" class="form-control" />
          </div>
          <div class="form-group col-md-6">
            <label for="txtMQty"><span class="errorStar">*</span> <?php echo $_data['text_8'];?> :</label>
            <input type="number" name="txtMQty" value="<?php echo $m_qty;?>" id="txtMQty" class="form-control" min="1" />
          </div>
          <div class="form-group col-md-6">
            <label for="txtMTransferTo"><span class="errorStar">*</span> <?php echo $_data['text_11'];?> :</label>
            <input type="text" name="txtMTransferTo" value="<?php echo $m_transfer_to;?>" id="txtMTransferTo" class="form-control" />
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
		else if($("#ddlBranch").val() == ''){
			alert("<?php echo $_data['v7']; ?>");
			$("#ddlBranch").focus();
			return false;
		}
		else if($("#ddlLocation").val() == ''){
			alert("<?php echo $_data['v2']; ?>");
			$("#ddlLocation").focus();
			return false;
		}
		else if($("#ddlLocation").val() == 'other' && $("#txtLocationOther").val() == ''){
			alert("<?php echo $_data['v6']; ?>");
			$("#txtLocationOther").focus();
			return false;
		}
		else if($("#txtMQty").val() == '' || parseInt($("#txtMQty").val(), 10) <= 0){
			alert("<?php echo $_data['v3']; ?>");
			$("#txtMQty").focus();
			return false;
		}
		else if($("#txtMTransferTo").val() == ''){
			alert("<?php echo $_data['v8']; ?>");
			$("#txtMTransferTo").focus();
			return false;
		}
		else if($("#txtMAmount").val() == ''){
			alert("<?php echo $_data['v4']; ?>");
			$("#txtMAmount").focus();
			return false;
		}
		else if($("#txtMDetails").val() == ''){
			alert("<?php echo $_data['v5']; ?>");
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

	$("#ddlBranch").change(function(){
		var branchId = $(this).val();
		$("#ddlLocation option").each(function(){
			var optionBranch = $(this).data('branch');
			if(!optionBranch){
				$(this).show();
				return;
			}
			if(branchId === "" || optionBranch == branchId){
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		$("#ddlLocation").val('');
		$("#locationOtherWrap").hide();
	});

	$("#ddlBranch").trigger('change');
</script>
<?php include('../footer.php'); ?>
