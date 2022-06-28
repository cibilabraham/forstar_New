<?php
	require("include/include.php");
	require_once('lib/ExporterMaster_ajax.php');
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	
	$selection 		=	"?pageNo=".$p["pageNo"];
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirmF=false;
	$printMode=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirmF=true;	
	//echo "The value of confirm is $confirmF";
	//----------------------------------------------------------
	
	//if ($g["print"]=="y")
	//{
	//	$printMode=true;
	//}
	
	# Add loading port
	if( $p["cmdAddNew"]!="" ){
		$addMode		=	true;
	}
	if ($p["cmdAdd"]!="") {
		
		$name	=	addSlash(trim($p["name"]));
	/*	$address		=	addSlash(trim($p["address"]));
		$place		=	addSlash(trim($p["place"]));
		$pinCode		=	trim($p["pinCode"]);
		$country		=	addSlash(trim($p["country"]));
		$telNo		=	$p["telNo"];
		$faxNo		=	$p["faxNo"];
		$alphaCode		=	addSlash(trim($p["alphaCode"]));*/

		$displayName		=	addSlash(trim($p["displayName"]));
		$iecCode		=	addSlash(trim($p["iecCode"]));
		$hidTableRowCount  =  $p['hidTableRowCount'];
		$createdBY=$userId;
		$chkEntryExist=false;
		
		if ($name!="" && $createdBY!="") {
			
		$chkEntryExist = $exporterMasterObj->chkEntryExist($name);
		//print_r($chkEntryExist);
			if(!$chkEntryExist)
				{
				
					//$exporterRecIns	= $exporterMasterObj->addExporter($name,$address,$place,$pinCode,$country,$telNo,$faxNo,$alphaCode,$displayName,$userId);
					$exporterRecIns	= $exporterMasterObj->addExporter($name,$displayName,$iecCode,$userId);
					if ($exporterRecIns) {
					$exporter_id = $databaseConnect->getLastInsertedId();
					}
					for($i=0; $i<$hidTableRowCount; $i++)
					{
						$status = $p["status_".$i];
						if ($status!='N') {
						$monitoringParamId = $p["monitoringParamId_".$i];
						$headName	= addSlash(trim($p["headName_".$i]));
						$exporterRecUnitIns	= $exporterMasterObj->addExporterUnit($exporter_id,$monitoringParamId,$headName);	

						}	
							
					}
					//hidTableRowCount
				}
			}
			
			if ($exporterRecUnitIns) {
				$sessObj->createSession("displayMsg",$msg_succAddExporterMaster);
				$sessObj->createSession("nextPage",$url_afterAddExporterMaster.$selection);
			} else {
				$addMode	=	true;
				if($chkEntryExist)
						{
							$err=$msg_AddExporterMasterExists;
						}
				else
						{
							$err		=	$msg_failExporterMasterUpdate;
						}
				
				//$err		=	$  $msg_succAddLoadingPort;
			}
			$exporterMasterRecIns		=	false;
		}

	
	
	# Edit loading port
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$exportingRec = $exporterMasterObj->find($editId);
		//print_r($loadingPortRec);
		$exportingId =	$exportingRec[0];
		$companyId=$exportingRec[1];
		$companyRecs 			= $billingCompanyObj->find($companyId);
		$companyContactDetailsRecs 			= $billingCompanyObj->findContactdetail($companyId);
		$name =	stripSlash($companyRecs[1]);
		$address =	stripSlash($companyRecs[2]);
		$place =	stripSlash($companyRecs[3]);
		$pinCode =	stripSlash($companyRecs[4]);
		$country =	stripSlash($companyRecs[5]);	
		if(sizeof($companyContactDetailsRecs)>0)
		{
			$telephoneNo=''; $fax='';
			foreach($companyContactDetailsRecs as $cdt)
			{
				if($cdt[1]!='')
				{
					if($telephoneNo=='')
					{
						$telephoneNo=$cdt[1];
					}
					else
					{
						$telephoneNo.=' , '.$cdt[1];
					}
				}
				if($cdt[3]!='')
				{
					if($fax=='')
					{
						$fax=$cdt[3];
					}
					else
					{
						$fax.=' , '.$cdt[3];
					}
				}
			}
		}
		$telNo =	$telephoneNo;
		$faxNo =	$fax;

		/*$telNo =	$companyRecs[6];
		$faxNo =	$companyRecs[7];
		*/
		$alphaCode =	stripSlash($companyRecs[8]);
		$displayName=stripSlash($exportingRec[10]);
		$iecCode=stripSlash($exportingRec[11]);
		$exporterunit=$exporterMasterObj->findExporterUnit($editId);
	}

	
	if ($p["cmdSaveChange"]!="") {
	
		$exporterId		=	$p["hidExporterMasterId"];
		$name	=	addSlash(trim($p["name"]));
		/*$address		=	addSlash(trim($p["address"]));
		$place		=	addSlash(trim($p["place"]));
		$pinCode		=	trim($p["pinCode"]);
		$country		=	addSlash(trim($p["country"]));
		$telNo		=	$p["telNo"];
		$faxNo		=	$p["faxNo"];
		$alphaCode		=	addSlash(trim($p["alphaCode"]));*/
		$displayName		=	addSlash(trim($p["displayName"]));
		$iecCode		=	addSlash(trim($p["iecCode"]));
		$hidTableRowCount  =  $p['hidTableRowCount'];
		$chkEntryExist=false;
		if ($exporterId!="" && $name!="") {
			$chkEntryExist = $exporterMasterObj->chkEntryExist($name, $exporterId);
			if(!$chkEntryExist)
			{
				//$exporterRecUptd		=	$exporterMasterObj->updateExporterMaster($exporterId,$name,$address,$place,$pinCode,$country,$telNo,$faxNo,$alphaCode,$displayName);
				$exporterRecUptd		=	$exporterMasterObj->updateExporterMaster($exporterId,$name,$displayName,$iecCode);

				for ($g=0; $g<$hidTableRowCount; $g++) 
				{
				$status = $p["status_".$g];
				$monitoringParamEntryId  		= $p["monitoringParamEntryId_".$g];
			   
					if ($status!='N') {
					$monitoringParamId = $p["monitoringParamId_".$g];
					$headName	= addSlash(trim($p["headName_".$g]));
				
						if ($monitoringParamId!=""  && $headName!=""   && $monitoringParamEntryId!="") {
							
							$updateExporterEntryRec = $exporterMasterObj->updateExporterUnit($monitoringParamEntryId,$monitoringParamId,$headName);
									
						} 
						else if($monitoringParamId!=""  && $headName!=""  &&  $monitoringParamEntryId=="" ) {

							$detailsIns	=	$exporterMasterObj->addExporterUnit($exporterId,$monitoringParamId,$headName);
						}
										
				} // Status Checking End

				if ($status=='N' && $monitoringParamEntryId!="") {
				
							$updateExporterEntryRec = $exporterMasterObj->delExporterUnit($monitoringParamEntryId);
					}
				}
			}
		}
	
		if ($updateExporterEntryRec) {
			$sessObj->createSession("displayMsg",$msg_succExporterMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateExporterMaster.$selection);
		} else {
			$editMode	=	true;
			$editId=$exporterId;
			if($chkEntryExist)
			{
				$err=$msg_ExporterMasterUpdate;
			}
			else
			{
				$err		=	$msg_failExporterMasterUpdate;
			}
		}
		$ExporterMasterRecUptd	=	false;
	}
	
	
	if ($p["cmdConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		//print_r($rowCount);
		for ($i=1; $i<=$rowCount; $i++) {
			$exporterId	=	$p["confirmId"];


			if ($exporterId!="") {
				// Checking the selected port of loading is link with any other process
				$exporterMasterRecConfirm = $exporterMasterObj->updateconfirmExporterMaster($exporterId);
			}

		}
		if ($exporterMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmExporterMaster);
			$sessObj->createSession("nextPage",$url_afterConfirmExporterMaster.$selection);
		} else {
			$errConfirm	=	$  $msg_failConfirmExporterMaster;
		}
	}
	
	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$exporterId	=	$p["rlconfirmId"];

			if ($exporterId!="") {
				#Check any entries exist
				
					$exporterMasterRecConfirm = $exporterMasterObj->updaterlconfirmExporterMaster($exporterId);
					}
		}
		if ($exporterMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmExporterMaster);
			$sessObj->createSession("nextPage", $url_afterReleaseConfirmExporterMaster.$selection);
		} else {
			$errReleaseConfirm	= $msg_failRlConfirmExporterMaster;
		}
	}

