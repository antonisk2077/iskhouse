<?php
ob_start();
session_start();
include("../config.php");
if(!isset($_SESSION['objLogin'])){
	header("Location: ".WEB_URL."logout.php");
	die();
}

include(ROOT_PATH.'partial/report_top_common.php');
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_fair_info_all.php');
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_common.php');
include(ROOT_PATH.'library/helper.php');
$ams_helper = new ams_helper();

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $building_name; ?></title>
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<?php include(ROOT_PATH.'/partial/header_script.php'); ?>
<script type="text/javascript">
function printContent(area,title){
	$("#"+area).printThis({
		 pageTitle: title
	});
}
</script>
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<section class="content">
<!-- Main content -->
<div id="printable">
  <div align="center" style="margin:20px 50px 50px 50px;">
    <input type="hidden" id="web_url" value="<?php echo WEB_URL; ?>" />
    <div class="row">
      <div class="col-xs-12">
	  	<?php include(ROOT_PATH.'partial/report_top_title.php'); ?>
        <div class="box box-success">
		  <div class="box-header">
            <h3 style="text-decoration:underline;font-weight:bold;color:#000" class="box-title">Income from rent</h3>
          </div>
          <div class="box-body">
            <div style="overflow:auto;">
			<table style="font-size:13px;" class="table sakotable table-bordered table-striped dt-responsive">
              <thead>
                <tr>
                  <th><?php echo $_data['text_2'];?></th>
                  <th><?php echo $_data['text_3'];?></th>
                  <th><?php echo $_data['text_4'];?></th>
                  <th><?php echo $_data['text_5'];?></th>
                  <th><?php echo $_data['text_6'];?></th>
                  <th><?php echo $_data['text_5'];?></th>
                  <th><?php echo $_data['text_77'];?></th>
                  <th><?php echo $_data['text_8'];?></th>
                  <th><?php echo $_data['text_14'];?></th>
                  <th><?php echo $_data['text_15'];?></th>
                </tr>
              </thead>
              <tbody>
                <?php

                  function year_gt ($year_id){
                    global $link;
                    $year = (int) $_GET['yid'];
                    $Select_y = mysqli_query($link,"SELECT * FROM `tbl_add_year_setup` WHERE y_id = '$year_id' ");
                    $year_ = mysqli_fetch_assoc($Select_y)['xyear'];
                    return $year_;
                }
				$rent_per_month_sub_total = 0;
				$gas_per_month_sub_total = 0;
				$electric_per_month_sub_total = 0;
				$water_per_month_sub_total = 0;
				$security_per_month_sub_total = 0;
				$utility_per_month_sub_total = 0;
				$other_per_month_sub_total = 0;
				$total_per_month_sub_total = 0;
				
				$query = "select *,r.r_name,o.o_name,fl.floor_no,u.unit_no,m.month_name from tbl_add_fair f left join tbl_add_rent r on r.rid = f.rid left join tbl_add_owner o on o.ownid = f.rid inner join tbl_add_floor fl on fl.fid = f.floor_no inner join tbl_add_unit u on u.uid = f.unit_no inner join tbl_add_month_setup m on m.m_id = f.month_id WHERE f.branch_id = '" . (int)$_SESSION['objLogin']['branch_id'] . "'";
				if(!empty($_GET['fid'])){
					$query .=" and f.floor_no='".$_GET['fid']."'";
				}
				if(!empty($_GET['uid'])){
					$query .=" and f.unit_no='".$_GET['uid']."'";
				}
				if(!empty($_GET['mid'])){
					$query .=" and f.month_id='".$_GET['mid']."'";
				}
				if(!empty($_GET['yid'])){
					$query .=" and f.xyear='".year_gt($_GET['yid'])."'";
				}
				if($_GET['ps'] != ''){
					$query .=" and f.bill_status='".$_GET['ps']."'";
				}
				$result = mysqli_query($link,$query);
				while($row = mysqli_fetch_array($result)){$rent_per_month_sub_total +=(float)$row['rent'];
				$gas_per_month_sub_total +=(float)$row['gas_bill'];
				$electric_per_month_sub_total +=(float)$row['electric_bill'];
				$water_per_month_sub_total +=(float)$row['water_bill'];
				$security_per_month_sub_total +=(float)$row['security_bill'];
				$utility_per_month_sub_total +=(float)$row['utility_bill'];
				$other_per_month_sub_total +=(float)$row['other_bill'];
				$total_per_month_sub_total +=(float)$row['total_rent'];
				?>
                <tr>
                    <td><?php echo $row['issue_date']; ?></td>
                    <td><?php if($row['type']=='Rented'){echo $row['r_name'];} else{echo $row['o_name'];} ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['floor_no']; ?></td>
                    <td><?php echo $row['unit_no']; ?></td>
                    <td><?php echo $row['month_name']; ?></td>
					<td><?php echo $row['xyear']; ?></td>
					<td><?php echo $ams_helper->currency($localization, $row['rent']); ?></td>
					<td><?php echo $ams_helper->currency($localization, $row['other_bill']); ?></td>
					<td><?php echo $ams_helper->currency($localization, $row['total_rent']); ?></td>
                </tr>
                <?php } //mysqli_close($link);$link = NULL; ?>
              </tbody>
              <tfoot>
                <tr>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
				  <th>&nbsp;</th>
                  <th style="color:red;"><?php echo $ams_helper->currency($localization, $rent_per_month_sub_total); ?></th>
				  <th style="color:red;"><?php echo $ams_helper->currency($localization, $other_per_month_sub_total); ?></th>
				  <th style="color:red;"><?php echo $ams_helper->currency($localization, $total_per_month_sub_total); ?></th>
                </tr>
              </tfoot>
            </table>
			</div>
            <div style="overflow:auto;">
		  <div class="box-header">
                <h3 style="text-decoration:underline;font-weight:bold;color:#000" class="box-title">Manintance cost</h3>
          </div>
                
			<table style="font-size:13px;" class="table sakotable table-bordered table-striped dt-responsive">
              <thead>
                <tr>
                  <th><?php echo $_data['text_2'];?></th>
                  <th>Maintenance Title</th>
                  <th>Month</th>
                  <th>Year</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php
              
				$rent_per_month_sub_total = 0;
				$gas_per_month_sub_total = 0;
				$electric_per_month_sub_total = 0;
				$water_per_month_sub_total = 0;
				$security_per_month_sub_total = 0;
				$utility_per_month_sub_total = 0;
				$other_per_month_sub_total = 0;
				$total_per_month_sub_total_1 = 0;
				
				$query_1 = "SELECT * FROM `tbl_add_maintenance_cost` WHERE branch_id = '" . (int)$_SESSION['objLogin']['branch_id'] . "'";
				if(!empty($_GET['fid'])){
					$query_1 .=" and f.floor_no='".$_GET['fid']."'";
				}
				if(!empty($_GET['uid'])){
					$query_1 .=" and f.unit_no='".$_GET['uid']."'";
				}
				if(!empty($_GET['mid'])){
					$query_1 .=" and xmonth='".$_GET['mid']."'";
				}
				if(!empty($_GET['yid'])){
					$query_1 .=" and xyear='".$_GET['yid']."'";
				}
				if($_GET['ps'] != ''){
					$query_1 .=" and f.bill_status='".$_GET['ps']."'";
				}
				$result_1 = mysqli_query($link,$query_1);
				while($row = mysqli_fetch_array($result_1)){
				    $rent_per_month_sub_total +=(float)$row['rent'];
				//$gas_per_month_sub_total +=(float)$row['gas_bill'];
				//$electric_per_month_sub_total +=(float)$row['electric_bill'];
				//$water_per_month_sub_total +=(float)$row['water_bill'];
				//$security_per_month_sub_total +=(float)$row['security_bill'];
				//$utility_per_month_sub_total +=(float)$row['utility_bill'];
				//$other_per_month_sub_total +=(float)$row['other_bill']; 
				$total_per_month_sub_total_1 +=(float)$row['m_amount'];
				?>
                <tr>
                    <td><?php echo $row['m_title']; ?></td>
                    <td><?php echo $row['m_date'];?></td>
                    <td><?php echo $row['xmonth']; ?></td>
                    <td><?php echo year_gt($row['xyear']); ?></td>
                    <td><?php echo $ams_helper->currency($localization,$row['m_amount']); ?></td>
                </tr>
                <?php } //mysqli_close($link);$link = NULL; ?>
              </tbody>
              <tfoot>
                <tr>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
				  <th style="color:red;"><?php echo $ams_helper->currency($localization, $total_per_month_sub_total_1); ?></th>
                </tr>
              </tfoot>
            </table>
			</div>
            <div style="overflow:auto;">
		  <div class="box-header">
            <h3 style="text-decoration:underline;font-weight:bold;color:#000" class="box-title">Employee Salary Report</h3>
          </div>
			<table style="font-size:13px;" class="table sakotable table-bordered table-striped dt-responsive">
              <thead>
                <tr>
                  <th><?php echo $_data['text_2'];?></th>
                  <th><?php echo $_data['text_3'];?></th>
                  <th><?php echo $_data['text_4'];?></th>
                  <th><?php echo $_data['text_5'];?></th>
                  <th><?php echo $_data['text_6'];?></th>
                  <th><?php echo $_data['text_7'];?></th>
                </tr>
              </thead>
              <tbody>
                <?php
				$total_salary = 0;
				$query = "SELECT *,e.e_name,m.month_name,ys.xyear as year_name FROM tbl_add_employee_salary_setup es inner join tbl_add_employee e on es.emp_name = e.eid inner join tbl_add_month_setup m on m.m_id = es.month_id inner join tbl_add_year_setup ys on es.xyear = ys.y_id where es.branch_id = " . (int)$_SESSION['objLogin']['branch_id'] . "";
				if(!empty($_GET['eid'])){
					$query .=" and es.emp_name='".$_GET['eid']."'";
				}
				if(!empty($_GET['mid'])){
					$query .=" and es.month_id='".$_GET['mid']."'";
				}
				if(!empty($_GET['yid'])){
					$query .=" and es.xyear='".$_GET['yid']."'";
				}
				$query .=" order by es.emp_id ASC";
				$result = mysqli_query($link,$query);
				while($row = mysqli_fetch_array($result)){
				$total_salary +=(float)$row['amount'];
				?>
                <tr>
                    <td><?php echo $row['issue_date']; ?></td>
                    <td><?php echo $row['e_name']; ?></td>
                    <td><?php echo $row['designation']; ?></td>
                    <td><?php echo $row['month_name']; ?></td>
                    <td><?php echo $row['year_name']; ?></td>
                    <td><?php echo $ams_helper->currency($localization, $row['amount']); ?></td>
                </tr>
                <?php } mysqli_close($link);$link = NULL; ?>
              </tbody>
              <tfoot>
                <tr>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th style="color:red;"><?php echo $ams_helper->currency($localization, $total_salary); ?></th>
                </tr>
              </tfoot>
            </table>
			</div>
			<div>
			    <?php 
			    $expense = $total_per_month_sub_total - $total_per_month_sub_total_1 - $total_salary
			    ?>
			    <h3>Total Income Report : <span style="color:red;"><?php echo $ams_helper->currency($localization,$expense); ?></span></h3>
			</div>    
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /.row -->
<div align="center" style="position:fixed;top:0;right:0;margin:10px;"><a title="<?php echo $_data['text_16'];?>" class="btn btn-success btn_save" onClick="javascript:printContent('printable','Fair Collection Report');" href="javascript:void(0);"><i class="fa fa-print"></i> </a></div>
</body>
</html>
