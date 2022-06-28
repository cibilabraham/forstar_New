var selUnitId = 0;
var KG2LBS = 0;
function validatePurchaseOrder(form, validate)
{	
	
	var itemSelected = false;
	//var invoiceType 	= document.getElementById("invoiceType").value;
	var validDespatchDate	= document.getElementById("validDespatchDate").value;
	var selCustomer		= document.getElementById("selCustomer");
	var dischargePort	= document.getElementById("dischargePort");
	var paymentTerms	= document.getElementById("paymentTerms");
	var lastDate		= document.getElementById("lastDate"); 
	var selectDate		= document.getElementById("selectDate"); 
	var oneUSDToINR		= document.getElementById("oneUSDToINR"); 
	var selCountry 		= document.getElementById("selCountry"); 
	var selPort		= document.getElementById("selPort"); 
	var selAgent		= document.getElementById("selAgent"); 

	var poNo		= document.getElementById("poNo");
	var poDate		= document.getElementById("poDate");

	var mode		= document.getElementById("hidMode").value; /* $addMode=>$mode = 1; $editMode=> $mode = 0;*/
	var itemCount		= document.getElementById("hidTableRowCount").value;
	var selCurrency		= document.getElementById("selCurrency");

	if (oneUSDToINR.value=="") {
		alert("Please set USD value in master.");
		return false;
	}


	/*
	if (invoiceType=='S') {
		var sampleInvoiceNo = document.getElementById("sampleInvoiceNo");
		if (sampleInvoiceNo.value=="") {
			alert("Please enter a Sample Invoice Number.");
			sampleInvoiceNo.focus();
			return false;
		}
	} else {
		var proformaInvoiceNo	= document.getElementById("proformaInvoiceNo");
		if (proformaInvoiceNo.value=="") {
			alert("Please enter a Proforma Invoice Number.");
			proformaInvoiceNo.focus();
			return false;
		}
	}
	*/
	
	if (poNo.value=="" || poNo.value==0) {
		alert("Please enter a PO No.");
		poNo.focus();
		return false;
	}

	if (poDate.value=="") {
		alert("Please select a PO date.");
		poDate.focus();
		return false;
	}

	if (selCustomer.value=="") {
		alert("Please select a Customer.");
		selCustomer.focus();
		return false;
	}

	if (selCountry.value=="") {
		alert("Please select a Country.");
		selCountry.focus();
		return false;
	}

	if (selPort.value=="") {
		alert("Please select a Port.");
		selPort.focus();
		return false;
	}

	if (selAgent.value=="") {
		alert("Please select a Agent.");
		selAgent.focus();
		return false;
	}


	if (dischargePort.value=="") {
		alert("Please enter Port of Discharge.");
		dischargePort.focus();
		return false;
	}

	if (paymentTerms.value=="") {
		alert("Please select a Payment Term.");
		paymentTerms.focus();
		return false;
	}

	if (lastDate.value=="") {
		alert("Please select a Last Date for Shipment.");
		lastDate.focus();
		return false;
	}

	if (validDespatchDate==1) {
		alert(" Please make sure the selected date for shipment is a valid date. ");
		lastDate.focus();
		return false;
	}

	if (selectDate.value=="") {
		alert("Please select a order entry date.");
		selectDate.focus();
		return false;
	}

	if (convertTime(lastDate.value)<convertTime(selectDate.value)) {
		alert("Please check date of Despatch and entry date.");
		lastDate.focus();
		return false;
	}

	if (selCurrency.value=="")
	{
		alert("Please select a currency");
		selCurrency.focus();
		return false;
	}


	var chkListRowCount	= document.getElementById("chkListRowCount").value;
			
	for (var i=1; i<=chkListRowCount; i++) {
		var chkListId 	= document.getElementById("chkListId_"+i);
		var required	= document.getElementById("required_"+i);
		var chkListName	= document.getElementById("chkListName_"+i).value;
		
		if (required.value=="Y" && !chkListId.checked) {
			alert("Please verify "+chkListName);
			chkListId.focus();
			return false;
		}
	}
	

	for (i=0; i<itemCount; i++) {
	   var rowStatus = document.getElementById("status_"+i).value;	
       		if (rowStatus!='N') {
			var fish	= document.getElementById("selFish_"+i);
			var processCode	= document.getElementById("selProcessCode_"+i);
			//var eUCode	= document.getElementById("selEuCode_"+i);
			var brand	= document.getElementById("selBrand_"+i);
			var selGrade	= document.getElementById("selGrade_"+i);
			var freezingStage = document.getElementById("selFreezingStage_"+i);
			var frozenCode	= document.getElementById("selFrozenCode_"+i);
			var mCPacking	= document.getElementById("selMCPacking_"+i);
			var numMC	= document.getElementById("numMC_"+i);
			var pricePerKg	= document.getElementById("pricePerKg_"+i);

			if (fish.value=="") {
				alert("Please select a Fish.");
				fish.focus();
				return false;
			}

			if (processCode.value=="") {
				alert("Please select a Process Code.");
				processCode.focus();
				return false;
			}

			/*if (eUCode.value=="") {
				alert("Please select a EU Code.");
				eUCode.focus();
				return false;
			}*/

			if (brand.value=="") {
				alert("Please select a Brand.");
				brand.focus();
				return false;
			}

			if (selGrade.value=="") {
				alert("Please select a Grade.");
				selGrade.focus();
				return false;
			}
			
			if (freezingStage.value=="") {
				alert("Please select a Freezing Stage.");
				freezingStage.focus();
				return false;
			}

			if (frozenCode.value=="") {
				alert("Please select a Frozen Code.");
				frozenCode.focus();
				return false;
			}

			if (mCPacking.value=="") {
				alert("Please select MC Packing.");
				mCPacking.focus();
				return false;
			}

			if (numMC.value=="") {
				alert("Please enter No. of MC.");
				numMC.focus();
				return false;
			}	

			if ((pricePerKg.value=="" || pricePerKg.value==0) && validate) {
				alert("Please enter Price Per Kg in USD.");
				pricePerKg.focus();
				return false;
			}

			if (fish.value!="") {	
				itemSelected = true;
			}
		}
	} // Loop Ends here	
	
	if (!itemSelected) {
		alert("Please select atleast one item");
		return false;
	}


	if (!validateItemRepeat()) {
		return false;
	}

	if (mode==0) {
		//alert("hh");
		var splitTbleRowCount = document.getElementById("splitTbleRowCount").value;
		//alert("check");
		for (var i=0; i<splitTbleRowCount; i++) {				
			var rowStatus = document.getElementById("spoStatus_"+i).value;	
			var splitMCEntered = false;
			var priceEntered = true;
       			if (rowStatus!='N') {
				var invoiceType = document.getElementById("invoiceType_"+i);
				//alert(invoiceType);
				var proformaInvoiceNo = document.getElementById("proformaInvoiceNo_"+i);				
				if (invoiceType.value=="") {
					alert("Please select a Invoice Type.");
					invoiceType.focus();
					return false;
				}

				if (proformaInvoiceNo.value=="") {
					alert("Please enter a Proforma no.");
					proformaInvoiceNo.focus();
					return false;
				}
				
				var balanceMC = 0;
				for (var j=0; j<rowCount; j++) {	
					var selPCId	= document.getElementById("selPCId_"+j+"_"+i).value;
					var MCInPO	= document.getElementById("MCInPO_"+j+"_"+i);
					var splitNumMC 	= document.getElementById("MCInInv_"+j+"_"+i).value;
					var pricePerKg	= document.getElementById("pricePerKg_"+j+"_"+i).value;
					
					/*
					if (splitNumMC!="" && splitNumMC!=0 && (MCInPO.value=="" || MCInPO.value==0) ) {
						alert("Please enter MC as per PO.");	
						MCInPO.focus();
						return false;
					}
					*/

					if (splitNumMC!="" && splitNumMC!=0) {
						splitMCEntered = true;
						if (pricePerKg=="" || pricePerKg==0) priceEntered = false;
					}
					
				} // Split Row PC Ends Here
				
				if (!splitMCEntered) {
					alert("Please enter MC in this invoice.");
					proformaInvoiceNo.focus();
					return false;
				}

				if (!priceEntered) {
					alert("Please enter price per kg.");
					proformaInvoiceNo.focus();
					return false;
				}
			} // Status Check Ends here
		} // Split Row Ends Here

	} // Mode Check Ends here

	
	if (!confirmSave()) return false;
	else return true;
}

