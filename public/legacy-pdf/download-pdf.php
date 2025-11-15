<?php 
$misccharge =	0;
$nre		=	0;
require_once('conn.php');
/*require('../pdfclass/html2fpdf.php'); */
require('../pdftest/html2pdf.class.php');
session_start();

$sqs = "select * from order_tb where ord_id='".$_REQUEST['id']."'";
$ress = mysql_query($sqs) or die('errorn in data');
$rws = mysql_fetch_array($ress);

/* Special requirement list array */
$option_arr = array();
$optcount = 0;

//$newdate = '09/23/2013';//Date for deciding new quotes

$innerCopper	= $rws['inner_copper'];
$startCuz		= $rws['start_cu'];
$fingers_gold	= $rws['fingers_gold'];

if($innerCopper != '' && $innerCopper != 'N/A') {
	$optcount++;
	$option_arr[$optcount] = $innerCopper.' Oz. Cu Internal';
}

if($startCuz != '') {
	$optcount++;
	$option_arr[$optcount] = $startCuz.' Oz. Cu External';
}

//($innerCopper != '' || $startCuz != '') && 
if( $fingers_gold  ==  'yes') {
	$optcount++;
	$option_arr[$optcount] = 'Nickel/Hard Gold Fingers';
}

$plated_cu	= trim($rws['plated_cu']);
$trace_min	= trim($rws['trace_min']);
$space_min	= trim($rws['space_min']);

if($plated_cu != "") {
	$optcount++;
	$option_arr[$optcount] = $plated_cu." Plated Cu in Holes (Min.)";
}

if($trace_min != "" && $trace_min != "Other") {
	$optcount++;
	$option_arr[$optcount] = "Trace Min. = ".$trace_min;
}

if($space_min != "" && $space_min != "Other") {
	$optcount++;
	$option_arr[$optcount] = "Space Min. = ".$space_min;
}

$hole_size	= trim($rws['hole_size']);
$small_pad	= trim($rws['pad']);
$hdi_design = trim($rws['hdi_design']);
$blind		= trim($rws['blind']);
$buried		= trim($rws['buried']);
$bb_both	= trim($rws['bb_both']);
$cond_vias	= trim($rws['cond_vias']);
$resin_filled	= trim($rws['resin_filled']);

if($hole_size != "") {
	$optcount++;
	$option_arr[$optcount] = "Smallest Hole Size = ".$hole_size;
}

if($small_pad != "") {
	$optcount++;
	$option_arr[$optcount] = "Smallest Pad = ".$small_pad;
}

if($hdi_design  ==  'Yes') {
	$optcount++;
	$option_arr[$optcount] = 'HDI Design';
}

if($bb_both  ==  'Yes' || ($blind  ==  'yes' && $buried  ==  'yes')) {
	$optcount++;
	$option_arr[$optcount] = 'Blind and Buried Vias Required';
} elseif($blind  ==  'yes') {
	$optcount++;
	$option_arr[$optcount] = 'Blind Vias Required';
} elseif($buried  ==  'yes') {
	$optcount++;
	$option_arr[$optcount] = 'Buried Vias Required'; 
}
if(strtolower($resin_filled)  ==  "yes") {
	$optcount++;
	$option_arr[$optcount] = 'Non-Conductive Filled/Plated Over';
}

if(strtolower($cond_vias)  ==  "yes") {
	$optcount++;
	$option_arr[$optcount] = 'Conductive Filled Vias Required';
}

$mask_size	= trim($rws['mask_size']);
$mask_type	= trim($rws['mask_type']);
$color		= $rws['color'];
$sscolor	= trim($rws['ss_color']);
$ss_side	= trim($rws['ss_side']);
$colorcnt = 0;

if($mask_size != "" && $mask_size  ==  "Both") {
	$optcount++;
	$option_arr[$optcount] = "Mask Both Sides";
	$colorcnt = $optcount;
} else if($mask_size != "" && $mask_size  ==  '1') {
	$optcount++;
	$option_arr[$optcount] = "Mask 1 Side";
	$colorcnt = $optcount;
} /*else if($mask_size  ==  "N/A") {
	$optcount++;
	$option_arr[$optcount] = "No Mask Required";
}*/

if($color != '' && $mask_size != "N/A") {
	if($colorcnt == 0) $colorcnt = $optcount + 1;
	$option_arr[$colorcnt] = $color.' '.(isset($option_arr[$colorcnt]) ? $option_arr[$colorcnt] : ' Mask');
}

if($mask_size != "" && $mask_size != "N/A" && $mask_type != "") {
	$optcount++;
	$option_arr[$optcount] = $mask_type." Mask Type";
}

if($ss_side != "" && $ss_side != "N/A") {
	$optcount++;
	if($sscolor != '') {
		$option_arr[$optcount] = $sscolor;
	}	
	$option_arr[$optcount] .= ' Silkscreen '.$ss_side." Side".($ss_side > 1 ? "s" : "");
}

$route		= trim($rws['route']);
$route_tole	= trim($rws['route_tole']);

/*if($route  ==  'Yes' && $route != "") {
	$optcount++;
	$option_arr[$optcount] = "Individual Route 1-up";
}*/

//$route_tole != "+/-.005" &&
if( $route_tole != "") {
	$optcount++;
	$option_arr[$optcount] = $route_tole." Overall Dimensions Tolerance";
}

$array_design	= trim($rws['array_design']);
$design_array	= trim($rws['design_array']);
$array_type1	= trim($rws['array_type1']);
$array_type2	= trim($rws['array_type2']);
$array_type3	= trim($rws['array_type3']);
$array_require1	= trim($rws['array_require1']);
$array_require2	= trim($rws['array_require2']);
$array_require3	= trim($rws['array_require3']);

if(strtolower($array_design)  ==  'yes' && $array_design != "") {
	$optcount++;
	$option_arr[$optcount] = "Array Design Provided";
}

if(strtolower($design_array)  ==  'yes' && $design_array != "") {
	$optcount++;
	$option_arr[$optcount] = "Factory to Design Array";
}

if($array_type1 != "" || $array_type2 != "" || $array_type3 != "") {
	$array_type = array();
	if($array_type1 != "" )
		$array_type[] = $array_type1;
	if($array_type2 != "" )
		$array_type[] = $array_type2;
	if($array_type3 != "" )
		$array_type[] = $array_type3;

	$optcount++;
	$option_arr[$optcount] = implode(", ", $array_type)." Array Type";
}

if($array_require1 != "" || $array_require2 != "" || $array_require3 != "") {
	$array_require = array();
	if($array_require1 != "" )
		$array_require[] = $array_require1;
	if($array_require2 != "" )
		$array_require[] = $array_require2;
	if($array_require3 != "" )
		$array_require[] = $array_require3;

	$optcount++;
	$option_arr[$optcount] = "Array Requires ".implode(", ", $array_require);
}

$miling		= trim($rws['bevel']);
$cDepth		= trim($rws['cut_outs']);
$ePlating	= trim($rws['slots']);
$csink		= trim($rws['counter_sink']);

