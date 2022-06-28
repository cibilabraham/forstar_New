<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$confirmed		=	"";

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$reEdit  = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	if ($accesscontrolObj->canReEdit()) $reEdit=true;
	//----------------------------------------------------------
	
	# Reset variables
	$selectDate 	= "";
	$fromDate 	= "";
	$toDate 	= "";
	$searchMode = false;
	$selDate	= "";	
	$processorsRecs = array();
	$details = "";
	$summary = "";
	$selProcessor = "";
	$statusChangeMode = false;
	$releaseAllocation = false;
	$releaseInvoice = false;

	$enableSearch = false;

	if ($p["supplyFrom"]) $dateFrom = $p["supplyFrom"];
	if ($p["supplyTill"]) $dateTill = $p["supplyTill"];

	# Select a Date
	if ($p["selDate"]) $selDate = $p["selDate"];

	if ($p["details"]!="") $details	= $p["details"];
	if ($p["summary"]!="") $summary	= $p["summary"];

	#Select a day or date range SD-> Single Date/ DR-> Date Range
	
			
	//if (($p["cmdSearch"]!="" && ($details!="" || $summary!="")) || (($statusChangeMode || $enableSearch) && ($details!="" || $summary!="")) ) {
	if (($p["cmdSearch"]!="") || ($dateFrom!='' &&  $dateTill!='')) {

		if ($dateFrom) $fromDate  = mysqlDateFormat($dateFrom);
		if ($dateTill) $toDate  = mysqlDateFormat($dateTill);
			$rmlotid=$p["rmlotid"];
			$rmLotRecords=$freezingReportObj->getAllRMLot($fromDate,$toDate);
			$freezingRecords = $freezingReportObj->getFreezingDetail($fromDate,$toDate,$rmlotid);
			$freezingRecordsSize=sizeof($freezingRecords);
			$searchMode = true;
	}

	

	# Display heading
	$heading = $label_addFreezingReport;

	$ON_LOAD_PRINT_JS	= "libjs/rmvariancereport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmFreezingReport" action="FreezingReport.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<? if($err!="" ){?>
		<tr>
			<td height="30" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											 <tr> 
												<td colspan="4" align="center">
												 <? if($print==true){?>
													<!--input type="button" name="Submit" value=" View / Print" class="button" onClick="return printWindow('PrintDailyCatchSumary.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>',700,600);"-->&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; 
												</td>
												 <? } ?>
											</tr>
											<tr> 
												<td colspan="3" nowrap>
													<table  border="0" align="center" cellpadding="0" cellspacing="0">
														<tr>
															<TD>
																<table>
																	<TR>
																		<TD valign="top">
																			<table>
																				<TR>
																					<TD>
																						<fieldset>
																							<legend class="listing-item">Date Selection</legend>
																								<table width="200" border="0">
																									<tr>
																										<td>
																											<table width="100" border="0">
																												<tr> 
																													<td class="fieldName">From:</td>
																													<td nowrap="true">
																							<!--   -->					<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" autocomplete="off"   onchange="submitForm('supplyFrom','supplyTill', frmFreezingReport);"/>
																													</td>
																													<td class="fieldName">Till:</td>
																													<td nowrap="true">
																														<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" autocomplete="off"  onchange="submitForm('supplyFrom','supplyTill', frmFreezingReport);"/>
																													</td>
																												</tr>	
																											</table>
																										</td>
																									</tr>
																								</table>
																							</fieldset>
																						</TD>
																					</TR>
																					<tr>
																						<TD height="5"></TD>
																					</tr>
																					<tr>
																						<TD valign="top"></TD>
																					</tr>
																				</table>
																			</TD>
																			<TD valign="top">
																				<table width="200" border="0" cellpadding="0" cellspacing="0">
																					<tr>
																						<td valign="top">
																							<fieldset>
																								<legend class="fieldName">Search Options </legend>
																									<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
																										<!--<tr>
																											<td  class="fieldName">Unit</td>
																											<td>
																												<select id="unit" name="unit" >
																													<option value="">--Select All--</option>
																													<?php
																													
																														if(sizeof($units) > 0)
																														{
																															foreach($units as $unitval)
																															{
																																$sel = '';
																																if($unit == $unitval['id']) $sel = 'selected';
																																
																																echo '<option '.$sel.' value="'.$unitval['id'].'">'.$unitval['name'].'</option>';
																															}
																														}
																													?>			
																												</select>	
																											</td>
																										</tr>-->

																										<tr>
																											<td  class="fieldName" nowrap>Rm Lot Id</td>
																											<td>
																												<select name='rmlotid' id='rmlotid'>
																													<option value=''>--SelectAll--</option>
																													<? foreach($rmLotRecords as $rmlotVal)
																														{ 
																															($rmlotid==$rmlotVal[0])? $selected="selected":$selected="";
																														?>
																															<option value='<?=$rmlotVal[0]?>' <?=$selected?>><?=$rmlotVal[1]?></option>
																														<?
																														}
																														?>
																												</select>
																											</td>
																										</tr>
																										
																										<tr>
																											<td>&nbsp;</td>
																											<td class="listing-item">
																											<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onClick="return validateRMVarianceReportSearch();"/>
																											</td>
																										</tr>
																										<tr>
																											<TD height="5"></TD>
																										</tr>
																									</table>
																								</fieldset>
																							</td>
																						</tr>
																					</table>
																				</TD>
																			</TR>
																		</table>				
																	</TD>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td colspan="3"  height="10" class="listing-item">&nbsp;</td>
													</tr>
													<tr> 
														<td colspan="3" class="listing-item" style="padding-left:10px; padding-right:10px;">
															<table border="0" cellpadding="2" cellspacing="0" align='center'>
																<tr bgcolor="#FFFFFF">
																	<td style="padding-left:10px; padding-right:10px;" >
																		<table cellpadding="1"   cellspacing="1" border="0" align="center" bgcolor="#999999">
																		<?
																		if( sizeof($freezingRecords) > 0 )
																		{
																		?>
																			<tr  bgcolor="#f2f2f2" align="center" >
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Sl no.</td>
																				<td class="listing-head" style="padding-left:5px; padding-right:5px; width:100px; height:40px"  nowrap>Date</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>RM Lot Id</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Fish</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Brand</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Packing</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Glaze</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Weight</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>Grade</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>MC</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px; width:100px; "  nowrap>LS</td>
																			</tr>
																			<?php
																			$totalCount	= 0;
																			$totalQty	= 0;
																			$i=0;
																			$oldFreezeDate=""; $oldfish=''; $oldbrand=''; $oldmcPack='';
																			foreach($freezingRecords as $fr)
																			{	$i++;
																				$freezId=$fr[0];
																				$freezDate=dateFormat($fr[1]);
																				$rmLotIdName=$fr[3];
																				$fish=$fr[5];
																				$brand=$fr[7];
																				$mcPack=$fr[9];
																				($oldFreezeDate!=$freezDate)?$frzDate=$freezDate:$frzDate="";
																				($oldfish!=$fish)?$fishName=$fish:$fishName="";
																				($oldbrand!=$brand)?$brandName=$brand:$brandName="";
																				($oldmcPack!=$mcPack)?$mcPackName=$mcPack:$mcPackName="";
																				$grade=$fr[11];
																				$mc=$fr[12];
																				$lc=$fr[13];
																				//echo $fr[15];
																				$wt=$fr[14];
																				($fr[15]!="0")?$glaze=$fr[15]:$glaze="";
																				

																			?>
																			<tr>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$i?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$frzDate?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$rmLotIdName?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><?=$fishName?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$brandName?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$mcPackName?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$glaze?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$wt?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$grade?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$mc?></td>
																				<td bgcolor="#fff" class="listing-item" style="padding-left:5px; padding-right:5px;" ><?=$lc?></td>
																				
																			</tr>
																			<? 
																				$oldFreezeDate=$freezDate;
																				$oldfish=$fish;
																				$oldbrand=$brand;
																				$oldmcPack=$mcPack;
																			}
																			?>


																	<?php
																		if ($dateRange && $details) $colspan = 10;
																		else $colspan = 10;
																	?>
																	<?php
																		} 
																		/*else if($searchMode!="") {
																	?>
																		<tr bgcolor="white">
																			<td colspan="11"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
																		</tr>	
																	<?
																		}*/
																	?>
																</table>
															</td>
														</tr>		
													</table>
												</td>
											</tr>
											<tr> 
												<td colspan="3" align="center" height="5"></td>
											</tr>
											<tr> 
												<? if($editMode){?>
												<?} else{?>
												<td colspan="4" align="center"><? if($print==true){?><input type="button" name="Submit" value=" View / Print " class="button" onClick="return printWindow('PrintFreezingReport.php?dateFrom=<?=$dateFrom?>&dateTo=<?=$dateTill?>&rmlotid=<?=$rmlotid?>',700,600);" <? if ($freezingRecordsSize==0 && $rmlotid=="") echo "disabled";?> ><? }?></td>
												<?} ?>
											</tr>
											<tr> 
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
							
						<!-- Form fields end   -->
						</td>
					</tr>		
				</table>
			</td>	
		</tr>
	</table>
</form>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplyFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplyTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>

<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>