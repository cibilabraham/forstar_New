<?php
require("include/include.php");
ob_start();

$editMode		=	false;
$exceptionMode		=	false;
$saveChanged		=	false;
$editExceptionProcessorId = "";
$rate		= "";
$commission	= "";
$criteria	= "";

$exceptionEntryId	= "";

if ($p["processMainId"]=="") $processMainId = $g["lastId"];
else $processMainId = $p["processMainId"];

$editModeId	=	($g["editId"]=="")?$p["editModeId"]:$g["editId"];
	

	# Multiple add
	if ($p["cmdSaveException"]!="") {
		$tableRowCount	= $p["hidTableRowCount"];
		$exceptionEntryId = "";
		for ($j=0; $j<$tableRowCount; $j++) {
			$selStatus = $p["status_".$j]; 
			$exceptionEntryId = $p["hidExceptionId_".$j];
			if ($selStatus!='N') {
				$selPreProcessor	= $p["selProcessor_".$j];
				$processRate		= $p["processRate_".$j];
				$processCommission 	= $p["processCommission_".$j];
				$processCriteria	= $p["processCriteria_".$j];
				$yieldTolerance		= $p["yieldTolerance_".$j];
				
				# Chk Rec Exist
				$processorExmptExist	= $processObj->chkProcessorExceptionExist($processMainId, $selPreProcessor, $exceptionEntryId);
				if (!$processorExmptExist && $exceptionEntryId==""){	
					# Insert recs
					$exmptProcessorRecIns = $processObj->addProcessorExmpt($selPreProcessor, $processMainId, $processRate, $processCommission, $processCriteria, $yieldTolerance);	
				} else if (!$processorExmptExist && $exceptionEntryId!="") {
					# Update
					//$processorExptRecUptd	=	$processObj->updateProcessorException($processorExptId, $rate, $commission, $criteria);
					$updateExptProcessorRec = $processObj->updateProcessorExmpt($exceptionEntryId, $selPreProcessor, $processRate, $processCommission, $processCriteria, $yieldTolerance);
				}
			} // Status Check ends here
			
			if ($selStatus=='N' && $exceptionEntryId!="") {
				# Delete the rec
				$processorExptRecDel = $processObj->deleteProcessorExptRec($exceptionEntryId);
			}
		}
	}

	# List All Processors	
	$preProcessorRecords = $preprocessorObj->fetchAllRecords();

	//$noProcessorExptRateExist = $processObj->chkRateExist($processMainId);

	# Get Default rate exist
	$defaultRateExist = $processObj->defaultRateExist($processMainId);

	# Get All Recs
	$getProcessorExptRecs = $processObj->getProcessorExptRecs($processMainId);	
?>
<html>
<head>
<TITLE></TITLE>
<script language="javascript">	
	parent.document.frmProcess.noProcessorExptRate.value='<?=$noProcessorExptRateExist?>';
	parent.document.frmProcess.defaultRateExist.value='<?=$defaultRateExist?>';
</script>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!-- 
body {
	behavior:url("libjs/csshover.htc");
}
-->
</style>
<script type="text/javascript" src="libjs/generalFunctions.js"></script>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	<?php		
		require("libjs/config.js");
	?>
	//-->
</SCRIPT>
<script language="JavaScript" type="text/javascript">
<!--
<?php
	require("libjs/frozenpacking.js");
?>
//-->
</script>
</head>
<body marginheight="0" marginwidth="0" bgcolor="#e8edff">
<form name="frmProcessPreProcessors" id="frmProcessPreProcessors" action="ProcessPreProcessors.php" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0" >
 <tr><TD>
	<table>
			<!--  Dynamic Row adding starts here-->
				<tr>
					<td colspan="2" style="padding-left:5px; padding-right:5px;">
						<table cellspacing="1" cellpadding="2" id="tblProcessorExpt" class="newspaperType">
						<thead>
						<TR align="center">
							<th style="padding-left:5px; padding-right:5px;">*Processor</th>
							<th nowrap style="padding-left:5px; padding-right:5px;">*Rate (Rs.)</th>
							<th nowrap style="padding-left:5px; padding-right:5px;">*Commission (Rs.)</th>
							<th nowrap style="padding-left:5px; padding-right:5px;">Criteria</th>	
							<th nowrap style="padding-left:5px; padding-right:5px;">Yield<br>Tolerance(%)</th>
							<th>&nbsp;</th>
						</TR>	
						</thead>
						</table>
						<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="">
						</td>
					</tr>
					<tr><TD height="10"></TD></tr>
				<tr>
					<TD nowrap style="padding-left:5px; padding-right:5px;">
						<table>
							<TR>
								<TD>
									<input name="cmdException" type="button" class="button" id="cmdException" style="width:100px;" value="Add Exceptions" onclick="addNewStateRow();">
								</TD>
								<TD>
									<input name="cmdSaveException" type="submit" class="button" id="cmdSaveException" style="width:100px;" value="Save Exception" onclick="return validateAddProcessorExptR();" />
								</TD>
							</TR>
						</table>
					</TD>
				</tr>
				<!--  Dynamic Row adding ends here-->
			</table>
</TD></tr>
	<tr><TD>
<INPUT type="hidden" name="editModeId" value="<?=$editModeId?>">
<input type="hidden" name="processMainId" value="<?=$processMainId?>">
</TD></tr>
 </table>
  <?
  	if($processorExptRecDel || $p["cmdSaveChange"]!=""){
  ?>
  <script type="text/javascript">
  parent.iFrame2.document.frmProcessPreProcessors.submit();
  </script>
  <?
  }
  ?>
	<script language="JavaScript">
			function addNewStateRow() 
			{
				addNewExceptionRow('tblProcessorExpt', '', '', '', '', '', '');
			}		
		</script>
	<script language="JavaScript" type="text/javascript">
		<?php
		if (sizeof($getProcessorExptRecs)>0) {
			 foreach ($getProcessorExptRecs as $per) {
				$exceptionId 		= $per[0];
				$exptProcessorId	= $per[1];
				$exptRate		= $per[2];
				$exptCommission		= $per[3];
				$exptCriteria		= $per[4];
				$selYieldTolerance		= $per[5];
		?>
		addNewExceptionRow('tblProcessorExpt','<?=$exptProcessorId?>','<?=$exptRate?>', '<?=$exptCommission?>', '<?=$exptCriteria?>', '<?=$exceptionId?>', '<?=$selYieldTolerance?>');
		<?php			
			} // Loop ends here
		} else {
		?>		
		window.onLoad = addNewStateRow();
		<?php
			 }
		?>
	</script>	
</form>
</body>
</html>
<?php
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>