<?php
	require("include/include.php");
	require_once("lib/HealthCertificate_ajax.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$userId		= $sessObj->getValue("userId");

	$dateSelection =  "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
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

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;	
	if ($p["cmdCancel"]!="") $addMode = false;

	#Add a SO
	if ($p["cmdAdd"]!="") {
		
		$selLanguage		= $p["selLanguage"];
		$consignorName		= addSlash(trim($p["consignorName"]));
		$consignorAddress	= addSlash(trim($p["consignorAddress"]));
		$consignorPostalCode	= addSlash(trim($p["consignorPostalCode"]));
		$consignorTelNo		= addSlash(trim($p["consignorTelNo"]));
		$consigneeName		= addSlash(trim($p["consigneeName"]));
		$consigneeAddress	= addSlash(trim($p["consigneeAddress"]));
		$consigneePostalCode	= addSlash(trim($p["consigneePostalCode"]));
		$consigneeTelNo		= addSlash(trim($p["consigneeTelNo"]));
		$originCompanyName	= addSlash(trim($p["originCompanyName"]));
		$originCompanyAddress	= addSlash(trim($p["originCompanyAddress"]));
		$originCompanyPostalCode = addSlash(trim($p["originCompanyPostalCode"]));
		$originCompanyTelNo	= addSlash(trim($p["originCompanyTelNo"]));
		$isoCode		= addSlash(trim($p["isoCode"]));
		$regionOfOrigin		= addSlash(trim($p["regionOfOrigin"]));
		$originCode		= addSlash(trim($p["originCode"]));
		$destinationCountry	= addSlash(trim($p["destinationCountry"]));
		$approvalNumber		= addSlash(trim($p["approvalNumber"]));
		$departureDate		= mysqlDateFormat($p["departureDate"]);
		$identification		= addSlash(trim($p["identification"]));
		$entryBPEU		= addSlash(trim($p["entryBPEU"]));
		$commodityDesciption	= addSlash(trim($p["commodityDesciption"]));
		$commodityCode		= addSlash(trim($p["commodityCode"]));
		$netWt			= addSlash(trim($p["netWt"]));
		$grWt			= addSlash(trim($p["grWt"]));
		$noOfPackage		= addSlash(trim($p["noOfPackage"]));
		$containerNo		= addSlash(trim($p["containerNo"]));
		$sealNo			= addSlash(trim($p["sealNo"]));
		$typeOfPackaging	= addSlash(trim($p["typeOfPackaging"]));	
		$species		= addSlash(trim($p["species"]));
		$natureOfCommodity	= addSlash(trim($p["natureOfCommodity"]));
		//$treatmentType		= $p["treatmentType"];
		//$identificationPackages	= $p["identificationPackages"];
		//$identificationWt	= $p["identificationWt"];
		//$tempProductFrozen	= $p["tempProductFrozen"];
		$destinationIsoCode	= addSlash(trim($p["destinationIsoCode"]));
		$transportType		= $p["transportType"];
		$proTempType		= $p["proTempType"];  // Temperture of Product
		$humanConsumption	= $p["humanConsumption"]; // Y/N
		$admissionEU		= $p["admissionEU"];		// Y/N

		if ($selLanguage && $consigneeName) {
			$healthCertificateRecIns = $healthCertificateObj->addHealthCetificate($selLanguage, $consignorName, $consignorAddress, $consignorPostalCode, $consignorTelNo, $consigneeName, $consigneeAddress, $consigneePostalCode, $consigneeTelNo, $originCompanyName, $originCompanyAddress, $originCompanyPostalCode, $originCompanyTelNo, $isoCode, $regionOfOrigin, $originCode, $destinationCountry, $approvalNumber, $departureDate, $identification, $entryBPEU, $commodityDesciption, $commodityCode, $netWt, $grWt, $noOfPackage, $containerNo, $sealNo, $typeOfPackaging, $species, $natureOfCommodity, $destinationIsoCode, $transportType, $proTempType, $humanConsumption, $admissionEU, $userId);	
		}

		if ($healthCertificateRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddHealthCertificate);
			$sessObj->createSession("nextPage",$url_afterAddHealthCertificate.$dateSelection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddHealthCertificate;
		}
		$healthCertificateRecIns = false;
	}
	

	# Edit a Rec
	if ($p["editId"]!="" ) {
		$editId			= $p["editId"];
		$editMode		= true;
		$healthCertificateRec	= $healthCertificateObj->find($editId);
		$editHealthCertificateId = $healthCertificateRec[0];
		$selLanguage		= $healthCertificateRec[1];
		$consignorName		= stripSlash($healthCertificateRec[2]);
		$consignorAddress	= stripSlash($healthCertificateRec[3]);
		$consignorPostalCode	= stripSlash($healthCertificateRec[4]);
		$consignorTelNo		= stripSlash($healthCertificateRec[5]);
		$consigneeName		= stripSlash($healthCertificateRec[6]);
		$consigneeAddress	= stripSlash($healthCertificateRec[7]);
		$consigneePostalCode	= stripSlash($healthCertificateRec[8]);
		$consigneeTelNo		= stripSlash($healthCertificateRec[9]);
		$originCompanyName	= stripSlash($healthCertificateRec[10]);
		$originCompanyAddress	= stripSlash($healthCertificateRec[11]);
		$originCompanyPostalCode = stripSlash($healthCertificateRec[12]);
		$originCompanyTelNo	= stripSlash($healthCertificateRec[13]);
		$isoCode		= stripSlash($healthCertificateRec[14]);
		$regionOfOrigin		= stripSlash($healthCertificateRec[15]);
		$originCode		= stripSlash($healthCertificateRec[16]);
		$destinationCountry	= stripSlash($healthCertificateRec[17]);
		$approvalNumber		= stripSlash($healthCertificateRec[18]);
		$departureDate		= dateFormat($healthCertificateRec[19]);
		$identification		= stripSlash($healthCertificateRec[20]);
		$entryBPEU		= stripSlash($healthCertificateRec[21]);
		$commodityDesciption	= stripSlash($healthCertificateRec[22]);
		$commodityCode		= stripSlash($healthCertificateRec[23]);
		$netWt			= stripSlash($healthCertificateRec[24]);
		$grWt			= stripSlash($healthCertificateRec[25]);
		$noOfPackage		= stripSlash($healthCertificateRec[26]);
		$containerNo		= stripSlash($healthCertificateRec[27]);
		$sealNo			= stripSlash($healthCertificateRec[28]);
		$typeOfPackaging	= stripSlash($healthCertificateRec[29]);
		$species		= stripSlash($healthCertificateRec[30]);
		$natureOfCommodity	= stripSlash($healthCertificateRec[31]);
		

		$destinationIsoCode	= $healthCertificateRec[32];
		$transportType		= $healthCertificateRec[33];
		if ($transportType=='PLANE') 		$transportType1 = "checked";
		else if ($transportType=='SHIP')  	$transportType2 = "checked";
		else if ($transportType=='AIR')  	$transportType3 = "checked";
		else if ($transportType=='RAIL')  	$transportType4 = "checked";
		else if ($transportType=='ROAD')  	$transportType5 = "checked";
		else if ($transportType=='OTHER')  	$transportType6 = "checked";
		$proTempType		= $healthCertificateRec[34];  // Temperture of Product

		if ($proTempType=='AMB') 	 	$proTempType1= "checked";
		else if ($proTempType=='CHI') 	$proTempType2= "checked";
		else if ($proTempType=='FRO') 	$proTempType3= "checked"; 

		$humanConsumption	= $healthCertificateRec[35];
		$admissionEU		= $healthCertificateRec[36];
		
		//$treatmentType		= $healthCertificateRec[33];
		//$identificationPackages	= $healthCertificateRec[34];
		//$identificationWt	= $healthCertificateRec[35];
		//$tempProductFrozen	= $healthCertificateRec[26];
		
	}


	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$healthCertificateRecId 		= $p["hidHealthCertificateId"];
		$selLanguage		= $p["selLanguage"];
		$consignorName		= addSlash(trim($p["consignorName"]));
		$consignorAddress	= addSlash(trim($p["consignorAddress"]));
		$consignorPostalCode	= addSlash(trim($p["consignorPostalCode"]));
		$consignorTelNo		= addSlash(trim($p["consignorTelNo"]));
		$consigneeName		= addSlash(trim($p["consigneeName"]));
		$consigneeAddress	= addSlash(trim($p["consigneeAddress"]));
		$consigneePostalCode	= addSlash(trim($p["consigneePostalCode"]));
		$consigneeTelNo		= addSlash(trim($p["consigneeTelNo"]));
		$originCompanyName	= addSlash(trim($p["originCompanyName"]));
		$originCompanyAddress	= addSlash(trim($p["originCompanyAddress"]));
		$originCompanyPostalCode = addSlash(trim($p["originCompanyPostalCode"]));
		$originCompanyTelNo	= addSlash(trim($p["originCompanyTelNo"]));
		$isoCode		= addSlash(trim($p["isoCode"]));
		$regionOfOrigin		= addSlash(trim($p["regionOfOrigin"]));
		$originCode		= addSlash(trim($p["originCode"]));
		$destinationCountry	= addSlash(trim($p["destinationCountry"]));
		$approvalNumber		= addSlash(trim($p["approvalNumber"]));
		$departureDate		= mysqlDateFormat($p["departureDate"]);
		$identification		= addSlash(trim($p["identification"]));
		$entryBPEU		= addSlash(trim($p["entryBPEU"]));
		$commodityDesciption	= addSlash(trim($p["commodityDesciption"]));
		$commodityCode		= addSlash(trim($p["commodityCode"]));
		$netWt			= addSlash(trim($p["netWt"]));
		$grWt			= addSlash(trim($p["grWt"]));
		$noOfPackage		= addSlash(trim($p["noOfPackage"]));
		$containerNo		= addSlash(trim($p["containerNo"]));
		$sealNo			= addSlash(trim($p["sealNo"]));
		$typeOfPackaging	= addSlash(trim($p["typeOfPackaging"]));	
		$species		= addSlash(trim($p["species"]));
		$natureOfCommodity	= addSlash(trim($p["natureOfCommodity"]));
		//$treatmentType		= $p["treatmentType"];
		//$identificationPackages	= $p["identificationPackages"];
		//$identificationWt	= $p["identificationWt"];
		//$tempProductFrozen	= $p["tempProductFrozen"];

		$destinationIsoCode	= $p["destinationIsoCode"];
		$transportType		= $p["transportType"];
		$proTempType		= $p["proTempType"];  // Temperture of Product
		$humanConsumption	= $p["humanConsumption"];
		$admissionEU		= $p["admissionEU"];
				
		if ($healthCertificateRecId!="" && $selLanguage && $consigneeName) {
			$healthCertificateRecUptd = $healthCertificateObj->updateHealthCertificate($healthCertificateRecId,$selLanguage, $consignorName, $consignorAddress, $consignorPostalCode, $consignorTelNo, $consigneeName, $consigneeAddress, $consigneePostalCode, $consigneeTelNo, $originCompanyName, $originCompanyAddress, $originCompanyPostalCode, $originCompanyTelNo, $isoCode, $regionOfOrigin, $originCode, $destinationCountry, $approvalNumber, $departureDate, $identification, $entryBPEU, $commodityDesciption, $commodityCode, $netWt, $grWt, $noOfPackage, $containerNo, $sealNo, $typeOfPackaging, $species, $natureOfCommodity, $destinationIsoCode, $transportType, $proTempType, $humanConsumption, $admissionEU);
		}	
		if ($healthCertificateRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succHealthCertificateUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateHealthCertificate.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failHealthCertificateUpdate;
		}
		$healthCertificateRecUptd	=	false;
	}
	

	/* Delete  */	
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$healthCertificateRecId	=	$p["delId_".$i];
			if ($healthCertificateRecId!="") {
				# Delete sales order main
				$healthCertificateRecDel = $healthCertificateObj->deleteHealthCertificate($healthCertificateRecId);
			}
		}
		if ($healthCertificateRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelHealthCertificate);
			$sessObj->createSession("nextPage",$url_afterDelHealthCertificate.$dateSelection);
		} else {
			$errDel	=	$msg_failDelHealthCertificate;
		}
		$healthCertificateRecDel	=	false;
	}


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
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

	# List all Purchase Order
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$healthCertificateRecords = $healthCertificateObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);		
		$salesOrderSize = sizeof($healthCertificateRecords);
		# For Pagination
		$fetchAllHealthCertificates = $healthCertificateObj->fetchAllDateRangeRecords($fromDate, $tillDate);		
	}

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllHealthCertificates);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	
	#Get Not completed SO for Printing
	$hcRecords = $healthCertificateObj->getHealthCertificateRecords();	

	
	# Setting the mode
	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 0;
	else 			$mode = "";	

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/HealthCertificate.js"; // For Printing JS in Head SCRIPT section

	if ($editMode) $heading	= $label_editHealthCertificate;
	else	       $heading	= $label_addHealthCertificate;
	
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmHealthCertificate" action="HealthCertificate.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="95%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
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
									<td colspan="2" style="padding-left:10px;padding-right:10px;">
										<table cellpadding="0"  width="95%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HealthCertificate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateHealthCertificate(document.frmHealthCertificate);">												</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HealthCertificate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateHealthCertificate(document.frmHealthCertificate);"> &nbsp;&nbsp;												</td>

												<?}?>
											</tr>
		<input type="hidden" name="hidHealthCertificateId" value="<?=$editHealthCertificateId;?>">			
				<tr>
				  <td colspan="2" height="5"></td>				  
			  	</tr>
	<tr>
		<TD colspan="2">
		<table>
			<TR>
				<TD>
					<table>
                                                <tr>
                                                  <td class="fieldName">*Language</td>
                                                  <td class="listing-item">
					<select name="selLanguage" id="selLanguage">			
                                        <option value="">-- Select --</option>
					<?	
					foreach ($langRecs as $langCode=>$language) {
						
						$selected = "";
						if ($langCode==$selLanguage) $selected = "selected";	
					?>
                            		<option value="<?=$langCode?>" <?=$selected?>><?=$language?></option>
					<? }?>
					</select>
					</td>
        </tr>
			


        </table>
				</TD>
				<td></td>
				<td></td>
			</TR>
			<!--<TR>
				<TD valign="top">
					<table cellpadding="0" cellspacing="0">
	<tr>
		<TD colspan="2" valign="top" >
			<fieldset>
				<legend class="listing-item">Consignor</legend>
				<table cellpadding="2" cellspacing="0">
					<TR>
						<TD class="fieldName" nowrap="true">Name:</TD>
						<td><input type="text" name="consignorName" id="consignorName" value="<?=$consignorName?>"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Address:</TD>
						<td><textarea name="consignorAddress" id="consignorAddress"><?=$consignorAddress?></textarea></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Postal Code:</TD>
						<td><input type="text" name="consignorPostalCode" id="consignorPostalCode" value="<?=$consignorPostalCode?>"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Tel.No.:</TD>
						<td><input type="text" name="consignorTelNo" id="consignorTelNo" value="<?=$consignorTelNo?>"></td>
					</TR>
				</table>
			</fieldset>
		</TD>
	</tr>	
        </table>
	</TD>
	<td valign="top">
	<table width="200" cellpadding="0" cellspacing="0">
	<tr>
		<TD colspan="2" valign="top">
			<fieldset>
				<legend class="listing-item">Consignee</legend>
				<table cellpadding="2" cellspacing="0">
					<TR>
						<TD class="fieldName" nowrap="true">Name:</TD>
						<td><input type="text" name="consigneeName" id="consigneeName" value="<?=$consigneeName?>"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Address:</TD>
						<td><textarea name="consigneeAddress" id="consigneeAddress"><?=$consigneeAddress?></textarea></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Postal Code:</TD>
						<td><input type="text" name="consigneePostalCode" id="consigneePostalCode" value="<?=$consigneePostalCode?>"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Tel.No.:</TD>
						<td><input type="text" name="consigneeTelNo" id="consigneeTelNo" value="<?=$consigneeTelNo?>"></td>
					</TR>
				</table>
			</fieldset>
		</TD>
	</tr>
        </table>
				</td>
				<td valign="top">
				<table width="200" cellpadding="0" cellspacing="0">
	<tr>
		<TD colspan="2" valign="top">
			<fieldset>
				<legend class="listing-item">Place of origin</legend>
				<table cellpadding="2" cellspacing="0">
					<TR>
						<TD class="fieldName" nowrap="true">Name:</TD>
						<td><input type="text" name="originCompanyName" id="originCompanyName" value="<?=$originCompanyName?>"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Address:</TD>
						<td><textarea name="originCompanyAddress" id="originCompanyAddress"><?=$originCompanyAddress?></textarea></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Postal Code:</TD>
						<td><input type="text" name="originCompanyPostalCode" id="originCompanyPostalCode" value="<?=$originCompanyPostalCode?>"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Tel.No.:</TD>
						<td><input type="text" name="originCompanyTelNo" id="originCompanyTelNo" value="<?=$originCompanyTelNo?>"></td>
					</TR>
				</table>
			</fieldset>
		</TD>
	</tr>
        </table>
	
	</td>
	</TR>-->
		<!--<tr>
			<TD valign="top">
