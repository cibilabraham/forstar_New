function validateSemiFinishProduct(form)
{
	var productCode		= form.productCode.value;
	var productName		= form.productName.value;
	var ingCategory 	= form.ingCategory.value;
	var subCategory		= form.subCategory.value;
	var openingQty		= form.openingQty.value;
	var kgPerBatch		= form.kgPerBatch.value;

	var processHrs		= form.processHrs.value;
	var processMints	= form.processMints.value;
	var gasHrs		= document.getElementById("gasHrs").value;
	var gasMints		= document.getElementById("gasMints").value;
	var steamHrs		= document.getElementById("steamHrs").value;
	var steamMints		= document.getElementById("steamMints").value;
	var fixedStaffHrs	= document.getElementById("fixedStaffHrs").value;
	var fixedStaffMints	= document.getElementById("fixedStaffMints").value;
	
	var noOfFixedStaff	= document.getElementById("noOfFixedStaff").value;
	var varStaffTotalCost 	= document.getElementById("varStaffTotalCost").value;
	
	//alert(convertHrs(processHrs, processMints))
		
	if (productCode=="") {
		alert("Please enter a Semi-Finished Product Code.");
		form.productCode.focus();
		return false;
	}	
	if (productName=="") {
		alert("Please enter a Semi-Finished Product Name.");
		form.productName.focus();
		return false;
	}	
	if (ingCategory=="") {
		alert("Please select a Category.");
		form.ingCategory.focus();
		return false;
	}
	if (subCategory=="") {
		alert("Please select a Sub-Category.");
		form.subCategory.focus();
		return false;
	}
	
	if (openingQty=="") {
		alert("Please enter a Opening Qty.");
		form.openingQty.focus();
		return false;
	}
	if (!checkNumber(openingQty)) {
		form.openingQty.value = "";
		form.openingQty.focus();
		return false;
	}

	if (kgPerBatch=="") {
		alert("Please enter Kg Per Batch.");
		form.kgPerBatch.focus();
		return false;
	}

	var itemCount	= document.getElementById("hidTableRowCount").value;
	var stockSelected = false;

	for (i=0; i<itemCount; i++) {
	    var status = document.getElementById("status_"+i).value;		    
	    if (status!='N') {
		var selStock	= document.getElementById("selIngredient_"+i);
		var quantity	= document.getElementById("quantity_"+i);		
		var ingType	= document.getElementById("ingType_"+i);
		
		if (selStock!="") {
			if (selStock.value == "") {
				alert("Please select an Ingredient.");
				selStock.focus();
				return false;
			}
			if (quantity.value == "") {
				alert("Please enter Raw quantity.");
				quantity.focus();
				return false;
			}			
			stockSelected = true;
		}
            }
	}

	if (stockSelected==false) {
		alert("Please add one or more Ingredient");
		return false;
	}

	if (!validateProductOfIngredientRepeat()) {
		return false;
	}
		

	if (processHrs=="" && processMints=="") {
		alert("Please enter Process Time");
		form.processHrs.focus();
		return false;
	}

	if (processHrs!="" &&  (!checkHrs(processHrs) || !checkNumber(processHrs))) {
		form.processHrs.focus();
		return false;
	}

	if (processMints!="" && (!checkMints(processMints) || !checkNumber(processMints))) {
		form.processMints.focus();
		return false;
	}

	if (gasHrs!="" &&  (!checkHrs(gasHrs) || !checkNumber(gasHrs))) {
		document.getElementById("gasHrs").focus();
		return false;
	}

	if (gasMints!="" && (!checkMints(gasMints) || !checkNumber(gasMints))) {
		document.getElementById("gasMints").focus();
		return false;
	}

	if (convertHrs(gasHrs, gasMints)>convertHrs(processHrs, processMints)) {
		alert("Please check Gas usage Time");
		document.getElementById("gasHrs").focus();
		return false;
	}

	/*
	if (gasMints>processMints) {
		alert("Please check Gas usage Minutes");
		document.getElementById("gasMints").focus();
		return false;
	}
	*/
	if (steamHrs!="" &&  (!checkHrs(steamHrs) || !checkNumber(steamHrs))) {
		document.getElementById("steamHrs").focus();
		return false;
	}

	if (steamMints!="" && (!checkMints(steamMints) || !checkNumber(steamMints))) {
		document.getElementById("steamMints").focus();
		return false;
	}

	if (convertHrs(steamHrs, steamMints)>convertHrs(processHrs, processMints)) {
		alert("Please check Steam usage Time");
		document.getElementById("steamHrs").focus();
		return false;
	}	
	/*
	if (steamHrs>processHrs) {
		alert("Please check Steam usage Hrs");
		document.getElementById("steamHrs").focus();
		return false;
	}

	if (steamMints>processMints) {
		alert("Please check Steam usage Minutes");
		document.getElementById("steamMints").focus();
		return false;
	}
	*/

	if (fixedStaffHrs!="" &&  (!checkHrs(fixedStaffHrs) || !checkNumber(fixedStaffHrs))) {
		document.getElementById("fixedStaffHrs").focus();
		return false;
	}

	if (fixedStaffMints!="" && (!checkMints(fixedStaffMints) || !checkNumber(fixedStaffMints))) {
		document.getElementById("fixedStaffMints").focus();
		return false;
	}

	if (convertHrs(fixedStaffHrs, fixedStaffMints)>convertHrs(processHrs, processMints)) {
		alert("Please check Man Power usage Time");
		document.getElementById("fixedStaffHrs").focus();
		return false;
	}
	/*
	if (fixedStaffHrs>processHrs) {
		alert("Please check Man Power usage Hrs");
		document.getElementById("fixedStaffHrs").focus();
		return false;
	}

	if (fixedStaffMints>processMints) {
		alert("Please check Man Power usage Minutes");
		document.getElementById("fixedStaffMints").focus();
		return false;
	}
	*/
	if ((noOfFixedStaff=="" ||noOfFixedStaff==0) && (varStaffTotalCost=="" || varStaffTotalCost==0) ) {
		alert ("Please enter no. of staff usage") ;
		return false;
	}


	if (!confirmSave()) {
			return false;
	}
	return true;
}

	//Validate repeated
	function validateProductOfIngredientRepeat()
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
		var prevOrder = 0;
		var arr = new Array();
		var arri=0;
		
		for (j=0; j<rc; j++) {
			var status = document.getElementById("status_"+j).value;
			if (status!='N') {
				var rv = document.getElementById("selIngredient_"+j).value;
				if ( arr.indexOf(rv) != -1 )    {
					alert("Ingredient cannot be duplicate.");
					document.getElementById("selIngredient_"+j).focus();
					return false;
				}
			arr[arri++]=rv;
			}
		}
		return true;
	}

	
	function checkHrs(selectTimeHour)
	{
		if (selectTimeHour>12 || selectTimeHour<=0) { 
			alert("Please check Hour");		
			return false;
		}
		return true;
	}

	function checkMints(selectTimeMints)
	{
		if (selectTimeMints>59 || selectTimeMints<0){
			alert("Please check Minute");
			return false;
		}
		return true;
	}


	function convertHrs(hrs, mints)
	{
		var selHr 	= (hrs!="")?hrs:0;
		var selMints 	= (mints!="")?mints:0;		
		var gHrs 	= parseFloat(selHr)*60;
		var gMints 	= (parseFloat(selMints)*60)/100;		
		var totalHrs 	= number_format(((gHrs + gMints)/60),2,'.','');
		return totalHrs;
	}	