if($miling  ==  'yes') {
	$optcount++;
	$option_arr[$optcount] = 'Miling Required';
}

if($csink  ==  'yes') {
	$optcount++;
	$option_arr[$optcount] = 'Countersink Required';
}
	
if($cDepth  ==  'Yes') {
	$optcount++;
	$option_arr[$optcount] = 'Control Depth Required';
}

if($ePlating  ==  'Yes') {
	$optcount++;
	$option_arr[$optcount] = 'Edge Plating Required';
}

$logo			= trim($rws['logo']);
$mark			= trim($rws['mark']);
$date_code		= trim($rws['date_code']);
$other_marking	= trim($rws['other_marking']);

if($logo != '' && $logo != 'None' && $logo != "Other")  {
	$optcount++;
	$option_arr[$optcount] = $logo." Logo";
}

if(strtolower($mark)  ==  'yes')  {
	$optcount++;
	$option_arr[$optcount] = "94V-0 Mark";
}

if($date_code != '' && $date_code != 'None'  && $date_code != 'Other Marking') {
	$optcount++;
	$option_arr[$optcount] = $date_code." Date Code Format";
} else if($date_code  ==  'Other Marking' && $other_marking != '') {
	$optcount++;
	$option_arr[$optcount] = $other_marking." Date Code Format";
}

$micro_section	= trim($rws['micro_section']);
$test_stamp		= trim($rws['test_stamp']);
$in_board		= trim($rws['in_board']);
$array_rail		= trim($rws['array_rail']);

if(strtolower($micro_section)  ==  'yes') {
	$optcount++;
	$option_arr[$optcount] = "Microsection Required";
}

if(strtolower($test_stamp)  ==  'yes')  {
	$optcount++;
	if($in_board != "") {
		$option_arr[$optcount] = $in_board." Electrical Test Stamp";
		if($array_rail != "")
			$option_arr[$optcount] = $in_board.' and '.$array_rail.' Electrical Test Stamp';
	}
	else {
		if($array_rail != "")
			$option_arr[$optcount] = $array_rail.' Electrical Test Stamp';
		else $option_arr[$optcount] = "Electrical Test Stamp";
	}
}

$xoutsallowed	= $rws['xouts'];
$numerxouts		= $rws['xouts1'];
$rosh_cert		= trim($rws['rosh_cert']);

/*if($xoutsallowed !='' && $xoutsallowed != 'yes') {
	$optcount++;
	$option_arr[$optcount] = ucfirst($xoutsallowed).' X-Outs Allowed';
} else*/ if($xoutsallowed != '' && $xoutsallowed  ==  'yes') {
	$optcount++;
	$option_arr[$optcount] = $numerxouts.' X-Out'.($numerxouts > 1 ? 's':'').' Allowed per Array';
} 

if(strtolower($rosh_cert)  ==  'yes') {
	$optcount++;
	$option_arr[$optcount] = "RoHS Cert Required";
}

if(strlen(trim(strip_tags($rws['special_instadmin']))) > 5) {
	$sp_intadmin_arr = explode('<br />', $rws['special_instadmin']);
	if(count($sp_intadmin_arr) > 1) {
		foreach ($sp_intadmin_arr as $sk => $sv) {
			if(trim(strip_tags($sv)) != "") {
				//echo trim(strip_tags($sv))."<br>";
				$optcount++;
				$option_arr[$optcount] = trim(strip_tags($sv));
			}
		}
	}
	else {
		$sp_intadmin_arr = explode('<p>', $rws['special_instadmin']);
		if(count($sp_intadmin_arr) > 0) {
			foreach ($sp_intadmin_arr as $sk => $sv) {
				if(trim(strip_tags($sv)) != "") {
					//echo trim(strip_tags($sv))."<br>";
					$optcount++;
					$option_arr[$optcount] = trim(strip_tags($sv));
				}
			}
		}
	}
}

$sp_reqs = $rws['sp_reqs'];
if(trim($sp_reqs) != "") {
	$temp_sp_reqs = explode('|', trim($sp_reqs));
	if(count($temp_sp_reqs) > 0) {
		foreach($temp_sp_reqs as $ks => $vs) {
			$optcount++;
			$option_arr[$optcount] = trim($vs);
		}
	}
}

/************************************************************/

$rest = substr($rws['cust_name'], -2);  

if ($rest  ==  '-C') {
	$rest1 = substr($rws['cust_name'], 0, -2);  // returns "abcde"
	$sqs2 = "select * from data_tb where c_name='".$rest1."'";
}
else {
	$sqs2 = "select * from data_tb where c_name='".$rws['cust_name']."'";
}
/*echo $sqs2;
exit(0);*/
$ress2 = mysql_query($sqs2) or die('error in data');
$rws2 = mysql_fetch_array($ress2);

?>
<?php
$path = "/home/pareomic/public_html/pcbsglobal.com/luke-pdf/upload/";

$html2pdf = new HTML2PDF('P','A4','en');

/*$pdf=new HTML2FPDF();
*/
/*$pdf->AddPage();
*/
/*calcu prices*/


$bprice=2;
$leadtm = $rws['day1'];
//echo $leadtm;
if($leadtm == 7 ) {
	$d1=$bprice;
}
if($leadtm == 1 ) {
	$d1=$bprice+($bprice*(250/100));
}
if($leadtm == 2 ) {
	$d1=$bprice+($bprice*(200/100));
}
if($leadtm == 3 ) {
	$d1=$bprice+($bprice*(150/100));
}
if($leadtm == 4 ) {
	$d1=$bprice+($bprice*(100/100));
}
//echo $d1;
// == = 2
$leadtm = $rws['day2'];
if($leadtm == 7 ) {
	$d2=$bprice;
}
if($leadtm == 1 ) {
	$d2=$bprice+($bprice*(250/100));
}
if($leadtm == 2 ) {
	$d2=$bprice+($bprice*(200/100));
}
if($leadtm == 3 ) {
	$d2=$bprice+($bprice*(150/100));
}
if($leadtm == 4 ) {
	$d2=$bprice+($bprice*(100/100));
}

// ==  == 3  ==  == =
$leadtm = $rws['day3'];
if($leadtm == 7 ) {
	$d3=$bprice;
}
if($leadtm == 1 ) {
	$d3=$bprice+($bprice*(250/100));
}
if($leadtm == 2 ) {
	$d3=$bprice+($bprice*(200/100));
}
if($leadtm == 3 ) {
	$d3=$bprice+($bprice*(150/100));
}
if($leadtm == 4 ) {
	$d3=$bprice+($bprice*(100/100));
}
// 4 

$leadtm = $rws['day4'];
if($leadtm == 7 ) {
	$d4=$bprice;
}
if($leadtm == 1 ) {
	$d4=$bprice+($bprice*(250/100));
}
if($leadtm == 2 ) {
	$d4=$bprice+($bprice*(200/100));
}
if($leadtm == 3 ) {
	$d4=$bprice+($bprice*(150/100));
}
if($leadtm == 4 ) {
	$d4=$bprice+($bprice*(100/100));
}


