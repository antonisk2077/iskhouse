<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
if(isset($_SESSION['login_type']) && (int)$_SESSION['login_type'] == 3){
	include('../header_emp.php');
} else {
	include('../header.php');
}
if(!isset($_SESSION['objLogin'])){
	header("Location: " . WEB_URL . "logout.php");
	die();
}
?>
<?php
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_maintenance_cost_list.php');
$delinfo = 'none';
$addinfo = 'none';
$msg = "";
$is_super_admin = isset($_SESSION['login_type']) && $_SESSION['login_type'] == '5';
$is_admin = isset($_SESSION['login_type']) && $_SESSION['login_type'] == '1';
$is_employee = isset($_SESSION['login_type']) && $_SESSION['login_type'] == '3';
$branch_filter = !empty($_SESSION['objLogin']['branch_id']) ? (int)$_SESSION['objLogin']['branch_id'] : 0;
if($is_super_admin || $is_employee){
	$branch_filter = 0;
}

function normalizeStatus($status){
	$status = trim((string)$status);
	if($status === 'approved' || $status === 'transferred' || $status === 'submitted'){
		return $status;
	}
	return 'submitted';
}

function uploadTransferImage(){
	if((!empty($_FILES["transfer_image"])) && ($_FILES['transfer_image']['error'] == 0)) {
		$filename = basename($_FILES['transfer_image']['name']);
		$ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
		if(($ext == "jpg" && $_FILES["transfer_image"]["type"] == 'image/jpeg') ||
			($ext == "jpeg" && $_FILES["transfer_image"]["type"] == 'image/jpeg') ||
			($ext == "png" && $_FILES["transfer_image"]["type"] == 'image/png') ||
			($ext == "gif" && $_FILES["transfer_image"]["type"] == 'image/gif')) {
			$temp = explode(".", $_FILES["transfer_image"]["name"]);
			$newfilename = rand(10000, 990000) . '.' . end($temp);
			if(move_uploaded_file($_FILES["transfer_image"]["tmp_name"], ROOT_PATH . '/img/upload/' . $newfilename)){
				return $newfilename;
			}
		}
	}
	return '';
}

if(isset($_GET['approve_id']) && $is_super_admin){
	$approve_id = (int)$_GET['approve_id'];
	$approve_sql = "UPDATE tbl_add_maintenance_cost SET m_status='approved' WHERE mcid = " . $approve_id . " AND m_status = 'submitted'";
	if($branch_filter > 0){
		$approve_sql .= " AND branch_id = " . $branch_filter;
	}
	mysqli_query($link,$approve_sql);
	header("Location: " . WEB_URL . "maintenance/maintenance_cost_list.php?m=approve");
	exit;
}

if(isset($_POST['transfer_mcid']) && ($is_admin || $is_super_admin)){
	$transfer_id = (int)$_POST['transfer_mcid'];
	$transfer_image = uploadTransferImage();
	if($transfer_image != ''){
		$transfer_sql = "UPDATE tbl_add_maintenance_cost SET m_status='transferred', transfer_image='".$transfer_image."' WHERE mcid = " . $transfer_id . " AND m_status = 'approved'";
		if($branch_filter > 0){
			$transfer_sql .= " AND branch_id = " . $branch_filter;
		}
		mysqli_query($link,$transfer_sql);
		header("Location: " . WEB_URL . "maintenance/maintenance_cost_list.php?m=transfer");
		exit;
	} else {
		$addinfo = 'block';
		$msg = $_data['transfer_error'];
	}
}
if(isset($_GET['id']) && $_GET['id'] != '' && $_GET['id'] > 0){
	$sqlx= "DELETE FROM `tbl_add_maintenance_cost` WHERE mcid = ".$_GET['id'];
	mysqli_query($link,$sqlx); 
	$delinfo = 'block';
}
if(isset($_GET['m']) && $_GET['m'] == 'add'){
	$addinfo = 'block';
	$msg = $_data['add_msg'];
}
if(isset($_GET['m']) && $_GET['m'] == 'up'){
	$addinfo = 'block';
	$msg = $_data['update_msg'];
}
if(isset($_GET['m']) && $_GET['m'] == 'approve'){
	$addinfo = 'block';
	$msg = $_data['approve_msg'];
}
if(isset($_GET['m']) && $_GET['m'] == 'transfer'){
	$addinfo = 'block';
	$msg = $_data['transfer_msg'];
}
?>
<!-- Content Header (Page header) -->

