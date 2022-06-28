<?php	
	require("include/include.php");
	
	
	# Get PO Id
	$selPOId = $g["selPOId"];
	$entryID = $g["entryID"];
	$edit = $g["edit"];
	$delete = $g["delete"];
	
	// ----------------------------------------------------------
	# Find PO Records
	if ($selPOId) $poItemRecs = $orderprocessingObj->getProductsInPO($selPOId,$entryID);
	
	#  Number of Copy
	$numCopy	= 1; //3	
	
	



	if($p['cmdEdit'])
	{
		//echo "hii";
		//echo $editID=$p['editId'];
		$editID=$p['editId'];
		$mainId=$p['mainId'];
		$rmLotID=$p['rmLotID'];

			$maxdate=$frozenStockAllocationObj->getMaxDate();
			$maximumdt= dateFormat($maxdate[0]);
			$dateFrom=$maximumdt;
			$fromDate = mysqlDateFormat($dateFrom);
			if ($maximumdt=="")
				{
					$defaultDFPDate			=	dateformat($displayrecordObj->getDefaultDFPDate());
					$fromDate=mysqlDateFormat(date("$defaultDFPDate"));
					$dateFrom=dateformat($displayrecordObj->getDefaultDFPDate());
				}
					//echo "******".$fromDate;
			$dateTill = date("d/m/Y");
			$tillDate = mysqlDateFormat($dateTill);

		if($rmLotID=='0' || $rmLotID=='')
		{
			//echo $editID.','.$mainId;

			$frozendetail=$frozenStockAllocationObj->getfrozenDetailOfAllocation($editID,$mainId);
			$updateID=$frozendetail[0];
			$MC=$frozendetail[1];
			$gradeId=$frozendetail[3];
			$gradeName=$frozendetail[4];
			$processid=$frozendetail[5];
			$freezingStage=$frozendetail[6];
			$frozenCodeId=$frozendetail[7];
			$mcpackingId=$frozendetail[8];
			list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacks($processid, $freezingStage, $frozenCodeId, $gradeId, $mcpackingId,$fromDate,$tillDate);
			
			//echo $frozn;
		}
		else
		{	
			$frozendetail=$frozenStockAllocationObj->getfrozenDetailOfAllocationRMLotID($editID,$mainId,$rmLotID);
			$updateID=$frozendetail[0];
			$MC=$frozendetail[1];
			$gradeId=$frozendetail[3];
			$gradeName=$frozendetail[4];
			$processid=$frozendetail[5];
			$freezingStage=$frozendetail[6];
			$frozenCodeId=$frozendetail[7];
			$mcpackingId=$frozendetail[8];
			list($availableMC, $availableLS) = $frozenStockAllocationObj->getAvailablePacksRMLot($processid, $freezingStage, $frozenCodeId, $gradeId, $mcpackingId,$fromDate,$tillDate,$rmLotID);

		}

	}
	
	if($p['cmdSaveChange'])
	{
		//echo "hii";
		$lotidStatus=$p['lotidStatus'];
		$updateID=$p['updateID'];
		$numMc=$p['numMc'];
		if($lotidStatus=="")
		{
			$frozenAllocate=$frozenStockAllocationObj->updateDailyFrozenAllocationGrade($updateID,$numMc);
		}
		else
		{
			$frozenAllocate=$frozenStockAllocationObj->updateDailyFrozenAllocationGradeRMLotID($updateID,$numMc);
		}
		
		if($frozenAllocate)
		{
			$sessObj->createSession("displayMsg",$msg_succUpdateFrozenAllocation);
			//$sessObj->createSession("nextPage",$url_afterUpdateOrderProcessing);
			header('Location: ViewOP.php?selPOId='.$selPOId.'&entryID='.$entryID.'&edit='.$edit.'&delete='.$delete.'');
			
		}
		else
		{
			$editMode	=	true;
			$err		=	$msg_failFrozenAllocationUpdate;
		}


	}



	if($p['cmdDelete'])
	{
		//echo "hii";
		$deleteID=$p['editId'];
		$mainId=$p['mainId'];
		$rmLotID=$p['rmLotID'];
		if($rmLotID!='0' || $rmLotID=='')
		{
			$frozenAllocationMain=$frozenStockAllocationObj->getfrozenAllocationDetail($mainId);
			if($frozenAllocationMain>1)
			{
				###delete the grade only
				$deletefrozengrade=$frozenStockAllocationObj->deleteFrozenAllocationGrade($deleteID);
			}
			elseif($frozenAllocationMain=='1')
			{
				###delete the allocation
				$deleteFrozenGrade=$frozenStockAllocationObj->deleteFrozenAllocationGrade($deleteID);
				$deleteFrozenAllocate=$frozenStockAllocationObj->deleteFrozenAllocation($mainId);
				$deleteFrozen=$frozenStockAllocationObj->deleteFrozenAllocationEntry($mainId);
			}
		}
		else
		{
			$frozenAllocationMain=$frozenStockAllocationObj->getfrozenAllocationDetailRMLotID($mainId);
			if($frozenAllocationMain>1)
			{
				###delete the grade only
				$deletefrozengrade=$frozenStockAllocationObj->deleteFrozenAllocationGradeRMLotID($deleteID);
			}
			elseif($frozenAllocationMain=='1')
			{
				###delete the allocation
				$deleteFrozenGrade=$frozenStockAllocationObj->deleteFrozenAllocationGradeRMLotID($deleteID);
				$deleteFrozenAllocate=$frozenStockAllocationObj->deleteFrozenAllocationRMLotID($mainId);
				$deleteFrozen=$frozenStockAllocationObj->deleteFrozenAllocationEntryRMLotID($mainId);
			}
		}
		//echo $deleteID.','.$mainId.','.$rmLotID,','.$frozenAllocationMain;
	}
