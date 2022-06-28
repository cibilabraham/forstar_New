function validateHealthCertificate(form)
{
	var selLanguage 	= form.selLanguage.value;
	var consignorName 	= document.getElementById("consignorName").value;
	var consigneeName	= document.getElementById("consigneeName").value;
	var isoCode		= document.getElementById("isoCode").value;
	var regionOfOrigin	= document.getElementById("regionOfOrigin").value;
	var originCode		= document.getElementById("originCode").value;
	var destinationCountry	= document.getElementById("destinationCountry").value;	
	var destinationIsoCode	= document.getElementById("destinationIsoCode").value;	
	var originCompanyName	= document.getElementById("originCompanyName").value;	
	var approvalNumber	= document.getElementById("approvalNumber").value;
	var departureDate	= document.getElementById("departureDate").value;	
	var identification	= document.getElementById("identification").value;	
	var entryBPEU		= document.getElementById("entryBPEU").value;	
	var commodityDesciption	= document.getElementById("commodityDesciption").value;	
	var commodityCode	= document.getElementById("commodityCode").value;
	var netWt		= document.getElementById("netWt").value;
	var grWt		= document.getElementById("grWt").value;
	var noOfPackage		= document.getElementById("noOfPackage").value;	
	var containerNo		= document.getElementById("containerNo").value;	
	var sealNo		= document.getElementById("sealNo").value;	
	var typeOfPackaging	= document.getElementById("typeOfPackaging").value;	
	var species		= document.getElementById("species").value;	
	var natureOfCommodity	= document.getElementById("natureOfCommodity").value;
	

	if (selLanguage=="") {
		alert("Please select a Language.");
		form.selLanguage.focus();
		return false;
	}

	if (consignorName=="") {
		alert("Please enter a consignor Name.");
		document.getElementById("consignorName").focus();
		return false;
	}

	if (consigneeName=="") {
		alert("Please enter a consignee Name.");
		document.getElementById("consigneeName").focus();
		return false;
	}

	if (isoCode=="") {
		alert("Please enter an iso Code.");
		document.getElementById("isoCode").focus();
		return false;
	}

	if (regionOfOrigin=="") {
		alert("Please enter a Region of Origin.");
		document.getElementById("regionOfOrigin").focus();
		return false;
	}

	if (originCode=="") {
		alert("Please enter Origin Code.");
		document.getElementById("originCode").focus();
		return false;
	}

	if (destinationCountry=="") {
		alert("Please enter a Destination Country.");
		document.getElementById("destinationCountry").focus();
		return false;
	}
		
	if (destinationIsoCode=="") {
		alert("Please enter a Destination Country ISO Code.");
		document.getElementById("destinationIsoCode").focus();
		return false;
	}

	if (originCompanyName=="") {
		alert("Please enter a name.");
		document.getElementById("originCompanyName").focus();
		return false;
	}
	
	if (approvalNumber=="") {
		alert("Please enter a Approval Number.");
		document.getElementById("approvalNumber").focus();
		return false;
	}

	if (departureDate=="") {
		alert("Please select a date of departure.");
		document.getElementById("departureDate").focus();
		return false;
	}
	
	if (!document.getElementById("transportType1").checked && !document.getElementById("transportType2").checked && !document.getElementById("transportType3").checked && !document.getElementById("transportType4").checked && !document.getElementById("transportType5").checked && !document.getElementById("transportType6").checked ) {
		alert("Please select Means of Transport");
		document.getElementById("transportType2").focus();
		return false;
	}
	
	if (identification=="") {
		alert("Please enter a identification.");
		document.getElementById("identification").focus();
		return false;
	}

	if (entryBPEU=="") {
		alert("Please enter a B/P in EU.");
		document.getElementById("entryBPEU").focus();
		return false;
	}
	
	if (entryBPEU=="") {
		alert("Please enter Description of Commodity.");
		document.getElementById("commodityDesciption").focus();
		return false;
	}

	
	if (commodityCode=="") {
		alert("Please enter Commodity code(HS Code).");
		document.getElementById("commodityCode").focus();
		return false;
	}
		
	if (netWt=="") {
		alert("Please enter a Net Wt.");
		document.getElementById("netWt").focus();
		return false;
	}

	if (grWt=="") {
		alert("Please enter a GR.WT.");
		document.getElementById("grWt").focus();
		return false;
	}
	
	if (!document.getElementById("proTempType1").checked && !document.getElementById("proTempType2").checked && !document.getElementById("proTempType3").checked ) {
		alert("Please select Temperature of product");
		document.getElementById("proTempType3").focus();
		return false;
	}
	
	if (noOfPackage=="") {
		alert("Please enter Number of Packages.");
		document.getElementById("noOfPackage").focus();
		return false;
	}

	if (containerNo=="") {
		alert("Please enter a container No.");
		document.getElementById("containerNo").focus();
		return false;
	}
		
	if (containerNo=="") {
		alert("Please enter a seal No.");
		document.getElementById("sealNo").focus();
		return false;
	}
	
	if (typeOfPackaging=="") {
		alert("Please enter Type of Packaging.");
		document.getElementById("typeOfPackaging").focus();
		return false;
	}

	if (species=="") {
		alert("Please enter Species.");
		document.getElementById("species").focus();
		return false;
	}

	if (natureOfCommodity=="") {
		alert("Please enter Nature of commodity.");
		document.getElementById("natureOfCommodity").focus();
		return false;
	}
	
	
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}

