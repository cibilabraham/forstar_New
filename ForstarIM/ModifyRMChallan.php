<?php
	require("include/include.php");
	require_once('lib/dailycatchentry_ajax.php');
	$err			=	"";
	$errDel			=	"";
	$addMode		=	false;
	$editMode		=	true;
	$searchMode		= 	false;
	$currentUserId		=	$sessObj->getValue("userId");
	$dateSelection = "?weighNumber=".$p["weighNumber"]."&selFish=".$p["selFish"]."&selProcesscode=".$p["selProcesscode"]."&selEntry=".$p["selEntry"];

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
		header("Location: ErrorPage.php");
		die();
	}
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	//----------------------------------------------------------

	if ($g["billingCompany"]!="") $billingCompany = $g["billingCompany"];
	else $billingCompany 	= $p["billingCompany"];

	if ($g["weighNumber"]!="") $weighNumber = trim($g["weighNumber"]);
	else $weighNumber 	= trim($p["weighNumber"]);
	
	if ($g["selFish"]!="") $selFish = $g["selFish"];
	else $selFish       = $p["selFish"];

	if ($g["selProcesscode"]!="") $selProcesscode = $g["selProcesscode"];
	else $selProcesscode = $p["selProcesscode"];
	
	if ($g["selEntry"]!="") $selEntry = $g["selEntry"];
	else $selEntry	= $p["selEntry"];
	
	/* Get Main Id and Catch Entry Id*/
	if ($weighNumber!="" && $selFish!="" && $selProcesscode!="") {
		//$weighNumber, $selFish, $selProcesscode
		list($editId,$dailyCatchentryId) = $dailycatchentryObj->findRMEntryRec($selEntry);
		if ($editId!="" && $dailyCatchentryId) $searchMode= true;
	}
	
	if ($p["cmdCancel"]!="") {
		$editId = "";
		$dailyCatchentryId	=	"";
		$weighNumber		= "";
		$selFish		=	"";
		$selProcesscode		= "";
		$editMode		=	true;
		$searchMode		= 	false;
		$selEntry		= "";
	}
		
	if ($p["cmdAddRawSelChallan"]!="") {
		$lastId	=	$p["selWtChallan"];
	} else if($p['editMode'] == "1"){
		$lastId	=	$p["enteredRMId"];
	} else {
		$lastId	=	$p["entryId"];
	}
	
	# Edit a Daily catch entry 
	if (($p["cmdSearch"]!="" && $p["cmdCancel"]=="") || $searchMode==true) {
				
		$catchEntryRec			=	$dailycatchentryObj->find($editId,$dailyCatchentryId);	
		$recordId			=	$catchEntryRec[0];
		$recordUnit			=	$catchEntryRec[1];
		$recordDate			=	$catchEntryRec[2];

		$recordVechNo		=	$catchEntryRec[3];
		$recordChallanNo	=	$catchEntryRec[4];
		$recordWeighNo		=	$catchEntryRec[5];
		$recordLanding		=	$catchEntryRec[6];
		$recordMainSupply		=	$catchEntryRec[7];
		$recordSubSupply	=	$catchEntryRec[8];
		$recordFish		=	$catchEntryRec[9];
		$recordProcessCode	=	$catchEntryRec[10];
		$recordIceWt		=	$catchEntryRec[11];
		$recordCount		=	$catchEntryRec[12];
		$recordAverage		=	$catchEntryRec[13];
		$recordLocalQty		=	$catchEntryRec[14];
		$recordWastage		=	$catchEntryRec[15];
		$recordSoft		=	$catchEntryRec[16];
		$recordReason		=	$catchEntryRec[17];
		$recordAdjust		=	$catchEntryRec[18];
		$recordGood		=	$catchEntryRec[19];
		$recordPeeling		=	$catchEntryRec[20];
		$recordRemarks		=	$catchEntryRec[21];
		
		$entryActualWt		=	$catchEntryRec[22];
		$entryEffectiveWt	=	$catchEntryRec[23];
		$recordDeclWeight	=	$catchEntryRec[27];
		$recordDeclCount	=	$catchEntryRec[28];
		$eDate			=	explode("-",$catchEntryRec[29]);
		$recordSelectDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
		$recordGradeId		=	$catchEntryRec[30];
		$recordBasketWt		=	$catchEntryRec[31];
		$reasonLocal		=	$catchEntryRec[32];
		$reasonWastage		=	$catchEntryRec[33];
		$reasonSoft			=	$catchEntryRec[34];
		$entryOption		=	$catchEntryRec[35];
		$catchEntryNewId		=	$catchEntryRec[36];
		$netGrossWt			=	$catchEntryRec[26];
		$selectTime			=	explode("-",$catchEntryRec[37]);
		$selectTimeHour			=	$selectTime[0];
		$selectTimeMints		=	$selectTime[1];
		$timeOption 			= 	$selectTime[2];
		$paymentBy		=	$catchEntryRec[38];
		if ($paymentBy=='D') $checked="Checked";
		
		$gradeCountAdj		=	$catchEntryRec[39];
		$gradeCountAdjReason	=	$catchEntryRec[40];
		
		$supplierRecords	=	$supplierMasterObj->fetchSupplierRecords($recordLanding);
		$subSupplierRecords	=	$subsupplierObj->filterSubSupplierRecords($recordMainSupply, $recordLanding);
		$processCodeRecords	=	$processcodeObj->processCodeRecFilter($recordFish);
		$gradeMasterRecords	=	$processcodeObj->fetchGradeRecords($recordProcessCode);
		
		$processCodeRec		=	$processcodeObj->find($recordProcessCode);
		$receivedBy		=	$processCodeRec[7];
		
		$readOnly		= "readonly";
	}

	#Update a Record
	if ($p["cmdDailySaveChange"]!="") {
		
		$challanMainId		=	$p["hidDailyCatchId"];
		$challanEntryId		=	$p["hidCatchEntryNewId"];
		
		$entryAdjust		=	$p["entryAdjust"];
		$reasonAdjust		=	$p["reasonAdjust"];
		$gradeCountAdj		=	($p["gradeCountAdj"]=="")?0:$p["gradeCountAdj"];
		$gradeCountAdjReason	=	$p["gradeCountAdjReason"];
		$entryActualWt		=	$p["entryActualWt"];
		$entryLocal		=	$p["entryLocal"];
		$reasonLocal 		= 	$p["reasonLocal"];
		$entryWastage		=	$p["entryWastage"];
		$reasonWastage 		= 	$p["reasonWastage"];
		$entrySoft		=	$p["entrySoft"];
		$reasonSoft		= 	$p["reasonSoft"];
		/*
		$goodPack		=	$p["goodPack"];
		$peeling		=	$p["peeling"];
		$entryRemark		=	$p["entryRemark"];
		*/
		$entryEffectiveWt	=	$p["entryEffectiveWt"];
		$hidEntryEffectiveWt	= 	$p["hidEntryEffectiveWt"];
		
		
		if ($challanMainId!="" && $challanEntryId!="" && $entryEffectiveWt==$hidEntryEffectiveWt) {
			$updateRMChallanRec = $dailycatchentryObj->updateModifiedRMChallanEntryRec($challanMainId, $challanEntryId, $entryAdjust, $reasonAdjust, $gradeCountAdj, $gradeCountAdjReason, $entryActualWt, $entryLocal, $reasonLocal, $entryWastage, $reasonWastage, $entrySoft, $reasonSoft, $currentUserId);
			if ($updateRMChallanRec) {
				$sessObj->createSession("displayMsg",$msg_succDailyCatchUpdate);
				$url_afterUpdateModifiedDailyCatchRec = "ModifyRMChallan.php";
				$sessObj->createSession("nextPage", $url_afterUpdateModifiedDailyCatchRec.$dateSelection);
			} else {
				$editMode	=	true;
				$err		=	$msg_failDailyCatchUpdate;
			}
		} else {
			$err 	 = "Filed to update Daily Catch Entry Record";
			//if ($p['editMode']=="1") $editMode = true;
			//else $addMode = true;
		}
		//$DailyCatchEntryRecUptd	=	false;
	}

	
	#Edit mode set the Daily catch entry ID
	if ($p["editId"]!="" || $p["selRawMaterial"]!="") {
		$entryId	=	$recordId;
		$lastId		=	$recordId;
	}

	

	#count all Gross Records
	$countGrossRecords	=	$dailycatchentryObj->fetchAllGrossRecords($catchEntryNewId);
	if (sizeof($countGrossRecords)>0) {
		foreach ($countGrossRecords as $cgr) {
			$countGrossWt		=	$cgr[1];
			$totalWt		=	$totalWt+$countGrossWt;
			$countGrossBasketWt	=	$cgr[2];
			$grandTotalBasketWt	=	$grandTotalBasketWt + $countGrossBasketWt;
			$netGrossWt		=	$totalWt - $grandTotalBasketWt;
		}
	}

	
	#List All Plants
	//$plantRecords	=	$plantandunitObj->fetchAllRecords();

	#List all Landing Centers
	//$landingCenterRecords	=	$landingcenterObj->fetchAllRecords();

	#List All Fishes
	//$fishMasterRecords	=	$fishmasterObj->fetchAllRecords();

	# Get Billing Comapany  Records
	//$billingCompanyRecords = $billingCompanyObj->fetchAllRecords();

	#List All Plants
	$plantRecords	=	$plantandunitObj->fetchAllRecordsPlantsActive();

	#List all Landing Centers
	$landingCenterRecords	=	$landingcenterObj->fetchAllRecordsActiveLanding();

	#List All Fishes
	$fishMasterRecords	=	$fishmasterObj->fetchAllRecordsFishactive();

	# Get Billing Comapany  Records
	$billingCompanyRecords = $billingCompanyObj->fetchAllRecordsActivebillingCompany();


	/*
	#Filter Process Code
	if ($addRaw==true)  $processId = "";
	else $processId	=	$p["processCode"];
	*/

	if ($editMode==true) {
		$processCodeIdOnchange	=	$processcodeObj->processCodeRecIdFilter($recordProcessCode);
	} else if ($addMode==true) {
		$processCodeIdOnchange	=	$processcodeObj->processCodeRecIdFilter($processId);
	}

	if (sizeof($processCodeIdOnchange)>0) {
		foreach ($processCodeIdOnchange as $flr) {
			$processBasketWt	=	$flr[4];
		}
	}

	if ($p["cmdReset"]!="") {
		$processBasketWt = $p["dailyBasketWt"];
	}	
	
	
	if ($editMode) $heading	=	$label_editDailyCatchEntry;
	else $heading	=	$label_addDailyCatchEntry;
		
	//$help_lnk="help/hlp_DailyCatchEntry.html";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	$ON_LOAD_PRINT_JS = "libjs/ModifyRMChallan.js";
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");	
?>	
<form name="frmDailyCatch" id="frmDailyCatch" action="ModifyRMChallan.php" method="post">
 <table cellspacing="0"  align="center" cellpadding="0" width="90%">
	<td height="40" align="center"></td>
   <? if($err!="" ){?> <tr> 
      <td height="40" align="center" class="err1" ><?=$err;?></td>
    </tr><? }?>
    <?
		if( $editMode || $addMode) {
	?>
	
    <tr> 
      <td> 
	 <table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
	       <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
               <tr> 
                  <td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Modifying challan without changing effective wt
                  </td>
                </tr>
		<tr>
			<TD height="20"></TD>
		</tr>
		<tr>
			<TD align="center" colspan="2" style="padding-left:5px;padding-right:5px;">
				<table cellpadding="2" cellspacing="0" align="center">
                            <tr>
				<TD class="fieldName" nowrap="true">*Billing Company</TD>
				<td>					
					<select name="billingCompany" id="billingCompany" onchange="xajax_getChallanWiseFishList('selFish', document.getElementById('weighNumber').value, document.getElementById('selFish').value, document.getElementById('billingCompany').value); xajax_getChallanWiseProcessCodeList('selProcesscode', document.getElementById('weighNumber').value, document.getElementById('selFish').value, document.getElementById('selProcesscode').value, document.getElementById('billingCompany').value); xajax_getRMEntryRecords(document.getElementById('weighNumber').value, document.getElementById('selFish').value, document.getElementById('selProcesscode').value, document.getElementById('selEntry').value, document.getElementById('billingCompany').value);">
					<!--<option value="">-- Select --</option>-->
					<?php
					foreach ($billingCompanyRecords as $bcr) {
						$billingCompanyId	= $bcr[0];
						$cName			= $bcr[1];
						$defaultChk		= $bcr[10];
						$displayCName		= $bcr[9];
						$selected = "";
						if ($billingCompanyId==$billingCompany || ($billingCompany=="" && $defaultChk=='Y') ) $selected = "selected";
					?>
					<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
					<?	
					}	
					?>
					</select>
				</td>
                              <td class="fieldName" nowrap="nowrap">* Weighment Challan:</td>
                              <td nowrap="nowrap">
				  <input name="weighNumber" type="text" id="weighNumber" size="8" value="<?=$weighNumber?>" onchange="xajax_getChallanWiseFishList('selFish', document.getElementById('weighNumber').value, document.getElementById('selFish').value, document.getElementById('billingCompany').value); xajax_getChallanWiseProcessCodeList('selProcesscode', document.getElementById('weighNumber').value, document.getElementById('selFish').value, document.getElementById('selProcesscode').value, document.getElementById('billingCompany').value); xajax_getRMEntryRecords(document.getElementById('weighNumber').value, document.getElementById('selFish').value, document.getElementById('selProcesscode').value, document.getElementById('selEntry').value, document.getElementById('billingCompany').value);" autocomplete="off">&nbsp;&nbsp;</td>
				<td class="fieldName" style="padding-left:5px;" nowrap>* Fish:</td>
				<td>
					<select name="selFish" id="selFish" onchange="xajax_getChallanWiseProcessCodeList('selProcesscode', document.getElementById('weighNumber').value, document.getElementById('selFish').value, document.getElementById('selProcesscode').value, document.getElementById('billingCompany').value); xajax_getRMEntryRecords(document.getElementById('weighNumber').value, document.getElementById('selFish').value, document.getElementById('selProcesscode').value, document.getElementById('selEntry').value, document.getElementById('billingCompany').value);"><option value="">--Select All--</option>
					</select></td>
				<td class="fieldName" style="padding-left:5px;" nowrap>* Processcode:</td>
				<td>
					<select name="selProcesscode" id="selProcesscode" onchange="xajax_getRMEntryRecords(document.getElementById('weighNumber').value, document.getElementById('selFish').value, document.getElementById('selProcesscode').value, document.getElementById('selEntry').value, document.getElementById('billingCompany').value);">
					<option value="">--Select All--</option>
					</select>
				</td>
				<td><div id="displayCG"></div></td>
				 <td>
				<input type="submit" name="cmdSearch" value=" Search" class="button" onclick="return validateModifyRMChallanSearch();" /></td>
                            </tr>				
                                  </table>
			</TD>
		</tr>
		<tr>
			<TD height="15"></TD>
		</tr>
<!--  New Table Starts-->
<? if ($searchMode==true) {?>
		<tr>
			<TD>
				<table>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" >
  <table cellpadding="0"  width="81%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td width="18%" height="10" ></td>
    </tr>
	<tr>
		<TD width="18%">
			<table align="center">
				<TR>
					<TD>
					<? if ($edit==true || $reEdit==true) {?>
						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('ModifyRMChallan.php');" />
					        &nbsp;&nbsp;
					        <input type="submit" name="cmdDailySaveChange" class="button" value=" Save Changes " onclick="return validateModifyRMChallan();" />
					<? }?>
					</TD>
				</TR>
			</table>
		</TD>
	</tr>
    <!--<tr>
      <td align="center">&nbsp;</td>
      <? if($editMode){?>
      <td width="24%" align="center" nowrap="nowrap"><input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('ModifyRMChallan.php');" />
        &nbsp;&nbsp;
        <input type="submit" name="cmdDailySaveChange" class="button" value=" Save Changes " onclick="return validateModifyRMChallan();" /></td>
      <?} else{?>
      <td width="29%" align="center" nowrap="nowrap"><input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onclick="return cancel('ModifyRMChallan.php');" />
        &nbsp;&nbsp;
        <input type="submit" name="cmdAddDailyCatch" class="button" value=" Save & Exit " onclick="return validateAddDailyCatchEntry(document.frmDailyCatch);" />
		&nbsp;&nbsp;
		<input type="submit" name="cmdAddNewChallan" value="save & Add New Challan" class="button" onclick="return recordSaved(document.frmDailyCatch);" style="width:150px;" /></td>
		<?}?>
  </tr>-->
    <input type="hidden" name="hidDailyCatchId" value="<?=$recordId;?>" />
	<input type="hidden" name="hidCatchEntryNewId" value="<?=$catchEntryNewId?>">
    <tr>
      <td colspan="4" nowrap class="fieldName" >
	<table width="100%" border="0" cellpadding="4" cellspacing="2" align="center">
          <tr>
            <td width='21%' valign="top">
		<fieldset><legend class="listing-item">Supplier Challan Details</legend>
		<table>
                <tr>
                  <td align="left"  class="fieldName" >Unit:</td>
                  <td align="left">
			<? if($addMode==true){ 
				if($p["unit"]!="") $unit	=	$p["unit"]; 
			}
			?>
                      <select name="unit" id="unit" tabindex="1" onkeypress="return focusNextBox(event,'document.frmDailyCatch','landingCenter');" disabled>
                        <option value="">-- Select --</option>
                        <? foreach($plantRecords as $pr)
				{
					$i++;
					$plantId		=	$pr[0];
					$plantNo		=	stripSlash($pr[1]);
					$plantName		=	stripSlash($pr[2]);
					$selected="";
					if ($plantId	== $recordUnit || $plantId== $unit) {
						$selected	=	"selected";
					}
			?>
                        <option value="<?=$plantId?>" <?=$selected?>> <?=$plantName?> </option>
                        <? }?>
                      </select></td>
                </tr>
                <tr>
                  <td class="fieldName" nowrap>Landing Center:</td>
                  <td><? if($addMode==true){ 
							if($p["landingCenter"]!="") $landingCenter		=	$p["landingCenter"];
						$supplierRecords	=	$supplierMasterObj->fetchSupplierRecords($landingCenter);
					?>
                      <select name="landingCenter" id="landingCenter" tabindex="2" onkeypress="return focusNextBox(event,'document.frmDailyCatch','mainSupplier');" onchange="this.form.submit();" disabled>
					  <? } else { ?>
<select name="landingCenter" id="landingCenter" tabindex="2"  onchange="this.form.editId.value=<?=$editId;?>;this.form.submit();" onkeypress="return focusNextBox(event,'document.frmDailyCatch','mainSupplier');" disabled>
					  <? }?>
                        <option value="">--Select--</option>
                        <?
			foreach ($landingCenterRecords as $fr)	{
				$i++;
				$centerId	=	$fr[0];
				$centerName	=	stripSlash($fr[1]);
				$centerCode	=	stripSlash($fr[2]);
				$centerDesc =	stripSlash($fr[3]);
				$selected="";
				if ($centerId==$recordLanding || $centerId==$landingCenter) {
					$selected	=	"selected";
				}
			?>
                        <option value="<?=$centerId?>" <?=$selected?>> <?=$centerName?> </option>
                        <? } ?>
                      </select>                  </td>
                </tr>
                <tr>
                  <td class="fieldName" nowrap>Main Supplier:</td>
                  <td><? 
				  	if($addMode==true){ 
						if($p["mainSupplier"]!="") $mainSupplier =	$p["mainSupplier"];
						$subSupplierRecords		=	$subsupplierObj->filterSubSupplierRecords($mainSupplier,$landingCenter);						
						# Get Payment By
						$paymentBy = $supplierMasterObj->getSupplierPaymentBy($mainSupplier);
						//edited on 02-11-07 Nov
						$checked = "";
						if ($paymentBy=='D') {
							$checked = "Checked";
						}
						?>
                      <select name="mainSupplier" id="mainSupplier" onchange="this.form.submit();" tabindex="3" onkeypress="return focusNextBox(event,'document.frmDailyCatch','subSupplier');" disabled>
                        <? } else {?>
                        <select name="mainSupplier" id="mainSupplier" onchange="this.form.editId.value=<?=$editId?>; this.form.submit();" onkeypress="return focusNextBox(event,'document.frmDailyCatch','subSupplier');" disabled>
                        <? }?>
                        <option value="">--select--</option>
                        <?
											foreach($supplierRecords as $fr)
													{
														$i++;
														$supplierId		=	$fr[1];
														$supplierName	=	stripSlash($fr[4]);
														$selected	=	"";
															if( $supplierId == $recordMainSupply ||$supplierId==$mainSupplier){
																	$selected	=	"selected";
																}
														
													?>
                        <option value="<?=$supplierId?>" <?=$selected?>>
                        <?=$supplierName?>
                        </option>
                        <? } ?>
                      </select>                  </td>
                </tr>
                
                <tr>
                  <td class="fieldName">Sub Supplier:</td>
                  <td><? if($addMode==true){ 
								if($p["subSupplier"]!="") $subSupplier	=	$p["subSupplier"];
							} ?>
                      <select name="subSupplier" tabindex="4" onkeypress="return focusNextBox(event,'document.frmDailyCatch','vechicleNo');" disabled>
                        <option value="">SELF</option>
                        <?
			  foreach ($subSupplierRecords as $fr) {
				$i++;
				$subSupplierId	=	$fr[0];
				$subSupplierName	=	stripSlash($fr[1]);
				$subSupplierCode	=	stripSlash($fr[2]);
				$mainSupplierCode	=	stripSlash($fr[3]);
				$selected	=	"";
				if ($subSupplierId == $recordSubSupply || $subSupplierId==$subSupplier) {
					$selected	=	"selected";
				}
			?>
                        <option value="<?=$subSupplierId?>" <?=$selected?>> <?=$subSupplierName?> </option>
                        <? }?>
                      </select></td>
                </tr>
		 <tr>
                  <td class="fieldName">Vehicle No:</td>
                  <td><? if($addMode==true) {
			if ($p["vechicleNo"]!="") $recordVechNo	= $p["vechicleNo"];
			}
			?>
                      <input type="text" name="vechicleNo" size="20" value="<?=$recordVechNo;?>" tabindex="5" onkeypress="return focusNextBox(event,'document.frmDailyCatch','supplyChallanNo');" readonly="true" style="border:none;" /></td>
                </tr>
                <tr>
                  <td class="fieldName" nowrap>Suppliers Challan No:</td>
                  <td><? if($addMode==true){
						 if($p["supplyChallanNo"]!="") $recordChallanNo	=	$p["supplyChallanNo"];
							}
										?>
                      <input name="supplyChallanNo" type="text" id="supplyChallanNo" size="6" value="<?=$recordChallanNo?>" tabindex="6" onkeypress="return focusNextBox(event,'document.frmDailyCatch','weighChallanNo');" readonly="true" style="border:none;"/></td>
                </tr>
            </table></fieldset></td>
            <td width="24%" valign="top">
			<fieldset><legend class="listing-item">Weighment Challan Details</legend>
			<table>
                <tr>
                  <td class="fieldName" nowrap>Weighment Challan No:</td>
                  <td><? 
				  if($addMode==true){
				   if($p["weighChallanNo"]!="")	$recordWeighNo	=	$p["weighChallanNo"];
					}
										?>
                      <input name="weighChallanNo" type="text" id="weighChallanNo" size="8" value="<?=$recordWeighNo?>" tabindex="7" onkeypress="return focusNextBox(event,'document.frmDailyCatch','selectDate');" readonly="true" style="border:none;"/></td>
                </tr>
                <tr>
                  <td class="fieldName">Entry Date </td>
                  <td>	
			<? 
			if ($addMode==true) {
				if ($p["selectDate"]!="") $recordSelectDate = $p["selectDate"];
							}
						if($recordSelectDate==""){
						    $recordSelectDate	=	date("d/m/Y");
						}						
						?>
                      <input type="text" id="selectDate" name="selectDate" size="8" value="<?=$recordSelectDate?>" tabindex="8" onkeypress="return focusNextBox(event,'document.frmDailyCatch','selectTimeHour');" readonly="true" style="border:none;"></td>
                </tr>
                <tr>
                  <td class="fieldName">Entry Time </td>
                  <td nowrap="nowrap">
		  <?
		   	if ($addMode==true) {
				if($p["selectTimeHour"]!="") $selectTimeHour = $p["selectTimeHour"];
			}
			 if ($selectTimeHour=="") $selectTimeHour = date("g");			  
		  ?>
		  <input type="text" id="selectTimeHour" name="selectTimeHour" size="1" value="<?=$selectTimeHour;?>" tabindex="9" onkeypress="return focusNextBox(event,'document.frmDailyCatch','selectTimeMints');" onchange="return timeCheck();" style="text-align:center;border:none;" readonly/>
                    :
                    <?
					if($addMode==true){
				  		if($p["selectTimeMints"]!="") $selectTimeMints	=	$p["selectTimeMints"];
				  	}
				  if($selectTimeMints=="") {
				  	$selectTimeMints		=	date("i");
				  }
				 
				  ?>
				    <input type="text" id="selectTimeMints" name="selectTimeMints" size="1" value="<?=$selectTimeMints;?>" tabindex="10" onkeypress="return focusNextBox(event,'document.frmDailyCatch','timeOption');" onchange="return timeCheck();" style="text-align:center;border:none;" readonly>
				  <? 
				  if($addMode==true){
						if($p["timeOption"]!="") $timeOption = $p["timeOption"];
					}
					if($timeOption=="") {
						$timeOption = date("A");
					}
				  ?>
                    <select name="timeOption" id="timeOption" tabindex="11" onkeypress="return focusNextBox(event,'document.frmDailyCatch','fish');" disabled>
					<option value="AM" <? if($timeOption=='AM') echo "selected"?>>AM</option>
					<option value="PM" <? if($timeOption=='PM') echo "selected"?>>PM</option>
                    </select>                    </td>
                </tr>
				<tr>
                  <td class="fieldName">*Fish </td>
                  <td><? 
					   
					   if($addMode==true){
					   		if($addRaw	==	true)  $fishId = "";
					   		else $fishId		=	$p["fish"];
							if ( $fishId != "" ){	
								$processCodeRecords	=	$processcodeObj->processCodeRecFilter($fishId);	
									
							}	
						
				  		  ?>
                    <select name="fish" id="fish" onchange="this.form.submit();" style="width:70%;" tabindex="12" onkeypress="return focusNextBox(event,'document.frmDailyCatch','processCode');" disabled>
                      <? } else {?>
          <select name="fish" id="fish" onchange="this.form.editId.value=<?=$editId?>; this.form.submit();" style="width:70%;" onkeypress="return focusNextBox(event,'document.frmDailyCatch','processCode');" tabindex="12" disabled>
                      <? }?>
                      <option value="">--Select--</option>
                      <?
			foreach ($fishMasterRecords as $fr) {
				$i++;
				$Id	=	$fr[0];
				$fishName	=	stripSlash($fr[1]);
				$fishCode	=	stripSlash($fr[2]);
				$selected	=	"";
				if ( $recordFish == $Id || $fishId==$Id) {
					$selected	=	"selected";
				}
			?>
                      <option value="<?=$Id?>" <?=$selected?>> <?=$fishName?> </option>
                      <? }?>
                    </select></td>
                </tr>
                <tr>
                  <td class="fieldName">*Code </td>
                  <td><? if ($addMode==true) { 
			if ($addRaw==true)  $processId = "";
			else $processId	=	$p["processCode"];
			$gradeMasterRecords = $processcodeObj->fetchGradeRecords($processId); 
			$processCodeRec	=	$processcodeObj->find($processId);
			$receivedBy	=	$processCodeRec[7];			
			?>
                    <select name="processCode" id="processCode" onchange="this.form.submit(); " tabindex="13" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryOption');" disabled>
                      	<? 
			} else {
		   	?>
                      <select name="processCode" id="processCode" onchange="this.form.editId.value=<?=$editId?>; this.form.submit();" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryOption');" onclick="assignValue(this.form,'2','codeChangedValue'); return gridShow(this.form);" tabindex="13" disabled>
                      <? }?>
                      <option value="">-- Select --</option>
                      <?
			if (sizeof($processCodeRecords)>0) {
				foreach ($processCodeRecords as $fl) {
					$processCodeId		=	$fl[0];
					$processCode		=	$fl[2];
					$selected	=	"";
					if ($recordProcessCode == $processCodeId || $processId==$processCodeId) {
						$selected	=	"selected";
					}
			?>
			<option value="<?=$processCodeId;?>" <?=$selected;?>><?=$processCode;?></option>
                      <?
				}
			}
			?>
                    </select></td>
                </tr>
				<tr>
				  <td class="fieldName">Entry</td>
				  <td>
				  <?
				//Text box Focus Setting
				if ($receivedBy=='C') {
					$onKeyPressFocusNext = "return focusNextBox(event,'document.frmDailyCatch','count');";
				} else if ($receivedBy=='G') {
					$onKeyPressFocusNext = "return focusNextBox(event,'document.frmDailyCatch','selGrade');";
				} else if ($receivedBy=='B') {
					$onKeyPressFocusNext = "return focusNextBox(event,'document.frmDailyCatch','count');";
				}
			

				  if($addMode==true) {
					  $entryOption	=	$p["entryOption"];			  
				  ?>
				  <select name="entryOption" id="entryOption" onchange="this.form.submit();" onkeypress="<?=$onKeyPressFocusNext?>" tabindex="14" disabled>
				  <? } else {?>
<select name="entryOption" id="entryOption" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();" onkeypress="<?=$onKeyPressFocusNext?>" tabindex="14" disabled>
				  <? }?>
				  		<option value="B" <? if($entryOption=='B') echo "Selected";?>>Basket Wt</option>
						<option value="N" <? if($entryOption=='N') echo "Selected";?>>Net Wt</option>
				    </select>				  </td></tr>
				<tr><td>
				<input type="hidden" name="hidReceived" value="<?=$receivedBy?>">
				<input  type="hidden" name="saveChangesOk" size="2" value="<? if ($editMode==true) echo 'Y'; ?>" >
				<input  type="hidden" name="codeChangedValue" size="2">					
				</td></tr>
				<? 
				//fish, Process code, count/ grade => if same don't add record for the same challan
				if ($receivedBy=='C' && $addMode!="") {
					$onChangeCheck = "xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, document.getElementById('count').value,'');";
				} else if ($receivedBy=='G' && $addMode!="") {
					$onChangeCheck = "xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value,'',document.getElementById('selGrade').value);";
				} else if ($receivedBy=='B' && $addMode!="") {
					$onChangeCheck = "xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value,document.getElementById('count').value,document.getElementById('selGrade').value);";
				}
				
				if ( ($receivedBy=='C' || $receivedBy=='B') && $addMode!="") {
					$onChanageCountAverage = "xajax_checkCountAverageSame(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, document.getElementById('countAverage').value);";
				}

				if ($receivedBy=='C' ||  $receivedBy=='B') {
				?>
                <tr>
                  <td class="fieldName">Count </td>
                  <td><? if($addMode==true){
			if ($addRaw==true)  $recordCount = "";
			else $recordCount				=	$p["count"];
			}
		?>
                    <input name="count" type="text" id="count" size="25" value="<?=$recordCount?>" onkeyup="<?=$onChangeCheck?> <?=$onChanageCountAverage?> return findAverage(document.frmDailyCatch);" tabindex="15" onkeypress="return focusNextBox(event,'document.frmDailyCatch','declWeight');" readonly="true" style="border:none;"></td>
                </tr>
                <tr>
                  <td class="fieldName">Average</td>
                  <td>
		<? if($addMode==true) {
			if ($addRaw==true)  $recordAverage = "";
			else $recordAverage = $p["countAverage"];
		  }
		?>
                    <input name="countAverage" type="text" id="countAverage" size="8" value="<?=$recordAverage?>" readonly style="border:none;">
		</td>
                </tr>
				<? 
					} 
				if ($receivedBy=='G' ||  $receivedBy=='B' ){
				?>                
                <tr>
                  <td class="fieldName">Grade</td>
                  <td class="fieldName">
		<? 
		if ($addRaw==true)  $gradeId = "";
		else $gradeId = $p["selGrade"];
		?>
		<select name="selGrade" id="selGrade" tabindex="16" onchange="<?=$onChangeCheck?>" disabled>
                <option value="" > Select Grade </option>
                <? 
		if (sizeof($gradeMasterRecords)> 0) {
			foreach ($gradeMasterRecords as $gl) {
				$id		=	$gl[3];							$code		=	$gl[4];
				$displayGrade	=	$code;
				$selected		=	"";
				if ($recordGradeId== $id || $gradeId==$id) {
					$selected	=	" selected ";
				}
		?>
                <option value="<?=$id;?>" <?=$selected;?> > <?=$displayGrade;?> </option>
                <?
			}
		}
		?>
                 </select></td>
                </tr>
				<? }?>
            </table></fieldset></td>
            <td width="20%" valign="top">			  
				  <table><tr><td>
				  <fieldset>
                    <legend class="listing-item">
				<? 					
					if ($addMode==true) { 					
						if ($p["paymentBy"]=='D') {
							$checked="Checked";
						}
				  }
			  	?>
                    <input name="paymentBy" id="paymentBy" type="checkbox" id="paymentBy" style="vertical-align:middle;" value="D" readonly <?=$checked;?> class="chkBox">&nbsp;Declared</legend>
                    <table border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td nowrap class="fieldName"><input type="hidden" name="totalDeclaredWt" id="totalDeclaredWt">
						<iframe 
src ="ModifyRMChallanDeclaredItem.php?entryId=<?=$catchEntryNewId;?>" width="300" frameborder="0" height="200"></iframe></td>
                        </tr>
                    </table>
                  </fieldset>		</td></tr></table>		  </td>
                </tr>
            </table>			</td>
            </tr>
          <tr>
            <td colspan="4" class="fieldName"><table>
              <tr>
                <td class="listing-item"><? 
					if($addRaw==true) $p["processCode"]=""; 
				if( ($p["processCode"]!=""&& $entryOption=='B')|| ($recordProcessCode!="" && $entryOption=='B') ){?><fieldset>
                    <legend class="listing-item">Count Details  </legend> 
		
                    <iframe 
src ="ModifyRMChallanGrossWt.php?lastId=<?=$catchEntryNewId?>&newWt=<?=$resetBasketWt?>&basketWt=<?=$processBasketWt?>&decTotalWt=<?=$decTotalWt?>" width="952" frameborder="0" height="400" marginwidth="2"></iframe>                   
                              </fieldset><? }?></td>
              </tr>
            </table></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td rowspan="8" valign="top" nowrap class="fieldName" align="center" ></td>
      <td rowspan="2" class="listing-item" align="center" valign="top">
	  
	  <input type="hidden" name="entryTotalGrossWt" value="<?=$totalWt;?>">
	  <input type="hidden" name="entryTotalBasketWt" value="<?=$grandTotalBasketWt;?>">
	  <table>
	  	<tr>
			<td valign="top">
			<table><TR><TD>
	  <fieldset>
        <legend class="listing-item">Weight Calculation</legend>
	  <table width="200" border="0" cellpadding="0" cellspacing="0">
        <? if($entryOption!='N'){?>
        <tr>
          <td class="fieldName" nowrap="nowrap">Total Gross Wt </td>
          <td class=""><input name="totalGrossWt" type="text" value="<?=$totalWt;?>" size="8" style="text-align:right;border:none;" readonly></td>
        </tr>
        <tr>
          <td class="fieldName" nowrap="nowrap">Total Basket Wt </td>
          <td><input name="totalBasketWt" type="text" value="<?=$grandTotalBasketWt;?>" size="8" style="text-align:right;border:none;" readonly></td>
        </tr>
		<? }?>
        <tr>
          <td class="fieldName" nowrap="nowrap">Net Wt:</td>
          <td class="listing-item">
		<? if($addMode==true){
			if($addRaw == true)  $netGrossWt = "";
			else $netGrossWt = $p["entryGrossNetWt"];
		}
		?>
		<input type="text" name="entryGrossNetWt" id="entryGrossNetWt" value="<?=$netGrossWt?>" size="8" style="text-align:right;border:none;" onchange="Javascript:actualWt(document.frmDailyCatch);" tabindex="17" readonly>
              Kg</td>
        </tr>
      </table>
	  </fieldset>
	  </TD></TR></table></td>
			<td valign="top">
			<table><tr><td>
	  <fieldset>
        <legend class="listing-item">Final Weight</legend>
        <table>
		<? if($entryOption!='N'){?>
          <tr>
            <td class="fieldName">&nbsp;</td>
            <td colspan="2" class="fieldName"> Basket Weight: </td>
            <td class="listing-item" nowrap align="right">&nbsp;</td>
            <td class="listing-item" nowrap align="right"><input name="dailyBasketWt" type="text" size="3" value="<?=$processBasketWt?>" style="text-align: right;border:none;" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryAdjust');" readonly>
              Kg </td>
            <td><!--<input type="submit" name="cmdReset" class=button value="Reset" <? if ($editId) {?> onclick="this.form.editId.value=<?=$editId?>;" <? }?>>--></td>
          </tr>
		  <? }?>
          <tr>
            <td class="fieldName">&nbsp;</td>
            <td colspan="2" class="fieldName"> Adjustment: </td>
            <td class="listing-item" nowrap align="right">&nbsp;</td>
            <td class="listing-item" nowrap align="right">
		<? if($addMode==true){
			if($addRaw == true)  $recordAdjust = "";
			else $recordAdjust		=	$p["entryAdjust"];
			}
		?>
                <input name="entryAdjust" type="text" size="4" onkeyup="return actualWt(document.frmDailyCatch);" onchange="return effectiveWt(document.frmDailyCatch);" value="<?=$recordAdjust?>" style="text-align: right" tabindex="18" onkeypress="return focusNextBox(event,'document.frmDailyCatch','reasonAdjust');" />
              Kg </td>
			  <td colspan="4" nowrap class="fieldName">&nbsp;Reason: &nbsp;
                <? if($addMode==true){
			if($addRaw	==	true)  $recordReason = "";
			else $recordReason		=	$p["reasonAdjust"];
			}
		?>
                <input name="reasonAdjust" type="text" id="reasonAdjust" size="20" value="<?=$recordReason?>" tabindex="19" onkeypress="return focusNextBox(event,'document.frmDailyCatch','gradeCountAdj');" /></td>
          </tr>
          <tr>
            <td class="fieldName" nowrap>&nbsp;</td>
            <td colspan="2" nowrap class="fieldName">Grade/Count Adj </td>
            <td class="listing-item" align="right">&nbsp;</td>
            <td class="listing-item" align="right"><? if($addMode==true){
										if($addRaw	==	true)  $gradeCountAdj = "";
										else $gradeCountAdj		=	$p["gradeCountAdj"];
										}
										?>
                <input name="gradeCountAdj" type="text" id="gradeCountAdj" style="text-align: right" tabindex="20" onchange="return effectiveWt(document.frmDailyCatch);" onkeypress="return focusNextBox(event,'document.frmDailyCatch','gradeCountAdjReason');" onkeyup="return actualWt(document.frmDailyCatch);" value="<?=$gradeCountAdj?>" size="4" />
              Kg</td>
			  <td colspan="4" nowrap class="fieldName">&nbsp;Reason: &nbsp;
                <? if($addMode==true){
										if($addRaw	==	true)  $gradeCountAdjReason = "";
										else $gradeCountAdjReason		=	$p["gradeCountAdjReason"];
										}
										?>
                <input name="gradeCountAdjReason" type="text" id="gradeCountAdjReason" size="20" value="<?=$gradeCountAdjReason?>" tabindex="21" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryLocal');" /></td>
          </tr>
          <tr>
            <td class="fieldName" nowrap>&nbsp;</td>
            <td colspan="2" nowrap class="fieldName">Actual Wt: </td>
            <td class="listing-item" align="right">&nbsp;</td>
            <td class="listing-item" align="right">
			<?
			if($addRaw	==	true)  $entryActualWt = "";
			?>
			<input name="entryActualWt" type="text" size="10" readonly style="text-align:right;border:none;" value="<?=$entryActualWt;?>">
              Kg</td>
          </tr>
          <tr>
            <td colspan="6"><table>
                <tr>
                  <td class="fieldName" nowrap>Local Quantity</td>
                  <td class="listing-item" nowrap><input name="entryLocalPercent" type="text" id="entryLocalPercent" value="0.00" size="3" style="text-align:right" readonly>
                    % </td>
                  <td class="listing-item" nowrap><? if($addMode==true){
										if($addRaw	==	true)  $recordLocalQty = "";			  						
										else $recordLocalQty			=	$p["entryLocal"];
										}
										?>
                      <input name="entryLocal" type="text" id="entryLocal" value="<? if($recordLocalQty=="") { echo 0; } else { echo $recordLocalQty;} ?>" onkeyup="return effectiveWt(document.frmDailyCatch);" size="5" style="text-align: right" tabindex="22" onkeypress="return focusNextBox(event,'document.frmDailyCatch','reasonLocal');"/>
                    Kg</td>
					<td class="fieldName" nowrap>&nbsp;Reason: </td>
                  <td colspan="2" class="listing-item">
				  <input name="reasonLocal" type="text" id="reasonLocal" size="20" tabindex="23" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryWastage');" value="<?=$reasonLocal;?>"></td>
                </tr>
                <tr>
                  <td class="fieldName" nowrap>Wastage</td>
                  <td class="listing-item" nowrap><input name="entryWastagePercent" type="text" id="entryWastagePercent" value="0.00" size="3" style="text-align:right" readonly>
                    % </td>
                  <td class="listing-item" nowrap><? if($addMode==true){
  											if($addRaw	==	true)  $recordWastage = "";								
											else $recordWastage		=	$p["entryWastage"];
										}
										?>
                      <input name="entryWastage" type="text" id="entryWastage" size="5" onkeyup="return effectiveWt(document.frmDailyCatch);" value="<? if($recordWastage=="") { echo 0; } else { echo $recordWastage;} ?>" style="text-align: right"  tabindex="24" onkeypress="return focusNextBox(event,'document.frmDailyCatch','reasonWastage');"/>
                    Kg</td>
					<td class="fieldName" nowrap>&nbsp;Reason</td>
                  <td colspan="2" class="listing-item"><input name="reasonWastage" type="text" id="reasonWastage" size="20" tabindex="25" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entrySoft');" value="<?=$reasonWastage;?>"></td>
                </tr>
                <tr>
                  <td class="fieldName" nowrap>Soft</td>
                  <td class="listing-item" nowrap><input name="entrySoftPercent" type="text" id="entrySoftPercent" value="0.00" size="3" style="text-align:right" readonly/>
                    %</td>
                  <td class="listing-item" nowrap><? if($addMode==true){
				  
										if($addRaw	==	true)  $recordSoft = "";								
										else $recordSoft		=	$p["entrySoft"];
										}
										?>
                      <input name="entrySoft" type="text" id="entrySoft" size="5" onkeyup="return effectiveWt(document.frmDailyCatch);" value="<? if($recordSoft=="") { echo 0; } else { echo $recordSoft;} ?>" style="text-align: right" tabindex="26" onkeypress="return focusNextBox(event,'document.frmDailyCatch','reasonSoft');">
                    Kg </td>
					<td class="fieldName" nowrap>&nbsp;Reason</td>
                  <td colspan="2" class="listing-item"><input name="reasonSoft" type="text" id="reasonSoft" size="20" tabindex="26" onkeypress="return focusNextBox(event,'document.frmDailyCatch','goodPack');" value="<?=$reasonSoft;?>" /></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td nowrap class="fieldName">&nbsp;</td>
            <td nowrap class="fieldName" align="left">&nbsp;</td>
            <td nowrap class="fieldName" align="left">Effective Weight</td>
            <td align="center" class="listing-item">&nbsp;</td>
            <td colspan="1" align="right" class="listing-item">
			<?
			if($addRaw	==	true)  $entryEffectiveWt = "";
			?>
			<input name="entryEffectiveWt" type="text" id="entryEffectiveWt" size="8" readonly style="text-align: right;border:none;" value="<?=$entryEffectiveWt;?>"  onchange="return effectiveWt(document.frmDailyCatch);">&nbsp;Kg
			<input type="hidden" name="hidEntryEffectiveWt" id="hidEntryEffectiveWt" value="<?=$entryEffectiveWt?>">
	</td>
          </tr>
        </table>
      </fieldset>
	  </td>
	  </tr>
	  </table></td>
		</tr>
	  </table>
	  </td>
      <td nowrap class="listing-item" valign="top" align="left">
	  <table><tr><td>
	  <fieldset>
        <legend class="listing-item">Quality</legend>
        <iframe 
src ="ModifyRMChallanQuality.php?entryId=<?=$catchEntryNewId;?>" width="250" frameborder="0"></iframe></fieldset></td></tr></table></td>
    </tr>
    <tr>
      <td nowrap class="listing-item" valign="top" align="left">
	  <table><tr><td>
	  <fieldset>
	  <table>
          <tr>
            <td class="fieldName" nowrap>Good for Packing:</td>
            <td class="listing-item">
		<? if($addMode==true){
			if($addRaw	==	true)  $recordGood = "";
			else $recordGood			=	$p["goodPack"];
		}
		?>
                <input name="goodPack" type="text" id="goodPack" size="4" value="<? if($recordGood=="") { echo 100; } else { echo $recordGood;} ?>"  tabindex="28" onkeypress="return focusNextBox(event,'document.frmDailyCatch','peeling');" style="text-align:right;border:none;" onkeyup="return calcPeeling(document.frmDailyCatch);" readonly="true">&nbsp;%</td>
          </tr>
          <tr>
            <td class="fieldName">For Peeling: </td>
            <td class="listing-item">
		<? 
			if($addMode==true){
				$recordPeeling =0;
				if( $p["peeling"]!=0  )	{
					if($addRaw	==	true)  $recordPeeling = "";
					else $recordPeeling	=	$p["peeling"];
				}
			}
		?>
                <input name="peeling" type="text" id="peeling" size="4" value="<?=$recordPeeling?>" onkeypress="return focusNextBox(event,'document.frmDailyCatch','entryRemark');" readonly style="text-align:right;border:none;">&nbsp;%</td>
          </tr>
          <tr>
            <td class="fieldName">Remarks:</td>
            <td class="listing-item">
		<? if($addMode==true){
			if($addRaw	==	true)  $recordRemarks = "";
			else $recordRemarks		=	$p["entryRemark"];
			}
		?>
                <textarea name="entryRemark" cols="23" rows="2" id="entryRemark" tabindex="29" onkeypress="return focusNextBox(event,'document.frmDailyCatch','cmdAddRaw');" readonly style="border:none;"><?=$recordRemarks?></textarea></td>
          </tr>
      </table></fieldset>
	  </td></tr></table>
	  </td>
    </tr>
    <tr>
      <td colspan="3"><table width="100%" border="0" cellpadding="3" cellspacing="2">
          <tr>
            <td width="40%" nowrap class="fieldName" valign="top"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td colspan="4"  height="10" ></td>
    </tr>
	<tr>
		<TD colspan="4">
			<table align="center">
				<TR>
					<TD>
					<? if ($edit==true || $reEdit==true) {?>
						 <input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('ModifyRMChallan.php');" />
					        &nbsp;&nbsp;
					        <input type="submit" name="cmdDailySaveChange" class="button" value=" Save Changes " onclick="return validateModifyRMChallan();" />
					<? }?>
					</TD>
				</TR>
			</table>
		</TD>
	</tr>
    <!--<tr>
      <? if($editMode){?>
      <td align="center" nowrap colspan="2"><input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('ModifyRMChallan.php');" />
        &nbsp;&nbsp;
        <input type="submit" name="cmdDailySaveChange" class="button" value=" Save Changes " onclick="return validateModifyRMChallan();" />&nbsp;&nbsp;<? if($add==true){?>		
	<? }?>      </td>
      <?} else{?>
      <td  colspan="2" align="center" nowrap><input type="submit" name="cmdAddCancel" class="button" value=" Cancel " onclick="return cancel('ModifyRMChallan.php');" />
        &nbsp;&nbsp;
        <input type="submit" name="cmdAddDailyCatch" class="button" value=" Save & Exit " onclick="return validateAddDailyCatchEntry(document.frmDailyCatch);" />&nbsp;&nbsp;<input type="submit" name="cmdAddNewChallan" value="save & Add New Challan" class="button" onclick="return recordSaved(document.frmDailyCatch);" style="width:150px;"> &nbsp;&nbsp;</td>
      <input type="hidden" name="cmdAddNew" value="1">
      <?}?>
	  <? if($addRawMaterial){?>
	  <input type="hidden" name="cmdAddRaw" value="1" />
	  <? }?>
    </tr>-->
    <tr>
      <td  height="10" ></td>
    </tr>
	</table>
    </td></tr>
	<? } ?>
  </table>
<!--  Edit Ends Here-->
</td>
</tr>
  </table>
</td>
</tr>
</table>
  <table cellspacing="0"  align="center" cellpadding="0" width="90%">
        <tr> 
      <td> 
        <!-- Form fields end   -->      </td>
    </tr>
    <?
		}
		
		# Listing DailyCatchEntry Starts
	?>
    <tr> 
      <td height="10">
			<input type="hidden" name="addMode" value="<?=$addMode?>">
			<input type="hidden" name="editMode" value="<?=$editMode?>">
			<input type="hidden" name="enteredRMId" value="<?=$editId;?>">
			<input type="hidden" name="dailyCatchentryId" value="<?=$dailyCatchentryId?>">
			<input type="hidden" name="editId" value="">
			<input type="hidden" name="editChellan" value="">
                  	<input type="hidden" name="editSelectionChange" value="0">
			<input type="hidden" name="entryId" id="entryId" value="<?=$lastId?>">
			<input type="hidden" name="catchEntryNewId" value="<?=$catchEntryNewId;?>">
			<input type="hidden" name="hidSelSupplierId" id="hidSelSupplierId" value="<?=$selSupplierId;?>">
			<input type="hidden" name="hidSelFish" id="hidSelFish" value="<?=$selFish;?>">
			<input type="hidden" name="hidSelProcesscode" id="hidSelProcesscode" value="<?=$selProcesscode;?>">
			<input type="hidden" name="hidSameEntryExist" id="hidSameEntryExist">
			<input type="hidden" name="hidSameCountAverage" id="hidSameCountAverage">
			<input type="hidden" name="hidSelEntry" id="hidSelEntry" value="<?=$selEntry;?>">	
	</td>
    </tr>
  </table>
  <? if ($searchMode==true) { ?>
  <script type="text/javascript" language="javascript">
	  actualWt(document.frmDailyCatch);
	  effectiveWt(document.frmDailyCatch);
  </script>
  <? } else if($addMode==true){?>
  <script type="text/javascript" language="javascript">
	  actualWt(document.frmDailyCatch);
  </script>
  <? }?>
  
  <? if($addMode==true||$editMode==true){?>
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
	<? }?> 
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

	<script>
