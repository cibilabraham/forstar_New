function validateSummarySearch(form)
{
	var supplier		=	form.supplier.value;

	var day			=	form.dateSelection[0].checked;
	var dateRange		=	form.dateSelection[1].checked;

	var supplierDate 	=	form.dateSelectFrom[0].checked;
	var wtChallanDate	=	form.dateSelectFrom[1].checked;

	if (supplierDate) {
		var supplierMemo		=	form.supplierMemo.checked;
		var declWtSummary		= 	form.declWtSummary.checked;
		var proSummary			=	form.proSummary.checked;
		//var supSetlmentSummary		=	form.supSetlmentSummary.checked;
	}

	if (wtChallanDate) {
		
		var details			=	form.details.checked;
		var proCount			=	form.proCount.checked;
		var proSummary			=	form.proSummary.checked;
		var fishCatchSummary 		=	form.fishCatchSummary.checked;
		var wtChallanSummary		=	form.wtChallanSummary.checked;

		var RMMatrix			=	form.RMMatrix.checked;
		var localQtyReportChk		=	form.localQtyReportChk.checked;
		var localQtyChk			=	form.localQtyChk.checked;
		var dailySummary		=	form.dailySummary.checked;
		var RMRateMatrix 		=	form.RMRateMatrix.checked;
		var qtySummary			= 	form.qtySummary.checked;
		var challanSummary		=	form.challanSummary.checked;
	}
	// For a date range
	if (dateRange) {
		var supplyFrom			=	form.supplyFrom.value;
		var supplyTill			=	form.supplyTill.value;
	
		if (supplyFrom=="") {
			alert("Please select Supply From Date");
			form.supplyFrom.focus();
			return false;
		}
		
		if (supplyTill=="") {
			alert("Please select Supply Till Date");
			form.supplyTill.focus();
			return false;
		}
	}
	
	//For a Day
	if (day) {
		var selDate		=	form.selDate.value;
		if (selDate=="") {
			alert("Please select a day");
			form.selDate.focus();
			return false;
		}
	}

	//&& supSetlmentSummary==""
	if (supplierDate!="" && supplierMemo=="" && declWtSummary=="" && proSummary=="" ) {
		alert("Please select atleast one Search option ");
		return false;
	}

	if (wtChallanDate!="" && details=="" && proCount=="" && proSummary=="" && fishCatchSummary=="" && wtChallanSummary=="" && RMMatrix=="" && localQtyReportChk=="" && dailySummary=="" && RMRateMatrix=="" && !qtySummary && !challanSummary) {
		alert("Please select atleast one Search option ");
		return false;
	}

	if (supplierDate) {	
		if (supplierMemo!="") {
			if (supplier==0) {
				alert("Please select a Supplier");
				form.supplier.focus();
				return false;
			}
		}
			
		if (declWtSummary!="") {
			if (supplier==0) {
				alert("Please select a Supplier");
				form.supplier.focus();
				return false;
			}
		}
	}

	if (!wtChallanSummary && !proCount && !proSummary && wtChallanDate!="") {
		form.localQtyChk.checked = false;
	}
 return true;
}
//Advance search option
function validateAdvanceSearch(form)
{
	$checked = false;
	var supplyFrom		=	form.supplyFrom.value;
	var supplyTill		=	form.supplyTill.value;
	if(supplyFrom==""){
		
			alert("Please select Supply From Date");
			form.supplyFrom.focus();
			return false;
	}
	
	if(supplyTill==""){
		
			alert("Please select Supply Till Date");
			form.supplyTill.focus();
			return false;
	}
return true;
}

function removeChkLocal(form)
{
	var wtChallanDate	=	form.dateSelectFrom[1].checked;
	if (wtChallanDate) form.localQtyChk.checked = false;
}

function removeChkRate(form)
{
	form.rateNAmount.checked = false;
	if ((form.wtChallanSummary.checked || form.proCount.checked || form.proSummary.checked) && form.localQtyChk.checked) {
		form.localQtyChk.checked = true;
	} else form.localQtyChk.checked = false;
}
//Using to activate only one search option
function removeAllChk(form, field)
{
	var supplierDate 	=	form.dateSelectFrom[0].checked;
	var wtChallanDate	=	form.dateSelectFrom[1].checked;

	if (!document.getElementById(field).checked) chk = false;
	else chk = true;

	if (wtChallanDate) {

		form.details.checked=false;
		form.proCount.checked =false;
		form.proSummary.checked =false;
		form.fishCatchSummary.checked =false;
		form.wtChallanSummary.checked =false;
		form.RMMatrix.checked = false;
		form.localQtyReportChk.checked = false;
		form.dailySummary.checked= false;
		form.RMRateMatrix.checked= false;
		form.qtySummary.checked= false;
		form.challanSummary.checked= false;
	}

	if (supplierDate) {
		form.supplierMemo.checked = false;
		form.declWtSummary.checked=false;
		form.proSummary.checked =false;
		//form.supSetlmentSummary.checked=false;
	}
	document.getElementById(field).checked = chk;
}

//Check Number of Sub Supplier Before Printing
function checkNumSubSupplier(form)
{
	var supplierDate 	=	form.dateSelectFrom[0].checked;
	var wtChallanDate	=	form.dateSelectFrom[1].checked;
	var QS			= 	form.searchMode[0].checked;
	var AS			= 	form.searchMode[1].checked;

	if (supplierDate && QS!="") {
		var subSupplierChk	= form.subSupplierChk.checked;
		var hidNumOfSubSupplier = form.hidNumOfSubSupplier.value;
		if (subSupplierChk!="" && hidNumOfSubSupplier!="") {
			alert("More than one Sub-Supplier. So can't Print Sub-Supplier wise Memo");
			return false;
		}		
	}	
	return true;
}

function getselUnit(formObj)
{
	showFnLoading(); 
	formObj.form.submit();

}
function functionLoad(formObj)
	{
		//alert("hai"+value);
		showFnLoading(); 
		formObj.form.submit();
	}