function calculateTotalValue()
{
	var totalValueUSD = 0;
	var oneUSDToINR		=	0;
	var totalValueINR	= 0;
	
	var frozenCode	=	document.getElementById("frozenCode").value;	
	var splitFilledWt	=	frozenCode.split("_");
	var filledWt		=	splitFilledWt[1];

	var mCPacking		=	document.getElementById("mCPacking").value;
	var splitNumPacks	=	mCPacking.split("_");
	var numPacks		=	splitNumPacks[1];
	
	var numMC		=	document.getElementById("numMC").value;
	var pricePerKg	=	document.getElementById("pricePerKg").value;
	
	oneUSDToINR 	=	document.getElementById("oneUSDToINR").value;
	
	totalValueUSD	=	filledWt *numPacks*numMC*pricePerKg;
	totalValueINR	=	totalValueUSD * oneUSDToINR;
	//alert(totalValueUSD);	
	if(!isNaN(totalValueUSD)){
		document.getElementById("valueInUSD").value = totalValueUSD;
		document.getElementById("valueInINR").value	= formatNumber(Math.abs(totalValueINR),2,'','.','','','','','');
	}
}

function extendedDateCheck(form)
{
	var d = new Date();
	var t_date = d.getDate();      // Returns the day of the month
	if (t_date<10) t_date	=	"0"+t_date;	
	var t_mon = d.getMonth() + 1;      // Returns the month as a digit
	if (t_mon<10) t_mon	=	"0"+t_mon;	
	var t_year = d.getFullYear();  // Returns 4 digit year	
	var curr_date	=	t_date + "/" + t_mon + "/" + t_year;
	
	CDT					=	curr_date.split("/");
	var CD_time		=	new Date(CDT[2], CDT[1], CDT[0]);
	
	var lastDate	=	document.getElementById("lastDate").value;	
	LDT				=	lastDate.split("/");
	var LD_time		=	new Date(LDT[2], LDT[1], LDT[0]);
	
	var one_day=1000*60*60*24

	//Calculate difference btw the two dates, and convert to days
	var extendedDays	=	Math.ceil((LD_time.getTime()-CD_time.getTime())/(one_day));
	
	if(extendedDays<0){
		alert("Last Date should be greater than or equal to current date");
		document.getElementById("lastDate").focus();
		return false;
	}
	return true;	
}

	function showInvRow(mode, proformaInvNo, sampleInvNo)
	{		
		var invoiceType = document.getElementById("invoiceType").value;
		if (invoiceType=='S') {
			document.getElementById("proformaInvNoRow").style.display="none";
			document.getElementById("sampleInvNoRow").style.display="";
			document.getElementById("proformaInvoiceNo").value = "";
			if (mode==1) xajax_getSampleInvoiceNo();
			else document.getElementById("sampleInvoiceNo").value = sampleInvNo;
		} else {
			document.getElementById("sampleInvNoRow").style.display="none";
			document.getElementById("proformaInvNoRow").style.display="";
			document.getElementById("sampleInvoiceNo").value = "";
			if (mode==1) xajax_getProformaInvoiceNo();
			else document.getElementById("proformaInvoiceNo").value = proformaInvNo;
		}
	}

	/* Show/hide Invoice Type Selection */
	function showInvoiceType()
	{
		var invoiceType = document.getElementById("invoiceType").value;		
	}

	function enableSPOButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableSPOButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

	
