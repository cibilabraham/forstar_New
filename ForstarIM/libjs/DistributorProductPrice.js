function validateDistributorProductPrice(form)
{
	var selDistributor	= form.selDistributor.value;
	var selProduct		= form.selProduct.value;
	

	if (selDistributor=="") {
		alert("Please select a Distributor.");
		form.selDistributor.focus();
		return false;
	}

	if (selProduct=="") {
		alert("Please select a Product.");
		form.selProduct.focus();
		return false;
	}	
		
	if (!confirmSave()) {
		return false;
	}
	return true;
}

	// Find Margin Structure Profit Margin
	function calcDistStructProfitMgn()
	{
		var selDistributor = document.getElementById("selDistributor").value;
		var selProduct = document.getElementById("selProduct").value;
		if (selDistributor!="" && selProduct!="") {

		var calcFirstRow = 0;
		var calcMarkUpMgn = 0;
		var calcMarkDownMgn = 0;
		var calcActualDistnCost = 0;
		
		var mrp 	= parseFloat(document.getElementById("mrp").value);
		var factoryCost = parseFloat(document.getElementById("factoryCost").value);

		var structArray = new Array();
		//var percentArray = new Array();
		//var val
		var hidFieldRowCount = document.getElementById("hidFieldRowCount").value;
		var prevUseAvgDistMagn = 'Y';
		for (i=1; i<=hidFieldRowCount; i++) {
			var marginStructureId = document.getElementById("marginStructureId_"+i).value;
			var distMarginPercent = parseFloat(document.getElementById("distMarginPercent_"+i).value);
			var priceCalcType = document.getElementById("priceCalcType_"+i).value;	
			var useAvgDistMagn = document.getElementById("useAvgDistMagn_"+i).value;
	
			var schemeChk	= document.getElementById("schemeChk_"+i).value;

			if (prevUseAvgDistMagn!=useAvgDistMagn) {
				// Setting Cost to Distributor Or Stockist
				document.getElementById("costToDistOrStkist").value = document.getElementById("distProfitMargin_"+(i-1)).value;
			}
	
			

			if (i==1 && priceCalcType=='MD') {
				calcFirstRow = mrp*(1-(distMarginPercent/100));
				if (!isNaN(calcFirstRow)) {
					document.getElementById("distProfitMargin_1").value = number_format(calcFirstRow,2,'.',''); 
				}
			}
		
			if (i>1 && useAvgDistMagn=='Y') {				
				if (priceCalcType=='MU') {				
					calcMarkUpMgn = ( parseFloat(document.getElementById("distProfitMargin_"+(i-1)).value)/(1+(distMarginPercent/100)));
					if (!isNaN(calcMarkUpMgn)) {
						document.getElementById("distProfitMargin_"+i).value = number_format(calcMarkUpMgn,2,'.',''); 
					}
				}
				if (priceCalcType=='MD') {
					calcMarkDownMgn = ( parseFloat(document.getElementById("distProfitMargin_"+(i-1)).value)*(1-(distMarginPercent/100)));
					if (!isNaN(calcMarkDownMgn)) {
						document.getElementById("distProfitMargin_"+i).value = number_format(calcMarkDownMgn,2,'.',''); 
					}
				}
			}

			/*} else {
				document.getElementById("distProfitMargin_"+i).value = document.getElementById("distProfitMargin_"+(i-1)).value;
				// Setting Cost to Distributor Or Stockist
				document.getElementById("costToDistOrStkist").value = document.getElementById("distProfitMargin_"+i).value;
			}*/
				
			if (useAvgDistMagn=='Y') {
				structArray[marginStructureId] = document.getElementById("distProfitMargin_"+i).value;
			}

			prevUseAvgDistMagn=useAvgDistMagn;	// setting Previous Value
		}
		// find the Scheme Value
		var totalScheme = 0;
		for (key in structArray) {
			for (i=1; i<=hidFieldRowCount; i++) {
				var useAvgDistMagn = document.getElementById("useAvgDistMagn_"+i).value;
				var schemeChk	= document.getElementById("schemeChk_"+i).value;
				var selSchemeHeadId = document.getElementById("selSchemeHeadId_"+i).value;
				var distMarginPercent = parseFloat(document.getElementById("distMarginPercent_"+i).value);
				if (useAvgDistMagn=='N' && schemeChk=='Y') {
					//alert(key+"=>"+structArray[key]+"="+selSchemeHeadId);
					calcValue = parseFloat(structArray[key]*(distMarginPercent/100) ) 
					if (key==selSchemeHeadId) {				
						document.getElementById("distProfitMargin_"+i).value=number_format(calcValue,2,'.','');
						totalScheme += calcValue;
					}
					
				}
			}			
		}
		// Struct Magn Cost Defining End ////////////
		var costToDistOrStkist = parseFloat(document.getElementById("costToDistOrStkist").value);

		//Act Distn Cost
		var calcActualDistnCost = parseFloat(document.getElementById("costToDistOrStkist").value)-totalScheme;
		if (!isNaN(calcActualDistnCost)) {
			document.getElementById("actualDistnCost").value = number_format(calcActualDistnCost,2,'.','');
		}

		// Octroi
		var calcOctroi = 0;
		var octroiPercent = parseFloat(document.getElementById("octroiPercent").value);
		calcOctroi = costToDistOrStkist * (octroiPercent/100);
		if (!isNaN(calcOctroi)) {
			document.getElementById("octroi").value = number_format(calcOctroi,2,'.','');
		}

		//Insurance
		var calcInsurance = 0;
		var insuranceCost = parseFloat(document.getElementById("insuranceCost").value);
		calcInsurance = costToDistOrStkist * (insuranceCost/100);
		if (!isNaN(calcInsurance)) {
			document.getElementById("insurance").value = number_format(calcInsurance,2,'.','');
		}
		
		//VAT / CST
		var calcVatOrCST = 0;
		var taxType = document.getElementById("taxType").value;
		var vatPercent = parseFloat(document.getElementById("vatPercent").value);
		var billingFormF = document.getElementById("billingFormF").value;
		var hidCstRate = parseFloat(document.getElementById("hidCstRate").value);
		if (taxType=='VAT') {
			calcVatOrCST = costToDistOrStkist-costToDistOrStkist/(1+(vatPercent/100));
		} else if (billingFormF=='N') {
			calcVatOrCST = costToDistOrStkist-costToDistOrStkist/(1+(hidCstRate/100));
		} else {
			calcVatOrCST = 0;	
		}
		if (!isNaN(calcVatOrCST)) {
			document.getElementById("vatOrCst").value = number_format(calcVatOrCST,2,'.','');
		}

		// Excise 
		var calcExcise = 0;
		var productExciseRatePercent = parseFloat(document.getElementById("productExciseRatePercent").value);
		
		var vatOrCst = parseFloat(document.getElementById("vatOrCst").value);
		if (productExciseRatePercent>0) {
			calcExcise = costToDistOrStkist-vatOrCst-(costToDistOrStkist-vatOrCst)/(1+(productExciseRatePercent/100));
		} 

		if (!isNaN(calcExcise)) {
			document.getElementById("excise").value = number_format(calcExcise,2,'.','');
		}

		// EducationCess
		var calcEduactionCess = 0;
		var educationCess = parseFloat(document.getElementById("educationCess").value);
		var excise = parseFloat(document.getElementById("excise").value);
		calcEduactionCess = excise * educationCess;
		if (!isNaN(calcEduactionCess)) {
			document.getElementById("eduCess").value = number_format(calcEduactionCess,2,'.','');
		}

		// Basic Cost
		var calcBasicCost = 0;
		var actualDistnCost 	= parseFloat(document.getElementById("actualDistnCost").value);
		var octroi 		= parseFloat(document.getElementById("octroi").value);
		var freight		= parseFloat(document.getElementById("freight").value);
		var insurance 		= parseFloat(document.getElementById("insurance").value);
		var vatOrCst 		= parseFloat(document.getElementById("vatOrCst").value);
		var excise 		= parseFloat(document.getElementById("excise").value);
		var eduCess 		= parseFloat(document.getElementById("eduCess").value);
		calcBasicCost = actualDistnCost-(octroi+freight+insurance+vatOrCst+excise+eduCess);
		if (!isNaN(calcBasicCost)) {
			document.getElementById("basicCost").value = number_format(calcBasicCost,2,'.','');
		}

		//Cost Margin
		var calcCostMargin = 0;
		var basicCost = parseFloat(document.getElementById("basicCost").value);
		calcCostMargin = basicCost-factoryCost;
		if (!isNaN(calcCostMargin)) {
			document.getElementById("costMargin").value = number_format(calcCostMargin,2,'.','');
		}

		//Actual Profit Margin
		var calcActualProfitMgn = 0;
		var factoryProfitMargin = parseFloat(document.getElementById("factoryProfitMargin").value);
		var costMargin = parseFloat(document.getElementById("costMargin").value);
		calcActualProfitMgn = costMargin+factoryProfitMargin;
		if (!isNaN(calcActualProfitMgn)) {
			document.getElementById("actualProfitMgn").value = number_format(calcActualProfitMgn,2,'.','');
		}
		
		// On MRP
		var calcOnMrpPercent = 0;
		var actualProfitMgn = parseFloat(document.getElementById("actualProfitMgn").value);
		calcOnMrpPercent = actualProfitMgn/mrp;
		if (!isNaN(calcOnMrpPercent)) {
			document.getElementById("onMrp").value = number_format(calcOnMrpPercent,2,'.','');
		}

		// On Factory Cost
		var calcOnFactoryCost = 0;
		var calcOnFactoryCost = actualProfitMgn/factoryCost;
		if (!isNaN(calcOnFactoryCost)) {
			document.getElementById("onFactoryCost").value = number_format(calcOnFactoryCost,2,'.','');
		}
	}
}