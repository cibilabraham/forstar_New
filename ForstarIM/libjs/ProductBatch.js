function validateProductBatch(form)
{
	var batchNo	= form.batchNo.value;
	var selProduct	= form.selProduct.value;
	//var productGmsPerPouch = 	form.productGmsPerPouch.value;
	//var pouchPerBatch	=	form.pouchPerBatch.value;
	var startTimeHour	=	form.startTimeHour.value;
	var startTimeMints	=	form.startTimeMints.value;
	var stopTimeHour	=	form.stopTimeHour.value;
	var stopTimeMints	=	form.stopTimeMints.value;
	var productGroupExist   =	form.productGroupExist.value;
	
	if (batchNo=="") {
		alert("Please enter a Batch Number.");
		form.batchNo.focus();
		return false;
	}
	
	if (selProduct=="") {
		alert("Please select a Product.");
		form.selProduct.focus();
		return false;
	}

	/*
	if (productGmsPerPouch=="") {
		alert("Please enter Gms per Pouch (Product).");
		form.productGmsPerPouch.focus();
		return false;
	}

	if (pouchPerBatch=="") {
		alert("Please enter Pouches Per Batch.");
		form.pouchPerBatch.focus();
		return false;
	}
	*/

	if (startTimeHour=="" || startTimeMints=="") {
		alert("Please enter Start Time.");
		//form.pouchPerBatch.focus();
		return false;
	}

	if (stopTimeHour=="" || stopTimeMints=="") {
		alert("Please enter Stop Time.");
		//form.pouchPerBatch.focus();
		return false;
	}

	if (!productBatchTimeCheck('H', 'startTimeHour') || !productBatchTimeCheck('M', 'startTimeMints') || !productBatchTimeCheck('H', 'stopTimeHour') || !productBatchTimeCheck('M', 'stopTimeMints')) {
		return false;
	}

	if (!productGroupExist) {
		var phFactorValue = form.phFactorValue.value;
		if (phFactorValue=="") {
			alert("Please enter PH Factor Value.");
			form.phFactorValue.focus();
			return false;
		}

	} else {
		var foFactorValue = form.foFactorValue.value;
		if (foFactorValue=="") {
			alert("Please enter F0 Factor Value.");
			form.foFactorValue.focus();
			return false;
		}
	}
	
	
	var fixedQty = false;
	if (selProduct!="") {
		var itemCount = document.getElementById("hidItemCount").value;

		for (i=1; i<=itemCount; i++) {

			var qty = document.getElementById("quantity_"+i);
			var fixedQtyChk = document.getElementById("fixedQtyChk_"+i);
			var existingQty = document.getElementById("existingQty_"+i).value;
			var cleanedQty = document.getElementById("cleanedQty_"+i);
			if (qty.value == "") {
				alert("Please enter Quantity.");
				qty.focus();
				return false;
			}
			if (cleanedQty.value == "") {
				alert("Please enter cleaned Quantity.");
				cleanedQty.focus();
				return false;
			}
			if (fixedQtyChk.value == 'Y') {
				fixedQty = true;
			}
			//Checking the balance qty
			var balanceQty = parseFloat(existingQty)-parseFloat(qty.value);
			if (balanceQty<=0) {
				alert("Please make sure the selected ingredient is available.");
				qty.focus();
				return false;
			}
		}
		if (fixedQty==false) {
			alert("Please select atleast one ingredient quantity as fixed.");
			return false;
		}
	}
	if (!confirmSave()) {
		return false;
	}
	return true;
}

//Time Check
function productBatchTimeCheck(mode, field)
{
	selectTime 	=	document.getElementById(field).value;
	if (mode=='H' && (selectTime>12 || selectTime<=0)) {
		alert("Hour is wrong");
		document.getElementById(field).focus();
		return false;
	}
	if (mode=='M' && (selectTime>59 || selectTime<0)){
		alert("Minute is wrong");
		document.getElementById(field).focus();
		return false;
	}
	return true;
}

/*
product define: net wt 285gm 80gm prawns 10gm fish

batch : filled net wt 300gm net wt proportion for each ingredient Qty

	Net Wt:300
	Prawns: (285-80 = 205-300 = 95)
	Fish: (285-10= 275-300 =25)
*/
// Calculate the Proportion of all ingredient
/*function calcIngProportion()
{
	var productGmsPerPouch    = document.getElementById("productGmsPerPouch").value;
	var hidProductGmsPerPouch = document.getElementById("hidProductGmsPerPouch").value;
	//alert(hidProductGmsPerPouch);
	var itemCount = document.getElementById("hidItemCount").value;

		for (i=1; i<=itemCount; i++) {

			var qty = document.getElementById("quantity_"+i).value;
			var hidQuantity = document.getElementById("hidQuantity_"+i).value;
			var calcProportionValue = parseFloat(hidProductGmsPerPouch) - parseFloat(hidQuantity) - parseFloat(productGmsPerPouch);
			//alert(Math.abs(calcProportionValue));
			if (!isNaN(calcProportionValue)) 
				document.getElementById("quantity_"+i).value = number_format(Math.abs(calcProportionValue),2,'.','');
		}
}*/