//ADD MULTIPLE Item- ADD ROW START
function addNewPOItem(tableId, poEntryId, selFishId, selProcessCodeId, selEuCodeId, selBrandId, selGradeId, selFreezingStageId, selFrozenCodeId, selMCPackingId, numMC, pricePerKg, valueInUSD, valueInINR, cpyItem, mode)
{
	var tbl		= document.getElementById(tableId);		
	var lastRow	= tbl.rows.length-1;	
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
	//var cell5=row.insertCell(3);
	var cell6	= row.insertCell(5);	
	var cell7	= row.insertCell(6);	
	var cell8	= row.insertCell(7);	
	var cell9	= row.insertCell(8);
	var cell10	= row.insertCell(9);
	var cell11	= row.insertCell(10);
	var cell12	= row.insertCell(11);
	var cell13	= row.insertCell(12);
	var cell14	= row.insertCell(13);
	
	if (mode==0) {
		var cell15	= row.insertCell(14);
		var cell16	= row.insertCell(15);
		//var cell17	= row.insertCell(16);
	} else {
		var cell15	= row.insertCell(14);
	}
	

	cell1.id = "srNo_"+fieldId;	
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
  cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";
	cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";
	cell7.className	= "listing-item"; cell7.align	= "center";cell7.noWrap = "true";
	cell8.className	= "listing-item"; cell8.align	= "center";cell8.noWrap = "true";
	cell9.className	= "listing-item"; cell9.align	= "center";cell9.noWrap = "true";
	cell10.className = "listing-item"; cell10.align	= "center";cell10.noWrap = "true";
	cell11.className = "listing-item"; cell11.align	= "center";cell11.noWrap = "true";
	cell12.className = "listing-item"; cell12.align	= "center";cell12.noWrap = "true";
	cell13.className = "listing-item"; cell13.align	= "center";cell13.noWrap = "true";
	cell14.className = "listing-item"; cell14.align	= "center";cell14.noWrap = "true";
	
	if (mode==0) {
		cell15.className = "listing-item"; cell15.align	= "center";cell15.noWrap = "true";
		cell16.className = "listing-item"; cell16.align	= "center";cell16.noWrap = "true";
		//cell17.className = "listing-item"; cell17.align	= "center";cell17.noWrap = "true";
	} else {
		cell15.className = "listing-item"; cell15.align	= "center";cell15.noWrap = "true";
	}
	
	var selWtType = "";
	// Copy Item
	if (cpyItem) {
		var fFieldId = getMaxRowId();
		if (fFieldId>=0) {
			selFishId 	 = document.getElementById('selFish_'+fFieldId).value;
			selProcessCodeId = document.getElementById('selProcessCode_'+fFieldId).value;			
			/*selEuCodeId	 = document.getElementById('selEuCode_'+fFieldId).value;*/
			selBrandId	 = document.getElementById('selBrand_'+fFieldId).value;
			selGradeId	 = document.getElementById('selGrade_'+fFieldId).value;
			selFreezingStageId = document.getElementById('selFreezingStage_'+fFieldId).value;
			selFrozenCodeId	= document.getElementById('selFrozenCode_'+fFieldId).value;
			selMCPackingId	= document.getElementById('selMCPacking_'+fFieldId).value;
			numMC	= document.getElementById('numMC_'+fFieldId).value;		
			pricePerKg	= document.getElementById('pricePerKg_'+fFieldId).value;
			valueInUSD	= document.getElementById('valueInUSD_'+fFieldId).value;
			valueInINR	= document.getElementById('valueInINR_'+fFieldId).value;
			selWtType	= document.getElementById('wtType_'+fFieldId).value;
		}
	}

	var selectFish	= "<select name='selFish_"+fieldId+"' id='selFish_"+fieldId+"' onchange=\"xajax_getProcessCodes(document.getElementById('selFish_"+fieldId+"').value, "+fieldId+", '');xajax_getFrznCodes("+fieldId+", document.getElementById('selFish_"+fieldId+"').value, '', '');\"><option value=''>--Select--</option>";
	<?php
		if (sizeof($fishMasterRecords)>0) {	
			foreach($fishMasterRecords as $fr) {
				$fishId		= $fr[0];
				$fishName	= stripSlash($fr[1]);
	?>	
		if (selFishId== "<?=$fishId?>")  var sel = "Selected";
		else var sel = "";

	selectFish += "<option value=\"<?=$fishId?>\" "+sel+"><?=$fishName?></option>";	
	<?php
			}
		}
	?>
	selectFish += "</select>";


	var selectPC	= "<select name='selProcessCode_"+fieldId+"' id='selProcessCode_"+fieldId+"' onchange=\"xajax_getGradeRecs(document.getElementById('selProcessCode_"+fieldId+"').value, "+fieldId+", ''); xajax_getFrznCodes("+fieldId+", document.getElementById('selFish_"+fieldId+"').value, document.getElementById('selProcessCode_"+fieldId+"').value, '');\"><option value=''>--Select--</option>";
	selectPC += "</select>";

	/*var selectEuCode	= "<select name='selEuCode_"+fieldId+"' id='selEuCode_"+fieldId+"'><option value=''>--Select--</option>";
	<?php
		if (sizeof($euCodeRecords)>0) {	
			foreach($euCodeRecords as $eucr) {
				$euCodeId	= $eucr[0];
				$euCode		= stripSlash($eucr[1]);
	?>	
		if (selEuCodeId== "<?=$euCodeId?>")  var sel = "Selected";
		else var sel = "";

	selectEuCode += "<option value=\"<?=$euCodeId?>\" "+sel+"><?=$euCode?></option>";	
	<?php
			}
		}
	?>
	selectEuCode += "</select>";*/

	var selectBrand	= "<select name='selBrand_"+fieldId+"' id='selBrand_"+fieldId+"' onchange=\"xajax_assignBrand(document.getElementById('selBrand_"+fieldId+"').value, "+fieldId+");\"><option value=''>--Select--</option>";
	selectBrand += "</select>";

	var selectGrade	= "<select name='selGrade_"+fieldId+"' id='selGrade_"+fieldId+"'><option value=''>--Select--</option>";
	selectPC += "</select>";
	
	var selectFrStage	= "<select name='selFreezingStage_"+fieldId+"' id='selFreezingStage_"+fieldId+"'><option value=''>--Select--</option>";
	<?php
		if (sizeof($freezingStageRecords)>0) {	
			foreach($freezingStageRecords as $fsr) {
				$freezingStageId	= $fsr[0];
				$freezingStageCode	= stripSlash($fsr[1]);
	?>	
		if (selFreezingStageId== "<?=$freezingStageId?>")  var sel = "Selected";
		else var sel = "";

	selectFrStage += "<option value=\"<?=$freezingStageId?>\" "+sel+"><?=$freezingStageCode?></option>";	
	<?php
			}
		}
	?>
	selectFrStage += "</select>";

	var selectFrznCode	= "<select name='selFrozenCode_"+fieldId+"' id='selFrozenCode_"+fieldId+"' onchange=\"xajax_getFilledWt(document.getElementById('selFrozenCode_"+fieldId+"').value, "+fieldId+"); xajax_getMCPkgs("+fieldId+", document.getElementById('selFrozenCode_"+fieldId+"').value, ''); \"><option value=''>--Select--</option>";
	<?php
		if (sizeof($frozenPackingRecords)>0) {	
			 foreach($frozenPackingRecords as $fpr) {
				$frozenPackingId = $fpr[0];
				$frozenPackingCode = stripSlash($fpr[1]);
	?>	
		if (selFrozenCodeId== "<?=$frozenPackingId?>")  var sel = "Selected";
		else var sel = "";

	selectFrznCode += "<option value=\"<?=$frozenPackingId?>\" "+sel+"><?=$frozenPackingCode?></option>";	
	<?php
			}
		}
	?>
	selectFrznCode += "</select>";

	var selectMCPkg	= "<select name='selMCPacking_"+fieldId+"' id='selMCPacking_"+fieldId+"' onchange=\"xajax_getNumMC(document.getElementById('selMCPacking_"+fieldId+"').value, "+fieldId+");\"><option value=''>--Select--</option>";
	<?php
		if (sizeof($mcpackingRecords)>0) {	
			 foreach($mcpackingRecords as $mcp) {
				$mcpackingId	= $mcp[0];
				$mcpackingCode	= stripSlash($mcp[1]);
	?>	
		if (selMCPackingId== "<?=$mcpackingId?>")  var sel = "Selected";
		else var sel = "";

	selectMCPkg += "<option value=\"<?=$mcpackingId?>\" "+sel+"><?=$mcpackingCode?></option>";	
	<?php
			}
		}
	?>
	selectMCPkg += "</select>";


	var selWtTypeArr	= "<select name='wtType_"+fieldId+"' id='wtType_"+fieldId+"' onchange=\"totRowVal("+fieldId+");\">";
	<?php
		if (sizeof($wtTypeArr)>0) {	
			 foreach($wtTypeArr as $wtTypeKey=>$wtTypeVal) {				
	?>	
		if (selWtType== "<?=$wtTypeKey?>")  var sel = "Selected";
		else var sel = "";

	selWtTypeArr += "<option value=\"<?=$wtTypeKey?>\" "+sel+"><?=$wtTypeVal?></option>";	
	<?php
			}
		}
	?>
	selWtTypeArr += "</select>";


	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setPOItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='poEntryId_"+fieldId+"' id='poEntryId_"+fieldId+"' value='"+poEntryId+"'>";	

	var hidOtherFields = "<input type='hidden' name='hidBrandId_"+fieldId+"' id='hidBrandId_"+fieldId+"' value='"+selBrandId+"'><input type='hidden' name='frznPkgFilledWt_"+fieldId+"' id='frznPkgFilledWt_"+fieldId+"' value='' readonly><input type='hidden' name='numPacks_"+fieldId+"' id='numPacks_"+fieldId+"' value=''><input type='hidden' name='frznPkgDeclaredWt_"+fieldId+"' id='frznPkgDeclaredWt_"+fieldId+"' value='' readonly><input type='hidden' name='frznPkgUnit_"+fieldId+"' id='frznPkgUnit_"+fieldId+"' value='' readonly>";
	
	
	cell1.innerHTML	= "";//(fieldId+1);
	cell2.innerHTML	= selectFish;
	cell3.innerHTML	= selectPC;
	/*cell4.innerHTML	= selectEuCode;
	cell5.innerHTML = selectBrand;
	cell6.innerHTML = selectGrade;
	cell7.innerHTML = selectFrStage;
	cell8.innerHTML = selectFrznCode;
	cell9.innerHTML	= selectMCPkg;
	cell10.innerHTML	= selWtTypeArr;
	cell11.innerHTML = "<input type='text' name='numMC_"+fieldId+"' id='numMC_"+fieldId+"' size='6' value='"+numMC+"' onkeyup='totRowVal("+fieldId+");' style='text-align:right;'>";
	cell12.innerHTML = "<input type='text' name='prdTotalWt_"+fieldId+"' id='prdTotalWt_"+fieldId+"' size='8' value='' readonly style='text-align:right; border:none;'>";
	cell13.innerHTML = "<input type='text' name='pricePerKg_"+fieldId+"' id='pricePerKg_"+fieldId+"' size='6' value='"+pricePerKg+"' onkeyup='totRowVal("+fieldId+");' style='text-align:right;'>";
	cell14.innerHTML = "<input type='text' name='valueInUSD_"+fieldId+"' id='valueInUSD_"+fieldId+"' size='8' value='"+valueInUSD+"' readonly style='text-align:right; border:none;'>";
	cell15.innerHTML = "<input type='text' name='valueInINR_"+fieldId+"' id='valueInINR_"+fieldId+"' size='8' value='"+valueInINR+"' readonly style='text-align:right; border:none;'>";
	if (mode==0) {
		cell16.innerHTML = "<input type='text' name='availableNumMC_"+fieldId+"' id='availableNumMC_"+fieldId+"' size='6' value='' readonly style='text-align:right; border:none;'>";
		cell17.innerHTML = imageButton+hiddenFields+hidOtherFields;	
	} else {
		cell16.innerHTML = imageButton+hiddenFields+hidOtherFields;	
	}*/	
	

	//cell4.innerHTML	= selectEuCode;
	cell4.innerHTML = selectBrand;
	cell5.innerHTML = selectGrade;
	cell6.innerHTML = selectFrStage;
	cell7.innerHTML = selectFrznCode;
	cell8.innerHTML	= selectMCPkg;
	cell9.innerHTML	= selWtTypeArr;
	cell10.innerHTML = "<input type='text' name='numMC_"+fieldId+"' id='numMC_"+fieldId+"' size='6' value='"+numMC+"' onkeyup='totRowVal("+fieldId+");' style='text-align:right;'>";
	cell11.innerHTML = "<input type='text' name='prdTotalWt_"+fieldId+"' id='prdTotalWt_"+fieldId+"' size='8' value='' readonly style='text-align:right; border:none;'>";
	cell12.innerHTML = "<input type='text' name='pricePerKg_"+fieldId+"' id='pricePerKg_"+fieldId+"' size='6' value='"+pricePerKg+"' onkeyup='totRowVal("+fieldId+");' style='text-align:right;'>";
	cell13.innerHTML = "<input type='text' name='valueInUSD_"+fieldId+"' id='valueInUSD_"+fieldId+"' size='8' value='"+valueInUSD+"' readonly style='text-align:right; border:none;'>";
	cell14.innerHTML = "<input type='text' name='valueInINR_"+fieldId+"' id='valueInINR_"+fieldId+"' size='8' value='"+valueInINR+"' readonly style='text-align:right; border:none;'>";
	if (mode==0) {
		cell15.innerHTML = "<input type='text' name='availableNumMC_"+fieldId+"' id='availableNumMC_"+fieldId+"' size='6' value='' readonly style='text-align:right; border:none;'>";
		cell16.innerHTML = imageButton+hiddenFields+hidOtherFields;	
	} else {
		cell15.innerHTML = imageButton+hiddenFields+hidOtherFields;	
	}
	if (cpyItem && fFieldId>=0) {
		xajax_getProcessCodes(selFishId, fieldId, selProcessCodeId);
		xajax_getGradeRecs(selProcessCodeId, fieldId, selGradeId);
		xajax_getFilledWt(selFrozenCodeId, fieldId);
		xajax_getNumMC(selMCPackingId, fieldId);
	}

	if (fieldId>0) {	
		//xajax_getFrznPkgCode(fieldId, selFrozenCodeId);
		xajax_getFrznCodes(fieldId, selFishId, selProcessCodeId, selFrozenCodeId);
		//xajax_getMCPkg(fieldId, selMCPackingId); 
		xajax_getMCPkgs(fieldId, selFrozenCodeId, selMCPackingId);
	}

	
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;	
	assignSrNo();
	if (cpyItem) calcTotalOrderVal();
}
// Add New Product Ends here
	

	function setPOItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
			assignSrNo();
			totRowVal(id)
		}
		return false;
	}

	function assignSrNo()
	{
		var itemCount	=	document.getElementById("hidTableRowCount").value;

		var j = 0;
		for (i=0; i<itemCount; i++) {
			var sStatus = document.getElementById("status_"+i).value;	
			if (sStatus!='N') {
				j++;	
				document.getElementById("srNo_"+i).innerHTML = j;
			}
		}		
	}


	// ------------------------------------------------------
	// Duplication check starts here
	// ------------------------------------------------------	
	function validateItemRepeat()
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
		var pArr	= new Array();	
		var pa		= 0;
	
		for (i=0; i<rc; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var fish	= document.getElementById("selFish_"+i).value;
				var processCode	= document.getElementById("selProcessCode_"+i).value;
				/*var eUCode	= document.getElementById("selEuCode_"+i).value;*/
				var brand	= document.getElementById("selBrand_"+i).value;
				var selGrade	= document.getElementById("selGrade_"+i).value;
				var freezingStage = document.getElementById("selFreezingStage_"+i).value;
				var frozenCode	= document.getElementById("selFrozenCode_"+i).value;
				var mCPacking	= document.getElementById("selMCPacking_"+i).value;
	
				//var addVal = fish+""+processCode+""+eUCode+""+brand+""+selGrade+""+freezingStage+""+frozenCode+""+mCPacking;
				var addVal = fish+""+processCode+""+brand+""+selGrade+""+freezingStage+""+frozenCode+""+mCPacking;
				
				if (pArr.indexOf(addVal)!=-1) {
					alert(" Raw item cannot be duplicate.");
					document.getElementById("selFish_"+i).focus();
					return false;	
				}
							
				pArr[pa++]	= addVal;
			}
		}	
		return true;
	}	
	// ------------------------------------------------------
	// Duplication check Ends here
	// ------------------------------------------------------

	function getMaxRowId()
	{
		var cnt = 0;
		var rc = document.getElementById("hidTableRowCount").value;
		for (i=0; i<rc; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				//cnt++;
				cnt = i;
			}

		}
		return cnt;
	}

	function calcAllRowVal()
	{
		var rc = document.getElementById("hidTableRowCount").value;
		for (i=0; i<rc; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
					totRowVal(i);
			}
		}
	}

	/* Calculate total value */
	function totRowVal(rowId)
	{
		var totalValueUSD  = 0;
		var oneUSDToINR	   = 0;
		var totalValueINR  = 0;
		var prdWt = 0;
	
		var wtType			= document.getElementById("wtType_"+rowId).value;
		var filledWt		= document.getElementById("frznPkgFilledWt_"+rowId).value;
		var declaredWt		= document.getElementById("frznPkgDeclaredWt_"+rowId).value;
		var numPacks		= document.getElementById("numPacks_"+rowId).value;// Packing val
		var numMC			= document.getElementById("numMC_"+rowId).value;
		var pricePerKg		= document.getElementById("pricePerKg_"+rowId).value;		
		oneUSDToINR 		= document.getElementById("oneUSDToINR").value;
		var frznCodeUnit	= document.getElementById("frznPkgUnit_"+rowId).value;
		
		var selPrdWt = (wtType=='NW')?declaredWt:filledWt;
		selPrdWt = (selUnitId==2 && frznCodeUnit=='Kg')?number_format((KG2LBS*selPrdWt),3,'.',''):selPrdWt;
		totalValueUSD	=	selPrdWt*numPacks*numMC*pricePerKg;
		totalValueINR	=	totalValueUSD * oneUSDToINR;
		
		prdWt = selPrdWt*numPacks*numMC;
		if(!isNaN(prdWt)){
			document.getElementById("prdTotalWt_"+rowId).value = number_format(prdWt,3,'.','');
		}

		if(!isNaN(totalValueUSD)){
			document.getElementById("valueInUSD_"+rowId).value = number_format(totalValueUSD,2,'.','');
			document.getElementById("valueInINR_"+rowId).value = number_format(totalValueINR,2,'.','');
		}

		calcTotalOrderVal();
	}

	// Calc total Order value
	function calcTotalOrderVal()
	{
		var rowCount 	= document.getElementById("hidTableRowCount").value;
		var totNumMC 	= 0;
		var totUSD	= 0;
		var totINR	= 0;
		var totNetWt = 0;

		for (i=0; i<rowCount; i++) {
			var rowStatus = document.getElementById("status_"+i).value;				
			if (rowStatus!='N') {
				var numMC 	=  parseInt(document.getElementById("numMC_"+i).value);	
				totNumMC 	+= numMC;
				var valueUSD 	= parseFloat(document.getElementById("valueInUSD_"+i).value);
				totUSD += valueUSD;
				var valueINR	= parseFloat(document.getElementById("valueInINR_"+i).value);
				totINR += valueINR;	
				
				var prdWt	= parseFloat(document.getElementById("prdTotalWt_"+i).value);
				totNetWt += prdWt;	
			}
		}
		
		if (!isNaN(totNumMC)) document.getElementById("totalNumMC").value = totNumMC;
		if (!isNaN(totUSD)) document.getElementById("totalValUSD").value = number_format(totUSD,2,'.','');
		if (!isNaN(totINR)) document.getElementById("totalValINR").value = number_format(totINR,2,'.','');
		if (!isNaN(totNetWt)) document.getElementById("totalNetWt").value = number_format(totNetWt,3,'.','');
	}

	// Split PO
	function splitPO(tableId, invoiceId, invNo, invDate, invType, invProfomaNo, invSampleNo, entryDate)
	{		
		if (!splitMCAvailable()) {
			alert("MC not available.");
			return false;
		}
		var tbl		= document.getElementById(tableId);		
		var lastRow	= tbl.rows.length;	
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "spoRow_"+fldId;
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		
		cell1.id = "srNo_"+fldId;	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setSplitPOStatus('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
		var hiddenFields = "<input name='spoStatus_"+fldId+"' type='hidden' id='spoStatus_"+fldId+"' value=''><input name='spoIsFromDB_"+fldId+"' type='hidden' id='spoIsFromDB_"+fldId+"' value='"+ds+"'><input name='hidInvoiceId_"+fldId+"' type='hidden' id='hidInvoiceId_"+fldId+"' value='"+invoiceId+"'><input name='spoMCStatus_"+fldId+"' type='hidden' id='spoMCStatus_"+fldId+"' value='Y'>";	

		if (invType=='T') var selTaxInvType = 'selected=true';
		else 	var selTaxInvType = '';
		if (invType=='S') var selSampleInvType = 'selected=true';
		else var selSampleInvType = '';			

		var htmlBlock = "";
		
		htmlBlock = "<fieldset><table>";
		// Invoice Details starts here
		htmlBlock += "<tr><td>";
		htmlBlock += "<table border=0>";
		htmlBlock += "<tr><td>";
			htmlBlock += "<table>";
			htmlBlock += "<tr><td class='fieldName' nowrap>*Invoice Type</td><td>";
			htmlBlock += "<select name='invoiceType_"+fldId+"' id='invoiceType_"+fldId+"' onchange=\"showSplitInvRow('', '"+invProfomaNo+"', '"+invSampleNo+"', '"+fldId+"');\">";
			//htmlBlock += "<option value='T' "+selTaxInvType+">Taxable</option>";
			//htmlBlock += "<option value='S' "+selSampleInvType+">Sample</option>";
			htmlBlock += "<option value=''>--Select--</option>";
			<?php
				foreach ($invoiceTypeMasterRecs as $itm) {
					$invoiceTypeId 		= $itm[0];
					$invoiceTypeName	= $itm[1];
			?>
			htmlBlock += "<option value='<?=$invoiceTypeId?>' "+selSampleInvType+"><?=$invoiceTypeName?></option>";
			<?php
				}
			?>
			htmlBlock += "</select>";
			htmlBlock += "</td></tr>";
			htmlBlock += "<tr id='sampleInvNoRow_"+fldId+"'><td class='fieldName' nowrap>*Sample Invoice No.</td>";
			htmlBlock += "<td nowrap='true'>";
			htmlBlock += "<input type='text' name='sampleInvoiceNo_"+fldId+"' id='sampleInvoiceNo_"+fldId+"' size='6' onkeyup=\"xajax_chkSplitSampleNoExist(document.getElementById('sampleInvoiceNo_"+fldId+"').value, '<?=$mode?>', '<?=$editSalesOrderId?>', document.getElementById('invoiceDate_"+fldId+"').value, '"+fldId+"');\" value='"+invSampleNo+"'>";	
			htmlBlock += "</td></tr>";
			htmlBlock += "<tr id='proformaInvNoRow_"+fldId+"'><td class='fieldName' nowrap='true'>*Proforma No.</td>";
			htmlBlock += "<td nowrap='true'>";
			htmlBlock += "<input type='text' name='proformaInvoiceNo_"+fldId+"' id='proformaInvoiceNo_"+fldId+"' size='6' value='"+invProfomaNo+"' onkeyup=\"xajax_chkSplitPfrmaNoExist(document.getElementById('proformaInvoiceNo_"+fldId+"').value, '<?=$mode?>', '<?=$editSalesOrderId?>', document.getElementById('entryDate_"+fldId+"').value, '"+fldId+"');\" autocomplete='off'>";		
			/*htmlBlock += "<input type='text' name='proformaInvoiceNo_"+fldId+"' id='proformaInvoiceNo_"+fldId+"' size='6' value='"+invProfomaNo+"' onkeyup=\"xajax_chkSplitPfrmaNoExist(document.getElementById('proformaInvoiceNo_"+fldId+"').value, '<?=$mode?>', '<?=$editSalesOrderId?>', document.getElementById('entryDate_"+fldId+"').value, '"+fldId+"');\" autocomplete='off'>";*/
			htmlBlock += "<span class='fieldName'>Eucode</span><select id='selEuCode_"+fldId+"' name='selEuCode_"+fldId+"'>";
		htmlBlock +="<option value=''>--Select--</option>";
		<?php
		if (sizeof($euCodeRecords)>0) {	
			foreach($euCodeRecords as $eucr) {
				$euCodeId	= $eucr[0];
				$euCode		= stripSlash($eucr[1]);
				//$selEUR = ($selEuCodeId==$euCodeId)?"selected":"";
				$selEUR = ($sEucodeId==$euCodeId)?"selected":"";
		?>	
			htmlBlock +="<option value='<?=$euCodeId?>' <?=$selEUR?>><?=$euCode?></option>";	
		<?php
				}
			}
		?>
	htmlBlock +="</select>";
			htmlBlock += "</td></tr>";
			
			if (entryDate=="") var etryDate = '<?=date("d/m/Y")?>';
			else var etryDate = entryDate;

			htmlBlock += "<input type='hidden' name='entryDate_"+fldId+"' id='entryDate_"+fldId+"' size='8' value='"+etryDate+"'>"	
			htmlBlock += "</table>";
		htmlBlock += "</td></tr>";
		htmlBlock += "<tr><TD class='listing-item' style='line-height:normal; font-size:10px; color:red;' id='divNumExistTxt_"+fldId+"' nowrap='true' align='center' colspan='2'></TD></tr>";
		htmlBlock += "</table>";
		htmlBlock += "</td></tr>";
		// Invoice Details Ends here
		htmlBlock += "<tr><td>";
		//htmlBlock += "<table  cellspacing='1' bgcolor='#999999' cellpadding='3' id='tblPOItem'><tr bgcolor='#f2f2f2' align='center'><td class='listing-head'>Process Code</td><td class='listing-head'>Freezing Stage</td><td class='listing-head'>No of MC</td><td class='listing-head'>Price per Kg in USD</td><td class='listing-head'>Value in USD</td><td class='listing-head'>Value in INR</td><td class='listing-head'>Available MC</td></tr>";
		htmlBlock += "<table  cellspacing='1' bgcolor='#999999' cellpadding='3' id='tblPOItem'><tr bgcolor='#f2f2f2' align='center'>";
		htmlBlock += "<td class='listing-head'>Description of Goods</td>";
		//htmlBlock += "<td class='listing-head'>Freezing Stage</td>";
		htmlBlock += "<td class='listing-head'>MC to be<br/> shipped</td>";
		htmlBlock += "<td class='listing-head'>MC in this<br/> Invoice</td>";
		htmlBlock += "<td class='listing-head'>Price per <span class='replaceUnitTxt'>Kg</span><br/> in <span class='replaceCY'>USD</span></td>";
		htmlBlock += "<td class='listing-head'>Value in<br/> <span class='replaceCY'>USD</span></td>";
		htmlBlock += "<td class='listing-head'>Value in<br/> INR</td>";
		htmlBlock += "<td class='listing-head'>Balance MC</td>";
		htmlBlock += "</tr>";
		
		rowCount = document.getElementById("hidTableRowCount").value;
		for (var i=0; i<rowCount; i++) {

			var poEntryId = document.getElementById("poEntryId_"+i).value;
						
			var invoiceEntryId 	= "";
			var mcInPO		= "";
			var mcInInvoice		= "";
			/*
			if (invoiceId) {
				var v =	xajax_getInvoiceEntryRecs(invoiceId, poEntryId);
				invoiceEntryId 	= v[0];
				mcInPO		= v[1];	
				mcInInvoice	= v[2];
			}
			*/
			var pricePerKg			= document.getElementById("pricePerKg_"+i).value;
			var sFrznPkgFilledWt	= document.getElementById("frznPkgFilledWt_"+i).value;
			var sNumPacks			= document.getElementById("numPacks_"+i).value;
			var sFrznPkgDeclaredWt	= document.getElementById("frznPkgDeclaredWt_"+i).value;
			var sWtType				= document.getElementById("wtType_"+i).value;
			var sFrznPkgUnit		= document.getElementById("frznPkgUnit_"+i).value;

			
			htmlBlock += "<tr class='whiteRow'>";
			htmlBlock += "<td class='listing-item'>";
			htmlBlock += document.getElementById("selProcessCode_"+i).options[document.getElementById("selProcessCode_"+i).selectedIndex].text;
			/*htmlBlock += "&nbsp;"+document.getElementById("selEuCode_"+i).options[document.getElementById("selEuCode_"+i).selectedIndex].text;*/
			htmlBlock += "&nbsp;"+document.getElementById("selBrand_"+i).options[document.getElementById("selBrand_"+i).selectedIndex].text;
			htmlBlock += "&nbsp;"+document.getElementById("selGrade_"+i).options[document.getElementById("selGrade_"+i).selectedIndex].text;		
			htmlBlock += "&nbsp;"+document.getElementById("selFreezingStage_"+i).options[document.getElementById("selFreezingStage_"+i).selectedIndex].text;
			htmlBlock += "&nbsp;"+document.getElementById("selFrozenCode_"+i).options[document.getElementById("selFrozenCode_"+i).selectedIndex].text;
			htmlBlock += "&nbsp;"+document.getElementById("selMCPacking_"+i).options[document.getElementById("selMCPacking_"+i).selectedIndex].text;

			htmlBlock += "<input type='hidden' name='selPCId_"+i+"_"+fldId+"' id='selPCId_"+i+"_"+fldId+"' size='6' value='"+ document.getElementById("selProcessCode_"+i).value+"' />";
			htmlBlock += "<input type='hidden' name='hidPOEntryId_"+i+"_"+fldId+"' id='hidPOEntryId_"+i+"_"+fldId+"' size='6' value='"+poEntryId+"' />";
			htmlBlock += "<input type='hidden' name='hidInvoiceEntryId_"+i+"_"+fldId+"' id='hidInvoiceEntryId_"+i+"_"+fldId+"' size='6' value='"+invoiceEntryId+"' />";
			htmlBlock += "<input type='hidden' id='frznPkgFilledWt_"+i+"_"+fldId+"' name='frznPkgFilledWt_"+i+"_"+fldId+"' value='"+sFrznPkgFilledWt+"' readonly /><input type='hidden' id='numPacks_"+i+"_"+fldId+"' name='numPacks_"+i+"_"+fldId+"' value='"+sNumPacks+"' readonly /><input type='hidden' name='frznPkgDeclaredWt_"+i+"_"+fldId+"' id='frznPkgDeclaredWt_"+i+"_"+fldId+"' value='"+sFrznPkgDeclaredWt+"' readonly><input type='hidden' name='wtType_"+i+"_"+fldId+"' id='wtType_"+i+"_"+fldId+"' value='"+sWtType+"' readonly><input type='hidden' name='frznPkgUnit_"+i+"_"+fldId+"' id='frznPkgUnit_"+i+"_"+fldId+"' value='"+sFrznPkgUnit+"' readonly>";
			htmlBlock += "</td>";
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += "<input type='text' name='MCInPO_"+i+"_"+fldId+"' id='MCInPO_"+i+"_"+fldId+"' size='6' value='"+mcInPO+"' style='text-align:right; border:none;' autocomplete='off' readonly />";			
			htmlBlock += "</td>";
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += "<input type='text' name='MCInInv_"+i+"_"+fldId+"' id='MCInInv_"+i+"_"+fldId+"' size='6' value='"+mcInInvoice+"' style='text-align:right;' onkeyup = 'chkMCQty();' autocomplete='off' />";
			htmlBlock += "</td>";			
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += "<input type='text' name='pricePerKg_"+i+"_"+fldId+"' id='pricePerKg_"+i+"_"+fldId+"' size='6' value='"+pricePerKg+"' style='text-align:right;' onkeyup = 'chkMCQty();' autocomplete='off' />";
			htmlBlock += "</td>";
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += "<input type='text' name='valueInUSD_"+i+"_"+fldId+"' id='valueInUSD_"+i+"_"+fldId+"' size='8' value='' style='text-align:right; border:none;' autocomplete='off' readonly />";
			htmlBlock += "</td>";
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += "<input type='text' name='valueInINR_"+i+"_"+fldId+"' id='valueInINR_"+i+"_"+fldId+"' size='8' value='"+mcInInvoice+"' style='text-align:right; border:none;' autocomplete='off' readonly />";
			htmlBlock += "</td>";			
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += "<input type='text' name='balanceMc_"+i+"_"+fldId+"' id='balanceMc_"+i+"_"+fldId+"' size='6' value='' style='text-align:right; border:none;' readonly='true'></td>";	
			htmlBlock += "</tr>";		
		}

		htmlBlock += "</table>";
		htmlBlock += "</td></tr>";
		// Invoice Display starts here
		/*
		htmlBlock += "<tr><td>";
		 htmlBlock += "<table border=0>";
		 htmlBlock += "<tr><td>";
			htmlBlock += "<table>";
			htmlBlock += "<tr><td class='fieldName' nowrap>*Invoice No.</td>";
			htmlBlock += "<td class='listing-item' nowrap>";
			htmlBlock += "<input name='invoiceNo_"+fldId+"' id='invoiceNo_"+fldId+"' type='text' size='6' onKeyUp=\"xajax_chkSONumberExist(document.getElementById('invoiceNo').value, '<?=$mode?>', '<?=$editSalesOrderId?>', document.getElementById('invoiceDate').value, document.getElementById('invoiceType').value);\" value='"+invNo+"' autocomplete='off' <?=$fieldReadOnly?>/>";
			htmlBlock += "<input type='hidden' name='validInvoiceNo_"+fldId+"' id='validInvoiceNo_"+fldId+"' value=''><br/><span id='divSOIdExistTxt' style='line-height:normal; font-size:10px; color:red;'></span>"
			htmlBlock += "</td>";
			htmlBlock += "<td class='fieldName' nowrap='true'>*Invoice Date</td>";
			htmlBlock += "<td nowrap='true'>";
				
				if (invDate=="") var invoiceDate = '<?=date("d/m/Y")?>';
				else var invoiceDate = invDate;
				
			//onchange='xajax_chkValidInvoiceDate(document.getElementById('invoiceDate').value);'
			htmlBlock += "<input type='text' name='invoiceDate_"+fldId+"' id='invoiceDate_"+fldId+"' value='"+invoiceDate+"' size='8' autocomplete='off' <?=$fieldReadOnly?>/>";
			htmlBlock += "<input type='hidden' name='validInvoiceDate_"+fldId+"' id='validInvoiceDate_"+fldId+"' value=''>";
			htmlBlock += "</td></tr>";
			htmlBlock += "<tr><td height='10'></td></tr>";
			htmlBlock += "</table>";
		 htmlBlock += "</td></tr>";		
		 htmlBlock += "</table>";
		htmlBlock += "</td></tr>";
		*/
		// Invoice Display Ends here
		htmlBlock += "</table>";
		htmlBlock += "</fieldset>";

		cell1.innerHTML	= htmlBlock;	
		cell2.innerHTML = imageButton+hiddenFields;	

		// Show Split inv Row
		//mode, proformaInvNo, sampleInvNo, rowId invProfomaNo, invSampleNo
		showSplitInvRow('', invProfomaNo, invSampleNo, fldId);

		fldId		= parseInt(fldId)+1;	
		document.getElementById("splitTbleRowCount").value = fldId;
		// Display Calender
		displayCalender();
		chkMCQty();
		replaceCYCode();
	}
	// Split PO Ends here

	/*
		for (var i=0; i<rowCount; i++) {
			htmlBlock += "<tr class='whiteRow'>";
			htmlBlock += "<td class='listing-item'>";
			htmlBlock += document.getElementById("selProcessCode_"+i).options[document.getElementById("selProcessCode_"+i).selectedIndex].text;
			htmlBlock += "</td>";
			htmlBlock += "<td class='listing-item'>";
			htmlBlock += document.getElementById("selFreezingStage_"+i).options[document.getElementById("selFreezingStage_"+i).selectedIndex].text;
			htmlBlock += "</td>";
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += document.getElementById("numMC_"+i).value;
			htmlBlock += "</td>";
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += document.getElementById("pricePerKg_"+i).value;
			htmlBlock += "</td>";
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += document.getElementById("valueInUSD_"+i).value;
			htmlBlock += "</td>";
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += document.getElementById("valueInINR_"+i).value;
			htmlBlock += "</td>";
			htmlBlock += "<td class='listing-item' align='right'>";
			htmlBlock += document.getElementById("availableNumMC_"+i).value;
			htmlBlock += "</td>";	
			htmlBlock += "</tr>";		
		}
	*/

	function setSplitPOStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("spoStatus_"+id).value = document.getElementById("spoIsFromDB_"+id).value;
			document.getElementById("spoRow_"+id).style.display = 'none';
			chkMCQty();
		}
		return false;
	}

	function disableInvoiceRow(fldId)
	{
		document.getElementById("proformaInvNoRow_"+fldId).style.display="";
		document.getElementById("sampleInvNoRow_"+fldId).style.display="";	
	}

	// Split invoice Row
	function showSplitInvRow(mode, proformaInvNo, sampleInvNo, rowId)
	{
		//alert(mode+","+proformaInvNo+","+sampleInvNo+"Row="+rowId);
		var invoiceType = document.getElementById("invoiceType_"+rowId).value;
		if (invoiceType=='S') {
			document.getElementById("proformaInvNoRow_"+rowId).style.display="none";
			document.getElementById("sampleInvNoRow_"+rowId).style.display="";
			document.getElementById("proformaInvoiceNo_"+rowId).value = "";
			document.getElementById("selEuCode_"+rowId).style.display = "";
			
			if (mode==1) xajax_getSampleInvoiceNo();
			else document.getElementById("sampleInvoiceNo_"+rowId).value = sampleInvNo;
		} else {
			document.getElementById("sampleInvNoRow_"+rowId).style.display="none";
			document.getElementById("proformaInvNoRow_"+rowId).style.display="";
			document.getElementById("sampleInvoiceNo_"+rowId).value = "";
			document.getElementById("selEuCode_"+rowId).style.display = "";

			//alert(proformaInvNo);
			if (!proformaInvNo) {
				xajax_getSplitPFInvoiceNo(rowId);
			}
			else document.getElementById("proformaInvoiceNo_"+rowId).value = proformaInvNo;

			/*
			if (mode==1) xajax_getProformaInvoiceNo();
			else document.getElementById("proformaInvoiceNo_"+rowId).value = proformaInvNo;
			*/
		}
	}

	function genSingleInv(singleInvEnabled)
	{	
		if (singleInvEnabled) {
			document.getElementById("poInSingleInv").style.display="";
			document.getElementById("splitPORow").style.display="none";			
		} else {
			document.getElementById("poInSingleInv").style.display="none";
			document.getElementById("splitPORow").style.display="";
		}
	}

	// Chk MC Qty available
	function chkMCQty()
	{
		//var varients	= Math.ceil(document.getElementById("varients").value/100);
		var varients	= number_format((parseFloat(document.getElementById("varients").value)/100),2,'.','');
		var oneUSDToINR 	=	document.getElementById("oneUSDToINR").value;
		
		var pcArr = new Array();
		rowCount = document.getElementById("hidTableRowCount").value;
		
		for (var i=0; i<rowCount; i++) {
			var rowStatus = document.getElementById("status_"+i).value;	
       			if (rowStatus!='N') {
					var processCodeId 	= document.getElementById("selProcessCode_"+i).value;	
					var numMC		= document.getElementById("numMC_"+i).value;

					if (numMC!=0 && numMC!="" && !isNaN(numMC)) {							
						if (typeof(pcArr[processCodeId])!="undefined" && numMC!=0) {
							numMC = parseInt(numMC) + parseInt(pcArr[processCodeId]);
						}
						if (numMC!=0) pcArr[processCodeId] = parseInt(numMC);
					}
				} // Status Check Ends here
		} // Main loop Ends here
	
		
		// Split table Row
		var splitPCArr = new Array();
		var splitTbleRowCount = document.getElementById("splitTbleRowCount").value;		
		var sArr = new Array();
		var p = 0;
		for (var i=0; i<splitTbleRowCount; i++) {
			var rowStatus = document.getElementById("spoStatus_"+i).value;	
       			if (rowStatus!='N') {
				var balanceMC = 0;
				for (var j=0; j<rowCount; j++) {	
					var selPCId	= document.getElementById("selPCId_"+j+"_"+i).value;
					var splitNumMC 	= document.getElementById("MCInInv_"+j+"_"+i).value;

					if (splitNumMC!=0 && splitNumMC!="" && !isNaN(splitNumMC)) {
						if (typeof(splitPCArr[selPCId])!="undefined" && splitNumMC!=0) {
							splitNumMC = parseInt(splitNumMC) + parseInt(splitPCArr[selPCId]);
						}
						if (splitNumMC!=0) splitPCArr[selPCId] = parseInt(splitNumMC);
					}
				} // Split Row PC Ends Here

				sArr[p] = i;
				p++;
			} // Status Check Ends here
		} // Split Row Ends Here
		
		//for (var i=0; i<splitTbleRowCount; i++) {
		for (var l=0; l<sArr.length; l++) {
			var i =  sArr[l];
			var rowStatus = document.getElementById("spoStatus_"+i).value;	
       			if (rowStatus!='N') {				
				var balanceMC = 0;
				var mcExist = true;
				for (var j=0; j<rowCount; j++) {						
					var selPCId	= document.getElementById("selPCId_"+j+"_"+i).value;
					//alert(selPCId+'------------'+pcArr[selPCId]);
					var splitNumMC 	= document.getElementById("MCInInv_"+j+"_"+i).value;
		
					var mcVarientsCalc = parseFloat(pcArr[selPCId])+(parseFloat(pcArr[selPCId])*parseFloat(varients));
					var adjustMC	   = number_format(mcVarientsCalc,0,'','');	
					var calcAdjMC	   = parseInt(pcArr[selPCId])-adjustMC;
					balanceMC = parseInt(pcArr[selPCId])-parseInt(splitPCArr[selPCId]);
					//alert(balanceMC);
					alert(parseInt(pcArr[selPCId]));
					
					if (l==0) 
						document.getElementById("MCInPO_"+j+"_"+i).value = parseInt(pcArr[selPCId]);
					else {
						var calcMCInPO = parseInt(document.getElementById("MCInPO_"+j+"_"+sArr[l-1]).value)-parseInt(document.getElementById("MCInInv_"+j+"_"+sArr[l-1]).value);

						if (!isNaN(calcMCInPO)) document.getElementById("MCInPO_"+j+"_"+i).value = calcMCInPO;
					
					}
					
					if (!isNaN(balanceMC) && sArr.length==(l+1)) {
						document.getElementById("balanceMc_"+j+"_"+i).value = balanceMC;

						if (balanceMC<calcAdjMC) mcExist = false;
						else if (balanceMC<=calcAdjMC && varients==0) mcExist = false;						
					}
					else document.getElementById("balanceMc_"+j+"_"+i).value = "";

					// Calc Row Wise value
					var pricePerKg = document.getElementById("pricePerKg_"+j+"_"+i).value;

					if (pricePerKg!="" && pricePerKg!=0 && !isNaN(pricePerKg)) {
						var filledWt		= document.getElementById("frznPkgFilledWt_"+j+"_"+i).value;
						var numPacks		= document.getElementById("numPacks_"+j+"_"+i).value;// Packing val
						var declaredWt		= document.getElementById("frznPkgDeclaredWt_"+j+"_"+i).value;
						var wtType			= document.getElementById("wtType_"+j+"_"+i).value;
						var frznCodeUnit	= document.getElementById("frznPkgUnit_"+j+"_"+i).value;

						var selPrdWt = (wtType=='NW')?declaredWt:filledWt;
						selPrdWt = (selUnitId==2 && frznCodeUnit=='Kg')?number_format((KG2LBS*selPrdWt),3,'.',''):selPrdWt;
						var valueInUSD	=	selPrdWt*numPacks*splitNumMC*pricePerKg;
						var valueInINR	=	valueInUSD * oneUSDToINR;						
						if(!isNaN(valueInUSD)){
							document.getElementById("valueInUSD_"+j+"_"+i).value = number_format(valueInUSD,2,'.','');
							document.getElementById("valueInINR_"+j+"_"+i).value = number_format(valueInINR,2,'.','');
						} 
					} else {
						document.getElementById("valueInUSD_"+j+"_"+i).value = "";
						document.getElementById("valueInINR_"+j+"_"+i).value = "";
					}
					
					
				} // Split Row PC Ends Here

				if (!mcExist) document.getElementById("spoMCStatus_"+i).value = 'N';
				else document.getElementById("spoMCStatus_"+i).value = 'Y';
				//spoMCStatus_
			} // Status Check Ends here
		} // Split Row Ends Here
		

		/*
		for (var lpr in pcArr) {
			alert(pcArr[lpr]);
		}
		*/
	}

	/* Calender Display */
	function displayCalender()
	{
		var rowCount = 	document.getElementById("splitTbleRowCount").value;
		for (i=0;i<rowCount;i++) {
			Calendar.setup 
			(	
				{
					inputField  : "invoiceDate_"+i,         // ID of the input field
					eventName   : "click",	    // name of event
					button : "invoiceDate_"+i, 
					ifFormat    : "%d/%m/%Y",    // the date format
					singleClick : true,
					step : 1
				}
			);
		}
	}
	/*Calender Display Ends here*/

	
	/* Quick Entry list generation Row Wise */
	function validateQELGen(poMainId, userId, rowId, lastDate)
	{
		if (!confirm("Do you wish to generate a Quick Entry List?")) {
			return false;
		}
		
		// Insert QEL
		xajax_genQuickEntryList(poMainId, userId, lastDate);
			
		//xajax_genGatePass(poMainId, userId);
		document.getElementById("qelCol_"+rowId).innerHTML = "PENDING";		
		//document.getElementById("qelCol_"+rowId).onMouseover = new function () { ShowTip('PENDING');};
		return true;
	}

	function incPFNO(rowId, pfNo)
	{
		document.getElementById("proformaInvoiceNo_"+rowId).value = pfNo+rowId;
	}

	function splitMCAvailable()
	{
		var mcAvailable = true;
		var splitTbleRowCount = document.getElementById("splitTbleRowCount").value;
		for (var i=0; i<splitTbleRowCount; i++) {
			var rowStatus = document.getElementById("spoStatus_"+i).value;	
       			if (rowStatus!='N') {
				var mcStatus = document.getElementById("spoMCStatus_"+i).value;
				if (mcStatus=='N') mcAvailable = false;
			}
		}
		
		if (mcAvailable) return true;
		else return false;
	}

	//lightbox-form.js
	//formtitle, fadin, filterDiv, boxDiv, boxTtleDiv (spo-boxtitle)
	function loadFrznGrade()
	{
		//alert(screen.height+"=="+screen.width);

		var itemCount	=	document.getElementById("hidTableRowCount").value;
		
		var j = 0;
		for (i=(itemCount-1); i>=0; i--) {
			var sStatus = document.getElementById("status_"+i).value;
			if (sStatus!='N') {
				var fish	= document.getElementById("selFish_"+i).value;
				var processCode	= document.getElementById("selProcessCode_"+i).value;
				if (fish!="" &&  processCode!="") break;
			}
		}	

		ifId=gmobj("addNewIFrame");
		ifId.src="ProcessCode.php?popupWindow=1&selFilter="+fish+"&selProcessCodeId="+processCode;		
		openModalBox("", 1, "spo-filter", "spo-box", "");
	}

	// Frozen Packing
	function loadFrznPkgCode()
	{
		ifId=gmobj("addNewIFrame");
		ifId.src="FrozenPacking.php?popupWindow=1";
		openModalBox("", 1, "spo-filter", "spo-box", "");
	}

	// MC Pkg
	function loadMCPkg()
	{
		ifId=gmobj("addNewIFrame");
		ifId.src="MCPacking.php?popupWindow=1";
		openModalBox("", 1, "spo-filter", "spo-box", "");
	}

	function closeLightBox()
	{
		closeModalBox("spo-box", "spo-filter");
	}

	// FG - FROZEN GRADE, FPC - FROZEN PACKING CODE, MCP - MC PACKING
	function reloadDropDownList(reloadType)
	{
		var itemCount		= document.getElementById("hidTableRowCount").value;
		//alert("h");
		for (i=0; i<itemCount; i++) {
			var rowStatus = document.getElementById("status_"+i).value;	
			if (rowStatus!='N') {
				var fish	= document.getElementById("selFish_"+i).value;
				var processCode	= document.getElementById("selProcessCode_"+i).value;
				var selGrade	= document.getElementById("selGrade_"+i).value;
				var frozenCode	= document.getElementById("selFrozenCode_"+i).value;
				var mCPacking	= document.getElementById("selMCPacking_"+i).value;				
				if (reloadType=='FG') xajax_getGradeRecs(processCode, i, selGrade);
				if (reloadType=='FPC') {
					xajax_getFrznCodes(i, fish, processCode, frozenCode);
					//xajax_getFrznPkgCode(i, frozenCode);
					//xajax_getFilledWt(frozenCode, i);
				}
				if (reloadType=='MCP') xajax_getMCPkg(i, mCPacking); 
			} // Status check 
		} // Row count ends here
	}

	function updateNumMC(mcPkgId, rowId)
	{
		xajax_getNumMC(mcPkgId, rowId);
	}

	function setCurrency()
	{
		var selectDate	= document.getElementById("selectDate").value;
		var selCurrency		= document.getElementById("selCurrency");
		replaceCYCode();
		
		xajax_getCurrency(selCurrency.value, selectDate);
	}

	function replaceCYCode()
	{
		var selCurrency		= document.getElementById("selCurrency");
		var currencyCode	= selCurrency.options[selCurrency.selectedIndex].text;
		$(".replaceCY").html(currencyCode);
	}

	function changeUnitTxt()
	{
		var selUnit		= document.getElementById("selUnit");
		selUnitId = parseInt(selUnit.value);
		KG2LBS = document.getElementById("hidKG2LBS").value;
		calcAllRowVal();
		chkMCQty();
		var unitTxt		= selUnit.options[selUnit.selectedIndex].text;
		$(".replaceUnitTxt").html(unitTxt);
	}