<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;

	if ($p["mainId"]=="")	$mainId = $g["mainId"];
	else $mainId = $p["mainId"];
	//echo $mainId;

	#Insert one record
	if ($p["cmdAdd"]!="") {

		$mainId 	=	$p["mainId"];

		$freezerId		=	$p["freezerName"];

		$startTimeHour		=	$p["startTimeHour"];
		$startTimeMints		=	$p["startTimeMints"];
		$startTimeOption 	= 	$p["startTimeOption"];

		$startTime	=	$p["startTimeHour"]."-".$p["startTimeMints"]."-".$p["startTimeOption"];

		$startTemp	=	$p["startTemp"];

		$stopTimeHour		=	$p["stopTimeHour"];
		$stopTimeMints		=	$p["stopTimeMints"];
		$stopTimeOption 	= 	$p["stopTimeOption"];

		$stopTime	=	$p["stopTimeHour"]."-".$p["stopTimeMints"]."-".$p["stopTimeOption"];

		$stopTemp	=	$p["stopTemp"];

		$coreTemp	=	$p["coreTemp"];

		$unloadTimeHour		=	$p["unloadTimeHour"];
		$unloadTimeMints	=	$p["unloadTimeMints"];
		$unloadTimeOption 	= 	$p["unloadTimeOption"];

		$unloadTime	=	$p["unloadTimeHour"]."-".$p["unloadTimeMints"]."-".$p["unloadTimeOption"];

		$volt		=	$p["volt"];
		$ampere		=	$p["ampere"];

		$consumElectricity	=	$p["consumElectricity"];
		$consumWater		=	$p["consumWater"];

		$remarks		=	$p["remarks"];
	

		if ($freezerId!="") {
			#Daily Activity Chart Entry Rec Insertion
			$dailyActivityChartRecIns = $dailyactivitychartObj->addDailyActivityEntryDetailsRec($mainId, $freezerId, $startTime, $startTemp, $stopTime, $stopTemp, $coreTemp, $unloadTime, $volt, $ampere, $consumElectricity, $consumWater, $remarks);
			
			if ($dailyActivityChartRecIns) {

				$sessObj->createSession("displayMsg", $msg_succAddDailyActivityChart);
				$addMode=true;
				$freezerId	=	"";
				$p["freezerName"]	=	"";
				$startTimeHour		=	"";
				$p["startTimeHour"]	=	"";
				$startTimeMints		=	"";
				$p["startTimeMints"]	=	"";
				$startTimeOption 	= 	"";
				$p["startTimeOption"]	=	"";
				$startTemp		=	"";
				$p["startTemp"]		=	"";
				$stopTimeHour		=	"";
				$p["stopTimeHour"]	=	"";
				$stopTimeMints		=	"";
				$p["stopTimeMints"]	=	"";
				$stopTimeOption 	= 	"";
				$p["stopTimeOption"]	=	"";
				$stopTemp		=	"";
				$p["stopTemp"]		=	"";
				$coreTemp		=	"";
				$p["coreTemp"]		=	"";
				$unloadTimeHour		=	"";
				$p["unloadTimeHour"]	=	"";
				$unloadTimeMints	=	"";
				$p["unloadTimeMints"]	=	"";
				$unloadTimeOption 	= 	"";
				$p["unloadTimeOption"]	=	"";
				$volt			=	"";
				$p["volt"]		=	"";
				$ampere			=	"";
				$p["ampere"]		=	"";
				$consumElectricity	=	"";
				$p["consumElectricity"]	=	"";
				$consumWater		=	"";
				$p["consumWater"]	=	"";
				$remarks		=	"";
				$p["remarks"]		=	"";
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddDailyActivityChart;
			}
			$dailyActivityChartRecIns	=	false;
		}
	}

	# Edit
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;

		$dailyActivityChartRec = $dailyactivitychartObj->findDailyActivityChartRec($editId);
		
		$entryId 	=	$dailyActivityChartRec[0];
		
		$freezerId		=	$dailyActivityChartRec[1];

		$startTime		=	explode("-", $dailyActivityChartRec[2]);
		$startTimeHour		=	$startTime[0];
		$startTimeMints		=	$startTime[1];
		$startTimeOption 	= 	$startTime[2];

		$startTemp	=	$dailyActivityChartRec[3];

		$stopTime		=	explode("-", $dailyActivityChartRec[4]);
		$stopTimeHour		=	$stopTime[0];
		$stopTimeMints		=	$stopTime[1];
		$stopTimeOption 	= 	$stopTime[2];

		$stopTemp	=	$dailyActivityChartRec[5];

		$coreTemp	=	$dailyActivityChartRec[6];

		$unloadTime	=	explode("-", $dailyActivityChartRec[7]);
		$unloadTimeHour		=	$unloadTime[0];
		$unloadTimeMints	=	$unloadTime[1];
		$unloadTimeOption 	= 	$unloadTime[2];

		//$unloadTime	=	$p["unloadTimeHour"]."-".$p["unloadTimeMints"]."-".$p["unloadTimeOption"];

		$volt		=	$dailyActivityChartRec[8];
		$ampere		=	$dailyActivityChartRec[9];

		$consumElectricity	=	$dailyActivityChartRec[10];
		$consumWater		=	$dailyActivityChartRec[11];

		$remarks		=	$dailyActivityChartRec[12];
	}


