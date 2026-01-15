<?php 
include('../header_emp.php');
if(!isset($_SESSION['objLogin'])){
	header("Location: " . WEB_URL . "logout.php");
	die();
}
?>
<?php
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_complain_list.php');
$delinfo = 'none';
$addinfo = 'none';
$msg = "";
$branch_filter = !empty($_SESSION['objLogin']['branch_id']) ? (int)$_SESSION['objLogin']['branch_id'] : 0;
if(!empty($_SESSION['objLogin']['eid'])){
	$branch_filter = 0;
}
if(isset($_GET['id']) && $_GET['id'] != '' && $_GET['id'] > 0){
	$sqlx= "DELETE FROM `tbl_add_complain` WHERE complain_id = ".$_GET['id'];
	mysqli_query($link,$sqlx); 
	$delinfo = 'block';
}
if(isset($_GET['m']) && $_GET['m'] == 'add'){
	$addinfo = 'block';
	$msg = $_data['text_8'];
}
if(isset($_GET['m']) && $_GET['m'] == 'up'){
	$addinfo = 'block';
	$msg = $_data['text_9'];
}


?>
<style>
.image_work {
    display: flex!important;
    flex-direction: column!important;
    margin-bottom: 16px!important;
    padding: 0!important;
    float: left;
}
</style>
<!-- Content Header (Page header) -->