<table width="200" cellpadding="0" cellspacing="0">
	<tr>
		<TD colspan="2" valign="top">	
				<fieldset>			
				<table cellpadding="2" cellspacing="0">
					<TR>
						<TD class="fieldName" nowrap="true">ISO Code:</TD>
						<td><input type="text" name="isoCode" id="isoCode" value="<?=$isoCode?>" size="4"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Region of origin:</TD>
						<td><input type="text" name="regionOfOrigin" id="regionOfOrigin" value="<?=$regionOfOrigin?>" size="4"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Code:</TD>
						<td><input type="text" name="originCode" id="originCode" value="<?=$originCode?>" size="4"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Country of destination:</TD>
						<td><input type="text" name="destinationCountry" id="destinationCountry" value="<?=$destinationCountry?>" size="4"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Approval Number:</TD>
						<td><input type="text" name="approvalNumber" id="approvalNumber" value="<?=$approvalNumber?>" size="4"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Date of departure:</TD>
						<td><input type="text" name="departureDate" id="departureDate" value="<?=$departureDate?>" size="8"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Identification:</TD>
						<td><input type="text" name="identification" id="identification" value="<?//$identification?>"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Entry B/P in EU:</TD>
						<td><input type="text" name="entryBPEU" id="entryBPEU" value="<?=$entryBPEU?>"></td>
					</TR>
				</table>
				</fieldset>
		</TD>
	</tr>
        </table>		
			</TD>
			<TD valign="top">
	<table width="200" cellpadding="0" cellspacing="0">
	<tr>
		<TD colspan="2" valign="top">	
				<fieldset>			
				<table cellpadding="2" cellspacing="0">					
					
					
					<TR>
						<TD class="fieldName" nowrap="true" style="line-height:normal;">Description of <br>Commodity:</TD>
						<td><textarea name="commodityDesciption" id="commodityDesciption"><?=$commodityDesciption?></textarea></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true" style="line-height:normal;">Commodity <br>code(HS Code):</TD>
						<td><input type="text" name="commodityCode" id="commodityCode" value="<?=$commodityCode?>" size="4"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">NET.WT:</TD>
						<td><input type="text" name="netWt" id="netWt" value="<?=$netWt?>" size="7"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">GR.WT:</TD>
						<td><input type="text" name="grWt" id="grWt" value="<?=$grWt?>" size="7"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true" style="line-height:normal;">Temperature of<br> product(FROZEN):</TD>
						<td><input type="text" name="tempProductFrozen" id="tempProductFrozen" value="<?=$tempProductFrozen?>" size="4"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Number of Packages:</TD>
						<td><input type="text" name="noOfPackage" id="noOfPackage" value="<?=$noOfPackage?>" size="4"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Container No:</TD>
						<td><input type="text" name="containerNo" id="containerNo" value="<?=$containerNo?>" size="6"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Seal No:</TD>
						<td><input type="text" name="sealNo" id="sealNo" value="<?=$sealNo?>" size="6"></td>
					</TR>
				</table>
				</fieldset>
		</TD>
	</tr>
        </table>
			</TD>
			<TD valign="top">
	<table width="200" cellpadding="0" cellspacing="0">
	<tr>
		<TD colspan="2" valign="top">	
				<fieldset>			
				<table cellpadding="2" cellspacing="0">					
					
					
					<TR>
						<TD class="fieldName" nowrap="true">Type of Packaging:</TD>
						<td><textarea name="typeOfPackaging" id="typeOfPackaging"><?=$typeOfPackaging?></textarea></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Species:</TD>
						<td><input type="text" name="species" id="species" value="<?=$species?>"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Nature of commodity:</TD>
						<td><input type="text" name="natureOfCommodity" id="natureOfCommodity" value="<?=$natureOfCommodity?>"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Treatment type:</TD>
						<td><input type="text" name="treatmentType" id="treatmentType" value="<?=$treatmentType?>"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Number of packages:</TD>
						<td><input type="text" name="identificationPackages" id="identificationPackages" value="<?=$identificationPackages?>" size="4"></td>
					</TR>
					<TR>
						<TD class="fieldName" nowrap="true">Net weight:</TD>
						<td><input type="text" name="identificationWt" id="identificationWt" value="<?=$identificationWt?>" size="4"></td>
					</TR>
					 
				</table>
				</fieldset>
		</TD>
	</tr>
        </table>
			</TD>
		</tr>-->		
		<!--<tr>
			<TD>c1</TD>
			<TD>c2</TD>
			<TD>c3</TD>
		</tr>-->