//Rate Per batch
function getProductRatePerBatch()
{
	var gravyGmsPerPouch = 0;

	var itemCount 	      = document.getElementById("hidItemCount").value;
	var productKgPerBatch = parseFloat(document.getElementById("productKgPerBatch").value);
	var pouchPerBatch     = parseFloat(document.getElementById("pouchPerBatch").value);
	var productGmsPerPouch = parseFloat(document.getElementById("productGmsPerPouch").value);	
	var productRatePerPouch = parseFloat(document.getElementById("productRatePerPouch").value);
	//var fishGmsPerPouch = document.getElementById("fishGmsPerPouch").value;
	var fishGmsPerPouch = parseFloat(document.getElementById("totalFixedFishQty").value);
	
	calcGravyGmsPerPouch = parseFloat(productGmsPerPouch)-parseFloat(fishGmsPerPouch);
	if (!isNaN(calcGravyGmsPerPouch)) {
		document.getElementById("gravyGmsPerPouch").value = number_format(calcGravyGmsPerPouch,3,'.','');
		gravyGmsPerPouch = parseFloat(document.getElementById("gravyGmsPerPouch").value);
	}


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
		//var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;				
		var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).value;				
		var quantity  = parseFloat(document.getElementById("quantity_"+i).value);
		//alert(fixedQtyChk);
		var lastPrice = parseFloat(document.getElementById("lastPrice_"+i).value);		
		//alert(quantity+"-"+lastPrice);
		//Find Rate for each Ingredient
		getIngPrice =  quantity*lastPrice;
		if (!isNaN(getIngPrice)) {
			document.getElementById("ratePerBatch_"+i).value = number_format(Math.abs(getIngPrice),2,'.','');
		}
		// Find Percentage for Each Item
		getPercentagePerbatch = (quantity/productKgPerBatch);
		if (!isNaN(getPercentagePerbatch)) {
			document.getElementById("percentagePerBatch_"+i).value = number_format(Math.abs(getPercentagePerbatch*100),0,'','');
		}
		//Find Gms Per pouch
		getGmsPerPouch = (quantity/pouchPerBatch);
		if (!isNaN(getGmsPerPouch)) {
			document.getElementById("ingGmsPerPouch_"+i).value  = number_format(getGmsPerPouch,3,'.','');
		}
		//Find Percentage Wt
		getPercentageWtPerpouch = ((parseFloat(document.getElementById("ingGmsPerPouch_"+i).value) /productGmsPerPouch)*100);
		if (!isNaN(getPercentageWtPerpouch)) {
			document.getElementById("percentageWtPerPouch_"+i).value = number_format(Math.abs(getPercentageWtPerpouch),2,'.','');
		}
		//Find Rate Per Pouch
		getRatePerPouch = (parseFloat(document.getElementById("ratePerBatch_"+i).value)/pouchPerBatch);
		if (!isNaN(getRatePerPouch)) {
			document.getElementById("ratePerPouch_"+i).value = number_format(Math.abs(getRatePerPouch),2,'.','');
		}
		//Find Percentage Cost Per Pouch
		getPercentageCostPerPouch = ((parseFloat(document.getElementById("ratePerPouch_"+i).value)/productRatePerPouch)*100);
		if (!isNaN(getPercentageCostPerPouch)) {
			document.getElementById("percentageCostPerPouch_"+i).value = number_format(Math.abs(getPercentageCostPerPouch),0,'','');
		}
		//////////////////////////

		if (fixedQtyChk=='Y') {
			//fishKgPerBatch += document.getElementById("quantity_"+i).value;
			fishKgPerBatch += parseFloat(document.getElementById("fixedQty_"+i).value); //Sum of FixedQty	
			fishRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i).value);

			fixedKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
		} else {			
			gravyKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
			gravyRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i).value);
		}		
	} //Loop End

	// Assign the values
	document.getElementById("fishGmsPerPouch").value = number_format(fishKgPerBatch,3,'.','');
	document.getElementById("totalFixedFishQty").value = number_format(fishKgPerBatch,3,'.','');

	//Kg (Raw) per Batch
	document.getElementById("fishKgPerBatch").value = number_format(fixedKgPerBatch,2,'.','');
	document.getElementById("gravyKgPerBatch").value = number_format(gravyKgPerBatch,2,'.','');
	document.getElementById("productKgPerBatch").value = number_format(( parseFloat(fixedKgPerBatch)+parseFloat(gravyKgPerBatch)),2,'.','');

	//Rs. Per Batch
	document.getElementById("fishRatePerBatch").value = number_format(fishRatePerBatch,2,'.','');
	document.getElementById("gravyRatePerBatch").value = number_format(gravyRatePerBatch,2,'.','');
	document.getElementById("productRatePerBatch").value = parseFloat(fishRatePerBatch)+parseFloat(gravyRatePerBatch);

	//Rs. Per Kg per Batch
	document.getElementById("fishRatePerKgPerBatch").value = number_format(( parseFloat(document.getElementById("fishRatePerBatch").value)/parseFloat(document.getElementById("fishKgInPouchPerBatch").value)),2,'.','');
	document.getElementById("gravyRatePerKgPerBatch").value = number_format((parseFloat(document.getElementById("gravyRatePerBatch").value)/parseFloat(document.getElementById("gravyKgInPouchPerBatch").value)),2,'.','');
	document.getElementById("productRatePerKgPerBatch").value = number_format(( parseFloat(document.getElementById("fishRatePerKgPerBatch").value)+parseFloat(document.getElementById("gravyRatePerKgPerBatch").value)),2,'.','');
	
	//Rs. Per Pouch
	document.getElementById("fishRatePerPouch").value  =number_format(( parseFloat(document.getElementById("fishRatePerKgPerBatch").value) * fishGmsPerPouch),2,'.','');

	document.getElementById("gravyRatePerPouch").value  = number_format((parseFloat( document.getElementById("gravyRatePerKgPerBatch").value)*gravyGmsPerPouch),2,'.','');

	document.getElementById("productRatePerPouch").value  = number_format((parseFloat(document.getElementById("productRatePerBatch").value)/pouchPerBatch),2,'.','');

	//% (Raw) per Batch
	document.getElementById("fishRawPercentagePerPouch").value = number_format((parseFloat(document.getElementById("fishKgPerBatch").value)/parseFloat(document.getElementById("productKgPerBatch").value))*100,0,'.','');
	document.getElementById("gravyRawPercentagePerPouch").value = number_format(( parseFloat(document.getElementById("gravyKgPerBatch").value)/parseFloat(document.getElementById("productKgPerBatch").value))*100,0,'.','');
	document.getElementById("productRawPercentagePerPouch").value = parseFloat(document.getElementById("fishRawPercentagePerPouch").value) + parseFloat(document.getElementById("gravyRawPercentagePerPouch").value)
	
	// Kg (in Pouch) per Batch
	document.getElementById("fishKgInPouchPerBatch").value = number_format((parseFloat(pouchPerBatch)*parseFloat(fishGmsPerPouch)),2,'.','');
	document.getElementById("gravyKgInPouchPerBatch").value = number_format((parseFloat(pouchPerBatch)*parseFloat(gravyGmsPerPouch)),2,'.','');

	calcProductKgInPouchPerBatch = parseFloat(document.getElementById("fishKgInPouchPerBatch").value) + parseFloat(document.getElementById("gravyKgInPouchPerBatch").value);

	if (!isNaN(calcProductKgInPouchPerBatch)) {
		document.getElementById("productKgInPouchPerBatch").value = number_format(calcProductKgInPouchPerBatch,2,'.','');
	}

	//% per Pouch
	document.getElementById("fishPercentagePerPouch").value  = number_format((fishGmsPerPouch/productGmsPerPouch)*100,0,'.','');
	document.getElementById("gravyPercentagePerPouch").value = number_format((gravyGmsPerPouch/productGmsPerPouch)*100,0,'.','');
	document.getElementById("productPercentagePerPouch").value  = parseFloat(document.getElementById("fishPercentagePerPouch").value) + parseFloat(document.getElementById("gravyPercentagePerPouch").value);

	//% Yield
	calcFishPercentYield = parseFloat(document.getElementById("fishKgInPouchPerBatch").value)/parseFloat(document.getElementById("fishKgPerBatch").value);	
	if (!isNaN(calcFishPercentYield)) {
		document.getElementById("fishPercentageYield").value  = number_format((calcFishPercentYield*100),0,'.','');
	}
	calcGravyPercentYield = parseFloat(document.getElementById("gravyKgInPouchPerBatch").value)/parseFloat(document.getElementById("gravyKgPerBatch").value);
	if (!isNaN(calcGravyPercentYield)) {
		document.getElementById("gravyPercentageYield").value  = number_format((calcGravyPercentYield*100),0,'.','');
	}

	//% Yield Edited 20-06-08
	/*
	document.getElementById("fishPercentageYield").value  = number_format((parseFloat(document.getElementById("fishKgInPouchPerBatch").value)/parseFloat(document.getElementById("fishKgPerBatch").value)),0,'.','');
	document.getElementById("gravyPercentageYield").value  = number_format(( parseFloat(document.getElementById("gravyKgInPouchPerBatch").value)/parseFloat(document.getElementById("gravyKgPerBatch").value)),0,'.','');	
	*/
}

