function validateDistributorMaster(form)
{
	var distriName		= form.distriName.value;
	var contactPerson 	= form.contactPerson.value;
	var crPeriodFrom	= $("#crPeriodFrom").val();
	var creditPeriod 	= $("#creditPeriod").val();	
	
	if (distriName=="") {
		alert("Please enter a Distributor Name.");
		form.distriName.focus();
		return false;
	}
	
	if (contactPerson=="") {
		alert("Please enter a Contact Person Name.");
		form.contactPerson.focus();
		return false;
	}

	if (crPeriodFrom!="" && creditPeriod!="") {
		if (!checkNumber(creditPeriod)) {
			$("#creditPeriod").focus();
			return false;
		}
	}

	if (crPeriodFrom=="" && creditPeriod!="") {		
		alert("Please select credit period date.");
		$("#crPeriodFrom").focus();
		return false;
	}

	if (crPeriodFrom!="" && creditPeriod=="" ) {		
		alert("Please enter credit period days");
		$("#creditPeriod").focus();
		return false;
	}
	
	
	var rowCount	=	document.getElementById("hidTableRowCount").value;
	var stateSelected = false;
	
	if (rowCount>0) {
		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var state	= document.getElementById("state_"+i);
				var city	= document.getElementById("city_"+i);		
				var billState   = document.getElementById("billState_"+i);
				var selTaxType	= document.getElementById("selTaxType_"+i);
				var billingFormF = document.getElementById("billingForm_"+i);
				var area = document.getElementById("area_"+i);
				var octroiApplicable = document.getElementById("octroiApplicable_"+i);
				var entryTaxApplicable = document.getElementById("entryTaxApplicable_"+i);
				//var octroiPercent = document.getElementById("octroiPercent_"+i);
				
					if (state.value=="") {
						alert("Please select a State.");
						state.focus();
						return false;
					}
					if (city.value=="") {
						alert("Please select a City.");
						city.focus();
						return false;
					}
					if (area.value=="") {
						alert("Please select a Area.");
						area.focus();
						return false;
					}
		
					if (billState.value=="") {
						alert("Please select a billing state.");
						billState.focus();
						return false;
					}
					/*
					if (octroiApplicable.checked) {
		
						
						if ((octroiPercent.value=="" || octroiPercent.value==0)) {
							alert("Please enter Octroi Percent.");
							octroiPercent.focus();
							return false;
						}
		
						if (!checkNumber(octroiPercent.value)) {
							octroiPercent.focus();
							return false;
						}				
					}
					*/
					if (entryTaxApplicable.checked) {
						var entryTaxPercent = document.getElementById("entryTaxPercent_"+i);
						
						if ((entryTaxPercent.value=="" || entryTaxPercent.value==0)) {
							alert("Please enter Entry Tax Percent.");
							entryTaxPercent.focus();
							return false;
						}
		
						if (!checkNumber(entryTaxPercent.value)) {
							entryTaxPercent.focus();
							return false;
						}				
					}
					
					if (selTaxType.value=="") {
						alert("Please select VAT/CST.");
						selTaxType.focus();
						return false;
					}
			
					if (billingFormF.value=="") {
						alert("Please select Billing on Form.");
						billingFormF.focus();
						return false;
					}
					if (state.value!="") {
						stateSelected = true;
					}
			}
		}  // For Loop Ends Here
	} // Row Count checking End

	if (stateSelected==false) {
		alert("Please add atleast one state");
		return false;
	}
	
	/* OLD
	if (!validateDistStateRepeat()) {
		return false;
	}	
	*/
	if (!chkLocRepeat()) {
		return false;
	}


	var tblRowCount	= document.getElementById("hidBankACTbleRowCount").value;
	if (tblRowCount>0) {
		for (i=0; i<tblRowCount; i++) {
			var bStatus = document.getElementById("bStatus_"+i).value;
			if (bStatus!='N') {
				var bankName 	= document.getElementById("bankName_"+i);
				var accountNo 	= document.getElementById("accountNo_"+i);

				if (bankName.value!="" && accountNo.value=="") {
					alert("Please enter a bank account no.");
					accountNo.focus();
					return false;
				}

				if (bankName.value=="" && accountNo.value!="") {
					alert("Please enter a bank name.");
					bankName.focus();
					return false;
				}
			}
		}
	}

	if (!confirmSave()) return false;	
	return true;
}	
	// If bill state = state => VAT else CST	
	function displayTaxType(i)
	{	
		var state	= document.getElementById("state_"+i).value;
		var billState   = document.getElementById("billState_"+i).value;		
		if (billState==state && billState!="" && state!="") {
			document.getElementById("selTaxType_"+i).value = 'VAT';
		} else if (billState!=state && billState!="" && state!="") {
			document.getElementById("selTaxType_"+i).value = 'CST';
		} 
		//alert(state+"==="+billState);
		displayBilling(i);
	}	

	function displayBilling(i)
	{	
		var state	= document.getElementById("state_"+i).value;		
		var taxType	= document.getElementById("selTaxType_"+i);
		var hidBForm	= document.getElementById("hidBillingForm_"+i).value;
		document.getElementById('billingForm_'+i).length=0
		
		if (state!="") {
			if (taxType.value=='VAT') {
				addOption(hidBForm,'billingForm_'+i,'VN','Normal');
				addOption(hidBForm,'billingForm_'+i,'ZP','Zero Percent');
			} else if (taxType.value=='CST') {
				addOption(hidBForm,'billingForm_'+i,'','-- Select --');
				addOption(hidBForm,'billingForm_'+i,'FF','Form F');
				addOption(hidBForm,'billingForm_'+i,'FC','Form C');
				addOption(hidBForm,'billingForm_'+i,'FN','Normal');
				addOption(hidBForm,'billingForm_'+i,'ZP','Zero Percent');
			} 
			else if (taxType.value=='GST') {
				addOption(hidBForm,'billingForm_'+i,'','-- Select --');
				addOption(hidBForm,'billingForm_'+i,'FF','Form F');
				addOption(hidBForm,'billingForm_'+i,'FC','Form C');
				addOption(hidBForm,'billingForm_'+i,'FN','Normal');
				addOption(hidBForm,'billingForm_'+i,'ZP','Zero Percent');
			} 
			else if (taxType.value=='IGST') {
				addOption(hidBForm,'billingForm_'+i,'','-- Select --');
				addOption(hidBForm,'billingForm_'+i,'FF','Form F');
				addOption(hidBForm,'billingForm_'+i,'FC','Form C');
				addOption(hidBForm,'billingForm_'+i,'FN','Normal');
				addOption(hidBForm,'billingForm_'+i,'ZP','Zero Percent');
			} 
			
			else {
				addOption(hidBForm,'billingForm_'+i,'','-- Select --');	
			}
		} else {				
			addOption('','billingForm_'+i,'','-- Select --');
		}
	}

