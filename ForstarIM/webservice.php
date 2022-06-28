<?php
require_once ("lib/databaseConnect.php");
require_once ("lib/Home_class.php");
require_once ("lib/config.php");
require_once ("lib/ManageChallan_class.php");
require_once ("lib/ManageDashboard_class.php");

require_once ("xmlrpc/lib/xmlrpc.inc");
require_once ("xmlrpc/lib/xmlrpcs.inc");

$noOfDays 	= 6;  //Set No. of days to display for days 6
$noOfMonths 	= 6;

//print getDailyProdQty();
//print getMissingChallan();
//print getDespatch();
/**
* Daily Production qty
*/
function getDailyProdQty($param)
{
	//$selDate = $par->getParam(0);
	//$selDate = "2010-02-23";
	$selDate = "23/02/2010";
	
	$databaseConnect  =	new DatabaseConnect();
	$homeObj	  =	new Home($databaseConnect);

	/*
	if ($selDate!="") $currentDate   = base64_decode($selDate);
	else $currentDate   = date("d/m/Y");
	*/
	$currentDate = $selDate;		
	$dateC	   =	explode("/", $currentDate);
	$dpPrevDate =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],$dateC[0]-7,$dateC[2]));
	$dpNextDate =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],$dateC[0]+7,$dateC[2]));
			
	$totalQty = "";
	
	$xmlData  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
	$xmlData .= "<dailyProd>";
	$xmlData .= "<header>";
	$xmlData .= "<label>Date</label>";
	$xmlData .= "<label>Effective Qty</label>";
	$xmlData .= "<label>Adjust Qty</label>";
	$xmlData .= "<label>Gd-Ct Adj</label>";
	$xmlData .= "<label>Total Quantity</label>";
	$xmlData .= "<label>Local Qty</label>";
	$xmlData .= "<label>Wastage Qty</label>";
	$xmlData .= "<label>Soft Qty</label>";
	$xmlData .= "<label>Qty for Pre-Process</label>";
	$xmlData .= "<label>Qty for Pkg</label>";
	$xmlData .= "<label>Qty Pkd</label>";
	$xmlData .= "<label>Pkg(%)</label>";
	$xmlData .= "<label>RM CB Qty</label>";
	$xmlData .= "</header>";
	$xmlData .= "<data>";

	for ($i=0; $i<=$noOfDays; $i++) {
		$selDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-$i,$dateC[2]));
				
		$challanDate = dateFormat($selDate);
		list($rmQty, $adjustQty, $localQty, $wastageQty, $softQty, $gradeCountAdj) = $homeObj->getRMQty($selDate);	
		$totalQty = $rmQty + $adjustQty; // Total Qty = EffectiveQty + AdjustQty
		
		# Pre-Processed Qty
		$totalPreProcessedQty = $homeObj->getTotalPreProcessedQty($selDate);

		# Frozen packing Qty
		$totalFPQty = $homeObj->getFrznPkgQty($selDate);
		//$totalFPQty = ($totalFPQty!="")?$totalFPQty:"0.00";

		# Daily RM CB Qty
		$totalRMCBQty = $homeObj->getDailyRMCBQty($selDate);

		# Qty For Pkg
		list($qtyForPkg, $prevDayCBQty) = $homeObj->qtyForPkg($selDate);
		
		# Calc Pkg Percent
		if ($totalFPQty) $pkgPercent = number_format(($totalFPQty/$qtyForPkg)*100,2,'.','');
		
		$xmlData .= '<row>';
		$xmlData .= '<date value="'.$challanDate.'" />';
		$xmlData .= '<effectiveQty value="'.$rmQty.'" />';
		$xmlData .= '<adjustQty value="'.$adjustQty.'" />';
		$xmlData .= '<gdCtAdj value="'.$gradeCountAdj.'" />';
		$xmlData .= '<totalQty value="'.$totalQty.'" />';
		$xmlData .= '<localQty value="'.$localQty.'" />';
		$xmlData .= '<wastageQty value="'.$wastageQty.'" />';
		$xmlData .= '<softQty value="'.$softQty.'" />';
		$xmlData .= '<preprocessQty value="'.$totalPreProcessedQty.' /> ';
		$xmlData .= '<qtyForPkg value="'.$qtyForPkg.'" />';
		$xmlData .= '<pkdQty value="'.$totalFPQty.'" />';
		$xmlData .= '<pkgPercent value="'.$pkgPercent.'" />';
		$xmlData .= '<rmcbQty value="'.$totalRMCBQty.'" /> ';
		$xmlData .= '</row>';
	}

	$xmlData .= '</data>';
	$xmlData .= '</dailyProd>';
	//return $xmlData
	return new xmlrpcresp(new xmlrpcval($xmlData, "string"));
}