<!--  New Stats Here-->
		<tr>
			<TD colspan="3">
				<table width='50%' cellpadding='0' cellspacing='0' class="print" align="center">
		<tr>
			<TD rowspan="3" width="350px" colspan="4"> 
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="listing-item" colspan="2"  style="padding-left:5px;pading-right:5px;">
						1.1 Consignor : 
					</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:30px;pading-right:5px;">
						*Name
						</td>
						<td class="listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:11px;">
							<input type="text" name="consignorName" id="consignorName" value="<?=$consignorName?>">
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:30px;pading-right:5px;" valign="top">
						Address
						</td>
						<td class="listing-item" style="padding-left:10px;pading-right:10px;font-size:11px;">
							<textarea name="consignorAddress" id="consignorAddress"><?=$consignorAddress?></textarea>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:30px;pading-right:5px;">
						Postal Code 
						</td>
						<td class="listing-item" nowrap="nowrap" style="padding-left:10px;pading-right:10px;font-size:11px;">
							<input type="text" name="consignorPostalCode" id="consignorPostalCode" value="<?=$consignorPostalCode?>">
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:30px;pading-right:5px;">
						Tel.No.
						</td>
						<td class="listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:11px;">
							<input type="text" name="consignorTelNo" id="consignorTelNo" value="<?=$consignorTelNo?>">
						</td>
					</tr>
				</table>
			</TD>
			<TD colspan="2" rowspan="1" class="listing-item" align="left"  width="175px">1.2 Certificate reference number</TD>	
			<TD colspan="2" rowspan="1" class="listing-item" align="left" width="175px">1.2a</TD>
		</tr>
		<tr>			
			<TD colspan="4">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="listing-item" style="padding-left:2px;pading-right:5px;" valign="top">1.3 Central Competent Authority
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="listing-item" style="padding-left:5px;pading-right:5px;" valign="top">Export Inspection Council of India, New Delhi
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="listing-item" style="padding-left:5px;pading-right:5px;" valign="top">(Ministry of Commerce & Industry, Govt. of India)
						</td>
					</tr>
				</table>
			</TD>						
		</tr>
		<tr>			
			<TD colspan="4">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="listing-item" style="padding-left:2px;pading-right:5px;" valign="top">1.4 Local Competent Authority
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="listing-item" style="padding-left:5px;pading-right:5px;" valign="top">Export Inspection Agency-Mumbai
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="listing-item" style="padding-left:5px;pading-right:5px;" valign="top">(Ministry of Commerce & Industry, Govt. of India)
						</td>
					</tr>
				</table>
			</TD>						
		</tr>
		<tr>
			<TD width="300px" colspan="4"> 
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="listing-item" colspan="2"  style="padding-left:5px;pading-right:5px;">
						1.5 Consignee :
					</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:30px;pading-right:5px;">
						*Name
						</td>
						<td class="listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:11px;">
							<input type="text" name="consigneeName" id="consigneeName" value="<?=$consigneeName?>">
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:30px;pading-right:5px;" valign="top">
						Address
						</td>
						<td class="listing-item" style="padding-left:10px;pading-right:10px;font-size:11px;">
							<textarea name="consigneeAddress" id="consigneeAddress"><?=$consigneeAddress?></textarea>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:30px;pading-right:5px;">
						Postal Code 
						</td>
						<td class="fieldName" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:11px;">
							<input type="text" name="consigneePostalCode" id="consigneePostalCode" value="<?=$consigneePostalCode?>">
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:30px;pading-right:5px;">
						Tel.No.
						</td>
						<td class="listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:11px;">
							<input type="text" name="consigneeTelNo" id="consigneeTelNo" value="<?=$consigneeTelNo?>">
						</td>
					</tr>
				</table>
			</TD>
			<TD colspan="4" rowspan="1" class="listing-item" align="left"  valign="top">1.6
                      </TD>		
		</tr>
		<tr>
			<TD>
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="listing-item">
							1.7 Country of origin 
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="listing-item" align="center">
							<strong>INDIA </strong>
						</td>
					</tr>					
				</table>
			</TD>
			<TD>
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="fieldName">
							*ISO Code
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="listing-item" align="center">
							<input type="text" name="isoCode" id="isoCode" value="<?=$isoCode?>" size="4" style="text-align:center;">
						</td>
					</tr>					
				</table>
			</TD>
			<TD>
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
						<td class="fieldName" nowrap="true">
							1.8 *Region of origin 
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="listing-item" align="center">
							<input type="text" name="regionOfOrigin" id="regionOfOrigin" value="<?=$regionOfOrigin?>" size="4">
						</td>
					</tr>					
				</table>
			</TD>
			<TD>
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="fieldName">
							*Code
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="listing-item" align="center">
							<input type="text" name="originCode" id="originCode" value="<?=$originCode?>" size="4">
						</td>
					</tr>					
				</table>
			</TD>
			<TD>
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
						<td class="fieldName" nowrap="true">
							1.9 *Country of destination
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="listing-item" align="center">
							<input type="text" name="destinationCountry" id="destinationCountry" value="<?=$destinationCountry?>" size="4">
						</td>
					</tr>					
				</table>
			</TD>
			<TD valign="top">
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="fieldName">
							*ISO Code
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="listing-item" align="center">
							<input type="text" name="destinationIsoCode" id="destinationIsoCode" value="<?=$destinationIsoCode?>" size="4">
						</td>
					</tr>					
				</table>
			</TD>
			<TD class="listing-item" valign="top">
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="listing-item" valign="top">
							1.10
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="listing-item" align="center">
							<strong></strong>
						</td>
					</tr>					
				</table>
			</TD>
		</tr>
		<tr>
			<TD width="300px" colspan="4"> 
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="listing-item"  style="padding-left:5px;pading-right:5px;">
						1.11 Place of origin
					</td>
					<td class="listing-item" align="center"><strong>INDIA</strong></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:30px;pading-right:5px;">
						*Name
						</td>
						<td class="listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:11px;">
							<input type="text" name="originCompanyName" id="originCompanyName" value="<?=$originCompanyName?>">
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:30px;pading-right:5px;" valign="top">
						Address
						</td>
						<td class="listing-item" style="padding-left:10px;pading-right:10px;font-size:11px;">
							<textarea name="originCompanyAddress" id="originCompanyAddress"><?=$originCompanyAddress?></textarea>
						</td>
					</tr>
					<tr><Td ></Td></tr>
					<tr>
						<td nowrap="nowrap" class="listing-item"  style="padding-left:30px;pading-right:5px;" valign="top">
						</td>
						<td class="listing-item" align="center">
							<table cellspacing='0' cellpadding='0' class="tdBoarder">
								<tr>
								<td nowrap="nowrap" class="fieldName"  style="padding-left:5px;pading-right:5px;">
									<strong>*APPROVAL NUMBER : </strong>
								</td>
								<td class="listing-item" align="center">
									<input type="text" name="approvalNumber" id="approvalNumber" value="<?=$approvalNumber?>" size="4" onkeyup="disTxtVal();" autocomplete="off">
								</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</TD>
			<TD colspan="4" rowspan="1" class="listing-item" align="left"  valign="top">1.12</TD>		
		</tr>
		<tr>
			<TD width="300px" colspan="4"> 
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="listing-item"  style="padding-left:5px;pading-right:5px;" colspan="2">
						1.13 Place of loading
					</td>					
					</tr>
					<tr>
						<td nowrap="nowrap" class="listing-item"  style="padding-left:30px;pading-right:5px;" align="center" colspan="2">
						<strong>JNPT INDIA</strong>
						</td>						
					</tr>					
				</table>
			</TD>
			<TD colspan="4" rowspan="1" class="listing-item" align="left"  valign="top">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="fieldName"  style="padding-left:5px;pading-right:5px;" colspan="2">
						1.14 *Date of departure
					</td>					
					</tr>
					<tr>
						<td nowrap="nowrap" class="listing-item"  style="padding-left:30px;pading-right:5px;" align="center" colspan="2">
						<input type="text" name="departureDate" id="departureDate" value="<?=$departureDate?>" size="8">
						</td>						
					</tr>					
				</table>
			</TD>		
		</tr>
		<tr>
			<TD rowspan="2" width="300px" colspan="4"> 
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="fieldName" colspan="2"  style="padding-left:5px;pading-right:5px;">
						1.15 *Means of Transport : 
					</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="listing-item" colspan="2"  style="padding-left:30px;pading-right:5px;">
							<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100">
						<tr>
						<td nowrap="nowrap" class="listing-item"  style="padding-left:5px;pading-right:5px;">
						  Aeroplane
						</td>
						<td>
							<INPUT type="radio" name="transportType" id="transportType1" value="PLANE" <?=$transportType1?>>
						</td>
						<td class="listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;">
							Ship
						</td>
						<td>
							<INPUT type="radio" name="transportType" id="transportType2" value="SHIP" <?=$transportType2?>>
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="listing-item"  style="padding-left:5px;pading-right:5px;">
						  BY AIR
						</td>
						<td>
							<INPUT type="radio" name="transportType" id="transportType3" value="AIR" <?=$transportType3?>>
						</td>
						<td class="listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:5px;">
							Railway wagon
						</td>
						<td>	
							<INPUT type="radio" name="transportType" id="transportType4" value="RAIL" <?=$transportType4?>>				
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="listing-item"  style="padding-left:5px;pading-right:5px;">
						 Road Vehicle
						</td>
						<td>
							<INPUT type="radio" name="transportType" id="transportType5" value="ROAD" <?=$transportType5?>>
						</td>
						<td class="listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:5px;">
							Other
						</td>
						<td>	
							<INPUT type="radio" name="transportType" id="transportType6" value="OTHER" <?=$transportType6?>>					
						</td>
					</tr>
						</table>
						</td>
					</tr>
					<tr>
					<td nowrap="nowrap" class="listing-item" colspan="2"  style="padding-left:30px;pading-right:5px;">
						<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100">					
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:5px;pading-right:5px;" colspan="2">
						 <strong>*Identification </strong>
						</td>						
						<td class="listing-item"   style="padding-left:5px;pading-right:5px;font-size:11px;" colspan="2" nowrap="true">
							<input type="text" name="identification" id="identification" value="<?=htmlspecialchars($identification);?>" size="24"/>
						</td>					
					</tr>	
					<tr>
						<td nowrap="nowrap" class="listing-item"  style="padding-left:5px;pading-right:5px;" colspan="2">
						 Documentary refrences:
						</td>						
						<td class="listing-item" nowrap="nowrap"  style="padding-left:5px;pading-right:5px;" colspan="2">		
						</td>					
					</tr>
						</table>
					</td>
					</tr>					
				</table>
			</TD>
			<TD colspan="4" class="listing-item" align="left"  width="250px" valign="top">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="fieldName" colspan="2"  style="padding-left:5px;pading-right:5px;">
						1.16 *Entry B/P in EU
					</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="listing-item"  style="padding-left:30px;pading-right:5px;">
						
						</td>
						<td class="listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:11px;">
							<input type="text" name="entryBPEU" id="entryBPEU" value="<?=$entryBPEU?>">
						</td>
					</tr>					
				</table>
			</TD>
		</tr>
		<tr>			
			<TD colspan="4" valign="top">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="listing-item" style="padding-left:2px;pading-right:5px;" valign="top">1.17
						</td>
					</tr>					
				</table>
			</TD>						
		</tr>
		<tr>
			<TD width="300px" colspan="4" rowspan="2"> 
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="fieldName"  style="padding-left:5px;pading-right:5px;">
						1.18 *Description of Commodity:
					</td>					
					</tr>
					<tr>
						<td class="listing-item"  style="padding-left:30px;pading-right:5px;font-size:11px;">
							<textarea name="commodityDesciption" id="commodityDesciption"><?=$commodityDesciption?></textarea>
						</td>
					</tr>
				</table>
			</TD>
			<TD colspan="2" class="fieldName" align="left"  rowspan="2" valign="top">1.19 *Commodity code(HS Code)</TD>	
			<TD class="listing-item" align="left" valign="top" style="padding-left:5px;pading-right:5px;" align="center">
				<input type="text" name="commodityCode" id="commodityCode" value="<?=$commodityCode?>" size="12">
			</TD>
		</tr>
		<tr>
			<td>
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="listing-item" colspan="2"  style="padding-left:5px;pading-right:5px;">
						1.20 Quantity :
					</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:20px;pading-right:5px;">
						 *NET.WT.:
						</td>
						<td class="listing-item" nowrap="nowrap"  style="padding-left:5px;pading-right:5px;font-size:11px;">
							<input type="text" name="netWt" id="netWt" value="<?=$netWt?>" size="7" style="text-align:right;" onkeyup="disTxtVal();" autocomplete="off">&nbsp;<strong>KGS.</strong>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="fieldName"  style="padding-left:20px;pading-right:5px;" valign="top">
						*GR.WT.:
						</td>
						<td class="listing-item" style="padding-left:5px;pading-right:5px;font-size:11px;" nowrap="true">
							<input type="text" name="grWt" id="grWt" value="<?=$grWt?>" size="7" style="text-align:right;" autocomplete="off">&nbsp;
							<strong>KGS</strong>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	<tr>
			<TD colspan="6">
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<tr>
						<td nowrap="nowrap" class="fieldName" valign="top">
							1.21 *Temperature of product
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" style="padding-left:30px;pading-right:5px;">
							<table cellspacing='0' cellpadding='0' width="50%">
							<tr><TD height="5"></TD></tr>
							<tr>						
								<td nowrap="nowrap" class="listing-item" valign="top" align="right">
									Ambient
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									<INPUT type="radio" name="proTempType" id="proTempType1" value="AMB" onclick="disTxtVal();" <?=$proTempType1?>>
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="right">
									Chilled
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									<INPUT type="radio" name="proTempType" id="proTempType2" value="CHI" onclick="disTxtVal();" <?=$proTempType2?>>
								</td>						
								<td nowrap="nowrap" class="listing-item" valign="top" align="right">
										FROZEN
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									<INPUT type="radio" name="proTempType" id="proTempType3" value="FRO" onclick="disTxtVal();" <?=$proTempType3?>>
								</td>
							</tr>	
							</table>
						</td>
					</tr>				
				</table>
			</TD>
			<TD class="listing-item" valign="top">
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="fieldName" valign="top">
							1.22 *Number of Packages : 
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="listing-item" align="center">
							<input type="text" name="noOfPackage" id="noOfPackage" value="<?=$noOfPackage?>" size="4" onkeyup="disTxtVal();" style="text-align:right;" autocomplete="off">
						</td>
					</tr>					
				</table>
			</TD>
		</tr>