$leadtm = $rws['day5'];
if($leadtm == 7 ) {
	$d5=$bprice;
}
if($leadtm == 1 ) {
	$d5=$bprice+($bprice*(250/100));
}
if($leadtm == 2 ) {
	$d5=$bprice+($bprice*(200/100));
}
if($leadtm == 3 ) {
	$d5=$bprice+($bprice*(150/100));
}
if($leadtm == 4 ) {
	$d5=$bprice+($bprice*(100/100));
}

// end of day

$qnt1 = $rws['qty1'];
if($qnt1>=250 ) {
	$q1=$bprice;
}
if($qnt1<=100 ) {
	$q1=$bprice+($bprice*(150/100));
}
if($qnt1<=50 ) {
	$q1=$bprice+($bprice*(200/100));
}
if($qnt1<=10 ) {
	$q1=$bprice+($bprice*(250/100));
}
// 2
$qnt2 = $rws['qty2'];
if($qnt2>=250 ) {
	$q2=$bprice;
}
if($qnt2<=100 ) {
	$q2=$bprice+($bprice*(150/100));
}
if($qnt2<=50 ) {
	$q2=$bprice+($bprice*(200/100));
}
if($qnt2<=10 ) {
	$q2=$bprice+($bprice*(250/100));
}
//3
$qnt3 = $rws['qty3'];
if($qnt3>=250 ) {
	$q3=$bprice;
}
if($qnt3<=100 ) {
	$q3=$bprice+($bprice*(150/100));
}
if($qnt3<=50 ) {
	$q3=$bprice+($bprice*(200/100));
}
if($qnt2<=10 ) {
	$q3=$bprice+($bprice*(250/100));
}
// 4
$qnt4 = $rws['qty4'];
if($qnt4>=250 ) {
	$q4=$bprice;
}
if($qnt4<=100 ) {
	$q4=$bprice+($bprice*(150/100));
}
if($qnt4<=50 ) {
	$q4=$bprice+($bprice*(200/100));
}
if($qnt4<=10 ) {
	$q4=$bprice+($bprice*(250/100));
}

$qnt5 = $rws['qty5'];
if($qnt5>=250 ) {
	$q5=$bprice;
}
if($qnt5<=100 ) {
	$q5=$bprice+($bprice*(150/100));
}
if($qnt5<=50 ) {
	$q5=$bprice+($bprice*(200/100));
}
if($qnt5<=10 ) {
	$q5=$bprice+($bprice*(250/100));
}

$qnt6 = $rws['qty6'];
if($qnt6>=250 ) {
	$q6=$bprice;
}
if($qnt6<=100 ) {
	$q6=$bprice+($bprice*(150/100));
}
if($qnt6<=50 ) {
	$q6=$bprice+($bprice*(200/100));
}
if($qnt6<=10 ) {
	$q6=$bprice+($bprice*(250/100));
}

$qnt7 = $rws['qty7'];
if($qnt7>=250 ) {
	$q7=$bprice;
}
if($qnt7<=100 ) {
	$q7=$bprice+($bprice*(150/100));
}
if($qnt7<=50 ) {
	$q7=$bprice+($bprice*(200/100));
}
if($qnt7<=10 ) {
	$q7=$bprice+($bprice*(250/100));
}

$qnt8 = $rws['qty8'];
if($qnt8>=250 ) {
	$q8=$bprice;
}
if($qnt8<=100 ) {
	$q8=$bprice+($bprice*(150/100));
}
if($qnt8<=50 ) {
	$q8=$bprice+($bprice*(200/100));
}
if($qnt8<=10 ) {
	$q8=$bprice+($bprice*(250/100));
}

$qnt9 = $rws['qty9'];
if($qnt9>=250 ) {
	$q9=$bprice;
}
if($qnt9<=100 ) {
$q9=$bprice+($bprice*(150/100));
}
if($qnt9<=50 ) {
	$q9=$bprice+($bprice*(200/100));
}
if($qnt9<=10 ) {
	$q9=$bprice+($bprice*(250/100));
}

$qnt10 = $rws['qty10'];
if($qnt10>=250 ) {
$q10=$bprice;
}
if($qnt10<=100 ) {
	$q10=$bprice+($bprice*(150/100));
}
if($qnt10<=50 ) {
	$q10=$bprice+($bprice*(200/100));
}
if($qnt10<=10 ) {
	$q10=$bprice+($bprice*(250/100));
}

$baseprice = $rws['price'];

/*
if($_REQUEST['chk2'] == 'Double Sided' ) {
if(isset($_REQUEST['per1']) && $_REQUEST['per1'] != 0)
$baseprice=$baseprice+($baseprice*($_REQUEST['per1']/100));
}

if($_REQUEST['chk3'] == '4Lyrs' ) {
$baseprice = $baseprice + ($baseprice*(10/100));
}
if($_REQUEST['chk4'] == '6Lyrs' ) {
$baseprice = $baseprice + ($baseprice*(20/100));
}
if($_REQUEST['chk5'] == '8Lyrs' ) {
$baseprice = $baseprice + ($baseprice*(30/100));
}
if($_REQUEST['chk6'] == '10Lyrs' ) {
$baseprice = $baseprice + ($baseprice*(40/100));
}

*/

$pr1=$d1+$q1+$baseprice;  	$pr2=$d2+$q1+$baseprice;	$pr3=$d3+$q1+$baseprice;	$pr4=$d4+$q1+$baseprice;    $pr5=$d5+$q1+$baseprice;

$pr6=$d1+$q2+$baseprice;  	$pr7=$d2+$q2+$baseprice;	$pr8=$d3+$q2+$baseprice;	$pr9=$d4+$q2+$baseprice;    
$pr10 = $d5+$q2+$baseprice;

$pr11=$d1+$q3+$baseprice;  	$pr12=$d2+$q3+$baseprice;	$pr13=$d3+$q3+$baseprice;	$pr14=$d4+$q3+$baseprice;    $pr15=$d5+$q3+$baseprice;

$pr16=$d1+$q4+$baseprice;  	$pr17=$d2+$q4+$baseprice;	$pr18=$d3+$q4+$baseprice;	$pr19=$d4+$q4+$baseprice;    $pr20=$d5+$q4+$baseprice;

$pr21=$d1+$q5+$baseprice;     $pr22=$d2+$q5+$baseprice;     $pr23=$d3+$q5+$baseprice;     $pr24=$d4+$q5+$baseprice;    $pr25=$d5+$q5+$baseprice;

$pr26=$d1+$q6+$baseprice;     $pr27=$d2+$q6+$baseprice;     $pr28=$d3+$q6+$baseprice;     $pr29=$d4+$q6+$baseprice;    $pr30=$d5+$q6+$baseprice;

$pr31=$d1+$q7+$baseprice;     $pr32=$d2+$q7+$baseprice;     $pr33=$d3+$q7+$baseprice;     $pr34=$d4+$q7+$baseprice;    $pr35=$d5+$q7+$baseprice;