/**
* Missing challan
*/
function getMissingChallan($selDate)
{
	$databaseConnect  =	new DatabaseConnect();
	$homeObj	  =	new Home($databaseConnect);
	$manageChallanObj = 	new ManageChallan($databaseConnect);

	$selDate="23/02/2010";
	$currentDate = $selDate;
	/*
	if ($selDate!="") $currentDate   = base64_decode($selDate);
	else $currentDate   = date("d/m/Y");
	*/

	$dateC	   =	explode("/", $currentDate);

	$mcPrevDate =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],$dateC[0]-7,$dateC[2]));
	$mcNextDate =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],$dateC[0]+7,$dateC[2]));
	
	$xmlData  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
	$xmlData .= "<missChallan>";
	$xmlData .= "<header>";
	$xmlData .= "<label>Date</label>";
	$xmlData .= "<label>Last Challan No</label>";
	$xmlData .= "<label>Missing Challan Nos</label>";
	$xmlData .= "</header>";
	$xmlData .= "<data>";

	$totalQty = "";
	for ($i=0; $i<=$noOfDays; $i++) {
		$selDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-$i,$dateC[2]));
		$challanDate = dateFormat($selDate);
		$prevDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-($i+1),$dateC[2]));
		$prevChallanDate = dateFormat($prevDate);

		# Get Billing Comapny Recs
		$getBillCompanyRecs = $homeObj->getBillingCompanyWiseRecs($selDate);
		
		if (sizeof($getBillCompanyRecs)>0) { 
			foreach ($getBillCompanyRecs as $gbcr) {
				//$lastRMChallanNumber 	= $gbcr[1];
				$billingCompanyId	= $gbcr[2];
				# Find the Last RM Challan Number (Max Challan Number)
				$lastRMChallanNumber = $homeObj->getLastChallanNumber($selDate, $billingCompanyId);
				$alphaCode		= $gbcr[3];
				$displayRMChallanNo 	= $alphaCode.$lastRMChallanNumber;
				# Find the Prev Date Last Challan Number (Min Challan Number, Callan Date)
				list($prevLastRMChallanNumber, $selPrevDate)  =  $homeObj->getPrevLastChallanNumber($prevDate, $billingCompanyId, $selDate);
				
				# Find the Missing Challan Numbers
				$missingChallanRecords = array();
				if ($prevLastRMChallanNumber=="") {
					list($startingNumber, $endingNumber) = $manageChallanObj->getChallanRec($selDate, $billingCompanyId);
					$prevLastRMChallanNumber = $startingNumber;
				}
				//echo "<br/>$prevLastRMChallanNumber, $lastRMChallanNumber, $selDate,$billingCompanyId<br/>";	
				if ($prevLastRMChallanNumber!="" && $lastRMChallanNumber!="") {
					$missingChallanRecords = $homeObj->getMissingRecords($prevLastRMChallanNumber, $lastRMChallanNumber, $selDate, $billingCompanyId);
				}				
				//$displayLink = "";				
				//if (sizeof($missingChallanRecords)>0) $displayLink = "ChallanVerification.php?supplyFrom=".dateFormat($selPrevDate)."&supplyTill=$challanDate&billingCompany=$billingCompanyId&startCNum=$prevLastRMChallanNumber&endCNum=$lastRMChallanNumber";
				//else $displayLink = "###";

				$numCol = 3;
				$displayMissChallan = "";
				if (sizeof($missingChallanRecords)>0) {
					if (sizeof($missingChallanRecords)<=10) {
						$nextRec = 0;
						$missingChallan = "";
						foreach ($missingChallanRecords as $key=>$value) {
							$missingChallan = $value;
							$nextRec++;

							if($nextRec>1) $displayMissChallan .= "&nbsp;,&nbsp;"; $displayMissChallan .= $missingChallan;
							if($nextRec%$numCol == 0) $displayMissChallan .= "<br/>";
						}
					} else {  # If size greater than 10
						$displayMissChallan = sizeof($missingChallanRecords)."&nbsp;Challan Missed";
					}
				} else if ($lastRMChallanNumber!="") $displayMissChallan = "NIL";
				
				$xmlData .= '<row>';
				$xmlData .= '<date value="'.$challanDate.'" />';
				$xmlData .= '<challan value="'.$displayRMChallanNo.'" />';
				$xmlData .= '<challanNos value="'.$displayMissChallan.'" />';
				$xmlData .= '</row>';
			} // Billing company loop ends here
		} else {
				$xmlData .= '<row>';
				$xmlData .= '<date value="'.$challanDate.'" />';
				$xmlData .= '<challan value="" />';
				$xmlData .= '<challanNos value="" />';
				$xmlData .= '</row>';
		}
	} // Loop ends here
	$xmlData .= "</data>";
	$xmlData .= "</missChallan>";

	//return $xmlData;
	return new xmlrpcresp(new xmlrpcval($xmlData, "string"));
}