<tr>
			<TD colspan="6">
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<tr>
						<td nowrap="nowrap" class="listing-item" valign="top">
							1.23 Identification of container / Seal number:
						</td>
					</tr>	
					<tr>
						<td style="padding-left:30px;pading-right:5px;" >
							<table cellspacing='0' cellpadding='0' width="80%">
							<tr><TD height="5"></TD></tr>
							<tr>
								<td class="listing-item" valign="top" align="center">
									<table cellspacing='0' cellpadding='0' class="tdBoarder">
									<tr>
										<td nowrap="nowrap" class="fieldName" valign="top">
											*CONTAINER NO.: 
										</td>
										<td nowrap="nowrap" class="listing-item" align="center">
											<input type="text" name="containerNo" id="containerNo" value="<?=$containerNo?>" size="24">
										</td>
									</tr>					
									</table>
								</td>					
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									
									<table cellspacing='0' cellpadding='0' class="tdBoarder">
									<tr>
										<td nowrap="nowrap" class="fieldName" valign="top">
											&nbsp;*SEAL NO.:
										</td>
										<td nowrap="nowrap" class="listing-item" align="center">
											<input type="text" name="sealNo" id="sealNo" value="<?=$sealNo?>" size="24">
										</td>
									</tr>					
								</table>
								</td>					
							</tr>	
							</table>
						</td>
					</tr>				
				</table>
			</TD>
			<TD valign="top">
				<table cellspacing='2' cellpadding='2' class="tdBoarder">
					<tr>
						<td  class="fieldName" valign="top">
							1.24 *Type of Packaging
						</td>
					</tr>	
					<tr>
						<td class="listing-item" align="center">
							<textarea name="typeOfPackaging" id="typeOfPackaging"><?=$typeOfPackaging?></textarea>
						</td>
					</tr>					
				</table>
			</TD>
		</tr>	
	<tr>
			<TD colspan="7">
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<tr>
						<td nowrap="nowrap" class="fieldName" valign="top">
							1.25 Commodities certified for 
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" style="padding-left:30px;pading-right:5px;" colspan="5">
							<table cellspacing='0' cellpadding='0' width="80%">
							<tr><TD height="10"></TD></tr>
							<tr>
								<td nowrap="nowrap" class="listing-item" valign="top" align="left">
									<table cellspacing='0' cellpadding='0' width="200">
									<tr>
										<td nowrap="nowrap" class="listing-item" valign="top" align="center">
										Human consumption
										</td>
										<td nowrap="nowrap" valign="top" align="right">
											<select name="humanConsumption" id="humanConsumption">
											<option value="Y" <?if ($humanConsumption=='Y') echo "Selected";?>>Yes</option>
											<option value="N" <?if ($humanConsumption=='N') echo "Selected";?>>No</option>
											</select>
										</td>
									</tr>
									</table>
								</td>						
							</tr>	
							</table>
						</td>
					</tr>				
				</table>
			</TD>
		</tr>	
	<tr>
			<TD width="300px" colspan="4" class="listing-item">1.26
			</TD>
			<TD colspan="3" rowspan="1" class="listing-item" align="left" >
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
							<tr><TD height="10"></TD></tr>
							<tr>
								<td nowrap="nowrap" class="listing-item" valign="top" align="left">
									<table cellspacing='0' cellpadding='0' width="100">
									<tr>
										<td nowrap="nowrap" class="fieldName" valign="top" align="left">
										1.27 For import or admission into EU
										</td>
										<td nowrap="nowrap" valign="top" align="right">
											<select name="admissionEU" id="admissionEU">
											<option value="Y" <?if ($admissionEU=='Y') echo "Selected";?>>Yes</option>
											<option value="N" <?if ($admissionEU=='N') echo "Selected";?>>No</option>
											</select>
										</td>
									</tr>
									</table>
								</td>						
							</tr>	
							</table>
			</TD>			
		</tr>
	<tr>
			<TD colspan="7">
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<tr>
						<td nowrap="nowrap" class="listing-item" valign="top">		
							<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
							<tr>
								<td nowrap="nowrap" class="listing-item" valign="top">
									1.28 Identification of the commodities
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top">
									Approval no of establishment:&nbsp;&nbsp
									<span class="listing-item" id="approvalNoEst" style="font-weight:bold;"></span>
								</td>
							</tr>	
							</table>
						</td>
					</tr>	
					<tr><TD height="5"></TD></tr>
					<tr>
						<td nowrap="nowrap" style="padding-left:30px;pading-right:5px;" colspan="5">
							<table cellspacing='2' cellpadding='2' width="100%">
							<tr>
								<td nowrap="nowrap" class="fieldName" valign="top" align="center">	
									*Species	
								</td>	
								<td nowrap="nowrap" class="fieldName" valign="top" align="center">
									*Nature of commodity
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									Treatment type
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									Manufacturing plant
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">	
									Number of packages	
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									Net weight
								</td>					
							</tr>	
							<tr>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">	
									(Scientific name)
								</td>	
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">	
									( Cases)
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									(  KGS )
								</td>					
							</tr>
							<tr>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">	
									<textarea name="species" id="species"><?=$species?></textarea>
									<!--<input type="text" name="species" id="species" value="<?=$species?>">-->
								</td>	
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									<input type="text" name="natureOfCommodity" id="natureOfCommodity" value="<?=$natureOfCommodity?>">
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center" id="treatmentType">
									<!--<input type="text" name="treatmentType" id="treatmentType" value="<?=$treatmentType?>">-->
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center">
									<strong>PROCESSING PLANT</strong>
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center" id="identificationPackages" style="font-weight:bold;">	
									<!--<strong>928 M/CTN</strong>-->	
								</td>
								<td nowrap="nowrap" class="listing-item" valign="top" align="center" id="identificationNetWt" style="font-weight:bold;">
									<!--<strong>9280.000 KGS.</strong>-->
								</td>					
							</tr>
							</table>
						</td>
					</tr>				
				</table>
			</TD>
		</tr>
	</table>
			</TD>
		</tr>
