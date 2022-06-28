<?php
	require("include/include.php");
	# MC Packing Conversion type, AC - Auto convert/ MC - Manually Convert
	$LSToMCConversionType = $manageconfirmObj->getLS2MCConversionType();

	$err		=	"";
	$saveChanged	=	false;

	$allocateMode	=	$g["allocateMode"];

	if ($g["entryId"]!="") {
		$entryId		=	$g["entryId"];
	} else {
		$entryId		=	$p["entryId"];
	}

	$fishId			=	$g["fishId"];

	if ($g["process"]!="") {
		$processcodeId	=	$g["process"];
	} else {
		$processcodeId	=	$p["process"];
	}

	if ($g["mCPacking"]!="") {
		$hidMcPkg = $g["mCPacking"];
	} else {
		$hidMcPkg = 	$p["hidMcPkg"];
	}
	
	#For getting the Number of Packs
	if ($hidMcPkg) {
		$mcpackingRec		=	$mcpackingObj->find($hidMcPkg);
		$numPacks		=	$mcpackingRec[2];
	}
 
	if ($p["cmdSave"]) {
		$rowCount	=	$p["hidRowCount"];
		$numMC = "";
		for ($i=1; $i<=$rowCount; $i++) {
			
			$gradeId	= $p["gradeId_".$i];

			$numMC		= ($p["numMC_".$i]=="")?0:$p["numMC_".$i];
			$numLooseSlab	= ($p["numLooseSlab_".$i]=="")?0:$p["numLooseSlab_".$i];

			/*If against a grade, ZERO MC quantity is entered and loose slabs are entered, then while saving the System should calculate No of Master Cartons from the loose Slabs based on selection of MC Packing.
			If against a grade, MC quantity is entered and loose slabs are also entered, then while saving the System should calculate No of Master Cartons from the loose Slabs based on selection of MC Packing, and add to the MC quantity already entered.
			????In both cases if any balance loose slabs are available, they should be considered as loose slabs and saved separately.
			*/

			/* if($numMC==0 && $numLooseSlab!=0 && $hidMcPkg!=0) {
				$numMC = floor($numLooseSlab/$numPacks);
			} else if($numMC!=0 && $numLooseSlab!=0 && $hidMcPkg!=0) {
				$totalMcPacks = floor($numLooseSlab/$numPacks);
				$numMC	+= $totalMcPacks;
			} else {
				$numMC		=	$p["numMC_".$i];
			}*/

				if (($numMC==0 || $numMC!=0) && $numLooseSlab!=0 && $hidMcPkg!=0 && $LSToMCConversionType=="AC") {
					$totalMcPacks = floor($numLooseSlab/$numPacks);
					$numMC	+= $totalMcPacks;
					$numLSlab = $numLooseSlab%$numPacks;
				} else {
					$numMC	=	($p["numMC_".$i]=="")?0:$p["numMC_".$i];
					$numLSlab = 	($p["numLooseSlab_".$i]=="")?0:$p["numLooseSlab_".$i];
				}
			
			$entryId	=	$p["entryId"];
			$gradeEntryId	=	$p["gradeEntryId_".$i];
			//echo "$gradeId=$numMC=$numLSlab=$gradeEntryId";
						
			if ($gradeId!="" && trim($numMC)!="" && trim($numLSlab)!="" && $gradeEntryId=="") {
					if (($gradeId>0) && ($numMC>0 )) {
				$gradeRecIns = $dailyfrozenpackingObj->addFrozenPackingGrade($entryId, $gradeId, $numMC, $numLSlab, $LSToMCConversionType);
					}
			} else if ($gradeId!="" && trim($numMC)!="" && trim($numLSlab)!="" && $gradeEntryId!="") {	
				if (($gradeId>0) && ($numMC>0 )) {
				$gradeRecIns = $dailyfrozenpackingObj->updateFrozenPackingGrade($gradeEntryId, $gradeId, $numMC, $numLSlab, $LSToMCConversionType);
				}
			}
		}
		if ($gradeRecIns) {
			$sessObj->createSession("displayMsg", $msg_succInsFrozenPackingGrade);
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
				$gradeEntryRecDel		=	$dailyfrozenpackingObj->deletePackingGradeRec($gradeEntryId);	
			}
		}
	}	

	#List All Records	
	$gradeMasterRecords = $dailyfrozenpackingObj->fetchFrozenGradeRecords($processcodeId, $entryId);
	$gradeMasterRecordsSize	= sizeof($gradeMasterRecords);
	//$gradeMastrds		=	$processcodeObj->fetchFrozenGradeRecords($processcodeId);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libjs/generalFunctions.js"></script>
<script language="JavaScript" type="text/JavaScript" src="libjs/dailyfrozenpacking.js"></script>

