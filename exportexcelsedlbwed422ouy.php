<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);

if($_SESSION['login_name'] == ""){
	echo "Unauthorized access. Please contat portal admin EVYZACA";
	exit;
}
if($_SESSION['login_category'] != "Panelist"){
	echo "Unauthorized access. Please contat portal admin EVYZACA";
	exit;
}

ini_set('memory_limit', '1024M');

include ("connection.php");
$linkid = db_connect();

include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

$dirpath="/home/mtmuser/empmgmt/employeeinfo/website/EricssonValues";
$downloadurl="eriteamtracker.egi.ericsson.com/EricssonValues/export.php?filename=";
//DEBUG-CHANGE

$exporttable = "csiemployees";

function cellColor($cells,$color){
        global $objPHPExcel;
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
        ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => $color)
        ));
}

$date =   date("Y-m-d");
$time =  str_replace(":", "_", date("H:i:s"));
$filename = "Ericsson_Values_Review_".$date."_".$time.".xls";

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Ericsson Values");
$objPHPExcel->getProperties()->setLastModifiedBy("EVYZACA");
$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Document");
$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Document");
$objPHPExcel->getProperties()->setDescription("Office 2007 XLSX, generated using PHP classes");
$objPHPExcel->setActiveSheetIndex(0);

// Prepare column herader
$col = 0;
$COLUMNLIST=array();
$RESULT = mysql_query("SELECT column_name FROM information_schema.columns WHERE table_name='$exporttable'");
if(mysql_num_rows($RESULT) > 0) {
	while ($ROW = mysql_fetch_array($RESULT)) {
		$column    = $ROW['column_name'];
		if($column != "$exporttable"."_id" and $column != 'Upload_Status'){
			array_push($COLUMNLIST, $column);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $column);
			$col++;
		}
	}
}

foreach (range(0,$col-1) as $colid) {
	$colname = PHPExcel_Cell::stringFromColumnIndex($colid);
	cellColor($colname."1", '3CB371');
        $objPHPExcel->getActiveSheet()->getColumnDimension($colname)->setAutoSize(true);
}
//print_r($COLUMNLIST);
//Fill data in the sheet
$RESULT = mysql_query("SELECT * FROM $exporttable");
if (mysql_num_rows($RESULT) > 0) {
	$row = 2;
	while ($ROW = mysql_fetch_array($RESULT)) {
		$col = 0;
		foreach($COLUMNLIST as $val){
			if(!isset($ROW[$val])){
				$ROW[$val]='';
			}
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $ROW[$val]);
	if($val == 'Professionalism_Video' || $val == 'Respect_Video' || $val == 'Perseverance_Video' || $val == 'Complete_Video'){
		if($ROW[$val] != ''){
		$objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col,$row)->getHyperlink()->setUrl("http://$downloadurl".$ROW[$val]);
		}
	}

			$col++;
		}
		$row++;
	}
}
// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Upload List');
//=============================================
// Save Excel 2007 file
$objPHPExcel->setActiveSheetIndex(0);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("$dirpath/$filename");

print $filename;

unset($objPHPExcel);
unset($objWriter);
exit;

//Ends here
