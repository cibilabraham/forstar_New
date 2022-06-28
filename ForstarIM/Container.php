<?php
	require("include/include.php");
	require_once("lib/container_ajax.php");

	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$isSearched		=	false;
	
	$selection = "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];
	
//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$reEdit = false; 
	
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
	if($accesscontrolObj->canReEdit()) $reEdit=true;
//----------------------------------------------------------	
	
	
	#New Entry
	$containerId = $p["containerId"];

	# Add New	
	if ($p["cmdAddNew"]!="") {		
		$addMode	= true;
		$containerGenDetail 	= $containerObj->getNextProformaInvoiceNo();	
		$containerId =$containerGenDetail[0];
		$containerAlpha =$containerGenDetail[2];
		$containerNumGen =$containerGenDetail[1];	
	}
	
	#Cancel 	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;
		$editMode	=	false;
		$mainId 	=	$p["mainId"];
		$entryId	=	$p["entryId"];
		$mainId 		= "";
		$p["mainId"]	= "";
		$entryId		= "";
		$p["entryId"] 	= "";
	}

	# Add
	if( $p["cmdAdd"]!="" || $p["cmdAddSameContainer"]!="" || $p["cmdAddNewContainer"]!=""){
	
		$selectDate  	= mysqlDateFormat($p["selectDate"]);		
		$shippingLine	= $p["shippingLine"];
		$containerNo	= $p["containerNo"];
		$sealNo		    = $p["sealNo"];
		$vessalDetails	= $p["vessalDetails"];			
		$sailingDate	= mysqlDateFormat($p["sailingDate"]);
		$expectedDate	= mysqlDateFormat($p["expectedDate"]);
		$containerYear	= date("Y", strtotime($selectDate));
		$containerType	= $p["containerType"];
		$containerAlpha = $p["containerAlpha"];
		$containerId    = $p["containerId"];
		$containerNumgen= $p["containerNumgen"];
		$itemCount      = $p["hidTableRowCount"];
		
		if ($containerId!="") {
		
			$insertContainerMainRec = $containerObj->insertContainerMainRec($containerId, $selectDate, $shippingLine, $containerNo, $sealNo, $vessalDetails, $sailingDate, $expectedDate, $containerYear, $containerType, $userId,$containerAlpha,$containerNumgen);
			
			if ($insertContainerMainRec) {
				# Container Id
				$selContainerId	= $databaseConnect->getLastInsertedId();

				for ($i=0; $i<$itemCount; $i++) {
						$status = $p["status_".$i];
						if ($status!='N') {
							$selPO	= $p["selInvoiceId_".$i];						

							if ($selContainerId && $selPO) {
								$insContainerPO = $containerObj->insertContainerPO($selContainerId, $selPO);	
							}
						} // Status Check Ends here
					} // For Loop Ends	
			}
		
			if ($insertContainerMainRec) {				
				 $sessObj->createSession("displayMsg",$msg_succAddContainer);
				
				 if ($p["cmdAddNewContainer"]!="") {
					$addMode=true;
					$p["mainId"] 	= "";
					$p["entryId"] 	= "";
					$mainId = "";
					$entryId = "";
					$p["selectDate"]	= "";
					$selDate		=  "";					
					$containerId		= "";					
					$shippingLine		= "";
					$p["shippingLine"] 	= "";
					$containerNo		= "";
					$p["containerNo"] 	= "";
					$sealNo			= "";
					$p["sealNo"] 		= "";
					$vessalDetails		= "";
					$p["vessalDetails"] 	= "";
					$p["sailingDate"] 	= "";
					$sailingDate		= "";
					$p["expectedDate"] 	= "";
					$expectedDate		= "";
					$selPOId 		= "";
					$p["selPOId"]		= "";
				} else {
					$addMode		=	false;
					$sessObj->createSession("nextPage",$url_afterAddContainer);
				}
			} else {
				$addMode		=	true;
				$err			=	$msg_failAddContainer;
			}
		}
	}
	
	# Edit Container
	if ($p["editId"]!="") {	
		$editId			= $p["editId"];
		
		$editMode		= true;
		$containerRec		= $containerObj->find($editId);
		
		$mainId			= $containerRec[0];			
		
		$selDate		=	dateFormat($containerRec[8]);
		
		$containerId	=	$containerRec[1];
		
					
		$shippingLine	=	$containerRec[2];
		$containerNo	=	$containerRec[3];
		$sealNo			=	$containerRec[4];
		$vessalDetails	=	$containerRec[5];		
		$sailingDate		= ($containerRec[6]!="0000-00-00")?dateFormat($containerRec[6]):"";		
		$expectedDate		= ($containerRec[7]!="0000-00-00")?dateFormat($containerRec[7]):"";

		$containerType		= $containerRec[9];
		$containerAlpha		= $containerRec[10];
		$containerNumGen    = $containerRec[11];
		
		# Get Selected invoices
		$selInvoiceRecs	 = $containerObj->getSelPORecs($mainId);
	}

	# Update
	if ($p["cmdSaveChange"]!="" || $p["cmdSaveAndConfirm"]) {		
		
		$containerConfirmed = 'N';
		$confirmedUser = "";	
		if ($p["cmdSaveAndConfirm"]!="") {
			$containerConfirmed = 'Y';
			$confirmedUser = $userId;
		}

		$mainId 	=	$p["mainId"];
		
		$selectDate	= mysqlDateFormat($p["selectDate"]);		
		$shippingLine	= $p["shippingLine"];
		$containerNo	= $p["containerNo"];
		$sealNo		= $p["sealNo"];
		$vessalDetails	= $p["vessalDetails"];			
		$sailingDate	= mysqlDateFormat($p["sailingDate"]);
		$expectedDate	= mysqlDateFormat($p["expectedDate"]);
		$containerYear	= date("Y", strtotime($selectDate));
		$containerType	= $p["containerType"];
		
		$itemCount = $p["hidTableRowCount"];
		
		
		if ($containerId!="" ) {
			$updateContainerMainRec = $containerObj->updateContainerMainRec($mainId, $containerId, $selectDate, $shippingLine, $containerNo, $sealNo, $vessalDetails, $sailingDate, $expectedDate, $containerYear, $containerConfirmed, $confirmedUser, $containerType);

			if ($updateContainerMainRec) {				

				for ($i=0; $i<$itemCount; $i++) {
					$status = $p["status_".$i];
					$poEntryId = $p["poEntryId_".$i];

					if ($status!='N') {
						$selPO	= $p["selInvoiceId_".$i];						

						if ($mainId && $selPO && $poEntryId=="") {
							$insContainerPO = $containerObj->insertContainerPO($mainId, $selPO);	
						} else if ($mainId && $selPO && $poEntryId!="") {
							$updatePOEntryRec = $containerObj->updateContainerEntry($selPO,$poEntryId);
						}	
					} // Status Check Ends here
					else if ($status=='N' && $poEntryId!="") {
						$delPOEntryRec = $containerObj->deleteContainerEntryRec($poEntryId);
					}
				} // For Loop Ends	
			} // Update Check ends here	
		}
	
		if ($updateContainerMainRec) {
			$sessObj->createSession("displayMsg",$msg_succUpdateContainer);
			$sessObj->createSession("nextPage",$url_afterUpdateContainer.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateContainer;
		}
		$updateContainerMainRec	=	false;
		$updateContainerEntryRec = false;
	}

	# Delete 
	if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		$containerAlreadyConfirmed = false;
		for ($i=1; $i<=$rowCount; $i++) {
			$containerMainId	= $p["delId_".$i];
			$containerStatus	= $p["hidContainerStatus_".$i];

			if ( $containerMainId!="" && $containerStatus!='Y') {								
				$containerEntryRecDel	=	$containerObj->deleteContainerIinvoiceEntries($containerMainId);
				$containerMainRecDel	=	$containerObj->deleteContainerMainRec($containerMainId);
			} else if ($containerMainId!="" && $containerStatus=='Y') {
				$containerAlreadyConfirmed = true;
			}
		}
		if ($containerMainRecDel || $containerEntryRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelContainer);
			$sessObj->createSession("nextPage",$url_afterDelContainer.$selection);
		} else {
			if ($containerAlreadyConfirmed) $errDel	= "Container you have selected is already confirmed.<br>".$msg_failDelContainer;
			else $errDel	=	$msg_failDelContainer;
		}

		$containerMainRecDel	=	false;
		$containerEntryRecDel	=	false;

	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------

	# select records between selected date
	if($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		$dateC	   =	explode("/", date("d/m/Y"));
		$dateFrom   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1],1,$dateC[2]));
		$dateTill   =  date("d/m/Y",mktime(0, 0, 0,$dateC[1], date('t'), $dateC[2]));	
	}

	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		
		$containerRecords = $containerObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);

		# Fetch All Recs
		$fetchAllRecords	= $containerObj->fetchAllRecords($fromDate, $tillDate);
		$containerRecordsize	= sizeof($containerRecords);
	}
	

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllRecords);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($addMode || $editMode) {

		#List All Invoice Recs
		$purchaseOrderRecords	= $containerObj->getInvoiceRecs();

		# Shipping Company Recs
		//$shippingCompanyRecs = $shippingCompanyMasterObj->fetchAllRecords();
		$shippingCompanyRecs = $shippingCompanyMasterObj->fetchAllRecordsActiveShippingCompany();
	}

	if ($editMode) $heading	= $label_editContainer;
	else $heading = $label_addContainer;	
	
	# Setting the mode
	$mode = "";
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;

	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	


	$ON_LOAD_PRINT_JS	= "libjs/container.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmContainer" action="Container.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="60%">
			<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td align="center" style="display:none;">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Container.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddContainer(document.frmContainer,'');">												</td>
												
												<?} else{?>

												
												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Container.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Save &amp; Exit " onClick="return validateAddContainer(document.frmContainer,'');">&nbsp;&nbsp;<input name="cmdAddNewContainer" id="cmdAddNewContainer" type="submit" class="button" id="cmdAddNewContainer" style="width:180px;" onclick="return validateAddContainer(document.frmContainer,'');" value="save &amp; Add New Container">												</td>
<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<input type="hidden" name="hidDailyFrozenPackingId" value="<?=$dailyFrozenPackingId;?>">
											<tr>
											  <td nowrap class="fieldName">											  </td>
										  </tr>
											

											<tr>
											  <td colspan="2" height="10"></td>
										  </tr>
											<!--<tr>
											  <td colspan="2" style="padding-left:60px; padding-right:60px;" align="right"><input name="cmdAddSameContainer" type="submit" class="button" id="cmdAddSameContainer" style="width:200px;" tabindex="32" onclick="return validateAddContainer(document.frmContainer,'');" value="Save &amp; Add New PO in Container" /></td>
										  </tr>-->
											<tr>
											  <td colspan="2" style="padding-left:60px;">&nbsp;</td>
										  </tr>
<!-- 	Display Error Message -->
		<tr><TD class="listing-item" style='line-height:normal; font-size:10px; color:red;' id="divNumExistTxt" nowrap="true" align="center" colspan="2"></TD></tr>
		<tr>
		  <td colspan="2" align="center" style="padding-left:10px;padding-right:10px;">
		  <table width="75%" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                  <td valign="top">
						<fieldset>
						<table width="200">
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">*Date</td>
                                                      <td>
													  <? 
													if($p["selectDate"]!="") $selDate	=	$p["selectDate"];
														if($selDate==""){
															$selDate	=	date("d/m/Y");
														}						
														?>
                      <input type="text" id="selectDate" name="selectDate" size="9" value="<?=$selDate?>"></td>
                                                    </tr>
                                                    
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">*Container Id </td>
                                                      <td><input type="text" name="containerAlpha" id="containerAlpha" size="2" value="<?=$containerAlpha?>" autocomplete="off" readonly="readonly">	
													<input type="text" name="containerId" id="containerId" size="6" value="<?=($containerId!=0)?$containerId:""?>" onkeyup="xajax_chkProformaNoExist(document.getElementById('containerId').value, '<?=$mode?>', '<?=$mainId?>', document.getElementById('selectDate').value);" autocomplete="off">	
													<input type="hidden" name="containerNumgen" id="containerNumgen" size="2" value="<?=$containerNumGen?>" autocomplete="off" readonly="readonly">
 
							</td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">*Shipping Line</td>
                                                      <td>
							  <!--<input name="shippingLine" type="text" id="shippingLine" value="<?=$shippingLine?>"></td>-->
							<select name="shippingLine" id="shippingLine">
								<option value="">--Select--</option>
								<?php
									foreach ($shippingCompanyRecs as $scr) {
										$shippinCompanyId = $scr[0];
										$cntryName	  = $scr[1];
										$selected = ($shippinCompanyId==$shippingLine)?"selected":"";
								?>
								<option value="<?=$shippinCompanyId?>" <?=$selected?>><?=$cntryName?></option>
								<?php
									}
								?>
							</select>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Container No.</td>
                                                      <td><? if($p["containerNo"]!="") $containerNo=$p["containerNo"];?>
                                                      <input name="containerNo" type="text" id="containerNo" value="<?=$containerNo?>" size="24"></td>
                                                    </tr>
						<tr>
                                                      <td class="fieldName" nowrap="nowrap">Container Type</td>
                                                      <td>
                                                      <input name="containerType" type="text" id="containerType" value="<?=$containerType?>" size="24"></td>
                                                </tr>	
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Seal No.</td>
                                                      <td>
							  <? if($p["sealNo"]!="") $sealNo=$p["sealNo"];?>
							  <input name="sealNo" type="text" id="sealNo" size="24" value="<?=$sealNo?>">
							</td>
                                                    </tr>
						<tr>
                                                      <td class="fieldName" nowrap="nowrap">Vessel Details</td>
                                                      <td>
							  <? if($p["vessalDetails"]!="") $vessalDetails=$p["vessalDetails"];?>
							  <textarea name="vessalDetails" rows="2" id="vessalDetails"><?=$vessalDetails?></textarea></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Sailing On</td>
                                                      <td>
								<? if($p["sailingDate"]!="") $sailingDate=$p["sailingDate"];?>
								 <input name="sailingDate" type="text" id="sailingDate" size="9" value="<?=$sailingDate?>"></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap" style="line-height:normal">Expted Date of Arrival<div style="line-height:normal" align="center">(at Destination)</div></td>
                                                      <td nowrap="nowrap">
							<? if($p["expectedDate"]!="") $expectedDate=$p["expectedDate"];?>
							<input name="expectedDate" type="text" id="expectedDate" size="9" value="<?=$expectedDate?>">
							</td>
                                                    </tr>	
                                                  </table>
				</fieldset>
				</td>
                                                  <td valign="top">
			<fieldset>
			<legend class="listing-item">Link to Invoice</legend>
			<table width="200">
			<tr><TD>
				<table>
		<tr>
	<TD style="padding-left:10px;padding-right:10px;">
		<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblPOItem">
	                <tr bgcolor="#f2f2f2" align="center">
                                <td class="listing-head" nowrap="true">Sr.No</td> 
				<td class="listing-head" nowrap="true">Invoice No</td>				
				<td>&nbsp;</td>
                        </tr>
		</table>	
	</TD>
</tr>
<!--  Hidden Fields-->
<input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize;?>">
<!--  Dynamic Row Ends Here-->
<tr><TD height="5"></TD></tr>
<tr>
	<TD style="padding-left:10px;padding-right:10px;">
		<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add Item</a>
	</TD>
</tr>
	</table>
			</TD></tr>			
                                                    <!--<tr>
                                                      <td class="fieldName" nowrap="nowrap">Vessel Details</td>
                                                      <td>
							  <? if($p["vessalDetails"]!="") $vessalDetails=$p["vessalDetails"];?>
							  <textarea name="vessalDetails" rows="2" id="vessalDetails"><?=$vessalDetails?></textarea></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Sailing On</td>
                                                      <td>
								<? if($p["sailingDate"]!="") $sailingDate=$p["sailingDate"];?>
								 <input name="sailingDate" type="text" id="sailingDate" size="9" value="<?=$sailingDate?>"></td>
                                                    </tr>
                                                    <tr>
                                                      <td class="fieldName" nowrap="nowrap">Expted Date of Arrival<div style="line-height:normal" align="center">(at Destination)</div></td>
                                                      <td nowrap="nowrap">
							<? if($p["expectedDate"]!="") $expectedDate=$p["expectedDate"];?>
							<input name="expectedDate" type="text" id="expectedDate" size="9" value="<?=$expectedDate?>">
							</td>
                                                    </tr>-->
                                                    <!--<tr>
                                                      <td class="fieldName" nowrap="nowrap">PO ID</td>
                                                      <td nowrap="nowrap" class="listing-item">
													 <? if($addMode){?>
													  <select name="selPOId" id="selPOId" onchange="this.form.submit();">
													  <? } else {?>
													<select name="selPOId" id="selPOId" onchange="this.form.editId.value=<?=$editId?>; this.form.submit();">							  
													  <? }?>
													  <option value="">-- Select --</option>
													  <?
														/*
													  foreach($purchaseOrderRecords as $por)
															{
															$purchaseOrderId	=	$por[0];
															$pOId				=	$por[1];
															$selected 	=	 "";
															if($selPOId == $purchaseOrderId) $selected = "Selected";
													*/
													?>
													<option value="<?=$purchaseOrderId?>" <?=$selected?>><?=$pOId?></option>
													<? //}?>
                                                      </select>                                                      </td>
                                                    </tr>-->
                                                  </table>
				</fieldset>
				</td>
                                                </tr>
                                              </table></td>
					  </tr>									
											<!--<tr>
											  <td colspan="2" align="center"><table cellpadding="1"  width="98%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($pORecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2" align="center">
												<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'gradeEntryId_'); " ></td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">Fish</td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">Process Code</td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">Grade</td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">Freezing Stage</td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">Brand</td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">Frozen Code&nbsp; </td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">MC Pkg</td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">No. MC</td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">Price/ Kg </td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">Value in USD</td>
												<td class="listing-head" style="padding-left:4px; padding-right:4px;">Value in INR</td>
												<? if($edit==true){?>
												<? }?>
											</tr>
											<?
												$totalValueInUSD 	= "";
												$totalValueInINR	=	"";
												$containerRMEntryId = "";
													foreach($pORecords as $por)
													{
														$i++;
														//$POMainId		=	$por[0];
														//$PORMEntryId 	= 	$por[6];
														//$POGradeEntryId =	$por[11];
														//echo "Main=$POMainId,RM=$PORMEntryId,Grade=$POGradeEntryId"."<br>";echo "GId=".
														
														$gradeEntryId = $por[11];
														
														$pOId			=	$por[1];
														$customer		=	$customerObj->findCustomer($por[2]);
														$fish	=	$fishmasterObj->findFishName($por[7]);
														$processCode = $processcodeObj->findProcessCode($por[8]);														
														$selGrade = $grademasterObj->findGradeCode($por[12]);
														$freezingStage = $freezingstageObj->findFreezingStageCode($por[13]);
														//$eUCode = $eucodeObj->findEUCode($por[9]);
														$brand = $brandObj->findBrandCode($por[10]);
														
														$frozenCode = $frozenpackingObj->findFrozenPackingCode($por[14]);
														$mCPackingCode = $mcpackingObj->findMCPackingCode($por[15]);
														$numMC			=	$por[16];
														$PricePerKg		=	$por[17];
														$valueInUSD		=	$por[18];
														$totalValueInUSD += $valueInUSD;
														$valueInINR		=	$por[19];
														$totalValueInINR	+= $valueInINR;	
														
														$containerRMEntryId	=	$por[23];
														$checked = "";
														if($containerRMEntryId!="")	$checked="Checked";								
													?>
											<tr  bgcolor="WHITE"  >
												<td width="20" height="25"><input type="checkbox" name="gradeEntryId_<?=$i;?>" id="gradeEntryId_<?=$i;?>" value="<?=$gradeEntryId;?>" <?=$checked?>></td>
												<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$fish?></td>
												<td class="listing-item" nowrap style="padding-left:5px;"><?=$processCode?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selGrade?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$freezingStage?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$brand?></td>
												<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$frozenCode;?></td>
												<td class="listing-item" nowrap style="padding-left:5px;"><?=$mCPackingCode?></td>
												<td class="listing-item" align="right" style="padding-right:5px;"><?=$numMC?></td>
												<td class="listing-item" align="right" style="padding-right:5px;"><?=$PricePerKg?></td>
												<td class="listing-item" align="right" style="padding-right:5px;"><?=$valueInUSD?></td>
												<td class="listing-item" align="right" style="padding-right:5px;"><?=$valueInINR?></td>												
											</tr>
											<?
													}
											?>
												
											<input type="hidden" name="hidRowRMCount"	id="hidRowRMCount" value="<?=$i?>" >										
											
											<tr bgcolor="white">
											  <td height="10" colspan="10" align="right" class="listing-head">Total :</td>
										      <td  height="10" align="center" class="listing-item" style="padding-right:5px; padding-left:5px;"><strong><? echo number_format($totalValueInUSD,2);?></strong></td>
										      <td class="listing-item" height="10" style="padding-right:5px; padding-left:5px;"><strong><? echo number_format($totalValueInINR,2);?></strong></td>
										      </tr>
										  <?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="12"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table></td>
										  </tr>-->
											<tr>
											  <td align="center">&nbsp;</td>
											  <td align="center">&nbsp;</td>
										  </tr>
											<tr>
												<? if($editMode){?>

												<td align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Container.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateAddContainer(document.frmContainer,'');">
													&nbsp;&nbsp;
													<input type="submit" name="cmdSaveAndConfirm" id="cmdSaveAndConfirm" class="button" value=" Save & Confirm " onClick="return validateAddContainer(document.frmContainer,1);">	
												</td>
												<? } else{?>

												<td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('Container.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Save &amp; Exit " onClick="return validateAddContainer(document.frmContainer,'');">&nbsp;&nbsp;<input name="cmdAddNewContainer" id="cmdAddNewContainer1" type="submit" class="button" id="cmdAddNewContainer" style="width:180px;" onclick="return validateAddContainer(document.frmContainer,'');" value="save &amp; Add New Container">												</td>

												<? }?>
											</tr>
											<tr>
												<td  height="10" ></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		<?
			}
			
			# Listing Grade Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Container </td>
								    <td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<table cellpadding="0" cellspacing="0">
									  <tr><td nowrap="nowrap">
									
									<table cellpadding="0" cellspacing="0">
                      <tr> 
					  	<td class="listing-item">From:</td>
                                    <td nowrap="nowrap"> 
                            				<? 
							if($dateFrom=="") $dateFrom=date("d/m/Y");
							?>
                            <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
						            <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till:</td>
                                    <td> 
                                      	<?php
					     if($dateTill=="") $dateTill=date("d/m/Y");
				  	?>
                                      <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
							        <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search " onclick="return validateContainerSearch(document.frmContainer);"></td>
                            <td class="listing-item" nowrap >&nbsp;</td>
                          </tr>
                    </table></td></tr></table></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$containerRecordsize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintContainer.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="")
									{
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" style="padding-left:5px;padding-right:5px;">
	<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
		if( sizeof($containerRecords) > 0 ) {
			$i	=	0;
	?>
	<? if($maxpage>1){?>
                <tr  bgcolor="#f2f2f2" align="center">
                <td colspan="10" bgcolor="#FFFFFF" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"Container.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Container.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Container.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div></td>
       </tr>
	   <? }?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Container Id</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Shipping Line </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Container No </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Seal No </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Vessal Details </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Sailing On </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Expected Date<br> of Arrival</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Invoice Nos</td>
		<? if($edit==true){?>
			<td class="listing-head" width="45"></td>
		<? }?>
	</tr>
	<?php
		foreach($containerRecords as $cr) {
			$i++;
			$containerMainId	=	$cr[0];
			$containerId		=	$cr[1];
			$shippingLine		=	$shippingCompanyMasterObj->getShippingCompanyName($cr[2]);
			$containerNo		=	$cr[3];
			$sealNo			=	$cr[4];
			$vessalDetails		=	$cr[5];			
			$sailingOn	= ($cr[6]!="0000-00-00")?dateFormat($cr[6]):"";
			$expectedDate	= ($cr[7]!="0000-00-00")?dateFormat($cr[7]):"";
				
			# Invoice Nos
			$selInvoiceNos = $containerObj->getSelPORecsEdit($containerMainId);

			$containerConfirmed = $cr[9];
			$disabledField = ($containerConfirmed=='Y')?"disabled":"";
			if ($reEdit) $disabledField = "";
	?>
	<tr  bgcolor="WHITE"  >
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$containerMainId;?>"><input type="hidden" name="containerEntryId_<?=$i;?>" value="<?=$containerEntryId?>">
			<input type="hidden" name="hidContainerStatus_<?=$i;?>" id="hidContainerStatus_<?=$i;?>" value="<?=$containerConfirmed?>">
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$containerId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$shippingLine?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$containerNo?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$sealNo?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$vessalDetails;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$sailingOn?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$expectedDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<table>
				<tr>
				<td class="listing-item" ><?=$selInvoiceNos?></td>
				
				<?php
				/*	$numLine = 3;
					if (sizeof($selInvoiceNos)>0) {
						$nextRec = 0;						
						foreach ($selInvoiceNos as $cR) {
							$j++;
							$invNo = $cR[2];
							$nextRec++;
				?>
				<td class="listing-item" nowrap="true">
					<? if($nextRec>1) echo ",";?><?=$invNo?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<?php 
						}	
					 }
					}*/
				?>
				</tr>
			</table>
		</td>
		<? if($edit==true){?>
			  <td class="listing-item" width="45" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$containerMainId;?>,'editId'); assignValue(this.form,'1','editSelectionChange'); this.form.action='Container.php';" <?=$disabledField?>></td>
		  <? }?>
	</tr>
	<?php
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editContainerEntryId" value="<?=$containerEntryId;?>">
	<input type="hidden" name="editSelectionChange" value="0">
	<input type="hidden" name="editMode" value="<?=$editMode?>">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
         	<td colspan="10" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"Container.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"Container.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"Container.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>
	  </div><input type="hidden" name="pageNo" value="<?=$pageNo?>"></td>
       	 	        </tr>
			<? }?>
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
											</table>
											<input type="hidden" name="mainId" id="mainId" value="<?=$mainId?>"><input type="hidden" name="entryId" id="entryId" value="<?=$entryId?>">
								  </td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$containerRecordsize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintContainer.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>	
	</table>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectDate", 
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
			inputField  : "sailingDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "sailingDate", 
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
			inputField  : "expectedDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "expectedDate", 
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
			inputField  : "selectFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectFrom", 
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
			inputField  : "selectTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<script language="JavaScript" type="text/javascript">
		function addNewItem()
		{
			addNewPOItem('tblPOItem', '', '');	
		}
	</script>
	<?php 
		if ($addMode) {
	?>
		<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			window.load = addNewItem();
			//xajax_container();
			xajax_chkProformaNoExist(document.getElementById('containerId').value, '<?=$mode?>', '<?=$mainId?>', document.getElementById('selectDate').value);
		</SCRIPT>
	<?php 
		} else if ($editMode) {
	?>	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
			xajax_chkProformaNoExist(document.getElementById('containerId').value, '<?=$mode?>', '<?=$mainId?>', document.getElementById('selectDate').value);
		</SCRIPT>
	<?php
		}	
	?>
	<?php
		if (sizeof($selInvoiceRecs)>0) {
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<?
			foreach ($selInvoiceRecs as $sir) {
				$selInvEId 	= $sir[0];
				$selInvId	= $sir[1];
	?>
		addNewPOItem('tblPOItem', '<?=$selInvEId?>', '<?=$selInvId?>');
	<?php 
			} // Loop ends here
	?>
	</SCRIPT>
	<?php
		} else if ($editMode) {
	?>
	<script language="JavaScript" type="text/javascript">
		addNewPOItem('tblPOItem', '', '');
	</script>
	<?php
		}
	?>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>