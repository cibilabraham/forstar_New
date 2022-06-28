function validateComboMatrix(form)
{
	var productCode = form.productCode.value;
	var productName = form.productName.value;	
	var forExport	= form.forExport.value;
	
	var packingCode = form.packingCode.value;
	var productCombination = form.productCombination.value;	
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
	if (forExport=="") {
		alert("Please select Export Option.");
		form.forExport.focus();
		return false;
	}	

	if (packingCode=="") {
		alert("Please select a Packing Code.");
		form.packingCode.focus();
		return false;
	}
	if (productCombination=="") {
		alert("Please enter No.of Product Combination.");
		form.productCombination.focus();
		return false;
	}
	
	if (productCombination!="" || productCombination!=0) {	
		var hidColumnCount = document.getElementById("hidColumnCount").value;
		
		for (i=1; i<=hidColumnCount;i++) {
			var netWt	= document.getElementById("netWt_"+i);
			var fishWt	= document.getElementById("fishWt_"+i); 
			var rMCodeId	= document.getElementById("rMCodeId_"+i);
			var noOfBatches = document.getElementById("noOfBatches_"+i); 
			var batchSize 	= document.getElementById("batchSize_"+i); 
			var selFish 	= document.getElementById("selFish_"+i); 
			var productionCode = document.getElementById("productionCode_"+i);
		/*	if (netWt.value=="") {
				alert("Please enter Net Wt.");
				netWt.focus();
				return false;
			}
			if (fishWt.value=="") {
				alert("Please enter Fish Wt.");
				fishWt.focus();
				return false;
			}
		*/
			if (rMCodeId.value=="") {
				alert("Please select RM Code.");
				rMCodeId.focus();
				return false;
			}
			if (noOfBatches.value=="") {
				alert("Please enter No. of Batches.");
				noOfBatches.focus();
				return false;
			}
			if (batchSize.value=="") {
				alert("Please enter a Batch Size.");
				batchSize.focus();
				return false;
			}
			if (selFish.value=="") {
				alert("Please select a Fish.");
				selFish.focus();
				return false;
			}
			if (productionCode.value=="") {
				alert("Please select a Production Code.");
				productionCode.focus();
				return false;
			}
		}
		/* Loop Ends Here*/
	}

	if (!validateComboProductRMCodeRepeat()) {
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


//Validate base Product Repeated 
function validateComboProductRMCodeRepeat()
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
	
    var rc = document.getElementById("hidColumnCount").value;
    var prevOrder = 0;
    var arr = new Array();
    var arri=0;

    for( j=1; j<=rc; j++ )    {
        var rv = document.getElementById("rMCodeId_"+j).value;
        if ( arr.indexOf(rv) != -1 )    {
            alert("RM Code cannot be duplicate.");
            document.getElementById("rMCodeId_"+j).focus();
            return false;
        }
        arr[arri++]=rv;
    }
    return true;
}

// Get all Combo Matrix Mix Product details
function getComboMatrixMixProduct()
{
	var productCombination = document.getElementById("productCombination").value;

	if (productCombination!="" || productCombination!=0) {

	var hidColumnCount = document.getElementById("hidColumnCount").value;	
	var calcGravyWt 		= 0;
	var calcSeaFoodPercent 		= 0;
	var totalWaterCostPouch 	= 0;
	var totalDieselCostPerPouch	= 0;
	var totalElectricCostPerPouch 	= 0;
	var totalGasCostPerPouch 	= 0;
	var totalConsumCostPerPouch	= 0;
	var totalManPowerCostPerPouch	= 0;
	var totalFishPrepCostPerPouch	= 0;
	var totalSeaFoodCost		= 0;
	var totalGravyCost		= 0;
	var calcRMCost 			= 0;
	var totalMktgCostPerPouch	= 0;
	var totalAdCostPerPouch		= 0;
	/**************	Loop Starts here*******************/ 	
	for (i=1; i<=hidColumnCount;i++) {
		var rMCodeId	= document.getElementById("rMCodeId_"+i).value;
	if (rMCodeId!="") {
		var netWt	= parseFloat(document.getElementById("netWt_"+i).value);
		var fishWt	= parseFloat(document.getElementById("fishWt_"+i).value); 
		// Calc Gravy Weight
		calcGravyWt = netWt-fishWt;
		if (!isNaN(calcGravyWt)) {
			document.getElementById("gravyWt_"+i).value = number_format(calcGravyWt,2,'.','') 
		}

		//Find % Of Seafood
		calcSeaFoodPercent = (fishWt/netWt)*100;
		//alert(fishWt+"/"+netWt);
		if (!isNaN(calcSeaFoodPercent)) {
			document.getElementById("percentSeafood_"+i).value = number_format(calcSeaFoodPercent,0,'','');
		}

		var batchSize	= parseInt(document.getElementById("batchSize_"+i).value); 
		// 1. Calculate Water Cost Per Pouch Each Product
		var calcWaterCostPerPouch = 0;
		var waterCostPerBtch = parseFloat(document.getElementById("waterCostPerBtch_"+i).value);
		calcWaterCostPerPouch = waterCostPerBtch/batchSize;
		
		if (!isNaN(calcWaterCostPerPouch)) {
			totalWaterCostPouch += calcWaterCostPerPouch;			
		}		

		// 2. Calc Diesel Cost Per Pouch
		var calcDieselCostPerPouch = 0;
		var dieselCostPerBtch = parseFloat(document.getElementById("dieselCostPerBtch_"+i).value);
		calcDieselCostPerPouch = dieselCostPerBtch/batchSize;
		if (!isNaN(calcDieselCostPerPouch)) {
			totalDieselCostPerPouch += calcDieselCostPerPouch;
		}

		//3. Calc Electric Cost Per Pouch
		var calcElectricCostPerPouch = 0;
		var electricityCostPerBtch= parseFloat(document.getElementById("electricityCostPerBtch_"+i).value);
		calcElectricCostPerPouch = electricityCostPerBtch/batchSize;
		if (!isNaN(calcElectricCostPerPouch)) {
			totalElectricCostPerPouch += calcElectricCostPerPouch;			
		}

		// 4. Calc Gas Cost Per Pouch
		var calcGasCostPerPouch = 0;
		var gasCostPerBtch = parseFloat(document.getElementById("gasCostPerBtch_"+i).value);
		calcGasCostPerPouch = gasCostPerBtch/batchSize;
		if (!isNaN(calcGasCostPerPouch)) {
			totalGasCostPerPouch +=calcGasCostPerPouch;
			//document.getElementById("gasCostPerPouch").value = number_format(calcGasCostPerPouch,2,'.','');
		}

		//5. Consumables per pouch
		var calcConsumCostPerPouch = 0;
		var maintCostPerBtch = parseFloat(document.getElementById("maintCostPerBtch_"+i).value);
		calcConsumCostPerPouch = maintCostPerBtch/batchSize;
		if (!isNaN(calcConsumCostPerPouch)) {
			totalConsumCostPerPouch += calcConsumCostPerPouch;
		}
	
		var noOfBatches  = parseInt(document.getElementById("noOfBatches_"+i).value); 

		// 6. Manpower Cost/Pouch
		var calcVariManPowerCostPerPouch = 0;
		var calcFixedManPowerCostPerPouch = 0;
		var calcManPowerCostPerPouch = 0;
		var variManPwerCostPerBtch = parseFloat(document.getElementById("variManPwerCostPerBtch_"+i).value);
		calcVariManPowerCostPerPouch = variManPwerCostPerBtch/batchSize;
		var fixedManPowerCostPerDay = parseFloat(document.getElementById("fixedManPowerCostPerDay_"+i).value);
		
		calcFixedManPowerCostPerPouch = fixedManPowerCostPerDay/(noOfBatches*batchSize);
		calcManPowerCostPerPouch = calcVariManPowerCostPerPouch+calcFixedManPowerCostPerPouch;
		if (!isNaN(calcManPowerCostPerPouch)) {
			totalManPowerCostPerPouch += calcManPowerCostPerPouch;
		}

		// 7. Fish prep cost/Pouch
		var calcFishPrepCostPerPouch = 0;
		var fishWt	= parseFloat(document.getElementById("fishWt_"+i).value);
		var selFishCost = parseFloat(document.getElementById("selFishCost_"+i).value);
		calcFishPrepCostPerPouch = fishWt * selFishCost;
		if (!isNaN(calcFishPrepCostPerPouch)) {
			totalFishPrepCostPerPouch += calcFishPrepCostPerPouch;
		}


		// RM Cost, sea Food cost and Gravy cost
		var calcSeaFoodCost = 0;
		var calcGravyCost = 0;
		
		var fishWt  = parseFloat(document.getElementById("fishWt_"+i).value);
		var gravyWt = parseFloat(document.getElementById("gravyWt_"+i).value);
		var fishRatePerKgPerBatch = parseFloat(document.getElementById("fishRatePerKgPerBatch_"+i).value);
		var gravyRatePerKgPerBatch = parseFloat(document.getElementById("gravyRatePerKgPerBatch_"+i).value);	

		calcSeaFoodCost = fishWt*fishRatePerKgPerBatch;
		if (!isNaN(calcSeaFoodCost)) {
			totalSeaFoodCost += calcSeaFoodCost;			
		}
		
		calcGravyCost = gravyWt*gravyRatePerKgPerBatch;
		if (!isNaN(calcGravyCost)) {
			totalGravyCost += calcGravyCost;
		}	
		
		var forExport = document.getElementById("forExport").value;

		// Find the Product Marketing Cost
		var calcMktgCostPerPouch = 0;
		var mktgTeamCostPerPouch = parseFloat(document.getElementById("mktgTeamCostPerPouch_"+i).value);
		var mktgTravelCost	 = parseFloat(document.getElementById("mktgTravelCost_"+i).value);
		calcMktgCostPerPouch  = mktgTeamCostPerPouch + mktgTravelCost;
		if (!isNaN(calcMktgCostPerPouch) && forExport=='N') {
			totalMktgCostPerPouch += calcMktgCostPerPouch;			
		} else {
			totalMktgCostPerPouch += 0;			
		}

		//Find the Advert Cost Calculation
		var adCostPerPouch = parseFloat(document.getElementById("adCostPerPouch_"+i).value);
		if (forExport=='N') {
			totalAdCostPerPouch += adCostPerPouch;			
		} else {
			totalAdCostPerPouch += 0;			
		}
	 } // RM Code check
	}	
	/**************	Loop Ends here*******************/ 	
	//1. Set the total Mix Product Water Cost	
	if (!isNaN(totalWaterCostPouch)) {
		document.getElementById("waterCostPerPouch").value = number_format(totalWaterCostPouch,2,'.','')
	}
	
	//2. Set the total Mix Product Diesel Cost	
	if (!isNaN(totalDieselCostPerPouch)) {
		document.getElementById("dieselCostPerPouch").value = number_format(totalDieselCostPerPouch,2,'.','')
	}

	//3. Set the total Mix Product Electric Cost	
	if (!isNaN(totalElectricCostPerPouch)) {
		document.getElementById("electricCostPerPouch").value = number_format(totalElectricCostPerPouch,2,'.','')
	}
		
	//4. Set the total Mix Product gas Cost	
	if (!isNaN(totalGasCostPerPouch)) {
		document.getElementById("gasCostPerPouch").value = number_format(totalGasCostPerPouch,2,'.','')
	}

	//5. Set the total Mix Product Consum Cost	
	if (!isNaN(totalConsumCostPerPouch)) {
		document.getElementById("consumableCostPerPouch").value = number_format(totalConsumCostPerPouch,2,'.','')
	}

	//6. Set the total Mix Product Manpower Cost	
	if (!isNaN(totalManPowerCostPerPouch)) {
		document.getElementById("manPowerCostPerPouch").value = number_format(totalManPowerCostPerPouch,2,'.','')
	}

	//7. Set the total Mix Product Manpower Cost	
	if (!isNaN(totalFishPrepCostPerPouch)) {
		document.getElementById("fishPrepCostPerPouch").value = number_format(totalFishPrepCostPerPouch,2,'.','')
	}

	//Processing cost	
	calcProcessingCost = totalWaterCostPouch + totalDieselCostPerPouch + totalElectricCostPerPouch + totalGasCostPerPouch + totalConsumCostPerPouch + totalManPowerCostPerPouch + totalFishPrepCostPerPouch;
	if (!isNaN(calcProcessingCost)) {
		document.getElementById("processingCost").value = number_format(calcProcessingCost,2,'.','');
	}	

	// set the total sea Food Cost
	if (!isNaN(totalSeaFoodCost)) {
		document.getElementById("seaFoodCost").value = number_format(totalSeaFoodCost,2,'.','')
	}

	// set the total sea Food Cost
	if (!isNaN(totalGravyCost)) {
		document.getElementById("gravyCost").value = number_format(totalGravyCost,2,'.','')
	}

	// Find RM Cost
	calcRMCost = totalSeaFoodCost + totalGravyCost;
	if (!isNaN(calcRMCost)) {
		document.getElementById("rMCost").value = number_format(calcRMCost,2,'.','');
	}

	// Testing Cost
	var calcTestingCost = 0;
	var pouchesTestPerBatchUnit = parseFloat(document.getElementById("pouchesTestPerBatchUnit").value);
	//var processingCost 	= parseFloat(document.getElementById("processingCost").value);
	//var rMCost		= parseFloat(document.getElementById("rMCost").value);
	calcTestingCost = (calcProcessingCost+calcRMCost) * pouchesTestPerBatchUnit;
	if (!isNaN(calcTestingCost)) {
		document.getElementById("testingCost").value = number_format((calcTestingCost/100),2,'.','');
	}	

	// Set total Mktg Cost
	if (!isNaN(totalMktgCostPerPouch) && forExport=='N') {		
		document.getElementById("mktgCost").value = number_format(totalMktgCostPerPouch,2,'.','');
	} else {
		document.getElementById("mktgCost").value =0;
	}

	// Set total ad Cost
	if (!isNaN(totalAdCostPerPouch) && forExport=='N') {
		//totalAdCostPerPouch += adCostPerPouch;
		document.getElementById("proAdvertCost").value = number_format(totalAdCostPerPouch,2,'.','');
	} else {
		document.getElementById("proAdvertCost").value == 0;			
	}

	//Basic Manufacturing Cost
	findComboMatrixBasicManufactCost();
   }
}

	//Basic Manufacturing Cost
	function findComboMatrixBasicManufactCost()
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
		findComboProdHoldingCost();
	}

	// Find Holding Cost	
	function findComboProdHoldingCost()
	{
		var calcHoldingCost = 0;
		var basicManufactCost	= parseFloat(document.getElementById("basicManufactCost").value);
		var holdingCost		= parseFloat(document.getElementById("holdingCost").value);
		var holdingDuration 	= parseFloat(document.getElementById("holdingDuration").value);
		var noOfDaysInYear 	= parseFloat(document.getElementById("noOfDaysInYear").value);
		//=(basicManufactCost*(holdingCost/No of Days in Year)*Holding Duration
		calcHoldingCost = (basicManufactCost*((holdingCost/100)/noOfDaysInYear)*holdingDuration);
		if (!isNaN(calcHoldingCost)) {
			document.getElementById("proHoldingCost").value = number_format(calcHoldingCost,2,'.','');
		}

		//Find Admin Overhead Charge	
		findComboProdAdminOverheadCharge();
	}

	//Find Admin Overhead Charge	
	function findComboProdAdminOverheadCharge()
	{
		var calcAdminOverheadCharge = 0;
		var proHoldingCost 	= parseFloat(document.getElementById("proHoldingCost").value);
		var proAdvertCost 	= parseFloat(document.getElementById("proAdvertCost").value);
		var mktgCost 		= parseFloat(document.getElementById("mktgCost").value);
		var basicManufactCost	= parseFloat(document.getElementById("basicManufactCost").value);

		var adminOverheadChargesCost = parseFloat(document.getElementById("adminOverheadChargesCost").value);

	//=SUM(proHoldingCost+proAdvertCost+mktgCost+basicManufactCost)*adminOverheadChargesCost
		calcAdminOverheadCharge = (proHoldingCost+proAdvertCost+mktgCost+basicManufactCost)*adminOverheadChargesCost;
		if (!isNaN(calcAdminOverheadCharge)) {
			document.getElementById("adminOverhead").value = number_format((calcAdminOverheadCharge/100),2,'.','');
		}

		// Total Factory Cost
		findComboProdTotalFactoryCost();
	}

	// Total Cost
	function findComboProdTotalFactoryCost()
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

		//Combo Product Profit Margin
		findComboProdProfitMargin();
	}

	//Combo Product Profit Margin
	function findComboProdProfitMargin()
	{
		var calcProfitMargin = 0;
		var totalCost	 = parseFloat(document.getElementById("totalCost").value);
		var profitMargin = parseFloat(document.getElementById("profitMargin").value);
		calcProfitMargin = totalCost*profitMargin; 
		if (!isNaN(calcProfitMargin)) {
			document.getElementById("productProfitMargin").value = number_format((calcProfitMargin/100),2,'.','');
		}

		//Actual Fact Cost
		findComboProdActualFactCost();
	}

	//Actual Fact Cost
	function findComboProdActualFactCost()
	{
		var calcActualCost = 0;
		var totalCost = parseFloat(document.getElementById("totalCost").value);
		var productProfitMargin = parseFloat(document.getElementById("productProfitMargin").value);
		calcActualCost = totalCost+productProfitMargin;
		if (!isNaN(calcActualCost)) {
			document.getElementById("actualFactCost").value = number_format(calcActualCost,2,'.','');
		}
		//Contingency
		findComboProdContingency()
	}

	//Contingency
	function findComboProdContingency()
	{
		var idealFactoryCost = 0;
		var actualFactCost = 0;
		
		if (document.getElementById("idealFactoryCost").value!="")
			idealFactoryCost = parseFloat(document.getElementById("idealFactoryCost").value);
		if (document.getElementById("actualFactCost").value!="")
			actualFactCost = parseFloat(document.getElementById("actualFactCost").value);
		// Ideal Fact cost - actual Fact cost
		calcContingency = idealFactoryCost-actualFactCost;
		if (!isNaN(calcContingency)) {
			document.getElementById("contingency").value = number_format(calcContingency,2,'.','');
		}	

		//PM in % of FC
		findComboProdPMInPercentOfFc();	
	}

	//PM in % of FC	
	function findComboProdPMInPercentOfFc()
	{
		var productProfitMargin = parseFloat(document.getElementById("productProfitMargin").value);
		var contingency 	= parseFloat(document.getElementById("contingency").value);
		var idealFactoryCost 	= parseFloat(document.getElementById("idealFactoryCost").value);

		//(PrfitMargin+Contigency)/Ideal Fact Cost
		calcPMInPercentOfFC = (productProfitMargin+contingency)/idealFactoryCost;
		if (!isNaN(calcPMInPercentOfFC) && document.getElementById("productProfitMargin").value!="") {
			document.getElementById("pmInPercentOfFc").value = number_format(Math.abs((calcPMInPercentOfFC*100)),2,'.','');
		} else {
			document.getElementById("pmInPercentOfFc").value = 0;
		}
	}


