<?php	
	require("include/include.php");

	$dateFrom 	= $g["dateFrom"];
	$dateTo 	= $g["dateTo"];	
	$selFish 	= $g["selFish"];
	$selCustomerId 		= $g["customer"];
	$selFishCategoryId 	= $g["fishCategory"];
	$reportType	= $g["reportType"];
	$packType	= $g["packType"];	
	$packTypeArr 	= array("MC"=>"MC", "LS"=>"Loose Slab");
	//if ($packType=="MC") $displayPackHead = "MC";

	if ($dateFrom && $dateTo) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTo);
		
		/*		
		$stkRGroupList = $dailyStockReportObj->stkRGroupList($fromDate, $tillDate, $selFish, $selCustomerId, $selFishCategoryId);
		$stkReportRecSize = sizeof($stkRGroupList);
		$stkGradeMaxCount = $dailyStockReportObj->maxGradeCount($fromDate, $tillDate, $selFish, $selCustomerId, $selFishCategoryId);
		*/

		$stkRGroupList = $dailyStockReportObj->stkRGList($fromDate, $tillDate, $selFish, $selCustomerId, $selFishCategoryId, $reportType, $packType);
		$stkReportRecSize = sizeof($stkRGroupList);
		$stkGradeMaxCount = $dailyStockReportObj->stkRGroupGradeMaxCount($fromDate, $tillDate, $selFish, $selCustomerId, $selFishCategoryId, $reportType, $packType);
	}



	$displayHead = "DAILY STOCK ".$packTypeArr[$packType]." REPORT";
	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>Daily Stock Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
	function displayBtn()
	{
		document.getElementById("printButton").style.display="block";
	}
	function printThisPage(printbtn)
	{
		document.getElementById("printButton").style.display="none";
		window.print();
		setTimeout("displayBtn()",3000);		
	}
</script>
<style type="text/css" media="print">
@page {
  size: A4 landscape;
}
</style>
</head>
<body >
<div>
<form name="frmPrintDailyStockReport">
<table width="85%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right">
	<input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
