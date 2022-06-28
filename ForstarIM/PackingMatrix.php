<?php
	$insideIFrame = "Y";
	require("include/include.php");
	require_once('lib/PackingMatrix_ajax.php');
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$selection 	=	"?pageNo=".$p["pageNo"];

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
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	$packingCostMaster = "PCM";
	$pcmRateList = $manageRateListObj->latestRateList($packingCostMaster);

	$packingLabourCost = "PLC";
	$plcRateList = $manageRateListObj->latestRateList($packingLabourCost);

	$packingSealingCost = "PSC";
	$pscRateList = $manageRateListObj->latestRateList($packingSealingCost);

	$packingMaterialCost = "PMC";
	$pmcRateList = $manageRateListObj->latestRateList($packingMaterialCost);

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}
	
	#Add New Packing Matrix
	if ($p["cmdAdd"]!="") 
	{
	    $packingType = $p["packingName"];
		$idealNetwt		 	= $p['idealNetWt'];
		$innerContainerId	= $p['innerContainerType'];
		$innerPackingId		= $p['innerPackingType'];
		$innerSampleId		= $p['innerSampleType'];
		$innerLabelingId	= $p['innerLabelingType'];
		$innerLeafletId		= $p['innerLeafletType'];
		$innerSealingId		= $p['innerSealingType'];
		$pkgLabourRateId	= $p['pkgLabourRateType'];
		
		$shrinkGroup = $p['shrinkGrp'];
		$dispenserShrink = $p['dispenserShrink'];
		$noOfPacksInMC		= $p["noOfPacksInMC"];
		$masterPackingId	= $p['masterPackingType'];
		$grossMC = $p['grossMC'];
		
		$innerContainerRate	= $p["innerContainerRate"];
		$innerPackingRate	= $p["innerPackingRate"];
		$innerSampleRate	= $p["innerSampleRate"];
		$innerLabelingRate	= $p["innerLabelingRate"];
		$innerLeafletRate	= $p["innerLeafletRate"];
		$innerSealingRate	= $p["innerSealingRate"];
		$pkgLabourRate		= $p["labourCost"];		
		$innerPkgCost		= $p["innerPkgCost"];
		$dispenserPkg       = $p['dispenserPkg'];
		$dispenserSeal      = $p['dispenserSealing'];
		$masterPackingRate	= $p["masterPackingRate"];
		$masterSealingRate	= $p["masterSealingRate"];
		$masterLoading      = $p['masterLoading'];
		$outerPackingCost	= $p["outerPackingCost"];
		$labourCostOnly     =$p['labourCostOnly'];
		
		$currentDate        = date("Y-m-d");

		if ($packingType!="") 
		{
			$packingMatrixRecIns = $packingMatrixObj->addPackingMatrix($packingType, $idealNetwt, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $shrinkGroup, $dispenserShrink, $noOfPacksInMC, $masterPackingId, $grossMC, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $dispenserPkg, $dispenserSeal, $masterPackingRate, $masterSealingRate, $masterLoading, $outerPackingCost, $labourCostOnly, $userId, $currentDate);

			if ($packingMatrixRecIns) 
			{
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddPackingMatrix);
				$sessObj->createSession("nextPage",$url_afterAddPackingMatrix.$selection);
			} 

			else 
			{
				$addMode = true;
				$err	 = $msg_failAddPackingMatrix;
			}
			$packingMatrixRecIns = false;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") 
	{
		$pkgMatrixRecId  	= $p["hidPackingMatrixId"];

		$packingType        = $p["packingName"];
		$idealNetwt		 	= $p['idealNetWt'];
		$innerContainerId	= $p['innerContainerType'];
		$innerPackingId		= $p['innerPackingType'];
		$innerSampleId		= $p['innerSampleType'];
		$innerLabelingId	= $p['innerLabelingType'];
		$innerLeafletId		= $p['innerLeafletType'];
		$innerSealingId		= $p['innerSealingType'];
		$pkgLabourRateId	= $p['pkgLabourRateType'];
		$shrinkGroup        = $p['shrinkGrp'];
		$dispenserShrink    = $p['dispenserShrink'];
		$noOfPacksInMC		= $p["noOfPacksInMC"];
		$masterPackingId	= $p['masterPackingType'];
		$grossMC            = $p['grossMC'];
		
		$innerContainerRate	= $p["innerContainerRate"];
		$innerPackingRate	= $p["innerPackingRate"];
		$innerSampleRate	= $p["innerSampleRate"];
		$innerLabelingRate	= $p["innerLabelingRate"];
		$innerLeafletRate	= $p["innerLeafletRate"];
		$innerSealingRate	= $p["innerSealingRate"];
		$pkgLabourRate		= $p["labourCost"];		
		$innerPkgCost		= $p["innerPkgCost"];
		$dispenserPkg       = $p['dispenserPkg'];
		$dispenserSeal      = $p['dispenserSealing'];
		$masterPackingRate	= $p["masterPackingRate"];
		$masterSealingRate	= $p["masterSealingRate"];
		$masterLoading      = $p['masterLoading'];
		$outerPackingCost	= $p["outerPackingCost"];
		$labourCostOnly     =$p['labourCostOnly'];
		
		if ($pkgMatrixRecId!="" && $packingType!="") 
		{
			$pkgMatrixRecUptd = $packingMatrixObj->updatePackingMatrix($pkgMatrixRecId, $packingType, $idealNetwt, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $shrinkGroup, $dispenserShrink, $noOfPacksInMC, $masterPackingId, $grossMC, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $dispenserPkg, $dispenserSeal, $masterPackingRate, $masterSealingRate, $masterLoading, $outerPackingCost, $labourCostOnly, $userId);
		}
	
		if ($pkgMatrixRecUptd) 
		{
			$sessObj->createSession("displayMsg",$msg_succPackingMatrixUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePackingMatrix.$selection);
		} 
		else 
		{
			$editMode	=	true;
			$err		=	$msg_failPackingMatrixUpdate;
		}
		$pkgMatrixRecUptd	=	false;
	}


	# Edit  
	if ($p["editId"]!="") 
	{
		$editId		=	$p["editId"];
		$editMode	=	true;
		if($editId!="")
		{
			$pkgMatrixRec	=	$packingMatrixObj->find($editId);
		}
		
		$edtPkgMatrixId                = $pkgMatrixRec[0];
		$edtPackingType                = $pkgMatrixRec[1];
		$edtIdealNetwt				   = $pkgMatrixRec[2];
		$edtInnerContainerId	       = $pkgMatrixRec[3];
		$edtInnerPackingId		       = $pkgMatrixRec[4];		
		$edtInnerSampleId		       = $pkgMatrixRec[5];		
		$edtInnerLabelingId            = $pkgMatrixRec[6];		
		$edtInnerLeafletId		       = $pkgMatrixRec[7];	
		$edtInnerSealingId		       = $pkgMatrixRec[8];		
		$edtPkgLabourRateId	           = $pkgMatrixRec[9];
		$edtShrinkGrp                  = $pkgMatrixRec[10];
		$edtDispensrShrink             = $pkgMatrixRec[11];
		$edtNoOfPacksInMC              = $pkgMatrixRec[12];
		$edtmasterPackingId            = $pkgMatrixRec[13];
		$edtGrossMC                    = $pkgMatrixRec[14];
		
		$edtInnerContainerRate	       = $pkgMatrixRec[15];
		$edtInnerPackingRate	       = $pkgMatrixRec[16];
		$edtInnerSampleRate	           = $pkgMatrixRec[17];
		$edtInnerLabelingRate	       = $pkgMatrixRec[18];
		$edtInnerLeafletRate	       = $pkgMatrixRec[19];
		$edtInnerSealingRate	       = $pkgMatrixRec[20];
		$edtLabourCost		           = $pkgMatrixRec[21];
		
		$edtInnerPackingCost           = $pkgMatrixRec[22];
		$edtDispenserPkg               = $pkgMatrixRec[23];
		$edtDispenserSeal              = $pkgMatrixRec[24];
		$edtMasterPkg                  = $pkgMatrixRec[25];
		$edtMasterSeal                 = $pkgMatrixRec[26];
		$edtMasterLoad                 = $pkgMatrixRec[27];
		$edtOuterPackgCost             = $pkgMatrixRec[28];
		$edtLabourCostOnly             = $pkgMatrixRec[29];

	}


	# Delete a Record
	if ( $p["cmdDelete"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++)
		{
			$pkgMatrixRecId	=	$p["delId_".$i];

			if ($pkgMatrixRecId!="") 
			{
				// Need to check the selected Category is link with any other process
				$pkgMatrixRecDel = $packingMatrixObj->deletePackingMatrixRec($pkgMatrixRecId);
			}
		}
		if ($pkgMatrixRecDel) 
		{
			$sessObj->createSession("displayMsg",$msg_succDelPackingMatrix);
			$sessObj->createSession("nextPage",$url_afterDelPackingMatrix.$selection);
		} 
		else 
		{
			$errDel	=	$msg_failDelPackingMatrix;
		}
		$pkgMatrixRecDel	=	false;
	}
	
	#Confirm Packing Matrix
	if($p['btnPending']!="")
	{
		$confirmId = $p['confirmId'];
		
		if($confirmId!="")
		{
			$confirmPackngMatrix = $packingMatrixObj->confirmPackingMatrix($confirmId);
		}
		if($confirmPackngMatrix)
		{
			$sessObj->createSession("displayMsg",$msg_succConfirmPackingMatrix);
			$sessObj->createSession("nextPage",$url_afterConfirmPackingMatrix.$selection);
		}
		else
		{
			$errConfirm	=	$msg_failConfirm;
		}
	}
	
	#Release Confirmation of Packing Matrix
	if($p['btnConfirm']!="")
	{
		$relConfirmId = $p['confirmId'];
		
		if($relConfirmId!="")
		{
			$relConfirmPackingMatrix = $packingMatrixObj->releaseConfirmation($relConfirmId);
		}
		
		if($relConfirmPackingMatrix)
		{
			$sessObj->createSession("displayMsg",$msg_succRelConfirmPackingMatrix);
			$sessObj->createSession("nextPage",$url_afterConfirmPackingMatrix.$selection);
		}
		else 
		{
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}
	
	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Packing Matrix
	$pkgMatrixResultSetObj = $packingMatrixObj->fetchAllPagingRecords($offset, $limit);
	$pkgMatrixRecordSize   = sizeof($pkgMatrixResultSetObj);
	

	## -------------- Pagination Settings II -------------------
	$allPackingMatrixResultSetObj = $packingMatrixObj->fetchAllRecords();
	$numrows	=  sizeof($allPackingMatrixResultSetObj);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	
	#Get all Packing Cost master Value
	if ($addMode || $editMode) {
		list($vatRateForPackingMaterial, $innerCartonWstage, $costOfGum, $noOfMcsPerTapeRoll, $costOfTapeRoll, $tapeCostPerMc) = $packingCostMasterObj->getPackingCostMasterValue($pcmRateList);
	}	

	# List all Packing Material Cost
	$packingMaterialCostResultSetObj = $packingMaterialCostObj->fetchAllRecords($pmcRateList);

	#Inner packing
	$innerPakgMaterialResultSetObj	= $packingMaterialCostObj->fetchAllRecords($pmcRateList);

	#Inner Sample
	$innerSampleMaterialResultSetObj = $packingMaterialCostObj->fetchAllRecords($pmcRateList);

	#Inner labeling
	$innerLabelMaterialResultSetObj = $packingMaterialCostObj->fetchAllRecords($pmcRateList);

	#Inner Leaflet
	$innerLeafletMaterialResultSetObj = $packingMaterialCostObj->fetchAllRecords($pmcRateList);

	# List all Sealing Cost
	$packingSealingCostResultSetObj = $packingSealingCostObj->fetchAllRecords($pscRateList);

	# List all Packing labour Cost
	
	$packingLabourCostResultSetObj = $packingLabourCostObj->fetchAllRecords($plcRateList);

	#Master packing 	
	$masterPkgMaterialResultSetObj = $packingMaterialCostObj->fetchAllRecords($pmcRateList);

	//Temporary Increase Factor
	$tempIncrease = $packingMatrixObj->getTempIncrease();
	
	# Display / Hide Qty boxes
	if ($addMode) $ON_LOAD_FN = "return hidPackingQtyBox();";
	if ($editMode) $ON_LOAD_FN = "return showPackingQtyBox();";

	
	#heading Section
	if ($editMode) $heading	= $label_editPackingMatrix;
	else	       $heading	= $label_addPackingMatrix;
	
	//Get All Active Inventory 
	$stockRecords	= $packingMaterialObj->getStockRTE();
	
	//Assigning Packing Cost array
	$packing = array("--Select--","Packing Labour rate per Glass Bottle", "Packing Labour rate per PET Bottle", "Packing Labour rate per Retort Pouch", "Sealing Cost for Glass Bottles", "Sealing Cost (3000packs/500ml gum) for IC", "Sealing Cost for PET Bottles");

	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	  
	$ON_LOAD_PRINT_JS = "libjs/PackingMatrix.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
<form name="frmPackingMatrix" action="PackingMatrix.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr>
		<TD height="10"></TD>
	</tr>	
	<? 
	if($err!="" )
	{
	?>
	<tr>
		<td height="10" align="center" class="err1" > <?=$err;?></td>
	</tr>
	<?
	}
	?>
	<tr>
		<td align="center">
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
				<tr>
					<td>
					<?php	
						$bxHeader = "Packing Matrix";
						include "template/boxTL.php";
					?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td colspan="3" align="center">
									<Table width="30%">
									<?
									if ( $editMode || $addMode) 
									{
									?>
										<tr>
											<td>
												<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="96%">
													<tr>
														<td>
														<!-- Form fields start -->
														<?php							
														$entryHead = $heading;
														require("template/rbTop.php");
														?>
														<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
															<!--<tr>
																<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
																<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
															</tr>-->
															<tr>
																<td width="1" ></td>
																<td colspan="2" >
																	<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
																		<tr>
																			<td colspan="2" height="10" ></td>
																		</tr>
																		<tr>
																			<? 
																			if($editMode)
																			{
																			?>
																			<td colspan="2" align="center">
																				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('PackingMatrix.php');" />&nbsp;&nbsp;
																				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validatePackingMatrix(document.frmPackingMatrix);" />
																			</td>
																			<?
																			} 
																			else
																			{
																			?>
																			<td  colspan="2" align="center">
																				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingMatrix.php');">&nbsp;&nbsp;
																				<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePackingMatrix(document.frmPackingMatrix);">
																			</td>
																			<?
																			}
																			?>
																		</tr>
																		<input type="hidden" name="hidPackingMatrixId" value="<?=$edtPkgMatrixId;?>">
																		<tr>
																			<TD height="10"></TD>
																		</tr>
																		<tr>
																			<td colspan="2" nowrap style="padding-left:10px; padding-right:10px;" >
																				<table>
																					<tr>
																					<!-- 	Ist Column -->
																						<TD valign="top">
																							<table>
																								<tr>
																									<td class="fieldName" nowrap >*Packing Type</td>
																									<td>
																										<input type="text" name="packingName" id="packingName" size="20" value="<?=$edtPackingType;?>" />
																									</td>
																								</tr>	
																								<tr>
																									<td class="fieldName" nowrap >*Ideal Net Wt</td>
																									<td>
																										<input type="text" name="idealNetWt" id="idealNetWt" size="6" value="<?=$edtIdealNetwt;?>" /><span class="fieldName"> in Kg</span>
																									</td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >*Inner Container</td>
																									<td>
																									<table>
																										<TR>
																											<TD>
																												<select name="innerContainerType" id="innerContainerType" onchange="xajax_getInnerContainer(this.value);">
																												<option value="0">-- Select --</option>
																												<?
																												if(sizeof($stockRecords) > 0)
																												{
																													foreach($stockRecords as $stk)
																													{
																														$stockId = $stk[0];
																														$stockName = $stk[1];
																														$selected =  ($edtInnerContainerId == $stockId)?"Selected":"";
																														?>
																														<option value="<?=$stockId;?>" <?=$selected;?>><?=$stockName;?></option>
																														<?php
																													}
																												}
																												?>
																												</select>
																												<input type="hidden" name="hid_StockId" id="hid_StockId" value="<?=$p['innerContainerType'];?>">
																											</TD>
																										</TR>
																									</table>
																									</td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Inner Packing</td>
																									<td class="listing-item">				
																									<table>
																										<TR>
																											<TD>
																												<select name="innerPackingType" id="innerPackingType" onchange="xajax_getInnerPacking(this.value);">
																												<option value="0">-- Select --</option>
																												<?
																												if(sizeof($stockRecords) > 0)
																												{
																													foreach($stockRecords as $stk)
																													{
																														$stockId = $stk[0];
																														$stockName = $stk[1];
																														$selected =  ($edtInnerPackingId == $stockId)?"Selected":"";
																														?>
																														<option value="<?=$stockId;?>" <?=$selected;?>><?=$stockName;?></option>
																														<?php
																													}
																												}
																												?>
																												</select>
																											</TD>
																								
																										</TR>
																									</table>
																									</td>
																								</tr>					
																								<tr>
																									<td class="fieldName" nowrap >Inner Sample</td>
																									<td class="listing-item">
																									<table>
																										<TR>
																											<TD>
																												<select name="innerSampleType" id="innerSampleType" onchange="xajax_getInnerSample(this.value);">
																												<option value="0">-- Select --</option>
																												<?
																												if(sizeof($stockRecords) > 0)
																												{
																													foreach($stockRecords as $stk)
																													{
																														$stockId = $stk[0];
																														$stockName = $stk[1];
																														$selected =  ($edtInnerSampleId	== $stockId)?"Selected":"";
																														?>
																														<option value="<?=$stockId;?>" <?=$selected;?>><?=$stockName;?></option>
																														<?php
																													}
																												}
																												?>
																												</select>
																											</TD>
																					
																										</TR>
																									</table>
																									</td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Inner Labeling</td>
																									<td class="listing-item">
																									<table>
																										<TR>
																											<TD>
																												<select name="innerLabelingType" id="innerLabelingType" onchange="xajax_getInnerLabeling(this.value);">
																												<option value="0">-- Select --</option>
																												<?
																												if(sizeof($stockRecords) > 0)
																												{
																													foreach($stockRecords as $stk)
																													{
																														$stockId = $stk[0];
																														$stockName = $stk[1];
																														$selected =  ($edtInnerLabelingId == $stockId)?"Selected":"";
																														?>
																														<option value="<?=$stockId;?>" <?=$selected;?>><?=$stockName;?></option>
																														<?php
																													}
																												}
																												?>
																												</select>
																											</TD>
																										</TR>
																									</table>
																									</td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Inner Leaflet</td>
																									<td class="listing-item">				
																									<table>
																										<TR>
																											<TD>
																												<select name="innerLeafletType" id="innerLeafletType" onchange="xajax_getInnerLeaflet(this.value);">
																												<option value="0">-- Select --</option>
																												<?
																												if(sizeof($stockRecords) > 0)
																												{
																												foreach($stockRecords as $stk)
																													{
																														$stockId = $stk[0];
																														$stockName = $stk[1];
																														$selected =  ($edtInnerLeafletId == $stockId)?"Selected":"";
																														?>
																														<option value="<?=$stockId;?>" <?=$selected;?>><?=$stockName;?></option>
																														<?php
																													}
																												}
																												?>
																												</select>
																											</TD>
																										</TR>
																									</table>
																									</td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Inner Sealing</td>
																									<td class="listing-item">
																									<TABLE>
																										<TR>
																											<TD>
																												<select name="innerSealingType" id="innerSealingType" onchange="xajax_getInnerSealing(this.value);">
																												<?
																												for($i=0; $i<7; $i++)
																												{
																													if($edtInnerSealingId == $i)
																													{
																														$selected = "Selected";
																													}
																													else
																													{
																														$selected = "";
																													}  
																													?>
																													<option value="<?=$i;?>" <?=$selected;?>><?=$packing[$i];?></option>
																													<?php 
																												}
																												?>
																												</select>
																											</TD>
																										</TR>
																									</table>
																									</td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >*Pkg labour Rate</td>		  		
																									<td>
																									<table>
																										<TR>
																											<TD>
																												<select name="pkgLabourRateType" id="pkgLabourRateType" onchange="xajax_getLabourCost(this.value);">
																												<?
																												for($i=0; $i<7; $i++)
																												{
																													if($edtPkgLabourRateId == $i)
																													{
																														$selected = "Selected";
																													}
																													else
																													{
																														$selected = "";
																													} 
																													?>
																													<option value="<?=$i;?>" <?=$selected;?>><?=$packing[$i];?></option>
																													<?php 
																												}
																												?>
																												</select>
																											</TD>
																										</TR>
																									</table>
																									</td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap>Shrink Group</td>
																									<td class="listing-item"><input type="text" name="shrinkGrp" size="5" id="shrinkGrp" value="<?=$edtShrinkGrp;?>" style="text-align:right;" onkeyup="xajax_getDispenserSeal(document.getElementById('shrinkGrp').value);"></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap>Dispenser with Shrink Pkg</td>
																									<td class="listing-item">
																										<select name="dispenserShrink" id="dispenserShrink" onchange="xajax_getDispenserPkg(this.value,document.getElementById('shrinkGrp').value);">
																										<option value="0">-- Select --</option>
																										<?
																										if(sizeof($stockRecords) > 0)
																										{
																											foreach($stockRecords as $stk)
																											{
																												$stockId = $stk[0];
																												$stockName = $stk[1];
																												$selected =  ($edtDispensrShrink == $stockId)?"Selected":"";
																												?>
																												<option value="<?=$stockId;?>" <?=$selected;?>><?=$stockName;?></option>
																												<?php
																											}
																										}
																										?>
																										</select>
																									</td>
																								</tr>
																							</table>
																						</TD>
																					<!-- IInd Column -->
																						<td valign="top">
																							<table>
																								<tr>
																									<td class="fieldName" nowrap >*No of Packs in MC</td>
																									<td class="listing-item"><input type="text" name="noOfPacksInMC" size="5" id="noOfPacksInMC" value="<?=$edtNoOfPacksInMC;?>" style="text-align:right;" onkeyup="xajax_getMasterSealing(document.getElementById('noOfPacksInMC').value);"></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >*Master Packing</td>
																									<td class="listing-item">
																										<select name="masterPackingType" id="masterPackingType" onchange="xajax_getMasterPacking(this.value,document.getElementById('noOfPacksInMC').value);">
																										<option value="0">-- Select --</option>
																										<?
																										if(sizeof($stockRecords) > 0)
																										{
																											foreach($stockRecords as $stk)
																											{
																												$stockId = $stk[0];
																												$stockName = $stk[1];
																												$selected =  ($edtmasterPackingId == $stockId)?"Selected":"";
																												?>
																												<option value="<?=$stockId;?>" <?=$selected;?>><?=$stockName;?></option>
																												<?php
																											}
																										}
																										?>
																										</select>
																									</td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Gross MC Wt (Kg)</td>
																									<td class="listing-item"><input type="text" name="grossMC" size="5" id="grossMC" value="<?=$edtGrossMC;?>" style="text-align:right;" onkeyup="xajax_getMasterLoading(document.getElementById('grossMC').value, document.getElementById('noOfPacksInMC').value);"></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap>Inner Container</td>
																									<td class="listing-item"><input type="text" name="innerContainerRate" size="5" id="innerContainerRate" value="<?=$edtInnerContainerRate;?>" style="text-align:right; border:none;" readonly></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Inner Carton</td>
																									<td class="listing-item"><input type="text" name="innerPackingRate" size="5" id="innerPackingRate" value="<?=$edtInnerPackingRate;?>" style="text-align:right; border:none;" readonly></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Inner Sample</td>
																									<td class="listing-item">
																									<input type="text" name="innerSampleRate" size="5" id="innerSampleRate" value="<?=$edtInnerSampleRate;?>" style="text-align:right; border:none;" readonly>				
																									</td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Inner Labeling</td>
																									<td class="listing-item"><input type="text" name="innerLabelingRate" size="5" id="innerLabelingRate" value="<?=$edtInnerLabelingRate;?>" style="text-align:right; border:none;" readonly></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Inner Leaflet</td>
																									<td class="listing-item"><input type="text" name="innerLeafletRate" size="5" id="innerLeafletRate" value="<?=$edtInnerLeafletRate;?>" style="text-align:right; border:none;" readonly></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Inner Sealing</td>
																									<td class="listing-item"><input type="text" name="innerSealingRate" size="5" id="innerSealingRate" value="<?=$edtInnerSealingRate;?>" style="text-align:right; border:none;" readonly></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Labour Cost</td>
																									<td class="listing-item"><input type="text" name="labourCost" size="5" id="labourCost" value="<?=$edtLabourCost;?>" style="text-align:right; border:none;" readonly ></td>
																								</tr>
																							</table>	
																						</td>
																						<td valign="top">
																							<table>
																								<tr>
																									<td class="fieldName" nowrap >INNER PACKING COST</td>
																									<td class="listing-item"><input type="text" name="innerPkgCost" size="5" id="innerPkgCost" value="<?=$edtInnerPackingCost;?>" style="text-align:right; border:none; font-weight:bold;" readonly></td>
																								</tr>
																								<tr>
																									<td><input type="hidden" name="tempIncreaseFactor" id="tempIncreaseFactor" value="<?=$tempIncrease[0];?>"/></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Dispenser Pkg</td>
																									<td class="listing-item"><input type="text" name="dispenserPkg" size="5" id="dispenserPkg" value="<?=$edtDispenserPkg;?>" style="text-align:right; border:none;" readonly></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Dispenser Sealing</td>
																									<td class="listing-item"><input type="text" name="dispenserSealing" size="5" id="dispenserSealing" value="<?=$edtDispenserSeal;?>" style="text-align:right; border:none;" readonly></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Master Packing</td>
																									<td class="listing-item"><input type="text" name="masterPackingRate" size="5" id="masterPackingRate" value="<?=$edtMasterPkg;?>" style="text-align:right; border:none;" readonly></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Master Sealing</td>
																									<td class="listing-item"><input type="text" name="masterSealingRate" size="5" id="masterSealingRate" value="<?=$edtMasterSeal;?>" style="text-align:right; border:none;" readonly></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >Master Loading</td>
																									<td class="listing-item"><input type="text" name="masterLoading" size="5" id="masterLoading" value="<?=$edtMasterLoad;?>" style="text-align:right; border:none;" readonly></td>
																								</tr>
																								<tr>
																									<td class="fieldName" nowrap >OUTER PACKING COST</td>
																									<td class="listing-item"><input type="text" name="outerPackingCost" size="5" id="outerPackingCost" value="<?=$edtOuterPackgCost;?>" style="text-align:right; border:none; font-weight:bold;" readonly></td>
																								</tr>	
																								<tr>
																									<td class="fieldName" nowrap >Labour Cost Only</td>
																									<td class="listing-item"><input type="text" name="labourCostOnly" size="5" id="labourCostOnly" value="<?=$edtLabourCostOnly;?>" style="text-align:right; border:none; font-weight:bold;" readonly></td>
																								</tr>						
																							</table>
																						</td>
																						<!--  Third Column End Here-->
																					</tr>						
																				</table>
																			</td>
																		</tr>
																		<tr>
																			<td colspan="2"  height="10" ></td>
																		</tr>
																		<tr>
																		<? if($editMode)
																		{
																		?>
																			<td colspan="2" align="center">
																				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingMatrix.php');">&nbsp;&nbsp;
																				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePackingMatrix(document.frmPackingMatrix);">												
																			</td>
																		<?
																		} 
																		else
																		{
																		?>
																			<td  colspan="2" align="center">
																				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingMatrix.php');">&nbsp;&nbsp;
																				<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePackingMatrix(document.frmPackingMatrix);">												
																			</td>
																			<input type="hidden" name="cmdAddNew" value="1">
																		<?
																		}
																		?>
																		</tr>
																		<tr>
																			<td colspan="2"  height="10" ></td>
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
									<?
									}
									# Listing Category Starts
									?>
									</table>
								</td>
							</tr>
							<tr>
								<td height="10" align="center" ></td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							<tr>	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$pkgMatrixRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingMatrix.php',700,600);"><? }?></td>
										</tr>
									</table>									
								</td>
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
								<td colspan="2" style="padding-left:10px; padding-right:10px;" >
									<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
									<?
									if ($pkgMatrixRecordSize > 0) 
									{
									$i	=	0;
									?>
										<thead>
											<? 
											if($maxpage>1)
											{
											?>
											<tr>
												<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
													<div align="right">
													<?php
													 $nav  = '';
													for ($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"PackingMatrix.php?pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"PackingMatrix.php?pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"PackingMatrix.php?pageNo=$page\"  class=\"link1\">>></a> ";
													} else {
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
												<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Packing Type</th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Inner Packing Cost</th>
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Outer Packing Cost</th>	
												<th class="listing-head" style="padding-left:10px; padding-right:10px;">Labour Cost Only</th>	
												<? 
												if($edit==true)
												{
												?>
												<th class="listing-head">&nbsp;</th>
												<? 
												}
												?>
												<? 
												if($confirm==true)
												{
												?>
												<th class="listing-head">&nbsp;</th>
												<?
												}
												?>
											</tr>
										</thead>
										<tbody>
										<?
										foreach ($pkgMatrixResultSetObj as $pmr)
										{
											$i++;
											$pkgMatrixRecId 	= $pmr[0];
											$pmType			    = $pmr[1];
											$innerPackingCost   = $pmr[2];
											$outerPackingCost   = $pmr[3];
											$labourCostOnly	    = $pmr[4];					
											$pmActive 	        = $pmr[5];					
										?>
											<tr <?php if ($pmActive==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
												<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$pkgMatrixRecId;?>" class="chkBox"></td>
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$pmType?></td>
												<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$innerPackingCost?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$outerPackingCost?></td>
												<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$labourCostOnly?></td>
												<? 
												if($edit==true)
												{
												?>
												<td class="listing-item" width="60" align="center"><?php if ($pmActive==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$pkgMatrixRecId;?>,'editId'); this.form.action='PackingMatrix.php';" ><? } ?></td>
												<? 
												}
												if ($confirm==true)
												{
												?>
												<td <?php if ($pmActive==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
												<?php
												if($confirm==true) 
												{ 
												if($pmActive==0) 
												{
												?>
												<input type="submit" name="btnPending" value="Pending" onclick="assignValue(this.form,<?=$pkgMatrixRecId;?>,'confirmId')">
												<?php 
												}
												else if($pmActive==1) 
												{ 
												?>
												<input type="submit" name="btnConfirm" value="Confirmed" onClick="assignValue(this.form,<?=$pkgMatrixRecId;?>,'confirmId')">
												<?php  
												} 
												} 
												?>
												</td>
												<?php
												}
												?>
											</tr>
										<?
										}
										?>
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" id="editId" value="">
											<input type="hidden" name="confirmId" id="confirmId" value="">
											<? 
											if($maxpage>1)
											{
											?>
											<tr>
												<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
													<div align="right">
													<?php
													 $nav  = '';
													for ($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"PackingMatrix.php?pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"PackingMatrix.php?pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"PackingMatrix.php?pageNo=$page\"  class=\"link1\">>></a> ";
													} else {
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
											<?
										} 
										else 
										{
										?>
											<tr>
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
										<?
										}
										?>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5">
									<input type="hidden" name="noOfMinutesForSealing" id="noOfMinutesForSealing" value="<?=$noOfMinutesForSealing?>">
									<input type="hidden" name="noOfMinutesPerHour" id="noOfMinutesPerHour" value="<?=$noOfMinutesPerHour?>">
									<input type="hidden" name="noOfPouchesSealed" id="noOfPouchesSealed" value="<?=$noOfPouchesSealed?>">
									<input type="hidden" name="noOfSealingMachines" id="noOfSealingMachines" value="<?=$noOfSealingMachines?>">
									<input type="hidden" name="noOfHoursPerShift" id="noOfHoursPerShift" value="<?=$noOfHoursPerShift?>">
									<input type="hidden" name="noOfRetorts" id="noOfRetorts" value="<?=$noOfRetorts?>">
									<input type="hidden" name="noOfShifts" id="noOfShifts" value="<?=$noOfShifts?>">
									<input type="hidden" name="dieselConsumptionOfBoiler" id="dieselConsumptionOfBoiler" value="<?=$dieselConsumptionOfBoiler?>">
									<input type="hidden" name="dieselCostPerLitre" id="dieselCostPerLitre" value="<?=$dieselCostPerLitre?>">
									<input type="hidden" name="electricConsumptionPerDayUnit" id="electricConsumptionPerDayUnit" value="<?=$electricConsumptionPerDayUnit?>">
									<input type="hidden" name="electricCostPerUnit" id="electricCostPerUnit" value="<?=$electricCostPerUnit?>">
									<input type="hidden" name="waterConsumptionPerRetortBatchUnit" id="waterConsumptionPerRetortBatchUnit" value="<?=$waterConsumptionPerRetortBatchUnit?>">
									<input type="hidden" name="generalWaterConsumptionPerDayUnit" id="generalWaterConsumptionPerDayUnit" value="<?=$generalWaterConsumptionPerDayUnit?>">
									<input type="hidden" name="noOfWorkingDaysInMonth" id="noOfWorkingDaysInMonth" value="<?=$noOfWorkingDaysInMonth?>">
									<input type="hidden" name="costPerLitreOfWater" id="costPerLitreOfWater" value="<?=$costPerLitreOfWater?>">
									<input type="hidden" name="costOfCylinder" id="costOfCylinder" value="<?=$costOfCylinder?>">
									<input type="hidden" name="gasPerCylinderPerDay" id="gasPerCylinderPerDay" value="<?=$gasPerCylinderPerDay?>">
									<input type="hidden" name="maintenanceCost" id="maintenanceCost" value="<?=$maintenanceCost?>">
									<input type="hidden" name="consumablesCost" id="consumablesCost" value="<?=$consumablesCost?>">
									<input type="hidden" name="labCost" id="labCost" value="<?=$labCost?>">
									<input type="hidden" name="variableManPowerCostPerDay" id="variableManPowerCostPerDay" value="<?=$variableManPowerCostPerDay?>">
									<input type="hidden" name="totalMktgCostTCost" id="totalMktgCostTCost" value="<?=$totalMktgCostTCost?>">
									<input type="hidden" name="totalTravelCost" id="totalTravelCost" value="<?=$totalTravelCost?>">
									<input type="hidden" name="advtCostPerMonth" id="advtCostPerMonth" value="<?=$advtCostPerMonth?>">
								</td>
							</tr>
							<tr>	
								<td colspan="3">
									<table cellpadding="0" cellspacing="0" align="center">
										<tr>
											<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$pkgMatrixRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingMatrix.php',700,600);"><? }?></td>
										</tr>
									</table>									
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
						</table>			
						<?php
							include "template/boxBR.php"
						?>
					</td>
				</tr>
			</table>
			<!-- Form fields end   -->		
		</td>
	</tr>			
	<tr>
		<td height="10"></td>
	</tr>
	<!--tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr-->
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
</table>
<? if ($iFrameVal=="") 
{
 ?>
	<script language="javascript">
	<!--
	function ensureInFrameset(form)
	{		
		var pLocation = window.parent.location ;	
		var cLocation = window.location.href;			
		if (pLocation==cLocation) {		// Same Location
			document.getElementById("inIFrame").value = 'N';
			form.submit();		
		} else if (pLocation!=cLocation) { // Not in IFrame
			document.getElementById("inIFrame").value = 'Y';
		}
	}
	ensureInFrameset(document.frmPackingMatrix);
	//-->
	</script>
<? 
}
?>
</form>
<?
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>