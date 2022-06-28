function validatePackingMatrix(form)
{
	var packingCode 	= form.packingCode.value;
	var packingName 	= form.packingName.value;
	var innerContainerType 	= form.innerContainerType.value;
	var pkgLabourRateType 	= form.pkgLabourRateType.value;
	var noOfPacksInMC	= form.noOfPacksInMC.value;
	var masterPackingType	= form.masterPackingType.value;
	var productType		= form.productType.value;

	/*if (packingCode=="") {
		alert("Please enter a Packing Code.");
		form.packingCode.focus();
		return false;
	} */	
	if (packingName=="") {
		alert("Please enter a Packing Name.");
		form.packingName.focus();
		return false;
	}
	/*if (productType=="") {
		alert("Please select a Product Type.");
		form.productType.focus();
		return false;
	} */
	if (innerContainerType==0) {
		alert("Please select a inner Container.");
		form.innerContainerType.focus();
		return false;
	}	
	if (pkgLabourRateType==0) {
		alert("Please select a Packing labour Rate.");
		form.pkgLabourRateType.focus();
		return false;
	}
	if (noOfPacksInMC=="") {
		alert("Please enter No of Packs in MC.");
		form.noOfPacksInMC.focus();
		return false;
	}	
	if (masterPackingType==0) {
		alert("Please select a Master Packing.");
		form.masterPackingType.focus();
		return false;
	}		
	if (!confirmSave()) {
		return false;
	}
	return true;
}


/*
 	//Display inner Container Rate
	function displayInnerContainerRate()
	{
		var innerContainerType = document.getElementById("innerContainerType").value;
		if (innerContainerType!=0) {
			var innerContainerSplit	=	innerContainerType.split("_");
			var innerContainerRate	=	innerContainerSplit[1];
			document.getElementById("innerContainerRate").value = innerContainerRate;		
		} else {
			document.getElementById("innerContainerRate").value =0;
		}
			
	}
*/

	// Common for Display Rate
	function displaySelPackingRate(field, displayField, qtyF)
	{
		var productType = document.getElementById("productType").value;
		var packingQty = 1;
		if (productType=='CP') { 
			packingQty = document.getElementById(qtyF).value;
		} else {
			packingQty = 1;
		}
		var getField = document.getElementById(field).value;
		if (getField!=0) {
			var getFieldSplit	=	getField.split("_");
			var packingRate		=	getFieldSplit[1];
			document.getElementById(displayField).value = number_format( (parseFloat(packingRate*packingQty)),2,'.','');		
		} else {
			document.getElementById(displayField).value =0;
		}
		// Calculate inner and outer packing cost
		calcInnerPackingCost();
		calcOuterPackingCost();
	}

	// Display Master Packing Rate
	function displayMasterPackingRate()
	{
		var calcMasterPkgRate = 0;
		var noOfPacksInMC = document.getElementById("noOfPacksInMC").value;
		
		var masterPackingType = document.getElementById("masterPackingType").value;
		if (masterPackingType!=0) {
			var masterPackingSplit	=	masterPackingType.split("_");
			var masterPackingRate	=	masterPackingSplit[1];
			calcMasterPkgRate = parseFloat(masterPackingRate/noOfPacksInMC);
			
			if (!isNaN(calcMasterPkgRate)) {				
				document.getElementById("masterPackingRate").value = number_format(calcMasterPkgRate,2,'.','');		
			} else {
				document.getElementById("masterPackingRate").value =0;
			}
		} else {
			document.getElementById("masterPackingRate").value =0;
		}		
	}

	// inner Packing Cost
	function calcInnerPackingCost()
	{
		var totalInnerpackingCost = 0;
		
		var innerContainerRate	= 0;
		var innerPackingRate	= 0;
		var innerSampleRate	= 0;
		var innerLabelingRate	= 0;
		var innerLeafletRate	= 0;
		var innerSealingRate	= 0;
		var pkgLabourRate	= 0;
		
		if (document.getElementById("innerContainerRate").value!=0) {
			innerContainerRate	= parseFloat(document.getElementById("innerContainerRate").value);
		}		
		if (document.getElementById("innerPackingRate").value!=0) {
			innerPackingRate	= parseFloat(document.getElementById("innerPackingRate").value);
		}
		if (document.getElementById("innerSampleRate").value!=0) {
			innerSampleRate		= parseFloat(document.getElementById("innerSampleRate").value);
		}
		if (document.getElementById("innerLabelingRate").value!=0) {
			innerLabelingRate	= parseFloat(document.getElementById("innerLabelingRate").value);
		}
		if (document.getElementById("innerLeafletRate").value!=0) {
			innerLeafletRate	= parseFloat(document.getElementById("innerLeafletRate").value);
		}
		if (document.getElementById("innerSealingRate").value!=0) {
			innerSealingRate	= parseFloat(document.getElementById("innerSealingRate").value);
		}
		if (document.getElementById("pkgLabourRate").value!=0) {
			pkgLabourRate	= parseFloat(document.getElementById("pkgLabourRate").value);
		}
		
		totalInnerpackingCost = innerContainerRate+innerPackingRate+innerSampleRate+innerLabelingRate+innerLeafletRate+innerSealingRate+pkgLabourRate;
		if (!isNaN(totalInnerpackingCost)) {
			document.getElementById("innerPkgCost").value = number_format(totalInnerpackingCost,2,'.','');
		}
	}


	// Outer Packing Cost
	function calcOuterPackingCost()
	{
		var totalOuterpackingCost = 0;
		
		var masterPackingRate	= 0;
		var masterSealingRate	= 0;		
		
		if (document.getElementById("masterPackingRate").value!=0) {
			masterPackingRate	= parseFloat(document.getElementById("masterPackingRate").value);
		}		
		if (document.getElementById("masterSealingRate").value!=0) {
			masterSealingRate	= parseFloat(document.getElementById("masterSealingRate").value);
		}		
		
		totalOuterpackingCost = masterPackingRate+masterSealingRate;
		if (!isNaN(totalOuterpackingCost)) {
			document.getElementById("outerPackingCost").value = number_format(totalOuterpackingCost,2,'.','');
		}
	}