</td>
</tr>
</table>
<?php
	# Report
	if ($stkGradeMaxCount) {
?>
<table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="3"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" height="5" ></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_ADDRESS?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_PHONE?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="printPageHead" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='center' colspan='2' style="text-transform:uppercase;"><?=$displayHead?><br/>
		<span style="font-size:11px;">
		 FROM <?=$dateFrom?> TO <?=$dateTo?> 
		</span>
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr>
	</table>
	</td>
  </tr>
<tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" style="padding-left:5px;padding-right:5px;">
<table width="95%" cellpadding="1" cellspacing="1" bgcolor="#999999" class="print" style="margin:0px;" height="100%">
	<?php
	if ($stkReportRecSize>0) {
		$rowHeadStyle = "padding-left:2px; padding-right:2px;font-size:10px;line-height:normal;";
	?> 		
	<tr bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="<?=$rowHeadStyle?>">Product Specification</td>
		<td class="listing-head" style="<?=$rowHeadStyle?>">Buyer,&nbsp;EU Code</td>				
		<td class="listing-head" style="<?=$rowHeadStyle?>">Brand</td>
		<td class="listing-head" style="<?=$rowHeadStyle?>">Process Code</td>
		<td class="listing-head" style="<?=$rowHeadStyle?>">Grades</td>
		<td class="listing-head" style="<?=$rowHeadStyle?>">Total</td>
        </tr>
      <?php
		$numRows = 25; // Setting No.of rows 25
		$j = 0;		
		$totalPage = ceil($stkReportRecSize/$numRows);

		$prevFishId = "";
		$prevSelFishId = "";
		$i = 0;
		$pcCol = 0;
		$pcTotCol = 0;
		$pcCol = 0;
		$grandTotalNumMc = 0;
		foreach ($stkRGroupList as $sgl) {
				
				$freezingStyle	 = $sgl[6];
				$freezingStage	 = $sgl[7];
				$stkGroupId 	= $sgl[8];
				$groupName	= $sgl[9];

				# grade
				$sgGrades = $dailyStockReportObj->stkGrGradeRecs($fromDate, $tillDate, $stkGroupId, $reportType, $packType);

				# Product List
				$productListRecs = $dailyStockReportObj->stkGProductList($fromDate, $tillDate, $selFish, $selCustomerId, $selFishCategoryId, $stkGroupId, $reportType, $packType);
				$productListRecSize = sizeof($productListRecs);

				$sTdWidth = ((100)/$stkGradeMaxCount);
				$tdWidth = ($stkGradeMaxCount*100)*3/4;	

				$pcCol++;
				if ($pcCol % 3 ==0 ) $pcHBgColor = "#f2f2f2";
				else if ($pcCol % 3 ==1 ) $pcHBgColor = "#D9F3FF";
				else $pcHBgColor = "#e3f0f7";			

				//background-color:white
				$disMHead = '<tr bgcolor="White" height="100%" >';
						$disMHead .= '<td class="fieldName" nowrap="nowrap" style="padding-left:5px; padding-right:5px; line-height:normal;text-align:left" colspan="4"><span style="font-size:9px;">'.$groupName.'&nbsp;&nbsp;'.$freezingStyle.'&nbsp;&nbsp;'.$freezingStage.'</span></td>';	
						//$disMHead .= '<td class="fieldName" nowrap="nowrap" style="padding-left:5px; padding-right:5px; line-height:normal;"><span style="font-size:9px;">'.$sgProcessCode.'</span></td>';
						$disMHead .= '
						<td nowrap width="'.$tdWidth.'" height="100%" style="padding:0px;" >
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tdBoarder" height="100%" style="padding:0px;">
						<tr align="center">';
						$c = 0;
						$dPCHead = "";
						$disPC = "";
						//foreach ($sgGrades as $grr) {
						for ($cnt=0; $cnt<$stkGradeMaxCount; $cnt++) {		
							$grr = $sgGrades[$cnt];					
							$c++;
							$disPC	 = $grr[1];
							$disStyle = "";							 
							$dPCHead = ($disPC)?$disPC:'&nbsp;';
							//if ($grSize!=$c)
							if ($stkGradeMaxCount!=$c) $disStyle="border-right:1px solid #999999; padding-left:2px; padding-right:2px; ";
							else $disStyle="padding-left:2px; padding-right:2px;";
	
							$disMHead .= '<td class="listing-head" align="center" style="'.$disStyle.' line-height:normal;" width="'.$sTdWidth.'%" nowrap><span style="font-size:10px;">'.$dPCHead.'</span></td>
							';	
						}
						//G='.$c.'
						$disMHead .= '</tr></table></td><td colspan="3">&nbsp;</td></tr>';
						echo $disMHead;

				$prevProductSpec = "";
				foreach ($productListRecs as $plr) {
					$i++;
					$qelId		= $plr[0];
					$qelName	= $plr[1];
					$customerId 	= $plr[2];
					$customName	= $plr[3];
					$qualityId	= $plr[4];
					$qualityCode	= $plr[5];
					$euCodeId	= $plr[6];
					$euCode		= $plr[7];
					$selProcessCodeId = $plr[8];
					$selProcessCode = $plr[9];
					$freezingStageId = $plr[10];
					$freezingStage  = $plr[11];
					$selFrozenCodeId   = $plr[12];
					$selBrandId = $plr[13];
					$selBrandName = $plr[14];
					$selFishId = $plr[15];
					$selFishName = $plr[16];

					$firstColHead =  $customName." ".$euCode;
					//Buyer, eucode, brand, qe, fs, quality		
					$productSpec = $qelName." ".$freezingStage." ".$qualityCode;
					$displayProductSpec = "";
					if (trim($prevProductSpec)!=trim($productSpec)) $displayProductSpec = $productSpec;
	?>
        <tr bgcolor="White">
		<td class="listing-item" nowrap="nowrap" style="<?=$rowHeadStyle?>"><?=$displayProductSpec?></td>
		<td class="listing-item" nowrap="nowrap" style="<?=$rowHeadStyle?>"><?=$firstColHead?></td>			
		<td class="listing-item" nowrap="nowrap" style="<?=$rowHeadStyle?>"><?=$selBrandName?></td>
		<td class="listing-item" nowrap="nowrap" style="<?=$rowHeadStyle?>" align="center"><?=$selProcessCode?></td>
		<td nowrap="nowrap" width="<?=$tdWidth?>" style="line-height:normal; padding:0px;" height="100%">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tdBoarder" height="100%" style="padding:0px;">
				<tr bgcolor="white" align="center">
				<?php					
				$totalNumMC = 0;
				$gr = 0;										
				//foreach ($sgGrades as $rgr) {
				for ($cnt=0; $cnt<$stkGradeMaxCount; $cnt++) {
					$rgr = $sgGrades[$cnt];
					$gr++;
					$gradeId	= $rgr[0];
					$gCode		= $rgr[1]; 
					$disStyle = "";
					//if ($grSize!=$gr)
					if ($stkGradeMaxCount!=$gr) $disStyle="border-right:1px solid #999999; padding-left:2px; padding-right:2px;";
					else $disStyle="padding-left:2px; padding-right:2px;";

					# NUM MC
					//echo "<br>$processCode, $frozenCode<br>";	
					$numMC	= $dailyStockReportObj->getNumMC($fromDate, $tillDate, $selProcessCodeId, $selFrozenCodeId, $gradeId, $customerId, $euCodeId, $selBrandId, $freezingStageId, $qualityId, $reportType, $packType);
					$totalNumMC += $numMC;
				?>
					<td class="listing-item" style="<?=$disStyle?> line-height:normal;" width="<?=$sTdWidth?>%"><span style="font-size:10px;"><?=($numMC!=0)?$numMC:"&nbsp;";?></span></td>	
				<?php
					} // Grade Ends here
					$grandTotalNumMc += $totalNumMC;
				?>
				</tr>
			</table>
		</td>			
		<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><strong><?=($totalNumMC!=0)?$totalNumMC:"";?></strong></td>
	</tr>	
	  	<?php
		//echo "$i%$numRows==".$i%$numRows."&& $stkReportRecSize!=$numRows||||";
		if ($i%$numRows==0 && ($stkReportRecSize+$productListRecSize)!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="99%" cellpadding="0" cellspacing="0">
        <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;" align="right">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="10"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    	</table></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
			<td height="10">&nbsp;</td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="2"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			<?=strtoupper($displayHead)?> - Cont.</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		</td>		 
		 </tr>
	</table></td>
	    </tr>
	
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		</td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>	  
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr bgcolor="White">
<td colspan="17" align="center" style="padding-left:5px;padding-right:5px;">
	<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<tr bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="<?=$rowHeadStyle?>">Product Specification</td>
		<td class="listing-head" style="<?=$rowHeadStyle?>">Buyer,&nbsp;EU Code</td>				
		<td class="listing-head" style="<?=$rowHeadStyle?>">Brand</td>
		<td class="listing-head" style="<?=$rowHeadStyle?>">Process Code</td>
		<td class="listing-head" style="<?=$rowHeadStyle?>">Grades</td>
		<td class="listing-head" style="<?=$rowHeadStyle?>">Total</td>
        </tr>
   <?php
	#Main Loop ending section 
			
	       }
		$prevProductSpec = $productSpec;
	}
?>	
	<?php
		$prevFishId = $fishId;
		$prevSelFishId = $fishId;
	} // Main Loop Ends here
   	?>	
	<tr bgcolor="White">
		<TD colspan="5" class="listing-head" style="padding-left:5px; padding-right:5px;" align="right">Grand Total:</TD>
		<td class="listing-item" nowrap align="right" style="<?=$rowHeadStyle?>"><strong><?=($grandTotalNumMc!=0)?number_format($grandTotalNumMc):"";?></strong></td>
	</tr>
    </table>
   </td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="99%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" align="right">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="<?=$rowHeadStyle?>" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
<?
	}
	# Sales Order Report Ends Here
?>
</form>	
</div>
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>
