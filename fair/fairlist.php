<?php 
include('../header.php');
if(!isset($_SESSION['objLogin'])){
	header("Location: " . WEB_URL . "logout.php");
	die();
}
?>
<?php
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_fare_list.php');
$delinfo = 'none';
$addinfo = 'none';
$msg = "";
if(isset($_GET['id']) && $_GET['id'] != '' && $_GET['id'] > 0){
	$sqlx= "DELETE FROM `tbl_add_fair` WHERE f_id = ".$_GET['id'];
	mysqli_query($link,$sqlx); 
	$delinfo = 'block';
}
if(isset($_GET['m']) && $_GET['m'] == 'add'){
	$addinfo = 'block';
	$msg = $_data['added_rent_successfully'];
}
if(isset($_GET['m']) && $_GET['m'] == 'up'){
	$addinfo = 'block';
	$msg = $_data['update_rent_successfully'] ;
}
?>
<!-- Content Header (Page header) -->

<section class="content-header">
  <h1><?php echo $_data['rent_list'];?></h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo WEB_URL?>dashboard.php"><i class="fa fa-dashboard"></i><?php echo $_data['home_breadcam'];?></a></li>
    <li class="active"><?php echo $_data['add_new_rent_information_breadcam'];?></li>
	<li class="active"><?php echo $_data['rent_list'];?></li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
