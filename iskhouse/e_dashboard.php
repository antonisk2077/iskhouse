<?php
include('header_emp.php');
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_admin_dashboard.php');
$dashboard_lang = $_data;
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_complain_list.php');
$complain_lang = $_data;
$_data = $dashboard_lang;
//
if(!isset($_SESSION['objLogin'])){
	header("Location: ".WEB_URL."logout.php");
	die();
}

$total_renter = 0; //default
$result_renter = mysqli_query($link,"Select count(r.rid) as total FROM tbl_add_rent r inner join tbl_add_floor f on f.fid = r.r_floor_no inner join tbl_add_unit u on u.uid = r.r_unit_no WHERE r.branch_id =".(int)$_SESSION['objLogin']['branch_id']." order by r.r_unit_no asc");
if($row_renter = mysqli_fetch_array($result_renter)){
	$total_renter = $row_renter['total'];
}

////////////////////monthly leave graph///////////////////////////////////////////////////////////////////////////////////////////////
/*$yearly_leave_data = '';
$year_label = '';

$_graph_yearly_leave = array();
$result_yearly_graph = mysqli_query($link,"SELECT *, YEAR(request_date) as leave_year, SUM(DATEDIFF(`to`,`from`))  as total_days FROM tbl_employee_leave_request where branch_id = '" . (int)$_SESSION['objLogin']['branch_id'] . "' and employee_id = '" . (int)$_SESSION['objLogin']['eid'] . "' AND request_status = 'Accepted' group by leave_year ORDER BY leave_year ASC");
while($row_yearly_data = mysqli_fetch_assoc($result_yearly_graph)){
	$_graph_yearly_leave[$row_yearly_data['leave_year']] = $row_yearly_data['total_days'];
}

$grapth_data = '';
if(!empty($_graph_yearly_leave)){
	$year_label = '';
	$yearly_leave_data = '';
	foreach($_graph_yearly_leave as $key => $value){
		$year_label .= $key.',';
		$yearly_leave_data .= $value.',';
	}
	$year_label = trim($year_label, ',');
	$yearly_leave_data = trim($yearly_leave_data, ',');
}*/

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////Monthly Visitor Graph///////////////////////////////////////////////////////////////////////////////////////////////
$monthly_visitor_graph = '0,0,0,0,0,0,0,0,0,0,0,0';
$_visitor_graph = array();
$result_visitor_graph = mysqli_query($link,"SELECT count(*) as total_visitor,xmonth FROM `tbl_visitor` WHERE xyear = ".date('Y')." and branch_id = ".(int)$_SESSION['objLogin']['branch_id']." group by xmonth order by xmonth ASC");
while($row_monthly_visitor_graph = mysqli_fetch_assoc($result_visitor_graph)){
	$_visitor_graph[$row_monthly_visitor_graph['xmonth']] = $row_monthly_visitor_graph;
}
$visitor_graph_data = '';
if(!empty($_visitor_graph)){
	for($i=1;$i<=12;$i++){
		if(isset($_visitor_graph[$i])){
			$visitor_graph_data .= $_visitor_graph[$i]['total_visitor']. ',';
		} else {
			$visitor_graph_data .='0,';
		}
	}
}
if(!empty($visitor_graph_data)){
	$monthly_visitor_graph = trim($visitor_graph_data, ',');
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////NOTICE BOARD/////////////////////////////////////////////////////////////////////////////////////
$notice = '';
$result_notice = mysqli_query($link,"SELECT * FROM tbl_employee_notice where notice_status = 1 AND branch_id=".(int)$_SESSION['objLogin']['branch_id']);
if($row_notice_board = mysqli_fetch_array($result_notice)){
	$notice = $row_notice_board['notice_description'];
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>
<!-- Content Header (Page header) -->

<section class="content-header">
  <h1><?php echo $_data['dashboard_emp'];?><small><?php echo $_data['control'];?></small> </h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo WEB_URL; ?>e_dashboard.php"><i class="fa fa-dashboard"></i> <?php echo $_data['home_breadcam'];?></a></li>
    <li class="active"><?php echo $_data['dash'];?></li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <!-- /.row start -->
  <div class="row home_dash_box">
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-green">
        <div class="inner">
          <h3><?php echo $total_renter; ?></h3>
          <p><?php echo $_data['rented_details'];?></p>
        </div>
        <div class="icon"> <img height="80" width="80" src="img/rented.png"></a> </div>
        <a href="<?php echo WEB_URL; ?>e_dashboard/rented_details.php" class="small-box-footer"><?php echo $_data['dashboard_more_info'];?> <i class="fa fa-arrow-circle-right"></i></a> </div>
    </div>
    <!-- ./col end -->
  </div>
  <!-- /.row end -->
  <div class="row">
    <div class="col-lg-6 col-xs-12">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-line-chart" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $_data['leave_graph_header_text']; ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="chart">
            <?php if(!empty($monthly_visitor_graph)) { ?>
            <canvas id="salesChart"></canvas>
            <?php } else { ?>
            <h4 align="center"><?php echo $_data['leave_graph_no_record']; ?></h4>
            <?php }?>
          </div>
        </div>
      </div>
    </div>
	<div class="col-lg-12 col-xs-12">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-car" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $_data['e_last_5_visitors']; ?></h3>
        </div>
        <div class="box-body">
			
			<table class="table table-bordered table-striped dt-responsive">
          <thead>
            <tr>
              <th><?php echo $_data['ee_text_5'];?></th>
              <th><?php echo $_data['ee_text_6'];?></th>
              <th><?php echo $_data['ee_text_7'];?></th>
              <th><?php echo $_data['ee_text_8'];?></th>
              <th><?php echo $_data['ee_text_9'];?></th>
              <th class="text-center"><?php echo $_data['ee_text_11'];?></th>
              <th class="text-center"><?php echo $_data['ee_text_13'];?></th>
              <th class="text-center"><?php echo $_data['ee_text_14'];?></th>
            </tr>
          </thead>
          <tbody>
            <?php
				$result = mysqli_query($link,"Select *,fr.floor_no,u.unit_no from tbl_visitor v inner join tbl_add_floor fr on fr.fid = v.floor_id inner join tbl_add_unit u on u.uid = v.unit_id where v.branch_id = " . (int)$_SESSION['objLogin']['branch_id'] . " order by v.vid desc LIMIT 5");
				while($row = mysqli_fetch_array($result)){?>
            <tr>
              <td><label class="label label-info ams_label"><?php echo $row['issue_date']; ?></label></td>
              <td><?php echo $row['name']; ?></td>
              <td><?php echo $row['mobile']; ?></td>
              <td><?php echo $row['address']; ?></td>
              <td><label class="label label-primary ams_label"><?php echo $row['floor_no']; ?></label></td>
              <td align="center"><label class="label label-warning ams_label"><?php echo $row['unit_no']; ?></label></td>
              <td align="center"><label class="label label-success ams_label"><?php echo $row['intime']; ?></label></td>
              <td align="center"><label class="label label-danger ams_label"><?php echo $row['outtime']; ?></label></td>
            </tr>
			<?php } ?>
          </tbody>
        </table>
			
			
		</div>
      </div>
    </div>
	<div class="col-lg-12 col-xs-12">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-comments" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $_data['c_table_header']; ?></h3>
        </div>
        <div class="box-body">
			<table class="table table-bordered table-striped dt-responsive">
          <thead>
            <tr>
              <th>Room</th>
              <th><?php echo $complain_lang['text_7']; ?></th>
              <th><?php echo $complain_lang['t_text_2']; ?></th>
              <th><?php echo $complain_lang['text_5']; ?></th>
              <th style="text-align:center;"><?php echo $_data['action_text']; ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
				$result = mysqli_query($link,"Select c.*,m.month_name,u.unit_no from tbl_add_complain c inner join tbl_add_month_setup m on m.m_id = c.c_month left join tbl_add_unit u on u.uid = c.c_unit_no where (c.assign_employee_id = '" . (int)$_SESSION['objLogin']['eid'] . "' or c.c_userid = '" . (int)$_SESSION['objLogin']['eid'] . "') order by complain_id DESC LIMIT 5");
				while($row = mysqli_fetch_array($result)){?>
            <tr>
              <td><?php echo $row['unit_no'] ?: '-'; ?></td>
              <td><?php echo $row['c_date']; ?></td>
              <td>
			  <?php if($row['job_status']=='0'){ echo $complain_lang['t_status_pending']; } ?>
			  <?php if($row['job_status']=='1'){ echo $complain_lang['t_status_in_progress']; } ?>
			  <?php if($row['job_status']=='2'){ echo $complain_lang['t_status_on_hold']; } ?>
			  <?php if($row['job_status']=='3'){ echo $complain_lang['t_status_completed']; } ?>
			  </td>
              <td><?php echo $row['c_title']; ?></td>
              <td align="center">
				<?php
					$image_after_work = $row['image_after_sol'];
					$image_before_sol = $row['image_before_sol'];
					$ListDescription = !empty($row['solution_List']) ? $row['solution_List'] : '';
				?>
				<a class="btn btn-success ams_btn_special" data-toggle="tooltip" href="javascript:;" onClick="$('#complain_view_<?php echo $row['complain_id']; ?>').modal('show');" data-original-title="<?php echo $_data['view_text'];?>"><i class="fa fa-eye"></i></a>
				<?php if($row['job_status']!='3'){ ?>
					<a class="btn btn-info ams_btn_special" data-toggle="tooltip" href="javascript:;" onClick="$('#solution_view_<?php echo $row['complain_id']; ?>').modal('show');" data-original-title="<?php echo $complain_lang['add_emp_solution'];?>"><i class="fa fa-thumbs-up"></i></a>
				<?php } ?>
				<a class="btn btn-warning ams_btn_special" data-toggle="tooltip" href="<?php echo WEB_URL; ?>e_dashboard/addcomplain.php?id=<?php echo $row['complain_id']; ?>" data-original-title="<?php echo $_data['edit_text'];?>"><i class="fa fa-pencil"></i></a>
				<a class="btn btn-danger ams_btn_special" data-toggle="tooltip" href="javascript:;" onClick="deleteComplain(<?php echo $row['complain_id']; ?>);" data-original-title="<?php echo $_data['delete_text'];?>"><i class="fa fa-trash-o"></i></a>
                <div style="text-align:left;" id="complain_view_<?php echo $row['complain_id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header green_header">
                        <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                        <h3 class="modal-title"><?php echo $_data['c_modal_top_header'];?></h3>
                      </div>
                      <div class="modal-body">
                        <h3 style="text-decoration:underline;"><?php echo $_data['details_information'];?></h3>
                        <div class="row">
                          <div class="col-xs-6"> <b><?php echo $_data['c_table_column_2'];?> :</b> <?php echo $row['c_date']; ?><br/>
                            <b><?php echo $_data['c_table_column_1'];?> :</b> <?php echo $row['c_title']; ?><br/>
                            <b><?php echo $_data['c_table_column_4'];?> :</b> <?php echo $row['month_name']; ?><br/>
                            <b><?php echo $_data['c_table_column_5'];?> :</b> <?php echo $row['c_year']; ?><br/>
                          </div>
                          <div class="col-xs-6"> <b><?php echo $_data['c_table_column_6'];?> :</b> <?php echo $row['c_description']; ?><br/>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
				<div id="solution_view_<?php echo $row['complain_id']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header green_header">
                        <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true"><i class="fa fa-close"></i></span></button>
                        <h3 class="modal-title"><?php echo $complain_lang['add_solution'];?></h3>
                      </div>
                      <div class="modal-body">
                        <div>
							<label for="txtSolutionDescription"><?php echo $complain_lang['t_text_2'];?></label><br/>
							<select style="width:100%;" id="ddlStatus_<?php echo $row['complain_id']; ?>" class="form-control" name="ddlStatus_<?php echo $row['complain_id']; ?>">
								<option value="0" <?php if($row['job_status']=='0'){echo 'selected';} ?>><?php echo $complain_lang['t_status_pending']; ?></option>
								<option value="1" <?php if($row['job_status']=='1'){echo 'selected';} ?>><?php echo $complain_lang['t_status_in_progress']; ?></option>
								<option value="2" <?php if($row['job_status']=='2'){echo 'selected';} ?>><?php echo $complain_lang['t_status_on_hold']; ?></option>
								<option value="3" <?php if($row['job_status']=='3'){echo 'selected';} ?>><?php echo $complain_lang['t_status_completed']; ?></option>
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
                        <div class="form-group image_work">
                                <label for="Prsnttxtarea">Image After Work :</label>
                                <a target="_blank" href="<?php echo !empty($image_after_work) ? $image_after_work : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png'; ?>">
                                    <img class="form-control output_<?php echo $row['complain_id']; ?>" src="<?php echo !empty($image_after_work) ? $image_after_work : 'https://hearhear.org/wp-content/uploads/2019/09/no-image-icon.png' ; ?>" style="height:300px;width:300px;"/>
                                </a>
                                <input type="hidden" id="image_after_work_<?php echo $row['complain_id']; ?>" name="image_after_work" value="" />
                             <div class="form-group">
                                 <span class="btn btn-file btn btn-default"><?php echo !empty($complain_lang['upload_image']) ? $complain_lang['upload_image'] : '' ;?>
                                <input type="file" name="uploaded_file" onchange="loadFile_1(event,<?php echo $row['complain_id']; ?>)" />
                                </span>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
						<br/>
						<div>
                          <label for="txtSolutionDescription"><?php echo $complain_lang['solution_label'];?></label><br/>
						  <textarea style="width:100%;" id="txtSolutionDescription_<?php echo $row['complain_id']; ?>" class="form-control" name="txtSolutionDescription_<?php echo $row['complain_id']; ?>"><?php echo $row['solution']; ?></textarea>
                        </div>
 						<div>
                          <label for="txtSolutionDescriptionList"><?php echo $complain_lang['text_51']; ?></label><br/>
						  <textarea style="width:100%;" id="ListtxtSolutionDescription_<?php echo $row['complain_id']; ?>" class="form-control" name="ListtxtSolutionDescription_<?php echo $row['complain_id']; ?>"><?php echo $ListDescription; ?></textarea>
                        </div>                       
						<br/>
						<div align="right">
						  <button id="btnSubmit" onclick="saveSolution(<?php echo $row['complain_id']; ?>);" class="btn btn-success" name="btnSubmit"><?php echo $complain_lang['add_solution'];?></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
			  </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
		</div>
      </div>
    </div>
	
	<div class="col-lg-12 col-xs-12">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-bullhorn" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $_data['c_notice_board']; ?></h3>
        </div>
        <div class="box-body"> <?php echo $notice; ?> </div>
      </div>
    </div>
    <div class="col-lg-12 col-xs-12" id="rules">
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-gavel" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $_data['c_apartment_rules']; ?></h3>
        </div>
        <div class="box-body"> <?php echo $building_rules; ?> </div>
      </div>
    </div>
	
	
	
  </div>
</section>
<!-- /.content -->
<script type="text/javascript">
  if($('#salesChart').length > 0){
  // Get context with jQuery - using jQuery's .get() method.
  var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
  // This will get the first returned node in the jQuery collection.
  var salesChart       = new Chart(salesChartCanvas);

  var salesChartData = {
    labels  : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [
      {
        label               : 'Bill Report',
        fillColor           : 'rgba(0, 166, 90, 1)',
        strokeColor         : 'rgba(0, 166, 90, 1)',
        pointColor          : '#00a65a',
        pointStrokeColor    : 'rgba(0, 166, 90, 1)',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(0, 166, 90, 1)',
        data                : [<?php echo $monthly_visitor_graph; ?>]
      }
    ]
  };

  var salesChartOptions = {
    // Boolean - If we should show the scale at all
    showScale               : true,
    // Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines      : true,
    // String - Colour of the grid lines
    scaleGridLineColor      : 'rgba(0,0,0,.05)',
    // Number - Width of the grid lines
    scaleGridLineWidth      : 1,
    // Boolean - Whether to show horizontal lines (except X axis)
    scaleShowHorizontalLines: true,
    // Boolean - Whether to show vertical lines (except Y axis)
    scaleShowVerticalLines  : true,
    // Boolean - Whether the line is curved between points
    bezierCurve             : true,
    // Number - Tension of the bezier curve between points
    bezierCurveTension      : 0.3,
    // Boolean - Whether to show a dot for each point
    pointDot                : true,
    // Number - Radius of each point dot in pixels
    pointDotRadius          : 4,
    // Number - Pixel width of point dot stroke
    pointDotStrokeWidth     : 1,
    // Number - amount extra to add to the radius to cater for hit detection outside the drawn point
    pointHitDetectionRadius : 20,
    // Boolean - Whether to show a stroke for datasets
    datasetStroke           : true,
    // Number - Pixel width of dataset stroke
    datasetStrokeWidth      : 2,
    // Boolean - Whether to fill the dataset with a color
    datasetFill             : true,
    // String - A legend template
    tooltipTemplate: "<%if (label){%><%=label %>: <%}%><%= value + ' Visitor' %>",
	//legendTemplate          : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<datasets.length; i++){%><li><span style=\'background-color:<%=datasets[i].lineColor%>\'></span><%=datasets[i].label%></li><%}%></ul>',
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio     : true,
    // Boolean - whether to make the chart responsive to window resizing
    responsive              : true
  };

  // Create the line chart
  salesChart.Line(salesChartData, salesChartOptions);

  // ---------------------------
  // - END MONTHLY SALES CHART -
  // ---------------------------
  }
  
  var loadFile_1 = function(event,cid) {
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
  	var iAnswer = confirm("<?php echo $complain_lang['confirm']; ?>");
	if(iAnswer){
		window.location = '<?php echo WEB_URL; ?>e_dashboard/complain.php?id=' + Id;
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
				alert('<?php echo $complain_lang['solution_success_msg']; ?>');
				window.location.reload();
			 }
			 else{
				window.location.href = '../index.php';
			 }
			}
		});
	} else {
		alert('<?php echo $complain_lang['solution_label_required']; ?>');
	}
  }

</script>
<?php include('footer.php'); ?>
