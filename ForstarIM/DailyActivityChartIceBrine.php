<?
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;

	if ($p["mainId"]=="") $mainId = $g["mainId"];
	else $mainId = $p["mainId"];
	

	#Insert Record
	if ($p["cmdAdd"]!="") {

		$mainId 	= $p["mainId"];
		$iceBrine	= $p["iceBrine"];
		$volt		= $p["volt"];
		$ampere		= $p["ampere"];	
	

		if ($mainId!="" && $iceBrine!="" && $volt!="" && $ampere!="") {
			#Daily Activity Chart Entry Rec Insertion
			$dailyActivityChartRecIns = $dailyactivitychartObj->addDailyActivityEntryIceBrineRec($mainId, $iceBrine, $volt, $ampere);
			
			if ($dailyActivityChartRecIns) {

				$sessObj->createSession("displayMsg", $msg_succAddDailyActivityChart);
				$addMode=true;
				$iceBrine		= 	"";
				$p["iceBrine"]		=	"";
				$volt			=	"";
				$p["volt"]		=	"";
				$ampere			=	"";
				$p["ampere"]		=	"";				
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddDailyActivityChart;
			}
			$dailyActivityChartRecIns	=	false;
		}
	}

	# Edit
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$dailyActivityChartRec = $dailyactivitychartObj->findDailyActivityChartIceBrineRec($editId);		
		$entryId 	=	$dailyActivityChartRec[0];		
		$iceBrine	= 	$dailyActivityChartRec[1];
		$volt		=	$dailyActivityChartRec[2];
		$ampere		=	$dailyActivityChartRec[3];	
	}


#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$entryId 	=	$p["hidDailyActivityChartEntryId"];
		
		$iceBrine	= 	$p["iceBrine"];
		$volt		=	$p["volt"];
		$ampere		=	$p["ampere"];		


		if ($mainId && $entryId && $iceBrine!="" && $volt!="" && $ampere!="") {
			#Daily Activity Chart Entry Rec Uptd
			$updateDailyActivityChartEntryRec = $dailyactivitychartObj->updateDailyActivityEntryIceBrineRec($entryId, $iceBrine, $volt, $ampere);
		}
	
		if ($updateDailyActivityChartEntryRec) {
			$editMode	=	false;			
			$iceBrine		= 	"";
			$p["iceBrine"]		=	"";
			$volt			=	"";
			$p["volt"]		=	"";
			$ampere			=	"";
			$p["ampere"]		=	"";	
			
			//$sessObj->createSession("displayMsg",$msg_succUpdateDailyActivityChart);
			//$sessObj->createSession("nextPage",$url_afterUpdateDailyActivityChart.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateDailyActivityChart;
		}
		$updateDailyActivityChartEntryRec	=	false;
	}
	

	# Delete
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$dailyActivityChartEntryId	=	$p["delId_".$i];
			
			if ($dailyActivityChartEntryId!="") {
				//Delete Entry Table Record
				$dailyActivityChartEntryRecDel = $dailyactivitychartObj->deleteDailyActivityChartIceBrineEntry($dailyActivityChartEntryId);
			}

		}
		if ($dailyActivityChartEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDailyActivityChart);
			//$sessObj->createSession("nextPage",$url_afterDelDailyActivityChart.$selection);
		} else {
			$errDel	=	$msg_failDelDailyActivityChart;
		}
		$dailyActivityChartEntryRecDel	=	false;
	}

	#List all ice Brine records
	$dailyActivityIceBrineEntryRecords = $dailyactivitychartObj->fetchDailyChartEntryIceBrineRec($mainId);
	$dailyActivityChartRecordSize	=	sizeof($dailyActivityIceBrineEntryRecords);	
