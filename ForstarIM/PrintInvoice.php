<?php
	require("include/include.php");
//	require_once ('components/base/LoadingPort_model.php');
//	require_once ('components/base/ExporterMaster_model.php');
	//$loadingPort_m = new LoadingPort_model();
	//$exporter_m		= new ExporterMaster_model();
	$shipProdTypeArr = array("AC"=>"Aqua Culture", "SC"=>"Seacaught");

	$confirmed = false;
	$currencyCode = "";
	$selInvoiceId 	= $g["invoiceId"];
	$print		= $g["print"];
	$pkgListEnabled = (isset($g["packingList"]) && $g["packingList"]=='Y')?true:false;
	$newTitle		= (isset($g["newTitle"]) && !empty($g["newTitle"]))?trim($g["newTitle"]):"";
	
	$invoiceRec	= $invoiceObj->find($selInvoiceId);
		
		$mainId 	= $invoiceRec[0];		
		$invoiceNo 	= $invoiceRec[1];
		$invDate	= $invoiceRec[2];
		$invoiceDate = dateFormat($invDate);
		$selDate	= dateFormat($invoiceRec[5]);		
		$selCustomerId	= $invoiceRec[3];
		$proformaNo	= $invoiceRec[4];
		$invoiceType	= $invoiceRec[8];
		$purchaseOrderId = $invoiceRec[6];
		$preCarrierPlace 	= $invoiceRec[9];
		$finalDestination	= $invoiceRec[10];
		$containerMarks		= $invoiceRec[11];
		$goodsDescription	= $invoiceRec[12];
		$discount		= $invoiceRec[13];
		$discountChk		= ($discount=='Y')?"checked":"";
		$discountRemark		= $invoiceRec[14];
		$discountAmt		= $invoiceRec[15];
		$totNetWt		= $invoiceRec[16];
		$totGrossWt		= $invoiceRec[17];
		//$totalValueInUSD	= $invoiceRec[18];
		$confirmedStatus	= $invoiceRec[19];

        $shipBillNo		= $invoiceRec[20];
		$shipBillDate		= ($invoiceRec[21]!='0000-00-00')?dateFormat($invoiceRec[21]):"";
		$billLaddingNo		= $invoiceRec[22];
		$billLaddingDate	= ($invoiceRec[23]!='0000-00-00')?dateFormat($invoiceRec[23]):"";
		$loadingPort		= $invoiceRec[24];
		$loadingPortName	= "";
		if ($loadingPort>0) {
			$lpRec =$loadingPortObj->find($loadingPort);	
			$loadingPortName = $lpRec[1];
		}

		// Exporter Details
		$exporter			= $invoiceRec[25];
		$exporterAddress	= $exporterMasterObj->getExporterDetails($exporter);
		$exporterAlphaCode	= $exporterMasterObj->getExporterAlphaCode($exporter);
		$exporterName		= $exporterMasterObj->getExporterName($exporter);


		$shipInvRemark		= stripSlash($invoiceRec[26]);
		//$editLog			= $invoiceRec[27];
		$termsDeliveryPayment = $invoiceRec[28];
		$pkgListRemark		= stripSlash($invoiceRec[29]);
		$unitid=$invoiceRec[31];
		$invoiceunitno=$plantandunitObj->find($unitid);
				$invoiceunitnoinv=$invoiceunitno[1];	

		$unitalphacode=$invoiceRec[32];
		$euCodeId=$invoiceRec[33];

		$selInvDate = ($invoiceNo=="" || $invoiceNo==0)?date('y-m-d'):$invDate;
		$invYearRange = getFinancialYearRange($selInvDate);

				
		list($selCustomerName, $custAddress, $custCountry) = $invoiceObj->getCustomerRec($selCustomerId);
		list($poNo, $poDate, $dischargePort, $paymentMode, $paymentTerms, $portName, $modeOfCarriage, $finalDestCountry, $otherBuyer) = $invoiceObj->getPurchaseOrderRec($purchaseOrderId);
		list($sizeVessalRecs, $vessalDetails, $containerType, $sailingDate, $shippingLine) = $invoiceObj->getContVessalRecs($mainId);
		
		# Get PO Recs
		$poRecs	= $invoiceObj->getInvoiceItemRecs($mainId);
		$totalNumMC = $invoiceObj->getInvoiceRec($mainId);

		$purchaseOrderRec = $invoiceObj->getPORec($purchaseOrderId);
		$currencyCode	=  $purchaseOrderRec[9];
		$selUnitId		=  $purchaseOrderRec[10];
		$unitTxt = ($selUnitId>0)?$spoUnitRecs[$selUnitId]:"";
		
		# Container Recs
		$selContainerRecs = $invoiceObj->getContainerRecs($mainId);

		$invoiceTypeId = $invoiceRec[7];
		$invoiceTypeRec	= $invoiceTypeMasterObj->find($invoiceTypeId);
		$invoiceTypeName = $invoiceTypeRec[1];

		#  Number of Copy
		if ($confirmedStatus=='Y') { 
			$numCopy	= 1;
			$confirmed 	= true;			
		} else {
			$numCopy	= 1;
		}	

		//$displayInvNum = "FFFPL/$invoiceNo/$invYearRange";
		if (($unitid!="") && ($unitid!=0)){
			$exporterAlphaCode=$unitalphacode;
		$displayInvNum = $exporterAlphaCode."/"."U-$invoiceunitnoinv"."/".sprintf("%02d",$invoiceNo)."/$invYearRange";
		}
		else {
		$displayInvNum = $exporterAlphaCode."/".sprintf("%02d",$invoiceNo)."/$invYearRange";
		}
		

		$defaultNumRows = 20;
        $decreaseRow = 1;
        $productRecSize = sizeof($poRecs);
		$pkgDescRecs = $invoiceObj->getPackingDescription($mainId);
		$pkgDescRecSize = sizeof($pkgDescRecs);
		$totInvRecSize = $productRecSize + $pkgDescRecSize; 

		$numRows = ($pkgListEnabled)?12:5; 
		$rowsPerPage = ($pkgListEnabled)?12:5;	/*Products per page*/
        if ($rowsPerPage==$totInvRecSize && sizeof($selContainerRecs)>2) $rowsPerPage = $rowsPerPage-$decreaseRow;
        else if(($rowsPerPage==$totInvRecSize+1 )&& sizeof($selContainerRecs)>2) $rowsPerPage = $rowsPerPage-2;      

		if ($sameBillingAddress=='N') $decreaseRow = 3;
		if ($soRemark!="")	      $decreaseRow += 2	;		
		if ($numRows==$totInvRecSize) $numRows = $numRows-$decreaseRow;

		# Find Balance Rows		
		$balanceRows = ($totInvRecSize%$numRows);               
		if ($balanceRows<=1 && $balanceRows!=0) $numRows = $numRows-1; 
		//$balRecCount = ($totInvRecSize%$rowsPerPage);
		//$totalPage = ($balRecCount==1)?floor($totInvRecSize/$rowsPerPage):ceil($totInvRecSize/$rowsPerPage);
		$totalPage = ceil($totInvRecSize/$rowsPerPage);
		if ($totalPage>1) $rowsPerPage = ($rowsPerPage<10)?12:$rowsPerPage;
		//echo "$totInvRecSize =$rowsPerPage";
		if ($newTitle!="") {
			$displayTxt = strtoupper($newTitle);
		} else {
			$displayTxt = ($pkgListEnabled)?"PACKING LIST":((!$confirmed)?strtoupper($invoiceTypeName)." (Draft)":strtoupper($invoiceTypeName));	
		}
		
		//$A4Height = 'height="1680"';
		//$A4Height = 'height="1350"';
		$A4Height = '';
		$A4Width = '100%';		
		//$numRows = $rowsPerPage = 1;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<title><?=$displayTxt." #".$displayInvNum;?></title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script src="libjs/jquery/jquery-1.3.2.min.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
