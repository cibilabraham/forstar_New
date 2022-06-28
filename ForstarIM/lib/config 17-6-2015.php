<?php
	$p		=	$_POST;
	$g		=	$_GET;
	/*
	$p		=	$HTTP_POST_VARS ;
	$g		=	$HTTP_GET_VARS ;
	*/
	date_default_timezone_set ("Asia/Calcutta");
	$today		=	Date('Y-m-j');
	$nowM		=	Date('m');
	$nowD		=	Date('d');
	$nowY		=	Date('Y');

	$xdays		=	10;
	
	//$oneUSD		=	39.62;
	
	/* Background Colors */ 
	$headBg		=   "#95b3d0";
	$bgColor1	=	"#eaeaea";
	$bgColor2	=	"#f0efef";
	$bgColor3	=	"#e5e5d5";
	$hoverBg1	=	"#f7eccb";

	$toEmail	=	"";

	//$ftpRoot		=	"/home/lenexate/public_ftp";
	// $ftpRoot		=	"C:\phpweb\ftp";#E1FFFF, #fdebc6

	$listRowMouseOverStyle="style=\"background-color: #ffffff;\"  onmouseover=\"this.style.backgroundColor='#fde89f'\" onmouseout=\"this.style.backgroundColor='#ffffff'\";";
	
	$portalAddr		=	"http://" . $_SERVER['HTTP_HOST']. $_SERVER['PHP_SELF'];
	$portalAddr		=	substr($portalAddr,0,(strrpos($portalAddr, "/")));
	$msgReadUrl		=	$portalAddr."/login.php?action=msgDisplay&msgId=";
	$fileDownUrl	=	$portalAddr."/login.php?action=fileDownload&filId=";


	$urlAfterLogin	=	"Home.php";

	function addSlash($key)
	{
		if (strstr( $key,"\'" )) {
			return $key;
		} else {
			return addslashes($key);
		}
	}


	function stripSlash($key)
	{
		if (strstr( $key,"\'" )) {
			return stripslashes($key);
		} else {
			return $key;
		}
	}

	#Return the Ordinary date format
	function dateFormat($selectedDate)
	{
		$sDate		= explode("-", $selectedDate);
		$formatedDate	= $sDate[2]."/".$sDate[1]."/".$sDate[0];
		return ($selectedDate!="")?$formatedDate:"";
	}

	#Return the date in database Format(ie.YYYY-MM-DD)
	function mysqlDateFormat($selectedDate)
	{
		$sDate		= explode("/", $selectedDate);
		$mysqlDate	= $sDate[2]."-".$sDate[1]."-".$sDate[0];
		return ($selectedDate!="")?$mysqlDate:"";
	}


	/**
	* Desc: Add day or month or year to  a date
	* @param $type: type to add
	* @param $number: number of days,months or year to add
	* @param $date: date 
	* return value: timestamp newly created date
	**/
	function getTimeStamp($time)
	{
		$ts = str2time($time,'H-i');
		return $ts;
	}

	/**
	* Desc: convert date to timestamp
	* @param $strStr: date string 
	* @param $strPattern: date pattern
	* return value: timestamp 
	**/
	function str2time($strStr, $strPattern = null)
	{
		// an array of the valide date characters, see: http://php.net/date#AEN21898
	$arrCharacters = array(
       'd', // day
       'm', // month
       'y', // year, 2 digits
       'Y', // year, 4 digits
       'H', // hours
       'i', // minutes
       's'  // seconds
	);
	   // transform the characters array to a string
	   $strCharacters = implode('', $arrCharacters);

	   // splits up the pattern by the date characters to get an array of the delimiters between the date characters
	   $arrDelimiters = preg_split('~['.$strCharacters.']~', $strPattern);
	   // transform the delimiters array to a string
	   $strDelimiters = quotemeta(implode('', array_unique($arrDelimiters)));

	   // splits up the date by the delimiters to get an array of the declaration
	   $arrStr    = preg_split('~['.$strDelimiters.']~', $strStr);
	   // splits up the pattern by the delimiters to get an array of the used characters
	   $arrPattern = preg_split('~['.$strDelimiters.']~', $strPattern);

	   // if the numbers of the two array are not the same, return false, because the cannot belong together
	   if (count($arrStr) !== count($arrPattern)) {
		   return false;
	   }

	   // creates a new array which has the keys from the $arrPattern array and the values from the $arrStr array
	   $arrTime = array();
	   for ($i = 0;$i < count($arrStr);$i++) {
		   $arrTime[$arrPattern[$i]] = $arrStr[$i];
	   }

	   // gernerates a 4 digit year declaration of a 2 digit one by using the current year
	   if (isset($arrTime['y']) && !isset($arrTime['Y'])) {
		   $arrTime['Y'] = substr(date('Y'), 0, 2) . $arrTime['y'];
	   }

	   // if a declaration is empty, it will be filled with the current date declaration
	   foreach ($arrCharacters as $strCharacter) {
		   if (empty($arrTime[$strCharacter])) {
			   $arrTime[$strCharacter] = date($strCharacter);
		   }
	   }

	   // checks if the date is a valide date
	   if (!checkdate($arrTime['m'], $arrTime['d'], $arrTime['Y'])) {
		   return false;
	   }

	   // generates the timestamp
	   return mktime($arrTime['H'], $arrTime['i'], $arrTime['s'], $arrTime['m'], $arrTime['d'], $arrTime['Y']);
	}


	/**
	* Desc: find the difference between two date in months/days
	* @param $dateTimeBegin: starting date time stamp 
	* @param $dateTimeEnd: ending date time stamp
	* @param $mode: difference in days or in months
	* return date differnce in months/days
	**/
	function dateDiff($dateTimeBegin, $dateTimeEnd, $mode=null)
	{      
		 $dif=$dateTimeEnd - $dateTimeBegin;

		 $hours = number_format(($dif / 3600),2,'.','');
		 //echo "$hours<br>";
		 $temp_remainder = $dif - ($hours * 3600);
		  
		 $minutes = floor($temp_remainder / 60);
		 $temp_remainder = $temp_remainder - ($minutes * 60);
		  
		 $seconds = $temp_remainder;
			
		 // leading zero's - not bothered about hours
		 $min_lead=':';
		 if($minutes <=9)
		   $min_lead .= '0';
		 $sec_lead=':';
		 if($seconds <=9)
		   $sec_lead .= '0';

		 if( $mode=='D'){
			 return floor( ( $hours / 24 ));
		 }
		 else if( $mode=='H'){
			 return $hours;
		 }
		 else	{
			return floor( ( $hours / 24 ) / 30 );
		 }
	}

	#Print Header
	define ("COMPANY_NAME", "FORESEE FROZEN FOODS PVT.LTD");		
	define ("COMPANY_ADDRESS", "");
	define ("COMPANY_PHONE", "");
	
	define ("FIF_COMPANY_NAME", "FORESEE FOODS");
	define ("FIF_SUB_HEAD", "(Div. of Foresee Frozen Foods Pvt Ltd)");
	define ("FIF_ADDRESS1", "");
	define ("FIF_ADDRESS2", "");
	define ("FIF_PHONE", "");
	define ("FIF_NEWPHONE", "");
	define ("FIF_FAX", "");
	define ("FIF_EMAIL", "");
	/*
		define ("FIF_ADDRESS1", "M-52, MIDC Industrial Area, Taloja");
	define ("FIF_ADDRESS2", "Navi Mumbai - 410 208, Maharashtra, India");
	define ("FIF_PHONE", "TEL: (22) 2741 2376,&nbsp;FAX: (22) 2741 0999");
	define ("FIF_NEWPHONE", "TEL: (22) 2741 0807/2376");
	define ("FIF_FAX", "FAX: (22) 2741 0999");
	define ("FIF_EMAIL", "EMAIL:info@foreseefoods.com");
	*/
	#End

	# For assigning value
	function selectAvailableVal($pval,$dbval)
	{
		if ($pval=='') {
			return ( $dbval == "" ) ? "" : $dbval;
		}
		return $pval;
	}

	$autoIdGenFunctions = array("PO"=>"Purchase Order (Inventory)", "SI"=>"Stock Issuance", "SR"=>"Stock Return", "DC"=>"Distributor Claim", "IPO"=>"Ingredient PO", "SO"=>"Sales Order");

	# Print alert
	function printJSAlert($msg)
	{
		$msg = addslashes($msg);
		$msg = str_replace("\n", "\\n", $msg);
		echo "<script language='javascript'><!--\n";
		echo 'alert("' . $msg . '")';
		echo "//--></script>\n\n"; 
	}

	/*
		Format number as the ex (eg.2.50)
	*/
	function formatAmount($num)
	{
		if( $num!="" ) return number_format($num,2,'.','');
		return number_format(0,2,'.','');
	}

	# Generating auto number for Master Screen
	function autoGenNum()
	{
		$dateTimeStamp= date("dmygis");
		$randomNumber = rand(10,100);	
		$autoGenNum = $dateTimeStamp.$randomNumber;
		return $autoGenNum;
	}

	# Get data base Enum full form
	$getEnumFunction = array("Y"=>"YES", "N"=>"NO");

	# Rate List Master Pages (Functionalities)
	$masterRateListPages = array("MPC"=>"Man Power Master","FCC"=>"Fish Cutting Cost Master", "MC"=>"Marketing Cost Master","TC"=>"Travel Cost Master","PLC"=>"Packing Labour Cost Master","PSC"=>"Packing Sealing Cost Master","PMC"=>"Packing Material Cost Master","PMM"=>"Production Matrix Master","PCM"=>"Packing Cost Master", "PMRP"=>"Product MRP Master");
	
	# Master Page URL
	$mRateListUrl = array("MPC"=>"ProductionManPower.php","FCC"=>"ProductionFishCutting.php", "MC"=>"ProductionMarketing.php","TC"=>"ProductionTravel.php","PLC"=>"PackingLabourCost.php","PSC"=>"PackingSealingCost.php","PMC"=>"PackingMaterialCost.php","PMM"=>"ProductionMatrixMaster.php","PCM"=>"PackingCostMaster.php", "PMRP"=>"ProductMRPMaster.php");

	# Dashboard
	$dashBoardFunctions = array("RMQ"=>"Raw Material Qty","MIC"=>"Missing Challan","PPQ"=>"Pre-Processed Qty","SOI"=>"Sales Order", "FPQ"=>"Frozen Packing Qty", "DAC"=>"Distributor Account");
	ksort($dashBoardFunctions);

	# Various Languages
	$langRecs = array("ENG"=>"English","FRA"=>"France", "BEL"=>"Belgium", "SPA"=>"Spain", "ITA"=>"Italy", "POR"=>"Portugal", "GER"=>"Germany", "SLO"=>"Slovenia", "GRE"=>"Greece");

	# Stock Types (using in Physical Stock Entry)
	$stkTypes = array ("I"=>"Ingredient","P"=>"Product","S"=>"Semi-Finished Product");

	/**
	* Desc: Add day or month or year to  a date
	* @param $type: type to add
	* @param $number: number of days,months or year to add
	* @param $date: date 
	* return value: timestamp newly created date
	**/

	function ctDateAdd($type, $number, $date)
	{
		$ts = str2time($date,'Y-m-d');
		return ctDateAdd2($type,$number,$ts);
	}

	/**
	* Desc: Add Two dates 
	* @param $type: type to add, month, day, year, minutes, seconds,hours
	* @param $number: number of type to add
	* @param $ts: current time stamp
	* return value: return timestamp
	**/
	function ctDateAdd2($type,$number,$ts)
	{
		$date_time_array = getdate($ts);
		$hours = $date_time_array['hours'];
		$minutes = $date_time_array['minutes'];
		$seconds = $date_time_array['seconds'];
		$month = $date_time_array['mon'];
		$day = $date_time_array['mday'];
		$year = $date_time_array['year'];
		switch ($type) {
			case 'yyyy':
				$year+=$number;
				break;
			case 'q':
				$year+=($number*3);
				break;
			case 'm':
				$month+=$number;
				break;
			case 'y':
			case 'd':
			case 'w':
				$day+=$number;
				break;
			case 'ww':
				$day+=($number*7);
				break;
			case 'h':
				$hours+=$number;
				break;
			case 'n':
				$minutes+=$number;
				break;
			case 's':
				$seconds+=$number;
				break;            
		}
	   $timestamp= mktime($hours,$minutes,$seconds,$month,$day,$year);
		return $timestamp;
	}
	
	/**
	* function which compares $arr1 with $arr2 and return all values from $arr2 that aren't in $arr1.
	* Associated array
	**/
	function arr_diff($a1,$a2)
	{
		$ar = array();
		foreach ($a2 as $k => $v) {
			if (!is_array($v)) {
			if ($v !== $a1[$k])
				$ar[$k] = $v;
			}else{
			if ($arr = arr_diff($a1[$k], $a2[$k]))
				$ar[$k] = $arr;
			}
		}
		return $ar;
	}

	# Id Gen Functions
	
	$idGenFunctions = array("PO"=>"Purchase Order (Inventory)", "SI"=>"Stock Issuance", "SR"=>"Stock Return", "DC"=>"Distributor Claim", "IPO"=>"Ingredient PO", "SO"=>"Sales Order", "RM"=>"Raw Material", "SPO"=>"Shipment Invoice", "SHPC"=>"Container", "MG"=>"Procurment Number ID", "LF"=>"RM LOT ID","WC"=>"Weighment Data Sheet","SL"=>"Seal Number","RG"=>"Receipt Gate Pass Number");
	/*$idGenFunctions = array("PO"=>"Purchase Order (Inventory)", "SI"=>"Stock Issuance", "SR"=>"Stock Return", "DC"=>"Distributor Claim", "IPO"=>"Ingredient PO", "SO"=>"Sales Order", "RM"=>"Raw Material", "SPO"=>"Purchase Order (Shipment)", "SHPC"=>"Container", "MG"=>"Procurment Number ID", "LF"=>"RM LOT ID","WC"=>"Weighment Data Sheet","SL"=>"Seal Number","RG"=>"Receipt Gate Pass Number");*/
	
	/*$idGenFunctions = array("PO"=>"Purchase Order (Inventory)", "SI"=>"Stock Issuance", "SR"=>"Stock Return", "DC"=>"Distributor Claim", "IPO"=>"Ingredient PO", "SO"=>"Sales Order", "RM"=>"Raw Material", "SPO"=>"Purchase Order (Shipment)", "SHPC"=>"Container", "MG"=>"Procurment Number ID", "LF"=>"RM LOT ID","WC"=>"Weighment Data Sheet","SL"=>"Seal Number","PG"=>"Procurment Gate Pass Number","RG"=>"Receipt Gate Pass Number");*/
	
	/*$idGenFunctions = array("PO"=>"Purchase Order (Inventory)", "SI"=>"Stock Issuance", "SR"=>"Stock Return", "DC"=>"Distributor Claim", "IPO"=>"Ingredient PO", "SO"=>"Sales Order", "RM"=>"Raw Material", "SPO"=>"Purchase Order (Shipment)", "SHPC"=>"Container", "MG"=>"Procurment Number ID", "LF"=>"RM LOT ID - Fresh", "LU"=>"RM LOT ID - Unit Transfer", "LC"=>"Cold Storage Lot","LT"=>"RM LOT ID - Thawed","WC"=>"Weightment Challan","SL"=>"Seal Number","PG"=>"Procurment Gate Pass Number","RG"=>"Receipt Gate Pass Number");*/
	
	//$idGenFunctions = array("PO"=>"Purchase Order (Inventory)", "SI"=>"Stock Issuance", "SR"=>"Stock Return", "DC"=>"Distributor Claim", "IPO"=>"Ingredient PO", "SO"=>"Sales Order", "RM"=>"Raw Material", "SPO"=>"Purchase Order (Shipment)", "SHPC"=>"Container", "MG"=>"Procurment Number ", "LF"=>"Fresh Lot", "LU"=>"Unit Lot", "LC"=>"Cold Storage Lot","LT"=>"Thawing Lot","WC"=>"Weightment Challan");
	
	
	ksort($idGenFunctions);	

	# Get Date Diff based on 
	function gDateDiff($startDate, $endDate)
	{
		$currentDate	= date("Y-m-d");
		$cDate		= explode("-",$currentDate);
		$d2 = mktime(0,0,0,$cDate[1],$cDate[2],$cDate[0]);

		$eDate		= explode("-", $selLastDate);		
		$d1 = mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);

		$dateDiff = floor(($d2-$d1)/86400);
		return $dateDiff; 
	}

	# Invoice Type Array
	$invoiceTypeRecs = array("TA"=>"TAX", "PF"=>"PROFORMA");
	//$invoiceTypeRecs = array("SA"=>"SAMPLE", "TA"=>"TAX", "PF"=>"PROFORMA", "GP"=>"GATE PASS");
	ksort($invoiceTypeRecs);

	# Return valid String after removed special characters
	function getValidString($string) 
	{
		$temp = $string;	
		// Lower case
		$temp = strtolower($temp);
		// Replace spaces with a '_'
		$temp = str_replace(" ", "_", $temp);
		// Loop through string
		$result = '';
		for ($i=0; $i<strlen($temp); $i++) {
			if (preg_match('([0-9]|[a-z]|_)', $temp[$i])) {
				$result = $result.$temp[$i];
			}
		}
		// Return filename
		return $result;
	}

	# Multidimensional array Difference
	function ary_diff( $ary_1, $ary_2 ) 
	{
		// compare the value of 2 array
		// get differences that in ary_1 but not in ary_2
		// get difference that in ary_2 but not in ary_1
		// return the unique difference between value of 2 array
		$diff = array();
		
		// get differences that in ary_1 but not in ary_2
		foreach ( $ary_1 as $v1 ) {
		$flag = 0;
		foreach ( $ary_2 as $v2 ) {
		$flag |= ( $v1 == $v2 );
		if ( $flag ) break;
		}
		if ( !$flag ) array_push( $diff, $v1 );
		}
		
		// get difference that in ary_2 but not in ary_1
		foreach ( $ary_2 as $v2 ) {
		$flag = 0;
		foreach ( $ary_1 as $v1 ) {
		$flag |= ( $v1 == $v2 );
		if ( $flag ) break;
		}
		if ( !$flag && !in_array( $v2, $diff ) ) array_push( $diff, $v2 );
		}
		
		return $diff;
	}

	# Invoice Filter Type
	$invoiceTypeArr	= array("TI"=>"TAXABLE", "PI"=>"PROFORMA", "SI"=>"SAMPLE");

	# Pkg Instruction Array	
	# GNP= Generate New Packing, PGP = Packing Generated & Pending, PGC = Packing Generation Completed
	$pkgInstStatusArr = array("GNP"=>"GENERATE NEW", "PGP"=>"PENDING", "PGC"=>"COMPLETED");

	# Get Full browser url
	function curPageURL() 
	{
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";}
			$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}	

	# Get Invoice No
	function getInvFormat($invType, $soNo, $pfNo, $saNo)
	{
		$invoiceNo = "";
		if ($soNo!=0) $invoiceNo=$soNo;
		else if ($invType=='T') $invoiceNo = "P$pfNo";
		else if ($invType=='S') $invoiceNo = "S$saNo";
		return $invoiceNo;
	}

	# Merging two array and remove duplicate values
	function ary_merge($array1, $array2)
	{
		if (sizeof($array1)>sizeof($array2)) {
			$size = sizeof($array1);
		} else {
			$a = $array1;
			$array1 = $array2;
			$array2 = $a;
			$size = sizeof($array1);
		}	
		$keys2 = array_keys($array2);		
		for ($i = 0;$i<$size;$i++) {
			$array1[$keys2[$i]] = $array1[$keys2[$i]] + $array2[$keys2[$i]];
		}	
		$array1 = array_filter($array1);
		return $array1;
   	 }

	# Compare Two values in USORT
	# For comparing String of second index
	function cmp_name($a, $b) 
	{ 
		return strnatcmp($a[1], $b[1]); 
	}

	function multi_unique($array) {
		foreach ($array as $k=>$na)
			$new[$k] = serialize($na);
			$uniq = array_unique($new);
		foreach($uniq as $k=>$ser)
			$new1[$k] = unserialize($ser);
		return ($new1);
	}	

	# Xajax Creating Drop Down List
	# $sSelectId = Selected Field, $options=>Associative array, $cId=>selected Id, $objResponse=>Response 
	function addDropDownOptions($sSelectId, $options, $cId, &$objResponse)
	{		
		$objResponse->script("document.getElementById('".$sSelectId."').length=0");
		if (sizeof($options) >0) {
			foreach ($options as $option=>$val) {
				$objResponse->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
			}
		}
	}

	# Get Fomated Address (If second comma then put a Break)
	function addressFormat($address)
	{
		$toAddr = explode(",",$address);	
		$k=0;
		$displayAddressVal = "";
		foreach ($toAddr as $kv=>$val) {
			$k++;				
			if ($k==3) $dVal .= "<br/>";
			$dVal .= $val;
			if ($k!=sizeof($toAddr)) $dVal .= ",";
		}	
		return $dVal;
	}

	
	/*	
	function financialYear($year=null)
	{
		$year = ($year)?$year:date("Y");
		$dateFrom   =  date("d/m/Y", mktime(0, 0, 0, 04, 01, ($year-1)));
		$dateTill   =  date("d/m/Y",mktime(0, 0, 0, 03, 31, $year));
		return array($dateFrom, $dateTill);
	}
	*/
	# Entry Type
	//$entryTypeArr = array("PR"=>"PAYMENT RECEIVED", "AD"=>"AMT DUE (Debit)", "AC"=>"AMT RECEIVED (Credit)");
	$entryTypeArr = array("AD"=>"AMT DUE (Debit)", "AC"=>"AMT RECEIVED (Credit)");

	# Payment Mode 
	$paymentModeArr = array("CHQ"=>"Cheque", "CH"=>"Cash", "RT"=>"RTGS");

	# COD Arr
	$codArr = array(""=>"--Select--","D"=>"DEBIT", "C"=>"CREDIT");

	# Compare Two values in USORT
	# For comparing String of first index
	function cmp_finame($a, $b) 
	{ 
		return strnatcmp($a[0], $b[0]); 
	}
	
	# Get Date Diff based on 
	# D1-D2
	function findDateDiff($startDate, $endDate)
	{
		//$currentDate	= date("Y-m-d");
		$cDate		= explode("-",$endDate);
		$d2 = mktime(0,0,0,$cDate[1],$cDate[2],$cDate[0]);

		$eDate		= explode("-", $startDate);		
		$d1 = mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);

		$dateDiff = floor(($d1-$d2)/86400);
		return $dateDiff; 
	}

	function printr($var)
	{
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}

	# Financial Year
	function financialYear()
	{
		$currentMonth = date("n");
		if ($currentMonth>=4) $startDate = date("d/m/Y", mktime(0, 0, 0, 04, 01, date("Y")));
		else $startDate = date("d/m/Y", mktime(0, 0, 0, 04, 01, (date("Y")-1)));
		return $startDate;
	}

	function generateRandomString($length = 10, $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
	{
		$s = '';
		$lettersLength = strlen($letters)-1;
		for ($i = 0 ; $i < $length ; $i++) {
			$s .= $letters[rand(0,$lettersLength)];
		}
		return $s;
	}
	

	/*
	* Export Address
	*/
	$exportAddrArr = array("ADRH"=>"FORESEE FROZEN FOODS PVT. LTD", "ADR1"=>"", "ADR2"=>"", "ADR3"=>"","ADR4"=>"","ADR5"=>"","ADR6"=>"");
	//$exportAddrArr = array("ADRH"=>"FORESEE FROZEN FOODS PVT. LTD", "ADR1"=>"505 A, GALLERIA, HIRANANDANI GARDENS", "ADR2"=>"A. S. MARG, POWAI", "ADR3"=>"MUMBAI – 400 076 (INDIA)","ADR4"=>"TEL: (22) 2741 2376,","ADR5"=>"FAX: (22) 2741 0999","ADR6"=>"EMAIL: info@foreseefoods.com");

	/*
	Get Financial Year Range
	*/

	$forstinsfoods=array("fifoods"=>"FORESEE FOODS");
	$divfrfoods=array("frfoods"=>"(Div. of Foresee Frozen Foods Pvt Ltd)");
	$addr=array("ADR1"=>"","ADR2"=>"","ADR3"=>"","ADR4"=>"","ADR5"=>"","ADR6"=>"","ADR7"=>"","ADR8"=>"","ADR9"=>"","ADR10"=>"","ADR11"=>"",);
	$companyArr=array("Name"=>"FORESEE FROZEN FOODS PVT.LTD","Email"=>"");
	$debitNoteArr = array("Name"=>"FORESEE","ADDR1"=>"","CONTACT_NUMBER"=>"","Email"=>"");
	/*
	$addr=array("ADR1"=>"M-52, MIDC Industrial Area, Taloja","ADR2"=>"Navi Mumbai - 410 208, Maharashtra, India","ADR3"=>"TEL: (22) 2741 2376,","ADR4"=>"FAX: (22) 2741 0999","ADR5"=>"EMAIL: info@foreseefoods.com","ADR6"=>"PLOT NO. M-53, P.O.BOX.NO. 09,","ADR7"=>"NEW BOMBAY-410208 (INDIA)","ADR8"=>"EIC APPROVAL.NO.209","ADR9"=>"VAT TIN:27420006313V w.e.f 01/04/2006","ADR10"=>"CST TIN:27420006313C w.e.f 01/04/2006","ADR11"=>"M-53, MIDC Industrial Area, Taloja",);
	$companyArr=array("Name"=>"FORESEE FROZEN FOODS PVT.LTD","Email"=>"EMAIL: info@foreseefoods.com");
	$debitNoteArr = array("Name"=>"NAIR BROTHERS","ADDR1"=>"PLOT NO. M-53, MIDC Ind Area, Taloja New Bombay 410 208, India","CONTACT_NUMBER"=>"Tel: 022-27412376, 27410807, FAX: 022-27410999, CELL: 9820449554,9870688991","Email"=>"EMAIL: export@foreseefoods.com");
	*/

	function getFinancialYearRange($selInvDate)
	{
		$year	= date('y', strtotime($selInvDate));
		$month  = date('m', strtotime($selInvDate));
		//$currentMonth = date("n");		
		$yearRange = ($month>=4)?($year."-".($year+1)):(($year-1)."-".$year);
		return $yearRange;
	}

	# Get Shipment Purchase Order Unit Recs 
	$spoUnitRecs = array("1"=>"KGS","2"=>"LBS");
	define ("KG2LBS", "2.20462262");

	
?>