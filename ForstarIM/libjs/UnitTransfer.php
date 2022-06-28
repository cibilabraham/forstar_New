<?
	require("include/include.php");
	require_once('lib/UnitTransfer_ajax.php');
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");
	$genReqNumber	= "";

	$selection = "?pageNo=".$p["pageNo"]."&selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
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
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	
	$requestNo		= $p["requestNo"];
	$selDepartment		= $p["selDepartment"];

	# For resetting the values from edit mode to add mode
	$hidEditId = "";
	if ($p["editId"]!="") {
		$hidEditId = $p["editId"];
	} else {
		$hidEditId = $p["hidEditId"];
	}

	if ($p["cmdAddNew"]!="" && $p["hidEditId"]!="") {
		$requestNo 	= "";
		$selDepartment  = "";
		//$hidEditId 	= "";
	}
	// end

	# Add RM Test Data Start 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;	
	}	
	
	

	#Add
	if ($p["cmdAdd"]!="" ) {
			
		$rmlotId		=	$p["rmlotId"];
		$supplierDetails		=	$p["supplierDetails"];
		$currentUnitName		=	$p["currentUnitName"];
		$currentProcessingStage		=	$p["currentProcessingStage"];
		$unitName		=	$p["unitName"];
		$processType		=	$p["processType"];
		$lotId		=	$p["lotId"];
		
		
		
		if ($rmlotId!="" ) {	
			$unitTransferRecIns	=	$unitTransferObj->addUnitTransfer($rmlotId, $supplierDetails, $currentUnitName, $currentProcessingStage, $unitName,$processType,$lotId ,$userId);
				
			

			if ($unitTransferRecIns) {
				
				$sessObj->createSession("displayMsg",$msg_succAddUnitTransfer);
				$sessObj->createSession("nextPage",$url_afterAddUnitTransfer.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddUnitTransfer;
			}
			$unitTransferRecIns		=	false;
		}	
	}
	

	# Edit Unit Transfer Data
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$unitTransferRec	=	$unitTransferObj->find($editId);		
		$editunitTransferDataId	=	$unitTransferRec[0];		
		$rmlotId		=	$unitTransferRec[1];		
		$supplierDetails	=	$unitTransferRec[2];
		$currentUnitName	=	$unitTransferRec[3];
		$currentProcessingStage	=	$unitTransferRec[4];
		$unitName	=	$unitTransferRec[5];
		$processType	=	$unitTransferRec[6];
		$lotId	=	$unitTransferRec[7];
		
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {		
		$unitTransferDataId	=	$p["hidunitTransferDataId"];		
		$rmlotId		=	$p["rmlotId"];
		$supplierDetails		=	$p["supplierDetails"];
		$currentUnitName		=	$p["currentUnitName"];
		$currentProcessingStage		=	$p["currentProcessingStage"];
		$unitName		=	$p["unitName"];
		$processType		=	$p["processType"];
		$lotId		=	$p["lotId"];
				
		

		if ($unitTransferDataId!="" && $rmlotId!="" && $supplierDetails!="" && $currentUnitName!="" && $currentProcessingStage!="" && $unitName!="" && $processType!="" && $lotId!="" ) {
			$unitTransferDataRecUptd	=	$unitTransferObj->updateUnitTransfer($unitTransferDataId, $rmlotId, $supplierDetails,$currentUnitName,$currentProcessingStage,$unitName,$processType,$lotId);
							
		}	
		if ($unitTransferDataRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUnitTransferUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateUnitTransfer.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUnitTransferUpdate;
		}
		$unitTransferDataRecUptd	=	false;		
	}
	
	# Delete unit transfer Data
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$unitTransferDataId	=	$p["delId_".$i];

			if ($unitTransferDataId!="" && $isAdmin!="") {

				$unitTransferDataRecDel =	$unitTransferObj->deleteUnitTransfer($unitTransferDataId);	
			}
		}
		if ($unitTransferDataRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelUnitTransfer);
			$sessObj->createSession("nextPage",$url_afterDelUnitTransfer.$selection);
		} else {
			$errDel	=	$msg_failDelUnitTransfer;
		}
		$unitTransferDataRecDel	=	false;
		
	}
	

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") {
		$pageNo=$p["pageNo"];
	} else if ($g["pageNo"] != "") {
		$pageNo=$g["pageNo"];
	} else {
		$pageNo=1;
	}
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}
	
	#List all Rm Test Data
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$unitTransferDataRecords	= $unitTransferObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$unitTransferDataSize	= sizeof($unitTransferDataRecords);
		$fetchAllunitTransferDataRecs = $unitTransferObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	//$stockissuanceObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllunitTransferDataRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	
	
	# List all records
	//$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
	$rmLotId	= $unitTransferObj->fetchAllRecords();
	$unitRecords	= $plantandunitObj->fetchAllRecordsPlantsActive();
	$processTypes	= $rmReceiptGatePassObj->fetchAllProcessType();
	
	
	if ($editMode) $heading	=	$label_editUnitTransfer;
	else $heading	=	$label_addUnitTransfer;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/UnitTransfer.js"; // For Printing JS in Head section

	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmUnitTransfer" action="UnitTransfer.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if ($editMode || $addMode) {
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('UnitTransfer.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateUnitTransfer(document.frmUnitTransfer);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('UnitTransfer.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateUnitTransfer(document.frmUnitTransfer);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidunitTransferDataId" value="<?=$editunitTransferDataId;?>">
											
										
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
			<table width="200" align="center">
					
					
							<tr>
								   <td class="fieldName" nowrap>*RM Lot Id:</td>
								 
												<td  height="10" ><select name="rmlotId" id="rmlotId" onchange="xajax_lotDetails(document.getElementById('rmlotId').value,'');">
											  <option value="">--select--</option>
											  <?
												foreach($rmLotId as $un)
													{
														$lot		=	$un[0];
														$lotName	=	stripSlash($un[7]);
															$selected = ($rmlotId==$lot)?"selected":""
														
											?>
											  <option value="<?=$lot?>" <?=$selected?>><?=$lotName?></option>
											  <? }?>
										        </select>										      </td>
								</tr>
							
							<tr>
							   <td class="fieldName" nowrap>*Supplier detail(Challan Number):</td>
							   <td><INPUT TYPE="text" NAME="supplierDetails" id="supplierDetails" size="15" value="<?=$supplierDetails?>"></td>
							   
							</tr>
					
							
                                <tr>
                                	<td class="fieldName" nowrap>*Current Unit Name:&nbsp;</td>
									
                                       <td  height="5" ><select name="currentUnitName" id="currentUnitName" >
											  <option value="">--select--</option>
											  <?
												foreach($unitRecords as $rm)
													{
														$cUnitId		=	$rm[0];
														$cUnit	=	stripSlash($rm[2]);
															$selected = ($currentUnitName==$cUnitId)?"selected":""
														
											?>
											  <option value="<?=$cUnitId?>" <?=$selected?>><?=$cUnit?></option>
											  <? }?>
										        </select>										      </td>
                                                </tr>
												
												
						  <tr>
                                	<td class="fieldName" nowrap>*Current Processing Stage:&nbsp;</td>
                                     
										<td  height="10" ><select name="currentProcessingStage" id="currentProcessingStage" >
											  <option value="">--select--</option>
											  <?
												foreach($processTypes as $rm)
													{
														 $cprocessTypeId		=	$rm[0];
														 $cprocessTypeName	=	stripSlash($rm[1]);
														$selected = ($currentProcessingStage==$cprocessTypeId)?"selected":""
														
											?>
											  <option value="<?=$cprocessTypeId?>" <?=$selected?>><?=$cprocessTypeName?></option>
											  <? }?>
										        </select>										      </td>
							   </tr>
							  <tr>
                                	<td class="fieldName" align='right'>*Transfer to unit Name:&nbsp;</td>
                                       <td  height="10" ><select name="unitName" id="unitName">
											  <option value="">--select--</option>
											  <?
												foreach($unitRecords as $rm)
													{
														$unitId		=	$rm[0];
														$unit	=	stripSlash($rm[2]);
															$selected = ($unitName==$unitId)?"selected":""
														
											?>
											  <option value="<?=$unitId?>" <?=$selected?>><?=$unit?></option>
											  <? }?>
										        </select>										      </td>
                                                </tr>
												
							 <tr>
                                	<td class="fieldName" align='right'>*Transfer to Processing stage:&nbsp;</td>
                                        <td  height="10" ><select name="processType" id="processType" onchange="xajax_getLotId('<?=$selDate?>',document.getElementById('processType').value);">
											  <option value="">--select--</option>
											  <?
												foreach($processTypes as $rm)
													{
														echo $processTypeId		=	$rm[0];
														echo $processTypeName	=	stripSlash($rm[1]);
														$selected = ($processType==$processTypeId)?"selected":""
														
											?>
											  <option value="<?=$processTypeId?>" <?=$selected?>><?=$processTypeName?></option>
											  <? }?>
										        </select>										      </td>
                                                </tr>
												
							<tr>
								   <td class="fieldName" nowrap>*RM Lot ID Generate:</td>
								 
												 <td><INPUT TYPE="text" NAME="lotId" id="lotId" size="15" value="<?=$lotId?>"></td>
								<td nowrap><div id="divlotIdExistTxt" style='line-height:normal; font-size:10px; color:red;'></div></td>
								</tr>
                                              </table></td>
					  </tr>
					<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>					
	

	
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
	<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('UnitTransfer.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateUnitTransfer(document.frmUnitTransfer);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('UnitTransfer.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateUnitTransfer(document.frmUnitTransfer);">												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Unit Transfer  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<table cellpadding="0" cellspacing="0">
									  <tr>
					<td nowrap="nowrap">
					<table cellpadding="0" cellspacing="0">
                      			<tr>
					  	<td class="listing-item"> From:</td>
                                    		<td nowrap="nowrap"> 
                            		<? 
					if ($dateFrom=="") $dateFrom=date("d/m/Y");
					?>
                            <input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
					    <td class="listing-item">&nbsp;</td>
				            <td class="listing-item"> Till:</td>
                                    <td> 
                                      <? 
					   if($dateTill=="") $dateTill=date("d/m/Y");
				      ?>
                                      <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
					   <td class="listing-item">&nbsp;</td>
					        <td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$unitTransferDataSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintUnitTransfer.php',700,600);"><? }?></td>
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
									<td colspan="2" >
										<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
												if( sizeof($unitTransferDataRecords) > 0 )
												{
													$i	=	0;
											?>
<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="6" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"UnitTransfer.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"UnitTransfer.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"UnitTransfer.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<tr  bgcolor="#f2f2f2" >
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">RM LOT ID</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Supply Details</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Current Unit name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Current Processing Stage</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Transfer to Unit Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Transfer to Processing Stage</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">New RM LOT ID</td>
		<td class="listing-head"></td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($unitTransferDataRecords as $sir) {
		$i++;
		$unitTransferDataId	=	$sir[0];
		//$lotRec		=	$rmTestDataObj->findLot($sir[1]);
		$lotRec		=	$unitTransferObj->findLot($sir[1]);
		$rmlotId		=	$lotRec[1];
		//echo $unit		=	$sir[2];
		//$supplierRec		=	$rmReceiptGatePassObj->find($sir[2]);
		//$supplierDetails		=	$unitRec[14];
		$supplierDetails		=	$sir[2];
		//$rmLotId		=	$sir[2];
		$unitRec		=	$plantandunitObj->find($sir[3]);
		$currentUnitName		=	$unitRec[2];
		//$currentUnitName		=	$sir[3];
		$type		=	$rmReceiptGatePassObj->findProcessType($sir[4]);
		$currentProcessingStage		=	$type[1];
		//$currentProcessingStage		=	$sir[4];
		//$rmTestName		=	$sir[3];
		
		$newUnitRec		=	$plantandunitObj->find($sir[5]);
		$unitName		=	$newUnitRec[2];
		$newProcess		=	$rmReceiptGatePassObj->findProcessType($sir[6]);
		$processType		=	$newProcess[1];
		//$newLotRec		=	$unitTransferObj->findLot($sir[7]);
		$lotId		=	$sir[7];
		
		
		
	?>
	<tr  bgcolor="WHITE">
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$unitTransferDataId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rmlotId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierDetails;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$currentUnitName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$currentProcessingStage;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unitName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$processType;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$lotId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<a href="javascript:printWindow('ViewUnitTransfer.php?unitTransferDataId=<?=$unitTransferDataId?>',700,600)" class="link1" title="Click here to view details.">View Details</a>
		</td>
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$unitTransferDataId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='UnitTransfer.php';"></td>
	<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="6" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"UnitTransfer.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"UnitTransfer.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"UnitTransfer.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$unitTransferDataSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintUnitTransfer.php',700,600);"><? }?></td>
												
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
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
	
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateOfTesting",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateOfTesting", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>