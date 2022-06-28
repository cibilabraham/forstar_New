function validateSchemeMaster(form)
{
	var schemeName = form.schemeName.value;
	var buyNum	= form.buyNum.value;
	var buyBasedOn	= document.getElementById("buyBasedOn").value;
	var getNum	= form.getNum.value;
	var getProductType = document.getElementById("getProductType").value;
	
	if (schemeName=="") {
		alert("Please enter a Scheme Name.");
		form.schemeName.focus();
		return false;
	}

	if (buyNum=="") {
		alert("Please enter buy number.");
		form.buyNum.focus();
		return false;
	}
	
	if (!isDigit(buyNum)) {
		alert("Please enter a number.");
		form.buyNum.focus();
		return false;
	}

	if (buyBasedOn=="") {
		alert("Please select buy based on type.");
		document.getElementById("buyBasedOn").focus();
		return false;
	}	
	
	if (buyBasedOn=='P') { // If Product
		if (document.getElementById("selProduct").value=="") {
			alert("Please select atleast one product.");
			document.getElementById("selProduct").focus();
			return false;
		}
	} else if (buyBasedOn=='M') { // If MRP
		if (document.getElementById("selMrp").value=="") {
			alert("Please select a MRP.");
			document.getElementById("selMrp").focus();
			return false;
		}
	}
	
	if (getNum=="") {
		alert("Please enter Get number.");
		form.getNum.focus();
		return false;
	}
	
	if (!isDigit(getNum)) {
		alert("Please enter a number.");
		form.getNum.focus();
		return false;
	}
	if (getProductType=="") {
		alert("Please select Get Product Type");
		document.getElementById("getProductType").focus();
		return false;
	}	

	if (getProductType=='MP') {	// Mrp Product
		var getMrpProductType = document.getElementById("getMrpProductType").value;
		if (getMrpProductType=="") {
			alert("Please select MRP Product Type");
			document.getElementById("getMrpProductType").focus();
			return false;
		}

		if (getMrpProductType=='G') {
			//var getMrpGroupType = document.getElementById("getMrpGroupType").value;
			if (document.getElementById("getMrpGroupType").value=="") {
				alert("Please select a MRP Group.");
				document.getElementById("getMrpGroupType").focus();
				return false;
			}	
			if (document.getElementById("selGroupMrp").value=="") {
				alert("Please select a Group MRP.");
				document.getElementById("selGroupMrp").focus();
				return false;
			}	
			
		} else if (getMrpProductType=='I') {
			if (document.getElementById("selIndProduct").value=="") {
				alert("Please select atleast one product.");
				document.getElementById("selIndProduct").focus();
				return false;
			}

		}
		
	} else if (getProductType=='SP') { // Sample Product
		if (document.getElementById("selSampleProduct").value=="") {
			alert("Please select a Sample Product.");
			document.getElementById("selSampleProduct").focus();
			return false;
		}
	}
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}
// Hide Schme Master Head
function hideSchemeMasterHead()
{
	document.getElementById("buyBasedOnProduct").style.display = "none";
	document.getElementById("buyBasedOnMrp").style.display = "none";
	document.getElementById("getMrpProductBaseOnR").style.display = "none";
	document.getElementById("getMrpGroupBasedOnR").style.display = "none";
	document.getElementById("getMrpIndProductBasedOnR").style.display = "none";
	document.getElementById("sampleProductR").style.display = "none";				
	displayBuyRow();
	displayGetFnRow();
	disGetMrpProdBased();
}
/* P- Product, M-MRP*/
function displayBuyRow()
{
	var buyBasedOn = document.getElementById("buyBasedOn").value;
	if (buyBasedOn=='P') {
		document.getElementById("buyBasedOnProduct").style.display = "";
		document.getElementById("buyBasedOnMrp").style.display = "none";
		document.getElementById("selMrp").value = "";
	} else if (buyBasedOn=='M') {
		document.getElementById("buyBasedOnMrp").style.display = "";
		document.getElementById("buyBasedOnProduct").style.display = "none";
		document.getElementById("selProduct").value = "";
	} else {
		document.getElementById("buyBasedOnProduct").style.display = "none";
		document.getElementById("buyBasedOnMrp").style.display = "none";
	}
	/*
	document.getElementById("selProduct").value = "";
	document.getElementById("selMrp").value = "";
	*/
		
}
/* MP -> MRP PRODUCT, SP-> SAMPLE PRODUCT*/
function displayGetFnRow()
{
	var getProductType = document.getElementById("getProductType").value;
	if (getProductType=='MP') {
		document.getElementById("getMrpProductBaseOnR").style.display = "";
		document.getElementById("sampleProductR").style.display = "none";
		document.getElementById("getMrpGroupBasedOnR").style.display = "none";
		document.getElementById("getMrpIndProductBasedOnR").style.display = "none";
		document.getElementById("selSampleProduct").value = "";	
	} else if (getProductType=='SP') {
		document.getElementById("getMrpProductBaseOnR").style.display = "none";
		document.getElementById("sampleProductR").style.display = "";
		document.getElementById("getMrpGroupBasedOnR").style.display = "none";
		document.getElementById("getMrpIndProductBasedOnR").style.display = "none";
		document.getElementById("getMrpProductType").value = "";
	} else {
		document.getElementById("getMrpProductBaseOnR").style.display = "none";
		document.getElementById("sampleProductR").style.display = "none";
		document.getElementById("getMrpGroupBasedOnR").style.display = "none";
		document.getElementById("getMrpIndProductBasedOnR").style.display = "none";
	}
	/*
	document.getElementById("getMrpProductType").value = "";
	document.getElementById("selSampleProduct").value = "";		
	*/
}

function disGetMrpProdBased()
{
	var getMrpProductType = document.getElementById("getMrpProductType").value;
	if (getMrpProductType=='G') {
		document.getElementById("getMrpGroupBasedOnR").style.display = "";
		document.getElementById("getMrpIndProductBasedOnR").style.display = "none";
		document.getElementById("selIndProduct").value = "";
	} else if (getMrpProductType=='I') {
		document.getElementById("getMrpGroupBasedOnR").style.display = "none";
		document.getElementById("getMrpIndProductBasedOnR").style.display = "";
		document.getElementById("getMrpGroupType").value = "";
		document.getElementById("selGroupMrp").value = "";
	} else {
		document.getElementById("getMrpGroupBasedOnR").style.display = "none";
		document.getElementById("getMrpIndProductBasedOnR").style.display = "none";
	}
	/*
	document.getElementById("selIndProduct").value = "";
	document.getElementById("getMrpGroupType").value = "";
	document.getElementById("selGroupMrp").value = "";
	*/		
}
