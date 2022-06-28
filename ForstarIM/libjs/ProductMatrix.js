function validateProductMatrix(form)
{
	var productCode = form.productCode.value;
	var productName = form.productName.value;
	var netWt 	= form.netWt.value;
	var fishWt 	= form.fishWt.value;
	var forExport	= form.forExport.value;
	var rMCodeId	= form.rMCodeId.value;
	var noOfBatches = form.noOfBatches.value;
	var batchSize	= form.batchSize.value;
	var selFish	= form.selFish.value;
	var productionCode = form.productionCode.value;
	var packingCode = form.packingCode.value;
	var freightChargePerPack = form.freightChargePerPack.value;
	var productExciseRate = form.productExciseRate.value;
	var idealFactoryCost = form.idealFactoryCost.value;

	if (productCode=="") {
		alert("Please enter a Product Code.");
		form.productCode.focus();
		return false;
	}	
	if (productName=="") {
		alert("Please enter a Product Name.");
		form.productName.focus();
		return false;
	}
	if (netWt=="") {
		alert("Please enter Net Wt.");
		form.netWt.focus();
		return false;
	}
	
	if (fishWt=="") {
		alert("Please enter Fish Wt.");
		form.fishWt.focus();
		return false;
	}

	if (forExport=="") {
		alert("Please select Export Option.");
		form.forExport.focus();
		return false;
	}
	if (rMCodeId=="") {
		alert("Please select RM Code.");
		form.rMCodeId.focus();
		return false;
	}
	if (noOfBatches=="") {
		alert("Please enter No. of Batches.");
		form.noOfBatches.focus();
		return false;
	}
	if (batchSize=="") {
		alert("Please enter a Batch Size.");
		form.batchSize.focus();
		return false;
	}
	if (selFish=="") {
		alert("Please select a Fish.");
		form.selFish.focus();
		return false;
	}
	if (productionCode=="") {
		alert("Please select a Production Code.");
		form.productionCode.focus();
		return false;
	}
	if (packingCode=="") {
		alert("Please select a Packing Code.");
		form.packingCode.focus();
		return false;
	}
	if (freightChargePerPack=="") {
		alert("Please enter Freight Charge per pack.");
		form.freightChargePerPack.focus();
		return false;
	}
	if (productExciseRate=="") {
		alert("Please enter Excise Rate.");
		form.productExciseRate.focus();
		return false;
	}
	if (idealFactoryCost=="") {
		alert("Please enter Ideal Factory Cost.");
		form.idealFactoryCost.focus();
		return false;
	}	

	if (!confirmSave()) {
		return false;
	}
	return true;
}

	// Find Gravy Wt
	function findGravyWt()
	{
		var netWt = parseFloat(document.getElementById("netWt").value);
		var fishWt = parseFloat(document.getElementById("fishWt").value);
		calcGravyWt = netWt - fishWt;
		if (!isNaN(calcGravyWt)) {
			document.getElementById("gravyWt").value = number_format(calcGravyWt,3,'.','');
		}
		
		//Percentage of Seafood
		seaFoodPercent();
	}

	//Find % Of Seafood
	function seaFoodPercent()
	{
		var netWt = parseFloat(document.getElementById("netWt").value);
		var fishWt = parseFloat(document.getElementById("fishWt").value);
		calcSeaFoodPercent = (fishWt/netWt)*100;
		if (!isNaN(calcSeaFoodPercent)) {
			document.getElementById("percentSeafood").value = number_format(calcSeaFoodPercent,0,'','');
		}
	}

	function findPerPouchCost()
	{
		var calcProcessingCost = 0;

		var batchSize	     = parseFloat(document.getElementById("batchSize").value);	

		// 1. Calculate Water Cost Per Pouch
		var calcWaterCostPerPouch = 0;
		var waterCostPerBtch = parseFloat(document.getElementById("waterCostPerBtch").value);
		calcWaterCostPerPouch = waterCostPerBtch/batchSize;		
		if (!isNaN(calcWaterCostPerPouch)) {
			document.getElementById("waterCostPerPouch").value = number_format(calcWaterCostPerPouch,2,'.','')
		}
		
		// 2. Calc Diesel Cost Per Pouch
		var calcDieselCostPerPouch = 0;
		var dieselCostPerBtch = parseFloat(document.getElementById("dieselCostPerBtch").value);
		calcDieselCostPerPouch = dieselCostPerBtch/batchSize;
		if (!isNaN(calcDieselCostPerPouch)) {
			document.getElementById("dieselCostPerPouch").value = number_format(calcDieselCostPerPouch,2,'.','');
		}

		//3. Calc Electric Cost Per Pouch
		var calcElectricCostPerPouch = 0;
		var electricityCostPerBtch= parseFloat(document.getElementById("electricityCostPerBtch").value);
		calcElectricCostPerPouch = electricityCostPerBtch/batchSize;
		if (!isNaN(calcElectricCostPerPouch)) {
			document.getElementById("electricCostPerPouch").value = number_format(calcElectricCostPerPouch,2,'.','');
		}

		// 4. Calc Gas Cost Per Pouch
		var calcGasCostPerPouch = 0;
		var gasCostPerBtch = parseFloat(document.getElementById("gasCostPerBtch").value);
		calcGasCostPerPouch = gasCostPerBtch/batchSize;
		if (!isNaN(calcGasCostPerPouch)) {
			document.getElementById("gasCostPerPouch").value = number_format(calcGasCostPerPouch,2,'.','');
		}
	
		//5. Consumables per pouch
		var calcConsumCostPerPouch = 0;
		var maintCostPerBtch = parseFloat(document.getElementById("maintCostPerBtch").value);
		calcConsumCostPerPouch = maintCostPerBtch/batchSize;
		if (!isNaN(calcConsumCostPerPouch)) {
			document.getElementById("consumableCostPerPouch").value = number_format(calcConsumCostPerPouch,2,'.','');
		}

		// 6. Manpower Cost/Pouch
		var calcVariManPowerCostPerPouch = 0;
		var calcFixedManPowerCostPerPouch = 0;
		var calcManPowerCostPerPouch = 0;
		var variManPwerCostPerBtch = parseFloat(document.getElementById("variManPwerCostPerBtch").value);
		calcVariManPowerCostPerPouch = variManPwerCostPerBtch/batchSize;
		var fixedManPowerCostPerDay = parseFloat(document.getElementById("fixedManPowerCostPerDay").value);
		var noOfBatches		    = parseInt(document.getElementById("noOfBatches").value);
		calcFixedManPowerCostPerPouch = fixedManPowerCostPerDay/(noOfBatches*batchSize);
		
		calcManPowerCostPerPouch = calcVariManPowerCostPerPouch+calcFixedManPowerCostPerPouch;
		if (!isNaN(calcManPowerCostPerPouch)) {
			document.getElementById("manPowerCostPerPouch").value = number_format(calcManPowerCostPerPouch,2,'.','');
		}		

		// 7. Fish prep cost/Pouch
		var calcFishPrepCostPerPouch = 0;
		var fishWt	= parseFloat(document.getElementById("fishWt").value);
		var selFishCost = parseFloat(document.getElementById("selFishCost").value);
		calcFishPrepCostPerPouch = fishWt * selFishCost;
		if (!isNaN(calcFishPrepCostPerPouch)) {
			document.getElementById("fishPrepCostPerPouch").value = number_format(calcFishPrepCostPerPouch,2,'.','');
		}

		//Processing cost	
		calcProcessingCost = calcWaterCostPerPouch + calcDieselCostPerPouch + calcElectricCostPerPouch + calcGasCostPerPouch + calcConsumCostPerPouch + calcManPowerCostPerPouch + calcFishPrepCostPerPouch;
		if (!isNaN(calcProcessingCost)) {
			document.getElementById("processingCost").value = number_format(calcProcessingCost,2,'.','');
		}
	
		// Find testing Cost
		findTestingCost();
		//Basic Manufacturing Cost
		findBasicManufacturingCost();			
	}

	// Find the Product Marketing Cost
	function findProductMktgCost()
	{
		var calcMktgCostPerPouch = 0;
		var forExport		= document.getElementById("forExport").value;
		var mktgTeamCostPerPouch = parseFloat(document.getElementById("mktgTeamCostPerPouch").value);
		var mktgTravelCost	 = parseFloat(document.getElementById("mktgTravelCost").value);
		calcMktgCostPerPouch  = mktgTeamCostPerPouch + mktgTravelCost;
		if (!isNaN(calcMktgCostPerPouch) && forExport=='N') {
			document.getElementById("mktgCost").value = number_format(calcMktgCostPerPouch,2,'.','');
		} else {
			document.getElementById("mktgCost").value = 0;
		}
		//Find Admin Overhead Charge
		findAdminOverheadCharge();
	}

	//Find the Advert Cost Calculation
	function findAdvertCost()
	{
		var forExport		= document.getElementById("forExport").value;
		var adCostPerPouch = parseFloat(document.getElementById("adCostPerPouch").value);
		if (forExport=='N') document.getElementById("proAdvertCost").value = number_format(adCostPerPouch,2,'.','');
		else document.getElementById("proAdvertCost").value = 0;

		//Find Admin Overhead Charge
		findAdminOverheadCharge();
	}	

	//Calculate the RM Cost (Product RM Cost) of Sea Food and gravy
	function calcProductRMCost()
	{
		var calcSeaFoodCost = 0;
		var calcGravyCost = 0;
		var calcRMCost = 0;
		var fishWt  = parseFloat(document.getElementById("fishWt").value);
		var gravyWt = parseFloat(document.getElementById("gravyWt").value);
		var fishRatePerKgPerBatch = parseFloat(document.getElementById("fishRatePerKgPerBatch").value);
		var gravyRatePerKgPerBatch = parseFloat(document.getElementById("gravyRatePerKgPerBatch").value);	

		calcSeaFoodCost = fishWt*fishRatePerKgPerBatch;
		if (!isNaN(calcSeaFoodCost)) {
			document.getElementById("seaFoodCost").value = number_format(calcSeaFoodCost,2,'.','');
		}
		
		calcGravyCost = gravyWt*gravyRatePerKgPerBatch;
		if (!isNaN(calcGravyCost)) {
			document.getElementById("gravyCost").value = number_format(calcGravyCost,2,'.','');
		}	

		// Find RM Cost
		calcRMCost = parseFloat(document.getElementById("seaFoodCost").value) + parseFloat(document.getElementById("gravyCost").value);
		if (!isNaN(calcRMCost)) {
			document.getElementById("rMCost").value = number_format(calcRMCost,2,'.','');
		}
		
		// Find testing Cost
		findTestingCost();
		//Basic Manufacturing Cost
		findBasicManufacturingCost();		
	}

	// Find Testing Cost
	function findTestingCost()
	{
		var calcTestingCost = 0;
		var pouchesTestPerBatchUnit = parseFloat(document.getElementById("pouchesTestPerBatchUnit").value);
		var processingCost 	= parseFloat(document.getElementById("processingCost").value);
		var rMCost		= parseFloat(document.getElementById("rMCost").value);

		calcTestingCost = (processingCost+rMCost) * pouchesTestPerBatchUnit;
		if (!isNaN(calcTestingCost)) {
			document.getElementById("testingCost").value = number_format((calcTestingCost/100),2,'.','');
		}
		
		//Basic Manufacturing Cost
		findBasicManufacturingCost();
	}

	//Basic Manufacturing Cost
	function findBasicManufacturingCost()
	{
		var productOuterPkgCost = 0;
		var productInnerPkgCost = 0;
		var testingCost 	= 0;
		var processingCost 	= 0;
		var rMCost		= 0;

		if (document.getElementById("productOuterPkgCost").value!="") 
			productOuterPkgCost = parseFloat(document.getElementById("productOuterPkgCost").value);
		if (document.getElementById("productInnerPkgCost").value!="") 
			productInnerPkgCost = parseFloat(document.getElementById("productInnerPkgCost").value);
		if (document.getElementById("testingCost").value!="") 
			testingCost = parseFloat(document.getElementById("testingCost").value);
		if (document.getElementById("processingCost").value!="") 
			processingCost = parseFloat(document.getElementById("processingCost").value);
		if (document.getElementById("rMCost").value!="") 
			rMCost = parseFloat(document.getElementById("rMCost").value);
		
		calcMftingCost =  productOuterPkgCost+productInnerPkgCost+testingCost+processingCost+rMCost;
		if (!isNaN(calcMftingCost)) {
			document.getElementById("basicManufactCost").value = number_format(calcMftingCost,2,'.','');
		}

		// Find Holding Cost
		findHoldingCost();
		//Find Admin Overhead Charge
		findAdminOverheadCharge();
	}


	// Find Holding Cost
	//=(basicManufactCost*(holdingCost/No of Days in Year)*Holding Duration
	function findHoldingCost()
	{
		var calcHoldingCost = 0;
		var basicManufactCost	= parseFloat(document.getElementById("basicManufactCost").value);
		var holdingCost		= parseFloat(document.getElementById("holdingCost").value);
		var holdingDuration 	= parseFloat(document.getElementById("holdingDuration").value);
		var noOfDaysInYear 	= parseFloat(document.getElementById("noOfDaysInYear").value);
		
		calcHoldingCost = (basicManufactCost*((holdingCost/100)/noOfDaysInYear)*holdingDuration);
		if (!isNaN(calcHoldingCost)) {
			document.getElementById("proHoldingCost").value = number_format(calcHoldingCost,2,'.','');
		}
		//Find Admin Overhead Charge
		findAdminOverheadCharge();		
	}

	//Find Admin Overhead Charge
	
	//=SUM(proHoldingCost+proAdvertCost+mktgCost+basicManufactCost)*adminOverheadChargesCost
	function findAdminOverheadCharge()
	{
		var calcAdminOverheadCharge = 0;
		var proHoldingCost 	= parseFloat(document.getElementById("proHoldingCost").value);
		var proAdvertCost 	= parseFloat(document.getElementById("proAdvertCost").value);
		var mktgCost 		= parseFloat(document.getElementById("mktgCost").value);
		var basicManufactCost	= parseFloat(document.getElementById("basicManufactCost").value);

		var adminOverheadChargesCost = parseFloat(document.getElementById("adminOverheadChargesCost").value);
		
		calcAdminOverheadCharge = (proHoldingCost+proAdvertCost+mktgCost+basicManufactCost)*adminOverheadChargesCost;
		if (!isNaN(calcAdminOverheadCharge)) {
			document.getElementById("adminOverhead").value = number_format((calcAdminOverheadCharge/100),2,'.','');
		}	
		
		// Total Host
		findTotalFactoryCost();	
	}

	// Total Host
	function findTotalFactoryCost()
	{
		var calcTotalCost = 0;
		var proHoldingCost 	= parseFloat(document.getElementById("proHoldingCost").value);
		var proAdvertCost 	= parseFloat(document.getElementById("proAdvertCost").value);
		var mktgCost 		= parseFloat(document.getElementById("mktgCost").value);
		var basicManufactCost	= parseFloat(document.getElementById("basicManufactCost").value);
		var adminOverhead	= parseFloat(document.getElementById("adminOverhead").value);

		calcTotalCost	= proHoldingCost+proAdvertCost+mktgCost+basicManufactCost+adminOverhead;
		if (!isNaN(calcTotalCost)) {
			document.getElementById("totalCost").value = number_format(calcTotalCost,2,'.','');
		}		
		
		//Profit Margin
		findProfitMargin();
		//Actual Fact Cost
		findActualFactCost();
	}

	//Profit Margin
	function findProfitMargin()
	{
		var calcProfitMargin = 0;
		var totalCost	 = parseFloat(document.getElementById("totalCost").value);
		var profitMargin = parseFloat(document.getElementById("profitMargin").value);
		calcProfitMargin = totalCost*profitMargin; 
		if (!isNaN(calcProfitMargin)) {
			document.getElementById("productProfitMargin").value = number_format((calcProfitMargin/100),2,'.','');
		}		

		//Actual Fact Cost
		findActualFactCost();
	}
	
	//Actual Fact Cost
	function findActualFactCost()
	{
		var calcActualCost = 0;
		var totalCost = parseFloat(document.getElementById("totalCost").value);
		var productProfitMargin = parseFloat(document.getElementById("productProfitMargin").value);
		calcActualCost = totalCost+productProfitMargin;
		if (!isNaN(calcActualCost)) {
			document.getElementById("actualFactCost").value = number_format(calcActualCost,2,'.','');
		}

		//Contingency
		findContingency();
	}

	//Contingency
	// Ideal Fact cost - actual Fact cost
	function findContingency()
	{
		var idealFactoryCost = 0;
		var actualFactCost = 0;
		
		if (document.getElementById("idealFactoryCost").value!="")
			idealFactoryCost = parseFloat(document.getElementById("idealFactoryCost").value);
		if (document.getElementById("actualFactCost").value!="")
			actualFactCost = parseFloat(document.getElementById("actualFactCost").value);

		calcContingency = idealFactoryCost-actualFactCost;
		if (!isNaN(calcContingency)) {
			document.getElementById("contingency").value = number_format(calcContingency,2,'.','');
		}	

		//PM in % of FC
		findPMInPercentOfFc();	
	}

	//PM in % of FC
	//(PrfitMargin+Contigency)/Ideal Fact Cost
	function findPMInPercentOfFc()
	{
		var productProfitMargin = parseFloat(document.getElementById("productProfitMargin").value);
		var contingency 	= parseFloat(document.getElementById("contingency").value);
		var idealFactoryCost 	= parseFloat(document.getElementById("idealFactoryCost").value);

		calcPMInPercentOfFC = (productProfitMargin+contingency)/idealFactoryCost;

		if (!isNaN(calcPMInPercentOfFC) && document.getElementById("productProfitMargin").value!="") {
			document.getElementById("pmInPercentOfFc").value = number_format(Math.abs((calcPMInPercentOfFC*100)),2,'.','');
		} else {
			document.getElementById("pmInPercentOfFc").value = 0;
		}
	}
	
	//Calculating Gravy Weight
	function calculateGravyWt()
	{
		var netWgt = document.getElementById('productNetwt').value;
		var fixedWgt = document.getElementById('fixedWgt').value;
		var recipeId = document.getElementById('recipeName').value;
		//var seafdYield = document.getElementById('seafoodYield').value;
		//var unit = document.getElementById('productUnit').value;
		
		if(netWgt == "")
		{
			netWgt = 0;
		}
		
		if(fixedWgt == "")
		{
			fixedWgt = 0;
		}
		
		if(fixedWgt <= netWgt)
		{
			var gravyWgt = parseFloat(netWgt) - parseFloat(fixedWgt);
			document.getElementById('gravyWgt').value = gravyWgt;
			xajax_getRecipeCost(recipeId,fixedWgt,gravyWgt);
		}
		else
		{
			alert("Fixed Wt must be less than Net Wt");
			document.getElementById('fixedWgt').focus();
		
		}
		
		if(netWgt!="")
		{
			xajax_getProductionMatrix(netWgt);
			xajax_getPackingMatrix(netWgt);
		}
		
	}
	
	function calcRawMaterialCost()
	{
		var mainIngCost = document.getElementById("mainIngredntCost").value;
		var gravyCost   = document.getElementById("gravyCost").value;
		
		var rawMaterialCost = parseFloat(mainIngCost) + parseFloat(gravyCost);
		
		document.getElementById("rawMaterialCost").value = rawMaterialCost;
	}
	
	function calcPerPouchCost()
	{
		var waterCostPerPouch=0; var dieselCostPerPouch=0; var electCostPerPouch=0; var gasCostPerPouch=0;
		var consmblCostPerPouch=0; var manPowerCostPerPouch=0; var rdStaffCostPerPouch=0;
		
		var waterCostVal = document.getElementById("waterCostValue").value;
		var dieselCostVal = document.getElementById("dieselCostValue").value;
		var electCostVal = document.getElementById("electCostValue").value;
		var gasCostVal = document.getElementById("gasCostValue").value;
		
		var waterCostPerBatch = document.getElementById("waterCostperBatch").value;
		var dieselCostPerBatch = document.getElementById("dieselCostperBatch").value;
		var electCostPerBatch = document.getElementById("electCostperBatch").value;
		var gasCostPerBatch = document.getElementById("gasCostperBatch").value; 
		
		var fixedManpowerCost = document.getElementById("fixedManpowerCost").value;
		var fixedStaffCost = document.getElementById("fixedStaffCost").value;
		var mainConsCost = document.getElementById("mainConsCost").value;
		var variblManpowerCost = document.getElementById("variblManpowerCost").value;
		var noOfDaysMnth = document.getElementById("noOfDaysMnth").value;
		
		var noOfBtchs = document.getElementById("noOfBatches").value; 
		var batchSize = document.getElementById("batchSize").value; 
		
		waterCostPerPouch = (waterCostPerBatch/batchSize)*waterCostVal;
		dieselCostPerPouch = (dieselCostPerBatch/batchSize)*dieselCostVal;
		electCostPerPouch = (electCostPerBatch/batchSize)*electCostVal;
		gasCostPerPouch = (gasCostPerBatch/batchSize)*gasCostVal; 
		consmblCostPerPouch = mainConsCost/batchSize;
		manPowerCostPerPouch = parseFloat(variblManpowerCost/batchSize) + parseFloat((fixedManpowerCost/noOfDaysMnth)/(noOfBtchs*batchSize));
		rdStaffCostPerPouch = parseFloat(variblManpowerCost/batchSize) + parseFloat((fixedStaffCost/noOfDaysMnth)/(noOfBtchs*batchSize));
		
		//alert(batchSize);
		//alert("water--"+waterCostVal+"--diesel--"+dieselCostVal+"--ELect--"+electCostVal+"--Gas--"+gasCostVal);
		//alert("water--"+waterCostPerBatch+"--diesel--"+dieselCostPerBatch+"--ELect--"+electCostPerBatch+"--Gas--"+gasCostPerBatch+"Maint"+mainConsCost+"VarManPower"+variblManpowerCost+"FixedMan"+fixedManpowerCost+"FixedStaff"+fixedStaffCost);
		
		document.getElementById("waterCostperPouch").value = number_format(waterCostPerPouch,2,'.','');
		document.getElementById("dieselCostperPouch").value = number_format(dieselCostPerPouch,2,'.','');
		document.getElementById("electCostperPouch").value = number_format(electCostPerPouch,2,'.','');
		document.getElementById("gasCostperPouch").value = number_format(gasCostPerPouch,2,'.',''); 
		document.getElementById("consumblCostperPouch").value = number_format(consmblCostPerPouch,2,'.',''); 
		document.getElementById("manpowerCostperPouch").value = number_format(manPowerCostPerPouch,2,'.',''); 
		document.getElementById("rdCostperPouch").value = number_format(rdStaffCostPerPouch,2,'.',''); 
		
		calcProcessingCost();
	}
	
	function calcProcessingCost()
	{
		var value=0;
		var processingCost=0;
		var rdInclude = document.getElementById("rdCostIncluded").value;
		var rdStaffCost = document.getElementById("rdCostperPouch").value;
		var manPowerCost = document.getElementById("manpowerCostperPouch").value;
		var waterCostPerPouch = document.getElementById("waterCostperPouch").value;
		var dieselCostperPouch = document.getElementById("dieselCostperPouch").value;
		var electCostperPouch = document.getElementById("electCostperPouch").value;
		var gasCostperPouch = document.getElementById("gasCostperPouch").value;
		var consumblCostperPouch = document.getElementById("consumblCostperPouch").value;
		
		if(rdInclude == 1)
		{
			value = rdStaffCost;
		}
		else if(rdInclude == 2)
		{
			value = parseFloat(manPowerCost) + parseFloat((parseFloat(rdStaffCost) - parseFloat(manPowerCost))/2);
		}
		else
		{
			value = manPowerCost;
		}
		
		processingCost = parseFloat(waterCostPerPouch) + parseFloat(dieselCostperPouch) + parseFloat(electCostperPouch) + parseFloat(gasCostperPouch) + parseFloat(consumblCostperPouch) + parseFloat(value);
		document.getElementById("processCost").value = number_format(processingCost,2,'.','');
		
		calcTestingCost();
	}
	
	function calcTestingCost()
	{
		var testingPouchUnit = document.getElementById("testingPouchUnit").value;
		var processingCost = document.getElementById("processCost").value;
		var rawMaterialCost = document.getElementById("rawMaterialCost").value;
		var netWgt = number_format(document.getElementById("productNetwt").value,3,'.','');
		var value = 0; var testingCost = 0;
		
		if(netWgt<0.500)
		{
			value = 2;
		}
		else{
			value = 1;
		}
		
		testingCost = ((parseFloat(processingCost) + parseFloat(rawMaterialCost))*testingPouchUnit)/(100*value);
		document.getElementById('testingCost').value = number_format(testingCost,2,'.','');
		
		calcPackingCost();
	}
	
	function calcPackingCost()
	{
		var innerPackingCost = 0; 
		var outerPackingCost = 0;
		
		var packingInnerCost = document.getElementById('packingInnerCost').value;
		var packingOuterCost = document.getElementById('packingOuterCost').value;
		var sharePrimaryPack = document.getElementById('sharedPrmryPckng').value;
		var shareVal = document.getElementById('shareVal').value;
		var sharePackVal = document.getElementById('sharePack').value;
		
		if(sharePrimaryPack == 1)
		{
		   if(sharePackVal=="Inner")
		   {
				innerPackingCost = packingInnerCost*(1/shareVal);
				outerPackingCost = packingOuterCost;
		   }
		   else if(sharePackVal=="Outer")
		   {
				innerPackingCost = packingInnerCost;
				outerPackingCost = packingOuterCost*(1/shareVal);
		   }
		}
		else
		{
			innerPackingCost = packingInnerCost;
			outerPackingCost = packingOuterCost;
		}
		
		document.getElementById('innerPackingCost').value = number_format(innerPackingCost,2,'.','');
		document.getElementById('outerPackingCost').value = number_format(outerPackingCost,2,'.','');
		
		calcBasicManufacturingCost();
	}
	
	function calcBasicManufacturingCost()
	{
		var basicManufacturingCost=0;
		var rawMaterialCost = document.getElementById('rawMaterialCost').value;
		var processingCost = document.getElementById('rawMaterialCost').value;
		var testingCost = document.getElementById('rawMaterialCost').value;
		var innerPackingCost = document.getElementById('rawMaterialCost').value;
		var outerPackingCost = document.getElementById('rawMaterialCost').value;
		
		basicManufacturingCost = parseFloat(rawMaterialCost) + parseFloat(processingCost) + parseFloat(testingCost) + parseFloat(innerPackingCost) + parseFloat(outerPackingCost);
		document.getElementById('basicManuftrCost').value = number_format(basicManufacturingCost,2,'.','');
		
		calculateHoldingCost();
	}
	
	function calculateHoldingCost()
	{
		var holdingCost=0;
		var basicManuftrCost = document.getElementById('basicManuftrCost').value;
		var holdingCost = document.getElementById('pdtHoldingCost').value;
		var noOfDaysinYear = document.getElementById('noOfDaysYear').value;
		var paymentDuration = document.getElementById('paymntDuratn').value;
		
		//alert(basicManuftrCost+"---"+holdingCost+"---"+noOfDaysinYear+"---"+paymentDuration);
		holdingCost = ((basicManuftrCost*holdingCost)/(noOfDaysinYear*100))*paymentDuration;
		document.getElementById("holdingCost").value = number_format(holdingCost,2,'.','');
		
		calculateTotalCost();
	}
	
	function calculateTotalCost()
	{
		var totalCost=0;
		var basicManuftrCost = document.getElementById('basicManuftrCost').value;
		var marketngCost = document.getElementById('marktngCost').value;
		var advrtsmntCost = document.getElementById('advtsCost').value;
		var holdingCost = document.getElementById('holdingCost').value;
		var adminOverhead = document.getElementById('adminOverhead').value;
		
		totalCost = parseFloat(basicManuftrCost) + parseFloat(marketngCost) + parseFloat(advrtsmntCost) + parseFloat(holdingCost) + parseFloat(adminOverhead);
		document.getElementById("totalCost").value = number_format(totalCost,2,'.','');
		
		calculateProfitMargin();
	}
	
	function calculateProfitMargin()
	{
		
	}
	
	function calculateActualFactoryCost()
	{
		var actualFactoryCost = 0;
		var profitMargin = document.getElementById("profitMargin").value;
		var testingCost = document.getElementById("totalCost").value;
		actualFactoryCost = parseFloat(profitMargin) + parseFloat(testingCost);
		document.getElementById("actualFactCost").value = actualFactoryCost;
		
		calculateIdealFactoryCost();
	}
	
	function calculateIdealFactoryCost()
	{
		var actualFactCost = document.getElementById("actualFactCost").value;
		document.getElementById("idealFactCost").value = number_format(actualFactCost,1,'.','');
		
		calculateContingency()
	}
	
	function calculateContingency()
	{
		var contingency=0;
		var idealFactCost = document.getElementById("idealFactCost").value;
		var actualFactCost = document.getElementById("actualFactCost").value;
	    contingency = parseFloat(idealFactCost) - parseFloat(actualFactCost);
		document.getElementById("contingency").value = number_format(contingency,2,'.','');
		
		calculatePMPercent();
	}
	
	function calculatePMPercent()
	{
		var pmPercent=0;
		var profitMargin = document.getElementById("profitMargin").value;
		var contingency = document.getElementById("contingency").value;
		var idealFactCost = document.getElementById("idealFactCost").value;
		
		if(profitMargin!=0)
		{
			pmPercent = ((parseFloat(profitMargin) + parseFloat(contingency))/idealFactCost)*100;
		}
		document.getElementById("pmPercent").value = pmPercent;
	}
	
	function innerOuterShareVal(shareVal)
	{
		if(shareVal == 1)
		{
			document.getElementById('shareDiv').style.display = "block";
			document.getElementById('shareInputDiv').style.display = "block";
		}
		else
		{
			document.getElementById('shareDiv').style.display = "none";
			document.getElementById('shareInputDiv').style.display = "none";
		}
	}
	
	function displaySingleCombo()
	{
		var pdtStyle = document.getElementById("productStyle").value;
		if(pdtStyle == 1)
		{
			document.getElementById('singleNetWgt').style.display = "block";
			document.getElementById('comboNetWgt').style.display = "none";
			document.getElementById('comboIdealPrice').style.display = "none";
		}
		else
		{
			document.getElementById('singleNetWgt').style.display = "none";
			document.getElementById('comboNetWgt').style.display = "block";
			document.getElementById('comboIdealPrice').style.display = "block";
		}
	}
	
	// ADD MULTIPLE Item- ADD ROW START
	function addNewRow(tableId,productId,productName)
	{
		var tbl		= document.getElementById(tableId);
		var lastRow	= tbl.rows.length;
		// alert(lastRow);
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "bRow_"+fldId;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		
		cell1.id = "srNo_"+fldId;		
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";

			//alert("entered");
			//alert("<?=$vehileTypeId?>");
			var product	= "<select name='productType_"+fldId+"' id='productType_"+fldId+"' ><option value='0'>--Select--</option>";
		<?php
			if (sizeof($selProductRecs)>0) {	
				foreach ($selProductRecs as $pdt) {
							$prdtId = $pdt[0];
							$prdtName	= $pdt[1];
							
		?>	
		
			if (productId=="<?=$prdtId?>")  var sel = "Selected";
			else var sel = "";

		product += "<option value=\"<?=$prdtId?>\" "+sel+"><?=$prdtName?></option>";	
		<?php
				}
			}
			
		?>	
		product += "</select>&nbsp;&nbsp;<input type='text' name='idealCost' id='idealCost' size='6' value=''/>";
		var ds = "N";	
		
		var imageButton = "<a href='###' onClick=\"setTestRowItemStatus('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		
		var hiddenFields = "<input name='bStatus_"+fldId+"' type='hidden' id='bStatus_"+fldId+"' value=''><input name='bIsFromDB_"+fldId+"' type='hidden' id='bIsFromDB_"+fldId+"' value='"+ds+"'><input type='hidden' name='productTypeId_"+fldId+"' id='productTypeId_"+fldId+"' value='"+productId+"'>";

		cell1.innerHTML	= product;
		cell2.innerHTML = imageButton+hiddenFields;	
		
		fldId		= parseInt(fldId)+1;	
		
		document.getElementById("hidProductTableRowCount").value = fldId;	
	
	}
	
	function setTestRowItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("bStatus_"+id).value = document.getElementById("bIsFromDB_"+id).value;
			document.getElementById("bRow_"+id).style.display = 'none';
	//document.getElementById("bRow_"+id).style.display = 'block';			
		}
		return false;
	}