function printSalesOrderWindow(url, width, height)
{
	var hcId = document.getElementById("selHC").value;
	var displayUrl = url+"?selHCId="+hcId;
	var winl = (screen.width - width) / 2;
     	var wint = (screen.height - height) / 2;
	eval("page = window.open(displayUrl, 'Forstar_Foods', 'top="+ wint +", left="+ winl +",  status=1,scrollbars=1,location=0,resizable=1,width="+ width +",height="+ height +"');");
}

/* Disable and enable the Print PO Button */
function disablePrintSOButton()
{
	if (document.getElementById("selHC").value=="") {
		document.getElementById("cmdPrintSO").disabled = true;
	} else {
		document.getElementById("cmdPrintSO").disabled = false;
	}
}

function enableSOButton(mode)
{
	if (mode==1) {
		document.getElementById("cmdAdd").disabled = false;
		document.getElementById("cmdAdd1").disabled = false;
	} else if (mode==0) {
		document.getElementById("cmdSaveChange").disabled = false;
		document.getElementById("cmdSaveChange1").disabled = false;
	}
}

function disableSOButton(mode)
{		
	if (mode==1) {
		document.getElementById("cmdAdd").disabled = true;
		document.getElementById("cmdAdd1").disabled = true;
	} else if (mode==0) {
		document.getElementById("cmdSaveChange").disabled = true;
		document.getElementById("cmdSaveChange1").disabled = true;
	}
}
	/* displaying selected Item*/
	function disTxtVal()
	{
		var productTemp = ""
		var noOfPackage = document.getElementById("noOfPackage").value;
		var netWt	= document.getElementById("netWt").value;	
		var approvalNumber = document.getElementById("approvalNumber").value;	

		if (document.getElementById("proTempType1").checked) 	  productTemp= "Ambient";
		else if (document.getElementById("proTempType2").checked) productTemp= "Chilled";
		else if (document.getElementById("proTempType3").checked) productTemp= "FROZEN";
		if (productTemp!="") document.getElementById("treatmentType").innerHTML = productTemp;

		if (noOfPackage) document.getElementById("identificationPackages").innerHTML = noOfPackage+"&nbsp;M/CTN";

		if (netWt) document.getElementById("identificationNetWt").innerHTML = number_format(netWt,3,'.','')+"&nbsp;KGS.";

		if (approvalNumber) document.getElementById("approvalNoEst").innerHTML = approvalNumber;
		
		
		
	}
