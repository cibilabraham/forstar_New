<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;

	if ($p["mainId"]=="")	$mainId = $g["mainId"];
	else $mainId = $p["mainId"];
	

	#Insert Record
	if ($p["cmdAdd"]!="") {

		$mainId 	= $p["mainId"];

		$coldTempTimeHour	=	$p["coldTempTimeHour"];
		$coldTempTimeMints	=	$p["coldTempTimeMints"];
		$coldTempTimeOption 	= 	$p["coldTempTimeOption"];

		$selColdTempTime = $p["coldTempTimeHour"]."-".$p["coldTempTimeMints"]."-".$p["coldTempTimeOption"];

		$coldTemp1	= $p["coldTemp1"];
		$coldTemp2	= $p["coldTemp2"];
		$coldTemp3	= $p["coldTemp3"];
		$coldTemp4	= $p["coldTemp4"];
		$coldTemp5	= $p["coldTemp5"];
		$IQF		= $p["IQF"];
	

		if ($mainId!=""  && $coldTemp1 && $coldTemp2 && $coldTemp3 && $coldTemp4 && $coldTemp5) {
			#Daily Activity Chart Entry Rec Insertion
			$dailyActivityChartRecIns = $dailyactivitychartObj->addDailyActivityEntryColdTempRec($mainId, $selColdTempTime, $coldTemp1, $coldTemp2, $coldTemp3, $coldTemp4, $coldTemp5, $IQF);
			
			if ($dailyActivityChartRecIns) {

				$sessObj->createSession("displayMsg", $msg_succAddDailyActivityChart);
				$addMode=true;
				$coldTempTimeHour	=	"";
				$p["coldTempTimeHour"]	=	"";
				$coldTempTimeMints	=	"";
				$p["coldTempTimeMints"]	=	"";
				$coldTempTimeOption 	= 	"";
				$p["coldTempTimeOption"] = 	"";
				$coldTemp1	= "";
				$p["coldTemp1"] = "";
				$coldTemp2	= "";
				$p["coldTemp2"]	= "";
				$coldTemp3	= "";
				$p["coldTemp3"]	= "";
				$coldTemp4	= "";
				$p["coldTemp4"]	= "";
				$coldTemp5	= "";
				$p["coldTemp5"]	= "";
				$IQF		= "";
				$p["IQF"]	= "";
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
		$dailyActivityChartRec = $dailyactivitychartObj->findDailyActivityChartColdTempRec($editId);		
		$entryId 	=	$dailyActivityChartRec[0];	

		$selColdTempTime = explode("-", $dailyActivityChartRec[1]);;

		$coldTempTimeHour	=	$selColdTempTime[0];
		$coldTempTimeMints	=	$selColdTempTime[1];
		$coldTempTimeOption 	= 	$selColdTempTime[2];

		$coldTemp1	= $dailyActivityChartRec[2];
		$coldTemp2	= $dailyActivityChartRec[3];
		$coldTemp3	= $dailyActivityChartRec[4];
		$coldTemp4	= $dailyActivityChartRec[5];
		$coldTemp5	= $dailyActivityChartRec[6];
		$IQF		= $dailyActivityChartRec[7];
	}




#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$entryId 	=	$p["hidDailyActivityChartEntryId"];
		
		$coldTempTimeHour	=	$p["coldTempTimeHour"];
		$coldTempTimeMints	=	$p["coldTempTimeMints"];
		$coldTempTimeOption 	= 	$p["coldTempTimeOption"];

		$selColdTempTime = $p["coldTempTimeHour"]."-".$p["coldTempTimeMints"]."-".$p["coldTempTimeOption"];

		$coldTemp1	= $p["coldTemp1"];
		$coldTemp2	= $p["coldTemp2"];
		$coldTemp3	= $p["coldTemp3"];
		$coldTemp4	= $p["coldTemp4"];
		$coldTemp5	= $p["coldTemp5"];
		$IQF		= $p["IQF"];	


		if ($mainId && $entryId && $coldTemp1 && $coldTemp2 && $coldTemp3 && $coldTemp4 && $coldTemp5) {
			#Daily Activity Chart Entry Rec Uptd
			$updateDailyActivityChartEntryRec = $dailyactivitychartObj->updateDailyActivityEntryColdTempRec($entryId, $selColdTempTime, $coldTemp1, $coldTemp2, $coldTemp3, $coldTemp4, $coldTemp5, $IQF);
		}
	
		if ($updateDailyActivityChartEntryRec) {
			$editMode	=	false;			
			$coldTempTimeHour	=	"";
			$p["coldTempTimeHour"]	=	"";
				$coldTempTimeMints	=	"";
				$p["coldTempTimeMints"]	=	"";
				$coldTempTimeOption 	= 	"";
				$p["coldTempTimeOption"] = 	"";
				$coldTemp1	= "";
				$p["coldTemp1"] = "";
				$coldTemp2	= "";
				$p["coldTemp2"]	= "";
				$coldTemp3	= "";
				$p["coldTemp3"]	= "";
				$coldTemp4	= "";
				$p["coldTemp4"]	= "";
				$coldTemp5	= "";
				$p["coldTemp5"]	= "";
				$IQF		= "";
				$p["IQF"]	= "";	
			
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
				$dailyActivityChartEntryRecDel = $dailyactivitychartObj->deleteDailyActivityChartColdTempEntry($dailyActivityChartEntryId);
			}

		}
		if ($dailyActivityChartEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDailyActivityChart);
			//$sessObj->createSession("nextPage",$url_afterDelDailyActivityChart.$selection);
		} else {
			$errDel	=	$msg_failDelDailyActivityChart;
		}
		$dailyActivityChartEntryRecDel	= false;
	}

	#List all records
	$dailyActivityColdTempEntryRecords = $dailyactivitychartObj->fetchDailyChartEntryColdTempRec($mainId);
	$dailyActivityChartRecordSize = sizeof($dailyActivityColdTempEntryRecords);	