# Delete port of loading
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		$recExists= false;
		for ($i=1; $i<=$rowCount; $i++) {
			$exporterId	=	$p["delId_".$i];
			if ($exporterId!="") {
				// Checking the selected port of loading is link with any other process
				$exporterMasterRecInUse = $exporterMasterObj->exporterMasterRecInUse($exporterId);
				if (!$exporterMasterRecInUse) {
					$exporterMasterRecInUsecDel = $exporterMasterObj->deleteExporterMaster($exporterId);
					$exporterMasterRecInUsecUnitDel = $exporterMasterObj->delExporterExporterUnit($exporterId);	
				}
				else
				{
					$recExists=true;
				}
			}
		}
		if ($exporterMasterRecInUsecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelExporterMaster);
			$sessObj->createSession("nextPage",  $url_afterDelExporterMaster.$selection);
			}
		else {
					if($recExists==true)
						{
							$errDel	=	$msg_delExporterMasterExists;
						}
						else
						{
							$errDel	=	$msg_failDelExporterMaster	;
						}
			}
		$ExporterMasterRecDel	=	false;

	}
	if ($p["cmdDefault"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$exporterId	=	$p["delId_".$i];

			if ($exporterId!="") {
				#Check any entries exist
				
					$exporterMasterRecDefault = $exporterMasterObj->updateExporterMasterDefaultRow($exporterId);
					}
		}
		if ($exporterMasterRecDefault) {
			$sessObj->createSession("displayMsg",$msg_succDefaultExporterMaster);
			$sessObj->createSession("nextPage", $url_afterDefaultExporterMaster.$selection);
		} else {
			$errReleaseExporter	= $msg_failDefaultExporterMaster;
		}
	}
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "")	$pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	#List All port of loading	
	$exporterMasterRecs	=	$exporterMasterObj->fetchAllPagingRecords($offset, $limit);
	$exporterMasterRecSize		=	sizeof($exporterMasterRecs);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($exporterMasterObj->fetchAllRecords());
	$maxpage	= ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
		
	
	if ($editMode) $heading =   $label_editExporterMaster;
	else $heading =   $label_addExporterMaster;

	$plantsRecords		= $plantandunitObj->fetchAllRecordsPlantsActive();
	$ON_LOAD_SAJAX = "Y";	
	$ON_LOAD_PRINT_JS	= "libjs/ExporterMaster.js";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");		
