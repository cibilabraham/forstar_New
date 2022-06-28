function continueMsg()
{
	var saveMsg = "Do you wish to Continue?";
	if (confirm(saveMsg)) {
		return true;
	}
	return false;
} 

// Validate RM Supply Cost
function validateAddSupplyCost(form)
{
	var individual		= form.selOption[0].checked;
	var group		= form.selOption[1].checked;
	//alert(individual);	
	if (individual=="" && group=="") {
		alert("Please select one display type option.");
		return false;
	}

	if (individual) {
		var selDate		=	form.selDate.value;
		var selWtChallan	=	form.selWtChallan.value;

		// Ice	
		var numIceBlocks	= form.numIceBlocks.value;
		var costPerBlock	= form.costPerBlockvalue;
		var chkFixedIceBlock	= form.chkIceBlock.checked;
		var fixedIceCost	= form.fixedIceCost.value;

		// Transportation
		var km			= form.km.value;
		var costPerKm		= form.costPerKm.value;
		var chkFixedTransportation = form.chkTransportation.checked;
		var fixedTransCost	= form.fixedTransCost.value;
		var hasCommi		= form.hasCommi.checked;
		var hasHandling		= form.hasHandling.checked;
// 		// Commission selection
// 		var commiDetailed	= form.selCommission[0].checked;
// 		var commiSummary 	= form.selCommission[1].checked;
// 		//Handling selection
// 		var handlingDetailed	= form.selHandling[0].checked;
// 		var handlingSummary	= form.selHandling[1].checked;
		
		// Commission
		/*if (commiSummary) {
			var commissionPerKg	= form.commissionPerKg.value;
			var chkFixedCommission	= form.chkCommission.checked;
			var fixedCommiRate	= form.fixedCommiRate.value;	
		}*/

		// Handling Cost
		/*if (handlingSummary) {
			var handlingRatePerKg	= form.handlingRatePerKg.value;
			var chkFixedHandling	= form.chkHandling.checked;
			var fixedHandlingAmt	= form.fixedHandlingAmt.value;	
		}*/


	if (checkNull(selDate)) {
		alert("Please select a date.");
		form.selDate.focus();
		return false;
	}

	if (checkNull(selWtChallan)) {
		alert("Please select a Wt Challan.");
		form.selWtChallan.focus();
		return false;
	}
	// Ice
	if (chkFixedIceBlock) {
		if (checkNull(fixedIceCost)) {
			alert("Please enter fixed cost of Ice block.");
			form.fixedIceCost.focus();
			return false;
		}
	}	
	
	// Transportation
	if (chkFixedTransportation) {
		if (checkNull(fixedTransCost)) {
			alert("Please enter fixed cost of Transportation.");
			form.fixedTransCost.focus();
			return false;
		}
	}	

	if (hasCommi!="") {

	// Commission selection
		var commiDetailed	= form.selCommission[0].checked;
		var commiSummary 	= form.selCommission[1].checked;

	if (commiDetailed=="" && commiSummary=="") {
		alert("Please select Commission Detailed/Summary option.");
		return false;
	}
	
	// Commission
	if (commiSummary) {
		var commissionPerKg	= form.commissionPerKg.value;
		var chkFixedCommission	= form.chkCommission.checked;
		var fixedCommiRate	= form.fixedCommiRate.value;	
		if (chkFixedCommission) {
			if (checkNull(fixedCommiRate)) {
				alert("Please enter fixed cost of Commission.");
				form.fixedCommiRate.focus();
				return false;
			}
		}
	}

	}

	if (hasHandling!="") {
		//Handling selection
		var handlingDetailed	= form.selHandling[0].checked;
		var handlingSummary	= form.selHandling[1].checked;
	if (handlingDetailed=="" && handlingSummary=="") {
		alert("Please select Handling Detailed/Summary option.");
		return false;
	}

	// handling
	if (handlingSummary) {
		var handlingRatePerKg	= form.handlingRatePerKg.value;
		var chkFixedHandling	= form.chkHandling.checked;
		var fixedHandlingAmt	= form.fixedHandlingAmt.value;	

		if (chkFixedHandling) {
			if (checkNull(fixedHandlingAmt)) {
				alert("Please enter fixed Amt of Handling Charges.");
				form.fixedHandlingAmt.focus();
				return false;
			}
		}
	}

	}
			
	}	

	//Group
	if (group) {
		var dateFrom = form.dateFrom.value;
		var dateTill = form.dateTill.value;
		var supplier = form.supplier.value;
		if (checkNull(dateFrom)) {
			alert("Please select a From date.");
			form.dateFrom.focus();
			return false;
		}
		if (checkNull(dateTill)) {
			alert("Please select a Till date.");
			form.dateTill.focus();
			return false;
		}
		if (checkNull(supplier)) {
			alert("Please select a supplier.");
			form.supplier.focus();
			return false;
		}
	}

	if (!confirmSave()) {
		return false;
	} else {
		return true;
	}
}