/**
* Get Month wise sales order amt
*/
function getSOAmt()
{
	$databaseConnect  =	new DatabaseConnect();
	$homeObj	  =	new Home($databaseConnect);
	
	$selDate	= "23/02/2010";
	$currentDate 	= $selDate;
	/*
	if ($selDate!="") $currentDate   = base64_decode($selDate);
	else $currentDate   = date("d/m/Y");
	*/

	$dateC	   =	explode("/", $currentDate);

	$selDate = "";
	$billedAmt = "";
	$pendingOrderAmt = "";

	$xmlData  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
	$xmlData .= "<soAmt>";
	$xmlData .= "<header>";
	$xmlData .= "<label>Month</label>";
	$xmlData .= "<label>Total Amt</label>";
	$xmlData .= "<label>Pending Amt</label>";
	$xmlData .= "</header>";
	$xmlData .= "<data>";

	$soDateArr = array();
	for ($i=0; $i<=$noOfMonths; $i++) {
		$selMonth = date("M",mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));	
		$month   =  date("m",mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));	
		$selYear   =  date("Y",mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));	
		$lastDateOfMonth=date('t',mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));
		$firstdate   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));
		$soDateArr[$i] = $firstdate;
		$lastdate   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1]-$i, $lastDateOfMonth, $dateC[2]));
		# C= Complete, P=Pending
		$billedAmt	 = $homeObj->getSOBilledAmt($month, 'C', $selYear);
		$pendingOrderAmt = $homeObj->getSOBilledAmt($month, 'P', $selYear);
		
		$selDYear   =  date("y",mktime(0, 0, 0,$dateC[1]-$i,1,$dateC[2]));	
		$displayMonth = $selMonth."&nbsp;".$selDYear;

		$xmlData .= '<row>';
		$xmlData .= '<month value="'.$displayMonth.'" />';
		$xmlData .= '<totalAmt value="'.$billedAmt.'" />';
		$xmlData .= '<pendingAmt value="'.$pendingOrderAmt.'" />';
		$xmlData .= '</row>';
	}
	$xmlData .= "</data>";
	$xmlData .= "</soAmt>";
	return new xmlrpcresp(new xmlrpcval($xmlData, "string"));
}