<section class="content-header">
  <h1><?php echo $_data['text_1'];?></h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo WEB_URL?>dashboard.php"><i class="fa fa-dashboard"></i><?php echo $_data['home_breadcam'];?></a></li>
    <li class="active"><?php echo $_data['text_2'];?></li>
    <li class="active"><?php echo $_data['text_1'];?></li>
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
      <?php echo $_data['text_10'];?> </div>
    <div id="you" class="alert alert-success alert-dismissable" style="display:<?php echo $addinfo; ?>">
      <button aria-hidden="true" data-dismiss="alert" class="close" type="button"><i class="fa fa-close"></i></button>
      <h4><i class="icon fa fa-check"></i> <?php echo $_data['success'];?> !</h4>
      <?php echo $msg; ?> </div>
    <div align="right" style="margin-bottom:1%;"> <a class="btn btn-success" data-toggle="tooltip" href="<?php echo WEB_URL; ?>e_dashboard/addcomplain.php" data-original-title="<?php echo $_data['text_3'];?>"><i class="fa fa-plus"></i></a> <a class="btn btn-success" data-toggle="tooltip" href="<?php echo WEB_URL; ?>e_dashboard.php" data-original-title="<?php echo $_data['home_breadcam'];?>"><i class="fa fa-dashboard"></i></a> </div>
    <div class="box box-success">
      <div class="box-header">
        <h3 class="box-title"><?php echo $_data['text_1'];?></h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <table class="table sakotable table-bordered table-striped dt-responsive">
          <thead>
            <tr>
              <th>Room</th>
              <th><?php echo $_data['text_7'];?></th>
              <th><?php echo $_data['text_5'];?></th>
			  <th><?php echo $_data['t_text_2'];?></th>
              <th><?php echo $_data['action_text'];?></th>
            </tr>
          </thead>
          <tbody>
            <?php
				$list_sql = "Select c.*,m.month_name,u.unit_no from tbl_add_complain c inner join tbl_add_month_setup m on m.m_id = c.c_month left join tbl_add_unit u on u.uid = c.c_unit_no where assign_employee_id = '".$_SESSION['objLogin']['eid']."'";
				if($branch_filter > 0){
					$list_sql .= " and c.branch_id = '" . $branch_filter . "'";
				}
				$list_sql .= " order by complain_id ASC";
				$result = mysqli_query($link,$list_sql);
				while($row = mysqli_fetch_array($result)){
                $image_after_work = "";
				$image_before_work = "";
				?>
            <tr>
              <td><?php echo $row['unit_no'] ?: '-'; ?></td>
			  <td><?php echo $row['c_date']; ?></td>
              <td><?php echo $row['c_title']; ?></td>
			  <td>
			  <?php if($row['job_status']=='0'){ echo $_data['t_status_pending']; } ?>
			  <?php if($row['job_status']=='1'){ echo $_data['t_status_in_progress']; } ?>
			  <?php if($row['job_status']=='2'){ echo $_data['t_status_on_hold']; } ?>
			  <?php if($row['job_status']=='3'){ echo $_data['t_status_completed']; } ?>
			  </td>
              <td><a class="btn btn-success ams_btn_special" data-toggle="tooltip" href="javascript:;" onClick="$('#nurse_view_<?php echo $row['complain_id']; ?>').modal('show');" data-original-title="<?php echo $_data['view_text'];?>"><i class="fa fa-eye"></i></a> <?php if($row['job_status']!='3'){ ?> <a class="btn btn-info ams_btn_special" data-toggle="tooltip" href="javascript:;" onClick="$('#solution_view_<?php echo $row['complain_id']; ?>').modal('show');" data-original-title="<?php echo $_data['add_emp_solution'];?>"><i class="fa fa-thumbs-up"></i></a><?php } ?>
                
				<div id="nurse_view_<?php echo $row['complain_id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header green_header">
                        <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                        <h3 class="modal-title"><?php echo $_data['text_1_1_1'];?></h3>
                      </div>
                      <div class="modal-body">
                        <h3 style="text-decoration:underline;"><?php echo $_data['details_information'];?></h3>
                        <div class="row">
                          <div class="col-xs-6"> <b><?php echo $_data['text_7'];?> :</b> <?php echo $row['c_date']; ?><br/>
                            <b><?php echo $_data['text_5'];?> :</b> <?php echo $row['c_title']; ?><br/>
                            <b><?php echo $_data['text_11'];?> :</b> <?php echo $row['month_name']; ?><br/>
                            <b><?php echo $_data['text_12'];?> :</b> <?php echo $row['c_year']; ?><br/>
							<b><?php echo $_data['t_text_2'];?> :</b> <?php if($row['job_status']=='0'){echo $_data['t_status_pending']; } else if($row['job_status']=='1'){echo $_data['t_status_in_progress']; } else if($row['job_status']=='2'){echo $_data['t_status_on_hold']; } else if($row['job_status']=='3'){echo $_data['t_status_completed']; } ?><br/>
							</div>
							<div class="col-xs-6">
							<b><?php echo $_data['text_6'];?> :</b> <?php echo $row['c_description']; ?><br/>
                          </div>
                        </div>
						<?php 
            				$image_after_work = $row['image_after_sol'];
            			    $image_before_sol = $row['image_before_sol'];
						?>
						
						<div class="row" align="center"><div class="col-xs-12">
                            <div style="display: flex;justify-content: center;gap: 45px;flex-direction:column;">
                              <div style="display: flex;flex-direction: column;">    
						      <?php if(!empty($image_before_sol)){ ?>
    						      <h3 style="text-decoration:underline;">Image Before Work :</h3>
                                    <a target="_blank" href="<?php echo !empty($image_before_sol) ? $image_before_sol : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png'; ?>">
                                        <img class="form-control output_<?php echo $row['complain_id']; ?>" src="<?php echo !empty($image_before_sol) ? $image_before_sol : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png' ; ?>" style="height:300px;width:300px;"/>
                                    </a>
                                <?php } ?>  
                                </div>  
						          
						      <div style="display: flex;flex-direction: column;">    
						      <?php if(!empty($image_after_work)){ ?>
    						      <h3 style="text-decoration:underline;">Image After Work :</h3>
                                    <a target="_blank" href="<?php echo !empty($image_after_work) ? $image_after_work : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png'; ?>">
                                        <img class="form-control output_<?php echo $row['complain_id']; ?>" src="<?php echo !empty($image_after_work) ? $image_after_work : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png' ; ?>" style="height:300px;width:300px;"/>
                                    </a>
                                <?php } ?>  
                                </div>  
                            </div>  						    
						  	<h3 style="text-decoration:underline;"><?php echo $_data['solution_label'];?></h3>
							<div><?php echo $row['solution']; ?></div>
						  	<h3 style="text-decoration:underline;"><?php echo $_data['text_51']; ?></h3>
							<div><?php 
							$ListDescription = !empty($row['solution_List']) ? $row['solution_List'] : '';

							echo nl2br($ListDescription); ?>
							</div>							
							
						  </div> 
						  

						  </div>

                      </div>
                    </div>
                    <!-- /.modal-content -->
                  </div>
                </div>
				
				<div id="solution_view_<?php echo $row['complain_id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header green_header">
                        <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                        <h3 class="modal-title"><?php echo $_data['add_solution'];?></h3>
                      </div>
                      <div class="modal-body">
                        <div>
							<label for="txtSolutionDescription"><?php echo $_data['t_text_2'];?></label><br/>
							<select style="width:100%;" id="ddlStatus_<?php echo $row['complain_id']; ?>" class="form-control" name="ddlStatus_<?php echo $row['complain_id']; ?>">
					<option value="0" <?php if($row['job_status']=='0'){echo 'selected';} ?>><?php echo $_data['t_status_pending']; ?></option>
					<option value="1" <?php if($row['job_status']=='1'){echo 'selected';} ?>><?php echo $_data['t_status_in_progress']; ?></option>
					<option value="2" <?php if($row['job_status']=='2'){echo 'selected';} ?>><?php echo $_data['t_status_on_hold']; ?></option>
					<option value="3" <?php if($row['job_status']=='3'){echo 'selected';} ?>><?php echo $_data['t_status_completed']; ?></option>
				  </select>
						
						</div>
						
						<br/>
                        <div class="form-group col-md-12 image_work">
                                <label for="Prsnttxtarea">Image Before Work :</label>
                                <?php if(!empty($image_before_sol)){ ?>
                                <a target="_blank" href="<?php echo !empty($image_before_sol) ? $image_before_sol : ''; ?>">
                                    <img class="form-control" src="<?php echo $image_before_sol; ?>" style="height:300px;width:300px;"/>
                                </a>
                                <?php } ?>
                        </div>
					
						<br/>
                        <div class="form-group  image_work">
                                <label for="Prsnttxtarea">Image After Work :</label>
                                <?php //if(!empty($image_after_work)){ ?>
                                <a target="_blank" href="<?php echo !empty($image_after_work) ? $image_after_work : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png'; ?>">
                                    <img class="form-control output_<?php echo $row['complain_id']; ?>" src="<?php echo !empty($image_after_work) ? $image_after_work : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png' ; ?>" style="height:300px;width:300px;"/>
                                </a>
                                <?php //} ?>
                                <input type="hidden" name="img_exist" value="<?php echo $image_after_work_track; ?>" />
                                <input type="hidden" id="image_after_work_<?php echo $row['complain_id']; ?>" name="image_after_work" value="" />
                             <div class="form-group">
                                 <span class="btn btn-file btn btn-default"><?php echo !empty($_data['upload_image']) ? $_data['upload_image'] : '' ;?>
                                <input type="file" name="uploaded_file" onchange="loadFile_1(event,<?php echo $row['complain_id']; ?>)" />
                                </span>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
						<br/>
						<div>
                          <label for="txtSolutionDescription"><?php echo $_data['solution_label'];?></label><br/>
						  <textarea style="width:100%;" id="txtSolutionDescription_<?php echo $row['complain_id']; ?>" class="form-control" name="txtSolutionDescription_<?php echo $row['complain_id']; ?>"><?php echo $row['solution']; ?></textarea>
                        </div>
                        <?php 
                        $ListDescription = !empty($row['solution_List']) ? $row['solution_List'] : '';
                        ?>
 						<div>
                          <label for="txtSolutionDescriptionList"><?php echo $_data['text_51']; ?></label><br/>
						  <textarea style="width:100%;" id="ListtxtSolutionDescription_<?php echo $row['complain_id']; ?>" class="form-control" name="ListtxtSolutionDescription_<?php echo $row['complain_id']; ?>"><?php echo $ListDescription; ?></textarea>
                        </div>                       

						<br/>
						<div align="right">
						  <button id="btnSubmit" onclick="saveSolution(<?php echo $row['complain_id']; ?>);" class="btn btn-success" name="btnSubmit">Save Solution</button>
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
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
<script type="text/javascript">
var loadFile_1 = function(event,cid) {
    console.log('output_'+cid);
	jQuery('.output_'+cid).attr('src',URL.createObjectURL(event.target.files[0]));

    var selectedfile = event.target.files;
    if (selectedfile.length > 0) {
      var imageFile = selectedfile[0];
      var fileReader = new FileReader();
      fileReader.onload = function(fileLoadedEvent) {
        var srcData = fileLoadedEvent.target.result;
        document.getElementById("image_after_work_"+cid).value = srcData;
      }
      fileReader.readAsDataURL(imageFile);
    }
	
	
	
};
function deleteComplain(Id){
  	var iAnswer = confirm("<?php echo $_data['confirm']; ?>");
	if(iAnswer){
		window.location = '<?php echo WEB_URL; ?>complain/complainlist.php?id=' + Id;
	}
  }
  function saveSolution(cid){
  	var _txtSolution = jQuery('#txtSolutionDescription_'+cid).val();
  	var _txtSolutionList = jQuery('#ListtxtSolutionDescription_'+cid).val();
	var _ddlStatus = jQuery('#ddlStatus_'+cid).val();
	var image_after_work = jQuery('#image_after_work_'+cid).val();
	if(_txtSolution != ''){
		$.ajax({
			url: "<?php echo WEB_URL; ?>/ajax/getunit.php",
			type: 'POST',
			data: 'txt='+_txtSolution+'&_txtSolutionList='+_txtSolutionList+'&cid='+cid+'&status='+_ddlStatus+'&image_after_work='+image_after_work+'&token=updateComplainSolution',
			dataType: 'html',
			success: function(data) {
			 if(data != '-99'){
				alert('<?php echo $_data['solution_success_msg']; ?>');
				window.location.reload();
			 }
			 else{
				window.location.href = '../index.php';
			 }
			}
		});
	} else {
		alert('<?php echo $_data['solution_label_required']; ?>');
	}
  }
  function _set_complain_status(cid, object){
	$.ajax({
		url: "<?php echo WEB_URL; ?>/ajax/getunit.php",
		type: 'POST',
		data: 'status='+object.value+'&cid='+cid+'&token=updateComplainStatus',
		dataType: 'json',
		success: function(data) {
		 if(data != '-99'){
			alert(data.msg);
			window.location.reload();
		 }
		 else{
			window.location.href = '../index.php';
		 }
		}
	});
  }
  $( document ).ready(function() {
	setTimeout(function() {
		$("#me").hide(300);
		$("#you").hide(300);
	}, 3000);
});

function set_job_assign(cid){
	if(jQuery('#ddlEmployee_'+cid).val()==''){
		alert('<?php echo $_data['select_employee'];?>');
	} else {
		$.ajax({
			url: "<?php echo WEB_URL; ?>/ajax/getunit.php",
			type: 'POST',
			data: 'eid='+jQuery('#ddlEmployee_'+cid).val()+'&cid='+cid+'&token=updateComplainJob',
			dataType: 'html',
			success: function(data) {
			 if(data != '-99'){
				alert('<?php echo $_data['assign_job_msg'];?>');
				window.location.reload();
			 }
			 else{
				window.location.href = '<?php echo WEB_URL; ?>/index.php';
			 }
			}
		});
	}
}
</script>
<?php include('../footer.php'); ?>