$exportAddrArr=$purchaseorderObj->getAllCompany();
	$exportAddrContact=$purchaseorderObj->getAllCompanyContact($exportAddrArr[0]);
?>

<html>
<head>
<title>Order Processing and Shipment</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script>
function assignValue(form,val,prefix)
{
	//showFnLoading();
 	if (val!="") {
		
		eval("form."+prefix+".value ="+"'"+val+"'");
		//alert("hii");
		eval( "form."+prefix+".value =	"+val);
	}
}

function validateAvailableMC()
{
		//alert("hii");
	var numMc=document.getElementById('numMc').value;
	var maxMC=document.getElementById('maxMC').value;
		//alert(numMc+'---'+maxMC);
	if(numMc=='' || numMc=='0')
	{
		alert("Cannot save data with out MC");
		return false;
	}
	if((maxMC=='') || (maxMC<='0') )
	{
		alert("Cannot save data MC exceeds the limit");
		return false;
	}
	if(parseInt(numMc)>parseInt(maxMC))
	{
		alert("Cannot save data MC exceeds the limit");
		return false;
	}
		return true;	
}

</script>


</head>

<form name="frmViewOP" id="frmViewOP" action="ViewOP.php?selPOId=<?=$selPOId?>&entryID=<?=$entryID?>&edit=<?=$edit?>&delete=<?=$delete?>" method="post">
	<table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center' border="0">
	<tr>
	<td>	
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	
		<tr bgcolor="White"><TD height="5"></TD></tr>
		<tr bgcolor="White">
			<td>
				<table cellpadding="0" cellspacing="0" width="100%">
					<TR>
						<TD align="left" valign="top"><img src="images/forstarfoods.gif" alt=""></TD>
						<td class="pageName" valign="bottom" align="center">						
							Order Processing and Shipment
						</td>
						<td align="right">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<TD>
										<table cellpadding="0" cellspacing="0">
											<TR>
												<TD class="listing-head" style="line-height:normal;"><font size="2px"><?=$exportAddrArr[1]?></font></TD>
											</TR>										
										</table>
									</TD>
								</tr>
								<tr>
									<TD class="print-SOTHead-item"><?=$exportAddrArr[2]?></TD>
								</tr>
								<tr>
									<TD class="print-SOTHead-item"><?=$exportAddrArr[3]?>, <?=$exportAddrArr[4]?>,<?=$exportAddrArr[5]?></TD>
								</tr>							
								<tr>
								<TD class="print-SOTHead-item"><?php 
								 
								 foreach($exportAddrContact as $expt1)
								 {
									 if($expt1[1]!='') echo $expt1[1].',';
									
								 }
								 ?>
								</TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?php 
								 foreach($exportAddrContact as $expt2)
								 {
									
									 if($expt2[2]!='')  echo $expt2[2].',';
								 }
								 ?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item">
								<?php 
								foreach($exportAddrContact as $expt3)
								 {
									 if($expt3[3]!='') echo  $expt3[3].',';
								 }
								 ?>
								 </td>
								 </tr>
							<tr>
								<TD class="print-SOTHead-item"><?php 
								 foreach($exportAddrContact as $expt4)
								 {
									 if($expt4[4]!='') echo $expt4[4].',';
								 }
								 ?></TD>
							</tr>
							</table>
						</td>
					</TR>
				</table>
			</td>

		</tr>
	</table>
	</td>
	</tr>


	<tr>
			<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
			<table width='99%' >
				 <tr>
				   <td class="listing-head" nowrap="nowrap" align='left' colspan='2' height="5"></td>
			 </tr>
			</table>
			</td>
		  </tr>


	  <?php if($editID!='') { ?>
		<tr >
			<td align="LEFT" valign="top" width='100%'  align='center'>
				<table cellspacing='0' cellpadding='0' width="100%"  bgcolor="#FFFFFF">
					<tr align='center'>
						<td  height="10">&nbsp;</td>
					</tr>
					<tr>
					<td>
					<table align="center" cellspacing='0' cellpadding='0' bgcolor="#FFFFFF" width="45%" style="border:#f2f2f2 ridge 2px;">
					<tr align='center'>
						<td  height='5' >&nbsp;</td>
					</tr>
					<tr align='center'>
						<td  class="listing-head">Edit Frozen Stock Allocation For a Grade</td>
					</tr>
					<tr align='center'>
						<td  height="5" >&nbsp;</td>
					</tr>
					<tr>
						<td   >
						<table width="200" border="1" cellpadding="1" cellspacing="0" align="center" id="prodnAllocateTble" bgcolor="#f2f2f2">
							
							<tr   align="center"  >
							<td colspan='2'>&nbsp;<input type='hidden' name='lotidStatus' id='lotidStatus' value="<?=$rmLotID?>"/>
							<input type='hidden' name='updateID' id='updateID' value="<?=$updateID?>"/></td>
							
							<td class="listing-head"><?=$gradeName?>
							
							</td>
							</tr>
							<tr   align="center"  >
							<td class="listing-head">Available Mc</td>
							<td class="listing-item">MC</td>
							<td class="listing-item"><?=$availableMC?><input type='hidden' name='maxMC' id='maxMC' value="<?=$availableMC+$MC?>" size='4'/></td>
							</tr>
							<tr   align="center"  >
							<td colspan='2'  bgcolor="#FFFFFF" class="listing-head">MC</td>
							<td  bgcolor="#FFFFFF"><input type='text' name="numMc" id="numMc" size='5' value="<?=$MC?>"></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr align='center'>
						<td  >&nbsp;</td>
					</tr>
					<tr align='center'>
						<td ><input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAvailableMC(document.frmViewOP);"></td>
					</tr>
					<tr align='center'>
						<td >&nbsp;</td>
					</tr>
					</table>
					</td>
					</tr>
					<tr><td height='5' bgcolor="#FFFFFF">&nbsp;</td></tr>
				</table>
			</td>
		</tr>
		
		<?php
	}
	?>
  <tr><td height='5' bgcolor="#ffffff">&nbsp;</td></tr>
  <tr>
	  <td>
	  <?php
		if (sizeof($poItemRecs)) {
		?>
			<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
				<tr bgcolor="#f2f2f2" align="center">
				<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Sr.<br>No</th> 
				<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true">RM Lot ID</th> 
				<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Fish</th>
				<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Process Code</th>		
				<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Brand</th>
				<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Grade</th>
				<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Freezing Stage</th>
				<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Frozen Code</th>	
				<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">MC Pkg</th>
				<!-- <th class="p-listing-head" style="padding-left:3px; padding-right:3px;">No of MC</th>-->
				 <th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Allocated MC</th>
				 <? 
				 if($edit==true){?>
					<th class="p-listing-head" style="padding-left:3px; padding-right:3px;"></th>
				<? }?>
				 <? 
				 if($delete==true){?>
					<th class="p-listing-head" style="padding-left:3px; padding-right:3px;"></th>
				<? }?>
				</tr>
				
				 <?php
		
		$i = 0;
		$totNumAllocated=0;
		//$totNumMC	= 0;
		//$totValInUSD	= 0;
		//$totValInINR	= 0;
		foreach ($poItemRecs as $poi) {
			$i++;
			$poEntryId 	= $poi[0];
			$selFish 	= $poi[1];
			$selProcessCode = $poi[2];
			$selEuCode	= $poi[3];
			$selBrand	= $poi[4];
			$selBrdFrom	= $poi[13];
			//$selBrandId	  = $selBrd."_".$selBrdFrom;
			$selGrade	  = $poi[5];
			$selFreezingStage = $poi[6];
			$selFrozenCode    = $poi[7];
			$selMCPacking	  = $poi[8];
			$numMC		= $poi[9];
			$totNumMC += $numMC;
			$pricePerKg	= $poi[10];
			$valueInUSD	= $poi[11];
			$totValInUSD += $valueInUSD;
			$valueInINR	= $poi[12];
			$totValInINR += $valueInINR;
			$gradeId=$poi[19];
			$allocatedEntryId=$poi[20];
			$allocatedCount=$poi[21];
			//$poi[21];
			$rmlotid=$poi[22];
			($rmlotid!='0') ? $rmlotidNm=$poi[23] : $rmlotidNm='';
			$allocatedMainId=$poi[24];
			$totNumAllocated+=$allocatedCount;
			//list($allocatedCount,$balCount) = $purchaseorderObj->getAllocatedMcno($selPOId,$poEntryId,$gradeId);
		//	$updatedeliveredStatus=$purchaseorderObj->updateDeliveredStatus($balCount,$poEntryId);
	?>	


				 <tr bgcolor="#FFFFFF">
		<td height='20' class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;line-height:normal;" align="center">
			<?=$i?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" nowrap="true">
			<?=$rmlotidNm?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" nowrap="true">
			<?=$selFish?>
		</td>	
		<td class="listing-item" align="left" style="padding-left:3px; padding-right:3px; font-size:8pt;"><?=$selProcessCode?></td>
		<td class="listing-item" align="left" style="padding-left:3px; padding-right:3px; font-size:8pt;"><?=$selBrand?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><?=$selGrade?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" nowrap="true">
			<?=$selFreezingStage?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;">
			<?=$selFrozenCode?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;">
			<?=$selMCPacking?>
		</td>
		<!--<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<?=$numMC?>
		</td>-->
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<?=$allocatedCount?>
		</td>
		<? if($edit==true){?>
			  <td class="listing-item" width="45" align="center">
				<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$allocatedEntryId;?>,'editId'); assignValue(this.form,<?=$allocatedMainId;?>,'mainId'); assignValue(this.form,<?=$rmlotid;?>,'rmLotID');"  <?=$disableEdit?>>
			</td>
		  <? }?>
	<? if($delete==true){?>
			  <td class="listing-item" width="45" align="center">
				<input type="submit" value=" Delete " name="cmdDelete" onClick="assignValue(this.form,<?=$allocatedEntryId;?>,'editId'); assignValue(this.form,<?=$allocatedMainId;?>,'mainId'); assignValue(this.form,<?=$rmlotid;?>,'rmLotID');"  >
			</td>
		  <? }?>
	
      </tr>
		<?php
		}
	  ?>
	<tr bgcolor="#FFFFFF">
	<td>
	  <input type='hidden' name='editId' id='editId' value=''/>
	   <input type='hidden' name='mainId' id='mainId' value=''/>
	    <input type='hidden' name='rmLotID' id='rmLotID' value=''/>
        <td height="20" colspan="8" nowrap="nowrap" class="listing-head" align="right" style="padding-left:3px; padding-right:10px;" >
		Total:
	</td>
   
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$totNumAllocated?></strong></td>
	<td bgcolor="#FFFFFF" colspan='2'>&nbsp;</td>
      </tr>	






			</table>

		<?php
		}
		else
		{
		?>
			<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#ffffff" >
				<tr bgcolor="#fff" align="center">
					<th colspan="13" class="err1" style="padding-left:3px; padding-right:3px;">No Records Found</th>
				</tr>
			</table>

		<?php
		}
		?>
	  </td>
  <tr>












	</table>
	
</form>
</html>