?>


<form name="frmExporterMaster" action="ExporterMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<?if($err){?>
		<tr>
			<td height="40" align="center" class="err1" ><?=$err ?></td>
		</tr>
		<? }?>
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td colspan='3'>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>							
						<?php	
						$bxHeader=$label_pageHeadingExporterMaster;
						include "template/boxTL.php";
						?>
						</td>
					</tr>
					<tr>
						<td colspan='3'>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">		<tr>
									<td colspan="3" align="center">
									<?
										if( $editMode || $addMode) {
										?>
										<table width="70%" align="center">		
											<tr >
												<td>
													<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
														<tr>
															<td colspan="3">			
																<?php			
																	$entryHead = $heading;
																	require("template/rbTop.php");
																?>
																	<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">		
																			<tr>
																				<td width="1" colspan='3'></td>
																				<td >
																					<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
																						<tr>
																							<td height="10"colspan="3" ></td>
																						</tr>
																						<tr>
																						<?			if($editMode)
																						{?>			
																							<td align="center" >
																							<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExporterMaster.php');">&nbsp;&nbsp;
																							<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateExporterMaster(document.frmExporterMaster);">	
																							
																							</td>		
																							<?} else if($addMode){?>	
																							<td align="center" >
																							<input  type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExporterMaster.php');">&nbsp;&nbsp;
																							<input  type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateExporterMaster(document.frmExporterMaster);">
																							</td>
																							<? }?>
																						</tr>
																						<input type="hidden" name="hidExporterMasterId" value="<?=$editId?>">
																						<? if($editMode)
																						{?>
																						<input type="hidden" name="data[Exporter][id]" value="" readonly>
																						<? }
																						?>
																						<tr>
																						  <td nowrap class="fieldName" colspan="3">											  </td>
																					  </tr>
																						<tr>
																						  <td colspan="3" height="5"></td>
																					  </tr>
																						<tr>
																							<td colspan="3" align="center" style="padding-left:10px; padding-right:10px;" id="divEntryExistTxt" class="err1"></td>
																						</tr>
																						<tr>
																							<td colspan="3" align="center" style="padding-left:10px; padding-right:10px;"> 
																									<table>
																										<tr>
																											<TD nowrap style="padding-left:5px; padding-right:5px;" valign="top" colspan="3">
		<table>
			<TR>
				<TD valign="top">
				<?php			
				$entryHead = "";
				require("template/rbTop.php");
				?>														
					<table>
						<tr>
							<td class="fieldName" nowrap >*Name</td>
							<?php
							$companyName=$billingCompanyObj->fetchAllRecordsActivebillingCompany();
				?>
							<td>
							<select name="name" id="name" onchange ="xajax_getCompanyDetails(document.getElementById('name').value);">
							<option value="">--select--</option>
     												
										<?php 
										foreach($companyName as $cr)
										{
										$companyIds		=	$cr[0];
										$companyNames	=	stripSlash($cr[9]);
										$selected="";
										if($companyId==$companyIds ) echo $selected="Selected";
									  ?>
										<option value="<?=$companyIds?>" <?=$selected?>><?=$companyNames?></option>
										<? }
										?>
							</select>
							</td>
						</tr>
						<tr>
							<td class="fieldName" nowrap >*Address</td>
							<td><textarea name="address" id="address" cols="27" rows="6" disabled><?=$address?></textarea>
							</td>
						</tr>
						<tr>
							<td class="fieldName" nowrap>*Place</td>
							<td><INPUT TYPE="text" name="place" id="place" size="30" value="<?=$place?>" readonly>
							</td>				
						</tr>
						<tr>
							<td class="fieldName" nowrap>*Pin Code</td>
							<td><input type="text" name="pinCode" id="pinCode" size="10" value="<?=$pinCode?>"  readonly/>
							</td>
						</tr>
					</table>
					<?php
						require("template/rbBottom.php");
					?>
				</TD>
				<td>&nbsp;</td>
				<td valign="top">
				<?php			
				$entryHead = "";
				require("template/rbTop.php");
				?>
				<table>
					<tr>
						<td class="fieldName" nowrap>*Country</td>
						<td><input type="text" name="country" id="country" size="10" value="<?=$country?>" readonly/>
						</td>
					</tr>
					<tr>
						<td class="fieldName" nowrap>*Tel.No</td>
						<td><INPUT TYPE="text" name="telNo" id="telNo" size="30" value="<?=$telNo?>" readonly>	</td>
					</tr>	
					<tr>
						<td nowrap></td>
						<td class="listing-item" style="line-height:normal;font-size:9px;">
						Eg:0471-2222222
						</td>
					</tr>		
					<tr>
						<td class="fieldName" nowrap>Fax No</td>
						<td><INPUT TYPE="text" name="faxNo" id="faxNo" size="30" value="<?=$faxNo?>" readonly>
						</td>
					</tr>
					<tr>
						<td class="fieldName" nowrap title="For display exporter code in invoice" >*Alpha Code</td>
						<td>
						<input type="text" name="alphaCode" id="alphaCode" size="10" value="<?=$alphaCode?>"  readonly/>
						<input type="hidden" name="hidAlphaCode" id="hidAlphaCode" size="5" value="" readonly/>
						</td>
					</tr>
					<tr>
						<td class="fieldName" nowrap>*Display Name</td>
						<td><input type="text" name="displayName" id="displayName" size="20" value="<?=$displayName?>" />
						</td>
					</tr>
					<tr>
						<td class="fieldName" nowrap>*IEC Code</td>
						<td><input type="text" name="iecCode" id="iecCode" size="20" value="<?=$iecCode?>" />
						</td>
					</tr>
				</table>
				<?php
					require("template/rbBottom.php");
				?>
			</td>
		</TR>
		</table>
	</TD>
	</tr>
		<tr>
			<TD style="padding-left:10px;padding-right:10px;" colspan="3" align="center">
				<table>
					<TR>
						<TD colspan='3'>
							<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblMonitorParam" class="newspaperType">
								<tr align="center">
									<th nowrap style="text-align:center; line-height:normal;">Unit No</th>	
									<th nowrap style="text-align:center;">*Code</th>	
									<th>&nbsp;</th>
								</tr>
								<? if (sizeof($exporterunit)>0)
									{$j=0;
										foreach($exporterunit as $expUnit)
										{ 
											$expId=$expUnit[0];
											$unitid=$expUnit[1];
											$unitcode=	$expUnit[2];
										?>
										
										<tr align="center" class="whiteRow" id="row_<?=$j?>">
											<td align="center" class="listing-item">
													<select id="monitoringParamId_<?=$j?>" 
													name="monitoringParamId_<?=$j?>">
													<?php if (sizeof($plantsRecords)>0)  {?>
														<?php foreach($plantsRecords as $plant) {
														$mParamId=$plant[0];
														$mParamName=$plant[2];
														$sel  = ($mParamId==$unitid)?"Selected":"";
														?>
														<option value='<?=$mParamId?>' <?=$sel?>><?=$mParamName?></option>
														<?php }?>	
													<?php }?>
													</select>
																	
												</td>
												<td align="center" class="listing-item">
													<input type="text" autocomplete="off" size="24" value="<?=$unitcode?>" id="headName_<?=$j?>" name="headName_<?=$j?>"  />
												</td>
												<td align="center" class="listing-item">
												<a onclick="setMParamItemStatus('<?=$j?>');" href="###">
												<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/>
												</a>
												<input type="hidden" value="" id="status_<?=$j?>" name="status_<?=$j?>"/>
												<input type="hidden" value="N" id="IsFromDB_<?=$j?>" name="IsFromDB_<?=$j?>"/>
												<input type="hidden" value="<?=$expId?>" id="monitoringParamEntryId_<?=$j?>" name="monitoringParamEntryId_<?=$j?>" readonly="true"/>
											<!--<input type="hidden" value="<?=$j?>" id="mParamSeqFlag_<?=$j?>" name="mParamSeqFlag_<?=$j?>" readonly="true"/>-->
											</td>
										</tr>
										<?php
									$j++;	}	
										

								
									}
								?>
							<!--	exporterunit
												
								{foreach:exporterUnitParamRecs,key,mpr}
								<tr align="center" class="whiteRow" id="row_{getRow():h}">
									<td align="center" class="listing-item">
										<select id="monitoringParamId_{key}" name="monitoringParamId_%s" flexy:nameuses="key">
										</select>
														
									</td>
									<td align="center" class="listing-item">
										<input type="text" autocomplete="off" size="24" value="{mpr.head_name}" id="headName_{key}" name="headName_%s"  />
									</td>
									<td align="center" class="listing-item">
									<a onclick="setMParamItemStatus('{getRow():h}');" href="###">
									<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item"/>
									</a>
									<input type="hidden" value="" id="status_{getRow():h}" name="status_{getRow():h}"/>
									<input type="hidden" value="N" id="IsFromDB_{getRow():h}" name="IsFromDB_{getRow():h}"/>
									<input type="hidden" value="{mpr.id}" id="monitoringParamEntryId_{getRow():h}" name="monitoringParamEntryId_{getRow():h}" readonly="true"/>
									<input type="hidden" value="{mpr.seq_flag}" id="mParamSeqFlag_{getRow():h}" name="mParamSeqFlag_{getRow():h}" readonly="true"/>
								</td>
							</tr>
							{incrementRow()}
							{end:}-->
						</table>
						<!--  Hidden Fields-->
						<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=sizeof($exporterunit);?>" readonly="true">
					</TD>
				</TR>
				<tr><TD height="5"></TD></tr>
				<tr>
					<TD>
						<a href="###" id='addRow' onclick="javascript:addNewMonitorParamItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New </a>
					</TD>
				</tr>
			</table>
		</TD>
	</tr>
</table>
</td>
</tr>
																						<tr>
																						  <td colspan="3" height="5"></td>
																					  </tr>
																						<tr>				<? if($editMode){?>			
																							<td align="center" >
																							<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExporterMaster.php');">&nbsp;&nbsp;
																							<input  type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateExporterMaster(document.frmExporterMaster);">	
																							</td>		<? } else if($addMode){?>						
																							<td align="center" >
																							<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ExporterMaster.php');">&nbsp;&nbsp;
																							<input  type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateExporterMaster(document.frmExporterMaster);">
																							</td>
																							<? } ?>
																						</tr>
																						<tr>
																							<td  height="10" colspan="3"></td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	<?php
																		require("template/rbBottom.php");
																	?>
																	</td>
																</tr>

															</table>
															<!-- Form fields end   -->
														</td>
													</tr>
													
												</table>

										<?php
										}
										?>
										
									</td>
								</tr>
							</table>
						</td>
					</tr>