?>
	<link href="libjs/style.css" rel="stylesheet" type="text/css">
	<script language="JavaScript" type="text/JavaScript" src="libjs/dailyactivitychart.js"></script>
	<script type="text/javascript" src="libjs/generalFunctions.js"></script>
	<form name="frmDailyActivityChartIceBrine" action="DailyActivityChartIceBrine.php" method="post">
	<table cellpadding="0"  width="550" cellspacing="0" border="0" align="center">
		<tr>
			<td height="5" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<tr>
			<td width="1" ></td>
			<td colspan="2" >
			<table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">
			<tr>
				<? if($editMode){?>
				<td align="center"><input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyActivityChartIceBrine(document.frmDailyActivityChartIceBrine);">												</td>
						<?} else{?>
						<td align="center">
						<input type="submit" name="cmdAdd" class="button" value="Save" onClick="return validateDailyActivityChartIceBrine(document.frmDailyActivityChartIceBrine);"></td>
					<?}?>
					</tr>
					<input type="hidden" name="hidDailyActivityChartEntryId" value="<?=$entryId;?>">
					<tr>
					  <td colspan="2" height="5"></td>
					</tr>
                  <tr>
                       <td nowrap>
			<table cellpadding="0" cellspacing="0" align="center">
				<TR>
					<TD class="fieldName">Ice brine Temp:</TD>
					<TD><INPUT type="text" size="5" name="iceBrine" value="<?=$iceBrine?>"></TD>
					<TD class="fieldName">&nbsp;Volt:</TD>
						<TD><INPUT type="text" size="5" name="volt" value="<?=$volt?>"></TD>
						<TD class="fieldName">&nbsp;Amps:</TD>
						<TD><INPUT type="text" size="5" name="ampere" value="<?=$ampere?>"></TD>					
				</TR>
			</table>
			</td>			
                  </tr>		
                 <tr>
			<TD></TD>
		</tr>
		<tr>
		<? if($editMode){?>
		<td align="center">
			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyActivityChartIceBrine(document.frmDailyActivityChartIceBrine);">
		</td>
		<? } else { ?>
		<td align="center">
		<input type="submit" name="cmdAdd" class="button" value=" Save " onClick="return validateDailyActivityChartIceBrine(document.frmDailyActivityChartIceBrine);">
		</td>
		<? }?>
		</tr>
                </table></td>
		</tr>
		<tr><TD height="5"></TD></tr>

<!-- Form Listing Starts Here	 -->
	
		<tr><TD colspan="8" align="center">
		<table><TR><TD>
			<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
			<?
			if (sizeof($dailyActivityIceBrineEntryRecords) > 0) {
				$i	=	0;
			?>
		<tr bgcolor="#FFFFFF">
	    <td colspan="15" align="center"><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$dailyActivityChartRecordSize;?>);"></td>	    </tr>
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Sl.<br>No </td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Ice Brine Temp </td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Volt </td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Amps </td>
		<td class="listing-head" width="45"></td>
	</tr>
	<?
		foreach($dailyActivityIceBrineEntryRecords as $dacr) {
			$i++;
			$dailyActivityChartEntryId	=	$dacr[0];
			$icebrineTemp			=	$dacr[1];			
			$volt				=	$dacr[2];
			$ampere				=	$dacr[3];				
	?>
<tr  bgcolor="WHITE"  >
<td width="20" height="25"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyActivityChartEntryId;?>" class="chkBox"></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$i;?></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$icebrineTemp;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$volt;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$ampere;?></td>
  <td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$dailyActivityChartEntryId;?>,'editId')"></td>

</tr>
	<?
		}
	?>
	<tr bgcolor="#FFFFFF">
	    <td colspan="15" align="center"><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$dailyActivityChartRecordSize;?>);"></td>
      </tr>

	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editMode" value="<?=$editMode?>">
	<?
		} else {
	?>
	<!--tr bgcolor="white">
		<td colspan="15"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr-->
	<?
		}
	?>

	</table>
	</TD></TR></table>
<!--  Listing end here-->
		</TD>
		</tr>
		<tr>
			<td  height="10">
			<input type="hidden" name="mainId" value="<?=$mainId?>">
			<input type="hidden" name="entryRecSize" value="<?=$dailyActivityChartRecordSize?>">
			</td>
		</tr>
		</table>

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

	<?
  	if($dailyActivityChartEntryRecDel || $p["cmdSaveChange"]!=""){
  ?>
  <script type="text/javascript">
  parent.iFrame1.document.frmDailyActivityChartIceBrine.submit();
  </script>
  <?
  }
  ?>
	</form>