// For Pouches Calculation
function calcProductBatchForPouch()
{
	var pouchesProductGmsPerPouch 	= parseFloat(document.getElementById("pouchesProductGmsPerPouch").value);	
	var pouchesFishGmsPerPouch 	= parseFloat(document.getElementById("pouchesFishGmsPerPouch").value);
	//Total Pouch
	var pouchesPerBatch		= document.getElementById("pouchesPerBatch").value;

	// Pouches - Kg (in Pouch) per Batch
	var pouchesFishKgInPouchPerBatch = parseFloat(document.getElementById("pouchesFishKgInPouchPerBatch").innerHTML);

	document.getElementById("pouchesFishKgInPouchPerBatch").innerHTML = number_format((pouchesPerBatch * pouchesFishGmsPerPouch),2,'.','');
	
	var pouchesFishKgInPouchPerBatch = parseFloat(document.getElementById("pouchesFishKgInPouchPerBatch").innerHTML);
	
	document.getElementById("pouchesGravyGmsPerPouch").innerHTML = number_format((pouchesProductGmsPerPouch-pouchesFishGmsPerPouch),3,'.','');

	var pouchesGravyGmsPerPouch = parseFloat(document.getElementById("pouchesGravyGmsPerPouch").innerHTML);
	
	document.getElementById("pouchesGravyKgInPouchPerBatch").innerHTML = number_format((pouchesPerBatch*pouchesGravyGmsPerPouch),2,'.','');

	var pouchesGravyKgInPouchPerBatch = parseFloat(document.getElementById("pouchesGravyKgInPouchPerBatch").innerHTML);

	calcPouchesProductKgInPouchPerBatch = parseFloat(pouchesFishKgInPouchPerBatch)+parseFloat(pouchesGravyKgInPouchPerBatch);
	if (!isNaN(calcPouchesProductKgInPouchPerBatch)) {
		document.getElementById("pouchesProductKgInPouchPerBatch").innerHTML= number_format((calcPouchesProductKgInPouchPerBatch),2,'.','');
	}


	var itemCount = document.getElementById("hidItemCount").value;
	var fishKgInPouchPerBatch = document.getElementById("fishKgInPouchPerBatch").value;
	var gravyKgInPouchPerBatch = document.getElementById("gravyKgInPouchPerBatch").value;

	var ingPouchesRatePerkg = 0;
	var ingPouchesWtPerBatch = 0;

	var pouchesFishKgPerBatch = 0;
	var pouchesGravyKgPerBatch = 0;
	var pouchesFishRatePerBatch = 0;
	var pouchesGravyRatePerBatch = 0;
	var calcPouchesFishPercentageYield = 0;
	var calcPouchesGravyPercentageYield = 0;
	for (i=1; i<=itemCount; i++) {
		var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).value;	
		//var fixedQty 		= document.getElementById("fixedQty_"+i).value;
		var quantity 		= document.getElementById("quantity_"+i).value;
		var ratePerBatch	= parseFloat(document.getElementById("ratePerBatch_"+i).value);
		
		if (fixedQtyChk=='Y') {
			document.getElementById("pouchesKgPerBatch_"+i).innerHTML = number_format(Math.abs(((quantity/fishKgInPouchPerBatch)*pouchesFishKgInPouchPerBatch)),2,'.','');
			pouchesFishKgPerBatch += parseFloat(document.getElementById("pouchesKgPerBatch_"+i).innerHTML);
			
		} else {
			document.getElementById("pouchesKgPerBatch_"+i).innerHTML = number_format(Math.abs(((quantity/gravyKgInPouchPerBatch)*pouchesGravyKgInPouchPerBatch)),2,'.','');
			pouchesGravyKgPerBatch += parseFloat(document.getElementById("pouchesKgPerBatch_"+i).innerHTML);
		}
		//alert(pouchesGravyKgPerBatch);

		var pouchesKgPerBatch = parseFloat(document.getElementById("pouchesKgPerBatch_"+i).innerHTML);

		ingPouchesRatePerkg = ((ratePerBatch/quantity)*(pouchesKgPerBatch/pouchesPerBatch));
		//alert(ingPouchesRatePerkg);
		if (!isNaN(ingPouchesRatePerkg)) {
			document.getElementById("pouchesRatePerBatch_"+i).innerHTML = number_format((ingPouchesRatePerkg),2,'.','');
		}		
		//alert(document.getElementById("pouchesRatePerBatch_"+i).innerHTML);
		//Rs. Per Batch
		if (fixedQtyChk=='Y') {
			pouchesFishRatePerBatch += parseFloat(document.getElementById("pouchesRatePerBatch_"+i).innerHTML);
		} else {
			pouchesGravyRatePerBatch += parseFloat(document.getElementById("pouchesRatePerBatch_"+i).innerHTML);
		}


		ingPouchesWtPerBatch = parseFloat(pouchesKgPerBatch/(pouchesPerBatch*pouchesProductGmsPerPouch));
		if (!isNaN(ingPouchesWtPerBatch)) {
			document.getElementById("pouchesWtPerBatch_"+i).innerHTML = number_format((ingPouchesWtPerBatch*100),2,'.','');
		}
		
	}

	//For Pouches Kg (Raw) per Batch
		document.getElementById("pouchesFishKgPerBatch").innerHTML = number_format(Math.abs(pouchesFishKgPerBatch),2,'.','');
		document.getElementById("pouchesGravyKgPerBatch").innerHTML = number_format(Math.abs(pouchesGravyKgPerBatch),2,'.','');
		calcPouchesProductKgPerbatch = eval(parseFloat(document.getElementById("pouchesFishKgPerBatch").innerHTML))+eval(parseFloat(document.getElementById("pouchesGravyKgPerBatch").innerHTML));
		if (!isNaN(calcPouchesProductKgPerbatch)) {
			document.getElementById("pouchesProductKgPerbatch").innerHTML = number_format(Math.abs(parseFloat(calcPouchesProductKgPerbatch)),2,'.','');
		}

		//For Pouches % (Raw) per Batch
		calcPouchesFishRawPercentagePerPouch = (eval(parseFloat(document.getElementById("pouchesFishKgPerBatch").innerHTML))/eval(parseFloat(document.getElementById("pouchesProductKgPerbatch").innerHTML)))*100;
		document.getElementById("pouchesFishRawPercentagePerPouch").innerHTML = number_format(Math.abs(calcPouchesFishRawPercentagePerPouch),0,'','');
		calcPouchesGravyRawPercentagePerPouch = (parseFloat(document.getElementById("pouchesGravyKgPerBatch").innerHTML)/parseFloat(document.getElementById("pouchesProductKgPerbatch").innerHTML))*100;
		document.getElementById("pouchesGravyRawPercentagePerPouch").innerHTML = number_format(Math.abs(calcPouchesGravyRawPercentagePerPouch),0,'','');

		totalPouchesProductRawPercentagePerPouch = eval( parseFloat(calcPouchesFishRawPercentagePerPouch))+eval(parseFloat(calcPouchesGravyRawPercentagePerPouch));

		if (!isNaN(totalPouchesProductRawPercentagePerPouch)) {
			document.getElementById("pouchesProductRawPercentagePerPouch").innerHTML = number_format(Math.abs(totalPouchesProductRawPercentagePerPouch),0,'','');
		}

		//Rs. Per Batch
		document.getElementById("pouchesFishRatePerBatch").innerHTML = number_format(Math.abs(parseFloat(pouchesFishRatePerBatch*pouchesPerBatch)),2,'.','');

		document.getElementById("pouchesGravyRatePerBatch").innerHTML = number_format(Math.abs(parseFloat(pouchesGravyRatePerBatch*pouchesPerBatch)),2,'.','');
		//alert(pouchesFishRatePerBatch+"*"+pouchesPerBatch);
		calcPouchesProductRatePerBatch = parseFloat(document.getElementById("pouchesFishRatePerBatch").innerHTML) + parseFloat(document.getElementById("pouchesGravyRatePerBatch").innerHTML);
		if (!isNaN(calcPouchesProductRatePerBatch)) {
			document.getElementById("pouchesProductRatePerBatch").innerHTML = number_format(Math.abs(calcPouchesProductRatePerBatch),2,'.','');
		}
		//-----------------------------
	//Rs. Per Kg per Batch
	calcPouchesFishRatePerKgPerbatch = parseFloat(document.getElementById("pouchesFishRatePerBatch").innerHTML)/parseFloat(document.getElementById("pouchesFishKgInPouchPerBatch").innerHTML);
	if (!isNaN(calcPouchesFishRatePerKgPerbatch)) {
		document.getElementById("pouchesFishRatePerKgPerbatch").innerHTML = number_format(Math.abs(calcPouchesFishRatePerKgPerbatch),2,'.','');
	}

	calcPouchesGravyRatePerKgPerbatch = parseFloat(document.getElementById("pouchesGravyRatePerBatch").innerHTML)/parseFloat(document.getElementById("pouchesGravyKgInPouchPerBatch").innerHTML);
	if (!isNaN(calcPouchesGravyRatePerKgPerbatch)) {
		document.getElementById("pouchesGravyRatePerKgPerbatch").innerHTML = number_format(Math.abs(calcPouchesGravyRatePerKgPerbatch),2,'.','');
	}

	calcPouchesProductRatePerKgPerbatch = parseFloat(document.getElementById("pouchesFishRatePerKgPerbatch").innerHTML) + parseFloat(document.getElementById("pouchesGravyRatePerKgPerbatch").innerHTML);
	if (!isNaN(calcPouchesProductRatePerKgPerbatch)) {
		document.getElementById("pouchesProductRatePerKgPerbatch").innerHTML = number_format(Math.abs(calcPouchesProductRatePerKgPerbatch),2,'.','');
	}
	
	//% per Pouch
	document.getElementById("pouchesFishPercentagePerPouch").innerHTML = number_format((((pouchesFishGmsPerPouch/pouchesProductGmsPerPouch)*100)),0,'','');

	document.getElementById("pouchesGravyPercentagePerPouch").innerHTML  =
	number_format((((pouchesGravyGmsPerPouch/pouchesProductGmsPerPouch)*100)),0,'','');

	calcPouchesProductPercentagePerPouch = parseFloat(document.getElementById("pouchesFishPercentagePerPouch").innerHTML)+parseFloat(document.getElementById("pouchesGravyPercentagePerPouch").innerHTML);
	if (!isNaN(calcPouchesProductPercentagePerPouch)) {
		document.getElementById("pouchesProductPercentagePerPouch").innerHTML  = number_format((calcPouchesProductPercentagePerPouch),0,'','');
	}

	//Rs. Per Pouch
	multiPlyPouchesFishRatePerPouch = parseFloat(document.getElementById("pouchesFishRatePerKgPerbatch").innerHTML) * pouchesFishGmsPerPouch;
	if (!isNaN(multiPlyPouchesFishRatePerPouch)) {
		document.getElementById("pouchesFishRatePerPouch").innerHTML  = number_format((multiPlyPouchesFishRatePerPouch),2,'.','');
	}
	multiPlyPouchesGravyRatePerPouch = parseFloat(document.getElementById("pouchesGravyRatePerKgPerbatch").innerHTML) * pouchesGravyGmsPerPouch;
	if (!isNaN(multiPlyPouchesGravyRatePerPouch)) {
		document.getElementById("pouchesGravyRatePerPouch").innerHTML = number_format((multiPlyPouchesGravyRatePerPouch),2,'.','');
	}
	
	calcPouchesProductRatePerPouch = parseFloat(document.getElementById("pouchesProductRatePerBatch").innerHTML)/pouchesPerBatch;

	if (!isNaN(calcPouchesProductRatePerPouch)) {
		document.getElementById("pouchesProductRatePerPouch").innerHTML = number_format((calcPouchesProductRatePerPouch),2,'.','');
	}

	//% Yield
	calcPouchesFishPercentageYield = parseFloat(document.getElementById("pouchesFishKgInPouchPerBatch").innerHTML)/parseFloat(document.getElementById("pouchesFishKgPerBatch").innerHTML);
	if (!isNaN(calcPouchesFishPercentageYield)) {
		document.getElementById("pouchesFishPercentageYield").innerHTML  = number_format((calcPouchesFishPercentageYield*100),0,'.','');
	}

	calcPouchesGravyPercentageYield = parseFloat(document.getElementById("pouchesGravyKgInPouchPerBatch").innerHTML)/parseFloat(document.getElementById("pouchesGravyKgPerBatch").innerHTML);

	if (!isNaN(calcPouchesGravyPercentageYield)) {
		document.getElementById("pouchesGravyPercentageYield").innerHTML  = number_format((calcPouchesGravyPercentageYield*100),0,'.','');
	}
}


