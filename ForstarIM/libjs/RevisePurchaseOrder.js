	// Validate Revise PO
	function validateRevisePurchaseOrder()
	{
		var poSelected = false;
		var rowCount = document.getElementById("hidReviseRowCount").value;

		var supplierFilter 	   = document.getElementById("supplierFilter").value;
		var supplierRateListFilter = document.getElementById("supplierRateListFilter").value;

		if (supplierFilter=="") {
			alert("Please select a Supplier");
			document.getElementById("supplierFilter").focus();
			return false;
		}
		if (supplierRateListFilter=="") {
			alert("Please select a rate list for revision");
			document.getElementById("supplierRateListFilter").focus();
			return false;
		}

		for (i=1; i<=rowCount; i++) {
			var poId = document.getElementById("poMainId_"+i).checked;
			if (poId!="") {
				poSelected = true;
			}
		}
	
		if (poSelected) {
			//var dMsg = "PO's will revised based on the selected rate list";
			//if (!confirm(dMsg)) return false;
			alert("Purchase order will revised based on the selected rate list");			
		}

		if (!poSelected) {
			alert(" Please select a purchase order");	
			return false;
		}
		if (!confirmSave()) {
			return false;
		}
		return true;
	}

function functionrevLoad(formObj)
	{
		//alert("hai");
		showFnLoading(); 
		formObj.form.submit();
	}