/*ADD MULTIPLE Item- ADD ROW START*/
function addNewIngItemRow(tableId)
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
	//var cell6	= row.insertCell(5);
	//var cell7	= row.insertCell(6);
	
	
	cell1.className	= "listing-item"; cell1.align	= "center";cell1.noWrap = "true";
	cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
        cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
        cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";
	cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";
	//cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";
      //  cell7.className	= "listing-item"; cell7.align	= "center";cell7.noWrap = "true";

	
	var selectIngredient	= "<select name='selIngredient_"+fieldId+"' id='selIngredient_"+fieldId+"' onchange=\"xajax_getIngRate(document.getElementById('selIngredient_"+fieldId+"').value,"+fieldId+",'');calcProductRatePerBatch();\"><option value=''>-- Select --</option>";
	<?php
	if (sizeof($ingredientRecords)>0) {
		$ingredientId = "";		
		foreach ($ingredientRecords as $ir) {
			$ingredientId	= $ir[0]; 
			$ingredientName = $ir[1];
	?>
	selectIngredient += "<option value='<?=$ingredientId;?>'><?=$ingredientName;?></option>";
	<?php
		}
	}
	?>
	selectIngredient += "</select>";
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIngItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='ingType_"+fieldId+"' type='hidden' id='ingType_"+fieldId+"'><input type='hidden' name='lastPrice_"+fieldId+"' id='lastPrice_"+fieldId+"' value=''>";

	cell1.innerHTML	= selectIngredient;
	cell2.innerHTML	= "<input name='quantity_"+fieldId+"' type='text' id='quantity_"+fieldId+"' value='' size='6' style='text-align:right' onkeyup='calcProductRatePerBatch();' autoComplete='off'>"+hiddenFields+"";
	//cell3.innerHTML	= "<input name='cleanedQty_"+fieldId+"' type='text' id='cleanedQty_"+fieldId+"' value='' size='6' style='text-align:right;' autoComplete='off'>";	
	//cell4.innerHTML	= "<input name='declYield_"+fieldId+"' type='text' id='declYield_"+fieldId+"' value='' size='6' style='text-align:right;border:none;' autoComplete='off'>&nbsp;%";
	cell3.innerHTML	= "<input type='text' name='percentagePerBatch_"+fieldId+"' id='percentagePerBatch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>%";
	cell4.innerHTML	= "<input type='text' name='ratePerBatch_"+fieldId+"' id='ratePerBatch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>";	
	cell5.innerHTML = imageButton;
	
	fieldId		= parseInt(fieldId)+1;
	document.getElementById("hidTableRowCount").value = fieldId;
	
}

function setIngItemStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none';
 		calcProductRatePerBatch();
	}
	return false;
}


/* Rate Per batch */
	function calcProductRatePerBatch()
	{
		var itemCount 	      = document.getElementById("hidTableRowCount").value;		
		var kgPerBatch 		= parseFloat(document.getElementById("kgPerBatch").value);		
		var totalRatePerBatch 	= 0;
		var totalKgPerBatch 	= 0;
		for (i=0; i<itemCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var selIngredient = document.getElementById("selIngredient_"+i).value;
				var quantity  = parseFloat(document.getElementById("quantity_"+i).value);
				var lastPrice = parseFloat(document.getElementById("lastPrice_"+i).value);	
				//Rate for each Ingredient
				getIngPrice =  quantity*lastPrice;
				if (!isNaN(getIngPrice)) {
					document.getElementById("ratePerBatch_"+i).value = number_format(Math.abs(getIngPrice),2,'.','');
				}
	
				// Percentage  Per Batch
				getPercentagePerbatch = parseFloat(quantity/document.getElementById("productKgRawPerBatch").value);	
				if (!isNaN(getPercentagePerbatch)) {
					document.getElementById("percentagePerBatch_"+i).value = number_format(Math.abs(getPercentagePerbatch*100),0,'','');
				}
				
				if (selIngredient!="") {			
					totalRatePerBatch 	+= parseFloat(getIngPrice);
					totalKgPerBatch 	+= parseFloat(quantity);
				} 
			}		
		} //Loop End
	
		/* Kg (Raw) per Batch */
		if (!isNaN(totalKgPerBatch)) {
			document.getElementById("productKgRawPerBatch").value = number_format(totalKgPerBatch,2,'.','');
		}
	
		/* Rs. Per Batch */
		if (!isNaN(totalRatePerBatch)) {
			document.getElementById("productRatePerBatch").value = number_format(totalRatePerBatch,2,'.','');
		}


		/* Rs. Per Kg per Batch */
		//var calcRatePerKgPerBatch = document.getElementById("productRatePerBatch").value/document.getElementById("productKgRawPerBatch").value;

		var calcRatePerKgPerBatch = totalRatePerBatch/totalKgPerBatch;
		if (!isNaN(calcRatePerKgPerBatch)) {
			document.getElementById("productRatePerKgPerBatch").value = number_format(calcRatePerKgPerBatch,2,'.','');
			/* Display in Total Cost*/
			document.getElementById("ingCostPerKg").value = number_format(calcRatePerKgPerBatch,2,'.','');
			
		}

		/* % Yield */		
		var calcProductYieldPercent = parseFloat(document.getElementById("kgPerBatch").value)/parseFloat(document.getElementById("productKgRawPerBatch").value);	
		if (!isNaN(calcProductYieldPercent)) {
			document.getElementById("productYieldPercent").value  = number_format((calcProductYieldPercent*100),0,'.','');
		}	

		/* Find the Poduction Cost*/
		totalProductionCost();	

		reCalcProductRatePerBatch();		
	}


	function reCalcProductRatePerBatch()
	{
		var itemCount 	      = document.getElementById("hidTableRowCount").value;		
		var kgPerBatch 		= parseFloat(document.getElementById("kgPerBatch").value);		
		var totalRatePerBatch 	= 0;
		var totalKgPerBatch 	= 0;
		for (i=0; i<itemCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var selIngredient = document.getElementById("selIngredient_"+i).value;
				var quantity  = parseFloat(document.getElementById("quantity_"+i).value);
				var lastPrice = parseFloat(document.getElementById("lastPrice_"+i).value);	
				//Rate for each Ingredient
				getIngPrice =  quantity*lastPrice;
				if (!isNaN(getIngPrice)) {
					document.getElementById("ratePerBatch_"+i).value = number_format(Math.abs(getIngPrice),2,'.','');
				}
	
				// Percentage  Per Batch
				getPercentagePerbatch = parseFloat(quantity/document.getElementById("productKgRawPerBatch").value);	
				if (!isNaN(getPercentagePerbatch)) {
					document.getElementById("percentagePerBatch_"+i).value = number_format(Math.abs(getPercentagePerbatch*100),0,'','');
				}
				
				if (selIngredient!="") {			
					totalRatePerBatch 	+= parseFloat(getIngPrice);
					totalKgPerBatch 	+= parseFloat(quantity);
				} 
			}		
		} //Loop End
	
		/* Kg (Raw) per Batch */
		if (!isNaN(totalKgPerBatch)) {
			document.getElementById("productKgRawPerBatch").value = number_format(totalKgPerBatch,2,'.','');
		}
	
		/* Rs. Per Batch */
		if (!isNaN(totalRatePerBatch)) {
			document.getElementById("productRatePerBatch").value = number_format(totalRatePerBatch,2,'.','');
		}

		/* Rs. Per Kg per Batch */
		//var calcRatePerKgPerBatch = document.getElementById("productRatePerBatch").value/document.getElementById("productKgRawPerBatch").value;

		var calcRatePerKgPerBatch = totalRatePerBatch/totalKgPerBatch;
		if (!isNaN(calcRatePerKgPerBatch)) {
			document.getElementById("productRatePerKgPerBatch").value = number_format(calcRatePerKgPerBatch,2,'.','');
			/* Display in Total Cost*/
			document.getElementById("ingCostPerKg").value = number_format(calcRatePerKgPerBatch,2,'.','');
			
		}

		/* % Yield */		
		var calcProductYieldPercent = parseFloat(document.getElementById("kgPerBatch").value)/parseFloat(document.getElementById("productKgRawPerBatch").value);	
		if (!isNaN(calcProductYieldPercent)) {
			document.getElementById("productYieldPercent").value  = number_format((calcProductYieldPercent*100),0,'.','');
		}	

		/* Find the Poduction Cost*/
		totalProductionCost();		
	}

	/*
	function calcProductionCostTime()
	{
		var processHrs 		= document.getElementById("processHrs").value;
		var processMints 	= document.getElementById("processMints").value;
		var gasHrs		= document.getElementById("gasHrs").value;
		var gasMints		= document.getElementById("gasMints").value;
		var steamHrs		= document.getElementById("steamHrs").value;
		var steamMints		= document.getElementById("steamMints").value;
		var fixedStaffHrs	= document.getElementById("fixedStaffHrs").value;
		var fixedStaffMints	= document.getElementById("fixedStaffMints").value;
		var varStaffHrs		= document.getElementById("varStaffHrs").value;	
		var varStaffMints	= document.getElementById("varStaffMints").value;
	}
	*/

	
	function calcTotalVariableStaffAmt(staffHrs, staffMints)
	{
		var varStaffRowCount = document.getElementById("varStaffRowCount").value;
		var totalVarCost = 0;
		var grandTotalVarStaffCost = 0;
		for (var i=1; i<=varStaffRowCount; i++) {
			var manPowerUnit = (document.getElementById("manPowerUnit_"+i).value!="")?document.getElementById("manPowerUnit_"+i).value:0;
			var unitCost	 = (document.getElementById("unitCost_"+i).value!="")?document.getElementById("unitCost_"+i).value:0;
			totalVarCost	= unitCost * manPowerUnit;
			grandTotalVarStaffCost += totalVarCost;
		}
		if (!isNaN(grandTotalVarStaffCost)) {
			document.getElementById("varStaffTotalCost").value = grandTotalVarStaffCost;		
			xajax_getVariableStaffCost(staffHrs, staffMints, grandTotalVarStaffCost);
		}		
	}

	function totalProductionCost()
	{
		var totalProductionCost = 0;
		var ingCostPerKg  = number_format(document.getElementById("ingCostPerKg").value,2,'.','');
		
		var electricityConsumptionCost  = number_format(document.getElementById("electricityConsumptionCost").value,2,'.','');
		var gasConsumptionCost 		= number_format(document.getElementById("gasConsumptionCost").value,2,'.','');
		var steamConsumptionCost 	= number_format(document.getElementById("steamConsumptionCost").value,2,'.','');
		var fixedStaffCostPerHr 	= number_format(document.getElementById("fixedStaffCostPerHr").value,2,'.','');
		var varStaffPerHrCost 		= number_format(document.getElementById("varStaffPerHrCost").value,2,'.','');

		totalProductionCost = parseFloat(electricityConsumptionCost) + parseFloat(gasConsumptionCost) + parseFloat(steamConsumptionCost) + parseFloat(fixedStaffCostPerHr) + parseFloat(varStaffPerHrCost);

		document.getElementById("productionCostPerKg").value = number_format(totalProductionCost,2,'.','');

		document.getElementById("totProdCostPerKg").value = number_format(( parseFloat(ingCostPerKg)+parseFloat(totalProductionCost)),2,'.','');
	
	}