<!-- Full Width boxes (Stat box) -->
<div class="row">
  <div class="col-xs-12">
    <div id="me" class="alert alert-danger alert-dismissable" style="display:<?php echo $delinfo; ?>">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button"><i class="fa fa-close"></i></button>
      <h4><i class="icon fa fa-ban"></i><?php echo $_data['delete_text'];?> !</h4>
      <?php echo $_data['delete_rent_information'];?> </div>
    <div id="you" class="alert alert-success alert-dismissable" style="display:<?php echo $addinfo; ?>">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button"><i class="fa fa-close"></i></button>
      <h4><i class="icon fa fa-check"></i><?php echo $_data['success'];?> !</h4>
      <?php echo $msg; ?> </div>
    <div align="right" style="margin-bottom:1%;"> <a class="btn btn-success" data-toggle="tooltip" href="<?php echo WEB_URL; ?>fair/addfair.php" data-original-title="<?php echo $_data['add_new_rent_breadcam'];?>"><i class="fa fa-plus"></i></a> <a class="btn btn-success" data-toggle="tooltip" href="<?php echo WEB_URL; ?>dashboard.php" data-original-title="<?php echo $_data['home_breadcam'];?>"><i class="fa fa-dashboard"></i></a> </div>
    <div class="box box-success">
      <div class="box-header">
        <h3 class="box-title"><?php echo $_data['rent_list'];?></h3>
      </div>
      <div class="box-body">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#rent-due">Due</a></li>
          <li><a data-toggle="tab" href="#rent-paid">Paid</a></li>
        </ul>
        <div class="tab-content" style="margin-top:12px;">
          <div id="rent-due" class="tab-pane fade in active">
        <table class="table sakotable table-bordered table-striped dt-responsive">
          <thead>
            <tr>
              <th><?php echo $_data['invoice_no'];?></th>
              <th><?php echo $_data['add_new_form_field_text_6'];?></th>
              <th><?php echo $_data['add_new_form_field_text_16'];?></th>
              <th><?php echo $_data['add_new_form_field_text_1'];?></th>
              <th><?php echo $_data['add_new_form_field_text_3'];?></th>
              <th><?php echo $_data['add_new_form_field_text_17'];?></th>
              <th><?php echo $_data['add_new_form_field_text_18'];?></th>
              <th>Other Fee</th>
              <th><?php echo $_data['add_new_form_field_text_14'];?></th>
              <th><?php echo $_data['action_text'];?></th>
            </tr>
          </thead>
          <tbody>
        <?php
		$result = mysqli_query($link,"Select *,ar.image as r_image,ar.r_name,fl.floor_no as fl_floor,u.unit_no as u_unit,m.month_name from tbl_add_fair f inner join tbl_add_floor fl on fl.fid = f.floor_no inner join tbl_add_unit u on u.uid = f.unit_no inner join tbl_add_month_setup m on m.m_id = f.month_id inner join tbl_add_rent ar on ar.rid = f.rid where f.type = 'Rented' and f.bill_status = 0 and f.branch_id = " . (int)$_SESSION['objLogin']['branch_id'] . " order by f.f_id desc");
				while($row = mysqli_fetch_array($result)){
					$r_image = WEB_URL . 'img/no_image.jpg';	
					if(file_exists(ROOT_PATH . '/img/upload/' . $row['r_image']) && $row['r_image'] != ''){
						$r_image = WEB_URL . 'img/upload/' . $row['r_image'];
					}
				?>
            <tr>
            <td><?php echo $row['f_id']; ?></td>
            <td><?php echo $row['r_name']; ?></td>
            <td><?php echo $row['type']; ?></td>
            <td><?php echo $row['fl_floor']; ?></td>
            <td><?php echo $row['u_unit']; ?></td>
            <td><?php echo $row['month_name']; ?></td>
            <td><?php echo $row['xyear']; ?></td>
            <td><?php echo $row['other_bill']; ?></td>
			<td><?php echo $ams_helper->currency($localization, $row['total_rent']); ?></td>
            <td>
            <a target="_blank" class="btn btn-info ams_btn_special" data-original-title="<?php echo $_data['invoice_no'];?>" data-toggle="tooltip" href="<?php echo WEB_URL;?>fair/invoice.php?rentid=<?php echo $row['f_id']; ?>"><i class="fa fa-file-text-o"></i></a> <a class="btn btn-success ams_btn_special" data-toggle="tooltip" href="javascript:;" onClick="$('#nurse_view_<?php echo $row['f_id']; ?>').modal('show');" data-original-title="<?php echo $_data['view_text'];?>"><i class="fa fa-eye"></i></a> <a class="btn btn-warning ams_btn_special" data-toggle="tooltip" href="<?php echo WEB_URL;?>fair/addfair.php?id=<?php echo $row['f_id']; ?>" data-original-title="<?php echo $_data['edit_text'];?>"><i class="fa fa-pencil"></i></a> <a class="btn btn-danger ams_btn_special" data-toggle="tooltip" onClick="deleteFair(<?php echo $row['f_id']; ?>);" href="javascript:;" data-original-title="<?php echo $_data['delete_text'];?>"><i class="fa fa-trash-o"></i></a>
            <div id="nurse_view_<?php echo $row['f_id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header green_header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                    <h3 class="modal-title"><?php echo $_data['fare_details'];?></h3>
                  </div>
                  <div class="modal-body model_view" align="center">&nbsp;
                    <div><img class="photo_img_round" style="width:100px;height:100px;" src="<?php echo $r_image;  ?>" /></div>
                    <div class="model_title"><?php echo $row['r_name']; ?></div>
					<div class="model_title"><?php echo $_data['invoice_no'];?> : <?php echo $row['f_id']; ?></div>
                  </div>
				  <div class="modal-body">
                    <h3 style="text-decoration:underline;text-align:left"><?php echo $_data['details_information'];?></h3><br/>
                    <div class="row">
                      <div class="col-xs-6"> 
					    <b><?php echo $_data['add_new_form_field_text_6'];?> :</b> <?php echo $row['r_name']; ?><br/>
                        <b><?php echo $_data['add_new_form_field_text_1'];?> :</b> <?php echo $row['fl_floor']; ?><br/>
                        <b><?php echo $_data['add_new_form_field_text_3'];?> :</b> <?php echo $row['u_unit']; ?><br/>
                        <b><?php echo $_data['add_new_form_field_text_17'];?> :</b> <?php echo $row['month_name'];?><br/>
						<b><?php echo $_data['add_new_form_field_text_18'];?> :</b> <?php echo $row['xyear'];?><br/>
                        <b><?php echo $_data['add_new_form_field_text_7'];?> : </b> <?php echo $ams_helper->currency($localization, $row['rent']); ?><br/>
                      </div>
                      
                      <div class="col-xs-6"> 
						<b><?php echo $_data['add_new_form_field_text_14'];?> : </b> <?php echo $ams_helper->currency($localization, $row['total_rent']); ?><br/>
						<b><?php echo $_data['add_new_form_field_text_15'];?> :</b> <?php echo $row['issue_date']; ?><br/>
	<?php $img_url = $row['images_proof'];?>					
	<b>Proof Of Payment :</b><?php echo !empty($row['images_proof']) ? '<a target="_blank" href="'.WEB_URL.'/img/upload/'.$img_url.'" > Open Proof</a>' : ''; ?><br/>
                      </div>
                      
                    </div>
                  </div>
				  
                </div>
                <!-- /.modal-content -->
              </div>
            </div>
            </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
          </div>
          <div id="rent-paid" class="tab-pane fade">
        <table class="table sakotable table-bordered table-striped dt-responsive">
          <thead>
            <tr>
              <th><?php echo $_data['invoice_no'];?></th>
              <th><?php echo $_data['add_new_form_field_text_6'];?></th>
              <th><?php echo $_data['add_new_form_field_text_16'];?></th>
              <th><?php echo $_data['add_new_form_field_text_1'];?></th>
              <th><?php echo $_data['add_new_form_field_text_3'];?></th>
              <th><?php echo $_data['add_new_form_field_text_17'];?></th>
              <th><?php echo $_data['add_new_form_field_text_18'];?></th>
              <th>Other Fee</th>
              <th><?php echo $_data['add_new_form_field_text_14'];?></th>
              <th><?php echo $_data['action_text'];?></th>
            </tr>
          </thead>
          <tbody>
        <?php
		$result = mysqli_query($link,"Select *,ar.image as r_image,ar.r_name,fl.floor_no as fl_floor,u.unit_no as u_unit,m.month_name from tbl_add_fair f inner join tbl_add_floor fl on fl.fid = f.floor_no inner join tbl_add_unit u on u.uid = f.unit_no inner join tbl_add_month_setup m on m.m_id = f.month_id inner join tbl_add_rent ar on ar.rid = f.rid where f.type = 'Rented' and f.bill_status = 1 and f.branch_id = " . (int)$_SESSION['objLogin']['branch_id'] . " order by f.f_id desc");
				while($row = mysqli_fetch_array($result)){
					$r_image = WEB_URL . 'img/no_image.jpg';	
					if(file_exists(ROOT_PATH . '/img/upload/' . $row['r_image']) && $row['r_image'] != ''){
						$r_image = WEB_URL . 'img/upload/' . $row['r_image'];
					}
				?>
            <tr>
            <td><?php echo $row['f_id']; ?></td>
            <td><?php echo $row['r_name']; ?></td>
            <td><?php echo $row['type']; ?></td>
            <td><?php echo $row['fl_floor']; ?></td>
            <td><?php echo $row['u_unit']; ?></td>
            <td><?php echo $row['month_name']; ?></td>
            <td><?php echo $row['xyear']; ?></td>
            <td><?php echo $row['other_bill']; ?></td>
			<td><?php echo $ams_helper->currency($localization, $row['total_rent']); ?></td>
            <td>
            <a target="_blank" class="btn btn-info ams_btn_special" data-original-title="<?php echo $_data['invoice_no'];?>" data-toggle="tooltip" href="<?php echo WEB_URL;?>fair/invoice.php?rentid=<?php echo $row['f_id']; ?>"><i class="fa fa-file-text-o"></i></a> <a class="btn btn-success ams_btn_special" data-toggle="tooltip" href="javascript:;" onClick="$('#nurse_view_<?php echo $row['f_id']; ?>').modal('show');" data-original-title="<?php echo $_data['view_text'];?>"><i class="fa fa-eye"></i></a> <a class="btn btn-warning ams_btn_special" data-toggle="tooltip" href="<?php echo WEB_URL;?>fair/addfair.php?id=<?php echo $row['f_id']; ?>" data-original-title="<?php echo $_data['edit_text'];?>"><i class="fa fa-pencil"></i></a> <a class="btn btn-danger ams_btn_special" data-toggle="tooltip" onClick="deleteFair(<?php echo $row['f_id']; ?>);" href="javascript:;" data-original-title="<?php echo $_data['delete_text'];?>"><i class="fa fa-trash-o"></i></a>
            <div id="nurse_view_<?php echo $row['f_id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header green_header">
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                    <h3 class="modal-title"><?php echo $_data['fare_details'];?></h3>
                  </div>
                  <div class="modal-body model_view" align="center">&nbsp;
                    <div><img class="photo_img_round" style="width:100px;height:100px;" src="<?php echo $r_image;  ?>" /></div>
                    <div class="model_title"><?php echo $row['r_name']; ?></div>
					<div class="model_title"><?php echo $_data['invoice_no'];?> : <?php echo $row['f_id']; ?></div>
                  </div>
				  <div class="modal-body">
                    <h3 style="text-decoration:underline;text-align:left"><?php echo $_data['details_information'];?></h3><br/>
                    <div class="row">
                      <div class="col-xs-6"> 
					    <b><?php echo $_data['add_new_form_field_text_6'];?> :</b> <?php echo $row['r_name']; ?><br/>
                        <b><?php echo $_data['add_new_form_field_text_1'];?> :</b> <?php echo $row['fl_floor']; ?><br/>
                        <b><?php echo $_data['add_new_form_field_text_3'];?> :</b> <?php echo $row['u_unit']; ?><br/>
                        <b><?php echo $_data['add_new_form_field_text_17'];?> :</b> <?php echo $row['month_name'];?><br/>
						<b><?php echo $_data['add_new_form_field_text_18'];?> :</b> <?php echo $row['xyear'];?><br/>
                        <b><?php echo $_data['add_new_form_field_text_7'];?> : </b> <?php echo $ams_helper->currency($localization, $row['rent']); ?><br/>
                      </div>
                      
                      <div class="col-xs-6"> 
						<b><?php echo $_data['add_new_form_field_text_14'];?> : </b> <?php echo $ams_helper->currency($localization, $row['total_rent']); ?><br/>
						<b><?php echo $_data['add_new_form_field_text_15'];?> :</b> <?php echo $row['issue_date']; ?><br/>
	<?php $img_url = $row['images_proof'];?>					
	<b>Proof Of Payment :</b><?php echo !empty($row['images_proof']) ? '<a target="_blank" href="'.WEB_URL.'/img/upload/'.$img_url.'" > Open Proof</a>' : ''; ?><br/>
                      </div>
                      
                    </div>
                  </div>
				  
                </div>
                <!-- /.modal-content -->
              </div>
            </div>
            </td>
            </tr>
            <?php } mysqli_close($link);$link = NULL; ?>
          </tbody>
        </table>
          </div>
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
<script type="text/javascript">
function deleteFair(Id){
  	var iAnswer = confirm("<?php echo $_data['confirm']; ?>");
	if(iAnswer){
		window.location = '<?php echo WEB_URL; ?>fair/fairlist.php?id=' + Id;
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
