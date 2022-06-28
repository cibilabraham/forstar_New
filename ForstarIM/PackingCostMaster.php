<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editPackingCostMasterRecId	=	"";
	$packingCostMasterId	=	"";
	$noRec			=	"";
	$editMode		=	true;
	$addMode		=	false;
	
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------


	
	#----------------Rate list--------------------	
	$packingCostMaster = "PCM";
	if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
	else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
	else $selRateList = $manageRateListObj->latestRateList($packingCostMaster);			
	#--------------------------------------------

	#Update / Insert a Record
	if ($p["cmdSaveChange"]!="") {
		
		$packingCostMasterId = $p["hidPackingCostMasterId"];

		$vatRateForPackingMaterial = $p["vatRateForPackingMaterial"];
		$innerCartonWastage	   = $p["innerCartonWastage"];
		$tempIncreaseFactor		   = $p["tempIncreaseFactor"];
		$costOfGum		   = $p["costOfGum"];
		$noOfMcsPerTapeRoll	   = $p["noOfMcsPerTapeRoll"];
		$costOfTapeRoll		   = $p["costOfTapeRoll"];
		$tapeCostPerMc		   = $p["tapeCostPerMc"];
		$shrinkFilm		   = $p["shrinkFilm"];
		$ratePerGlassBottle= $p["ratePerGlassBottle"];
		$ratePerBottle= $p["ratePerBottle"];
		$ratePerRetortPouch= $p["ratePerRetortPouch"];
		$sealingCostGlassBottles= $p["sealingCostGlassBottles"];
		$sealingCost			= $p["sealingCost"];
		$sealingCostPetBottles	= $p["sealingCostPetBottles"];
		$loadingCharges	= $p["loadingCharges"];
		$transportCostNSWE	= $p["transportCostNSWE"];
		$transportCostNE	= $p["transportCostNE"];
		$plcRateList = $p["hidPLCRateList"];	
		$pscRateList = $p["hidPSCRateList"];
		$pmcRateList = $p["hidPMCRateList"];
		#packing Cost Master
		if ($packingCostMasterId!="") {
			$packingCostMasterRecUptd = $packingCostMasterObj->updatePackingCostMaster($packingCostMasterId, $vatRateForPackingMaterial, $innerCartonWastage, $costOfGum, $noOfMcsPerTapeRoll, $costOfTapeRoll, $tapeCostPerMc, $plcRateList, $pscRateList, $pmcRateList, $selRateList,$tempIncreaseFactor,$shrinkFilm,$ratePerGlassBottle,$ratePerBottle,$ratePerRetortPouch,$sealingCostGlassBottles,$sealingCost,$sealingCostPetBottles,$loadingCharges,$transportCostNSWE,$transportCostNE);
		} else {	
			//echo "hii";
			$packingCostMasterRecUptd = $packingCostMasterObj->addPackingCostMaster($vatRateForPackingMaterial, $innerCartonWastage, $costOfGum, $noOfMcsPerTapeRoll, $costOfTapeRoll, $tapeCostPerMc, $plcRateList, $pscRateList, $pmcRateList, $selRateList,$tempIncreaseFactor,$shrinkFilm,$ratePerGlassBottle,$ratePerBottle,$ratePerRetortPouch,$sealingCostGlassBottles,$sealingCost,$sealingCostPetBottles,$loadingCharges,$transportCostNSWE,$transportCostNE);	
		}

	/*	# Find the Current Rate List Id
		$cRateListId	=  $manageRateListObj->latestRateList($packingCostMaster);
		if ($packingCostMasterId!="" && $selRateList==$cRateListId) {
			# List all Packing Matrix
			$pkgMatrixResultSetObj = $packingMatrixObj->fetchAllRecords();
			//echo "hhhhhhhhhhhhhhhh=".$pkgMatrixResultSetObj->getNumRows();
			$packingLabourCost = "PLC";
			$plcRateList = $manageRateListObj->latestRateList($packingLabourCost);

			$packingSealingCost = "PSC";
			$pscRateList = $manageRateListObj->latestRateList($packingSealingCost);

			$packingMaterialCost = "PMC";
			$pmcRateList = $manageRateListObj->latestRateList($packingMaterialCost);

			$totalInnerpackingCost = "";
			$calcMasterPkgRate	= "";
			$totalOuterpackingCost = "";
			while ($pmr=$pkgMatrixResultSetObj->getRow()) {
				$pkgMatrixRecId 	= $pmr[0];						
				$noOfPacksInMC		= $pmr[10];						
				$productType		= $pmr[23];			
				list($innerContainerId, $innerContainerRate) = $packingCostMasterObj->getInnerPackingRate($pmcRateList);
				list($innerPackingId, $innerPackingRate) =  $packingCostMasterObj->getInnerPackingRate($pmcRateList);
				list($innerSampleId, $innerSampleRate) =  $packingCostMasterObj->getInnerPackingRate($pmcRateList);
				list($innerLabelingId, $innerLabelingRate) =  $packingCostMasterObj->getInnerPackingRate($pmcRateList);
				list($innerLeafletId, $innerLeafletRate) =  $packingCostMasterObj->getInnerPackingRate($pmcRateList);
				list($innerSealingId, $innerSealingRate) =  $packingCostMasterObj->getInnerSealingCost($pscRateList);
				list($pkgLabourRateId, $pkgLabourRate) =  $packingCostMasterObj->getPackingLabourCost($plcRateList);		
				list($masterPackingId, $masterPkgRate) = $packingCostMasterObj->getInnerPackingRate($pmcRateList);			
				//echo "$masterPackingId, $masterPkgRate";
				$innerContainerQty	= $pmr[24];
				$innerPackingQty	= $pmr[25];
				$innerSampleQty		= $pmr[26];
				$innerLabelingQty	= $pmr[27];
				$innerLeafletQty	= $pmr[28];
				$innerSealingQty	= $pmr[29];
				$pkgLabourRateQty	= $pmr[30];

				if ($productType=='CP') {
					$innerContainerRate = number_format(($innerContainerQty*$innerContainerRate),2,'.','');
					$innerPackingRate	=  number_format(($innerPackingQty*$innerPackingRate),2,'.','');
					$innerSampleRate	=  number_format(($innerSampleQty*$innerSampleRate),2,'.','');
					$innerLabelingRate	=  number_format(($innerLabelingQty*$innerLabelingRate),2,'.','');
					$innerLeafletRate	=  number_format(($innerLeafletQty*$innerLeafletRate),2,'.','');
					$innerSealingRate	=  number_format(($innerSealingQty*$innerSealingRate),2,'.','');
					$pkgLabourRate		=  number_format(($pkgLabourRateQty*$pkgLabourRate),2,'.','');
				}	

				# Find Inner Packing Cost
				$totalInnerpackingCost = $innerContainerRate+$innerPackingRate+$innerSampleRate+$innerLabelingRate+$innerLeafletRate+$innerSealingRate+$pkgLabourRate;	
				$innerPkgCost		= number_format($totalInnerpackingCost,2,'.','');	
				# Calc Master Packing Rate
				$calcMasterPkgRate = $masterPkgRate/$noOfPacksInMC;
				$masterPackingRate	= number_format($calcMasterPkgRate,2,'.','');
				$masterSealingRate	= number_format($tapeCostPerMc,2,'.','');
				# Calc Outer Packing Cost
				$totalOuterpackingCost = $masterPackingRate+$masterSealingRate;
				$outerPackingCost	= number_format($totalOuterpackingCost,2,'.','');	

				# Update Packing Matrix
				$pkgMatrixRecUptd = $packingCostMasterObj->updatePackingMatrix($pkgMatrixRecId, $innerContainerId, $innerPackingId, $innerSampleId, $innerLabelingId, $innerLeafletId, $innerSealingId, $pkgLabourRateId, $masterPackingId, $innerContainerRate, $innerPackingRate, $innerSampleRate, $innerLabelingRate, $innerLeafletRate, $innerSealingRate, $pkgLabourRate, $innerPkgCost, $masterPackingRate, $masterSealingRate, $outerPackingCost, $innerContainerQty, $innerPackingQty, $innerSampleQty, $innerLabelingQty, $innerLeafletQty, $innerSealingQty, $pkgLabourRateQty, $userId);
				
			}
		}*/

		if ($packingCostMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPackingCostMasterUpdate);
			//$sessObj->createSession("nextPage",$url_afterUpdateCompany);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPackingCostMasterUpdate;
		}
		$packingCostMasterRecUptd		=	false;
	}
	
	
	# Edit 
		$packingCostMasterRec	    =	$packingCostMasterObj->find($selRateList);
		$editPackingCostMasterRecId =	$packingCostMasterRec[0];
		$vatRateForPackingMaterial  = $packingCostMasterRec[1];
		$innerCartonWastage	    = $packingCostMasterRec[2];
		$costOfGum		   = $packingCostMasterRec[3];
		$noOfMcsPerTapeRoll	   = $packingCostMasterRec[4];
		$costOfTapeRoll		   = $packingCostMasterRec[5];
		$tapeCostPerMc		   = $packingCostMasterRec[6];	
		$plcRateListId		   = $packingCostMasterRec[7];			
		$pscRateListId		   = $packingCostMasterRec[8];				
		$pmcRateListId		   = $packingCostMasterRec[9];	
		$tempIncreaseFactor		   = $packingCostMasterRec[11];	
		$shrinkFilm		   = $packingCostMasterRec[12];	
		$ratePerGlassBottle		   = $packingCostMasterRec[13];	
		$ratePerBottle		   = $packingCostMasterRec[14];	
		$ratePerRetortPouch		   = $packingCostMasterRec[15];	
		$sealingCostGlassBottles		   = $packingCostMasterRec[16];	
		$sealingCost		   = $packingCostMasterRec[17];	
		$sealingCostPetBottles		   = $packingCostMasterRec[18];	
		$loadingCharges		   = $packingCostMasterRec[19];	
		//$transportCostNSWE		   = $packingCostMasterRec[20];	
		//$transportCostNE		   = $packingCostMasterRec[21];	
		

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$packingCostMasterId = $p["hidPackingCostMasterId"];
		if ($packingCostMasterId!="") {
			// Need to check the selected Category is link with any other process
			$prodMatrixRecDel = $packingCostMasterObj->deletePackingCostMasterRec($packingCostMasterId);
		}
		
		if ($prodMatrixRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPackingCostMaster);
			$sessObj->createSession("nextPage",$url_afterDelPackingCostMaster);
		} else {
			$errDel	=	$msg_failDelPackingCostMaster;
		}
		$prodMatrixRecDel	=	false;
	}