<form name="frmDailyFrozenPackingGrade"  id="frmDailyFrozenPackingGrade" action="DailyFrozenPackingGrade.php" method="post">
<table cellpadding="0" cellspacing="0" width="100%">
	 <? if($err!="" ) {?>
      <tr class="listing-head">
        <td colspan="5" align="center" class="err1"><?=$err;?></td>
      </tr>
	  <? }?>
	<tr>
	    <td align="center"><?if(!$allocateMode){?><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$gradeMasterRecordsSize;?>);">&nbsp;&nbsp;<input type="submit" name="cmdSave" class="button" value=" Save " onClick="return validateFrozenpackingGrade(document.frmDailyFrozenPackingGrade); this.form.submit();"/><? }?></td>
    	</tr>
	<tr><TD height="5"></TD></tr>
	<tr>
	<TD align="center">
	<table width="200" border="0" cellpadding="1" cellspacing="1" align="center" bgcolor="#999999">
	  <?
		if (sizeof($gradeMasterRecords)) {
			$i	=	0;
	?>
      <tr bgcolor="#f2f2f2" class="listing-head" align="center">
	<? if(!$allocateMode){?>
        <td nowrap>&nbsp;</td>
	<? }?>
        <td nowrap style="padding-left:2px;padding-right:2px;">Grade</td>
        <td nowrap style="padding-left:2px;padding-right:2px;">No.of <br>MC </td>
        <td nowrap style="padding-left:2px;padding-right:2px;">No of <br>Loose Slabs</td>
      </tr>
	  <?php
		$totMcPack	= 0;
		$totLooseSlab 	= 0;
		foreach ($gradeMasterRecords as $gl) {
			$i++;
			$id		=	$gl[0];
			$displayGrade	=	$gl[1];
			$dailyFrozenGradeEntryId = $gl[2];
			$numMC		=	$gl[4];	
			$numLooseSlab	= 	$gl[5];	
			$readOnly 	= "";
			if ($allocateMode) {					
				$readOnly = "readOnly";
			}
			$totMcPack	+= $numMC;
			$totLooseSlab	+= $numLooseSlab;
  	  ?>
      <tr bgcolor="WHITE">
	<? if(!$allocateMode) { ?>
        <td class="listing-item" nowrap align="center" style="padding-left:2px;padding-right:2px;">
		<input type="checkbox" name="delId_<?=$i?>" id="delId_<?=$i?>" value="<?=$dailyFrozenGradeEntryId;?>" class="chkBox">
		<input type="hidden" name="gradeEntryId_<?=$i?>" value="<?=$dailyFrozenGradeEntryId?>">
	</td>
        <? }?>
	<td class="listing-item" nowrap style="padding-left:2px;padding-right:2px;">
		<input type="hidden" name="gradeId_<?=$i;?>" value="<?=$id?>"><?=$displayGrade?>
	</td>
        <td class="listing-item" nowrap align="right" style="padding-left:2px;padding-right:2px;">
		<input name="numMC_<?=$i;?>" type="text" id="numMC_<?=$i;?>" size="4" value="<?=$numMC?>" style="text-align:right" onkeydown="return focusNextGradeEntry(event,'document.frmDailyFrozenPackingGrade','numMC_<?=$i?>');" <?=$readOnly?>>
		<input name="hidNumMC_<?=$i;?>" type="hidden" id="hidNumMC_<?=$i;?>" size="4" value="<?=$numMC?>">
	</td>
        <td class="listing-item" nowrap align="right" style="padding-left:2px;padding-right:2px;">
		<input name="numLooseSlab_<?=$i;?>" type="text" id="numLooseSlab_<?=$i;?>" size="4" value="<?=$numLooseSlab?>" style="text-align:right" onkeydown="return focusNextGradeEntry(event,'document.frmDailyFrozenPackingGrade','numLooseSlab_<?=$i?>');" <?=$readOnly?>>
		<input name="hidNumLooseSlab_<?=$i;?>" type="hidden" id="hidNumLooseSlab_<?=$i;?>" size="4" value="<?=$numLooseSlab?>">
	</td>
      </tr>
	<? 
		}
	?>
	<?php 
		if (!$allocateMode) $colSpan = 2;
		else	$colSpan = 1;
	?>
	<tr bgcolor="White">
		<TD class="listing-head" colspan="<?=$colSpan?>" align="right" style="padding-left:2px;padding-right:2px;">Total:</TD>
		<td class="listing-item" align="right" style="padding-left:2px;padding-right:2px;"><strong><?=$totMcPack?></strong></td>
		<td class="listing-item" align="right" style="padding-left:2px;padding-right:2px;"><strong><?=$totLooseSlab?></strong></td>
	</tr>
	
	  <input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i;?>" >
	  <? } ?>
	</TD></tr></table>
	  <tr>
	     <td align="center" height="5"></td>
      </tr>
	  <tr>
	    <td align="center"><?if(!$allocateMode){?><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$gradeMasterRecordsSize;?>);">&nbsp;&nbsp;<input type="submit" name="cmdSave" class="button" value=" Save " onClick="return validateFrozenpackingGrade(document.frmDailyFrozenPackingGrade); this.form.submit();"/><? }?></td>
    </tr>
	<tr>
	  <td></td>
	  <td>
	<input type="hidden" name="entryId" value="<?=$entryId;?>">
	<input type="hidden" name="fishId" value="<?=$fishId;?>">
	<input type="hidden" name="process" value="<?=$processcodeId;?>">
	<input type="hidden" name="hidMcPkg" id="hidMcPkg" value="<?=$hidMcPkg?>">
	</td></tr>
  </TD>
  <?
		$displayStatus	=	"";
		$nextPage		=	"";
		$displayStatus	=	$sessObj->getValue("displayMsg");
		$nextPage		=	$sessObj->getValue("nextPage");
		if( $displayStatus!="" ) 
		{
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