<!----list row--->
					<tr>
						<td colspan="3">
							<table cellpadding="0" cellspacing="0" align="center">
								<? if(!$printMode){?>
								<tr >
									<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$exporterMasterRecSize?>);"><? } ?>&nbsp; <? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? } ?>&nbsp; <? if($print==true){?><input  type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintExporterMaster.php?print=y',700,600);"><? } ?>
									<? if($add==true || $edit==true){?>
									<input  type="submit" value=" Make Default " class="button"  name="cmdDefault" onClick="return confirmExpMakeDefault('delId_', '<?=$exporterMasterRecSize?>');" >
									<? } ?>
									
									</td>
								</tr>
								<?php
									}
								?>
							</table>
						</td>
					</tr>
					
					<tr>
						<td colspan="3" height="5" ></td>
					</tr>
					<?if($errDel){?>
					<tr>
						<td colspan="3" height="15" align="center" class="err1"><?=$errDel?></td>
					</tr>
					<? } ?>	

					<?if(!$addMode || !$editMode){?>
					<tr>
						<td colspan="3">
								<table cellpadding="1"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
									<?
										if( sizeof($exporterMasterRecs) > 0 )
											{
												$i	=	0;
									?>
									<thead>
												<? if($maxpage>1) { ?>
												<tr>
													<td colspan="10" style="padding-right:10px" class="navRow">
														<div align="right" class="navRow">
												  <?php 				 			  
												 $nav  = '';
										for($page=1; $page<=$maxpage; $page++)
											{
												if ($page==$pageNo)
												{
												$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
												}
												else
												{
										$nav.= " <a href=\"ExporterMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
												//echo $nav;
											}
										}
									if ($pageNo > 1)
										{
										$page  = $pageNo - 1;
										$prev  = " <a href=\"ExporterMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
										}
										else
										{
										$prev  = '&nbsp;'; // we're on page one, don't print previous link
										$first = '&nbsp;'; // nor the first page link
										}

									if ($pageNo < $maxpage)
										{
										$page = $pageNo + 1;
										$next = " <a href=\"ExporterMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
										}
										else
										{
										$next = '&nbsp;'; // we're on the last page, don't print next link
										$last = '&nbsp;'; // nor the last page link
										}
										// print the navigation link
										$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
										echo $first . $prev . $nav . $next . $last . $summary; 
									  ?>	
									  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
									  </div>
													</td>
												</tr>
												<?
													}
												?>
											
										<tr align="center">
											<th width="20" ><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="chkAll(this.form,'delId_'); " class="chkBox"></th>
											<th align="center" style="padding-left:10px; padding-right:10px;" >Alpha Code</th>
											<th align="center" style="padding-left:10px; padding-right:10px;" >Display Name</th>
											<th align="center" style="padding-left:10px; padding-right:10px;" >Name</th>
											<th style="padding-left:10px; padding-right:10px;">Address</th>
											<th style="padding-left:10px; padding-right:10px;">Tel. No.</th>
											
											<th style="padding-left:10px; padding-right:10px;">Default</th>
											
											<? if($edit==true  && !$printMode){?>
											<th width="45" >&nbsp;</th>
											<? }?>
											<? if($edit==true  && !$printMode){?>
											<th width="45">&nbsp;</th>
											<? }?>
										</tr>
									</thead>
									<tbody>
									<?
									foreach($exporterMasterRecs as $emR)
									{
											$i++;
											
											$Id = $emR[0];
											$companyID	=	stripSlash($emR[1]);
											$companyRecs 			= $billingCompanyObj->find($companyID);
											$name=$companyRecs[1];
											$address	=	$companyRecs[2];
											$place	=	$companyRecs[3];
											$pin	=	$companyRecs[4];
											$country	=	$companyRecs[5];
											//$telno	=	$companyRecs[6];
											$faxno	=	$companyRecs[7];
											$alphaCode	=	$companyRecs[8];
											$defaultRow	=	$emR[9];
											$displayName	=	$emR[10];
											$active	=	$emR[11];
											$contactDetails = $billingCompanyObj->findContactdetail($companyID);
											$unitCodesRecs=$exporterMasterObj->fetchAllUnitCodesdis($Id);

											$displayHtml = "";
											if (sizeof($unitCodesRecs)>0) {
												$displayHtml.= "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";		
												$displayHtml .= "<tr bgcolor=#fffbcc align=center>";
													$displayHtml .= "<td class=listing-head>Unit No</td>";
													$displayHtml .= "<td class=listing-head>Unit Code</td>";
													$displayHtml .= "<td class=listing-head>Alpha Code</td>";
												$displayHtml .= "</tr>";
												
												foreach($unitCodesRecs as $ucr) {
													$displayHtml .= "<tr bgcolor=#fffbcc>";
														$displayHtml .= "<td class=listing-item nowrap>";
														$displayHtml .= $ucr[20];
														$displayHtml .= "</td>";
														$displayHtml .= "<td class=listing-item nowrap>";
														$displayHtml .= $ucr[2];
														$displayHtml .= "</td>";
														$displayHtml .= "<td class=listing-item align=left>";
														$displayHtml .= $ucr[33];
														$displayHtml .=	"</td>";
													$displayHtml .= "</tr>";	
												}
												$displayHtml  .= "</table>";
											}
									?>
									<tr>
										<? if(!$printMode){?>
										<td width="20" align="center" >
											<input type="checkbox"  name="delId_<?=$i;?>" id="delId_<?=$i;?>"  value="<?=$Id;?>" class="chkBox">
										</td>
										<? } ?>
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$alphaCode?></td>
										<td class="listing-item" style="padding-left:10px; padding-right:10px;">
										<a href="###" onMouseover="ShowTip('<?=$displayHtml?>');" onMouseout="UnTip();" class="link5" >
										<?=$displayName?></a>
										
										</td>
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$name?></td>
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$address.','.$place.','.$pin.','.$country?></td>
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
										<?php
											foreach($contactDetails as $cdt)
											 {
												echo $cdt[1].'<br/>';
											 }
										?>
										<?/*=$telno*/?></td>		
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
										<? if($defaultRow=='Y') { ?><img src='images/y.png' /> <? } else {?> <img src='images/x.png' /> <? } ?>
										
										</td>
										
										<? if($edit==true  && !$printMode){?>
										  <td class="listing-item" width="45" align="center" flexy:if="!printMode">
										   <?php if ($active!=1) {?>
										  <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$Id?>,'editId');">
										  <? } ?>
											</td>
										<? } ?>
										<? if($active==false && !$printMode ) {?>
										 <td class="listing-item" width="45" align="center" ><input type="submit" value=" Confirm " name="cmdConfirm" onClick="assignValue(this.form,<?=$Id?>,'confirmId');">
											</td>
										<? } ?>
										<? if($active==true && !$printMode ) {?>
										 <td class="listing-item" width="45" align="center" ><input type="submit" value=" ReleaseConfirm " name="btnRlConfirm" onClick="assignValue(this.form,<?=$Id?>,'rlconfirmId');">
											</td>
										<? } ?>
									</tr>
									<? } ?>

									<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$exporterMasterRecSize?>" >
									<input type="hidden" name="editId" value="">
									<input type="hidden" name="confirmId" value="">
									<input type="hidden" name="rlconfirmId" value="">
									<? if($maxpage>1){?>
									<tr>
										<td colspan="10" style="padding-right:10px" class="navRow">
											<div align="right">
												  <?php 				 			  
												 $nav  = '';
										for($page=1; $page<=$maxpage; $page++)
											{
												if ($page==$pageNo)
												{
												$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
												}
												else
												{
										$nav.= " <a href=\"ExporterMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";
												//echo $nav;
											}
										}
									if ($pageNo > 1)
										{
										$page  = $pageNo - 1;
										$prev  = " <a href=\"ExporterMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
										}
										else
										{
										$prev  = '&nbsp;'; // we're on page one, don't print previous link
										$first = '&nbsp;'; // nor the first page link
										}

									if ($pageNo < $maxpage)
										{
										$page = $pageNo + 1;
										$next = " <a href=\"ExporterMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
										}
										else
										{
										$next = '&nbsp;'; // we're on the last page, don't print next link
										$last = '&nbsp;'; // nor the last page link
										}
										// print the navigation link
										$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
										echo $first . $prev . $nav . $next . $last . $summary; 
									  ?>
									  </div>
										</td>
									</tr>
										<? }?>
										<?
											}
											else
											{
										?>
									<tr><TD align="center"><?=$msgNoRecords;?></TD></tr>
										<?
											}
										?>
										</table>
						</td>
					</tr>

					<? }
					?>

					<tr>
						<td colspan="3">
							<table cellpadding="0" cellspacing="0" align="center">
								<? if(!$printMode){?>
								<tr >
									<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$exporterMasterRecSize?>);"><? } ?>&nbsp; <? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? } ?>&nbsp; <? if($print==true){?><input  type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintExporterMaster.php?print=y',700,600);"><? } ?>
									<? if($add==true || $edit==true){?>
									&nbsp;
									<input  type="submit" value=" Make Default " class="button"  name="cmdDefault" onClick="return confirmExpMakeDefault('delId_', <?=$exporterMasterRecSize?>);" >
									<? } ?>
									</td>
								</tr>
								<?php
									}
								?>
							</table>
						</td>
					</tr>
				<tr><td height="10" colspan="3"></td></tr>	
		</table>
		</td></tr>
		
		</table>
		</td></tr>
		<tr><td height="10" colspan="3"></td></tr>	
		</table>
</form>

<script language="JavaScript" type="text/javascript">
	<?php
	if (sizeof($exporterunit)>0) 
	{
	?>
		fieldId = <?=sizeof($exporterunit)?>;
	<?php
	}
	?>
	function addNewMonitorParamItem()
	{	
		addNewMonitorParam('tblMonitorParam','','');		
	}
</script>	
			

<? if ($addMode) {?>
<SCRIPT LANGUAGE="JavaScript">

window.onLoad = addNewMonitorParamItem();

</SCRIPT>
<? }?>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>




	