$pr36=$d1+$q8+$baseprice;     $pr37=$d2+$q8+$baseprice;     $pr38=$d3+$q8+$baseprice;     $pr39=$d4+$q8+$baseprice;    $pr40=$d5+$q8+$baseprice;

$pr41=$d1+$q9+$baseprice;     $pr42=$d2+$q9+$baseprice;     $pr43=$d3+$q9+$baseprice;     $pr44=$d4+$q9+$baseprice;    $pr45=$d5+$q9+$baseprice;

$pr46 = $d1+$q10+$baseprice;     
$pr47 = $d2+$q10+$baseprice;     
$pr48 = $d3+$q10+$baseprice;     
$pr49 = $d4+$q10+$baseprice;    
$pr50 = $d5+$q10+$baseprice;

$baseprice = round($baseprice, 2);

$pr1 = $rws['pr11'];
$pr2 = $rws['pr12'];
$pr3 = $rws['pr13'];
$pr4 = $rws['pr14'];
$pr5 = $rws['pr15'];

$pr6 = $rws['pr21'];
$pr7 = $rws['pr22'];
$pr8 = $rws['pr23'];
$pr9 = $rws['pr24'];
$pr10 = $rws['pr25'];

$pr11 = $rws['pr31'];
$pr12 = $rws['pr32'];
$pr13 = $rws['pr33'];
$pr14 = $rws['pr34'];
$pr15 = $rws['pr35'];

$pr16 = $rws['pr41'];
$pr17 = $rws['pr42'];
$pr18 = $rws['pr43'];
$pr19 = $rws['pr44'];
$pr20 = $rws['pr45'];

$pr21 = $rws['pr51'];
$pr22 = $rws['pr52'];
$pr23 = $rws['pr53'];
$pr24 = $rws['pr54'];
$pr25 = $rws['pr55'];

$pr26 = $rws['pr61'];
$pr27 = $rws['pr62'];
$pr28 = $rws['pr63'];
$pr29 = $rws['pr64'];
$pr30 = $rws['pr65'];

$pr31 = $rws['pr71'];
$pr32 = $rws['pr72'];
$pr33 = $rws['pr73'];
$pr34 = $rws['pr74'];
$pr35 = $rws['pr75'];

$pr36 = $rws['pr81'];
$pr37 = $rws['pr82'];
$pr38 = $rws['pr83'];
$pr39 = $rws['pr84'];
$pr40 = $rws['pr85'];

$pr41 = $rws['pr91'];
$pr42 = $rws['pr92'];
$pr43 = $rws['pr93'];
$pr44 = $rws['pr94'];
$pr45 = $rws['pr95'];

$pr46 = $rws['pr101'];
$pr47 = $rws['pr102'];
$pr48 = $rws['pr103'];
$pr49 = $rws['pr104'];
$pr50 = $rws['pr105'];

for($m = 1; $m <= 50; $m++) {
	$var = 'pr'.$m;
	if($$var != "") {
		$$var = format_num($$var);
	}
}

$qno = intval($_REQUEST['id']) + 10000;

if ($rws['no_layer'] == 'single')
$nol = '1';
else if ($rws['no_layer'] == 'Double Sided')
$nol = '2';
else if ($rws['no_layer'] == '4Lyrs')
$nol = '4';
else if ($rws['no_layer'] == '6Lyrs')
$nol = '6';
else if ($rws['no_layer'] == '8Lyrs')
$nol = '8';
else if ($rws['no_layer'] == '10Lyrs')	
$nol = '10';
else 
$nol = $rws['no_layer']. '';

$message  = '<page><br>
<table width="1500px" border="0" >
<tr> 
	<td style="width:200px"><img src="http://pcbsglobal.com/luke-new/admin/images/logo.jpg" width="50px" height="40px" style="width:160px; height:180px; " alt="PCBsGlobal Inc. Logo"></td>  
	<td style="width:950px" style="vertical-align:top">

	<h1 style="font-size: 40pt;color:#5660B1">  Quotation &nbsp;&nbsp;</h1>

	<strong>PCBs Global Incorporated.</strong><br>

	Phone (855) 722-7456<br> 
	Fax: (855) 262-5305 <br>
	 sales@pcbsglobal.com <br>
		Quote Prepared By: '.$_SESSION['uname'].'
	</td>	

	<td style="vertical-align:top">	
	<b>Quote No : </b>'.$qno.' <br>
	<b>Quotation Date: </b>  '.$rws[ord_date] .' <br>
	<b>Quote Valid for : </b> 30 Days <br><br><br><br>
	<strong>Quote To:</strong><br>'.$rws2['c_name'].'<br>'.$rws['req_by'].'<br>'.$rws['phone'].'<br> 
	'.$rws['email'].'</td>

</tr>
</table>

	<table border="0" >

	<tr style="background-color:#656BBC; color:#FFF; text-align: center; font-size: 18pt;">
		<td width="750" height="25" ><b>Order Information</b></td>
	</tr>

	<tr><td height="3px" ></td></tr>

	<tr>
		<td><b>Part Number: </b>'.$rws[part_no].' &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; <b>Revision: </b>'.$rws[rev].'&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<b> '.$rws[new_or_rep].' </b>';
	 	
	if ($rws['cancharge'] == 'yes') {
		$message  = $message.' &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; <b>Cancellation Charge: </b> $ '.$rws[ccharge];
	}
		
	$message  = $message.'</td></tr>
	<tr><td height="3px" ></td></tr>
	</table>';