//ADD MULTIPLE Item- ADD ROW START
function addNewDistributorStateRow(tableId, selStateId, billingAddress, deliveryAddress, pinCode, telNo, faxNo, mobNo, vatNo, tinNo, cstNo,selTaxType, billingForm, mode, distStateEntryId, billingStateId, sameBillingAdrChk, distCityEntryId, octroiApplicable, octroiPercent, octroiExempted, entryTaxApplicable, entryTaxPercent, entryTaxExempted, cityContactPerson, openingBalance, crLimit,eccNo)
{	
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	var iteration	= lastRow+1;
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
	var cell6	= row.insertCell(5);
	var cell7	= row.insertCell(6);
	var cell8	= row.insertCell(7);
					
	cell1.className	= "listing-item"; cell1.align	= "center";cell1.noWrap = "true";cell1.vAlign="top";
	cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";cell2.vAlign="top";
        cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";cell3.vAlign="top";
        cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";cell4.vAlign="top";
	cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";cell5.vAlign="top";
	cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";cell6.vAlign="top";
        cell7.className	= "listing-item"; cell7.align	= "center";cell7.noWrap = "true";cell7.vAlign="top";
        cell8.className	= "listing-item"; cell8.align	= "center";cell8.noWrap = "true";cell8.vAlign="middle";
		
	if (sameBillingAdrChk=='Y') var sameBillChk = "checked";
	else var sameBillChk = "";
	
	if (octroiApplicable=='Y') var octroiAppChk = "checked";
	else var octroiAppChk = "";

	if (octroiExempted=='Y') var octroiExemptedChk = "checked";
	else var octroiExemptedChk = "";

	if (entryTaxApplicable=='Y') var entryTaxAppChk = "checked";
	else var entryTaxAppChk = "";

	if (entryTaxExempted=='Y') var entryTaxExemptedChk = "checked";
	else var entryTaxExemptedChk = "";
	
	var stateList = "<select name='state_"+fieldId+"' id='state_"+fieldId+"' onChange=\"xajax_getCityList(document.getElementById('state_"+fieldId+"').value,'"+fieldId+"', '"+mode+"'); xajax_chkOctroi(document.getElementById('state_"+fieldId+"').value, '', '"+fieldId+"');xajax_chkEntryTax(document.getElementById('state_"+fieldId+"').value, '"+fieldId+"');\" style='width:125px;'><option value=''>--Select--</option>";
	<?php
	
		if (sizeof($stateMasterRecs)>0) {			
			while ($sr=$stateMasterRecs->getRow()) {
						$stateId = $sr[0];
						$stateCode	= stripSlash($sr[1]);
						$stateName	= stripSlash($sr[2]);	
						//$selected = "";
						//if ($selStateId==$stateId) $selected = "Selected";
					?>					
	
		if (selStateId=="<?=$stateId?>") var selStateOpt = 'selected=true';
		else var selStateOpt = '';
		stateList += "<option value='<?=$stateId?>' "+selStateOpt+"><?=$stateName?></option>";
	<?php
			}
		}
	?>
		stateList += "</select>";

	var cityList 	= "<select name='city_"+fieldId+"' id='city_"+fieldId+"' onChange=\"xajax_getAreaList(document.getElementById('city_"+fieldId+"').value,'"+fieldId+"', '"+mode+"', ''); xajax_chkOctroi(document.getElementById('state_"+fieldId+"').value, document.getElementById('city_"+fieldId+"').value, '"+fieldId+"');\" style='width:125px;'>";	
	cityList 	+= "<option value=''>-- Select --</option>";	
	cityList 	+= "</select>";

	var areaList 	= "<select name='area_"+fieldId+"[]' id='area_"+fieldId+"' multiple='true' size='5'>";	
	areaList 	+= "<option value=''>--Select--</option>";	
	areaList 	+= "</select>";

	var stateNCity	= "<table>";
	stateNCity 	+= "<tr><td class='row-listing-head' nowrap='true'>*State</td><td align='left'>"+stateList+"</td></tr>";	
	stateNCity 	+= "<tr><td class='row-listing-head' nowrap='true'>*City</td><td align='left'>"+cityList+"</td></tr>";
	stateNCity 	+= "<tr><td class='row-listing-head' nowrap='true'>*Area</td><td align='left'>"+areaList+"</td></tr>";	
	stateNCity 	+= "<tr><td height='5'></td></tr>";
	stateNCity 	+= "<tr><td class='row-listing-head' nowrap='true' title='Location Id'>Loc ID</td><td align='left'><input type='text' name='locId_"+fieldId+"' id='locId_"+fieldId+"' value='' style='border:none; font-weight:bold; text-align:center;' readonly size='5' /><!--Location Id--></td></tr>";
	stateNCity    += "</table>";	

	var billState = "<select name='billState_"+fieldId+"' id='billState_"+fieldId+"' onChange=\"displayTaxType("+fieldId+");\" style='width:95px;'><option value=''>--Select--</option>";
	<?php
		foreach ($billingStateRecords as $bsr) {
			$billStateId	= $bsr[0];
			$billStateName	= $bsr[1];		
	?>	
	if (billingStateId==<?=$billStateId?>) var selBillStateOpt = 'selected=true';
	else var selBillStateOpt = '';

	billState += "<option value='<?=$billStateId?>' "+selBillStateOpt+"><?=$billStateName?></option>";
	<?php
		}
	?>
	billState += "</select>";

	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIngItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidDistStateEntryId_"+fieldId+"' type='hidden' id='hidDistStateEntryId_"+fieldId+"' value='"+distStateEntryId+"'><input name='distCityEntryId_"+fieldId+"' type='hidden' id='distCityEntryId_"+fieldId+"' value='"+distCityEntryId+"'><input name='hidBillingForm_"+fieldId+"' type='hidden' id='hidBillingForm_"+fieldId+"' value='"+billingForm+"'><input name='hidSelTaxType_"+fieldId+"' type='hidden' id='hidSelTaxType_"+fieldId+"' value='"+selTaxType+"'>";

	var billAddress 	= "<table>";
	billAddress 	+= "<tr><td class='row-listing-head' nowrap='true' colspan='2'><textarea name='billingAddress_"+fieldId+"' id='billingAddress_"+fieldId+"' rows='5' cols='15'>"+unescape(billingAddress)+"</textarea></td></tr>";
	billAddress 	+= "<tr><td class='row-listing-head' nowrap='true'>Pin Code</td><td align='center'><input type='text' name='pinCode_"+fieldId+"' id='pinCode_"+fieldId+"' value='"+pinCode+"' size='5'></td></tr>";
	billAddress    += "</table>";

	var delvryAddress 	= "<table>";
	delvryAddress 	+= "<tr><td nowrap><input type='checkbox' name='sameBillingAdr_"+fieldId+"' id='sameBillingAdr_"+fieldId+"' class='chkBox' value='Y' onclick='deliverySame("+fieldId+");' "+sameBillChk+"></td><td class='listing-item' style='line-height:normal; font-size:10px;'>Same as Billing Address</td></tr>";
	delvryAddress 	+= "<tr><td nowrap='true' colspan='2' align='center'><textarea name='deliveryAddress_"+fieldId+"' id='deliveryAddress_"+fieldId+"' rows='5' cols='15'>"+unescape(deliveryAddress)+"</textarea></td></tr>";
	delvryAddress    += "</table>";
	
	var contacts 	= "<table>";
	contacts 	+= "<tr><td class='row-listing-head' nowrap='true'>Person</td><td align='center'><input type='text' name='cityContactPerson_"+fieldId+"' id='cityContactPerson_"+fieldId+"' value=\""+unescape(cityContactPerson)+"\" size='8'></td></tr>";
	contacts 	+= "<tr><td class='row-listing-head' nowrap='true'>Tel No</td><td align='center'><input type='text' name='telNo_"+fieldId+"' id='telNo_"+fieldId+"' value='"+telNo+"' size='8'></td></tr>";
	contacts 	+= "<tr><td class='row-listing-head' nowrap='true'>Fax No</td><td align='center'><input type='text' name='faxNo_"+fieldId+"' id='faxNo_"+fieldId+"' value='"+faxNo+"' size='8'></td></tr>";
	contacts 	+= "<tr><td class='row-listing-head' nowrap='true'>Mob No</td><td align='center'><input type='text' name='mobNo_"+fieldId+"' id='mobNo_"+fieldId+"' value='"+mobNo+"' size='8'></td></tr>";
	contacts    += "</table>";

	var taxRegNos 	= "<table >";
	taxRegNos 	+= "<tr><td class='row-listing-head' nowrap='true'>VAT No</td><td align='center'><input type='text' name='vatNo_"+fieldId+"' id='vatNo_"+fieldId+"' value='"+vatNo+"' size='12'></td></tr>";
	taxRegNos 	+= "<tr><td class='row-listing-head' nowrap='true'>TIN No</td><td align='center'><input type='text' name='tinNo_"+fieldId+"' id='tinNo_"+fieldId+"' value='"+tinNo+"' size='12'></td></tr>";
	taxRegNos 	+= "<tr><td class='row-listing-head' nowrap='true'>CST No</td><td align='center'><input type='text' name='cstNo_"+fieldId+"' id='cstNo_"+fieldId+"' value='"+cstNo+"' size='12'></td></tr>";
	taxRegNos 	+= "<tr><td class='row-listing-head' nowrap='true'>ECC No</td><td align='center'><input type='text' name='eccNo_"+fieldId+"' id='eccNo_"+fieldId+"' value='"+eccNo+"' size='12'></td></tr>";
	taxRegNos 	+= "<tr><td class='row-listing-head' nowrap='true'>GISTIN No</td><td align='center'><input type='text' name='gistinNo_"+fieldId+"' id='gistinNo_"+fieldId+"' value='' size='12'></td></tr>";

	taxRegNos    += "</table>";

	var octroiCol 	= "<table cellpadding='0' celspacing='0'>";
	octroiCol 	+= "<tr><td class='row-listing-head' nowrap='true'>Applicable</td><td align='center'><input type='checkbox' name='octroiApplicable_"+fieldId+"' id='octroiApplicable_"+fieldId+"' class='chkBox' value='Y' "+octroiAppChk+" /></td></tr>";
	octroiCol 	+= "<tr><td class='row-listing-head' nowrap='true'>Exempted</td><td align='center'><input type='checkbox' name='octroiExempted_"+fieldId+"' id='octroiExempted_"+fieldId+"' class='chkBox' value='Y' "+octroiExemptedChk+" /></td></tr>";	
	octroiCol    += "</table>";

	var entryTaxCol = "<table cellpadding='0' celspacing='0'>";
	entryTaxCol 	+= "<tr><td class='row-listing-head' nowrap='true'>Applicable</td><td align='center'><input type='checkbox' name='entryTaxApplicable_"+fieldId+"' id='entryTaxApplicable_"+fieldId+"' class='chkBox' value='Y' "+entryTaxAppChk+"/></td></tr>";

	entryTaxCol 	+= "<tr><td class='row-listing-head' nowrap='true'>Tax(%)</td><td align='center'><input type='text' name='entryTaxPercent_"+fieldId+"' id='entryTaxPercent_"+fieldId+"' style='text-align:right;' size='2' value='"+entryTaxPercent+"' autocomplete='off' /></td></tr>";

	entryTaxCol 	+= "<tr><td class='row-listing-head' nowrap='true'>Exempted</td><td align='center'><input type='checkbox' name='entryTaxExempted_"+fieldId+"' id='entryTaxExempted_"+fieldId+"' class='chkBox' value='Y' "+entryTaxExemptedChk+"/></td></tr>";	
	entryTaxCol    += "</table>";

	 
	var taxDtls	= "<table cellpadding='0' celspacing='0'>";
	taxDtls 	+= "<tr><td class='listing-item' nowrap='true' style='line-height:normal;'><fieldset style='border-right:0px; border-left:0px; border-bottom:0px; padding-bottom:0px;'><legend>Octroi</legend>"+octroiCol+"</fieldset></td></tr>";	
	taxDtls 	+= "<tr><td class='listing-item' nowrap='true' style='line-height:normal;'><fieldset style='border-right:0px; border-left:0px; border-bottom:0px; padding-bottom:0px;'><legend>Entry Tax</legend>"+entryTaxCol+"</fieldset></td></tr>";	
	taxDtls    	+= "</table>";

	if (selTaxType=='VAT') var selVatType = 'selected=true';
	else 	var selVatType = '';
	if (selTaxType=='CST') var selCstType = 'selected=true';
	else var selCstType = '';
	if (selTaxType=='GST') var selCstType = 'selected=true';
	else var selCstType = '';
	if (selTaxType=='IGST') var selCstType = 'selected=true';
	else var selCstType = '';
	
	var taxType = "<select name='selTaxType_"+fieldId+"' id='selTaxType_"+fieldId+"' onChange='displayBilling("+fieldId+");'><option value=''>--Select--</option>";
	taxType     += "<option value='VAT' "+selVatType+">VAT</option>";
	taxType     += "<option value='CST' "+selCstType+">CST</option>";
	taxType     += "<option value='GST' "+selCstType+">GST</option>";
	taxType     += "<option value='IGST' "+selCstType+">IGST</option>";
	taxType     += "</select>";

	// EXCISE EXEMPTION FOR EXPORT
	/*
	var exExmptType = "<select name='exExmptType_"+fieldId+"' id='exExmptType_"+fieldId+"'><option value=''>--Select--</option>";
	exExmptType     += "<option value='VAT'>VAT</option>";
	exExmptType     += "<option value='CST'>CST</option>";
	exExmptType     += "</select>";
	*/

	var billingForm = "<select name='billingForm_"+fieldId+"' id='billingForm_"+fieldId+"'><option value=''>--Select--</option>";
	billingForm	+= "</select>";

	var ddlExBillingForm = "<select name='exBillingForm_"+fieldId+"' id='exBillingForm_"+fieldId+"'>";
	<?php
	foreach ($exBillingFormArr as $exBillKey=>$exBillValue) {
	?>
	ddlExBillingForm += "<option value='<?=$exBillKey?>'><?=$exBillValue?></option>";
	<?php } ?>
	ddlExBillingForm += "</select>";

	var billingDtls	= "<table cellpadding='0' celspacing='0'>";
	billingDtls 	+= "<tr><td class='row-listing-head' nowrap='true'>*Billing From</td><td align='left'>"+billState+"</td></tr>";	
	billingDtls 	+= "<tr><td class='row-listing-head' nowrap='true'>*VAT/CST/GST/IGST/</td><td align='left'>"+taxType+"</td></tr>";
	billingDtls 	+= "<tr><td class='row-listing-head' nowrap='true' title='Sales tax billing form'>*St.Billing</td><td align='left'>"+billingForm+"</td></tr>";		
	billingDtls 	+= "<tr><td class='row-listing-head' nowrap='true' title='Excise billing form'>Ex.Billing</td><td align='left'>"+ddlExBillingForm+"</td></tr>";
	billingDtls    += "</table>";
	
	var billingNTaxReg	= "<table cellpadding='0' celspacing='0'>";
	billingNTaxReg 	+= "<tr><td class='listing-item' nowrap='true' style='line-height:normal;'>"+billingDtls+"</td></tr>";	
	billingNTaxReg 	+= "<tr><td class='listing-item' nowrap='true' style='line-height:normal;'><fieldset style='border-right:0px; border-left:0px; border-bottom:0px; padding-bottom:0px;'><legend>Tax Reg Nos.</legend>"+taxRegNos+"</fieldset></td></tr>";	
	billingNTaxReg    	+= "</table>";


	var lwActive = "<select name='lwStatus_"+fieldId+"' id='lwStatus_"+fieldId+"'>";
	lwActive     += "<option value='Y'>YES</option>";
	lwActive     += "<option value='N'>NO</option>";
	lwActive     += "</select>";

	var settings	= "<table>";
	settings 	+= "<tr><td class='row-listing-head' nowrap='true' title='Starting Date of Distributor location wise'>DOE</td><td align='left'><input type='text' name='locationStartDate_"+fieldId+"' id='locationStartDate_"+fieldId+"' size='8' value=''></td></tr>";
	settings 	+= "<tr><td class='row-listing-head' nowrap='true' title='Opening Balance'>OB</td><td align='left'><input type='text' name='openingBalance_"+fieldId+"' id='openingBalance_"+fieldId+"' size='8' value='"+openingBalance+"' style='text-align:right;' autocomplete='off' onkeyup='calcOBNCrLimit();'></td></tr>";	
	settings 	+= "<tr><td class='row-listing-head' nowrap='true'>Credit Limit</td><td align='left'><input type='text' name='creditLimit_"+fieldId+"' id='creditLimit_"+fieldId+"' size='8' value='"+crLimit+"' style='text-align:right;' autocomplete='off' onkeyup='calcOBNCrLimit();'></td></tr>";
	settings 	+= "<tr><td class='row-listing-head' nowrap='true'>Active</td><td align='left'>"+lwActive+"</td></tr>";		
	settings 	+= "<tr><td class='row-listing-head' nowrap='true' height='10px'>&nbsp;<input type='hidden' name='hidExportFlag_"+fieldId+"' id='hidExportFlag_"+fieldId+"' value='' readonly /><input type='hidden' name='hidDiffExportRemoved_"+fieldId+"' id='hidDiffExportRemoved_"+fieldId+"' value='' readonly /></td></tr>";
	settings 	+= "<tr id='exportOnlyFlag_"+fieldId+"'><td class='row-listing-head' nowrap='true' title='For export only'>Export Only</td><td align='left'><input type='checkbox' name='export_"+fieldId+"' id='export_"+fieldId+"' value='Y' class='chkBox' /></td></tr>";
	//settings 	+= "<tr id='exEmptRow_"+fieldId+"' style='display:none;'><td class='row-listing-head' nowrap='true' title='EXCISE EXEMPTION FOR EXPORT'>Ex.Exmpt</td><td align='left'>"+exExmptType+"</td></tr>";
	settings    	+= "</table>";

	cell1.innerHTML = stateNCity;	
	cell2.innerHTML = billingNTaxReg; 
	cell3.innerHTML = billAddress;		
	cell4.innerHTML = delvryAddress;
	cell5.innerHTML = contacts;	
	cell6.innerHTML = taxDtls; 
	cell7.innerHTML = settings;		
	cell8.innerHTML = imageButton+hiddenFields;

	// Create Calender
	calenderSetup(fieldId);

	fieldId		= parseInt(fieldId)+1;
	document.getElementById("hidTableRowCount").value = fieldId;
	assignLocationId();
	reloadLoc();
}