//Conversion Rate Per batch
function calcCmbMtxProductConversionRatePerBatch()
{
	var hidColumnCount = document.getElementById("hidColumnCount").value;	
   for (j=1; j<=hidColumnCount;j++) {	
	var rMCodeId	= document.getElementById("rMCodeId_"+j).value;
   if (rMCodeId!="") {
	var gravyGmsPerPouch = 0;
	var calcRatePerBatch = 0;

	var itemCount 	      = document.getElementById("hidItemCount_"+j).value;
	
	var productKgPerBatch = parseFloat(document.getElementById("productKgPerBatch_"+j).value);
	var pouchPerBatch     = parseFloat(document.getElementById("pouchPerBatch_"+j).value);
	var productGmsPerPouch = parseFloat(document.getElementById("productGmsPerPouch_"+j).value);	
	var productRatePerPouch = parseFloat(document.getElementById("productRatePerPouch_"+j).value);
	//var fishGmsPerPouch = document.getElementById("fishGmsPerPouch").value;
	var fishGmsPerPouch = parseFloat(document.getElementById("totalFixedFishQty_"+j).value);
	
	calcGravyGmsPerPouch = parseFloat(productGmsPerPouch)-parseFloat(fishGmsPerPouch);
	if (!isNaN(calcGravyGmsPerPouch)) {
		document.getElementById("gravyGmsPerPouch_"+j).value = number_format(calcGravyGmsPerPouch,3,'.','');
		gravyGmsPerPouch = parseFloat(document.getElementById("gravyGmsPerPouch_"+j).value);
	}
	
	// Assign fish Wt
	document.getElementById("fishWt_"+j).value = fishGmsPerPouch;
	document.getElementById("gravyWt_"+j).value = gravyGmsPerPouch;
	document.getElementById("netWt_"+j).value  = productGmsPerPouch;
	getComboMatrixMixProduct();

	var getIngPrice = 0;
	var getPercentagePerbatch = 0;
	fishKgPerBatch = 0;
	gravyKgPerBatch = 0;
	fishRatePerBatch=0;
	gravyRatePerBatch = 0;	
	var calcProductKgInPouchPerBatch = 0;
	var fixedKgPerBatch=0;	
	var calcFishPercentYield = 0;
	var calcGravyPercentYield = 0;	
	for (i=1; i<=itemCount; i++) {
		
		var selIngredient = document.getElementById("selIngredient_"+i+"_"+j).value;
		var fixedQtyChk = document.getElementById("fixedQtyChk_"+i+"_"+j).value;	
		//var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;		
		var quantity  = parseFloat(document.getElementById("quantity_"+i+"_"+j).value);
		var lastPrice = parseFloat(document.getElementById("lastPrice_"+i+"_"+j).value);		
		//Find Rate for each Ingredient
		getIngPrice =  quantity*lastPrice;
		if (!isNaN(getIngPrice)) {
			document.getElementById("ratePerBatch_"+i+"_"+j).value = number_format(Math.abs(getIngPrice),2,'.','');
		}
		// Find Percentage for Each Item
		getPercentagePerbatch = (quantity/productKgPerBatch);
		if (!isNaN(getPercentagePerbatch)) {
			document.getElementById("percentagePerBatch_"+i+"_"+j).value = number_format(Math.abs(getPercentagePerbatch*100),0,'','');
		}
		//Find Gms Per pouch
		getGmsPerPouch = (quantity/pouchPerBatch);
		if (!isNaN(getGmsPerPouch)) {
			document.getElementById("ingGmsPerPouch_"+i+"_"+j).value  = number_format(getGmsPerPouch,3,'.','');
		}
		//Find Percentage Wt
		getPercentageWtPerpouch = ((parseFloat(document.getElementById("ingGmsPerPouch_"+i+"_"+j).value) /productGmsPerPouch)*100);
		if (!isNaN(getPercentageWtPerpouch)) {
			document.getElementById("percentageWtPerPouch_"+i+"_"+j).value = number_format(Math.abs(getPercentageWtPerpouch),2,'.','');
		}
		//Find Rate Per Pouch
		getRatePerPouch = (parseFloat(document.getElementById("ratePerBatch_"+i+"_"+j).value)/pouchPerBatch);
		if (!isNaN(getRatePerPouch)) {
			document.getElementById("ratePerPouch_"+i+"_"+j).value = number_format(Math.abs(getRatePerPouch),2,'.','');
		}
		//Find Percentage Cost Per Pouch
		getPercentageCostPerPouch = ((parseFloat(document.getElementById("ratePerPouch_"+i+"_"+j).value)/productRatePerPouch)*100);
		if (!isNaN(getPercentageCostPerPouch)) {
			document.getElementById("percentageCostPerPouch_"+i+"_"+j).value = number_format(Math.abs(getPercentageCostPerPouch),0,'','');
		}
		//////////////////////////

		if (fixedQtyChk=='Y' && selIngredient!="") {
			//fishKgPerBatch += parseFloat(document.getElementById("quantity_"+i+"_"+j).value);
			fishKgPerBatch += parseFloat(document.getElementById("fixedQty_"+i+"_"+j).value); //Sum of FixedQty	
			fishRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i+"_"+j).value);

			fixedKgPerBatch += parseFloat(document.getElementById("quantity_"+i+"_"+j).value);
		} else if (selIngredient!="") {			
			gravyKgPerBatch += parseFloat(document.getElementById("quantity_"+i+"_"+j).value);
			gravyRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i+"_"+j).value);
		}		
	} //Loop End
		
	// Assign the values
	document.getElementById("fishGmsPerPouch_"+j).value = number_format(fishKgPerBatch,3,'.','');
	document.getElementById("totalFixedFishQty_"+j).value = number_format(fishKgPerBatch,3,'.','');

	//Kg (Raw) per Batch
	document.getElementById("fishKgPerBatch_"+j).value = number_format(fixedKgPerBatch,2,'.','');
	document.getElementById("gravyKgPerBatch_"+j).value = number_format(gravyKgPerBatch,2,'.','');
	document.getElementById("productKgPerBatch_"+j).value = number_format(( parseFloat(fixedKgPerBatch)+parseFloat(gravyKgPerBatch)),2,'.','');

	//Rs. Per Batch
	document.getElementById("fishRatePerBatch_"+j).value = number_format(fishRatePerBatch,2,'.','');
	document.getElementById("gravyRatePerBatch_"+j).value = number_format(gravyRatePerBatch,2,'.','');
	calcRatePerBatch = parseFloat(fishRatePerBatch)+parseFloat(gravyRatePerBatch);
	if (!isNaN(calcRatePerBatch)) {
		document.getElementById("productRatePerBatch_"+j).value =number_format(calcRatePerBatch,2,'.','') ;
	}

	//Rs. Per Kg per Batch
	document.getElementById("fishRatePerKgPerBatch_"+j).value = number_format(( parseFloat(document.getElementById("fishRatePerBatch_"+j).value)/parseFloat(document.getElementById("fishKgInPouchPerBatch_"+j).value)),2,'.','');
	document.getElementById("gravyRatePerKgPerBatch_"+j).value = number_format((parseFloat(document.getElementById("gravyRatePerBatch_"+j).value)/parseFloat(document.getElementById("gravyKgInPouchPerBatch_"+j).value)),2,'.','');
	document.getElementById("productRatePerKgPerBatch_"+j).value = number_format(( parseFloat(document.getElementById("fishRatePerKgPerBatch_"+j).value)+parseFloat(document.getElementById("gravyRatePerKgPerBatch_"+j).value)),2,'.','');
	
	//Rs. Per Pouch
	document.getElementById("fishRatePerPouch_"+j).value  =number_format(( parseFloat(document.getElementById("fishRatePerKgPerBatch_"+j).value) * fishGmsPerPouch),2,'.','');

	document.getElementById("gravyRatePerPouch_"+j).value  = number_format((parseFloat( document.getElementById("gravyRatePerKgPerBatch_"+j).value)*gravyGmsPerPouch),2,'.','');

	document.getElementById("productRatePerPouch_"+j).value  = number_format((parseFloat(document.getElementById("productRatePerBatch_"+j).value)/pouchPerBatch),2,'.','');

	//% (Raw) per Batch
	document.getElementById("fishRawPercentagePerPouch_"+j).value = number_format((parseFloat(document.getElementById("fishKgPerBatch_"+j).value)/parseFloat(document.getElementById("productKgPerBatch_"+j).value))*100,0,'.','');
	document.getElementById("gravyRawPercentagePerPouch_"+j).value = number_format(( parseFloat(document.getElementById("gravyKgPerBatch_"+j).value)/parseFloat(document.getElementById("productKgPerBatch_"+j).value))*100,0,'.','');
	document.getElementById("productRawPercentagePerPouch_"+j).value = parseFloat(document.getElementById("fishRawPercentagePerPouch_"+j).value) + parseFloat(document.getElementById("gravyRawPercentagePerPouch_"+j).value)
	
	// Kg (in Pouch) per Batch
	document.getElementById("fishKgInPouchPerBatch_"+j).value = number_format((parseFloat(pouchPerBatch)*parseFloat(fishGmsPerPouch)),2,'.','');
	document.getElementById("gravyKgInPouchPerBatch_"+j).value = number_format((parseFloat(pouchPerBatch)*parseFloat(gravyGmsPerPouch)),2,'.','');

	calcProductKgInPouchPerBatch = parseFloat(document.getElementById("fishKgInPouchPerBatch_"+j).value) + parseFloat(document.getElementById("gravyKgInPouchPerBatch_"+j).value);

	if (!isNaN(calcProductKgInPouchPerBatch)) {
		document.getElementById("productKgInPouchPerBatch_"+j).value = number_format(calcProductKgInPouchPerBatch,2,'.','');
	}

	//% per Pouch
	document.getElementById("fishPercentagePerPouch_"+j).value  = number_format((fishGmsPerPouch/productGmsPerPouch)*100,0,'.','');
	document.getElementById("gravyPercentagePerPouch_"+j).value = number_format((gravyGmsPerPouch/productGmsPerPouch)*100,0,'.','');
	document.getElementById("productPercentagePerPouch_"+j).value  = parseFloat(document.getElementById("fishPercentagePerPouch_"+j).value) + parseFloat(document.getElementById("gravyPercentagePerPouch_"+j).value);

	//% Yield
	calcFishPercentYield = parseFloat(document.getElementById("fishKgInPouchPerBatch_"+j).value)/parseFloat(document.getElementById("fishKgPerBatch_"+j).value);	
	if (!isNaN(calcFishPercentYield)) {
		document.getElementById("fishPercentageYield_"+j).value  = number_format((calcFishPercentYield*100),0,'.','');
	}
	calcGravyPercentYield = parseFloat(document.getElementById("gravyKgInPouchPerBatch_"+j).value)/parseFloat(document.getElementById("gravyKgPerBatch_"+j).value);
	if (!isNaN(calcGravyPercentYield)) {
		document.getElementById("gravyPercentageYield_"+j).value  = number_format((calcGravyPercentYield*100),0,'.','');
	}
   }
 }
}

