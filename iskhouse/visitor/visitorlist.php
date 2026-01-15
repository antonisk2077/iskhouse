<?php include('../header.php');
if(!isset($_SESSION['objLogin'])){
	header("Location: " . WEB_URL . "logout.php");
	die();
}
?>
<?php
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_visitor_list.php');
$delinfo = 'none';
$addinfo = 'none';
$msg = "";
$filter_branch_id = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : (int)$_SESSION['objLogin']['branch_id'];
$branch_filter_sql = ($filter_branch_id > 0) ? " where v.branch_id = " . $filter_branch_id : "";
if(isset($_GET['id']) && $_GET['id'] != '' && $_GET['id'] > 0){
	$sqlx= "DELETE FROM `tbl_visitor` WHERE vid = ".$_GET['id'];
	mysqli_query($link,$sqlx); 
	$delinfo = 'block';
}
if(isset($_GET['m']) && $_GET['m'] == 'add'){
	$addinfo = 'block';
	$msg = $_data['text_15'];
}
if(isset($_GET['m']) && $_GET['m'] == 'up'){
	$addinfo = 'block';
	$msg = $_data['text_17'];
}
?>
<!-- Content Header (Page header) -->

<section class="content-header">
  <h1><?php echo $_data['text_1_1'];?></h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo WEB_URL?>dashboard.php"><i class="fa fa-dashboard"></i><?php echo $_data['home_breadcam'];?></a></li>
	<li class="active"><?php echo $_data['text_2'];?></li>
    <li class="active"><?php echo $_data['text_1_1'];?></li>
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
      <?php echo $_data['text_18'];?> </div>
    <div id="you" class="alert alert-success alert-dismissable" style="display:<?php echo $addinfo; ?>">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button"><i class="fa fa-close"></i></button>
      <h4><i class="icon fa fa-check"></i> <?php echo $_data['success'];?> !</h4>
      <?php echo $msg; ?> </div>
    <div align="right" style="margin-bottom:1%;">
      <form method="get" class="form-inline" style="display:inline-block;margin-right:6px;">
        <select name="branch_id" class="form-control" onchange="this.form.submit();">
          <option value="0"><?php echo $_data['branch_all']; ?></option>
          <?php
            $result_branch = mysqli_query($link,"SELECT branch_id, branch_name FROM tblbranch WHERE b_status = 1 ORDER BY branch_name ASC");
            while($row_branch = mysqli_fetch_array($result_branch)){?>
          <option <?php if($filter_branch_id == $row_branch['branch_id']){echo 'selected';}?> value="<?php echo $row_branch['branch_id'];?>"><?php echo $row_branch['branch_name'];?></option>
          <?php } ?>
        </select>
      </form>
      <a class="btn btn-success" data-toggle="tooltip" href="<?php echo WEB_URL; ?>visitor/addvisitor.php" data-original-title="<?php echo $_data['text_3'];?>"><i class="fa fa-plus"></i></a>
      <a class="btn btn-success" data-toggle="tooltip" href="<?php echo WEB_URL; ?>dashboard.php" data-original-title="<?php echo $_data['home_breadcam'];?>"><i class="fa fa-dashboard"></i></a>
    </div>
    <div class="box box-success">
      <div class="box-header">
        <h3 class="box-title"><?php echo $_data['text_1_1'];?></h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <table class="table sakotable table-bordered table-striped dt-responsive">
          <thead>
            <tr>
              <th><?php echo $_data['text_5'];?></th>
              <th><?php echo $_data['text_6'];?></th>
              <th><?php echo $_data['text_7'];?></th>
              <th><?php echo $_data['text_8'];?></th>
              <th><?php echo $_data['text_9'];?></th>
              <th><?php echo $_data['text_11'];?></th>
              <th><?php echo $_data['text_13'];?></th>
              <th><?php echo $_data['text_14'];?></th>
			  <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <?php
				$result = mysqli_query($link,"Select *,fr.floor_no,u.unit_no from tbl_visitor v inner join tbl_add_floor fr on fr.fid = v.floor_id inner join tbl_add_unit u on u.uid = v.unit_id".$branch_filter_sql." order by v.vid desc");
				while($row = mysqli_fetch_array($result)){?>
            <tr>
              <td><?php echo $row['issue_date']; ?></td>
              <td><?php echo $row['name']; ?></td>
              <td><?php echo $row['mobile']; ?></td>
              <td><?php echo $row['address']; ?></td>
              <td><?php echo $row['floor_no']; ?></td>
              <td><label class="label label-warning ams_label"><?php echo $row['unit_no']; ?></label></td>
              <td><label class="label label-success ams_label"><?php echo $row['intime']; ?></label></td>
              <td><label class="label label-danger ams_label"><?php echo $row['outtime']; ?></label></td>
              <td><a class="btn btn-warning ams_btn_special" data-toggle="tooltip" href="<?php echo WEB_URL;?>visitor/addvisitor.php?id=<?php echo $row['vid']; ?>" data-original-title="<?php echo $_data['edit_text'];?>"><i class="fa fa-pencil"></i></a> <a class="btn btn-danger ams_btn_special" data-toggle="tooltip" onclick="deleteFloor(<?php echo $row['vid']; ?>);" href="javascript:;" data-original-title="<?php echo $_data['delete_text'];?>"><i class="fa fa-trash-o"></i></a> </td>
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
function deleteFloor(Id){
  	var iAnswer = confirm("<?php echo $_data['text_19']; ?>");
	if(iAnswer){
		window.location = '<?php echo WEB_URL; ?>visitor/visitorlist.php?id=' + Id;
	}
  }
  
  $( document ).ready(function() {
	setTimeout(function() {
		  $("#me").hide(300);
		  $("#you").hide(300);
	}, 3000);
});
</script>
<?php include('../footer.php'); ?>