var key1="119";
var x='';
function handler(e)
{
    var code;
    if (!e) var e = window.event;
    if (e.keyCode) code = e.keyCode;
    else if (e.which) code = e.which;		
    //alert(code);
    if (code=="112") {
		alert("Access denied for printing!!!");
		return false;		
    }
}
if (!document.all){
	window.captureEvents(Event.KEYPRESS);
	window.onkeypress=handler;
} else {
	document.onkeypress = handler;
}
 </script>
<script language="javascript" type="text/javascript">
function printDoc()
	{	
		window.print();	
		return false;
	}
	
	function displayBtn()
	{
		document.getElementById("printButton").style.display="block";			
	}
	
	function printThisPage(printbtn)
	{	
		document.getElementById("printButton").style.display="none";	
		if (!printDoc()) {
			setTimeout("displayBtn()",7000); //3500			
		}		
	}
</script>
</head>
<body>
<form name="frmPrintInvoice" id="frmPrintInvoice" action="PrintInvoice.php">
<?php
	if ($print=='Y' || $pkgListEnabled) {
?>
<table width="90%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<?php
	}
?>
<!--  Strat table here-->
<?php
	# Number of Copy	
 for ($print=0;$print<$numCopy;$print++) {
?>
<table width='90%' cellspacing='1' cellpadding='1'  align='center' border="0" >
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="17" align="right" class="listing-head" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%'>
	<table width='100%' bgcolor="#f2f2f2" border="0">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='center' width="90%" style="padding-left:90px;">
			<span style="font-size:16px;">
			<?=$displayTxt?>	
			</span>
		</td>
			<td width="10%" align="right" class="export-listing-item" id="totalPageDisplay"><?php if ($totalPage>1) { echo "1/";?><span id="totalPage"><?=$totalPage?></span><? }	?></td>
		   </tr>
	</table>
	</td>
  </tr>
  <tr> 
    <td colspan="17" class="listing-head">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top" style="border: 1px solid #000000;">
	<table width="<?=$A4Width?>" border="0" cellpadding="0" cellspacing="0" <?=$A4Height?> id="pageContent">
          <tr>
            <td width="400" colspan="2" rowspan="3" valign="top" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000">
			<table width="200" border="0" >
              <tr>
                <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Exporter</td>
              </tr>
              <tr>
                <td class="export-listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
				<strong>
					<span id="exporterAddr"><?=$exporterAddress?></span>
				</strong>
		</td>
              </tr>
            </table></td>
            <td style="border-right: 1px solid #000000; border-bottom: 1px solid #000000">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;" valign="top" nowrap="true">Invoice No. &amp; Date </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;">
				<?php
					if ($invoiceNo!=0 && $confirmedStatus=='Y') {
				?>
				<table>
						<tr>
						<td class="export-listing-item" nowrap="true">
							<?=$displayInvNum?>
						</td>
						<td nowrap="true">&nbsp;</td>
							<td nowrap="true" class="export-listing-item">
							<?=$invoiceDate?>
							</td>
						</tr>
				</table>
				<?php
					} else echo "&nbsp;";
				?>
			</td>
                  </tr>
                </table>
	</td>
        <td valign="top" style="border-bottom: 1px solid #000000">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Exporter's Ref  </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item">&nbsp;</td>
                  </tr>
                </table></td>
          </tr>
          <tr>
            <td colspan="2" style="border-bottom: 1px solid #000000">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >             
              <tr>
                <td >
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="export-print-listing-head" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">Buyer's Ref.No. &amp; Date </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="left">
					<table>
						<tr>						
						<td class="export-listing-item" nowrap="true">
							<?=$poNo?>
						</td>
						<td nowrap="true">&nbsp;</td>
							<td nowrap="true" class="export-listing-item">
								<?=dateFormat($poDate)?>
							</td>
						</tr>
					</table>
			</td>
                  </tr>
                </table></td>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" align="right" style="padding-right:10px">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2" style="border-bottom: 1px solid #000000">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" >
              <tr>
                <td></td>
                <td></td>
              </tr>              
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Other Reference(s)</td>
                  </tr>
                  <tr>
                    <td class="export-listing-item">&nbsp;</td>
                  </tr>
                </table></td>
                <td>&nbsp;</td>
              </tr>
              </table></td>
          </tr>
          <tr>
            <td width="400" colspan="2" rowspan="2" valign="top" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000" height="150">
			<table width="200" border="0" >
              <tr>
                <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Consignee</td>
              </tr>
		<tr>
			<td class="export-listing-item" nowrap="nowrap" height="20" style="padding-left:5px;padding-right:5px;">
						<strong>M/S.&nbsp;<?=$selCustomerName?></strong>
					</td>
					</tr>
					<tr>
					<td class="export-listing-item" width='350' height="20" colspan="2" style="padding-left:5px;padding-right:5px;">
						<?=$custAddress?>
					</td>
					</tr>
					<tr>
						<td class="export-listing-item" width='200' height="15" colspan="2" style="padding-left:5px;padding-right:5px;">
							<?=$custCountry?>
						</td>
					</tr>
            </table>
		</td>
            <td width="500" colspan="2" style="border-bottom: 1px solid #000000" valign="top" >
			<table width="200" border="0" >
              <tr>
                <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Buyer (if other than Consignee) </td>
              </tr>
              <tr>
                <td class="export-listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" height="50" valign="top">
					<?=$otherBuyer;?>
				</td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td style="border-right: 1px solid #000000;border-bottom: 1px solid #000000">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px; padding-right:5px;">Country of Origin of Goods </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="center">India</td>
                  </tr>
                </table></td>
            <td style="border-bottom: 1px solid #000000"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px; padding-right:5px;">Country of Final Destination  </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=$finalDestCountry?></td>
                  </tr>
                </table></td>
          </tr>
          <tr>
            <td style="border-right: 1px solid #000000;border-bottom: 1px solid #000000" valign="top">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px; padding-right:5px;">Pre-Carriage by </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="center">By <?=$modeOfCarriage?> </td>
                  </tr>
                </table></td>
            <td style="border-right: 1px solid #000000;border-bottom: 1px solid #000000" valign="top">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px; padding-right:5px;" valign="top">Place of Receipt by Pre-carrier </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=$preCarrierPlace?></td>
                  </tr>
                </table></td>
            <td colspan="2" rowspan="3" valign="top" style="border-bottom: 1px solid #000000" height="150">
				<?
				if (!$pkgListEnabled) {	
				?>
					<table width="100%" border="0" cellpadding="0" cellspacing="0" >
					  <tr>
						<td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Terms of Delivery and payment  </td>
					  </tr>
					  <tr>
						<td class="export-listing-item" style="padding-left:5px; padding-right:5px;" height="100" valign="top"><?php if (!empty($termsDeliveryPayment)) { echo nl2br($termsDeliveryPayment); } else { ?>C&amp;F&nbsp;<?=$dischargePort?><br>
						PAYMENT BY <?=$paymentMode?> &nbsp;<?=$paymentTerms?><br>VESSEL <?=$shippingLine?> ON DATE: <?=($sailingDate!="0000-00-00")?date("d.m.Y",strtotime($sailingDate)):""?><? }?></td>
					  </tr>
					</table>
				<?php } else { ?>
				&nbsp;
				<?php } ?>
			</td>
          </tr>
          <tr>
            <td style="border-right: 1px solid #000000;border-bottom: 1px solid #000000" valign="top">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px; padding-right:5px;" valign="top">Vessel/ Flight No. </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=$vessalDetails?></td>
                  </tr>
                </table>
		</td>
            <td style="border-right: 1px solid #000000;border-bottom: 1px solid #000000" valign="top">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Port of Loading </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=$loadingPortName?><!--J.N.P.T, INDIA--></td>
                  </tr>
                </table></td>
            </tr>
          <tr>
            <td style="border-right: 1px solid #000000;border-bottom: 1px solid #000000" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Port of Discharge </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=$dischargePort?></td>
                  </tr>
                </table></td>
            <td style="border-right: 1px solid #000000;border-bottom: 1px solid #000000" valign="top">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;" valign="top">Final Destination </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=(!$finalDestination)?$finalDestCountry:$finalDestination?></td>
                  </tr>
                </table></td>
            </tr>          
          <tr>
            <td colspan="4" valign="top">
	<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" id="listingTbl">	
	<tr align="center">
        	<th class="listing-head" style="padding-left:5px; padding-right:5px; border-width:0px" width="100">Marks &amp; Nos/ Container No </th>
        	<th class="listing-head" style="padding-left:5px; padding-right:5px;" width="10%">No. &amp; kind of PKgs </th>
	    	<th class="listing-head" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;">Description of Goods </th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" rowspan="2" valign="top">Quantity<br/><br/>In <span class="replaceUnitTxt">KGS</span></th>
		<?php
			if ($pkgListEnabled) {
		?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" rowspan="2" valign="top">Net Wt.<br/><br/>(<span class="replaceUnitTxt">KGS</span>)</th> 
        <th class="listing-head" style="padding-left:5px; padding-right:5px; " rowspan="2" valign="top">Gross Wt.<br/><br/>(<span class="replaceUnitTxt">KGS</span>)</th>
		<? } else {?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" rowspan="2" valign="top">Rate<br/><br/><span class="replaceCY">US$</span><br/>Per <span class="replaceUnitTxt">KGS</span></th> 
        <th class="listing-head" style="padding-left:5px; padding-right:5px; " rowspan="2" valign="top">Amount<br/><br/>C&amp;F <span class="replaceCY">US$</span></th>
		<? }?>
	</tr>
	<tr>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px; ">
			<table>
				<tr><td class="export-listing-item" valign="top">
					<?=$containerMarks?>
				</td></tr>
				<?php
				$c = 0;
				foreach ($selContainerRecs as $rec) {
                                        $c++;
					$containerNo 	= $rec[2];
					$sealNo		= $rec[4];
				?>
				<tr><td class="export-listing-item" nowrap="true">Container No:<?=$containerNo?></td></tr>
				<tr><td class="export-listing-item" nowrap="true">Seal No:<?=$sealNo?></td></tr>
				<?php
					}
				?>
			</table>
			
		</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-bottom: 1px dotted #000000;" >			
			<?=$totalNumMC?>&nbsp;Cartons
		</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-bottom: 1px dotted #000000; border-right: 1px solid #000000;" align="left" width="50%">
			<?=($goodsDescription!="")?nl2br($goodsDescription):"&nbsp;"?>
		</td>		
	</tr>
	<?php		
		$totAmtArr = array();
		$j = 0;
		$i = 0;
		$p= 0;				
		$totalValueInUSD = 0;
		$totalNetWt = 0; 
		$dupRow = 0;	/*No of empty rows*/
		$loopChk = 0;	/*flag to check whether the last product is in loop */
        $increFromSecpage = 0;
		$euCodeArr = array();
		$rowCountPerPage = 0;
		$rowWithPkgCount = 0;

		foreach($poRecs as $poi) {
			$i++;
			$p++;
			$rowCountPerPage++;
			$rowWithPkgCount++;
			if( $j>0 ) $increFromSecpage++;
			$next = $poRecs[$i][2];
			$sPOEntryId 	= $poi[0];
			$sFish 		= $poi[1];
			$sProcessCode 	= $poi[2];
			$sEuCode	= $poi[3];
			$sEuCodeId	 = $poi[28];
			$sEuCodeAddr = $poi[29];
			$euCodeArr[$sEuCode] = $sEuCodeAddr;

			$sBrand		= $poi[4];
			$sBrdFrom	= $poi[13];					
			$sGrade	  	= $poi[5];
			$sFreezingStage = $poi[6];
			$sFrozenCode    = $poi[7];
			$sMCPacking	= $poi[8];
			$selPCId	= $poi[14];
			$mcInPO		= $poi[16];
			$mcInInvoice	= $poi[17];
			$totalMC	+= $mcInInvoice;
			$pricePerKg	= $poi[18];			
			//$valueInINR	= $poi[20];
			//$totalValueInINR += $valueInINR;
			$filledWt	= $poi[21];
			$declWt = $poi[27];
			$weightType = $poi[26];
			$calWt = ($weightType=='NW')?$declWt:$filledWt;

			$numPacks	= $poi[22];			
			$invoiceRawEntryId = $poi[15];
			
			$disProdItem	= $sProcessCode."&nbsp;".$sEuCode."&nbsp;".$sBrand."&nbsp;".$sGrade."&nbsp;".$sFreezingStage."&nbsp;".$sFrozenCode."&nbsp;".$sMCPacking;
			$qtyInKg	= $calWt*$numPacks*$mcInInvoice;
			//$totalNetWt += $qtyInKg;
			$displayQtyInKg = number_format($qtyInKg,3,'.','');
			$displayQtyInKg = ($selUnitId==2)?number_format((KG2LBS*$displayQtyInKg),3,'.',''):$displayQtyInKg;
			$totalNetWt += $displayQtyInKg;

			//$valueInUSD	= $poi[19];
			$valueInUSD	= $displayQtyInKg*$pricePerKg;
			$totalValueInUSD += $valueInUSD;	
			$totAmtArr[$j] += $valueInUSD;


			$productDescr = stripSlash($poi[23]);
			$grossWt = $poi[25];
			$packingDescRecs = $invoiceObj->getPackingDescription($mainId,$invoiceRawEntryId);
			$packingDescRowId = $packingDescription = $prdOriginType = "";
			if(sizeof($packingDescRecs)>0)	{				
					$packingDescRowId = $packingDescRecs[0][0];
					$packingDescription = $packingDescRecs[0][2];
					$prdOriginType	= $packingDescRecs[0][4];
			}

	?>	
  <tr id="content_">
        <?php 
		if ($i==1) { 
	?>
		<!--td height='100' class="export-listing-item" style="padding-left:5px; padding-right:5px; " rowspan="<?=sizeof($poRecs);?>">&nbsp;</td-->		
	<?php 
		}
	?>
		<td height='20' class="export-listing-item" style="padding-left:5px; padding-right:5px; ">&nbsp;</td>
        <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  " align="center"><?=$mcInInvoice?>&nbsp;Cartons</td>
	<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" nowrap="nowrap">
		<?=($productDescr!="")?nl2br($productDescr):$disProdItem?>
	</td>
	<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"><?=$displayQtyInKg;?></td>
	<?php
			if ($pkgListEnabled) {
		?>
			<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"><?=$displayQtyInKg;?></td>
			<td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right"><?=($grossWt!=0)?$grossWt:"&nbsp;";?></td>
		<? } else {?>
			<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"><?=$pricePerKg;?></td>
			<td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right"><?=number_format($valueInUSD,2,'.','');?></td>
		<? }?>		
  </tr>
  <?php		
	
  if(($prev=="" && $sProcessCode!=$next) || ($prev!="" && $sProcessCode!=$next))    {

	  if (sizeof($packingDescRecs)>0)	{
		  
			//preg_match_all("/(\n)/", $packingDescription, $matches);
			//$lineCount = count($matches[0]) + 1;
			//$rowWithPkgCount = $rowWithPkgCount+$lineCount;
			$rowWithPkgCount++;
  ?>      
		<tr>
                <td height='20' class="export-listing-item" style="padding-left:5px; padding-right:5px; ">&nbsp;</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  " align="center"></td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" width="200">
					PACKING: <?=nl2br($packingDescription) ;?>
				</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"></td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"></td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right"></td>
           </tr>
		<?php
		  }
	   ?>
   <?php
	    if ($prdOriginType!="")	{			
  ?>      
		<tr>
                <td height='20' class="export-listing-item" style="padding-left:5px; padding-right:5px; ">&nbsp;</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  " align="center"></td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" nowrap="nowrap">
					<? echo strtoupper("INDIAN ORIGIN ".$shipProdTypeArr[$prdOriginType]." PRODUCT")?>
				</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"></td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"></td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right"></td>
           </tr>
		   <?php
				$scMsgStyle= "display:none;";
				if ($prdOriginType=='SC') {
					$scMsgStyle= "";					
				}
			?>
		   <tr style="<?=$scMsgStyle?>">
                <td height='20' class="export-listing-item" style="padding-left:5px; padding-right:5px; ">&nbsp;</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  " align="center"></td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" nowrap="nowrap">
					CAUGHT IN INDIAN OCEAN FAO AREA ZONE 51
				</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"></td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"></td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right"></td>
           </tr>
		<?php
		  }
	   ?>

  <?
  }
$prev = $sProcessCode;

		/* Modified By-> Vineeth-28th oct 2011 */
		//echo "<br>$i == $totInvRecSize";
		if($i == $totInvRecSize) {
			$loopChk = 1;
			$dupRow = 4;
		}

		//echo "<br>$i==$rowsPerPage::$rowCountPerPage==$rowsPerPage::: $rowCountPerPage==$rowsPerPage:::pkgCount=$pkgCount";
		if($rowCountPerPage==$rowsPerPage) {			
			   if(sizeof($selContainerRecs) > 1 )   {
				   if(sizeof($selContainerRecs)==2) $dupRow =1;
				   else $dupRow =0;
			   }
			   else $dupRow = ($totalPage>1)?15:22;
		} 
		else if($i<$rowsPerPage && $j==0 && $loopChk==1)	{
				if(sizeof($selContainerRecs) > 1 )   {
				   if(sizeof($selContainerRecs)==2) $dupRow = 0;
				   else $dupRow = 0;
				}
				else $dupRow = $rowsPerPage*2.5;                      
		} 

                //if($j>0 && $loopChk!=1 && $i==$rowsPerPage )   {
				if($j>0 && $loopChk!=1 && $rowCountPerPage==$rowsPerPage )   {
                        $dupRow = 22;
						//echo "=>1";
                }
				else if($j>0 && $loopChk==1)	{  
					$dupRow = (10-(($increFromSecpage%10 == 0)?10:$increFromSecpage%10)) + 7;
					//echo "=>2";
				} else if ($rowWithPkgCount==$totInvRecSize && $rowCountPerPage<$rowsPerPage) {
						// Last Page
						$dupRow = $rowsPerPage-$rowCountPerPage;
						//$calcRow = $rowsPerPage-$rowCountPerPage;
						//$dupRow = ($calcRow>10)?($calcRow*2):($calcRow*5);
						//$dupRow = $calcRow*2;
						//echo "=>3";
				} else {
					$calcBalRow = $dupRow-$rowWithPkgCount; 
					$dupRow = ($calcBalRow>0)?($calcBalRow*2):$dupRow;
					//echo "=>4";
				}
				
          // echo "<br>DupRow=".$dupRow.":::rowWithPkgCount=$rowWithPkgCount::==>$rowsPerPage-$rowCountPerPage::$totInvRecSize";
			for($k=0;$k<$dupRow;$k++)    {
                  ?>
              <tr id="dupRow_<?=$k?>">
                <td height='15' class="export-listing-item" style="padding-left:5px; padding-right:5px; ">&nbsp;</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="center">&nbsp;</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" nowrap="nowrap">&nbsp;</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right">&nbsp;</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right">&nbsp;</td>
                <td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right"></td>
              </tr>
          <?php
			}
		//echo "<br>$i=".$poRecs[$i][0]."==".$poRecs[$i+1][0];
		//echo "($totInvRecSize!=$rowsPerPage && $rowCountPerPage==$rowsPerPage)";
		//$nextRecId = $poRecs[$i+1][0];
		if (($totInvRecSize!=$rowsPerPage && $rowCountPerPage==$rowsPerPage))  {
			$rowsPerPage =$rowsPerPage+10;
			$dupRow = 0;
			$j++; 
			$rowCountPerPage = 0;
	?>
	<!--tr>
		<td height='100' class="export-listing-item" style="padding-left:5px; padding-right:5px; ">&nbsp;</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  " align="center"></td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" nowrap="nowrap">
		</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"></td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right"></td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right"></td>
	</tr-->
	<?php
	if (!$pkgListEnabled) {
    ?>
	<tr>
		<td height='30' colspan="3" align="center" style="padding-left: 150px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp</td>
		<td style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
	    <td class="export-listing-item" style="padding-left:5px; padding-right:5px; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
	  </tr>
	<!--tr>
	    <td height='30' colspan="5" align="right" style="border-right: 1px solid #000000;border-top: 1px solid #000000;">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" nowrap>Amount in Words  </td>
                    <td class="listing-head" nowrap align="right" valign="bottom" rowspan="2">Total:</td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px;">	</td>                    
                  </tr>
            </table>
		</td>		
	    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-bottom: 1px solid #000000;border-top: 1px solid #000000;" align="right" valign="middle">
			<strong><?=number_format($totalValueInUSD,2,'.','');?></strong>
		</td>
	</tr-->
	<tr>
	    <td height='30' colspan="3" align="right">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" nowrap><!--Amount in Words-->&nbsp;</td>
                    <td class="listing-head" nowrap align="right" valign="middle" rowspan="2">Total:</td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px;">&nbsp;</td>
                  </tr>
                </table>
		</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-bottom: 1px solid #000000;border-left: 1px solid #000000;" align="right" valign="middle">
			<strong><?=number_format($totalNetWt,3,'.','');?></strong>
		</td>
		<td style="border-bottom: 1px solid #000000;border-left: 1px solid #000000;">&nbsp;</td>
	    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-bottom: 1px solid #000000;border-left: 1px solid #000000;" align="right" valign="middle">
			<strong><?=number_format($totalValueInUSD,2,'.','');?></strong>
		</td>
	    </tr>
	<?php
	} else {	
	?>
	<tr>
	    <td height='30' colspan="5" align="right" style="border-top: 1px solid #000000;">&nbsp;</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-top: 1px solid #000000;" align="right" valign="middle">&nbsp;</td>
	</tr>
	<?php } ?>
	    </table>
	</td>
	</tr>
<!--  Sign Starts-->
<tr>
	<td colspan="17" align="center" valign="bottom">
		<table width='99%' cellpadding='0' cellspacing='0' align="center">
			<tr>	
				<td>
					<table cellspacing='0' cellpadding='0' width="100%">
		<tr>
			<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<?php
					if ($j==1) {
					?>
					 <tr>
						<td colspan="2" nowrap class="export-listing-item">
						MANUFACTURER/PROCESSOR/PACKER:<br><?=implode(",",array_values($euCodeArr))?>
						<br>EIC APPROVAL.NO.<?=implode(",",array_keys($euCodeArr))?>
						
						</td>
                    </tr>
					 <?php }?>
                  <tr>				 
                    <td class="export-listing-item">&nbsp;</td>
                    <td class="export-listing-item">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="export-listing-item">&nbsp;</td>
                    <td class="export-listing-item">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="export-listing-item">
					<?php
					if (!$pkgListEnabled) {	
					?>
					<table width="300" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" style="line-height:normal">Declaration:</td>
                  </tr>
                  <tr>
                    <td class="export-listing-item">We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct </td>
                  </tr>
                </table>
					<?php } else { ?>
					&nbsp;
					<? }?>
				</td>
                    <td class="export-listing-item" valign="top">
					<table width="200" align="right" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-listing-item" nowrap style="padding:5px; border-left: 1px solid #000000; border-top: 1px solid #000000;"><strong>For <?=$exporterName?></strong><br><br><br><br><div align="right">Authorised Signatory</div></td>
                  </tr>
                </table></td>
                  </tr>
                </table>
		</td>
	</tr>	
	</table>
	</td>
	</tr>
	</table>
	</td>
  </tr>
<!-- Ends Here -->	
	</table>
	</td></tr>
     </table>
	 </td>
	 </tr>
     </table>
	<!-- Setting Page Break start Here-->
	 <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='100%' cellspacing='1' cellpadding='1' align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0">	  		
	   <tr>
	<td align="center" valign="top" width='100%'>
	<table width='100%' bgcolor="#f2f2f2" border="0">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='center' width="90%" style="padding-left:90px;">
			<span style="font-size:16px;">
			<?=$displayTxt?>	
			</span> <!-- - Cont. -->
		</td>
			<td width="10%" align="right" class="export-listing-item">Cont.&nbsp;&nbsp;<?php if ($totalPage>1) echo ($totalPage==$j)?$totalPage:($j+1); echo "/";	?><span id="totalPage"><?=$totalPage?></span></td>
		   </tr>
	</table>
	</td>
  </tr>
	<tr><td height="5"></td></tr>	
	<tr>
	<td align="left" valign="top" width='100%' style="border:1px solid #000000">
	<table width='100%' cellpadding='0' cellspacing='0' align="center">
		<tr>
            <td width="400" colspan="2" rowspan="3" valign="top" style="border-right:1px solid #000000;border-bottom:1px solid #000000">
			<table width="200" border="0" >
              <tr>
                <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;">Exporter</td>
              </tr>
              <tr>
                <td class="export-listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">
				<strong>
					<span id="exporterAddr"><?=$exporterAddress?></span>
				</strong>
				</td>
              </tr>
            </table></td>
            <td style="border-right:1px solid #000000;border-bottom:1px solid #000000;">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;" valign="top" nowrap="true">Invoice No. &amp; Date </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="left">
				<?php
					if ($invoiceNo!=0 && $confirmedStatus=='Y') {
				?>
				<table>
						<tr>
						<td class="export-listing-item" nowrap="true">
							<?=$displayInvNum?>
						</td>
						<td nowrap="true">&nbsp;</td>
							<td nowrap="true" class="export-listing-item">
							<?=$invoiceDate?>
							</td>
						</tr>
				</table>
				<?php
					} else echo "&nbsp;";
				?>
			</td>
                  </tr>
                </table>
	</td>
        <td valign="top" style="border-bottom:1px solid #000000;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true" valign="top">Exporter's Ref</td>
                  </tr>
                  <tr>
                    <td class="export-listing-item">&nbsp;</td>
                  </tr>
                </table></td>
          </tr>
          <tr>
            <td colspan="2" style="border-bottom:1px solid #000000;">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >              
              <tr>
                <td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="export-print-listing-head" nowrap="true" style="padding-left:5px; padding-right:5px;">Buyer's Ref.No. &amp; Date </td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="left">
					<table>
						<tr>						
						<td class="export-listing-item" nowrap="true">
							<?=$poNo?>
						</td>
						<td class="export-print-listing-head" nowrap="true">&nbsp</td>
							<td nowrap="true" class="export-listing-item">
								<?=dateFormat($poDate)?>
							</td>
						</tr>
					</table>
			</td>
                  </tr>
                </table></td>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2" style="border-bottom:1px solid #000000;">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" >              
				  <tr>
					<td>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					  <tr>
						<td class="export-print-listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Other Reference(s)  </td>
					  </tr>
					  <tr>
						<td class="export-listing-item">&nbsp;</td>
					  </tr>
					</table></td>
					<td>&nbsp;</td>
				  </tr>
				  </table>
	</td>
  </tr>
  <tr>
	<td colspan="17" align="center">
  	  <table width="100%" cellpadding="2" cellspacing="0" id="listingTbl_">
	<tr align="center">
		<th class="listing-head" style="padding-left:5px; padding-right:5px; " width="100">Marks &amp; Nos/ Container No </th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px; " width="10%">No. &amp; kind of PKgs </th>
	    <th class="listing-head" style="padding-left:5px; padding-right:5px;  border-right:1px solid #000000;">Description of Goods </th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;  border-right:1px solid #000000;" valign="top">Quantity<br/><br/>In <span class="replaceUnitTxt">KGS</span></th>
		<? if ($pkgListEnabled) { ?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" valign="top">Net Wt.<br/><br/>(<span class="replaceUnitTxt">KGS</span>)</th> 
        <th class="listing-head" style="padding-left:5px; padding-right:5px; " valign="top">Gross Wt.<br/><br/>(<span class="replaceUnitTxt">KGS</span>)</th>
		<? } else {?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;  border-right:1px solid #000000;" 
valign="top">Rate<br/><br/><span class="replaceCY">US$</span><br/>Per <span class="replaceUnitTxt">KGS</span></th> 
        <th class="listing-head" style="padding-left:5px; padding-right:5px; " valign="top">Amount<br/><br/>C&amp;F <span class="replaceCY">US$</span></th>
		<? } ?>
      </tr>
	  <?php
		# Number of Page > 1
		$forwardAmt = array_sum($totAmtArr);
	  ?>
	  <? if (!$pkgListEnabled) { ?>
	<tr>       
		<td height='15' class="export-listing-item" style="padding-left:5px; padding-right:5px; ">&nbsp;</td>
        <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  " align="center">&nbsp;</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" nowrap="nowrap">
			Brought Forward
		</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right"><?=number_format($forwardAmt,2,'.','');?></td>
	 </tr>
	 <?php } ?>
	<tr>
		<td height='20' class="export-listing-item" style="padding-left:5px; padding-right:5px; ">&nbsp;</td>
        <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  " align="center">&nbsp;</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" nowrap="nowrap">
			&nbsp;
		</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right">&nbsp;</td>				
	 </tr>	
	<?php 
			} // Page break Condition ends here 			
		} // Product Loop Ends here              
	?>
	<!--tr rowspan='8' height='100' >
		<td nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td nowrap="nowrap" class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>	
		<td class="listing-head" align="right" style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td class="export-listing-item" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>		
		<td class="export-listing-item" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="export-listing-item" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="export-listing-item" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="export-listing-item" style="padding:0px 3px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
		<td class="export-listing-item" style="padding:0px 3px; border-bottom: 1px solid #000000;" align="right">&nbsp;</td>
	</tr-->
	<?php
		if ($discount=='Y' && !$pkgListEnabled) {
			$totalValueInUSD += $discountAmt;
	?>
				<tr id="discountRow">
					<td height='30' colspan="3" align="center" style="padding-left: 45px; border-right: 1px solid #000000;" class="listing-head">
						<?=$discountRemark?>
					</td>
					<td style="border-right: 1px solid #000000;">&nbsp;</td>
					<td style="border-right: 1px solid #000000;">&nbsp;</td>
					<td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="right">
						<?=number_format($discountAmt,2,'.','');?>
					</td>
				</tr>
	<?php
		}
                else {
                    ?>
				<tr id="discountRow">
					<td height='30' colspan="3" align="center" style="padding-left: 45px; border-right: 1px solid #000000;" class="listing-head">
					</td>
					<td style="border-right: 1px solid #000000;">&nbsp;</td>
					<td style="border-right: 1px solid #000000;">&nbsp;</td>
					<td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="right">
					</td>
				</tr>
        <?php
                }
	?>
	<tr>
		<td height='30' colspan="3" align="center" style="padding-left: 150px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
			<table>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-listing-item" align="left" nowrap="true">Total Net Weight:</td>
								<td class="export-listing-item" nowrap="true">
									<strong><?=number_format($totalNetWt,3,'.','');?>&nbsp; <span class="replaceUnitTxt">KGS</span></strong>
								</td>
							</tr>
						</table>
					</td>
					<td>
							<table>
								<tr>
									<td class="export-listing-item" align="left" nowrap="true">Total Gross Weight:</td>
									<td class="export-listing-item" nowrap="true">
											<strong><?=number_format($totGrossWt,3,'.','');?>&nbsp; <span class="replaceUnitTxt">KGS</span></strong>				
									</td>
								</tr>
							</table>
					</td>
				</tr>
				<?php	
					$zeroChkPattern = "/^[0]*$/";
					if (!preg_match($zeroChkPattern,trim($shipBillNo)) && !preg_match($zeroChkPattern,trim($billLaddingNo))) {
				?>
				<tr>
					<td>
						<table>
							<tr>
								<td class="export-listing-item" align="left" nowrap>S/B No:&nbsp;</td>
								<td class="export-listing-item" nowrap>
									<strong><?=$shipBillNo?></strong>
								</td>
								<td class="export-listing-item" align="left" nowrap>Dated&nbsp;</td>
								<td class="export-listing-item" align="left" nowrap>
									<strong><?=$shipBillDate?></strong>
								</td>
							</tr>
						</table>
					</td>
					<td>
						<table>
							<tr>
								<td class="export-listing-item" align="left" title="Bill of Ladding" nowrap>BL No:&nbsp;</td>
								<td class="export-listing-item" nowrap>
									<strong><?=$billLaddingNo?></strong>
								</td>
								<td class="export-listing-item" align="left" nowrap>Dated&nbsp;</td>
								<td class="export-listing-item" align="left" nowrap>
									<strong><?=$billLaddingDate?></strong>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php
					}			
				?>
			</table>			
		</td>
		<td style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
		<td style="border-right: 1px solid #000000; border-bottom: 1px solid #000000;">&nbsp;</td>
	    <td class="export-listing-item" style="padding-left:5px; padding-right:5px; border-bottom: 1px solid #000000;" align="right">
			&nbsp;
		</td>
	  </tr>
	  <?php
			if (!$pkgListEnabled) {
	  ?>
	  <tr>
	    <td height='30' colspan="3">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-print-listing-head" nowrap style="padding-left:5px;">Amount in Words</td>
                    <td class="listing-head" nowrap align="right" valign="middle" rowspan="2" style="padding-right:5px;">Total:</td>
                  </tr>
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px;">C&amp;F <span class="replaceCY">US$</span> 
					<?php /* $input = ceil($totalValueInUSD); echo convert($input);*/ echo makewords($totalValueInUSD);?> Only
					</td>
                  </tr>
                </table>
		</td>
		<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-bottom: 1px solid #000000;border-left: 1px solid #000000;" align="right" valign="middle">
			<strong><?=number_format($totalNetWt,3,'.','');?></strong>
		</td>
		<td style="border-bottom: 1px solid #000000;border-left: 1px solid #000000;">&nbsp;</td>
	    <td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-bottom: 1px solid #000000;border-left: 1px solid #000000;" align="right" valign="middle">
			<strong><?=number_format($totalValueInUSD,2,'.','');?></strong>
		</td>
	    </tr>
		<?php } else { ?>
			<tr>
				<td height='30' colspan="5" align="right">&nbsp;</td>
				<td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right" valign="middle">&nbsp;</td>
			</tr>
		<?php } ?>
	     </table></td>
            </tr>
          <tr>
            <td colspan="4">
		<table width="100%" align="center" cellpadding="2" cellspacing="0">
	  <tr>
	    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" >
				<?php
				if ($j==0) {
				?>
                  <tr>
                    <td colspan="2" class="export-listing-item" style="padding-left:5px;"><!-- style="border-bottom: 1px dotted #000000;"-->
					MANUFACTURER/PROCESSOR/PACKER:<br><?=implode(",",array_values($euCodeArr))?>
						<br>EIC APPROVAL.NO.
						<?php echo $euCodeId;?>
						<?//=implode(",",array_keys($euCodeArr))?>
						<?php
						if ($shipInvRemark!="") {
						?>
						<br><!--Remarks:&nbsp;--><?php echo nl2br($shipInvRemark);?>
						<?php
						}	
						?>
						<?php
						if ($pkgListEnabled && !empty($pkgListRemark)) {
						?>
						<br><?php echo nl2br($pkgListRemark);?>
						<?php
						}	
						?>
						
					</td>
                    </tr>
					<?php }?>
                  <tr>
                    <td class="export-listing-item">&nbsp;</td>
                    <td class="export-listing-item">&nbsp;</td>
                  </tr>                 
                  <tr>
                    <td class="export-listing-item" style="padding-left:5px;">
					<?php
					if (!$pkgListEnabled) {	
					?>
					<table width="350" border="0" cellpadding="0" cellspacing="0" >
					  <tr>
						<td class="export-print-listing-head" style="line-height:normal">Declaration:</td>
					  </tr>
					  <tr>
						<td class="export-listing-item">We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct </td>
					  </tr>
	                </table>  
					<?php 
						} else {	
					?>
					&nbsp;
					<? } ?>
				</td>
                    <td class="export-listing-item" valign="top"><table width="200" align="right" cellpadding="0" cellspacing="0" >
                  <tr>
                    <td class="export-listing-item" nowrap style="padding:5px; border-left: 1px solid #000000; border-top: 1px solid #000000;"><strong>For <?=$exporterName?></strong><br><br><br><br><br><br><div align="right">Authorised Signatory</div></td>
                  </tr>
                </table></td>
                  </tr>
                </table></td>
	    </tr>
	     </table></td>
            </tr>
        </table></td>
      </tr>
    </table>
	</td>
          </tr>
	</table>
	</td>
  </tr>
	</table>
	<!--/td></tr></table-->		
</form>	
<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
<?php
	} // Num copy Ends here
