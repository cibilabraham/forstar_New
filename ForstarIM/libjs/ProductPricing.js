function validateProductPricing(form)
{
	var selProduct		= form.selProduct.value;
	var selBuffer		= form.selBuffer.value;
	var mrp			= form.mrp.value;
	var productPriceRateList = form.productPriceRateList.value;
	var hidNumOfDistRecords = document.getElementById("hidNumOfDistRecords").value;

	if (selProduct=="") {
		alert("Please select a Product.");
		form.selProduct.focus();
		return false;
	}	
	
	if (selBuffer=="") {
		alert("Please select Buffer.");
		form.selBuffer.focus();
		return false;
	}

	if (mrp=="") {
		alert("Please enter an MRP.");
		form.mrp.focus();
		return false;
	}

	if (productPriceRateList=="") {
		alert("Please select a Rate list.");
		form.productPriceRateList.focus();
		return false;
	}
		
	if (hidNumOfDistRecords==0) {
		alert("No Distributor Records Found.");
		//form.productPriceRateList.focus();
		return false;
	}

	if (!confirmSave()) {
		return false;
	}
	return true;
}

	// calculate average dist margin for Each product
	function calcAvgProductMargin()
	{
		var calcProductMagn=0 ;
		var factoryCost = parseFloat(document.getElementById("factoryCost").value);
		var avgTotalDistMargin = parseFloat(document.getElementById("avgTotalDistMargin").value);
		//=factoryCost/(1-(avgDistMargin/100))
		calcProductMagn = factoryCost/(1-parseFloat(avgTotalDistMargin/100));		
		if (!isNaN(calcProductMagn)) {
			document.getElementById("avgDistMgn").value = number_format(calcProductMagn,2,'.','');
		}
	}

	// Find Margin Sscheme (MRP- Avg distMargin)
	function findMarginforScheme()
	{
		var mrp = parseFloat(document.getElementById("mrp").value);
		var avgDistMgn = parseFloat(document.getElementById("avgDistMgn").value);
		calcMarginForScheme = mrp-avgDistMgn;
		if (!isNaN(calcMarginForScheme)) {
			document.getElementById("mgnForScheme").value = number_format(calcMarginForScheme,2,'.',''); 
		}

		// No of Packs free
		noOfPacksForOneFree();
	}

	// No of Packs for One Free (=Basic Manuf Cost/Mgn For Scheme)
	function noOfPacksForOneFree()
	{
		var calcNoOfPacksFree = 0;
		var basicManufCost = parseFloat(document.getElementById("basicManufCost").value);
		var mgnForScheme   = parseFloat(document.getElementById("mgnForScheme").value);
		calcNoOfPacksFree = Math.floor(basicManufCost/mgnForScheme);
		if (!isNaN(calcNoOfPacksFree)) {
			document.getElementById("noOfPacksFree").value = number_format(calcNoOfPacksFree,0,'','');
		}
	}

	// Include Buffer
	function includeBuffer()
	{
		var calcProfitMargin = 0;	
		var selBuffer	= document.getElementById("selBuffer").value;
		//alert(selBuffer);
		var hidContigency = parseFloat(document.getElementById("hidContigency").value);

		if (selBuffer=='Y') {
			document.getElementById("inclBuffer").value = hidContigency;
		} else {
			document.getElementById("inclBuffer").value = 0;
		}

		var hidProfitMargin = parseFloat(document.getElementById("hidProfitMargin").value);
		var inclBuffer	    = parseFloat(document.getElementById("inclBuffer").value);
		calcProfitMargin = hidProfitMargin + inclBuffer;
		//alert("ProfitMgn="+hidProfitMargin+"--"+inclBuffer);
		if (!isNaN(calcProfitMargin)) {
			document.getElementById("profitMargin").value = number_format(calcProfitMargin,2,'.','');
		}

		// Caalculate Distributor wise Profit Margin
		calcDistributorProfitMargin();
	}

	// Calculate Distributor wise Profit Margin
	function calcDistributorProfitMargin()
	{
		var distributorCost = 0;
		var margin = 0;
		var totalDistMargin = 0;
		var grandTotalDistMargin = 0;
		var calcActualProfitMargin = 0;
		var calcOnMRP	= 0;
		var calcOnFactoryCost = 0;

		var mrp = parseFloat(document.getElementById("mrp").value);
		var factoryCost = parseFloat(document.getElementById("factoryCost").value); 
		var profitMargin = parseFloat(document.getElementById("profitMargin").value); 
		var selProduct	= document.getElementById("selProduct").value;
		var hidNumOfDistRecords = document.getElementById("hidNumOfDistRecords").value;
	if (selProduct!="" && hidNumOfDistRecords>0) {
		var hidDistributorRowCount = document.getElementById("hidDistributorRowCount").value;
	
	for (m=1; m<=hidDistributorRowCount; m++) {

		/*********** Dist Struct Margin  **********************************/
		var calcFirstRow = 0;
		var calcMarkUpMgn = 0;
		var calcMarkDownMgn = 0;
		var calcActualDistnCost = 0;
		
		var mrp 	= parseFloat(document.getElementById("mrp").value);
		var factoryCost = parseFloat(document.getElementById("factoryCost").value);

		var structArray = new Array();		
		var hidFieldRowCount = document.getElementById("hidFieldRowCount_"+m).value;
		//alert(hidFieldRowCount);
		var prevUseAvgDistMagn = 'Y';
		for (k=1; k<=hidFieldRowCount; k++) {
			var marginStructureId = document.getElementById("marginStructureId_"+m+"_"+k).value;
			var distMarginPercent = parseFloat(document.getElementById("distMarginPercent_"+m+"_"+k).value);
			var priceCalcType = document.getElementById("priceCalcType_"+m+"_"+k).value;	
			var useAvgDistMagn = document.getElementById("useAvgDistMagn_"+m+"_"+k).value;
	
			var schemeChk	= document.getElementById("schemeChk_"+m+"_"+k).value;

			if (prevUseAvgDistMagn!=useAvgDistMagn) {
				// Setting Cost to Distributor Or Stockist
				document.getElementById("costToDistOrStkist_"+m).value = document.getElementById("distProfitMargin_"+m+"_"+(k-1)).value;
			}			

			if (k==1 && priceCalcType=='MD') {
				calcFirstRow = mrp*(1-(distMarginPercent/100));
				if (!isNaN(calcFirstRow)) {
					document.getElementById("distProfitMargin_"+m+"_"+k).value = number_format(calcFirstRow,2,'.',''); 
				}
			}
		
			if (k>1 && useAvgDistMagn=='Y') {
				if (priceCalcType=='MU') {
					calcMarkUpMgn = ( parseFloat(document.getElementById("distProfitMargin_"+m+"_"+(k-1)).value)/(1+(distMarginPercent/100)));
					if (!isNaN(calcMarkUpMgn)) {
						document.getElementById("distProfitMargin_"+m+"_"+k).value = number_format(calcMarkUpMgn,2,'.',''); 
					}
				}
				if (priceCalcType=='MD') {
					calcMarkDownMgn = ( parseFloat(document.getElementById("distProfitMargin_"+m+"_"+(k-1)).value)*(1-(distMarginPercent/100)));
					if (!isNaN(calcMarkDownMgn)) {
						document.getElementById("distProfitMargin_"+m+"_"+k).value = number_format(calcMarkDownMgn,2,'.',''); 
					}
				}
			}		
				
			if (useAvgDistMagn=='Y') {
				structArray[marginStructureId] = document.getElementById("distProfitMargin_"+m+"_"+k).value;
			}

			prevUseAvgDistMagn=useAvgDistMagn;	// setting Previous Value
		}
		// find the Scheme Value
		var totalScheme = 0;
		for (key in structArray) {
			for (k=1; k<=hidFieldRowCount; k++) {
				var useAvgDistMagn = document.getElementById("useAvgDistMagn_"+m+"_"+k).value;
				var schemeChk	= document.getElementById("schemeChk_"+m+"_"+k).value;
				var selSchemeHeadId = document.getElementById("selSchemeHeadId_"+m+"_"+k).value;
				var distMarginPercent = parseFloat(document.getElementById("distMarginPercent_"+m+"_"+k).value);
				if (useAvgDistMagn=='N' && schemeChk=='Y') {
					//alert(key+"=>"+structArray[key]+"="+selSchemeHeadId);
					calcValue = parseFloat(structArray[key]*(distMarginPercent/100) ) 
					if (key==selSchemeHeadId) {				
						document.getElementById("distProfitMargin_"+m+"_"+k).value=number_format(calcValue,2,'.','');
						totalScheme += calcValue;
					}					
				}
			}			
		}
		// Struct Magn Cost Defining End ////////////
		var costToDistOrStkist = parseFloat(document.getElementById("costToDistOrStkist_"+m).value);

		//Act Distn Cost
		var calcActualDistnCost = costToDistOrStkist-totalScheme;
		if (!isNaN(calcActualDistnCost)) {
			document.getElementById("actualDistnCost_"+m).value = number_format(calcActualDistnCost,2,'.','');
		}

		// Octroi
		var calcOctroi = 0;
		var octroiPercent = parseFloat(document.getElementById("octroiPercent_"+m).value);
		calcOctroi = costToDistOrStkist * (octroiPercent/100);
		if (!isNaN(calcOctroi)) {
			document.getElementById("octroi_"+m).value = number_format(calcOctroi,2,'.','');
		}

		//Insurance
		var calcInsurance = 0;
		var insuranceCost = parseFloat(document.getElementById("insuranceCost_"+m).value);
		calcInsurance = costToDistOrStkist * (insuranceCost/100);
		if (!isNaN(calcInsurance)) {
			document.getElementById("insurance_"+m).value = number_format(calcInsurance,2,'.','');
		}
		
		//VAT / CST
		var calcVatOrCST = 0;
		var taxType = document.getElementById("taxType_"+m).value;
		var vatPercent = parseFloat(document.getElementById("vatPercent_"+m).value);
		var billingFormF = document.getElementById("billingFormF_"+m).value;
		var hidCstRate = parseFloat(document.getElementById("hidCstRate_"+m).value);
		/*
			FF: 0%, FC:2%, FN:4% (vat rate) not complete info
		*/
		if (taxType=='VAT') {
			calcVatOrCST = costToDistOrStkist-costToDistOrStkist/(1+(vatPercent/100));
		} else if (billingFormF=='FF') {
			calcVatOrCST = costToDistOrStkist-costToDistOrStkist/(1+(hidCstRate/100));
		} else {
			calcVatOrCST = 0;	
		}
		if (!isNaN(calcVatOrCST)) {
			document.getElementById("vatOrCst_"+m).value = number_format(calcVatOrCST,2,'.','');
		}

		// Excise 
		var calcExcise = 0;
		var productExciseRatePercent = parseFloat(document.getElementById("productExciseRatePercent_"+m).value);
		
		var vatOrCst = parseFloat(document.getElementById("vatOrCst_"+m).value);
		if (productExciseRatePercent>0) {
			calcExcise = costToDistOrStkist-vatOrCst-(costToDistOrStkist-vatOrCst)/(1+(productExciseRatePercent/100));
		} 

		if (!isNaN(calcExcise)) {
			document.getElementById("excise_"+m).value = number_format(calcExcise,2,'.','');
		}

		// EducationCess
		var calcEduactionCess = 0;
		var educationCess = parseFloat(document.getElementById("educationCess_"+m).value);
		var excise = parseFloat(document.getElementById("excise_"+m).value);
		calcEduactionCess = excise * educationCess;
		if (!isNaN(calcEduactionCess)) {
			document.getElementById("eduCess_"+m).value = number_format(calcEduactionCess,2,'.','');
		}

		// Basic Cost
		var calcBasicCost = 0;
		var actualDistnCost 	= parseFloat(document.getElementById("actualDistnCost_"+m).value);
		var octroi 		= parseFloat(document.getElementById("octroi_"+m).value);
		var freight		= parseFloat(document.getElementById("freight_"+m).value);
		var insurance 		= parseFloat(document.getElementById("insurance_"+m).value);
		var vatOrCst 		= parseFloat(document.getElementById("vatOrCst_"+m).value);
		var excise 		= parseFloat(document.getElementById("excise_"+m).value);
		var eduCess 		= parseFloat(document.getElementById("eduCess_"+m).value);
		calcBasicCost = actualDistnCost-(octroi+freight+insurance+vatOrCst+excise+eduCess);
//alert("calcBasicCost="+actualDistnCost+"-"+"("+octroi+"+"+freight+"+"+insurance+"+"+vatOrCst+"+"+excise+"+"+eduCess+")");
		if (!isNaN(calcBasicCost)) {
			document.getElementById("basicCost_"+m).value = number_format(calcBasicCost,2,'.','');
		}

		//Cost Margin
		var calcCostMargin = 0;
		var basicCost = parseFloat(document.getElementById("basicCost_"+m).value);
		calcCostMargin = basicCost-factoryCost;
		if (!isNaN(calcCostMargin)) {
			document.getElementById("costMargin_"+m).value = number_format(calcCostMargin,2,'.','');
		}

		//Actual Profit Margin
		var calcActualProfitMgn = 0;
		// factoryProfitMargin
		var factoryProfitMargin = parseFloat(document.getElementById("profitMargin").value);
		var costMargin = parseFloat(document.getElementById("costMargin_"+m).value);
		calcActualProfitMgn = costMargin+factoryProfitMargin;
		//alert(basicCost+"-"+factoryCost+">"+costMargin+"="+factoryProfitMargin);
		if (!isNaN(calcActualProfitMgn)) {
			document.getElementById("actualProfitMgn_"+m).value = number_format(calcActualProfitMgn,2,'.','');
			
			// display the actual Profit Margin
			document.getElementById("distriActualProfitMargin_"+m).value = number_format(calcActualProfitMgn,2,'.','');
			grandTotalDistMargin += calcActualProfitMgn;
		}
		
		// On MRP
		var calcOnMrpPercent = 0;
		var actualProfitMgn = parseFloat(document.getElementById("actualProfitMgn_"+m).value);
		calcOnMrpPercent = actualProfitMgn/mrp;
		if (!isNaN(calcOnMrpPercent)) {
			document.getElementById("onMrp_"+m).value = number_format(calcOnMrpPercent,2,'.','');
		}

		// On Factory Cost
		var calcOnFactoryCost = 0;
		var calcOnFactoryCost = actualProfitMgn/factoryCost;
		if (!isNaN(calcOnFactoryCost)) {
			document.getElementById("onFactoryCost_"+m).value = number_format(calcOnFactoryCost,2,'.','');
		}
		
		/********************************************/
			/*
			var avgDistributorMargin = parseFloat(document.getElementById("avgDistributorMargin_"+m).value); 
			var distriTransportCost = parseFloat(document.getElementById("distriTransportCost_"+m).value);
			
			//eg: mrp: 100 ->dist. avg: 40%
			//mrp-(mrp*40%)= 60rs-transportation Cost= dist cost-ex factory cost=margin+ factory profit margin = actual Profit Margin
			distributorCost = mrp-((mrp*avgDistributorMargin)/100)-distriTransportCost;
			if (!isNaN(distributorCost)) {
				document.getElementById("costToDistOrStkist_"+m).value = number_format(distributorCost,2,'.','');
			}
			margin = distributorCost-factoryCost;
			totalDistMargin = margin + profitMargin;
			//alert(totalDistMargin);
			grandTotalDistMargin += totalDistMargin;  // Find Product Actual Profit Margin
			if (!isNaN(totalDistMargin)) {
				document.getElementById("distriActualProfitMargin_"+m).value = number_format(totalDistMargin,2,'.','');
			}
			*/			
		}
	}	
		
	// Product Actual Profit Margin
		calcActualProfitMargin = grandTotalDistMargin/hidDistributorRowCount;
		if (!isNaN(calcActualProfitMargin)) {
			document.getElementById("actualProfitMargin").value = number_format(calcActualProfitMargin,2,'.','');
		}
		// On MRP (actualProfitMargin/mrp)
		var actualProfitMargin = parseFloat(document.getElementById("actualProfitMargin").value);
		calcOnMRP = actualProfitMargin/mrp;
		if (!isNaN(calcOnMRP)) {
			document.getElementById("onMRP").value = number_format(calcOnMRP,2,'.','');
		}

		//On Factory Cost (actualProfitMargin/factoryCost)
		calcOnFactoryCost = actualProfitMargin/factoryCost;
		if (!isNaN(calcOnFactoryCost)) {
			document.getElementById("onFactoryCost").value = number_format(calcOnFactoryCost,2,'.','');
		}		
	}