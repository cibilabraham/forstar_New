	function validateDailyCatchReport(form)
	{
		var supplier		=	form.selSupplier.value;
		var weighChallan	=	form.selWeighment.value;
		if (supplier=="") {
			alert("Please select a Supplier");
			form.selSupplier.focus();
			return false;
		}
			
		if (weighChallan=="") {
			alert("Please select a Weighment Challan No");
			form.selWeighment.focus();
			return false;
		}
		return true;
	}

	function validateSearch(form)
	{
		var weighChallan	= form.weighNumber.value;
		var selBillingCompany	= form.selBillingCompany.value;
		if (weighChallan=="") {
			alert("Please enter a Weighment Challan No");
			form.weighNumber.focus();
			return false;
		}
		if (selBillingCompany=="") {
			alert("Please select a billing company.");
			form.selBillingCompany.focus();
			return false;
		}
		return true;
	}

	//For confirm the challn no
	function validateDailyCatchReportConfirm(form)
	{	
		var zeroEntryExist = document.getElementById("zeroEntryExist").value;
		if (zeroEntryExist!="") {
			alert("Please make sure the Weighment Challan have no zero entry exists.");		
			return false;
		}
		if (!confirmSave()) {
			return false;
		} 
		return true;
	}


	function enableConfirmButton()
	{	
		document.getElementById("cmdConfirm").disabled = false;		
	}
	
	function disableConfirmButton()
	{	
		document.getElementById("cmdConfirm").disabled = true;
	}


//function getSupplier(formObj,value)
function functionLoad(formObj)
	{
		//alert("hai"+value);
		showFnLoading(); 
		formObj.form.submit();
	}
	function getBillCompany(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();
	}