?>
</body>
</html>
<script language="JavaScript" type="text/javascript">	
$(document).ready(function() {
	//alert($('#listingTbl tr').length+":::"+$("#listingTbl tr[id^=dupRow_]").length);
	//var overAllListingRow = 52;
	var overAllListingRow = 38;
	var mainTblLength = $('#listingTbl tr').length;
	//var dupRowSize = $("#listingTbl tr[id^=dupRow_]").length;
	var balanceRow = overAllListingRow-mainTblLength;

	var rowHtml = '<tr id="appendedDupRow_">'
                +'<td height="20" class="export-listing-item" style="padding-left:5px; padding-right:5px; ">&nbsp;</td>'
                +'<td class="export-listing-item" style="padding-left:5px; padding-right:5px;" align="center">&nbsp;</td>'
                +'<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" nowrap="nowrap">&nbsp;</td>'
                +'<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right">&nbsp;</td>'
                +'<td class="export-listing-item" style="padding-left:5px; padding-right:5px;  border-right: 1px solid #000000;" align="right">&nbsp;</td>'
                +'<td class="export-listing-item" style="padding-left:5px; padding-right:5px; " align="right"></td>'
              +'</tr>';
    // First page
	if (balanceRow>0)
	{		
		var apdRowHtml = ""; 
		for (var i=0;i<balanceRow;i++ )
		{
			apdRowHtml += rowHtml;
		}
		$("#listingTbl tr[id^=dupRow_0]").closest( "tr" ).before(apdRowHtml);
	}

	// Multiple page
	var totalRowsInExtendedPage = 58;
	$('table[id^=listingTbl_]').each(function(index) {
		var curTblRowLength = $(this).find('tr').length;
		var ctBalRow = totalRowsInExtendedPage-curTblRowLength;
		if (ctBalRow>0)
		{		
			var ctApdRowHtml = ""; 
			for (var i=0;i<ctBalRow;i++ )
			{
				ctApdRowHtml += rowHtml;
			}

			$(this).find( "tr[id^=discountRow]" ).before(ctApdRowHtml);
		}
	});

<?php 
if ($totalPage>1 && $j>0) { 
?>
	
			$('span[id*=totalPage]').each(function(i){
				$(this).html('<?php echo ($totalPage==$j)?$totalPage:($j+1);?>');
			});		
<?php
} else {	
?>
$("#totalPageDisplay").hide();
<? }?>
			<?php
			if ($currencyCode!="") {	
			?>
			$(".replaceCY").html('<?=$currencyCode?>');
			<?php
			}	
			?>
			<?php
			if ($unitTxt!="")
			{	
			?>
				$(".replaceUnitTxt").html('<?=$unitTxt?>');
			<?php
			}	
			?>
});
</script>