<section class="content-header">
  <h1><?php echo $_data['add_title_text'];?></h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo WEB_URL?>dashboard.php"><i class="fa fa-dashboard"></i><?php echo $_data['home_breadcam'];?></a></li>
    <li class="active"><?php echo $_data['m_cost'];?></li>
	<li class="active"><?php echo $_data['add_title_text'];?></li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
<!-- Full Width boxes (Stat box) -->
<div class="row">
  <div class="col-xs-12">
    <div id="me" class="alert alert-danger alert-dismissable" style="display:<?php echo $delinfo; ?>">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button"><i class="fa fa-close"></i></button>
      <h4><i class="icon fa fa-ban"></i> <?php echo $_data['delete_text'];?> !</h4>
      <?php echo $_data['delete_msg'];?> </div>
    <div id="you" class="alert alert-success alert-dismissable" style="display:<?php echo $addinfo; ?>">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button"><i class="fa fa-close"></i></button>
      <h4><i class="icon fa fa-check"></i> <?php echo $_data['success'];?>!</h4>
      <?php echo $msg; ?> </div>
    <div align="right" style="margin-bottom:1%;"> <?php if($is_employee){ ?><a class="btn btn-success" data-toggle="tooltip" href="<?php echo WEB_URL; ?>maintenance/add_maintenance_cost.php" data-original-title="<?php echo $_data['add_m_cost'];?>"><i class="fa fa-plus"></i></a><?php } ?> <a class="btn btn-success" data-toggle="tooltip" href="<?php echo WEB_URL; ?>dashboard.php" data-original-title="<?php echo $_data['home_breadcam'];?>"><i class="fa fa-dashboard"></i></a> </div>
    <div class="box box-success">
      <div class="box-header">
        <h3 class="box-title"><?php echo $_data['add_title_text'];?></h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <table class="table sakotable table-bordered table-striped dt-responsive">
          <thead>
            <tr>
              <th><?php echo $_data['date'];?></th>
              <th><?php echo $_data['text_1'];?></th>
              <th><?php echo $_data['text_3'];?></th>
              <th><?php echo $_data['text_2'];?></th>
              <th><?php echo $_data['text_7'];?></th>
              <th><?php echo $_data['text_6'];?></th>
              <th><?php echo $_data['action_text'];?></th>
            </tr>
          </thead>
          <tbody>
        <?php
			$list_sql = "Select mc.*,e.e_name from tbl_add_maintenance_cost mc left join tbl_add_employee e on e.eid = mc.m_created_by";
			if($branch_filter > 0){
				$list_sql .= " where mc.branch_id = " . $branch_filter;
			}
			$list_sql .= " order by mcid desc";
			$result = mysqli_query($link,$list_sql);
			while($row = mysqli_fetch_array($result)){?>
            <tr>
            <td><?php echo $row['m_date']; ?></td>
            <td><?php echo $row['m_location']; ?></td>
            <td><?php echo $row['m_details']; ?></td>
            <td><?php echo $ams_helper->currency($localization, $row['m_amount']); ?></td>
            <td><?php echo !empty($row['e_name']) ? $row['e_name'] : '-'; ?></td>
            <?php
				$status_value = normalizeStatus($row['m_status']);
				$status_labels = array(
					'submitted' => $_data['status_submitted'],
					'approved' => $_data['status_approved'],
					'transferred' => $_data['status_transferred']
				);
				$status_label = isset($status_labels[$status_value]) ? $status_labels[$status_value] : $_data['status_submitted'];
			?>
            <td><?php echo $status_label; ?></td>
            <td>
            <a class="btn btn-success ams_btn_special" data-toggle="tooltip" href="javascript:;" onclick="$('#nurse_view_<?php echo $row['mcid']; ?>').modal('show');" data-original-title="<?php echo $_data['view_text'];?>"><i class="fa fa-eye"></i></a>
            <?php if($is_employee && $status_value === 'submitted'){ ?>
              <a class="btn btn-warning ams_btn_special" data-toggle="tooltip" href="<?php echo WEB_URL;?>maintenance/add_maintenance_cost.php?id=<?php echo $row['mcid']; ?>" data-original-title="<?php echo $_data['edit_text'];?>"><i class="fa fa-pencil"></i></a>
              <a class="btn btn-danger ams_btn_special" data-toggle="tooltip" onclick="deleteMaintenanceCost(<?php echo $row['mcid']; ?>);" href="javascript:;" data-original-title="<?php echo $_data['delete_text'];?>"><i class="fa fa-trash-o"></i></a>
            <?php } ?>
            <?php if($is_super_admin && $status_value === 'submitted'){ ?>
              <a class="btn btn-info ams_btn_special" data-toggle="tooltip" href="<?php echo WEB_URL;?>maintenance/maintenance_cost_list.php?approve_id=<?php echo $row['mcid']; ?>" data-original-title="<?php echo $_data['approve_text'];?>"><i class="fa fa-check"></i></a>
            <?php } ?>
            <?php if(($is_admin || $is_super_admin) && $status_value === 'approved'){ ?>
              <a class="btn btn-primary ams_btn_special" data-toggle="tooltip" href="javascript:;" onclick="$('#transfer_view_<?php echo $row['mcid']; ?>').modal('show');" data-original-title="<?php echo $_data['transfer_text'];?>"><i class="fa fa-exchange"></i></a>
            <?php } ?>
            <div id="nurse_view_<?php echo $row['mcid']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header green_header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                    <h3 class="modal-title"><?php echo $_data['m_cost_details'];?></h3>
                  </div>
                  
                  <div class="modal-body">
                    <h3 style="text-decoration:underline;"><?php echo $_data['details_information'];?></h3>
                    <div class="row">
                      <div class="col-xs-6"> 
					    <b><?php echo $_data['date'];?> :</b> <?php echo $row['m_date']; ?><br/>
					    <b><?php echo $_data['text_1'];?> :</b> <?php echo $row['m_location']; ?><br/>
                        <b><?php echo $_data['text_2'];?> : </b> <?php echo $ams_helper->currency($localization, $row['m_amount']); ?><br/>
                        <b><?php echo $_data['text_6'];?> :</b> <?php echo $status_label; ?><br/>
                      </div>
					  <div class="col-xs-6">
					  	<b><?php echo $_data['text_3'];?> :</b> <?php echo $row['m_details']; ?><br/>
                        <b><?php echo $_data['text_7'];?> :</b> <?php echo !empty($row['e_name']) ? $row['e_name'] : '-'; ?><br/>
                        <?php if(!empty($row['transfer_image'])){ ?>
						  <b><?php echo $_data['transfer_proof'];?> :</b> <a target="_blank" href="<?php echo WEB_URL.'img/upload/'.$row['transfer_image']; ?>"><?php echo $_data['open_proof'];?></a><br/>
                        <?php } ?>
					  </div>
                    </div>
                  </div>
				  
                </div>
                <!-- /.modal-content -->
              </div>
            </div>
            <?php if(($is_admin || $is_super_admin) && $status_value === 'approved'){ ?>
              <div id="transfer_view_<?php echo $row['mcid']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header green_header">
                      <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                      <h3 class="modal-title"><?php echo $_data['transfer_text'];?></h3>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                      <div class="modal-body">
                        <div class="form-group">
                          <label for="transfer_image_<?php echo $row['mcid']; ?>"><span class="errorStar">*</span> <?php echo $_data['upload_proof'];?> :</label>
                          <button type="button" class="btn btn-default" onclick="document.getElementById('transfer_image_<?php echo $row['mcid']; ?>').click();"><i class="fa fa-upload"></i> <?php echo $_data['upload_image_text'];?></button>
                          <input type="file" name="transfer_image" id="transfer_image_<?php echo $row['mcid']; ?>" class="form-control" style="display:none;" />
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> <?php echo $_data['transfer_done_text'];?></button>
                      </div>
                      <input type="hidden" name="transfer_mcid" value="<?php echo $row['mcid']; ?>" />
                    </form>
                  </div>
                </div>
              </div>
            <?php } ?>
            </td>
            </tr>
            <?php } mysqli_close($link);$link = NULL; ?>
          </tbody>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
<script type="text/javascript">
function deleteMaintenanceCost(Id){
  	var iAnswer = confirm("<?php echo $_data['confirm']; ?>");
	if(iAnswer){
		window.location = '<?php echo WEB_URL; ?>maintenance/maintenance_cost_list.php?id=' + Id;
	}
  }
  $(document).ready(function() {
	setTimeout(function() {
		  $("#me").hide(300);
		  $("#you").hide(300);
	}, 3000);
});
</script>
<?php include('../footer.php'); ?>