if($rws['simplequote'] != '1') {

$message  = $message.'
<table  border="1" cellspacing="0">
  <tr>
    <td style="width:130px" >PCB Type: <strong>'.$nol.'</strong> Lyr</td>
	<td style="width:165px" >Material: <strong>'.$rws[m_require].'</strong></td>
    <td style="width:150px">Thick: <strong>'.$rws[thickness].'</strong>';
	
	
	if($rws['thickness_tole'] != '')
	$message  = $message.' '.$rws['thickness_tole'];
	
	$pieces = explode("/", $rws[start_cu] );
	
	$pieces2 = explode("/", $rws[inner_copper] );
	
	/*$message  = $message.'
	</td>
	
	<td style="width:180px">&nbsp;Cu: '.$pieces[0].'&nbsp;Oz O/Ls&nbsp;&nbsp;&nbsp;';
	
	if (($nol == '1')or($nol == '2'))
	$message  = $message;
	else 
	$message  = $message. $pieces2[0].'&nbsp;Oz I/Ls ';
	*/

	$message  = $message.'
	</td>
	
	<td style="width:130px">FOB:  <strong>'.($rws['fob'] == 'Other' ? $rws['fob_oth'] : $rws['fob']).'</strong>&nbsp;';	
	
	$message  = $message.'	
	</td>
   <td style="width:110px">IPC Class:  <strong>'.$rws[ipc_class].'</strong></td>
  </tr>
  <tr>
    <td colspan="2">Array Info: ';
	
	
	if($rws['array'] == 'YES')
	$message  = $message.'	<strong>'.$rws[array_size1].' X '.$rws[array_size2].' ('.$rws[b_per_array].') Up</strong> ';
	
	$message  = $message.'
	
	</td>
    <td>Bd size: <strong>'.$rws[board_size1].' X '.$rws[board_size2].'</strong>&nbsp;</td>
    <td>Imp: <strong>';
	
	
	if ( ($rws[con_impe_sing] == '') and ($rws[con_impe_diff] == ''))
	$message  = $message.'';
	else if ( ($rws[con_impe_sing] != '') and ($rws[con_impe_diff] != ''))
	$message  = $message.'&nbsp;SE/Diff&nbsp;&nbsp;&nbsp;&nbsp;Tol:&nbsp;'.$rws[tore_impe];
	else if ($rws[con_impe_sing] != '')
	$message  = $message.'&nbsp;SE&nbsp;&nbsp;&nbsp;&nbsp;Tol:&nbsp;'.$rws[tore_impe];
	else if ($rws[con_impe_diff] != '')
	$message  = $message.'&nbsp;Diff&nbsp;&nbsp;&nbsp;&nbsp;Tol:&nbsp;'.$rws[tore_impe];
	
	$message  = $message.' </strong>	
	</td>
	<td>Finish: <strong>'.$rws[finish].'</strong></td>
  </tr>
 
  <tr>
    <td colspan="5" style="padding-top:5px; "><strong>Special Requirements / Notes</strong>:<div style="width: 750px; font-size: 9pt; padding-top: 5px; padding-bottom: 10px">';  

	//echo "<PRE>"; print_r($option_arr);
	$col_count = 3;
	if(count($option_arr) > 0) {
		while ($col_count > count($option_arr) ) $col_count--;
		$per_col_items = ceil(count($option_arr) / $col_count);
		$lpad = 5;
		$tdwidth = floor((740- ($lpad*$col_count*2))/$col_count);

		$spr  = '<table border="0" cellspacing="0" cellpadding="0" border="0" style="width:740px">
		<tr>
			<td style="vertical-align: top; padding-right:'.$lpad.'px;">
			';

		$cntr = 0;

		foreach ($option_arr as $k => $v) {
			if($cntr  ==  $per_col_items) {
				$spr  .= '
				</td>
				<td style="vertical-align: top; width:'.($tdwidth-5).'px">
				';
				$cntr = 0;
			}
			$spr  .= '<table cellpadding="0" cellspacing="0" border="0" style="width: 100%" >
			<tr>
				<td style="vertical-align: top; width:12px; color:#646bbb">'.$k.'.)</td>
				<td style="width:'.($tdwidth-20).'px; vertical-align: top; text-wrap: normal; padding-left: '.$lpad.'px;">&nbsp;'.$v.'</td>
			</tr></table>
			';
			$cntr++;
		}

		$spr  .= '
		</td></tr>
		</table>';
	}

	//echo "<br>".$spr;		
	
	$message  .= $spr.'</div></td>
  </tr>
</table>
<br>';
}
else {
	if(trim($rws['comments']) != "") {
		$message  = $message.'
		<table border="1" cellspacing="0">
		<tr>
			<td width="500"><strong>Comments:</strong><br>'.$rws['comments'].'</td>
		</tr>
		</table><br>';
	}
}

if($rws['cancharge'] != 'yes') {

	$misccharge =	$rws['necharge'];
	$nre		=	$rws['descharge'];

	if($rws['simplequote'] != '1') {

		if($rws['descharge'] != 0.00 || $rws['descharge1'] != "" || $rws['descharge2'] != "" || $rws['necharge'] != 0.00) 
			$message = $message.'
			<table border="1" cellspacing="0"><tr>';

		if($rws['necharge'] != 0.00)
		$message = $message.'<td width="100">&nbsp;<b>NRE: </b>&nbsp;$'.$rws['necharge'].'</td>';

		if($rws['desdesc'] != "" && $rws['descharge'] != 0.00)
			$message = $message.'<td width="210">&nbsp;<b>'.trim($rws['desdesc']).':</b>&nbsp;$'.$rws['descharge'].'</td>';

		if($rws['desdesc1'] != "" && $rws['descharge1'] != "")
			$message = $message.'<td width="205">&nbsp;<b>'.trim($rws['desdesc1']).':</b>&nbsp;$'.$rws['descharge1'].'</td>';

		if($rws['desdesc2'] != "" && $rws['descharge2'] != "")
			$message = $message.'<td width="205">&nbsp;<b>'.trim($rws['desdesc2']).':</b>&nbsp;$'.$rws['descharge2'].'</td>';
			
		if($rws['descharge'] != 0.00 || $rws['descharge1'] != "" || $rws['descharge2'] != "" || $rws['necharge'] != 0.00)
			$message  = $message.'</tr></table><br>';	
	}
	else {
		if($rws['descharge'] != 0.00 || $rws['descharge1'] != "" || $rws['descharge2'] != "" || $rws['necharge'] != 0.00) 
			$message = $message.'
			<table border="0" cellspacing="3" style="border: 1px solid #000000" >';
		
		$ct_misc = 0;
		$tot_misc = 0;

		if($rws['desdesc'] != "" && $rws['descharge'] != 0.00) {
			$message = $message.'<tr><td width="400"><b>'.trim($rws['desdesc']).'</b></td><td width="100" align="right">$ '.$rws['descharge'].'</td></tr>';
			$ct_misc++;
			$tot_misc += $rws['descharge'];
		}

		if($rws['desdesc1'] != "" && $rws['descharge1'] != "") {
			$message = $message.'<tr><td width="400"><b>'.trim($rws['desdesc1']).'</b></td><td width="100" align="right">$ '.$rws['descharge1'].'</td></tr>';
			$ct_misc++;
			$tot_misc += $rws['descharge1'];
		}

		if($rws['desdesc2'] != "" && $rws['descharge2'] != "") {
			$message = $message.'<tr><td width="400"><b>'.trim($rws['desdesc2']).'</b></td><td width="100" align="right">$ '.$rws['descharge2'].'</td></tr>';
			$ct_misc++;
			$tot_misc += $rws['descharge2'];
		}

		if($ct_misc > 1 && $tot_misc > 0) {
			$message = $message.'<tr><td width="500" colspan="2"><hr></td></tr>
			<tr><td width="400" align="right"><b>Total</b></td><td width="100" align="right">$ '.format_num($tot_misc).'</td></tr>';
		}
			
		if($rws['descharge'] != 0.00 || $rws['descharge1'] != "" || $rws['descharge2'] != "" || $rws['necharge'] != 0.00)
			$message  = $message.'</table><br>';	
	}
}