window.onLoad = call();
function call()
{	
	xajax_getChallanWiseFishList('selFish', document.getElementById('weighNumber').value, document.getElementById('hidSelFish').value, '<?=$billingCompany?>');
	xajax_getChallanWiseProcessCodeList('selProcesscode', document.getElementById('weighNumber').value, document.getElementById('hidSelFish').value, document.getElementById('hidSelProcesscode').value, '<?=$billingCompany?>');
	xajax_getRMEntryRecords(document.getElementById('weighNumber').value, document.getElementById('hidSelFish').value, document.getElementById('hidSelProcesscode').value, document.getElementById('hidSelEntry').value, '<?=$billingCompany?>');
}
</script>

<? 
	//Checking same Entries Exist
	if ($addMode) {
?>
<script>
	<? 
		echo $onChangeCheck;
		echo $onChanageCountAverage;
	?>
</script>
	<?
		if ($receivedBy=='C' || $receivedBy=='B') {		
	?>
<script>
//On Change Check
document.getElementById("count").onchange = function() {

	<? 
		echo $onChangeCheck;
		echo $onChanageCountAverage;
	?>
};
</script>
	<? 
		}
	}
	?>

</form>
<?
# Include Template [bottomRightNav.php]
require("template/bottomRightNav.php");
?>
