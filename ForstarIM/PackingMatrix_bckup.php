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
	
	#Add 
	if ($p["cmdAdd"]!="") {
		$packingCode	= addSlash(trim($p["packingCode"]));
		$packingName	= addSlash(trim($p["packingName"]));
		
		$innerContainerType	= explode("_",$p["innerContainerType"]);
		$innerContainerId	= $innerContainerType[0];
		$innerPackingType	= explode("_",$p["innerPackingType"]);
		$innerPackingId		= $innerPackingType[0];

		$innerSampleType	= explode("_",$p["innerSampleType"]);
		$innerSampleId		= $innerSampleType[0];
		
		$innerLabelingType	= explode("_",$p["innerLabelingType"]);
		$innerLabelingId	= $innerLabelingType[0];
	
		$innerLeafletType	= explode("_",$p["innerLeafletType"]);
		$innerLeafletId		= $innerLeafletType[0];

		$innerSealingType	= explode("_",$p["innerSealingType"]);
		$innerSealingId		= $innerSealingType[0];

		$pkgLabourRateType	= explode("_",$p["pkgLabourRateType"]);
		$pkgLabourRateId	= $pkgLabourRateType[0];

		$noOfPacksInMC		= $p["noOfPacksInMC"];
		
		$masterPackingType	= explode("_",$p["masterPackingType"]);
		$masterPackingId	= $masterPackingType[0];
		
		$innerContainerRate	= $p["innerContainerRate"];
		$innerPackingRate	= $p["innerPackingRate"];
		$innerSampleRate	= $p["innerSampleRate"];
		$innerLabelingRate	= $p["innerLabelingRate"];
		$innerLeafletRate	= $p["innerLeafletRate"];
		$innerSealingRate	= $p["innerSealingRate"];
		$pkgLabourRate		= $p["pkgLabourRate"];		
		$innerPkgCost		= $p["innerPkgCost"];
		$masterPackingRate	= $p["masterPackingRate"];
		$masterSealingRate	= $p["masterSealingRate"];
		$outerPackingCost	= $p["outerPackingCost"];

		$productType		= $p["productType"];
		$innerContainerQty	= $p["innerContainerQty"];
		$innerPackingQty	= $p["innerPackingQty"];
		$innerSampleQty		= $p["innerSampleQty"];
		$innerLabelingQty	= $p["innerLabelingQty"];
		$innerLeafletQty	= $p["innerLeafletQty"];
		$innerSealingQty	= $p["innerSealingQty"];
		$pkgLabourRateQty	= $p["pkgLabourRateQty"];
		
		if ($packingCode!="" && $packingName!="") {
			$packingMatrixRecIns = $packingMatrixObj->addPackingMatrix($packingCode, $packingName, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $noOfPacksInMC, $masterPackingId, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $masterPackingRate, $masterSealingRate, $outerPackingCost, $productType, $innerContainerQty, $innerPackingQty, $innerSampleQty, $innerLabelingQty, $innerLeafletQty, $innerSealingQty, $pkgLabourRateQty, $userId);

			if ($packingMatrixRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddPackingMatrix);
				$sessObj->createSession("nextPage",$url_afterAddPackingMatrix.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddPackingMatrix;
			}
			$packingMatrixRecIns = false;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$pkgMatrixRecId	= $p["hidPackingMatrixId"];

		$packingCode	= addSlash(trim($p["packingCode"]));
		$packingName	= addSlash(trim($p["packingName"]));
		$innerContainerType	= explode("_",$p["innerContainerType"]);
		$innerContainerId	= $innerContainerType[0];
		$innerPackingType	= explode("_",$p["innerPackingType"]);
		$innerPackingId		= $innerPackingType[0];
		$innerSampleType	= explode("_",$p["innerSampleType"]);
		$innerSampleId		= $innerSampleType[0];
		$innerLabelingType	= explode("_",$p["innerLabelingType"]);
		$innerLabelingId	= $innerLabelingType[0];
		$innerLeafletType	= explode("_",$p["innerLeafletType"]);
		$innerLeafletId		= $innerLeafletType[0];
		$innerSealingType	= explode("_",$p["innerSealingType"]);
		$innerSealingId		= $innerSealingType[0];
		$pkgLabourRateType	= explode("_",$p["pkgLabourRateType"]);
		$pkgLabourRateId	= $pkgLabourRateType[0];
		$noOfPacksInMC		= $p["noOfPacksInMC"];
		$masterPackingType	= explode("_",$p["masterPackingType"]);
		$masterPackingId	= $masterPackingType[0];
		$innerContainerRate	= $p["innerContainerRate"];
		$innerPackingRate	= $p["innerPackingRate"];
		$innerSampleRate	= $p["innerSampleRate"];
		$innerLabelingRate	= $p["innerLabelingRate"];
		$innerLeafletRate	= $p["innerLeafletRate"];
		$innerSealingRate	= $p["innerSealingRate"];
		$pkgLabourRate		= $p["pkgLabourRate"];		
		$innerPkgCost		= $p["innerPkgCost"];
		$masterPackingRate	= $p["masterPackingRate"];
		$masterSealingRate	= $p["masterSealingRate"];
		$outerPackingCost	= $p["outerPackingCost"];

		$productType		= $p["productType"];
		$innerContainerQty	= $p["innerContainerQty"];
		$innerPackingQty	= $p["innerPackingQty"];
		$innerSampleQty		= $p["innerSampleQty"];
		$innerLabelingQty	= $p["innerLabelingQty"];
		$innerLeafletQty	= $p["innerLeafletQty"];
		$innerSealingQty	= $p["innerSealingQty"];
		$pkgLabourRateQty	= $p["pkgLabourRateQty"];
		
		
		if ($pkgMatrixRecId!="" && $packingCode!="" && $packingName!="") {
			$pkgMatrixRecUptd = $packingMatrixObj->updatePackingMatrix($pkgMatrixRecId, $packingCode, $packingName, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $noOfPacksInMC, $masterPackingId, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $masterPackingRate, $masterSealingRate, $outerPackingCost, $productType, $innerContainerQty, $innerPackingQty, $innerSampleQty, $innerLabelingQty, $innerLeafletQty, $innerSealingQty, $pkgLabourRateQty, $userId);
		}
	
		if ($pkgMatrixRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPackingMatrixUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePackingMatrix.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPackingMatrixUpdate;
		}
		$pkgMatrixRecUptd	=	false;
	}


	# Edit  
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$pkgMatrixRec	=	$packingMatrixObj->find($editId);
		$editPkgMatrixId =	$pkgMatrixRec[0];

		$packingCode	= stripSlash($pkgMatrixRec[1]);
		$packingName	= stripSlash($pkgMatrixRec[2]);
		$innerContainerId	= $pkgMatrixRec[3];
		$innerPackingId		= $pkgMatrixRec[4];		
		$innerSampleId		= $pkgMatrixRec[5];		
		$innerLabelingId	= $pkgMatrixRec[6];		
		$innerLeafletId		= $pkgMatrixRec[7];	
		$innerSealingId		= $pkgMatrixRec[8];		
		$pkgLabourRateId	= $pkgMatrixRec[9];
		$noOfPacksInMC		= $pkgMatrixRec[10];		
		$masterPackingId	= $pkgMatrixRec[11];
		
		$productType		= $pkgMatrixRec[23];	

		if ($productType=='CP') {
			$innerContainerRate = $packingMatrixObj->getInnerPackingRate($innerContainerId, $pmcRateList);
			$innerPackingRate =  $packingMatrixObj->getInnerPackingRate($innerPackingId, $pmcRateList);
			$innerSampleRate =  $packingMatrixObj->getInnerPackingRate($innerSampleId, $pmcRateList);
			$innerLabelingRate =  $packingMatrixObj->getInnerPackingRate($innerLabelingId, $pmcRateList);
			$innerLeafletRate =  $packingMatrixObj->getInnerPackingRate($innerLeafletId, $pmcRateList);
			$innerSealingRate =  $packingMatrixObj->getInnerSealingCost($innerSealingId, $pscRateList);
			$pkgLabourRate =  $packingMatrixObj->getPackingLabourCost($pkgLabourRateId, $plcRateList);
		} else {
			$innerContainerRate	= $pkgMatrixRec[12];
			$innerPackingRate	= $pkgMatrixRec[13];
			$innerSampleRate	= $pkgMatrixRec[14];
			$innerLabelingRate	= $pkgMatrixRec[15];
			$innerLeafletRate	= $pkgMatrixRec[16];
			$innerSealingRate	= $pkgMatrixRec[17];
			$pkgLabourRate		= $pkgMatrixRec[18];			
		}
	
		$innerPkgCost		= $pkgMatrixRec[19];
		$masterPackingRate	= $pkgMatrixRec[20];
		$masterSealingRate	= $pkgMatrixRec[21];
		$outerPackingCost	= $pkgMatrixRec[22];

		$innerContainerType	= $innerContainerId."_".$innerContainerRate;	
		$innerPackingType	= $innerPackingId."_".$innerPackingRate;
		$innerSampleType	= $innerSampleId."_".$innerSampleRate;	
		$innerLabelingType	= $innerLabelingId."_".$innerLabelingRate;	
		$innerLeafletType	= $innerLeafletId."_".$innerLeafletRate;
		$innerSealingType	= $innerSealingId."_".$innerSealingRate;
		$pkgLabourRateType	= $pkgLabourRateId."_".$pkgLabourRate;
		#Find the Original Packing Material Rate
		$masterPkgRate 		= $packingMatrixObj->findPackingMaterialRate($masterPackingId, $pmcRateList);
		$masterPackingType	= $masterPackingId."_".$masterPkgRate;	

		$innerContainerQty	= $pkgMatrixRec[24];
		$innerPackingQty	= $pkgMatrixRec[25];
		$innerSampleQty		= $pkgMatrixRec[26];
		$innerLabelingQty	= $pkgMatrixRec[27];
		$innerLeafletQty	= $pkgMatrixRec[28];
		$innerSealingQty	= $pkgMatrixRec[29];
		$pkgLabourRateQty	= $pkgMatrixRec[30];
		if ($productType=='CP') {
			$innerContainerRate = number_format(($innerContainerQty*$innerContainerRate),2,'.','');
			$innerPackingRate	=  number_format(($innerPackingQty*$innerPackingRate),2,'.','');
			$innerSampleRate	=  number_format(($innerSampleQty*$innerSampleRate),2,'.','');
			$innerLabelingRate	=  number_format(($innerLabelingQty*$innerLabelingRate),2,'.','');
			$innerLeafletRate	=  number_format(($innerLeafletQty*$innerLeafletRate),2,'.','');
			$innerSealingRate	=  number_format(($innerSealingQty*$innerSealingRate),2,'.','');
			$pkgLabourRate		=  number_format(($pkgLabourRateQty*$pkgLabourRate),2,'.','');
		}
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$pkgMatrixRecId	=	$p["delId_".$i];

			if ($pkgMatrixRecId!="") {
				// Need to check the selected Category is link with any other process
				$pkgMatrixRecDel = $packingMatrixObj->deletePackingMatrixRec($pkgMatrixRecId);
			}
		}
		if ($pkgMatrixRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPackingMatrix);
			$sessObj->createSession("nextPage",$url_afterDelPackingMatrix.$selection);
		} else {
			$errDel	=	$msg_failDelPackingMatrix;
		}
		$pkgMatrixRecDel	=	false;
	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Packing Matrix
	$pkgMatrixResultSetObj = $packingMatrixObj->fetchAllPagingRecords($offset, $limit);
	$pkgMatrixRecordSize   = $pkgMatrixResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allPackingMatrixResultSetObj = $packingMatrixObj->fetchAllRecords();
	$numrows	=  $allPackingMatrixResultSetObj->getNumRows();
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

	# Display / Hide Qty boxes
	if ($addMode) $ON_LOAD_FN = "return hidPackingQtyBox();";
	if ($editMode) $ON_LOAD_FN = "return showPackingQtyBox();";

	#heading Section
	if ($editMode) $heading	= $label_editPackingMatrix;
	else	       $heading	= $label_addPackingMatrix;
	
	//Get All Active Inventory 
	$stockRecords	= $packingMaterialObj->getStockRTE();
	/*echo '<pre>';
	print_r($stockRecords);
	echo '</pre>'; */
	
	echo $p['innerContainerType'];
	

	$ON_LOAD_PRINT_JS = "libjs/PackingMatrix.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmPackingMatrix" action="PackingMatrix.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>	
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1" > <?=$err;?></td>
	</tr>
	<?}?>
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
			if ( $editMode || $addMode) {
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
												<? if($editMode){?>

											  <td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('PackingMatrix.php');" />&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validatePackingMatrix(document.frmPackingMatrix);" /></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingMatrix.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePackingMatrix(document.frmPackingMatrix);">												</td>

												<?}?>
											</tr>
			<input type="hidden" name="hidPackingMatrixId" value="<?=$editPkgMatrixId;?>">
			<tr><TD height="10"></TD></tr>
			<tr>
				<td colspan="2" nowrap style="padding-left:10px; padding-right:10px;" >
					<table>
					<tr>
<!-- 	Ist Column -->
					<TD valign="top">
					<table>
					 <tr>
					  <td class="fieldName" nowrap >*Packing Code</td>
					  <td nowrap="true">
					  <input type="text" name="packingCode" size="20" value="<?=$packingCode?>" /></td>
				  	</tr>	
					<tr>
					  <td class="fieldName" nowrap >*Packing Name</td>
					  <td>
					  <input type="text" name="packingName" size="20" value="<?=$packingName?>" /></td>
				  	</tr>
					<tr>
					  <td class="fieldName" nowrap >*Product Type</td>
					  <td>
						<select name="productType" id="productType" onchange="showPackingQtyBox();">
							<option value="">--Select--</option>
							<option value="SP" <? if ($productType=='SP') echo "selected";?>>Single</option>
							<option value="CP" <? if ($productType=='CP') echo "selected";?>>Combo</option>
						</select>
					</td>
				  	</tr>	
					<tr>
					  <td class="fieldName" nowrap >*Inner Container</td>
					  <td>
						<table>
							<TR>
							<TD>
						<select name="innerContainerType" id="innerContainerType" onchange="displaySelPackingRate('innerContainerType', 'innerContainerRate', 'innerContainerQty'); calcInnerPackingCost();calcOuterPackingCost(); xajax_getInnerContainer(this.value);">
						<option value="0">-- Select --</option>
						<?
						/* while($pmcr=$packingMaterialCostResultSetObj->getRow()) {
							$pkngMaterialCostRecId 	  = $pmcr[0];	
							$materialName		  = $pmcr[4];
							$materialCode		  = $pmcr[5];
							$materialCost		  = $pmcr[3]; 
							$totalMaterialCost	  = $pmcr[6];
							$optionValue = $pkngMaterialCostRecId."_".$totalMaterialCost;

							$selected =  ($innerContainerType==$optionValue)?"Selected":"";
						?>
						<option value="<?=$optionValue?>" <?=$selected?>><?=$materialCode?></option>
						<? } */
						if(sizeof($stockRecords) > 0)
						{
							foreach($stockRecords as $stk)
							{
								$stockId = $stk[0];
								$stockName = $stk[1];
								$selected =  ($innerContainerType==$stockId)?"Selected":"";
								?>
								<option value="<?=$stockId;?>" <?=$selected;?>><?=$stockName;?></option>
								<?php
							}
						}
						?>
						</select>
						<input type="hidden" name="hid_StockId" id="hid_StockId" value="<?=$p['innerContainerType'];?>">
								</TD>
								<TD>
							<div id="innerContainerQtyDiv" style="display:block">
									<table>
										<TR>
											<TD class="listing-item">X</TD>
			<TD>
				<input type="text" size="1" name="innerContainerQty" id="innerContainerQty" value="<?=$innerContainerQty?>" style="text-align:center;" onkeyup="displaySelPackingRate('innerContainerType', 'innerContainerRate', 'innerContainerQty');">
			</TD>
		</TR>
		</table>
							</div>
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
						<select name="innerPackingType" id="innerPackingType" onchange="displaySelPackingRate('innerPackingType','innerPackingRate', 'innerPackingQty'); calcInnerPackingCost();calcOuterPackingCost();">
						<option value="0">-- Select --</option>
						<?
						while ($pmcr=$innerPakgMaterialResultSetObj->getRow()) {
							$pkngMaterialCostRecId 	  = $pmcr[0];	
							$materialName		  = $pmcr[4];
							$materialCode		  = $pmcr[5];
							$materialCost		  = $pmcr[3]; 
							$totalMaterialCost	  = $pmcr[6];
							$optionValue = $pkngMaterialCostRecId."_".$totalMaterialCost;
							$selected = ($innerPackingType==$optionValue)?"Selected":"";
						?>
						<option value="<?=$optionValue?>" <?=$selected?>><?=$materialCode?></option>
						<? }?>
						</select>
					</TD>
					<TD>
						<div id="innerPackingQtyDiv" style="display:block">
									<table>
										<TR>
											<TD class="listing-item">X</TD>
											<TD>
						<input type="text" size="1" name="innerPackingQty" id="innerPackingQty" value="<?=$innerPackingQty?>" style="text-align:center;" onkeyup="displaySelPackingRate('innerPackingType','innerPackingRate', 'innerPackingQty');"></TD>
										</TR>
									</table>
							</div>
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
			<select name="innerSampleType" id="innerSampleType" onchange="displaySelPackingRate('innerSampleType','innerSampleRate','innerSampleQty');calcInnerPackingCost();calcOuterPackingCost();">
						<option value="0">-- Select --</option>
						<?
						while ($pmcr=$innerSampleMaterialResultSetObj->getRow()) {
							$pkngMaterialCostRecId 	  = $pmcr[0];			
							$materialName		  = $pmcr[4];
							$materialCode		  = $pmcr[5];
							$materialCost		  = $pmcr[3]; 
							$totalMaterialCost	  = $pmcr[6];
							$optionValue = $pkngMaterialCostRecId."_".$totalMaterialCost;
							$selected = ($innerSampleType==$optionValue)?"Selected":"";
						?>
						<option value="<?=$optionValue?>" <?=$selected?>><?=$materialCode?></option>
						<? }?>
						</select>
		</TD>
		<TD>
			<div id="innerSampleQtyDiv" style="display:block">
			<table>
				<TR>
					<TD class="listing-item">X</TD>
					<TD>
						<input type="text" size="1" name="innerSampleQty" id="innerSampleQty" value="<?=$innerSampleQty?>" style="text-align:center;" onkeyup="displaySelPackingRate('innerSampleType','innerSampleRate','innerSampleQty');">
					</TD>
				</TR>
			</table>
			</div>
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
			<select name="innerLabelingType" id="innerLabelingType" onchange="displaySelPackingRate('innerLabelingType','innerLabelingRate','innerLabelingQty');calcInnerPackingCost();calcOuterPackingCost();">
						<option value="0">-- Select --</option>
						<?
						while ($pmcr=$innerLabelMaterialResultSetObj->getRow()) {
							$pkngMaterialCostRecId 	  = $pmcr[0];	
							$materialName		  = $pmcr[4];
							$materialCode		  = $pmcr[5];
							$materialCost		  = $pmcr[3]; 
							$totalMaterialCost	  = $pmcr[6];
							$optionValue = $pkngMaterialCostRecId."_".$totalMaterialCost;
							$selected = ($innerLabelingType==$optionValue)?"Selected":"";
						?>
						<option value="<?=$optionValue?>" <?=$selected?>><?=$materialCode?></option>
						<? }?>
						</select>
		</TD>
		<TD>
			<div id="innerLabelingQtyDiv" style="display:block">
			<table>
				<TR>
					<TD class="listing-item">X</TD>
					<TD>
						<input type="text" size="1" name="innerLabelingQty" id="innerLabelingQty" value="<?=$innerLabelingQty?>" style="text-align:center;" onkeyup="displaySelPackingRate('innerLabelingType','innerLabelingRate','innerLabelingQty');">
					</TD>
				</TR>
			</table>
			</div>
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
		<select name="innerLeafletType" id="innerLeafletType" onchange="displaySelPackingRate('innerLeafletType','innerLeafletRate','innerLeafletQty');calcInnerPackingCost();calcOuterPackingCost();">
		<option value="0">-- Select --</option>
		<?
			while($pmcr=$innerLeafletMaterialResultSetObj->getRow()) {
				$pkngMaterialCostRecId 	  = $pmcr[0];			
				$materialName		  = $pmcr[4];
				$materialCode		  = $pmcr[5];
				$materialCost		  = $pmcr[3]; 
				$totalMaterialCost	  = $pmcr[6];
				$optionValue = $pkngMaterialCostRecId."_".$totalMaterialCost;
				$selected =  ($innerLeafletType==$optionValue)?"Selected":"";
		?>
		<option value="<?=$optionValue?>" <?=$selected?>><?=$materialCode?></option>
		<? }?>
		</select>
		</TD>
		<TD>
			<div id="innerLeafletQtyDiv" style="display:block">
			<table>
				<TR>
					<TD class="listing-item">X</TD>
					<TD>
						<input type="text" size="1" name="innerLeafletQty" id="innerLeafletQty" value="<?=$innerLeafletQty?>" style="text-align:center;" onkeyup="displaySelPackingRate('innerLeafletType','innerLeafletRate','innerLeafletQty');">
					</TD>
				</TR>
			</table>
			</div>
		</TD>
	</TR>
</table>
			  </td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Inner Sealing</td>
					  <td class="listing-item">				
<table>
	<TR>
		<TD>
			<select name="innerSealingType" id="innerSealingType" onchange="displaySelPackingRate('innerSealingType','innerSealingRate','innerSealingQty');calcInnerPackingCost();calcOuterPackingCost();">
			<option value="0">-- Select --</option>
			<?
			while ($pscr=$packingSealingCostResultSetObj->getRow()) {
				$pkngSealingCostRecId 	= $pscr[0];
				$itemName	= stripSlash($pscr[1]);
				$itemCode	= $pscr[2];	
				$itemCost	= $pscr[3];	
				$optionValue = $pkngSealingCostRecId."_".$itemCost;
				$selected = ($innerSealingType==$optionValue)?"Selected":"";
			?>
			<option value="<?=$optionValue?>" <?=$selected?>><?=$itemCode?></option>
			<? }?>
			</select>
		</TD>
		<TD>
			<div id="innerSealingQtyDiv" style="display:block">
			<table>
				<TR>
					<TD class="listing-item">X</TD>
					<TD>
						<input type="text" size="1" name="innerSealingQty" id="innerSealingQty" value="<?=$innerSealingQty?>" style="text-align:center;" onkeyup="displaySelPackingRate('innerSealingType','innerSealingRate','innerSealingQty');">
					</TD>
				</TR>
			</table>
			</div>
		</TD>
	</TR>
</table>
					  </td>
					</tr>
					</table>
					</TD>
<!-- IInd Column -->
					<td valign="top">
						<table>	
						<tr>
					  		<td class="fieldName" nowrap >*Pkg labour Rate</td>		  		
							<td>
								<table>
	<TR>
		<TD>
						<select name="pkgLabourRateType" id="pkgLabourRateType" onchange="displaySelPackingRate('pkgLabourRateType','pkgLabourRate','pkgLabourRateQty');calcInnerPackingCost();calcOuterPackingCost();">
						<option value="0">-- Select --</option>
						<?
						while ($pcr=$packingLabourCostResultSetObj->getRow()) {
							$pkgLabourCostRecId = $pcr[0];
							$itemName	= stripSlash($pcr[1]);
							$itemCode	= $pcr[2];	
							$itemCost	= $pcr[3];
							$optionValue	= $pkgLabourCostRecId."_".$itemCost;
							$selected = ($pkgLabourRateType==$optionValue)?"Selected":"";
						?>
						<option value="<?=$optionValue?>" <?=$selected?>><?=$itemCode?></option>
						<? }?>
						</select></TD>
		<TD>
			<div id="pkgLabourRateDiv" style="display:block">
			<table>
				<TR>
					<TD class="listing-item">X</TD>
					<TD>
						<input type="text" size="1" name="pkgLabourRateQty" id="pkgLabourRateQty" value="<?=$pkgLabourRateQty?>" style="text-align:center;" onkeyup="displaySelPackingRate('pkgLabourRateType','pkgLabourRate','pkgLabourRateQty');">
					</TD>
				</TR>
			</table>
			</div>
		</TD>
	</TR>
</table>
						</td>
					</tr>
						<tr>
					  		<td class="fieldName" nowrap >*No of Packs in MC</td>
					  		<td class="listing-item"><input type="text" name="noOfPacksInMC" size="5" id="noOfPacksInMC" value="<?=$noOfPacksInMC?>" style="text-align:right;" onkeyup="displayMasterPackingRate();calcOuterPackingCost();"></td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >*Master Packing</td>
					 		<td class="listing-item">
						<select name="masterPackingType" id="masterPackingType" onchange="displayMasterPackingRate();calcOuterPackingCost();">
						<option value="0">-- Select --</option>
						<?
						while(($pmcr=$masterPkgMaterialResultSetObj->getRow())) {
							$pkngMaterialCostRecId 	  = $pmcr[0];			
							$materialName		  = $pmcr[4];
							$materialCode		  = $pmcr[5];
							$materialCost		  = $pmcr[3]; 
							$totalMaterialCost	  = $pmcr[6];
							$optionValue = $pkngMaterialCostRecId."_".$totalMaterialCost;
							$selected = ($masterPackingType==$optionValue)?"Selected":"";
						?>
						<option value="<?=$optionValue?>" <?=$selected?>><?=$materialCode?></option>
						<? }?>
						</select>
							</td>
							</tr>
							<tr>
							<td class="fieldName" nowrap>Inner Container</td>
					 		 <td class="listing-item"><input type="text" name="innerContainerRate" size="5" id="innerContainerRate" value="<?=$innerContainerRate?>" style="text-align:right; border:none;" readonly></td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >Inner Packing</td>
					  		<td class="listing-item"><input type="text" name="innerPackingRate" size="5" id="innerPackingRate" value="<?=$innerPackingRate?>" style="text-align:right; border:none;" readonly></td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >Inner Sample</td>
					  		<td class="listing-item">
							<input type="text" name="innerSampleRate" size="5" id="innerSampleRate" value="<?=$innerSampleRate?>" style="text-align:right; border:none;" readonly>				
							</td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >Inner Labeling</td>
					  		<td class="listing-item"><input type="text" name="innerLabelingRate" size="5" id="innerLabelingRate" value="<?=$innerLabelingRate?>" style="text-align:right; border:none;" readonly></td>
							</tr>
							<tr>
					  		<td class="fieldName" nowrap >Inner Leaflet</td>
					  		<td class="listing-item"><input type="text" name="innerLeafletRate" size="5" id="innerLeafletRate" value="<?=$innerLeafletRate?>" style="text-align:right; border:none;" readonly></td>
							</tr>
							<tr>
						<td class="fieldName" nowrap >Inner Sealing</td>
					 	<td class="listing-item"><input type="text" name="innerSealingRate" size="5" id="innerSealingRate" value="<?=$innerSealingRate?>" style="text-align:right; border:none;" readonly></td>
						</tr>						
						</table>	
					</td>
					<td valign="top">
					<table>	
						<tr>
					  	<td class="fieldName" nowrap >Pkg labour Rate</td>
					  	<td class="listing-item"><input type="text" name="pkgLabourRate" size="5" id="pkgLabourRate" value="<?=$pkgLabourRate?>" style="text-align:right; border:none;" readonly></td>
						</tr>
						<tr>
					  	<td class="fieldName" nowrap >INNER PACKING COST</td>
					  <td class="listing-item"><input type="text" name="innerPkgCost" size="7" id="innerPkgCost" value="<?=$innerPkgCost?>" style="text-align:right; border:none; font-weight:bold;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Master Packing</td>
					  <td class="listing-item"><input type="text" name="masterPackingRate" size="5" id="masterPackingRate" value="<?=$masterPackingRate?>" style="text-align:right; border:none;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >Master Sealing</td>
					  <td class="listing-item"><input type="text" name="masterSealingRate" size="5" id="masterSealingRate" value="<?=$tapeCostPerMc?>" style="text-align:right; border:none;" readonly></td>
					</tr>
					<tr>
					  <td class="fieldName" nowrap >OUTER PACKING COST</td>
					  <td class="listing-item"><input type="text" name="outerPackingCost" size="5" id="outerPackingCost" value="<?=$outerPackingCost?>" style="text-align:right; border:none; font-weight:bold;" readonly></td>
					</tr>					
					</table>
					</td>
<!--  Third Column End Here-->
					</tr>						
					</table></td>
					</tr>
					<tr>
						<td colspan="2"  height="10" ></td>
					</tr>
					<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingMatrix.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePackingMatrix(document.frmPackingMatrix);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PackingMatrix.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePackingMatrix(document.frmPackingMatrix);">												</td>
												<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>	
							<?php
								require("template/rbBottom.php");
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
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
		<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Matrix  </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$pkgMatrixRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPackingMatrix.php',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;" >
<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($pkgMatrixRecordSize) {
			$i	=	0;
		?>
		<thead>
	<? if($maxpage>1){?>
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
	  </div> </td>
	</tr>
	<? }?>
	<tr align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Code</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Inner Packing Cost</th>	
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Outer Packing Cost</th>	
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?
		while ($pmr=$pkgMatrixResultSetObj->getRow()) {
			$i++;
			$pkgMatrixRecId 	= $pmr[0];
			$pmCode			= $pmr[1];
			$pmName			= $pmr[2];
			$numOfPacksMC		= $pmr[10];
			$innerPackingCost 	= $pmr[19];					
			$outerPackingCost 	= $pmr[22];					
	?>
	<tr>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$pkgMatrixRecId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$pmCode?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$pmName?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$innerPackingCost?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$outerPackingCost?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$pkgMatrixRecId;?>,'editId'); this.form.action='PackingMatrix.php';" ><? } ?></td>
		<? }?>
	</tr>
		<?
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
	<? if($maxpage>1){?>
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
	  </div> </td>
	</tr>
	<? }?>
	<?
		} else {
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
										</table>									</td>
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
	<? if ($iFrameVal=="") { ?>
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