/*
function enableExExmpt(rowId)
{
	if ($("#export_"+rowId).attr("checked")==true) $("#exEmptRow_"+rowId).show();
	else {
		$("#exEmptRow_"+rowId).hide();
		$("#exExmptType_"+rowId).val('');
	}
}
*/

function setIngItemStatus(id)
{
	if (confirmRemoveLocLink(id)) {
		if (confirmRemoveItem()) {
			if ($("#export_"+id).attr("checked")==true) {
				$("#export_"+id).removeAttr("disabled");
				$("#export_"+id).removeAttr("checked");
				$("#exportOnly_"+id).removeAttr("disabled");
				$("#hidExportFlag_"+id).val('');
				id = id+"_"+1;
				$("#hidDiffExportRemoved_"+id).val('Y');
			}
			
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none'; 		
			calcOBNCrLimit();
			assignLocationId();
			reloadLoc();
		}
	}
	return false;
}
/* ------------------------------------------------------ */
// Duplication check starts here
/* ------------------------------------------------------ */
var cArr = new Array();
var cArri = 0;	
var dArr = new Array();
var dArri = 0;
//var aArr = new Array();
function validateDistStateRepeat()
{	
	if (Array.indexOf != 'function') {  
		Array.prototype.indexOf = function(f, s) {
		if (typeof s == 'undefined') s = 0;
			for (var i = s; i < this.length; i++) {   
			if (f === this[i]) return i; 
			} 
		return -1;  
		}
	}
	
    var rc = document.getElementById("hidTableRowCount").value;

    var arr = new Array();
    var arri=0;
   var aArr = new Array();

    for (j=0; j<rc; j++) {
    	var status = document.getElementById("status_"+j).value;
    	if (status!='N') {
        	var state = document.getElementById("state_"+j).value;
		var city = document.getElementById("city_"+j).value;
		var area = document.getElementById("area_"+j);		
		var areaOptLength = area.options.length;	

		var areaArr = new Array();
		var aV = 0;
		for(var i = 0; i <areaOptLength; i++) 
		{
			if (area.options[i].selected) {
				var areaR = area.options[i].value;	
				if (areaR!="") {
					areaArr[aV] = areaR;
				}				
				if (aArr.length>0) {				
					if (aArr[city]==0 && aArr.length>0) {
						alert(" Areas already selected.");
						return false;
					} else {
						aArr[city] = areaR;	
					}
				} else {
					aArr[city] = areaR;	
				}				
								
				if (checkArr(areaR) > 0 && areaR!=0) {
					alert("Area cannot be duplicate.");
					cleanUpArr(areaR);
					return false;
				} else if (areaR!="") {
					 cArr[cArri++] = areaR;	
				}
				aV++;
			}
		}
		var areaVal = implode(',',areaArr);
		var rv = state+","+city+","+areaVal;		
		if (arr.indexOf(rv)!=-1) {
			alert(" Please make sure duplicate entry does not exist.");
			document.getElementById("state_"+j).focus();
			return false;	
		}		
        	arr[arri++] = rv;
      }
    }
    return true;
}
	
	function checkArr(val)
	{
		var count = 0;
		for (i=0; i<cArr.length; i++) {
			if (cArr[i]==val) {
				count++;
				cArr.splice(i,1);
			}
		}
		return count;
	}

	function cleanUpArr(val)
	{
		var count = 0;
		for (i=0; i<cArr.length; i++) {
			if (cArr[i] != val) {
				cArr.splice(i,1);
			}
		}
	}

	function cUpAreaArr(val)
	{
		var count = 0;
		for (i=0; i<cArr.length; i++) {
			if (cArr[i] != val) {
				cArr.splice(i,1);
			}
		}
	}
