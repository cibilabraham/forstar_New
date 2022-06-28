	<?php
	require("include/include.php");		
	# Get Company Details
	list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
	$displayAddress		= "";
	$displayTelNo		= "";
	if ($companyName)	$displayAddress = $address."&nbsp;".$place."&nbsp;".$pinCode;
	if ($telNo)		$displayTelNo	= $telNo;
	if ($faxNo)		$displayTelNo	.= "&nbsp;/&nbsp;".$faxNo;	
	# Default Yield Tolerance
	$defaultYieldTolerance  = $displayrecordObj->getDefaultYieldTolerance();
	$dateFrom=$g[stockFrom];
	$dateTo=$g[stockTo];
	$fromDate = mysqlDateFormat($dateFrom);
	$tillDate = mysqlDateFormat($dateTo);
	$gradeRecs = $dailyStockReportObj->getProductionGradeRecs($fromDate, $tillDate);
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	if($g[checkboxSel]){
		$fromDate=date("Y-m-d");
	}
	$dailyFrozenPackingRecs = $dailyStockReportObj->getdailystkPagingDFPRecs($fromDate, $tillDate);		
	$numrows	=  sizeof($dailyStockReportObj->getdailystkPagingDFPRecs($fromDate, $tillDate));
	$colnum=sizeof($dailyStockReportObj->getProductionGradeRecs($fromDate,$tillDate));	
	$stocktype=$g["stocktype"];
	//echo $stocktype;
	$packType=$g["packType"];
	$reportType=$g["reportType"];
	$numRows 	= 15;							
	$i = 0;
	$j = 0;							
	$dailyFrozenPackingReportSize = sizeof($dailyFrozenPackingRecs);		
	$totalPage = ceil($dailyFrozenPackingReportSize/$numRows);			
?>
<html>
<head>
<title>DailyStockReport</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function printDoc()
{
	window.print();	
	return false;
}