// Text box Display Section
	function hidPackingQtyBox()
	{
		document.getElementById("innerContainerQtyDiv").style.display = "none";
		document.getElementById("innerPackingQtyDiv").style.display = "none";
		document.getElementById("innerSampleQtyDiv").style.display = "none";
		document.getElementById("innerLabelingQtyDiv").style.display = "none";
		document.getElementById("innerLeafletQtyDiv").style.display = "none";
		document.getElementById("innerSealingQtyDiv").style.display = "none";
		document.getElementById("pkgLabourRateDiv").style.display = "none";		
	}

	function showPackingQtyBox()
	{
		// CP = combo Product
		var productType = document.getElementById("productType").value;
		if (productType=='CP') {
			document.getElementById("innerContainerQtyDiv").style.display = "block";
			document.getElementById("innerPackingQtyDiv").style.display = "block";	
			document.getElementById("innerSampleQtyDiv").style.display = "block";
			document.getElementById("innerLabelingQtyDiv").style.display = "block";
			document.getElementById("innerLeafletQtyDiv").style.display = "block";
			document.getElementById("innerSealingQtyDiv").style.display = "block";
			document.getElementById("pkgLabourRateDiv").style.display = "block";	
		} else {
			document.getElementById("innerContainerQtyDiv").style.display = "none";
			document.getElementById("innerPackingQtyDiv").style.display = "none";
			document.getElementById("innerSampleQtyDiv").style.display = "none";
			document.getElementById("innerLabelingQtyDiv").style.display = "none";
			document.getElementById("innerLeafletQtyDiv").style.display = "none";
			document.getElementById("innerSealingQtyDiv").style.display = "none";
			document.getElementById("pkgLabourRateDiv").style.display = "none";
		}
	}
	
	function displayDispenserPkg(shrinkValue)
	{
		var dispenserId = document.getElementById("dispenserShrink").value;
		xajax_getDispenserPkg(dispenserId,shrinkValue);
	}
	
	function displayMasterPacking(numPacks)
	{
		var masterId = document.getElementById("masterPackingType").value;
		xajax_getMasterPacking(masterId,numPacks);
	}
	
	function displayInnerPackingCost()
	{
		var innerContainer = 0;
		var innerCarton = 0;
		var innerSample = 0;
		var innerLabeling = 0;
		var innerLeaflet = 0;
		var innerSealing = 0;
		var labourCost = 0;
		var tempIncrease = 0;
		
		if(document.getElementById("innerContainerRate").value!="")
		{ 
			innerContainer = document.getElementById("innerContainerRate").value; 
		}
		
		if(document.getElementById("innerPackingRate").value!="")
		{
			innerCarton = document.getElementById("innerPackingRate").value;
		}
		
		if(document.getElementById("innerSampleRate").value!="")
		{
			innerSample = document.getElementById("innerSampleRate").value;
		}
		
		if(document.getElementById("innerLabelingRate").value!="")
		{
			innerLabeling = document.getElementById("innerLabelingRate").value;
		}
		
		if(document.getElementById("innerLeafletRate").value!="")
		{
			innerLeaflet = document.getElementById("innerLeafletRate").value;
		}
		
		if(document.getElementById("innerSealingRate").value!="")
		{
			innerSealing = document.getElementById("innerSealingRate").value;
		}
		
		if(document.getElementById("labourCost").value!="")
		{
			labourCost = document.getElementById("labourCost").value;
		}
		
		tempIncrease = document.getElementById("tempIncreaseFactor").value;
		
		
		var innerPackingCost = (parseFloat(innerContainer) + parseFloat(innerCarton) + parseFloat(innerSample) + parseFloat(innerLabeling) + parseFloat(innerLeaflet) + parseFloat(innerSealing) + parseFloat(labourCost))* (1 + tempIncrease/100);
		var innerPackCost = number_format(innerPackingCost, 3, '.', '');
		document.getElementById("innerPkgCost").value = innerPackCost;
		
		
	}
	
	function displayOuterPackingCost()
	{
		var dispenserPkg = 0;
		var dispenserSeal = 0;
		var masterPacking = 0;
		var masterSeal = 0;
		var masterLoad = 0;
		var tempIncrease = 0;
		var outerPackingCost = 0;
		var outerPackCost = 0;
		
		
		if(document.getElementById("dispenserPkg").value!="")
		{
			dispenserPkg = document.getElementById("dispenserPkg").value;
		}
		
		if(document.getElementById("dispenserSealing").value!="")
		{
			dispenserSeal = document.getElementById("dispenserSealing").value;
		}
		
		if(document.getElementById("masterPackingRate").value!="")
		{
			masterPacking = document.getElementById("masterPackingRate").value;
		}
		
		if(document.getElementById("masterSealingRate").value!="")
		{
			masterSeal = document.getElementById("masterSealingRate").value;
		}
		
		if(document.getElementById("masterLoading").value!="")
		{
			masterLoad = document.getElementById("masterLoading").value;
		}
		
		tempIncrease = document.getElementById("tempIncreaseFactor").value;
		
		outerPackingCost = (parseFloat(dispenserPkg) + parseFloat(dispenserSeal) + parseFloat(masterPacking) + parseFloat(masterSeal) + parseFloat(masterLoad)) * (1 + tempIncrease/100);
		outerPackCost = number_format(outerPackingCost, 3, '.', '');
		document.getElementById("outerPackingCost").value = outerPackCost;
	}
	
	function labourCostOnly()
	{
		var masterLoading = 0;
		var masterSealing = 0;
		var dispenserSealing = 0;
		var labourCost = 0;
		var innerSeal = 0;
		var labourCostOnly = 0;
		var labourCst = 0;
		
		if(document.getElementById("masterLoading").value!="")
		{
			masterLoading = document.getElementById("masterLoading").value;
		}
		
		if(document.getElementById("masterSealingRate").value!="")
		{
			masterSealing = document.getElementById("masterSealingRate").value;
		}
		
		if(document.getElementById("dispenserSealing").value!="")
		{
			dispenserSealing = document.getElementById("dispenserSealing").value;
		}
		
		if(document.getElementById("labourCost").value!="")
		{
			labourCost = document.getElementById("labourCost").value;
		}
		
		if(document.getElementById("innerSealingRate").value!="")
		{
			innerSeal = document.getElementById("innerSealingRate").value;
		}
		
		labourCostOnly = parseFloat(masterLoading) + parseFloat(masterSealing) + parseFloat(dispenserSealing) + parseFloat(labourCost) + parseFloat(innerSeal);
		labourCst = number_format(labourCostOnly, 3, '.', '');
		document.getElementById("labourCostOnly").value = labourCst;
	}
