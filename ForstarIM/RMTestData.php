<?
	require("include/include.php");
	require_once('lib/RMTestData_ajax.php');
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
		
		$selCompanyName=	$p["selCompanyName"];
		$unit		=	$p["unit"];
		$rmLotId		=	$p["rmLotId"];
		$rmTestName		=	$p["rmTestName"];
		$rmtestMethod		=	$p["rmtestMethod"];
		$dateOfTesting		=	mysqlDateFormat($p["dateOfTesting"]);
		$result		=	$p["result"];
		
		
		
		if ($unit!="" ) {	
			$rmTestDataRecIns	=	$rmTestDataObj->addRmTestData($selCompanyName,$unit, $rmLotId, $rmTestName, $rmtestMethod, $dateOfTesting,$result, $userId);
				
			

			if ($rmTestDataRecIns) {
				
				$sessObj->createSession("displayMsg",$msg_succAddRMTestData);
				$sessObj->createSession("nextPage",$url_afterAddRMTestData.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddRMTestData;
			}
			$rmTestDataRecIns		=	false;
		}	
	}
	

	# Edit RmTest Data
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$rmTestDataRec	=	$rmTestDataObj->find($editId);		
		$editrmTestDataId	=	$rmTestDataRec[0];	
		$selCompanyName=	$rmTestDataRec[1];
		$unit		=	$rmTestDataRec[2];		
		$rmLotId	=	$rmTestDataRec[3];
		$rmTestName	=	$rmTestDataRec[4];
		$rmtestMethod	=	$rmTestDataRec[5];
		$dateOfTesting	=	dateFormat($rmTestDataRec[6]);
		$result	=	$rmTestDataRec[7];
		$rmLotRecords	= $rmTestDataObj->fetchAllRecordsRMLotId($unit,$selCompanyName);
		
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {		
		$rmTestDataId	=	$p["hidRmTestDataId"];
		$selCompanyName=	$p["selCompanyName"];		
		$unit		=	$p["unit"];
		$rmLotId		=	$p["rmLotId"];
		$rmTestName		=	$p["rmTestName"];
		$rmtestMethod		=	$p["rmtestMethod"];
		$dateOfTesting		=	mysqlDateFormat($p["dateOfTesting"]);		
		$result = $p["result"];
				
		

		if ($rmTestDataId!="" && $selCompanyName!="" && $unit!="" && $rmLotId!="" && $rmTestName!="" && $rmtestMethod!="" && $dateOfTesting!="" && $result!="" ) {
			$rmTestDataRecUptd	=	$rmTestDataObj->updateStockIssuance($rmTestDataId,$selCompanyName, $unit, $rmLotId,$rmTestName,$rmtestMethod,$dateOfTesting,$result);
							
		}	
		if ($rmTestDataRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succRMTestDataUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateRMTestData.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failRMTestDataUpdate;
		}
		$rmTestDataRecUptd	=	false;		
	}
	
	# Delete RM Test Data
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$rmTestDataId	=	$p["delId_".$i];

			if ($rmTestDataId!="" && $isAdmin!="") {

				$rmTestDataRecDel =	$rmTestDataObj->deleteRmTestData($rmTestDataId);	
			}
		}
		if ($rmTestDataRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelRMTestData);
			$sessObj->createSession("nextPage",$url_afterDelRMTestData.$selection);
		} else {
			$errDel	=	$msg_failDelRMTestData;
		}
		$rmTestDataRecDel	=	false;
		
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

		$rmTestDataRecords	= $rmTestDataObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$rmTestDataSize	= sizeof($rmTestDataRecords);
		$fetchAllrmTestDataRecs = $rmTestDataObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	//$stockissuanceObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllrmTestDataRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Stocks
	//$stockRecords		= $stockObj->fetchAllActiveRecords();
	//$stockRecords		= $stockObj->fetchAllActiveRecordsConfirm();
	
	# List all Supplier
	//$supplierRecords	= $supplierMasterObj->fetchAllRecords();
	
	# List all records
	//$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
	$companyRecords	= $rmProcurmentOrderObj->fetchAllCompanyName();
	$unitRecords	= $plantandunitObj->fetchAllRecordsPlantsActive();
	
	$rmTestNameRecords	= $rmTestMasterObj->fetchAllRecordsActive();
	
	if ($editMode)
	
	$heading	=	$label_editRMTestData;
	
	
	else $heading	=	$label_addRMTestData;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/rmTestData.js"; // For Printing JS in Head section

	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmRMTestData" action="RMTestData.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="80%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMTestData.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateRMTestData(document.frmRMTestData);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMTestData.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateRMTestData(document.frmRMTestData);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidRmTestDataId" value="<?=$editrmTestDataId;?>">
											<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>
										
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
			<!--<table width="200" align="center">-->
					<table width="30%" border="0" cellpadding="4" cellspacing="0" align="left">
          <tr>
            <td width='30%' valign="top">
		<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
		<table align="left" cellpadding="0" cellspacing="0" width="100%">
					
							<tr>
								
								
								<td class="fieldName" align='right'>*Company Name:&nbsp;</td>
                                        
                                        <td class="listing-item">
					<select name="selCompanyName" id="selCompanyName">
                                        <option value="">--select--</option>
     												
										<?php 
										foreach($companyRecords as $cr)
										{
						$companyId		=	$cr[0];
						$companyName	=	stripSlash($cr[1]);
						$selected="";
						if($selCompanyName==$companyId ) echo $selected="Selected";
					  ?>
                                        <option value="<?=$companyId?>" <?=$selected?>><?=$companyName?></option>
                                                    <? }
										
										
										?>
                                                  </select></td>
							</tr>
							<tr>
							
								   <td class="fieldName" nowrap>*Unit Name:</td>
								 
												<td  height="10" ><select name="unit" id="unit" onchange="xajax_lotId(document.getElementById('unit').value,document.getElementById('selCompanyName').value,'');">
											  <option value="">--select--</option>
											  <?
												foreach($unitRecords as $un)
													{
														$unitId		=	$un[0];
														$unitName	=	stripSlash($un[2]);
															$selected = ($unit==$unitId)?"selected":""
														
											?>
											  <option value="<?=$unitId?>" <?=$selected?>><?=$unitName?></option>
											  <? }?>
										        </select>										    
												</td>
								</tr>
								<tr>
												
												
												<td class="fieldName" nowrap>*RM Lot Id:</td>
							   <td  height="10" ><select name="rmLotId" id="rmLotId">
											  <option value="">--select--</option>
											  <?
												foreach($rmLotRecords as $rm)
													{
														$lotId		=	$rm[0];
														$lotNumber	=	stripSlash($rm[1]);
															$selected = ($rmLotId==$lotId)?"selected":""
														
											?>
											  <option value="<?=$lotId?>" <?=$selected?>><?=$lotNumber?></option>
											  <? }?>
										        </select>										     
									</td>
								</tr>
							
							<!--<tr>
							   <td class="fieldName" nowrap>*RM Lot Id:</td>
							   <td  height="10" ><select name="rmLotId" id="rmLotId">
											  <option value="">--select--</option>
											  <?
												foreach($rmLotRecords as $rm)
													{
														$lotId		=	$rm[0];
														$lotNumber	=	stripSlash($rm[1]);
															$selected = ($rmLotId==$lotId)?"selected":""
														
											?>
											  <option value="<?=$lotId?>" <?=$selected?>><?=$lotNumber?></option>
											  <? }?>
										        </select>										      </td>
							</tr>-->
					</table>
		<?php
			require("template/rbBottom.php");
		?>
		</td>
		</tr>
		</table>
		
		<table width="40%" border="0" cellpadding="4" cellspacing="0" align="left">
          <tr>
            <td width='50%' valign="top">
		<?php
			$left_l=true;
			$entryHead = "";
			$rbTopWidth = "";
			require("template/rbTop.php");
		?>
		<table align="center" cellpadding="0" cellspacing="0" width="100%">
							
							 <tr>
                                	<td class="fieldName" nowrap>*Date of Testing:&nbsp;</td>
                                       <td><input type="text" name="dateOfTesting" id="dateOfTesting" size="9" value="<?=$dateOfTesting;?>" autocomplete="off" /></td>
									 </tr>
                                <tr>
                                	<td class="fieldName" nowrap>*RM Test Name:&nbsp;</td>
                                       <td  height="5" ><select name="rmTestName" id="rmTestName" onchange="xajax_testMethod(document.getElementById('rmTestName').value,'');">
											  <option value="">--select--</option>
											  <?
												foreach($rmTestNameRecords as $rm)
													{
														$testId		=	$rm[0];
														$testName	=	stripSlash($rm[1]);
															$selected = ($rmTestName==$testId)?"selected":""
														
											?>
											  <option value="<?=$testId?>" <?=$selected?>><?=$testName?></option>
											  <? }?>
										        </select>										     
												</td>
											</tr>
											<tr>
												<td class="fieldName" nowrap>*RM Test Method:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="rmtestMethod" id="rmtestMethod" size="15" value="<?=$rmtestMethod?>"></td>
                                                </tr>
												
												
						<!--  <tr>
                                	<td class="fieldName" nowrap>*RM Test Method:&nbsp;</td>
                                       <td><INPUT TYPE="text" NAME="rmtestMethod" id="rmtestMethod" size="15" value="<?=$rmtestMethod?>"></td>
                               </tr>-->
							 
									 <tr>
									   
									   <td class="fieldName" nowrap>*Result:&nbsp;</td>
                                        <td><textarea TYPE="text" NAME="result" id="result" size="15" ><?=$result?></textarea></td>
                                                </tr>
												
							<!-- <tr>
                                	<td class="fieldName" nowrap>*Result:&nbsp;</td>
                                        <td><textarea TYPE="text" NAME="result" id="result" size="15" ><?=$result?></textarea></td>
                                                </tr>-->
                                             <!-- </table>-->
											 
						</table>
		<?php
			require("template/rbBottom.php");
		?>
		</td>
		</tr>
		</table>
											 </td>
					  </tr>
					<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>					
	

	
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
	<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMTestData.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateRMTestData(document.frmRMTestData);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('RMTestData.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateRMTestData(document.frmRMTestData);">												</td>

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
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;RM Test Data  </td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmTestDataSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? /* if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRMtestData.php',700,600);"><? } */?></td>
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
												if( sizeof($rmTestDataRecords) > 0 )
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
      				$nav.= " <a href=\"RMTestData.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RMTestData.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RMTestData.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Company Name</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Unit Name</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">RM Lot ID</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">RM Test Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Test Method</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date of Testing</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Result</td>
		<!--<td class="listing-head"></td>-->
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($rmTestDataRecords as $sir) {
		$i++;
		$rmTestDataId	=	$sir[0];
		//$unit		=	$sir[1];
		$companyRec		=	$rmTestDataObj->findCompany($sir[1]);
		$selCompany		=	$companyRec[1];
		$unitRec		=	$plantandunitObj->find($sir[2]);
		$unit		=	$unitRec[2];
		//$rmLotId		=	$sir[2];
		$lotRec		=	$rmTestDataObj->findLote($sir[3]);
		$rmLotId		=	$lotRec[1];

		//$rmTestName		=	$sir[3];
		$testNameRec		=	$rmTestMasterObj->find($sir[4]);
		$rmTestName		=	$testNameRec[1];
		$rmtestMethod		=	$testNameRec[2];
		$dateOfTesting		=	dateFormat($sir[6]);
		$result		=	$sir[7];
		
	?>
	<tr  bgcolor="WHITE">
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$rmTestDataId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selCompany;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rmLotId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rmTestName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rmtestMethod;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$dateOfTesting;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$result;?></td>
	<!--	<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<a href="javascript:printWindow('ViewRMTestDetails.php?rmTestDataId=<?=$rmTestDataId?>',700,600)" class="link1" title="Click here to view details.">View Details</a>
		</td>-->
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$rmTestDataId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='RMTestData.php';"></td>
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
      				$nav.= " <a href=\"RMTestData.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"RMTestData.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"RMTestData.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$rmTestDataSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? /* if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintRMtestData.php',700,600);"><? } */?></td>
												
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