//For Fish/ Fixed Calculation
function calcProductBatchForFish()
{
	// Gms per Pouch
	var productGmsPerPouchForFish = parseFloat(document.getElementById("productGmsPerPouchForFish").value);
	var fishGmsPerPouchForFish  = parseFloat(document.getElementById("fishGmsPerPouchForFish").value);
	calcGravyGmsPerPouchOfFish = productGmsPerPouchForFish-fishGmsPerPouchForFish;
	if (!isNaN(calcGravyGmsPerPouchOfFish)) {
		document.getElementById("gravyGmsPerPouchForFish").innerHTML = number_format((calcGravyGmsPerPouchOfFish),3,'.','');
		var gravyGmsPerPouchForFish = parseFloat(document.getElementById("gravyGmsPerPouchForFish").innerHTML);
	}
	//Kg (in Pouch) per Batch
	var fishKgInPouchPerBatchForFish = parseFloat(document.getElementById("fishKgInPouchPerBatchForFish").value);

	//Pouches per Batch
	var calcPouchPerBatchForFish = fishKgInPouchPerBatchForFish/fishGmsPerPouchForFish;
	if (!isNaN(calcPouchPerBatchForFish)) {
		document.getElementById("pouchPerBatchForFish").innerHTML = number_format((calcPouchPerBatchForFish),0,'','');
		var pouchPerBatchForFish = eval(document.getElementById("pouchPerBatchForFish").innerHTML);
	}
	//Kg (in Pouch) per Batch
	var calcGravyKgInPouchPerBatchForFish = parseFloat(document.getElementById("pouchPerBatchForFish").innerHTML)*parseFloat(document.getElementById("gravyGmsPerPouchForFish").innerHTML);
	if (!isNaN(calcGravyKgInPouchPerBatchForFish)) {
		document.getElementById("gravyKgInPouchPerBatchForFish").innerHTML = number_format((calcGravyKgInPouchPerBatchForFish),2,'.','');

		var gravyKgInPouchPerBatchForFish = parseFloat(document.getElementById("gravyKgInPouchPerBatchForFish").innerHTML);
	}
	

	var calcProductKgInPouchPerBatchForFish = fishKgInPouchPerBatchForFish + parseFloat(document.getElementById("gravyKgInPouchPerBatchForFish").innerHTML);
	if (!isNaN(calcProductKgInPouchPerBatchForFish)) {
		document.getElementById("productKgInPouchPerBatchForFish").innerHTML = number_format((calcProductKgInPouchPerBatchForFish),2,'.','');
	}

	var itemCount = document.getElementById("hidItemCount").value;
	var fishKgInPouchPerBatch = document.getElementById("fishKgInPouchPerBatch").value;
	var gravyKgInPouchPerBatch = document.getElementById("gravyKgInPouchPerBatch").value;

	var ingRatePerkgForFish = 0;
	var ingWtPerBatchForFish = 0;

	var fishKgPerBatchForFish = 0;
	var gravyKgPerBatchForFish = 0;
	var fishRatePerBatchForFish = 0;
	var gravyRatePerBatchForFish = 0;
	var calcFishPercentageYieldForFish = 0;
	var calcGravyPercentageYieldForFish = 0;

	for (i=1; i<=itemCount; i++) {
		var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).value;	
		//var fixedQty 		= document.getElementById("fixedQty_"+i).value;		
		var quantity 		= document.getElementById("quantity_"+i).value;
		var ratePerBatch	= parseFloat(document.getElementById("ratePerBatch_"+i).value);
		
		if (fixedQtyChk=='Y') {
			document.getElementById("kgPerBatchForFish_"+i).innerHTML = number_format(Math.abs(((quantity/fishKgInPouchPerBatch)*fishKgInPouchPerBatchForFish)),2,'.','');
			fishKgPerBatchForFish += parseFloat(document.getElementById("kgPerBatchForFish_"+i).innerHTML);
			
		} else {
			document.getElementById("kgPerBatchForFish_"+i).innerHTML = number_format(Math.abs(((quantity/gravyKgInPouchPerBatch)*gravyKgInPouchPerBatchForFish)),2,'.','');
			gravyKgPerBatchForFish += parseFloat(document.getElementById("kgPerBatchForFish_"+i).innerHTML);
		}

		var kgPerBatchForFish = parseFloat(document.getElementById("kgPerBatchForFish_"+i).innerHTML);

		ingRatePerkgForFish = ((ratePerBatch/quantity)*kgPerBatchForFish/pouchPerBatchForFish);
		if (!isNaN(ingRatePerkgForFish)) {
			document.getElementById("ratePerBatchForFish_"+i).innerHTML = number_format((ingRatePerkgForFish),2,'.','');
		}

		//Rs. Per Batch
		if (fixedQtyChk=='Y') {
			fishRatePerBatchForFish += parseFloat(document.getElementById("ratePerBatchForFish_"+i).innerHTML);
		} else {
			gravyRatePerBatchForFish += parseFloat(document.getElementById("ratePerBatchForFish_"+i).innerHTML);
		}

		ingWtPerBatchForFish = (kgPerBatchForFish/(pouchPerBatchForFish*productGmsPerPouchForFish));
		if (!isNaN(ingWtPerBatchForFish)) {
			document.getElementById("wtPerBatchForFish_"+i).innerHTML = number_format((ingWtPerBatchForFish*100),2,'.','');
		}
	}

	//Kg (Raw) per Batch
		if (!isNaN(fishKgPerBatchForFish)) {
			document.getElementById("fishKgPerBatchForFish").innerHTML = number_format((fishKgPerBatchForFish),2,'.','');
		}
		if (!isNaN(gravyKgPerBatchForFish)) {
			document.getElementById("gravyKgPerBatchForFish").innerHTML = number_format((gravyKgPerBatchForFish),2,'.','');
		}
	
		calcProductKgPerBatchForFish = parseFloat(document.getElementById("fishKgPerBatchForFish").innerHTML)+parseFloat(document.getElementById("gravyKgPerBatchForFish").innerHTML);
	
		if (!isNaN(calcProductKgPerBatchForFish)) {
			document.getElementById("productKgPerBatchForFish").innerHTML = number_format((calcProductKgPerBatchForFish),2,'.','');
		}

		//For Pouches % (Raw) per Batch
		calcFishRawPercentagePerBatchForFish = (parseFloat(document.getElementById("fishKgPerBatchForFish").innerHTML)/parseFloat(document.getElementById("productKgPerBatchForFish").innerHTML))*100;

		if (!isNaN(calcFishRawPercentagePerBatchForFish)) {
			document.getElementById("fishRawPercentagePerBatchForFish").innerHTML = number_format(Math.abs(calcFishRawPercentagePerBatchForFish),0,'','');
		}

		calcGravyRawPercentagePerBatchForFish = (parseFloat(document.getElementById("gravyKgPerBatchForFish").innerHTML)/parseFloat(document.getElementById("productKgPerBatchForFish").innerHTML))*100;

		if (!isNaN(calcGravyRawPercentagePerBatchForFish)) {
			document.getElementById("gravyRawPercentagePerBatchForFish").innerHTML = number_format(Math.abs(calcGravyRawPercentagePerBatchForFish),0,'','');
		}

		calcProductRawPercentagePerBatchForFish = (calcFishRawPercentagePerBatchForFish+calcGravyRawPercentagePerBatchForFish);

		if (!isNaN(calcProductRawPercentagePerBatchForFish)) {
			document.getElementById("productRawPercentagePerBatchForFish").innerHTML = number_format(Math.abs(calcProductRawPercentagePerBatchForFish),0,'','');
		}

		// Rs. Per Batch
		calcFishRatePerBatchForFish = (fishRatePerBatchForFish*pouchPerBatchForFish);
		if (!isNaN(calcFishRatePerBatchForFish)) {
			document.getElementById("fishRatePerBatchForFish").innerHTML = number_format((calcFishRatePerBatchForFish),2,'.','');
		}
		calcGravyRatePerBatchForFish = (gravyRatePerBatchForFish*pouchPerBatchForFish);
		if (!isNaN(calcGravyRatePerBatchForFish)) {
			document.getElementById("gravyRatePerBatchForFish").innerHTML = number_format((calcGravyRatePerBatchForFish),2,'.','');
		}

		calcProductRatePerBatchForFish = parseFloat(document.getElementById("fishRatePerBatchForFish").innerHTML) + parseFloat(document.getElementById("gravyRatePerBatchForFish").innerHTML);
		if (!isNaN(calcProductRatePerBatchForFish)) {
			document.getElementById("productRatePerBatchForFish").innerHTML = number_format((calcProductRatePerBatchForFish),2,'.','');
		}

	//Rs. Per Kg per Batch
	calcFishRatePerKgPerBatchForFish = (parseFloat(document.getElementById("fishRatePerBatchForFish").innerHTML)/fishKgInPouchPerBatchForFish);
	if (!isNaN(calcFishRatePerKgPerBatchForFish)) {
		document.getElementById("fishRatePerKgPerBatchForFish").innerHTML = number_format(Math.abs(calcFishRatePerKgPerBatchForFish),2,'.','');
	}

	calcGravyRatePerKgPerBatchForFish = parseFloat(document.getElementById("gravyRatePerBatchForFish").innerHTML)/parseFloat(document.getElementById("gravyKgInPouchPerBatchForFish").innerHTML);
	if (!isNaN(calcGravyRatePerKgPerBatchForFish)) {
		document.getElementById("gravyRatePerKgPerBatchForFish").innerHTML = number_format(Math.abs(calcGravyRatePerKgPerBatchForFish),2,'.','');
	}

	calcProductRatePerKgPerBatchForFish = parseFloat(document.getElementById("fishRatePerKgPerBatchForFish").innerHTML) + parseFloat(document.getElementById("gravyRatePerKgPerBatchForFish").innerHTML);
	if (!isNaN(calcProductRatePerKgPerBatchForFish)) {
		document.getElementById("productRatePerKgPerBatchForFish").innerHTML = number_format(Math.abs(calcProductRatePerKgPerBatchForFish),2,'.','');
	}
	

	//% per Pouch
	calcFishPercentagePerPouchForFish = ((fishGmsPerPouchForFish/productGmsPerPouchForFish)*100);
	if (!isNaN(calcFishPercentagePerPouchForFish)) {
		document.getElementById("fishPercentagePerPouchForFish").innerHTML = number_format(Math.abs(calcFishPercentagePerPouchForFish),0,'','');
	}

	calcGravyPercentagePerPouchForFish = ((gravyGmsPerPouchForFish/productGmsPerPouchForFish)*100);
	if (!isNaN(calcGravyPercentagePerPouchForFish)) {
		document.getElementById("gravyPercentagePerPouchForFish").innerHTML = number_format(Math.abs(calcGravyPercentagePerPouchForFish),0,'','');
	}

	calcProductPercentagePerPouchForFish = parseFloat(document.getElementById("fishPercentagePerPouchForFish").innerHTML)+parseFloat(document.getElementById("gravyPercentagePerPouchForFish").innerHTML);
	if (!isNaN(calcProductPercentagePerPouchForFish)) {
		document.getElementById("productPercentagePerPouchForFish").innerHTML  = number_format(Math.abs(calcProductPercentagePerPouchForFish),0,'','');
	}


	// Rs. Per Pouch
	multiPlyFishRatePerPouchForFish = parseFloat(document.getElementById("fishRatePerKgPerBatchForFish").innerHTML) * fishGmsPerPouchForFish;
	if (!isNaN(multiPlyFishRatePerPouchForFish)) {
		document.getElementById("fishRatePerPouchForFish").innerHTML  = number_format(Math.abs(multiPlyFishRatePerPouchForFish),2,'.','');
	}
	multiPlyGravyRatePerPouchForFish = parseFloat(document.getElementById("gravyRatePerKgPerBatchForFish").innerHTML) * gravyGmsPerPouchForFish;

	if (!isNaN(multiPlyGravyRatePerPouchForFish)) {
		document.getElementById("gravyRatePerPouchForFish").innerHTML = number_format(Math.abs(multiPlyGravyRatePerPouchForFish),2,'.','');
	}
	calcProductRatePerPouchForFish = parseFloat(document.getElementById("productRatePerBatchForFish").innerHTML)/pouchPerBatchForFish;

	if (!isNaN(calcProductRatePerPouchForFish)) {
		document.getElementById("productRatePerPouchForFish").innerHTML = number_format(Math.abs(calcProductRatePerPouchForFish),2,'.','');
	}

	//% Yield 
	calcFishPercentageYieldForFish = (fishKgInPouchPerBatchForFish/parseFloat(document.getElementById("fishKgPerBatchForFish").innerHTML));
	if (!isNaN(calcFishPercentageYieldForFish)) {
		document.getElementById("fishPercentageYieldForFish").innerHTML  = number_format((calcFishPercentageYieldForFish*100),0,'','');
	}

	calcGravyPercentageYieldForFish = ( parseFloat(document.getElementById("gravyKgInPouchPerBatchForFish").innerHTML)/parseFloat(document.getElementById("gravyKgPerBatchForFish").innerHTML));

	if (!isNaN(calcGravyPercentageYieldForFish)) {
		document.getElementById("gravyPercentageYieldForFish").innerHTML  = number_format((calcGravyPercentageYieldForFish*100),0,'','');
	}
}