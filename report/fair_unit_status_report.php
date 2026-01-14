<?php
ob_start();
session_start();
include("../config.php");
if(!isset($_SESSION['objLogin'])){
	header("Location: ".WEB_URL."logout.php");
	die();
}

include(ROOT_PATH.'partial/report_top_common.php');
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_fair_report.php');
include(ROOT_PATH.'language/'.$lang_code_global.'/lang_common.php');
include(ROOT_PATH.'library/helper.php');
$ams_helper = new ams_helper();

$month_id = isset($_GET['mid']) ? (int)$_GET['mid'] : 0;
$year_id = isset($_GET['yid']) ? (int)$_GET['yid'] : 0;
$month_name = '';

if($month_id > 0){
	$month_result = mysqli_query($link,"SELECT month_name FROM tbl_add_month_setup WHERE m_id = ".$month_id);
	if($month_row = mysqli_fetch_array($month_result)){
		$month_name = $month_row['month_name'];
	}
}

$total_income = 0;
if($month_id > 0 && $year_id > 0){
	$income_result = mysqli_query($link,"SELECT SUM(total_rent) as total_income FROM tbl_add_fair WHERE branch_id = '" . (int)$_SESSION['objLogin']['branch_id'] . "' and month_id = ".$month_id." and xyear = ".$year_id);
	if($income_row = mysqli_fetch_array($income_result)){
		$total_income = (float)$income_row['total_income'];
	}
}
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
            <h3 style="text-decoration:underline;font-weight:bold;color:#000" class="box-title"><?php echo $_data['text_1'];?></h3>
          </div>
          <div class="box-body">
            <?php if($month_id == 0 || $year_id == 0){ ?>
              <div class="alert alert-warning"><?php echo $_data['validation']; ?></div>
            <?php } else { ?>
            <div style="margin-bottom:10px;">
              <strong><?php echo $_data['text_6']; ?>:</strong> <?php echo $month_name; ?>&nbsp;&nbsp;
              <strong><?php echo $_data['text_66']; ?>:</strong> <?php echo $year_id; ?>&nbsp;&nbsp;
              <strong><?php echo $_data['text_25']; ?>:</strong> <?php echo $ams_helper->currency($localization, $total_income); ?>
            </div>
            <div style="overflow:auto;">
              <table style="font-size:13px;" class="table sakotable table-bordered table-striped dt-responsive">
                <thead>
                  <tr>
                    <th><?php echo $_data['text_27']; ?></th>
                    <th><?php echo $_data['text_21']; ?></th>
                    <th><?php echo $_data['text_22']; ?></th>
                    <th><?php echo $_data['text_26']; ?></th>
                    <th><?php echo $_data['text_28']; ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
					$query = "SELECT u.unit_no, fr.rent as unit_rent, r.r_name as tenant_name, r.r_date as checkin_date FROM tbl_add_unit u left join (SELECT unit_no, MAX(rent) as rent FROM tbl_add_fair WHERE branch_id = '" . (int)$_SESSION['objLogin']['branch_id'] . "' and month_id = ".$month_id." and xyear = ".$year_id." GROUP BY unit_no) fr on fr.unit_no = u.uid left join tbl_add_rent r on r.r_unit_no = u.uid and r.r_status = 1 and r.branch_id = '" . (int)$_SESSION['objLogin']['branch_id'] . "' where u.branch_id = '" . (int)$_SESSION['objLogin']['branch_id'] . "' order by u.unit_no ASC";
					$result = mysqli_query($link,$query);
					while($row = mysqli_fetch_array($result)){?>
                  <tr>
                    <td><?php echo !empty($row['tenant_name']) ? $row['tenant_name'] : '-'; ?></td>
                    <td><?php echo $row['unit_no']; ?></td>
                    <td><?php echo ($row['unit_rent'] !== null) ? $_data['text_23'] : $_data['text_24']; ?></td>
                    <td><?php echo $row['unit_rent'] !== null ? $ams_helper->currency($localization, $row['unit_rent']) : '-'; ?></td>
                    <td><?php echo !empty($row['checkin_date']) ? $row['checkin_date'] : '-'; ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <?php } mysqli_close($link);$link = NULL; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /.row -->
<div align="center" style="position:fixed;top:0;right:0;margin:10px;"><a title="<?php echo $_data['print'];?>" class="btn btn-success btn_save" onClick="javascript:printContent('printable','Rental Collection Report');" href="javascript:void(0);"><i class="fa fa-print"></i> </a></div>
</body>
</html>
