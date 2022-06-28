<?php
require("include/include.php");
$err			=	"";
$saveChanged		=	false;
	
	if ($g["mainId"]!="") $mainId = $g["mainId"];
	else $mainId = $p["mainId"];
	
	$fishId = $g["fishId"];
	
	if ($g["process"]!="") $processcodeId = $g["process"];
	else $processcodeId = $p["process"];


	# Save
	if ($p["cmdSave"]) {
	
		$rowCount	=	$p["hidRowCount"];
			
		for ($i=1; $i<=$rowCount; $i++) {
			$gradeId	=	$p["gradeId_".$i];
	
			$numMCThawing		=	$p["numMCThawing_".$i];
			$numLooseSlabThawing	=	$p["numLooseSlabThawing_".$i];
	
			$totalNumMC		=	($p["totalNumMC_".$i]=="")?0:$p["totalNumMC_".$i];
			$totalNumLooseSlab	=	($p["totalNumLooseSlab_".$i]=="")?0:$p["totalNumLooseSlab_".$i];
				
			$mainId		=	$p["mainId"];
				
			$gradeEntryId	=	$p["gradeEntryId_".$i];
									
			if ($gradeId!="" && $numMCThawing!="" && $numLooseSlabThawing!="" && $gradeEntryId=="") {
				$gradeRecIns	=	$dailythawingObj->addFrozenPackingThawingGrade($mainId, $gradeId, $numMCThawing, $numLooseSlabThawing, $totalNumMC, $totalNumLooseSlab);
			} else if ($gradeId!="" && $numMCThawing!="" && $numLooseSlabThawing!="" && $gradeEntryId!="") {
				$gradeRecIns	=	$dailythawingObj->updateFrozenPackingThawingGrade($gradeEntryId, $gradeId, $numMCThawing, $numLooseSlabThawing, $totalNumMC, $totalNumLooseSlab);
			}
	
		}
		if ($gradeRecIns) {
			$sessObj->createSession("displayMsg",$msg_succInsThawingGrade);
			$saveChanged=true;
			//$sessObj->createSession("nextPage",$url_afterDelProcessor);
		} else {
			$err	=	$msg_failInsFrozenPackingGrade;
		}
		$gradeRecIns	=	false;
	}

	#Delete List
	if ($p["cmdDelete"]!="") {
	
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$gradeEntryId	=	$p["delId_".$i];
			if ($gradeEntryId!="") {
				$gradeEntryRecDel = $dailythawingObj->deletePackingGradeRec($gradeEntryId);
			}
		}
	}	

#List All Records	
	$gradeMasterRecords		=	$dailythawingObj->fetchFrozenGradeRecords($processcodeId, $mainId);
	$gradeMasterRecordsSize		=	sizeof($gradeMasterRecords);	
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script type="text/javascript" src="libjs/generalFunctions.js"></script><script language="JavaScript" type="text/JavaScript" src="libjs/dailythawing.js"></script>

<form name="frmDailyThawingGrade"  id="frmDailyThawingGrade" action="DailyThawingGrade.php" method="post">
<table width="100%" border="0" cellpadding="1" cellspacing="1">
				
      <? if($err!="" ) {?>
      <tr class="listing-head">
        <td colspan="7" align="center" class="err1"><?=$err;?></td>
      </tr>
	  <? }?>
	<?
	if (sizeof($gradeMasterRecords)) {
		$i	=	0;
	?>
      <tr bgcolor="#f2f2f2" class="listing-head" align="center">
        <td nowrap>&nbsp;</td>
        <td nowrap style="padding-left:2px; padding-right:2px;">Grade</td>
        <td style="padding-left:2px; padding-right:2px;">No.of Mc<br> in Stock </td>
        <td style="padding-left:2px; padding-right:2px;">No.of Loose <br>Slabs in Stock </td>
        <td style="padding-left:2px; padding-right:2px;">No of MCs <br>used for Thawing </td>
        <td style="padding-left:2px; padding-right:2px;">No of Loose Packs <br />used for Thawing</td>
      </tr>
	<?php
	foreach ($gradeMasterRecords as $gl) {
		$i++;
		$id			=	$gl[0];
		$displayGrade		=	$gl[1];
		$dailyFrozenGradeEntryId = 	$gl[2];
		$numMCThawing		=	$gl[4];	
		$numLooseSlabThawing	= 	$gl[5];	

		//Find the total number of stock items(MC and Loos Slab)					
		list($totalNumMC, $totalNumLooseSlab) = $dailythawingObj->getTotalStock($processcodeId, $id);		
	?>
      <tr>
        <td class="listing-item" nowrap align="center"><input type="checkbox" name="delId_<?=$i?>" id="delId_<?=$i?>" value="<?=$dailyFrozenGradeEntryId;?>"><input type="hidden" name="gradeEntryId_<?=$i?>" value="<?=$dailyFrozenGradeEntryId?>"></td>
        <td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;"><input type="hidden" name="gradeId_<?=$i;?>" value="<?=$id?>"><?=$displayGrade?></td>
        <td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;" align="center"><?=$totalNumMC?><input name="totalNumMC_<?=$i;?>" type="hidden" id="totalNumMC_<?=$i;?>" size="4" value="<?=$totalNumMC?>" style="text-align:right"></td>
        <td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;" align="center"><?=$totalNumLooseSlab?><input name="totalNumLooseSlab_<?=$i;?>" type="hidden" id="totalNumLooseSlab_<?=$i;?>" size="4" value="<?=$totalNumLooseSlab?>" style="text-align:right"></td>
        <td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;" align="center"><input name="numMCThawing_<?=$i;?>" type="text" id="numMCThawing_<?=$i;?>" size="4" value="<?=$numMCThawing?>" style="text-align:right"></td>
        <td class="listing-item" nowrap style="padding-left:2px; padding-right:2px;" align="center"><input name="numLooseSlabThawing_<?=$i;?>" type="text" id="numLooseSlabThawing_<?=$i;?>" size="4" value="<?=$numLooseSlabThawing?>" style="text-align:right"></td>
      </tr>
	  <? }?>
	  <input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i;?>">	  
	  <? } ?>
	  <tr>
	     <td colspan="6" align="center" height="5"></td>
      	 </tr>
	  <tr>
	    <td colspan="6" align="center"><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$gradeMasterRecordsSize;?>);">&nbsp;&nbsp;<input type="submit" name="cmdSave" class="button" value=" Save " onClick="return validateFrozenPackingThawingGrade(document.frmDailyThawingGrade); this.form.submit();"/></td>
    </tr>
	<tr>
	  <td></td>
	  <td>
	<input type="hidden" name="mainId" value="<?=$mainId;?>">
	<input type="hidden" name="fishId" value="<?=$fishId;?>">
	<input type="hidden" name="process" value="<?=$processcodeId;?>"></td></tr>
  </table>
  	<?
		$displayStatus	=	"";
		$nextPage	=	"";
		$displayStatus	=	$sessObj->getValue("displayMsg");
		$nextPage	=	$sessObj->getValue("nextPage");
		if ($displayStatus!="")	{
			$sessObj->putValue("displayMsg","");
			$sessObj->putValue("nextPage","");
	?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		alert("<?=$displayStatus;?>");
		//window.location="<?=$nextPage;?>";
		//-->
		</SCRIPT>
	<?
		}
	?>		
</form>