<!-- New End -->
		</table>
		</TD>				
	</tr>
			<tr>
				  <td colspan="2" nowrap>
					
						</td>
					</tr>
					<tr>
					  <td colspan="2" height="5"></td>
					</tr>					
<tr><TD height="5"></TD></tr>
					<tr>
						  <td colspan="2" height="5"></td>
					 </tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HealthCertificate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateHealthCertificate(document.frmHealthCertificate);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('HealthCertificate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateHealthCertificate(document.frmHealthCertificate);">&nbsp;&nbsp;												</td>
												<input type="hidden" name="cmdAddNew" value="1">
											<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Health Certificate  </td>
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
								  <td colspan="3" align="right" style="padding-right:10px;">
								  <table width="200" border="0">
                        <tr>
                          <td><fieldset>
                            <legend class="listing-item">Print Health Certificate</legend>
							<table width="200" cellpadding="0" cellspacing="0" bgcolor="#999999">
					
                      <tr bgcolor="#FFFFFF">
                        <td class="listing-item" nowrap="nowrap" height="25">HC: </td>
                        <td nowrap="nowrap"><? $selHCId=$p["selHC"];?>&nbsp;
						<select name="selHC" id="selHC" onchange="disablePrintSOButton();">
						<option value="">-- Select --</option>
						<?php
						foreach ($hcRecords as $phcr) {
							$hcRecId    =	$phcr[0];
							$hcConsignee = $phcr[1];
							$hcCreated   = dateFormat($phcr[2]);	
							$displayTxt = "";
							$displayTxt = $hcConsignee."(".$hcCreated .")";
							$selected="";
							if ($selHCId==$hcRecId) $selected="Selected";
						?>
						<option value="<?=$hcRecId?>" <?=$selected?>><?=$displayTxt?></option>
						<? }?>
                        </select></td>
						<? if($print==true){?>
						<td nowrap="nowrap">&nbsp;<input name="cmdPrintInvoice" type="button" class="button" id="cmdPrintSO" onClick="return printSalesOrderWindow('PrintHealthCertificate.php',700,600);" value="Print HC" <? if($selSOId=="") echo $disabled="disabled"; ?> >
						<!-- Original <td nowrap="nowrap">&nbsp;<input name="cmdPrintInvoice" type="button" class="button" id="cmdPrintSO" onClick="return printSalesOrderWindow('PrintHealthCertificate.php',700,600);" value="Print SO" <? if($selSOId=="") echo $disabled="disabled"; ?> >-->
						</td>						
						<td>&nbsp;</td>
						<? }?>
                      </tr>
                    </table></fieldset></td>
                          </tr>
                      </table></td> </tr>
			<tr>
			<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$salesOrderSize;?>);"><?}?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSalesOrderList.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?></td>
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
	<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($healthCertificateRecords)>0) {
		$i = 0;
	?>
	<? if($maxpage>1){?>
                <tr  bgcolor="#f2f2f2" align="center">
                <td colspan="7" bgcolor="#FFFFFF" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		$nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"HealthCertificate.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"HealthCertificate.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"HealthCertificate.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Consignee Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Language</td>		
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($healthCertificateRecords as $hcr) {
		$i++;
		$healthCertificateRecId	= $hcr[0];
		$selDate	= dateFormat($hcr[3]);
		$consignee	= $hcr[2];
		$selLang	= $langRecs[$hcr[1]];
	?>
	<tr  bgcolor="WHITE">
		<td width="20">
			<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$healthCertificateRecId;?>" class="chkBox">
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$consignee;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left"><?=$selLang;?></td>		
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$healthCertificateRecId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='HealthCertificate.php';">
		</td>
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
         	<td colspan="7" style="padding-right:10px;">
		<div align="right">
		<?php 				 			  
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
	      			$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"HealthCertificate.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"HealthCertificate.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"HealthCertificate.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</table></td>
	</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$salesOrderSize;?>);"><?}?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSalesOrderList.php?selectFrom=<?=$dateFrom?>&selectTill=<?=$dateTill?>',700,600);"><? }?>
		</td>
		</tr>
		</table></td>
								</tr>
								<tr>
		<td colspan="3" height="5" >
		<input type="hidden" name="editMode" value="<?=$editMode?>">
		<input type="hidden" name="hidProductRateListId" id="hidProductRateListId" value="<?=$productPriceRateListId?>" >
		</td>
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
		<input type='hidden' name='genPoId' id='genPoId' value="<?=$genPoId;?>" >	
	</table>
<?
	if ($editMode) {
?>
	<script>
		disTxtVal();
	</script>
<?
	}
?>

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
	</SCRIPT><SCRIPT LANGUAGE="JavaScript">
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
	</SCRIPT><SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "departureDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "departureDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
