<?php
	require("include/include.php");

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);;
	
	#List all Records
	$claimRecords = $claimObj->fetchAllRecords($fromDate, $tillDate);
	$claimRecordSize = sizeof($claimRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Claim</td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							
							<?
								if($errDel!="")
								{
							?>
							<tr>
								<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
							</tr>
							<?
								}
							?>
							<tr>
								<td width="1" ></td>
								<td colspan="2" >
<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($claimRecords)>0) {
		$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Number</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Sales Order Number</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Last Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>
	</tr>
	<?
	foreach ($claimRecords as $cor) {
		$i++;
		$i++;
		$claimOrderId	= $cor[0];
		$claimNumber	= $cor[1];

		// Find the Total Amount of Each Sales Order
		//$salesOrderTotalAmt = $claimObj->getClaimAmount($claimOrderId);

		$distributorName = $cor[5];
		
		//$salesOrderNo 	= $cor[7];		

		/********************************************************/		
		$selStatusId	= 	$cor[10];

		$currentDate	=	 date("Y-m-d");
		$cDate		=	explode("-",$currentDate);
		$d2 = mktime(22,0,0,$cDate[1],$cDate[2],$cDate[0]);

		$selLastDate	= 	$cor[4]; 	
		$eDate		=	explode("-", $selLastDate);
		$lastDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
		$d1=mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);

		$dateDiff = floor(($d2-$d1)/86400);
		$status = "";
		$statusFlag	=	"";
		$extended	=	$cor[6];
		if ($extended=='E' && ($selStatusId=="" || $selStatusId==0)) {
			$status	=	"<span class='err1'>Extended & Pending </span>";
			$statusFlag =	'E';
		} else {
			if ($statusObj->findStatus($selStatusId)) {
				$status	=	$statusObj->findStatus($selStatusId);
			} else if ($dateDiff>0) {
				$status 	= "<span class='err1'>Delayed</span>";
				$statusFlag =	'D';
			} else {
				$status = "Pending";
			}
		}		
		$currentLogStatus	=	$cor[8];
		$currentLogDate		=	$cor[9];
		$dispatchLastDate	=	$cor[4];
		if ((($statusFlag=='E') || ($statusFlag=='D')) && strlen($currentLogStatus)<=1 ) {
			if ($currentLogStatus=='D' && $statusFlag=='E') {
				$statusFlag = $currentLogStatus.",".$statusFlag;
				$dispatchLastDate = $currentLogDate.",".$dispatchLastDate;	
			}
		
		}
		/*******************************************************/
		# Get Sales Order numbers
		$getSORecords = $claimObj->getClaimSORecords($claimOrderId);
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$claimNumber;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$distributorName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left">
			<table>
			<tr>
				<?
					$numColumn	=	3;
					if (sizeof($getSORecords)>0) {
						$nextRec	=	0;
						$k=0;
						foreach($getSORecords as $soR) {
							$j++;
							$soNumber=	$soR[2];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$soNumber?>
				</td>
				<? 
					if($nextRec%$numColumn == 0) {
				?>
			</tr>
			<tr>
				<? 
					}	
				}
				}
				?>
			</tr>
			</table>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$lastDate;?></td>
		<td class="listing-item" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$status?></td>		
	</tr>
	<?
		}
	?>
	<?
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
						
						</table>
					</td>
				</tr>
			</table>
			<!-- Form fields end   -->
		</td>
	</tr>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