/*
	# List all Packing labour Cost
	$packingLabourCost = "PLC";
	if ($p["selRateList"]=="") $plcRateList = $manageRateListObj->latestRateList($packingLabourCost);
	else $plcRateList = $plcRateListId;
	$packingLabourCostResultSetObj = $packingLabourCostObj->fetchAllRecords($plcRateList);

	# List all Sealing Cost
	$packingSealingCost = "PSC";
	if ($p["selRateList"]=="") $pscRateList = $manageRateListObj->latestRateList($packingSealingCost);
	else $pscRateList = $pscRateListId;
	$packingSealingCostResultSetObj = $packingSealingCostObj->fetchAllRecords($pscRateList);

	# List all Packing Material Cost
	$packingMaterialCost = "PMC";
	if ($p["selRateList"]=="") $pmcRateList = $manageRateListObj->latestRateList($packingMaterialCost);
	else $pmcRateList = $pmcRateListId;
	$packingMaterialCostResultSetObj = $packingMaterialCostObj->fetchAllRecords($pmcRateList);

*/
	# Rate List
	$pcmRateListRecords = $manageRateListObj->fetchAllRecords($packingCostMaster);

	$ON_LOAD_PRINT_JS = "libjs/PackingCostMaster.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
<form name="frmPackingCostMaster" action="PackingCostMaster.php" method="post">
<table cellspacing="0"  align="center" cellpadding="0" width="96%">
	<tr><TD height="5"></TD></tr>
	<!--
	<tr><td height="10" align="center"><a href="PackingLabourCost.php" class="link1">Packing Labour Cost</a>&nbsp;&nbsp;<a href="PackingSealingCost.php" class="link1">Packing Sealing Cost</a>&nbsp;&nbsp;<a href="PackingMaterialCost.php" class="link1">Packing Material Cost</a></td></tr>
	-->
	<tr><TD height="5"></TD></tr>
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1" ><?=$err;?></td>
	</tr>
	<?}?>
		