function printThisPage(printbtn)
{	
	document.getElementById("printButton").style.display="none";	
	if (!printDoc()) {
		document.getElementById("printButton").style.display="block";			
	}		
}
</script>
</head>
<body>
<table width="100%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this)" style="display:block"></td>
</tr>
</table>
<table width="90%" cellpadding="1" cellspacing="1" class="boarder" align="center">
<tr>
<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	<tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head" height="5"></td>
	</tr>
	<tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head" ><font size="3"><?=$companyArr["Name"];?></font></td>
	</tr>  
	<tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item">M53, MIDC, Taloja, New Bombay 410208</td>
	</tr>  
	<tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item">Tel: 022 2741 0807 / 2741 2376</td>
	</tr>  
	<tr bgcolor=white>
    <td colspan="17" align="right" class="listing-item" height="5"></td>
	</tr>  
	<tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
	</tr>
	<tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><font size="2"><b>DAILY STOCK REPORT</b> </font></td>
	</tr>  
	<tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
	</tr>
	<tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-item"><table width="100%">
	<tr>
	<td>
	<table width="100" cellpadding="0" cellspacing="0">
    <tr>
    <td colspan="4" height="5"></td>
    </tr>
    <tr>
	<td class="fieldName" nowrap="nowrap">DAILY STOCK REPORT</td>
	<td class="listing-item" nowrap="nowrap">&nbsp;<b><?=$processorName?></b> </td>
    <td class="listing-item" nowrap="nowrap">&nbsp;</td>
    <td class="listing-item" nowrap="nowrap" style="padding-left:10px;">
	     <table width="200" cellpadding="0" cellspacing="0">
		 <tr> 
         <td class="fieldName">As&nbsp;On:</td>
         <td class="listing-item" nowrap><?=$dateTo?></td>
        </tr>
        </table>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		<tr><td>
		<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
            <tr bgcolor="#f2f2f2" align="center">
			<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" >Process<br/>Code</th>			
            <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Freezing Stage</th>
            <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Frozen<br/>code</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">MC Pkg</th>
			<?php					
					foreach ($gradeRecs as $gR) {
						$gradeId = $gR[1];
						if (($stocktype==2) || ($stocktype==3))
						 {
		  ?>
							<th class="listing-head" colspan=1 style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$gradeId?></th>	
						<?php } else {?>				
							<th class="listing-head" colspan=1 style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$gradeId?></th>	
						<?php
						}}
						?>	
							<th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="right">Total</th>	
			</tr>
			<tr bgcolor="#f2f2f2"  align="center">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<?php					
			foreach ($gradeRecs as $gR) {	
				if ($reportType=="PRIR")
					{
					
					?>

					<td class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" >Price per KGS</td>
					<?php }
				if ($stocktype==1)
					 {?>
					<th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">FS</th>
				<?php } 
			   if ($stocktype==3) 
					{?>				
				<th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"> AS</th>				
				<?php } if ($stocktype==2){?>
				<th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">TS</th>	
				<?php }?>
				<?php
			}
			?>
			</tr>
			<?php		
			foreach ($dailyFrozenPackingRecs as $dfpr) {
			$i++;
			$dailyFrozenPackingMainId	= $dfpr[0];
			$selProcessCodeId	= $dfpr[3];
			$selProcessCode		= $dfpr[6];	
			$selFreezingStageId	= $dfpr[4];
			$selFreezingStage	= $dfpr[7];
			$selFrozenCodeId	= $dfpr[5];
			$selFrozenCode		= $dfpr[8];
			$reportConfirmed	= $dfpr[9];
			$allocatedCount		= $dfpr[10];
			$selMCPackingId		= $dfpr[11];
			$selMCPkgCode	    = $dfpr[12];
			$rnumMC="";
			$lnumLS="";
			?>	
			<tr align="center" <?=$listRowMouseOverStyle?>>		
				<td  class="listing-item" nowrap width="20" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="left" ><?=$selProcessCode?></td>	
				<td class="listing-item" nowrap width="30" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="left" ><?=$selFreezingStage?></td>
				<td class="listing-item" nowrap width="40" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="left" >
					<?=$selFrozenCode?>
				</td>
			<td class="listing-item" nowrap width="30" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;"><?=$selMCPkgCode?>&nbsp;</td>
				<?php					
				foreach ($gradeRecs as $gR) {
					$gradeId = $gR[0];
					list($gradeEntryId, $numMC, $numLS)=$dailyStockReportObj->getSlab($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate, $tillDate);
					list($agradeEntryId, $anumMC, $anumLS)=$dailyStockReportObj->getallocateSlab($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate, $tillDate);
					list($tnumMC)=$frozenStockAllocationObj->getThaGradeQty($selProcessCodeId,$selFreezingStageId,$selFrozenCodeId,$selMCPackingId,$fromDate,$gradeId);
					//echo "ooooo----$tnumMC<br>";
					list($stkPrice)=$dailyStockReportObj->getPrice($selProcessCodeId, $selFreezingStageId, $selFrozenCodeId,$selMCPackingId,$gradeId,$fromDate, $tillDate);
					$totNumMCs = $numMC-($tnumMC+$anumMC);

					//echo "rrrr---$totNumMCs<br>";
					$totNumLSs = $numLS-($tnumLS+$anumLS);

					if ($reportType=="PRIR")
					{
					
					?>

					<td  class="listing-item" nowrap width="12" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle"><?=($stkPrice!=0)?$stkPrice:"&nbsp;";?></td>
					<?php }
					//echo $stocktype;
				if ($stocktype==2)
				{?>
					<td  class="listing-item" nowrap width="12" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle">
					<?php if ($packType=="MC")
					{?>
				<?=($numMC!=0)?$numMC:"&nbsp;";?>
				<?php 
				$rnumMC=$rnumMC+$numMC;
				} else if ($packType=="LS"){?>
				<?=($numLS!=0)?$numLS:"&nbsp;";?>
				<?php } 
				$lnumLS=$lnumLS+$numLS;
				
				}?></td>
				<?php
				if ($stocktype==3)
				{?>
					<td class="listing-item" nowrap width="12" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle" >
					<?php if ($packType=="MC")
					{?>
				<?=($anumMC!=0)?$anumMC:"&nbsp;";?>
				<?php 
				$rnumMC=$rnumMC+$anumMC;
				} else if ($packType=="LS"){?>
				<?=($anumLS!=0)?$anumLS:"&nbsp;";?>
				<?php } 
				$lnumLS=$lnumLS+$anumLS;
				}?>			
					</td>
				<?php 
			
				if ($stocktype==1){
					//echo "ffff------------$stocktype<br>";
				?>
					<td class="listing-item" nowrap width="12" style="padding-left:5px; padding-right:5px; border-right:1px solid #999999;" align="right" valign="middle" >
					<?php if ($packType=="MC")
					{?>
				<?=($totNumMCs!=0)?$totNumMCs:"&nbsp;";?>				
				
				<?php 
				$rnumMC=$rnumMC+$totNumMCs;
				
				} else if ($packType=="LS"){?>
				<?=($totNumLSs!=0)?$totNumLSs:"&nbsp;";?>	
				<?php 
				$lnumLS=$lnumLS+$totNumLSs;
				
				}?>	
					</td>
				<?php }?>
				<?php }?>	
				
				<td class="listing-item" nowrap width="12" style="padding-left:5px; padding-right:5px; border:1px solid #999999;" align="right" valign="middle">&nbsp;
				<?php if ($packType=="MC")
					{?>
				<?=$rnumMC;?>
				<?php }else if ($packType=="LS"){?> <?=$lnumLS;?>  <?php }
				
				$totRSumMC=$totRSumMC+$rnumMC;
				?></td>
		</tr>
			<?
	  		if($i%$numRows==0 && $preProcessingReportSize!=$numRows) {								
				$j++;
			?>
			</table></td></tr>
			<tr bgcolor="#FFFFFF">
			<td colspan="<?=$colnum+3;?>" class="listing-item" nowrap width="12" style="padding-left:5px; padding-right:5px; border-bottom:1px solid #999999;border-left:1px solid #999999;border-right:1px solid #999999;" align="right" valign="middle">&nbsp;<?=$totRSumMC;?></td></tr>
			
			<td>
			<table width="98%" align="center" cellpadding="2">
			<tr>
			<td colspan="6" height="5"></td>
			</tr>
			<tr>
			<td colspan="6" height="5">&nbsp;</td>
			</tr>
			
			
			<tr>
			<tr valign="top">
			<td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
			<td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
			<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
			<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
			<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
			<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
			</tr>
			<tr valign="top">
			<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;"><? echo date("d/m/Y");?></td>
			</tr>
			 <tr valign="top">
			<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr></table>
			</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	<table width="90%" cellpadding="1" cellspacing="1" class="boarder" align="center">
	<tr><td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	<tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head" height="5"></td>
	 </tr>
	<tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head" ><font size="3"><?=$companyArr["Name"];?></font> </td>
	</tr>
	<tr bgcolor=white>
    <td colspan="17" align="right" class="listing-item" height="5"></td>
	</tr>  
	<tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
	 </tr>
	 <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><font size="2"><b>DAILY STOCK REPORT</b> </font> - Cont.</td>
	</tr>  
	<tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
	</tr>
	<tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-item"><table width="100%">
						<tr>
						<td><table width="100" cellpadding="0" cellspacing="0">
                              <tr>
                                <td colspan="4" height="5"></td>
                                </tr>
                              <tr>
                           <td class="fieldName" nowrap="nowrap">Daily Stock Report</td>
					      <td class="listing-item" nowrap="nowrap">&nbsp;<b><?=$processorName?></b> </td>
                           <td class="listing-item" nowrap="nowrap">&nbsp;</td>
                           <td class="listing-item" nowrap="nowrap" style="padding-left:10px;"><table width="200" cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName">As&nbsp;On:</td>
                <td class="listing-item" nowrap> 
                  <?=$dateTo?>                </td>              
              </tr>
            </table></td>
                              </tr>
                            </table></td>
						</tr>
						<tr><td>
						<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#999999" class="print">
<tr bgcolor="#f2f2f2" align="center">
			<th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Process<br/>Code</th>				
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Freezing Stage</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Frozen<br/>code </th>
					<th class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">MC Pkg</th>

			<?php					
					foreach ($gradeRecs as $gR) {
						$gradeId = $gR[1];
						if (($stocktype==2) || ($stocktype==3))
							 {
				?>
						<th class="listing-head" colspan=1 style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$gradeId?></th>	
				<?php	 } else {	?>				
						<th class="listing-head" colspan=1 style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"><?=$gradeId?></th>	
				<?php
				}	}
				?>	
				<th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">Total</th>	
					</tr><tr bgcolor="#f2f2f2"  align="center">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<?php					
					foreach ($gradeRecs as $gR) {				
					if ($stocktype==2)
					{?>
							<th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">TS</th>
					<?php } 
					if ($stocktype==3)
					{?>
				
							<th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px"> AS</th>
				
					<?php } if ($stocktype==1){?>
							<th  class="listing-head" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px">FS</th>	
					<?php }?>
					<?php
					}
					?>
			</tr>
		
                 <?
					}
				$prevFishId=$fishId;
				$prevRecDate	=	$recDate;
				}
				?> 
				<tr bgcolor="#FFFFFF">
			<td colspan="<?=$colnum+4;?>" style="padding-right:10px;font-size:11px">&nbsp;</td><td style="padding-right:5px;font-size:11px" align="right">&nbsp;<?=$totRSumMC;?></td></tr>
                 </table></td></tr>
				</table></td>
				 </tr>  
				 	
  <tr bgcolor=white> 
	<td colspan="17" align="center" class="fieldName"><table width="98%" cellpadding="2">      
	 <tr>
     <td colspan="6" height="5"></td>
   </tr>      
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="padding-left:5px; padding-right:5px; line-height:normal; font-size:10px; line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
  </tr> 
  </table>
</td>
</tr>
</table>
</body>
</html>