// Hide Or UnHide Section ------------------------------------
function supplyCostHide()
{
	if (document.getElementById("selOption0").checked)  {

		document.getElementById( "fixedIceBlock" ).style.display = "none";
		document.getElementById( "fixedTransBlock" ).style.display = "none";
		if (document.getElementById("selCommission1").checked) {
			document.getElementById( "fixedCommiBlock" ).style.display = "none";
		}

		if (document.getElementById("selHandling1").checked) {
			document.getElementById( "fixedHandlingBlock" ).style.display = "none";
		}

	if (document.getElementById("chkIceBlock").checked) {
		showFixedIceCost();
	}
	if (document.getElementById("chkTransportation").checked) {
		showFixedTransCost();	
	}
	if (document.getElementById("selCommission1").checked) {
		if (document.getElementById("chkCommission").checked) {
			showFixedCommissionCost();
		}
	}
	if (document.getElementById("selHandling1").checked) {
		if (document.getElementById("chkHandling").checked) {
			showFixedHandlingCost();
		}
	}

	// Show Commi
	showHasCommi();
	
	// Show Handling Block
	showHasHandling();
	}
}
// Ice Block
function showFixedIceCost() 
{
	if (document.getElementById("chkIceBlock").checked) {
		document.getElementById( "iceBlock" ).style.display 	= "none";
		document.getElementById( "fixedIceBlock" ).style.display = "block";
	} else {
		document.getElementById( "iceBlock" ).style.display 	= "block";
		document.getElementById( "fixedIceBlock" ).style.display = "none";
	}
}
// Transportation
function showFixedTransCost() 
{
	if (document.getElementById("chkTransportation").checked) {
		document.getElementById( "transportationBlock" ).style.display = "none";
		document.getElementById( "fixedTransBlock" ).style.display = "block";
	} else {
		document.getElementById( "transportationBlock" ).style.display 	= "block";
		document.getElementById( "fixedTransBlock" ).style.display = "none";
	}
}

// Fixed Commission
function showFixedCommissionCost() 
{
	if (document.getElementById("chkCommission").checked) {
		document.getElementById( "commissionBlock" ).style.display = "none";
		document.getElementById( "fixedCommiBlock" ).style.display = "block";
	} else {
		document.getElementById( "commissionBlock" ).style.display 	= "block";
		document.getElementById( "fixedCommiBlock" ).style.display = "none";
	}
}

// Fixed Handling Cost
function showFixedHandlingCost() 
{
	if (document.getElementById("chkHandling").checked) {
		document.getElementById( "handlingBlock" ).style.display = "none";
		document.getElementById( "fixedHandlingBlock" ).style.display = "block";
	} else {
		document.getElementById( "handlingBlock" ).style.display 	= "block";
		document.getElementById( "fixedHandlingBlock" ).style.display = "none";
	}
}

// Show commission Block
function showHasCommi()
{
	if (document.getElementById("hasCommi").checked) {
		document.getElementById( "divHasCommi" ).style.display = "block";	
		
	} else {
		document.getElementById( "divHasCommi" ).style.display = "none";
	}
}