<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Packing Cost Master";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="60%">
	<?
		if ( $editMode || $addMode) {
	?>
<tr>
		<td colspan="3" align="center">
			<table width="35%">
				<TR>
					<TD>
					<?php	
					/*
						$entryHead = "";
						require("template/rbTop.php");
					?>
					<table cellpadding="3" cellspacing="3">
					  <tr>
					<td nowrap="nowrap" style="padding:5px;">
					<table width="200" border="0">
                  <tr>
                    <td class="fieldName" nowrap>Rate List </td>
                    <td nowrap>
		<select name="selRateList" id="selRateList" onchange="this.form.submit();">
                <option value="">-- Select --</option>
                <?
		foreach ($pcmRateListRecords as $prl) {
			$mRateListId	= $prl[0];
			$rateListName	= stripSlash($prl[1]);
			$startDate	= dateFormat($prl[2]);
			$displayRateList = $rateListName."&nbsp;(".$startDate.")";
			$selected =  ($selRateList==$mRateListId)?"Selected":"";
		?>
                <option value="<?=$mRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                 <? }?>
                </select></td>
		   <? if($add==true){?>
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='ManageRateList.php?mode=AddNew&selPage=<?=$packingCostMaster?>'"></td>
		<? }?>		
                  </tr>
                </table>
		</td></tr>
	</table>
		<?php
			require("template/rbBottom.php");
			*/
		?>
		</td>
		</tr>
		</table>
				</td>
			</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
				<tr>
					<td>
						<!-- Form fields start -->
						<?php							
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<!--<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Cost Master  </td>
							</tr>-->
							<tr>
								<td width="1" ></td>
							  <td colspan="2" >
							    <table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
										<tr>
											<td colspan="2" height="10" ></td>
										</tr>
										<tr>
											<? if($editMode){?>
											<td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;  <input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validatePackingCostMaster(document.frmPackingCostMaster)">	<? }?>										</td>
											
											<?} else{?>

										  <td align="center">&nbsp;&nbsp;</td>

											<?}?>
										</tr>
	<input type="hidden" name="hidPackingCostMasterId" value="<?=$editPackingCostMasterRecId;?>" />
	<tr>
		<td colspan="2" nowrap height="10"></td>
	</tr>
		<tr>
			<td colspan="2" nowrap style="padding-left:10px; padding-right:10px;">
	<table width="200" align="center">
          <tr>
              	<td nowrap >
		<table cellpadding="1" cellspacing="1" id="newspaper-b1">
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">HEAD</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>				
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Vat Rate for Packing Material</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"><input type="text" name="vatRateForPackingMaterial" id="vatRateForPackingMaterial" size="5" value="<?=$vatRateForPackingMaterial?>" style="text-align:right" onkeypress="return isNumber (event);" >&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>				
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Inner Carton wastage</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"><input type="text" name="innerCartonWastage" id="innerCartonWastage" size="5" style="text-align:right" value="<?=$innerCartonWastage?>"  autoComplete="off" onkeypress="return isNumber (event);" >&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Temporary Increase Factor (Buffer)</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"><input type="text" name="tempIncreaseFactor" id="tempIncreaseFactor" size="5" style="text-align:right" value="<?=$tempIncreaseFactor?>"  autoComplete="off" onkeypress="return isNumber (event);" >&nbsp;%</td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Cost of 500 Ml Gum</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="costOfGum" id="costOfGum" size="5" style="text-align:right" value="<?=$costOfGum?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">No of MCs per Tape Roll - 65 mtr</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="noOfMcsPerTapeRoll" id="noOfMcsPerTapeRoll" size="5" style="text-align:right" value="<?=$noOfMcsPerTapeRoll?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Cost of Tape Roll</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="costOfTapeRoll" id="costOfTapeRoll" size="5" style="text-align:right" value="<?=$costOfTapeRoll?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Tape Cost per MC (18 MCs per Roll) </TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="tapeCostPerMc" id="tapeCostPerMc" size="5" style="text-align:right" value="<?=$tapeCostPerMc?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">Shrink Film + elect cost per Dispenser </TD>
				<td nowrap style="padding-left:5px; padding-right:5px;"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="shrinkFilm" id="shrinkFilm" size="5" style="text-align:right" value="<?=$shrinkFilm?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>
			</TR>
			
			
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">LABOUR RATE</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>				
			</TR>	
			
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				<input type="hidden" name="labrCostRecId_<?=$i?>" id="labrCostRecId_<?=$i?>" value="">Rate per Glass Bottle</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="ratePerGlassBottle" id="ratePerGlassBottle" size="5" style="text-align:right" value="<?=$ratePerGlassBottle?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>											
			
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				<input type="hidden" name="labrCostRecId_<?=$i?>" id="labrCostRecId_<?=$i?>" value="">Rate per PET Bottle</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="ratePerBottle" id="ratePerBottle" size="5" style="text-align:right" value="<?=$ratePerBottle?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>	
			
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				<input type="hidden" name="labrCostRecId_<?=$i?>" id="labrCostRecId_<?=$i?>" value="">Rate per Retort Pouch</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="ratePerRetortPouch" id="ratePerRetortPouch" size="5" style="text-align:right" value="<?=$ratePerRetortPouch?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>	
			
<!--  Sealing Cost-->
			<TR>
				<Th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">SEALING COST</Th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">CODE</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">COST</th>
				<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">UNIT</th>				
			</TR>	
			
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				Sealing Cost for Glass Bottles</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="sealingCostGlassBottles" id="sealingCostGlassBottles" size="5" style="text-align:right" value="<?=$sealingCostGlassBottles?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				Sealing Cost (3000packs/500ml gum) for IC</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="sealingCost" id="sealingCost" size="5" style="text-align:right" value="<?=$sealingCost?>" onkeypress="return isNumber (event);"  ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>	
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				Sealing Cost for PET Bottles</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="sealingCostPetBottles" id="sealingCostPetBottles" size="5" style="text-align:right" value="<?=$sealingCostPetBottles?>" onkeypress="return isNumber (event);"  ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>	
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				Loading Charges per 20kG MC</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="loadingCharges" id="loadingCharges" size="5" style="text-align:right" value="<?=$loadingCharges?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>	
			<!--<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				Transport Cost per Gr Kg - NSWE</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="transportCostNSWE" id="transportCostNSWE" size="5" style="text-align:right" value="<?=$transportCostNSWE?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>	
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;">
				Transport Cost per Gr Kg - NE</TD>
				<td nowrap style="padding-left:5px; padding-right:5px;" class="listing-item"></td>
				<td nowrap style="padding-left:5px; padding-right:5px;"><input type="text" name="transportCostNE" id="transportCostNE" size="5" style="text-align:right" value="<?=$transportCostNE?>" onkeypress="return isNumber (event);" ></td>
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center"></td>	
			</TR>-->	
			
			

		</table>
		</td>
          </tr>
          </table></td>
	  </tr>
	<tr>
		<td colspan="4"  height="10" ></td>
	</tr>
	<tr>
	<? if($editMode){?>
  	<td colspan="2" align="center"><? if($edit==true){?>&nbsp;&nbsp;
	<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick=" return validatePackingCostMaster(document.frmPackingCostMaster);"><? }?></td>
	<?} else{?>
  	<td align="center">&nbsp;&nbsp;</td>

											<?}?>
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
		
		# Listing LandingCenter Starts
	?>
			</table>
		</td>
	</tr>
	<tr>
				<td height="10" align="center" ></td>
	</tr>				
	<input type='hidden' name="hidPLCRateList" value="<?=$plcRateList?>">
	<input type='hidden' name="hidPSCRateList" value="<?=$pscRateList?>">
	<input type='hidden' name="hidPMCRateList" value="<?=$pmcRateList?>">
		<tr>
			<td height="10"></td>
		</tr>		
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
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
	<tr><TD height="10"></TD></tr>
	<!--
	<tr><td align="center"><a href="PackingLabourCost.php" class="link1">Packing Labour Cost</a>&nbsp;&nbsp;<a href="PackingSealingCost.php" class="link1">Packing Sealing Cost</a>&nbsp;&nbsp;<a href="PackingMaterialCost.php" class="link1">Packing Material Cost</a></td></tr>-->	
	</table>
<? 
	if ($iFrameVal=="") { 
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
	ensureInFrameset(document.frmPackingCostMaster);
	//-->
	</script>
<? 
	}
?>
</form>
<?php
# Include Template [bottomRightNav.php]
if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>