// ------------------------------------------------------
// Duplication check Ends here
// ------------------------------------------------------

	function deliverySame(rowId)
	{
		
		if (document.getElementById("sameBillingAdr_"+rowId).checked) {
			document.getElementById("deliveryAddress_"+rowId).value = document.getElementById("billingAddress_"+rowId).value;
		} else document.getElementById("deliveryAddress_"+rowId).value = "";
	}

	/*Check Octroi Applicable*/
	function chkOctroi(rowId, octroiApplicable)
	{		
		if (octroiApplicable=='Y') document.getElementById('octroiApplicable_'+rowId).checked=true;
		else document.getElementById('octroiApplicable_'+rowId).checked=false;		
	}

	/*
		Check Entry Tax Applicable
	*/
	function chkEntryTax(rowId, entryTaxApplicable)
	{		
		if (entryTaxApplicable=='Y') document.getElementById('entryTaxApplicable_'+rowId).checked=true;
		else document.getElementById('entryTaxApplicable_'+rowId).checked=false;		
	}

	/* Calculate OB and Cr Limit */
	function calcOBNCrLimit()
	{		
		var rowCount	=	document.getElementById("hidTableRowCount").value;
		
		var totalOB	= 0;
		var totalCrL	= 0;
		for (i=0; i<rowCount; i++) {
			var rowStatus = document.getElementById("status_"+i).value;
			if (rowStatus!='N') {
				var ob = document.getElementById("openingBalance_"+i);
				var openingBalance	= (ob.value!=0)?parseFloat(ob.value):0;
				totalOB += openingBalance;
				
				var cl = document.getElementById("creditLimit_"+i);
				var creditLimit		= (cl.value!=0)?parseFloat(cl.value):0;
				totalCrL += creditLimit;
			} // Status chk ends here
		}  // For Loop Ends Here
			
		if (!isNaN(totalOB) && totalOB!=0) document.getElementById("openingBal").value = number_format(totalOB, 2, '.', '');
		else document.getElementById("openingBal").value = "";

		if (!isNaN(totalCrL) && totalCrL!=0) document.getElementById("creditLimit").value = number_format(totalCrL, 2, '.', '');
		else document.getElementById("creditLimit").value = "";		
	}

	function validateDistStatus(distributorId, rowId)
	{
		if (!confirm("Do you wish to change distributor status?")) {
			return false;
		}
		// Ajax 
		xajax_changeDistStatus(distributorId, rowId);		
		return true;
	}

	//ADD MULTIPLE Item- ADD ROW START
	function addNewBankAC(tableId)
	{
		var tbl		= document.getElementById(tableId);	
		var lastRow	= tbl.rows.length;	
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "bRow_"+fldId;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
		var cell4	= row.insertCell(3);
		var cell5	= row.insertCell(4);
		var cell6	= row.insertCell(5);
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		cell4.className	= "listing-item"; cell4.align	= "center";
		cell5.className	= "listing-item"; cell5.align	= "center"; cell5.id="locCellId_"+fldId;
		cell6.className	= "listing-item"; cell6.align	= "center";
		
		
		var locCell = "<table cellpadding='0' cellspacing='0'>";		
		locCell += "<tr>";
		for (var lc=1; lc<=maxLocId; lc++) {
			var lcFieldName = "locChk_"+lc+"_"+fldId;	
			locCell += "<td class='listing-item' style='border:0px;'><input type='checkbox' class='chkBox' value='"+lc+"' id='"+lcFieldName+"' name='"+lcFieldName+"' onclick='mngLocChk("+fldId+");'/>&nbsp;"+lc+"</td>";
		}
		locCell += "</tr>";
		locCell += "</table>";
		
		var ds = "N";	
		//if( fldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setBankACItemStatus('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
		var hiddenFields = "<input name='bStatus_"+fldId+"' type='hidden' id='bStatus_"+fldId+"' value=''><input name='bIsFromDB_"+fldId+"' type='hidden' id='bIsFromDB_"+fldId+"' value='"+ds+"'><input name='bankACEntryId_"+fldId+"' type='hidden' id='bankACEntryId_"+fldId+"' value=''><input type='hidden' name='selLoc_"+fldId+"' id='selLoc_"+fldId+"' value='' readonly />";	
				
		cell1.innerHTML	= "<input type='text' name='bankName_"+fldId+"' id='bankName_"+fldId+"' size='24'>";	
		cell2.innerHTML	= "<input type='text' name='accountNo_"+fldId+"' id='accountNo_"+fldId+"' size='24' autocomplete='off'>";
		cell3.innerHTML	= "<input type='text' name='branchLocation_"+fldId+"' id='branchLocation_"+fldId+"' size='24' autocomplete='off'>";
		cell4.innerHTML	= "<input type='checkbox' name='defaultAC_"+fldId+"' id='defaultAC_"+fldId+"' value='Y' class='chkBox' onclick=\"bacDefaultChk('"+fldId+"');\">";	
		cell5.innerHTML = locCell;
		cell6.innerHTML = imageButton+hiddenFields;	
		
		fldId		= parseInt(fldId)+1;	
		document.getElementById("hidBankACTbleRowCount").value = fldId;				
	}

	function setBankACItemStatus(id)
	{
		if (confirmRemoveItem()) {			
			document.getElementById("bStatus_"+id).value = document.getElementById("bIsFromDB_"+id).value;
			document.getElementById("bRow_"+id).style.display = 'none';
		}
		return false;
	}


	function mngLocChk(rowId)
	{
			var locArr = new Array();
			var i=0;
			for (var lc=1; lc<=parseInt(maxLocId); lc++) 
			{
				var lcFieldName = "locChk_"+lc+"_"+rowId;	
				if (document.getElementById(lcFieldName)!=null && document.getElementById(lcFieldName).checked)
				{
					var locVal = document.getElementById(lcFieldName).value;
					locArr[i++] = locVal;
				}			
			}
			var selectedLoc = implode(",",locArr);
			document.getElementById("selLoc_"+rowId).value = selectedLoc;
	}

	function chkInArray(val, arr)
	{
		for (var j=0; j<arr.length; j++)
		{
			if (val==arr[j]) return true;
		}
		return false;
	}

	function reloadLoc()
	{
		var rc = document.getElementById("hidBankACTbleRowCount").value;
		for (j=0; j<rc; j++) {
			var status = document.getElementById("bStatus_"+j).value;
			if (status!='N') {
				// For selecting already selected value
				var selLocIds =	document.getElementById("selLoc_"+j).value;
				var locArr = new Array();
				if (selLocIds!="") locArr = selLocIds.split(",");

				var locCell = "<table cellpadding='0' cellspacing='0'>";		
				locCell += "<tr>";
				for (var lc=1; lc<=maxLocId; lc++) {
					var lcFieldName = "locChk_"+lc+"_"+j;	
					var valExist = chkInArray(lc, locArr);
					
					var cbChecked = (valExist)?"checked":""; 
					locCell += "<td class='listing-item' style='border:0px;'><input type='checkbox' class='chkBox' value='"+lc+"' id='"+lcFieldName+"' name='"+lcFieldName+"' onclick='mngLocChk("+j+");' "+cbChecked+"/>&nbsp;"+lc+"</td>";
				}
				locCell += "</tr>";
				locCell += "</table>";

				document.getElementById("locCellId_"+j).innerHTML = locCell;
			}
		}
	}


	/* ------------------------------------------------------ */
	// Duplication check starts here
	/* ------------------------------------------------------ */
	var cArr = new Array();
	var cArri = 0;	
	function validateBankACRepeat()
	{			
		if (Array.indexOf != 'function') {  
			Array.prototype.indexOf = function(f, s) {
			if (typeof s == 'undefined') s = 0;
				for (var i = s; i < this.length; i++) {   
				if (f === this[i]) return i; 
				} 
			return -1;  
			}
		}
		
		var rc = document.getElementById("hidBankACTbleRowCount").value;
		var prevOrder = 0;
		var arr = new Array();
		var arri=0;

		for (j=0; j<rc; j++) {
			var status = document.getElementById("bStatus_"+j).value;
			if (status!='N') {
				var rv = document.getElementById("bankName_"+j).value;
				
				if ( arr.indexOf(rv) != -1 )    {
					alert("Please make sure the bank ac is not duplicate.");					
					document.getElementById("bankName_"+j).focus();
					return false;
				}		
				arr[arri++]=rv;
			}
		}
		return true;
	}

	function bacDefaultChk(rowId)
	{	
		if (!document.getElementById("defaultAC_"+rowId).checked) chk = false;
		else chk = true;	
		var rc = document.getElementById("hidBankACTbleRowCount").value;
		for (j=0; j<rc; j++) {
			document.getElementById("defaultAC_"+j).checked = false;
		}
		document.getElementById("defaultAC_"+rowId).checked = chk;
	}
	
	function createExportEntry(rowId)
	{		
		$orginal = $("#row_"+rowId);

		var $exportRow = $("#row_"+rowId).clone(true).attr("id", function() { return this.id +'_'+1; });
		$exportRow.find('select, textarea, :checkbox, :text, :file, input:hidden').attr( {'id': function() {  return this.id +'_'+1; }, 'name':function() { return modifyFieldName(this); }});
		$exportRow.find('#exportFlag_'+rowId).hide();
		$exportRow.find('#exportOnlyFlag_'+rowId).hide();
		
		var $originalSelects = $orginal.find('select');
		$exportRow.find('select').each(function(index, item) {			
			$(item).val( $originalSelects.eq(index).val() );		
		});

		//get original textareas into a jq object
		var $originalTextareas = $orginal.find('textarea');		
		$exportRow.find('textarea').each(function(index, item) {
			//set new textareas to value of old textareas
			$(item).val($originalTextareas.eq(index).val());
		});		
		$exportRow.insertAfter("#row_"+rowId);
		
		calenderSetup(rowId+'_'+1);

		$("#hidExportFlag_"+rowId+'_'+1).val(1);
		$("#hidExportFlag_"+rowId).val(1);
		$("#export_"+rowId).attr("disabled", true);
		$("#exportOnly_"+rowId).attr("disabled", true);		
	}

	function modifyFieldName(obj)
	{
		if (obj.multiple==true) {
			var fName = obj.name;
			var fnArr = fName.split('[]');
			var newField = fnArr[0]+'_'+1+'[]';
			return newField;
		} else {
			return obj.name +'_'+1;
		}
	}

	// Distributor Loc Repeat
	function chkLocRepeat()
	{
		if (Array.indexOf != 'function') {  
			Array.prototype.indexOf = function(f, s) {
			if (typeof s == 'undefined') s = 0;
				for (var i = s; i < this.length; i++) {   
				if (f === this[i]) return i; 
				} 
			return -1;  
			}
		}
		
		var rc = $("#hidTableRowCount").val();
		
		var aridx=0;
		var itemArr = new Array();
		var duplicateExist = false;
		for (j=0; j<rc; j++) {
			var status = $("#status_"+j).val();
			var exportChk = $("#export_"+j).attr("checked");

			if (status!='N' && !exportChk) {
				var state = $("#state_"+j).val();
				var city = $("#city_"+j).val();
				var area = $("#area_"+j).val();
				//alert(state+"::"+city+"::"+area);
				if (area==0 && area!='null') {
					$("#area_"+j+" option").each(function(){
						var area = $(this).val();
						var rv = state+","+city+","+area;
						if (itemArr.indexOf(rv)!=-1) {
							duplicateExist = true;
							$("#area_"+j).focus();
						}		
						itemArr[aridx++] = rv;
					});
				} else if (area != 'null') {
					$("#area_"+j+" option:selected").each(function(){
						var area = $(this).val();
						var rv = state+","+city+","+area;
						if (itemArr.indexOf(rv)!=-1) {
							duplicateExist = true;
							$("#area_"+j).focus();
						}		
						itemArr[aridx++] = rv;
					});
				}				
			}
		}

		if (duplicateExist) {
			alert("Area cannot be duplicate.");
			return false;	
		}
		return true;	
	}

	// Calender Display
	function calenderSetup(rowId)
	{
		Calendar.setup 
		(	
			{
			inputField  : "locationStartDate_"+rowId,         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "locationStartDate_"+rowId, 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
			}
		);
	}
	
	function chkExportOnly(rowId)
	{
		if ($("#exportOnly_"+rowId).attr("checked")==true) {
			$("#export_"+rowId).attr("disabled", true);
		} else {
			$("#export_"+rowId).removeAttr("disabled");
		}
	}

	var maxLocId = 0;
	function assignLocationId()
	{
		var itemCount	=	document.getElementById("hidTableRowCount").value;

		var j = 0;
		for (i=0; i<itemCount; i++) {
			var sStatus = document.getElementById("status_"+i).value;	
			if (sStatus!='N') {
				j++;
				document.getElementById("locId_"+i).value = j;
			}
		}
		maxLocId = j;
	}

	function confirmRemoveLocLink(rowId)
	{
		var rc = document.getElementById("hidBankACTbleRowCount").value;
		var removedLocId = document.getElementById("locId_"+rowId).value;

		var hasTag = false; 
		for (j=0; j<rc; j++) {
			var status = document.getElementById("bStatus_"+j).value;
			if (status!='N') {
				// For selecting already selected value
				var selLocIds =	document.getElementById("selLoc_"+j).value;
				var locArr = new Array();
				if (selLocIds!="") locArr = selLocIds.split(",");
			
				for (var lc=1; lc<=maxLocId; lc++) 
				{
					var valExist = chkInArray(lc, locArr);					
					if (valExist && removedLocId==lc) hasTag = true;
				}
			}
		}
		
		if (hasTag)
		{
			alert("The selected location is tagged with bank ac. In order to remove this location, please deselect the tagged location.");
			return false;
		}
		return true;
	}