// Show Handling Block
function showHasHandling()
{
	if (document.getElementById("hasHandling").checked) {
		document.getElementById( "divHasHandling" ).style.display = "block";	
		
	} else {
		document.getElementById( "divHasHandling" ).style.display = "none";
	}
}
// ----------------------------------------
// Calculate the Total Ice Block rate
function calcIceBlockTotalRate() 
{
	var numIceBlocks 	= 	document.getElementById("numIceBlocks").value;
	var costPerBlock	=	document.getElementById("costPerBlock").value;		
	document.getElementById("totalIceCost").value = formatNumber(Math.abs(numIceBlocks * costPerBlock ),2,'','.','','','','','');
}
// Calculate Transportation Cost
function calcTransportationTotalAmt() 
{
	var km 		= 	document.getElementById("km").value;
	var costPerKm	=	document.getElementById("costPerKm").value;		
	document.getElementById("totalTransAmt").value = formatNumber(Math.abs(km * costPerKm ),2,'','.','','','','','');
}

function calcCommissionTotalRate() 
{
	var totalQuanty		= 	document.getElementById("totalQuanty").value;
	var commissionPerKg	=	document.getElementById("commissionPerKg").value;		
	document.getElementById("totalCommiRate").value = formatNumber(Math.abs(totalQuanty * commissionPerKg ),2,'','.','','','','','');
}
// Calc handling Cost
function calcHandlingTotalAmt()
{
	var totalRMQuanty	= 	document.getElementById("totalRMQuanty").value;
	var handlingRatePerKg	=	document.getElementById("handlingRatePerKg").value;		
	document.getElementById("totalHandlingAmt").value = formatNumber(Math.abs(totalRMQuanty * handlingRatePerKg ),2,'','.','','','','','');
}

// Check empty
function checkNull(field) 
{
	if (field=="" || field==0) return true;
}

//Calc Commission Detailed
function calcComisionIdividalRate()
{
	var hidIvidualCommiRowCount = document.getElementById("hidIvidualCommiRowCount").value;
	
	var calcAmt = 0;
	var grandTotalAmt = 0;
	for (i=1;i<=hidIvidualCommiRowCount;i++) {

		if (document.getElementById("rate_"+i).value=="") {
			document.getElementById("rate_"+i).value = 0;
		}
		var totalQty =  parseFloat(document.getElementById("totalQty_"+i).value);
		var rate = parseFloat(document.getElementById("rate_"+i).value);
		
		calcAmt =  totalQty * rate;

		grandTotalAmt += calcAmt;

		if (!isNaN(calcAmt)) {
			document.getElementById("totalAmt_"+i).value = number_format(calcAmt,2,'.','');
		}				
	}
	if (!isNaN(grandTotalAmt)) {
		document.getElementById("grandTotalCommiAmt").value = number_format(grandTotalAmt,2,'.','');
	}		
}

//Calc handling Detailed Rate
function calcHadlngDetailedRate()
{
	var hidDetailedHadlgRowCount = document.getElementById("hidDetailedHadlgRowCount").value;	
	var calcAmt = 0;
	var grandTotalAmt = 0;
	for (i=1;i<=hidDetailedHadlgRowCount;i++) {
		if (document.getElementById("hRate_"+i).value=="") {
			document.getElementById("hRate_"+i).value = 0;
		}
		var totalQty =  parseFloat(document.getElementById("hTotalQty_"+i).value);
		var rate = parseFloat(document.getElementById("hRate_"+i).value);		
		calcAmt =  totalQty * rate;
		grandTotalAmt += calcAmt;
		if (!isNaN(calcAmt)) {
			document.getElementById("hTotalAmt_"+i).value = number_format(calcAmt,2,'.','');
		}				
	}
	if (!isNaN(grandTotalAmt)) {
		document.getElementById("grandTotalHadlngAmt").value = number_format(grandTotalAmt,2,'.','');
	}		
}