#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$entryId 	=	$p["hidDailyActivityChartEntryId"];

		$freezerId		=	$p["freezerName"];

		$startTimeHour		=	$p["startTimeHour"];
		$startTimeMints		=	$p["startTimeMints"];
		$startTimeOption 	= 	$p["startTimeOption"];

		$startTime	=	$p["startTimeHour"]."-".$p["startTimeMints"]."-".$p["startTimeOption"];

		$startTemp	=	$p["startTemp"];

		$stopTimeHour		=	$p["stopTimeHour"];
		$stopTimeMints		=	$p["stopTimeMints"];
		$stopTimeOption 	= 	$p["stopTimeOption"];

		$stopTime	=	$p["stopTimeHour"]."-".$p["stopTimeMints"]."-".$p["stopTimeOption"];

		$stopTemp	=	$p["stopTemp"];

		$coreTemp	=	$p["coreTemp"];

		$unloadTimeHour		=	$p["unloadTimeHour"];
		$unloadTimeMints	=	$p["unloadTimeMints"];
		$unloadTimeOption 	= 	$p["unloadTimeOption"];

		$unloadTime	=	$p["unloadTimeHour"]."-".$p["unloadTimeMints"]."-".$p["unloadTimeOption"];

		$volt		=	$p["volt"];
		$ampere		=	$p["ampere"];

		$consumElectricity	=	$p["consumElectricity"];
		$consumWater		=	$p["consumWater"];

		$remarks		=	$p["remarks"];


		if ($mainId && $freezerId) {
			#Daily Activity Chart Entry Rec Uptd
			$updateDailyActivityChartEntryRec = $dailyactivitychartObj->updateDailyActivityEntryRec($entryId, $freezerId, $startTime, $startTemp, $stopTime, $stopTemp, $coreTemp, $unloadTime, $volt, $ampere, $consumElectricity, $consumWater, $remarks);
		}
	
		if ($updateDailyActivityChartEntryRec) {
			$editMode	=	false;
			$freezerId	=	"";
				$p["freezerName"]	=	"";
				$startTimeHour		=	"";
				$p["startTimeHour"]	=	"";
				$startTimeMints		=	"";
				$p["startTimeMints"]	=	"";
				$startTimeOption 	= 	"";
				$p["startTimeOption"]	=	"";
				$startTemp		=	"";
				$p["startTemp"]		=	"";
				$stopTimeHour		=	"";
				$p["stopTimeHour"]	=	"";
				$stopTimeMints		=	"";
				$p["stopTimeMints"]	=	"";
				$stopTimeOption 	= 	"";
				$p["stopTimeOption"]	=	"";
				$stopTemp		=	"";
				$p["stopTemp"]		=	"";
				$coreTemp		=	"";
				$p["coreTemp"]		=	"";
				$unloadTimeHour		=	"";
				$p["unloadTimeHour"]	=	"";
				$unloadTimeMints	=	"";
				$p["unloadTimeMints"]	=	"";
				$unloadTimeOption 	= 	"";
				$p["unloadTimeOption"]	=	"";
				$volt			=	"";
				$p["volt"]		=	"";
				$ampere			=	"";
				$p["ampere"]		=	"";
				$consumElectricity	=	"";
				$p["consumElectricity"]	=	"";
				$consumWater		=	"";
				$p["consumWater"]	=	"";
				$remarks		=	"";
				$p["remarks"]		=	"";
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
		for($i=1; $i<=$rowCount; $i++)
		{
			$dailyActivityChartEntryId	=	$p["delId_".$i];
			
			if ($dailyActivityChartEntryId!="") {
				//Delete Entry Table Record
				$dailyActivityChartEntryRecDel = $dailyactivitychartObj->deleteDailyActivityChartEntryRec($dailyActivityChartEntryId);
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




		$dailyActivityChartEntryRecords	=	$dailyactivitychartObj->fetchDailyChartEntry($mainId);
		$dailyActivityChartRecordSize	=	sizeof($dailyActivityChartEntryRecords);
	

	#List all Freezer Records
		$freezerRecords = $freezercapacityObj->fetchAllRecords();
?>
	<link href="libjs/style.css" rel="stylesheet" type="text/css">
	<script language="JavaScript" type="text/JavaScript" src="libjs/dailyactivitychart.js"></script>
	<script type="text/javascript" src="libjs/generalFunctions.js"></script>
	<form name="frmDailyActivityChartDetails" action="DailyActivityChartDetails.php" method="post">
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

				<td align="center"><input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyActivityChartDetails(document.frmDailyActivityChartDetails);">												</td>
						<?} else{?>
						<td align="center">
						<input type="submit" name="cmdAdd" class="button" value="Save" onClick="return validateDailyActivityChartDetails(document.frmDailyActivityChartDetails);"></td>
					<?}?>
					</tr>
					<input type="hidden" name="hidDailyActivityChartEntryId" value="<?=$entryId;?>">

											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
                  <tr>
                       <td nowrap>
			<table>
				<TR>
					<TD class="fieldName">PF No/BF No:</TD>
					<TD>
						<SELECT name="freezerName">
						<option value="">--Select --</option>
						<?
						foreach($freezerRecords as $fr)
						{
							$freezerRecId	=	$fr[0];
							$freezerName	=	stripSlash($fr[1]);
							$selected = "";
							if($freezerId==$freezerRecId) $selected = "Selected";
						?>
						<option value="<?=$freezerRecId?>" <?=$selected?>><?=$freezerName?></option>
						<? }?>
						</SELECT>
					</TD>
				</TR>
			</table>
			</td>
			<!-- Seconf Column Starting here -->
			<td nowrap rowspan="4" valign="top">
			<table cellpadding="0" cellspacing="0">
			<TR><TD>
				<table cellpadding="0" cellspacing="0">
					<TR>
						<TD class="fieldName">Volt:</TD>
						<TD><INPUT type="text" size="5" name="volt" value="<?=$volt?>"></TD>
					</TR>
					<TR>
						<TD class="fieldName">Amps:</TD>
						<TD><INPUT type="text" size="5" name="ampere" value="<?=$ampere?>"></TD>
					</TR>
				</table>
			</TD></TR>
			<tr>
			<td>
			<table cellpadding="0" cellspacing="0">
				<TR>
				<TD>
				<table cellpadding="0" cellspacing="0">
				<TR><TD>
				<fieldset><legend class="listing-item">Consumption</legend>
				<table>
					<TR>
     					<TD class="fieldName">Electricity</TD>
					<td><INPUT type="text" size="4" name="consumElectricity" value="<?=$consumElectricity?>"></td>
					</TR>
					<TR>
					<TD class="fieldName">Water</TD>
					<TD class="fieldName"><INPUT type="text" size="4" name="consumWater" value="<?=$consumWater?>"></TD>
					</TR>
				</table>
				</fieldset>
				</TD></TR>
				</table>
				</TD>
			</TR>
			</table></td></tr>
			<tr>
			<TD>
				<table cellpadding="0" cellspacing="0">
				<TR>
					<TD class="fieldName">Remarks</TD>
					
				</TR>
				<tr><TD><textarea rows="2" cols="20" name="remarks"><?=$remarks?></textarea></TD></tr>
				</table>
			</TD>
			</tr>
			</table>
			<!-- Second Column  End -->
			</td>
                  </tr>
		  <tr>
			<TD nowrap>
			<table cellpadding="0" cellspacing="0">
				<TR>
				<TD>
				<table cellpadding="0" cellspacing="0">
				<TR><TD>
				<fieldset><legend class="listing-item">Start</legend>
				<table cellpadding="0" cellspacing="0">
					<TR>
     <TD class="fieldName">Time</TD>
					<td nowrap="nowrap">
				  <?
				  if ($addMode==true) {
				  	if($p["startTimeHour"]!="") $startTimeHour		=	$p["startTimeHour"];
				}
				 
				 if ($startTimeHour=="") {
				  	$startTimeHour		=	date("g");
				  }
				  ?>
				  <input type="text" id="startTimeHour" name="startTimeHour" size="1" value="<?=$startTimeHour;?>" onchange="return timeCheck();" style="text-align:center;" maxlength="2">:
				<?
				if ($addMode==true) {
			  		if($p["startTimeMints"]!="") $startTimeMints	=	$p["startTimeMints"];
			  	}
			  	if($startTimeMints=="") {
				  	$startTimeMints		=	date("i");
				}
				 
				?>
				    <input type="text" id="startTimeMints" name="startTimeMints" size="1" value="<?=$startTimeMints;?>" onchange="return timeCheck();" style="text-align:center;" maxlength="2">
				  <?
					if ($addMode==true) {
						if($p["startTimeOption"]!="") $startTimeOption = $p["startTimeOption"];
				  	}
					if($startTimeOption=="") {
						$startTimeOption = date("A");
					}
				  ?>
                    	<select name="startTimeOption" id="startTimeOption">
				<option value="AM" <? if($startTimeOption=='AM') echo "selected"?>>AM</option>
				<option value="PM" <? if($startTimeOption=='PM') echo "selected"?>>PM</option>
                    	</select></td>
					</TR>
					<TR>
					<TD class="fieldName">Temp</TD>
					<TD class="fieldName"><INPUT type="text" size="3" name="startTemp" value="<?=$startTemp?>"></TD>
					</TR>
				</table>
				</fieldset>
				</TD></TR>
				</table>
				</TD>
			<td><table cellpadding="0" cellspacing="0">
				<TR><TD>
				<fieldset><legend class="listing-item">Stop</legend>
				<table cellpadding="0" cellspacing="0">
					<TR>
					<TD class="fieldName">Time</TD>
					<td nowrap="nowrap">
				  <?
				  if ($addMode==true) {
				  	if($p["stopTimeHour"]!="") $stopTimeHour   =	$p["stopTimeHour"];
				}
				 
				 if ($stopTimeHour=="") {
				  	$stopTimeHour		=	date("g");
				  }
				  ?>
				  <input type="text" id="stopTimeHour" name="stopTimeHour" size="1" value="<?=$stopTimeHour;?>" onchange="return timeCheck();" style="text-align:center;" maxlength="2">:
				<?
				if ($addMode==true) {
			  		if($p["stopTimeMints"]!="") $stopTimeMints = $p["selectTimeMints"];
			  	}
			  	if($stopTimeMints=="") {
				  	$stopTimeMints		=	date("i");
				}
				 
				?>
				    <input type="text" id="stopTimeMints" name="stopTimeMints" size="1" value="<?=$stopTimeMints;?>" onchange="return timeCheck();" style="text-align:center;" maxlength="2">
				  <?
					if ($addMode==true) {
						if($p["stopTimeOption"]!="") $stopTimeOption = $p["stopTimeOption"];
				  	}
					if($stopTimeOption=="") {
						$stopTimeOption = date("A");
					}
				  ?>
                    	<select name="stopTimeOption" id="stopTimeOption">
				<option value="AM" <? if($stopTimeOption=='AM') echo "selected"?>>AM</option>
				<option value="PM" <? if($stopTimeOption=='PM') echo "selected"?>>PM</option>
                    	</select></td>
					</TR>
					<TR>
					<TD class="fieldName">Temp</TD>
					<TD class="fieldName"><INPUT type="text" size="3" name="stopTemp" value="<?=$stopTemp?>"></TD>
					</TR>
				</table>
				</fieldset>
				</TD></TR>
				</table></td>
			</TR>
			</table>
			</TD>

                  </tr>
		<tr>
                    <TD>
			<table>
				<TR>
					<TD class="fieldName">Core Temp:</TD>
					<TD><INPUT type="text" size="5" name="coreTemp" value="<?=$coreTemp?>"></TD>
				</TR>
			</table>
                  </TD>
			
                  </tr>
                 <tr>
			<TD>
			<table>
				<TR>
					<TD class="fieldName">Unloading:</TD>
					<td nowrap="nowrap">
				  <?
				  if ($addMode==true) {
				  	if($p["unloadTimeHour"]!="") $unloadTimeHour = $p["unloadTimeHour"];
				}
				 
				 if ($unloadTimeHour=="") {
				  	$unloadTimeHour		=	date("g");
				  }
				  ?>
				  <input type="text" id="unloadTimeHour" name="unloadTimeHour" size="1" value="<?=$unloadTimeHour;?>" onchange="return timeCheck();" style="text-align:center;" maxlength="2">:
				<?
				if ($addMode==true) {
			  		if($p["unloadTimeMints"]!="") $unloadTimeMints = $p["unloadTimeMints"];
			  	}
			  	if($unloadTimeMints=="") {
				  	$unloadTimeMints	=	date("i");
				}
				 
				?>
				    <input type="text" id="unloadTimeMints" name="unloadTimeMints" size="1" value="<?=$unloadTimeMints;?>" onchange="return timeCheck();" style="text-align:center;" maxlength="2">
				  <?
					if ($addMode==true) {
						if($p["unloadTimeOption"]!="") $unloadTimeOption = $p["unloadTimeOption"];
				  	}
					if($unloadTimeOption=="") {
						$unloadTimeOption = date("A");
					}
				  ?>
                    	<select name="unloadTimeOption" id="unloadTimeOption">
				<option value="AM" <? if($unloadTimeOption=='AM') echo "selected"?>>AM</option>
				<option value="PM" <? if($unloadTimeOption=='PM') echo "selected"?>>PM</option>
                    	</select></td>
				</TR>
			</table>
                        </TD>
		</tr>
		<tr>
		<? if($editMode){?>
		<td align="center">
			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDailyActivityChartDetails(document.frmDailyActivityChartDetails);">
		</td>
		<? } else { ?>
		<td align="center">
		<input type="submit" name="cmdAdd" class="button" value=" Save " onClick="return validateDailyActivityChartDetails(document.frmDailyActivityChartDetails);">
		</td>
		<? }?>
		</tr>
                </table></td>
		</tr>
		<tr><TD height="5"></TD></tr>

<!-- Form Listing Starts Here	 -->
	
		<tr><TD colspan="8">
		<table><TR><TD>
			<table cellpadding="1"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
			<?
			if (sizeof($dailyActivityChartEntryRecords) > 0) {
				$i	=	0;
			?>
		<tr bgcolor="#FFFFFF">
	    <td colspan="15" align="center"><input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$dailyActivityChartRecordSize;?>);"></td>	    </tr>
	<tr  bgcolor="#f2f2f2" align="center">
<td width="20" rowspan="2"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></td>
<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">Sl.<br>No </td>
<td class="listing-head" style="padding-left:5px; padding-right:5px;" rowspan="2">P.F.No/ B.F.No </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="2">Start </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="2">Stop </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Time<br> Diff</td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Core<br> Temp </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Unloading </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Volt </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" rowspan="2">Amps </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="2">Consumption</td>
<td class="listing-head" width="45" rowspan="2"></td>
</tr>
<tr  bgcolor="#f2f2f2" align="center">
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Time </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Temp </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Time </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Temp </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Electricity </td>
<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Water</td>
</tr>

			<?
			foreach($dailyActivityChartEntryRecords as $dacr)
			{
				$i++;
				$dailyActivityChartEntryId	=	$dacr[0];

				$selFreezerId			=	$dacr[1];
				$freezerNo			=	$freezercapacityObj->findFreezer($dacr[1]);
				
				$startTime			=	$dacr[2];
				$startTemp			=	$dacr[3];
				$stopTime			=	$dacr[4];
				$stopTemp			=	$dacr[5];
				$coreTemp			=	$dacr[6];
				$unloadTime			=	$dacr[7];
				$volt				=	$dacr[8];
				$ampere				=	$dacr[9];
				$consumElectricity		=	$dacr[10];
				$consumWater			=	$dacr[11];
				#----------------------------------------------------
				//Calculating difference between Start and Stoptime
				$freezerTime  = $freezercapacityObj->getFreezerTime($selFreezerId);

				list($startTimeHour, $startTimeMints, $startTimeOption) = explode("-", $startTime);
				$parseStartTime = "$startTimeHour"."-"."$startTimeMints";
				$startTimeStamp = getTimeStamp($parseStartTime); //From Config File

				list($stopTimeHour, $stopTimeMints, $stopTimeOption) = explode("-", $stopTime);
				$parseStopTime 	= "$stopTimeHour"."-"."$stopTimeMints";
				$stopTimeStamp = getTimeStamp($parseStopTime);
				$mode='H';
				$workedTime = abs(dateDiff($startTimeStamp, $stopTimeStamp, $mode));
				$timeDiff = abs($freezerTime - $workedTime);
				//echo "$freezerTime-$workedTime<br>";
				$displayDiffTime = "";
				if ($freezerTime<$workedTime) {
					$displayDiffTime = "<span style=\"color:#FF0000\">"."+".$workedTime."</span>";
				} else if ($freezerTime>$workedTime &&  $workedTime!=0) {
					$displayDiffTime = "-".$workedTime;
				} else {
					$displayDiffTime = "";
				}
				//---------------------------------------------------
			?>
<tr  bgcolor="WHITE"  >
<td width="20" height="25"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$dailyActivityChartEntryId;?>" class="chkBox"></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$i;?></td>
<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$freezerNo;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$startTime;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$startTemp;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$stopTime;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$stopTemp;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$displayDiffTime;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$coreTemp;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$unloadTime;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$volt;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$ampere;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$consumElectricity;?></td>
<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$consumWater;?></td>
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
  parent.iFrame1.document.frmDailyActivityChartDetails.submit();
  </script>
  <?
  }
  ?>
	</form>