?>
	<link href="libjs/style.css" rel="stylesheet" type="text/css">
	<script language="JavaScript" type="text/JavaScript" src="libjs/dailyactivitychart.js"></script>
	<script type="text/javascript" src="libjs/generalFunctions.js"></script>
	<form name="frmDailyActivityChartColdTemp" action="DailyActivityChartColdTemp.php" method="post">
	<table cellpadding="0"  width="550" cellspacing="0" border="0" align="center">
		<tr>
			<td height="5" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<tr>
			<td width="1" ></td>
			<td colspan="2" >
			<table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">
			<tr>
				<? if($editMode){ ?>
				<td align="center"><input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyActivityChartColdTemp(document.frmDailyActivityChartColdTemp);">												</td>
						<?} else{?>
						<td align="center">
						<input type="submit" name="cmdAdd" class="button" value="Save" onClick="return validateDailyActivityChartColdTemp(document.frmDailyActivityChartColdTemp);"></td>
					<?}?>
					</tr>
					<input type="hidden" name="hidDailyActivityChartEntryId" value="<?=$entryId;?>">
					<tr>
					  <td colspan="2" height="5"></td>
					</tr>
                  <tr>
                       <td nowrap>
			<table cellpadding="0" cellspacing="0" align="center">
				<tr>
					<TD>
<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">		
		
	<tr  bgcolor="#f2f2f2" align="center">
