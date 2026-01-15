<?php

include('../header.php');
include('../utility/common.php');
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_add_bill.php');
if(!isset($_SESSION['objLogin'])){
	header("Location: " . WEB_URL . "logout.php");
	die();
}
$success = "none";
$bill_type = '';
$bill_date = '';
$bill_month = '';
$bill_year = '';
$total_amount = '';
$deposit_bank_name = '';
$bill_details = '';
$branch_id = '';
$title = $_data['text_1'];
$button_text = $_data['save_button_text'];
$successful_msg = $_data['text_15'];
$form_url = WEB_URL . "bill/add_bill.php";
$id="";
$hdnid="0";

if(isset($_POST['txtTotalAmount'])){
	if(isset($_POST['hdn']) && $_POST['hdn'] == '0'){
		$sql = "INSERT INTO tbl_add_bill(total_amount,deposit_bank_name,bill_details,branch_id) values('$_POST[txtTotalAmount]','$_POST[txtDepositBankName]','$_POST[txtBillDetails]','" . $_SESSION['objLogin']['branch_id'] . "')";
		mysqli_query($link,$sql);
		//mysqli_close($link);
		$url = WEB_URL . 'bill/bill_list.php?m=add';
		header("Location: $url");
	}else{
		$sql = "UPDATE `tbl_add_bill` SET `total_amount`='".$_POST['txtTotalAmount']."',`deposit_bank_name`='".$_POST['txtDepositBankName']."',`bill_details`='".$_POST['txtBillDetails']."' WHERE bill_id='".$_GET['id']."'";
		mysqli_query($link,$sql);
		$url = WEB_URL . 'bill/bill_list.php?m=up';
		header("Location: $url");
	}
	$success = "block";
}

if(isset($_GET['id']) && $_GET['id'] != ''){
	$result = mysqli_query($link,"SELECT * FROM tbl_add_bill where bill_id = '" . $_GET['id'] . "'");
	if($row = mysqli_fetch_array($result)){
		$total_amount = $row['total_amount'];
		$deposit_bank_name = $row['deposit_bank_name'];
		$bill_details = $row['bill_details'];
		$hdnid = $_GET['id'];
		$title = $_data['text_1_1'];
		$button_text = $_data['update_button_text'];
		$successful_msg = $_data['text_16'];
		$form_url = WEB_URL . "bill/add_bill.php?id=".$_GET['id'];
	}
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
    <div align="right" style="margin-bottom:1%;"> </div>
    <div class="box box-success">
      <div class="box-header">
        <h3 class="box-title"><?php echo $_data['text_4'];?></h3>
      </div>
      <form onSubmit="return validateMe();" action="<?php echo $form_url; ?>" method="post" enctype="multipart/form-data">
        <div class="box-body row">
          <div class="form-group col-md-6">
            <label for="txtTotalAmount"><span class="errorStar">*</span> <?php echo $_data['text_12'];?> :</label>
            <div class="input-group">
              <input type="text" name="txtTotalAmount" value="<?php echo $total_amount;?>" id="txtTotalAmount" class="form-control"/>
              <div class="input-group-addon"><?php echo CURRENCY;?></div>
            </div>
          </div>
          <div class="form-group col-md-6">
            <label for="txtDepositBankName"><span class="errorStar">*</span> <?php echo $_data['text_13'];?> :</label>
            <input type="text" name="txtDepositBankName" value="<?php echo $deposit_bank_name;?>" id="txtDepositBankName" class="form-control"/>
          </div>
          <div class="form-group col-md-12">
            <label for="txtBillDetails"><span class="errorStar">*</span> <?php echo $_data['text_14'];?> :</label>
            <textarea name="txtBillDetails" id="txtBillDetails" class="form-control"><?php echo $bill_details;?></textarea>
          </div>
        </div>
		<div class="box-footer">
          <div class="form-group pull-right">
            <button type="submit" name="button" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php echo $button_text; ?></button>
            <a class="btn btn-warning" href="<?php echo WEB_URL; ?>bill/bill_list.php"><i class="fa fa-reply"></i> <?php echo $_data['back_text'];?></a> </div>
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
function validateMe(){


   if($("#txtTotalAmount").val() == ''){
		alert("<?php echo $_data['required_5']; ?>");
		$("#txtTotalAmount").focus();
		return false;
	}
	else if($("#txtDepositBankName").val() == ''){
		alert("<?php echo $_data['required_6']; ?>");
		$("#txtDepositBankName").focus();
		return false;
	}
	else if($("#txtBillDetails").val() == ''){
		alert("<?php echo $_data['required_7']; ?>");
		$("#txtBillDetails").focus();
		return false;
	}
	else{
		return true;
	}
}
</script>
<?php include('../footer.php'); ?>
