<?php 
require("include/include.php");
ob_start();

$err	= "";

	if ($p["cmdQuality"]!="") {
		$currentId	= $p["curEntryId"];
		$quality	= $p["entryQuality"];
		$percentage	= $p["qualityPercent"];
	
		if ($currentId!="" && $quality!="" && $percentage!="") {
			$qualityRecIns=$dailycatchentryObj->addQuality($currentId,$quality,$percentage);
		}
		
		if ($qualityRecIns!="") {
			//$addMode	=	true;
			/*$sessObj->createSession("displayMsg",$msg_succAddDailyGrossWt);
			$sessObj->createSession("nextPage",$url_afterAddDailyGrossWt);*/
		} else {
			//$addMode	=	true;
			$err		=	$msg_failAddEntryQuality;
		}
		$dailyGrossRecIns		=	false;
	}

	if ($p["cmdSaveChange"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++) {
			$qualityId	= $p["qualityId_".$i];
			$percentage	= $p["qualityPercent_".$i];
			$currentId	= $p["curEntryId"];

			if ($qualityId!="") {
				$qualityUpdateRec		=	$dailycatchentryObj->updateQuality($qualityId,$percentage,$currentId);	
			}
		}
		if ($qualityUpdateRec) {
			//$sessObj->createSession("displayMsg",$msg_succUpdateQuality);
			//$sessObj->createSession("nextPage",$url_afterDelProcessor);
		} else {
			$err	=	$msg_failUpdateQuality;
		}
		$qualityUpdateRec	=	false;
	}


	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++) {
			$qualityId	=	$p["delId_".$i];

			if( $qualityId!="" ) {
				// Need to check the selected Processor is link with any other process 
				$qualityRecDel		=	$dailycatchentryObj->deleteQuality($qualityId);	
			}
		}
		/*if($processorRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProcessor);
			$sessObj->createSession("nextPage",$url_afterDelProcessor);
		} else {
			$errDel	=	$msg_failDelProcessor;
		}
		$processorRecDel	=	false;*/
	}


	#fetch all quality records
	if ($p["curEntryId"]=="") $currentId = $g["entryId"];
	else $currentId = $p["curEntryId"];

	if ($currentId) $qualityRecords	= $dailycatchentryObj->fetchAllQualityRecords($currentId);
	$qualityRecordSize	= sizeof($qualityRecords);

	# Quality Master Recs
	//$qualityMasterRecords = $qualitymasterObj->fetchAllRecords();

	$qualityMasterRecords = $qualitymasterObj->fetchAllRecordsActiveQuality();
?>
<html>
<head>
<TITLE></TITLE>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="libjs/dailycatchentry.js"></script>
<script type="text/javascript" src="libjs/generalFunctions.js"></script>
</head>
<body marginheight="0" marginwidth="0" bgcolor="#e8edff">
<form name="frmEntryQuality" action="CatchEntryQuality_new.php" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" id="newspaper-dce-decl">
	<? if($err!="" ){?>
  <tr>
    <td colspan="3" align="center" class="err1" ><?=$err;?></td>
    </tr>
	<?}?>
  <tr align="center">
    <td class="fieldName1" style="text-align:center;">Quality</td>
    <td class="fieldName1" style="text-align:center;">Percent</td>
    <td>&nbsp;</td>
  </tr>
  <tr align="center">
    <td class="fieldName1">
	<select name="entryQuality" style="width:90%;">
		<option value="">--Select--</option>
		<?php
		foreach ($qualityMasterRecords as $qmr) {
			$qualityMasterId = $qmr[0];
			$qualityName	 = $qmr[1];
		?>
		<option value="<?=$qualityMasterId?>"><?=$qualityName?></option>
		<?php
			}
		?>
    	</select>	
	</td>
    <td class="listing-item" nowrap><input name="qualityPercent" type="text" id="qualityPercent" size="2"  style="text-align:right;" />
%</td>
    <td class="listing-item"><input type="hidden" name="curEntryId" value="<?=$currentId?>" />
      <input type="submit" name="cmdQuality" class=button value="Add" /></td>
  </tr>
  <tr>
    <td colspan="3" align="center">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" id="newspaper-b1">
	<?php
		if (sizeof($qualityRecords)) {
			$i	=	0;
	?>
	<thead>
		<tr class="listing-head">
			<th nowrap>&nbsp;</th>
			<th nowrap>Quality</th>
			<th nowrap>Percent(%)</th>
		</tr>
	</thead>
	<tbody>
	<?php
		foreach ($qualityRecords as $qr) {
			$i++;
			$qualityId		=	$qr[0];
			$quality		=	$qr[3];
			$percent	=	$qr[2];
	?>
      <tr>
        <td class="listing-item" nowrap><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$qualityId;?>" class="chkBox" ></td>
        <td class="listing-item" nowrap>
		<input type="hidden" name="qualityId_<?=$i;?>" value="<?=$qualityId?>" />
		<?=$quality?>
		<!--<input type="text" name="entryQuality_<?=$i;?>" value="<?=$quality?>" size="15" readonly style="border:none" ></td>-->
        <td class="listing-item" nowrap><input name="qualityPercent_<?=$i;?>" type="text" style="width:30%;" value="<?=$percent?>" size="3" maxlength="3" /></td>
      </tr>
	  <? }?>
	</tbody>
	  <input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" readonly="true" >
	<tr bgcolor="White">
	    <td colspan="3" align="center">
		<input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$qualityRecordSize;?>);">
	      &nbsp;&nbsp;
	      <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " /></td>
	    </tr>
	<?php
		 } // Size check ends here
	?>
    </table></td>
    </tr>
	<!--<tr bgcolor="White">
	    <td align="center" colspan="3">
		<input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$qualityRecordSize;?>);">
	      &nbsp;&nbsp;
	      <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " /></td>
	    </tr>-->
</table>
<script language="javascript" type="text/javascript">
	function getValueEntryId()
	{
		var curr_Id	= parent.document.frmDailyCatch.catchEntryNewId.value;
		document.frmEntryQuality.curEntryId.value	=	curr_Id;
	}
	getValueEntryId();
	init_fields();
</script>
</form>
</body>
</html>
<?php
$outputContents = ob_get_contents(); 
ob_end_clean();
echo $outputContents;
?>