<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Time </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="5">Cold Storage Temp </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">IQF</td>
</tr>
<tr  bgcolor="#f2f2f2" align="center">
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">1 </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">2 </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">3 </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">4 </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">5 </td>
</tr>
<tr  bgcolor="WHITE">
<td class="listing-item" style="padding-left:5px; padding-right:5px;">
<table cellpadding="0" cellspacing="0">
				<TR>
					<td nowrap="nowrap">
				  <?
				  if ($addMode==true) {
				  	if($p["coldTempTimeHour"]!="") $coldTempTimeHour = $p["coldTempTimeHour"];
				  }
				 
				 if ($coldTempTimeHour=="") $coldTempTimeHour =	date("g");
				  
				  ?>
				  <input type="text" id="coldTempTimeHour" name="coldTempTimeHour" size="1" value="<?=$coldTempTimeHour;?>" onchange="return timeCheck();" style="text-align:center;" maxlength="2">:
				<?
				if ($addMode==true) {
			  		if($p["coldTempTimeMints"]!="") $coldTempTimeMints = $p["coldTempTimeMints"];
			  	}

			  	if($coldTempTimeMints=="") $coldTempTimeMints =	date("i");
				 
				?>
				    <input type="text" id="coldTempTimeMints" name="coldTempTimeMints" size="1" value="<?=$coldTempTimeMints;?>" onchange="return timeCheck();" style="text-align:center;" maxlength="2">
				  <?
					if ($addMode==true) {
						if($p["coldTempTimeOption"]!="") $coldTempTimeOption = $p["coldTempTimeOption"];
				  	}
					if ($coldTempTimeOption=="") $coldTempTimeOption = date("A");
				  ?>
                    	<select name="coldTempTimeOption" id="coldTempTimeOption">
				<option value="AM" <? if($coldTempTimeOption=='AM') echo "selected"?>>AM</option>
				<option value="PM" <? if($coldTempTimeOption=='PM') echo "selected"?>>PM</option>
                    	</select></td>
					</TR>					
				</table></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><INPUT type="text" size="3" name="coldTemp1" value="<?=$coldTemp1?>" style="text-align:right;"></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><INPUT type="text" size="3" name="coldTemp2" value="<?=$coldTemp2?>" style="text-align:right;"></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><INPUT type="text" size="3" name="coldTemp3" value="<?=$coldTemp3?>" style="text-align:right;"></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><INPUT type="text" size="3" name="coldTemp4" value="<?=$coldTemp4?>" style="text-align:right;"></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><INPUT type="text" size="3" name="coldTemp5" value="<?=$coldTemp5?>" style="text-align:right;"></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><INPUT type="text" size="3" name="IQF" value="<?=$IQF?>" style="text-align:right;"></td>
</tr>
	</table></TD></tr>				
			</table>
			</td>			
                  </tr>		
                 <tr>
			<TD>
                        </TD>
		</tr>
		<tr>
		<? if($editMode){?>
		<td align="center">
			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyActivityChartColdTemp(document.frmDailyActivityChartColdTemp);">
		</td>
		<? } else { ?>
		<td align="center">
		<input type="submit" name="cmdAdd" class="button" value=" Save " onClick="return validateDailyActivityChartColdTemp(document.frmDailyActivityChartColdTemp);">
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
			if (sizeof($dailyActivityColdTempEntryRecords) > 0) {
				$i	=	0;
			?>
		<tr bgcolor="#FFFFFF">
	    <td colspan="15" align="center"><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$dailyActivityChartRecordSize;?>);"></td>	    </tr>
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20" rowspan="2">
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Sl.<br>No </td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Time </td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="5">Cold Storage Temp </td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">IQF</td>
		<td class="listing-head" width="45" rowspan="2"></td>
	</tr>
	<tr  bgcolor="#f2f2f2" align="center">
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">1 </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">2 </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">3 </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">4 </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">5 </td>
</tr>
	<?
		foreach($dailyActivityColdTempEntryRecords as $dacr) {
			$i++;
			$dailyActivityChartEntryId	=	$dacr[0];
			$coldTempTime			=	$dacr[1];
			$coldTemp1			=	$dacr[2];	
			$coldTemp2			=	$dacr[3];			
			$coldTemp3			=	$dacr[4];
			$coldTemp4			=	$dacr[5];
			$coldTemp5			=	$dacr[6];
			$iQF				=	$dacr[7];
	?>
<tr  bgcolor="WHITE"  >
<td width="20" height="25"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyActivityChartEntryId;?>" class="chkBox"></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$i;?></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$coldTempTime;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$coldTemp1;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$coldTemp2;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$coldTemp3;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$coldTemp4;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$coldTemp5;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$iQF;?></td>
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
  parent.iFrame1.document.frmDailyActivityChartColdTemp.submit();
  </script>
  <?
  }
  ?>
	</form>