if ($rws['cancharge'] != 'yes' && $rws['simplequote'] != '1') {
  
  $message  = $message.'
    <table style=" text-align: center;" width="1000" border="1">
	<tr>
    <td width="95"></td>
	<td width="95"></td>';
   
    if($rws['day1'] > 0)
	$message  = $message.'<td width="95">'.$rws['day1'].' Days</td>';
   
    if($rws['day2'] > 0)
	$message  = $message.'
    <td width="95">'.$rws['day2'].' Days</td>';
	
	if($rws['day3'] > 0)
	$message  = $message.'
    <td width="95">'.$rws['day3'].' Days</td>';
	
	if($rws['day4'] > 0)
	$message  = $message.'
    <td width="95">'.$rws['day4'].' Days</td>';
	
	if($rws['day5'] > 0)
	$message  = $message.'
    <td width="95">'.$rws['day5'].' Days</td>';   
    
    $message  = $message.'</tr>';
 
   if($rws['qty1'] > 0) {
 
$message  = $message.'
 <tr>
    <td>Option 1</td>
    <td>'.$rws['qty1'].' Pcs</td> ';
   
    if($rws['day1'] > 0) {
     if($pr1 > 0) {
		 $message  = $message.'<td style="text-align:left">&nbsp;$'.$pr1.' ea</td>';
	  }
	  else {
		  $message  = $message.'<td style="text-align:left">&nbsp;</td>';		
	  }
	}

	if($rws['day2'] > 0) {
	if($pr2 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr2.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}  
 
  if($rws['day3'] > 0) {
   if($pr3 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr3.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  } 
  } 
   
   if($rws['day4'] > 0) {
     if($pr4 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr4.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   } 
 
   if($rws['day5'] > 0) {
     if($pr5 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr5.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
 $message  = $message.'</tr>
 
<tr>
    <td colspan="2" style="text-align:right"><strong>'.($rws['new_or_rep'] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included&nbsp;</strong></td>';

	/*'.($rws[ord_date] > $newdate ? ($rws[new_or_rep] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included' : 'Order Total').'*/
   
  if($rws['day1'] > 0) {
  
   if($pr1 > 0) {
		  $total = ($rws['qty1'] * str_replace(",","",$pr1) ) +  $misccharge + $nre + $rws['descharge1'] + $rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }  
    }

  if($rws['day2'] > 0) {
   if($pr2 > 0) {
		  $total = ($rws['qty1'] * str_replace(",","",$pr2) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   
 }
  if($rws['day3'] > 0) {
    if($pr3 > 0) {
		  $total = ($rws['qty1'] * str_replace(",","",$pr3) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }

   if($rws['day4'] > 0) {
    if($pr4 > 0) {
		  $total = ($rws['qty1'] * str_replace(",","",$pr4) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day5'] > 0) {
    if($pr5 > 0) {
		  $total = ($rws['qty1'] * str_replace(",","",$pr5) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}
$message  = $message.'</tr>';  
 }

  if($rws['qty2'] > 0) { 
 $message  = $message.' 
  <tr>
    <td>Option 2</td>
    <td>'.$rws['qty2'].' Pcs</td>';
 if($rws['day1'] > 0)  {
   if($pr6 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr6.' ea</td>';
	  }
	  else   {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
 }
   
 if($rws['day2'] > 0)  {
   if($pr7 > 0) 	  {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr7.' ea</td>';
	  }
	  else 	  {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
 }
 
   
if($rws['day3'] > 0 ) {
	if($pr8 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr8.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}

   
if($rws['day4'] > 0 ) {
  if($pr9 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr9.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}
   
if($rws['day5'] > 0 ) {
 if($pr10 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr10.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}
 
$message  = $message.'</tr>
 
<tr>
    <td colspan="2" style="text-align:right"><strong>'.($rws[new_or_rep] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included&nbsp;</strong></td>';
 
  if($rws['day1'] > 0) {
     if($pr6 > 0) {
		 $total = ($rws['qty2'] * str_replace(",","",$pr6) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }  
    }

  if($rws['day2'] > 0) {
     if($pr7 > 0) {
		 $total = ($rws['qty2'] * str_replace(",","",$pr7) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }   
 }

  if($rws['day3'] > 0) {
      if($pr8 > 0) {
		 $total = ($rws['qty2'] * str_replace(",","",$pr8) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day4'] > 0) {
     if($pr9 > 0) {
		 $total = ($rws['qty2'] * str_replace(",","",$pr9) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }

   if($rws['day5'] > 0) {
     if($pr10 > 0) {
		 $total = ($rws['qty2'] * str_replace(",","",$pr10) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}

$message  = $message.'</tr>';
    }
	
	
   if($rws['qty3'] > 0) {
   $message  = $message.' 
   <tr>
    <td>Option 3</td>
    <td>'.$rws['qty3'].' Pcs</td>';
   
  if($rws['day1'] > 0) {
	  if($pr11 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr11.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
  }
   
  if($rws['day2'] > 0) {
	  if($pr12 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr12.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}
 
   
  if($rws['day3'] > 0) {
	  if($pr13 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr13.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
  }

  if($rws['day4'] > 0) {
	if($pr14 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr14.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
  }
   
 if($rws['day5'] > 0) {
	if($pr15 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr15.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
 }
 
$message  = $message.'</tr>
 
<tr>
    <td colspan="2" style="text-align:right"><strong>'.($rws[new_or_rep] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included&nbsp;</strong></td>';
 
  if($rws['day1'] > 0) {
   if($pr11 > 0) {
		  $total = ($rws['qty3'] * str_replace(",","",$pr11) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }  
    }

  if($rws['day2'] > 0) {
  
  if($pr12 > 0) {
		$total = ($rws['qty3'] * str_replace(",","",$pr12) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }     
 }

  if($rws['day3'] > 0) {
 
 if($pr13 > 0) {
		  $total = ($rws['qty3'] * str_replace(",","",$pr13) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day4'] > 0) {
 
 if($pr14 > 0) {
		  $total = ($rws['qty3'] * str_replace(",","",$pr14) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day5'] > 0) {
 if($pr15 > 0) {
		  $total = ($rws['qty3'] * str_replace(",","",$pr15) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}

$message  = $message.'</tr>';  
  }   
	
	
	if($rws['qty4'] > 0) {
    $message  = $message.' 
	<tr>
    <td>Option 4</td><td>'.$rws['qty4'].' Pcs</td>';
  
  if($rws['day1'] > 0) {
  if($pr16 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr16.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
  }
   
   if($rws['day2'] > 0) {
   if($pr17 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr17.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   
   if($rws['day3'] > 0) {
   if($pr18 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr18.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
 
  if($rws['day4'] > 0) {
  if($pr19 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr19.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
  }
 
   if($rws['day5'] > 0) {
   if($pr20 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr20.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }   
 
$message  = $message.'</tr> 
 
 <tr>
    <td colspan="2" style="text-align:right"><strong>'.($rws[new_or_rep] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included&nbsp;</strong></td>';
   
  if($rws['day1'] > 0) {
  
  if($pr16 > 0) {
		   $total = ($rws['qty4'] * str_replace(",","",$pr16) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }  
    }

  if($rws['day2'] > 0) {
  if($pr17 > 0) {
		   $total = ($rws['qty4'] * str_replace(",","",$pr17) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   
 }
  if($rws['day3'] > 0) {
  if($pr18 > 0) {
		   $total = ($rws['qty4'] * str_replace(",","",$pr18) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day4'] > 0) {
  if($pr19 > 0) {
		   $total = ($rws['qty4'] * str_replace(",","",$pr19) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day5'] > 0) {
 
   if($pr20 > 0) {
		   $total = ($rws['qty4'] * str_replace(",","",$pr20) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}

$message  = $message.'</tr>';   
  }
   
  if($rws['qty5'] > 0) {
  $message  = $message.' 
 <tr>
  
      <td>Option 5</td>
    <td>'.$rws['qty5'].' Pcs</td> ';  
   
    if($rws['day1'] > 0)
	{
	if($pr21 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr21.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
   
   
   if($rws['day2'] > 0) {
   if($pr22 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr22.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }   
    if($rws['day3'] > 0)
	{
	if($pr23 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr23.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
   
   
    if($rws['day4'] > 0)
	{
	if($pr24 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr24.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
   
    if($rws['day5'] > 0)
	{
	if($pr25 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr25.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
  
    
$message  = $message.'</tr>
 
<tr>
    <td colspan="2" style="text-align:right"><strong>'.($rws[new_or_rep] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included&nbsp;</strong></td>';
   
  if($rws['day1'] > 0) {
  
    if($pr21 > 0) {
		   $total = ($rws['qty5'] * str_replace(",","",$pr21) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
    }

  if($rws['day2'] > 0) {
    if($pr22 > 0) {
		   $total = ($rws['qty5'] * str_replace(",","",$pr22) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }   
 }

  if($rws['day3'] > 0) {
   if($pr23 > 0) {
		   $total = ($rws['qty5'] * str_replace(",","",$pr23) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day4'] > 0) {
 
  if($pr24 > 0) {
		   $total = ($rws['qty5'] * str_replace(",","",$pr24) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day5'] > 0) {
   if($pr25 > 0) {
		   $total = ($rws['qty5'] * str_replace(",","",$pr25) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}

$message  = $message.'</tr>';  
   
   }
  
   if($rws['qty6'] > 0) {
  $message  = $message.' 
  
  <tr>
    <td>Option 6</td>
   
   <td>'.$rws['qty6'].' Pcs</td>';   
    if($rws['day1'] > 0)
	{
	if($pr26 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr26.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}  
   
    if($rws['day2'] > 0)
	{
	if($pr27 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr27.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	} 
   
    if($rws['day3'] > 0) {
	if($pr28 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr28.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
   
    
    if($rws['day4'] > 0)
	{
	if($pr29 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr29.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
   
   
    if($rws['day5'] > 0)
	{
	if($pr30 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr30.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
   
       
$message  = $message.'</tr>
 
<tr>
    <td colspan="2" style="text-align:right"><strong>'.($rws[new_or_rep] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included&nbsp;</strong></td>';
   
  if($rws['day1'] > 0) {
    if($pr26 > 0) {
		     $total = ($rws['qty6'] * str_replace(",","",$pr26) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
			  $message  = $message.'
			 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }

  
    }
  if($rws['day2'] > 0) {
   if($pr27 > 0) {
		     $total = ($rws['qty6'] * str_replace(",","",$pr27) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
			  $message  = $message.'
			 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   
 }
  if($rws['day3'] > 0) {
  if($pr28 > 0) {
		     $total = ($rws['qty6'] * str_replace(",","",$pr28) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
			  $message  = $message.'
			 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day4'] > 0) {
 if($pr29 > 0) {
		     $total = ($rws['qty6'] * str_replace(",","",$pr29) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
			  $message  = $message.'
			 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day5'] > 0) {
  if($pr30 > 0) {
		     $total = ($rws['qty6'] * str_replace(",","",$pr30) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
			  $message  = $message.'
			 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}

$message  = $message.'</tr>'; 
  }
  
   if($rws['qty7'] > 0) {
   $message  = $message.' 
  <tr>
    <td>Option 7</td>
    <td>'.$rws['qty7'].' Pcs</td>';
	
	
	 if($rws['day1'] > 0) {
	 if($pr31 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr31.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	 }
   
   
    if($rws['day2'] > 0) {
	if($pr32 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr32.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
   
    if($rws['day3'] > 0) {
	if($pr33 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr33.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
   
    if($rws['day4'] > 0) {
	if($pr34 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr34.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
   
   
    if($rws['day5'] > 0) {
	if($pr35 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr35.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}   
   
$message  = $message.'</tr>
 
<tr>
    <td colspan="2" style="text-align:right"><strong>'.($rws[new_or_rep] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included&nbsp;</strong></td>';
   
  if($rws['day1'] > 0) {
  if($pr31 > 0) {
		  $total = ($rws['qty7'] * str_replace(",","",$pr31) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }  
    }

  if($rws['day2'] > 0) {
if($pr32 > 0) {
		  $total = ($rws['qty7'] * str_replace(",","",$pr32) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }   
 }

  if($rws['day3'] > 0) {
 if($pr33 > 0) {
		$total = ($rws['qty7'] * str_replace(",","",$pr33) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		$message  = $message.'
		<td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day4'] > 0) {
 if($pr34 > 0) {
		 $total = ($rws['qty7'] * str_replace(",","",$pr34) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		$message  = $message.'<td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }

   if($rws['day5'] > 0) {
	if($pr35 > 0) {
		  $total = ($rws['qty7'] * str_replace(",","",$pr35) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}

$message  = $message.'</tr>';   
  }
 
 
  if($rws['qty8'] > 0) {
 $message  = $message.' 
 <tr>
    <td>Option 8</td>
    <td>'.$rws['qty8'].' Pcs</td>';
	
if($rws['day1'] > 0 ) {
if($pr36 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr36.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}
   
 if($rws['day2'] > 0) {
 if($pr37 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr37.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
 }
   
  if($rws['day3'] > 0) {
  if($pr38 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr38.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
  }
   
  if($rws['day4'] > 0) {
  if($pr39 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr39.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
  }
   
 if($rws['day5'] > 0) {
 if($pr40 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr40.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
 }
  $message  = $message.'  
  </tr>
 
 <tr>
    <td colspan="2" style="text-align:right"><strong>'.($rws[new_or_rep] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included&nbsp;</strong></td> ';
   
  if($rws['day1'] > 0) {
  if($pr36 > 0) {
		   $total = ($rws['qty8'] * str_replace(",","",$pr36) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }  
    }

  if($rws['day2'] > 0) {
	if($pr37 > 0) {
		   $total = ($rws['qty8'] * str_replace(",","",$pr37) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }   
 }

  if($rws['day3'] > 0) {
 if($pr38 > 0) {
		   $total = ($rws['qty8'] * str_replace(",","",$pr38) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day4'] > 0) {
 
 if($pr39 > 0) {
		   $total = ($rws['qty8'] * str_replace(",","",$pr39) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day5'] > 0) {
 
 if($pr40 > 0) {
		   $total = ($rws['qty8'] * str_replace(",","",$pr40) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}

$message  = $message.'</tr>';  
  }
 

 if($rws['qty9'] > 0) {
$message  = $message.' 
 <tr>
    <td>Option 9</td>
    <td>'.$rws['qty9'].' Pcs</td>';  
   
   if($rws['day1'] > 0) {
   if($pr41 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr41.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   
  if($rws['day2'] > 0) {
  if($pr42 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr42.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
  }
   
   if($rws['day3'] > 0) {
   if($pr43 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr43.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   
   if($rws['day4'] > 0) {
   if($pr44 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr44.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   
   if($rws['day5'] > 0)  {
   if($pr45 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr45.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   
   $message = $message.'</tr>
 
<tr>
    <td colspan="2" style="text-align:right"><strong>'.($rws[new_or_rep] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included&nbsp;</strong></td>';
   
  if($rws['day1'] > 0) {
  if($pr41 > 0) {
		  $total = ($rws['qty9'] * str_replace(",","",$pr41) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }  
    }
  if($rws['day2'] > 0) {
  
if($pr42 > 0) {
		  $total = ($rws['qty9'] * str_replace(",", "", $pr42) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   
 }
  if($rws['day3'] > 0) {
 if($pr43 > 0) {
		  $total = ($rws['qty9'] * str_replace(",", "", $pr43) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day4'] > 0) {
 
if($pr44 > 0) {
		  $total = ($rws['qty9'] * str_replace(",", "", $pr44) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day5'] > 0) {
 if($pr45 > 0) {
		  $total = ($rws['qty9'] * str_replace(",", "", $pr45) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}

$message  = $message.'</tr>'; 
  }
 
  if($rws['qty10'] > 0) {
  $message  = $message.' 
 <tr>
    <td>Option 10</td>
    <td>'.$rws['qty10'].' Pcs</td>'; 
    
	if($rws['day1'] > 0) {
	if($pr46 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr46.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
	}
   
  if($rws['day2'] > 0) {
  if($pr47 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr47.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
  }
   
   if($rws['day3'] > 0) {
   if($pr48 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr48.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   
 if($rws['day4'] > 0) {
 if($pr49 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr49.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
 }
   
   if($rws['day5'] > 0) {
   if($pr50 > 0) {
		$message  = $message.'<td style="text-align:left">&nbsp;$'.$pr50.' ea</td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   
   $message  = $message.'</tr>
 
<tr>
    <td colspan="2" style="text-align:right"><strong>'.($rws[new_or_rep] == 'New Part' ? 'NRE/' : '').'Shipping to FOB Included&nbsp;</strong></td>';
   
  if($rws['day1'] > 0) {
  if($pr46 > 0) {
		 $total = ($rws['qty10'] * str_replace(",", "", $pr46) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
  
      }
  if($rws['day2'] > 0) {
   if($pr47 > 0) {
		 $total = ($rws['qty10'] * str_replace(",", "", $pr47) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   
 }
  if($rws['day3'] > 0) {
  if($pr48 > 0) {
		 $total = ($rws['qty10'] * str_replace(",", "", $pr48) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }
   if($rws['day4'] > 0) {
  if($pr49 > 0) {
		 $total = ($rws['qty10'] * str_replace(",", "", $pr49) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
   }

   if($rws['day5'] > 0) {
 
  if($pr50 > 0) {
		 $total = ($rws['qty10'] * str_replace(",", "", $pr50) ) +  $misccharge+ $nre+$rws['descharge1']+$rws['descharge2'];
		  $message  = $message.'
		 <td style="text-align:left">&nbsp;<strong>$'.format_num($total).'</strong> </td>';
	  }
	  else {
		 $message  = $message.'<td style="text-align:left">&nbsp;</td>';
	  }
}

$message  = $message.'</tr>';
}
   
$message  = $message.'   
</table>'; 
 }

/*if ($rws[simplequote] == '1') {
	if($rws[comments] != '') {
		$message  = $message.'
		<table border="1" cellspacing="0">
		<tr>
			<td style="width:500px" ><strong>Comments</strong>:<br>'.$rws[comments].'</td>
		</tr>
		</table>';
	}
}
else {*/
//if ($rws[is_spinsadmact] == 'no') {	
//echo $message;
 //}
 //}
 /*else
 {*/ 
 //$message  = $message. '<br>'.$rws[special_instadmin];
 // } 
/* $message  = $message.'
 <table width="1000px" border="0">   
   <tr   style=" text-align: center;">
    <td  width="750">
	  <p >'.$rws[special_instadmin].'</p></td></tr>
</table>
';*/

 $message  = $message.'  
  <br>
 When placing your purchase order, please refer to the Quote Number listed at the top of this page. <br>
Please feel free to call us should any requirements change.<br>

Thank you for the opportunity to quote your printed circuit board requirements.<br>

Sincerely,<br>
 PCBsGlobal Inc. Sales Team.  

  <hr>
  <p style="font-size: 8pt">
	Quoted Lead times are based on material availability and shop capacity at time of order placement.  Quoted Lead Times are based on business days<br>  (Monday through Friday) not calendar days.  Holiday or Plant closures affecting lead-time will be noted during time of quote.
<br>
Quoted Lead times five business days or less are valid for 24 hours from time of issuance of quote.<br>

Price and delivery are subject to change pending final review of complete data package, including but not limited to, artwork, drawings, and applicable<br> specifications. Unless otherwise stated in the RFQ, price is based on a 20% X-out allowance on jobs being built in an array form.<br><br>
Please visit www.pcbsglobal.com/PCBsGlobal_Inc_Terms_of_Sale.pdf for our Terms of Sale
  	</p>

<span style="position:absolute;bottom:0px;font-size:8px;">FM8.1.0</span>
 </page>


 ';

  $po = $rws[ord_id] + 10000 ;

   $html2pdf->WriteHTML($message);

if ($_REQUEST['oper']  ==  'view') {
	$html2pdf->Output('exemple.pdf');
} else {

	if ($rws['cancharge'] != 'yes') {		
		$filename = "quotationpdf/Quotation-$po-$rws[cust_name]-$rws[part_no]-$rws[rev] ".date("m-d-Y") . ".pdf";
		$html2pdf->Output($filename,'F');
		$filename = "Quotation-$po-$rws[cust_name]-$rws[part_no]-$rws[rev] ".date("m-d-Y") . ".pdf";
		$html2pdf->Output($filename,'D');
	}
	else {
		$filename = "quotationpdf/Quotation-$po-$rws[cust_name]-$rws[part_no]-$rws[rev]-Cancellation_".date("m-d-Y") . ".pdf";
		$html2pdf->Output($filename,'F');
		$filename = "Quotation-$po-$rws[cust_name]-$rws[part_no]-$rws[rev]-Cancellation_".date("m-d-Y") . ".pdf";
		$html2pdf->Output($filename,'D');
	}
}

//$html2pdf->Output('exemple.pdf');

?>