function callFunCalc()
{
	calcCmbMtxProductConversionRatePerBatch();
}

/*
product define: net wt 285gm 80gm prawns 10gm fish
batch : filled net wt 300gm net wt proportion for each ingredient Qty

	Net Wt:300
	Prawns: (285-80 = 205-300 = 95)
	Fish: (285-10= 275-300 =25)
*/

/* 
	Calculate the Proportion of all ingredient when converting
*/

function comboProductConversionIngProportion()
{
	var hidColumnCount = document.getElementById("hidColumnCount").value;	
	for (j=1; j<=hidColumnCount;j++) {	
		var productGmsPerPouch    = document.getElementById("productGmsPerPouch_"+j).value;
		var hidProductGmsPerPouch = document.getElementById("hidProductGmsPerPouch_"+j).value;	
		var itemCount = document.getElementById("hidItemCount_"+j).value;

		for (i=1; i<=itemCount; i++) {
			var qty = document.getElementById("quantity_"+i+"_"+j).value;
			var hidQuantity = document.getElementById("hidQuantity_"+i+"_"+j).value;
			// Ideal Qty
			var cleanedQty	= document.getElementById("cleanedQty_"+i+"_"+j).value;
			var hidCleanedQty	= document.getElementById("hidCleanedQty_"+i+"_"+j).value;
			// Calc Raw Material Proportion value
			var calcProportionValue = parseFloat(hidProductGmsPerPouch) - parseFloat(hidQuantity) - parseFloat(productGmsPerPouch);
			// calc Ideal Kg Propo value
			var calcIdealKgPropoValue = parseFloat(hidProductGmsPerPouch) - parseFloat(hidCleanedQty) - parseFloat(productGmsPerPouch);
			//alert(Math.abs(calcProportionValue));
			// Raw Kg Proportion value
			if (!isNaN(calcProportionValue)) { 
				document.getElementById("quantity_"+i+"_"+j).value = number_format(Math.abs(calcProportionValue),2,'.','');
			}
			// Ideal Qty proportion value
			if (!isNaN(calcIdealKgPropoValue)) { 
				document.getElementById("cleanedQty_"+i+"_"+j).value = number_format(Math.abs(calcIdealKgPropoValue),2,'.','');
			}			
		}
   	}
}