/**
* Despatch invoice
*/
function getDespatch()
{
	$databaseConnect  =	new DatabaseConnect();
	$homeObj	  =	new Home($databaseConnect);
	
	$selDate	= "05/02/2010";
	$currentDate 	= $selDate;

	/*
	if ($selDate!="") $currentDate   = base64_decode($selDate);
	else $currentDate   = date("d/m/Y");
	*/

	$dateC	   =	explode("/", $currentDate);
		
	$displayArr = array();
	if ($nextDays=="") {
		$daysAhead = 3;
		$daysBack = 3;				
		$c = 0;
		for ($dh=$daysAhead; $dh>0; $dh--) {
			$sDDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]+$dh,$dateC[2]));
			$displayArr[$c] = $sDDate;
			$c++;
		}
		if ($nextDays=="") $displayArr[$c++] = date("Y-m-d");
		for ($db=1; $db<=$daysBack; $db++) {
			$sDDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],($dateC[0]-$db),$dateC[2]));
			$displayArr[$c++] = $sDDate;
		}
	} 

	if ($nextDays!="") {
		$sDispatchDate = "";
		for ($i=0; $i<=$noOfDays; $i++) {
			$sDispatchDate = date("Y-m-d",mktime(0, 0, 0,$dateC[1],$dateC[0]-$i,$dateC[2]));
			$displayArr[$i] = $sDispatchDate;
		}
	}


	$xmlData  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
	$xmlData .= "<despatch>";
	$xmlData .= "<header>";
	$xmlData .= "<label>Despatch Date</label>";
	$xmlData .= "<label>Despatch Inv No</label>";
	$xmlData .= "<label>Delivery Inv No.</label>";
	$xmlData .= "</header>";
	$xmlData .= "<data>";
	

	foreach ($displayArr as $daKey=>$selDispatchDate) {
		# Despatch Details
		$dispatchSORecs = $homeObj->getSOBasedOnDespatchDate($selDispatchDate);
		
		# Delivery Details
		$deliverySORecs = $homeObj->getSOBasedOnDeliveryDate($selDispatchDate);
	
		$displayDespatchDate = "";
		if (date("Y-m-d")==$selDispatchDate) $displayDespatchDate= "<b>".dateFormat($selDispatchDate)."</b>";
		else $displayDespatchDate= dateFormat($selDispatchDate);

		$numCol = 3;
		$despatchInv = "";
		if (sizeof($dispatchSORecs)>0) {
			$nextRec=	0;
			$invoiceNo = "";
			foreach ($dispatchSORecs as $dsor) {
				$soNo 	= $dsor[0];		
				$invType = $dsor[1];			
				$pfNo 	= $dsor[2];
				$saNo	= $dsor[3];
				$invoiceNo = "";
				if ($soNo!=0) $invoiceNo=$soNo;
				else if ($invType=='T') $invoiceNo = "P$pfNo";
				else if ($invType=='S') $invoiceNo = "S$saNo";
				$nextRec++;

				if($nextRec>1) $despatchInv .= "&nbsp;,&nbsp;"; $despatchInv .= $invoiceNo;
				if($nextRec%$numCol == 0) $despatchInv .= "<br/>";
			}
		}
		

		$deliveryInv = "";
		$numDInvLine = 3;
		if (sizeof($deliverySORecs)>0) {			
			$nextDInvRec	=	0;
			$dInvoiceNo = "";			
			foreach ($deliverySORecs as $dsor) {
				$soNo 	= $dsor[0];		
				$invType = $dsor[1];			
				$pfNo 	= $dsor[2];
				$saNo	= $dsor[3];		
				//# Get Inv No (Config)	
				$dInvoiceNo = getInvFormat($invType, $soNo, $pfNo, $saNo);
				$nextDInvRec++;

				if($nextDInvRec>1) $deliveryInv .= "&nbsp;,&nbsp;"; $deliveryInv .= $dInvoiceNo;
				if($nextDInvRec%$numDInvLine == 0) $deliveryInv .= "<br/>";
			}
		}

		$xmlData .= '<row>';
		$xmlData .= '<despatchDate value="'.$displayDespatchDate.'" />';
		$xmlData .= '<despatchInv value="'.$despatchInv.'" />';
		$xmlData .= '<deliveryInv value="'.$deliveryInv.'" />';
		$xmlData .= '</row>';
	}

	$xmlData .= "</data>";
	$xmlData .= "</despatch>";

	//return $xmlData;
	return new xmlrpcresp(new xmlrpcval($xmlData, "string"));	
}

/**
* Distributor account details
*/
function getDistAccount()
{
	$databaseConnect  	=	new DatabaseConnect();
	$homeObj	  	=	new Home($databaseConnect);
	$dashboardManagerObj	= 	new ManageDashboard($databaseConnect);

	# Pending cheque display days
	list($pChqDays, $crBalDisplayLimit, $overdueDisplayLimit) = $dashboardManagerObj->getPendingChqDisplayDays();
							
	# Current Financial Year
	$dateFrom = date("d/m/Y", mktime(0, 0, 0, 04, 01, (date("Y")-1)));
	$dateTill = date("d/m/Y");

	$pendingChqTillDate = date("d/m/Y", mktime(0, 0, 0, date("m"), (date("d")+$pChqDays), date("Y")));

	$distACRecs =  $homeObj->getDistAccountRecs(mysqlDateFormat($dateFrom), mysqlDateFormat($dateTill), mysqlDateFormat($pendingChqTillDate));	
	$distACRecSize = sizeof($distACRecs);

	$xmlData  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
	$xmlData .= "<distAccount>";
	$xmlData .= "<header>";
	$xmlData .= "<label>Distributor</label>";
	$xmlData .= "<label>Credit Balance</label>";
	$xmlData .= "<label>Pending Cheques</label>";
	$xmlData .= "<label>Overdue Amt</label>";
	$xmlData .= "</header>";
	$xmlData .= "<data>";
	$totOverdueAmt = 0;
	foreach ($distACRecs as $distributorId=>$dar) {
		$distributorName	= $dar[0];
		$creditBalanceAmt	= $dar[1];
		$pendingAmt 		= $dar[2];
		$showPmnt		= $dar[3];
		$overDueAmt		= $dar[4];
		$overDueInvoices	= $dar[5];
		
		$displayPendingChqs = "";
		if ($pendingAmt!=0) {
			$displayPendingChqs = "<span onMouseover=\"ShowTip('$showPmnt');\" onMouseout=\"UnTip();\"><a href='DistributorAccount.php?selectFrom=$dateFrom&selectTill=$pendingChqTillDate&distributorFilter=$distributorId&filterType=PE' class='home-link5'>".number_format($pendingAmt,2,'.',',')."</a></span>";
		}

		$displayOverdueInv = "";
		if ($overDueAmt!=0) {
			$totOverdueAmt += $overDueAmt;
			//if ((float)$overDueAmt>(float)$overdueDisplayLimit) 
			$displayOverdueInv = "<span onMouseover=\"ShowTip('$overDueInvoices');\" onMouseout=\"UnTip();\"><a href='DistributorReport.php?dateFrom=$dateFrom&dateTill=$dateTill&selDistributor=$distributorId&distOverdue=1&cmdSearch=1' class='home-link5'>".number_format($overDueAmt,2,'.',',')."</a></span>";
		}

		$xmlData .= '<row>';
		$xmlData .= '<distributor value="'.$distributorName.'" />';
		$xmlData .= '<creditBal value="'.$creditBalanceAmt.'" />';
		$xmlData .= '<pendingAmt value="'.$pendingAmt.'" />';
		$xmlData .= '<overdueAmt value="'.$overDueAmt.'" />';
		$xmlData .= '</row>';
	}

	$xmlData .= "</data>";
	$xmlData .= "</distAccount>";

	//return $xmlData;
	return new xmlrpcresp(new xmlrpcval($xmlData, "string"));	

}


function onttax($par){
	$amount=$par->getParam(0);
	$amountval=$amount->scalarval(); 
	$taxcalc=$amountval*.15;
	return new xmlrpcresp(new xmlrpcval($taxcalc, "string"));
}



$server = new xmlrpc_server(
    array(
      	"dashboard.onttax" => array("function" => "onttax"),
      	"dashboard.getDailyProdQty" => array("function" => "getDailyProdQty"),
	"dashboard.getMissingChallan" => array("function" => "getMissingChallan"),
	"dashboard.getSOAmt" => array("function" => "getSOAmt"),
	"dashboard.getDespatch" => array("function" => "getDespatch")
    ));



?>