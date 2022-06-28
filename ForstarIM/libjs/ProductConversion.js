function validateProductConversionMaster(form)
{
	var productCode		=	form.productCode.value;
	var productName		=	form.productName.value;
	var selProduct		=	form.selProduct.value;

	var hidSelProductId	= 	form.hidSelProductId.value;
	
	
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

	if (selProduct=="") {
		alert("Please select a Reference product.");
		form.selProduct.focus();
		return false;
	}	
	
	if (selProduct!="") {
		var productGmsPerPouch	=	form.productGmsPerPouch.value;
		if (productGmsPerPouch=="") {
			alert("Please enter Gms Per Pouch.");
			form.productGmsPerPouch.focus();
			return false;
		}
	}

	if(!confirmSave()){
			return false;
	}
	return true;
}

//Conversion Rate Per batch
function calcProductConversionRatePerBatch()
{
	var gravyGmsPerPouch = 0;
	var calcRatePerBatch = 0;

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

	for (i=1; i<=itemCount; i++) {
		//alert("Here"+i);

		var selIngredient = document.getElementById("selIngredient_"+i).value;
		var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).value;		
		//var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;		
		var quantity  = parseFloat(document.getElementById("quantity_"+i).value);
		var lastPrice = parseFloat(document.getElementById("lastPrice_"+i).value);		
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

		if (fixedQtyChk=='Y' && selIngredient!="") {
			//fishKgPerBatch += document.getElementById("quantity_"+i).value;
			fishKgPerBatch += parseFloat(document.getElementById("fixedQty_"+i).value); //Sum of FixedQty	
			fishRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i).value);
		} else if (selIngredient!="") {			
			gravyKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
			gravyRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i).value);
		}		
	} //Loop End
	
	// Assign the values
	document.getElementById("fishGmsPerPouch").value = number_format(fishKgPerBatch,3,'.','');
	document.getElementById("totalFixedFishQty").value = number_format(fishKgPerBatch,3,'.','');

	//Kg (Raw) per Batch
	document.getElementById("fishKgPerBatch").value = number_format(fishKgPerBatch,2,'.','');
	document.getElementById("gravyKgPerBatch").value = number_format(gravyKgPerBatch,2,'.','');
	document.getElementById("productKgPerBatch").value = number_format(( parseFloat(fishKgPerBatch)+parseFloat(gravyKgPerBatch)),2,'.','');

	//Rs. Per Batch
	document.getElementById("fishRatePerBatch").value = number_format(fishRatePerBatch,2,'.','');
	document.getElementById("gravyRatePerBatch").value = number_format(gravyRatePerBatch,2,'.','');
	calcRatePerBatch = parseFloat(fishRatePerBatch)+parseFloat(gravyRatePerBatch);
	if (!isNaN(calcRatePerBatch)) {
		document.getElementById("productRatePerBatch").value =number_format(calcRatePerBatch,2,'.','') ;
	}

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
	document.getElementById("fishPercentageYield").value  = number_format((parseFloat(document.getElementById("fishKgInPouchPerBatch").value)/parseFloat(document.getElementById("fishKgPerBatch").value)),0,'.','');
	document.getElementById("gravyPercentageYield").value  = number_format(( parseFloat(document.getElementById("gravyKgInPouchPerBatch").value)/parseFloat(document.getElementById("gravyKgPerBatch").value)),0,'.','');	
}

/*
product define: net wt 285gm 80gm prawns 10gm fish
batch : filled net wt 300gm net wt proportion for each ingredient Qty

	Net Wt:300
	Prawns: (285-80 = 205-300 = 95)
	Fish: (285-10= 275-300 =25)
*/
// Calculate the Proportion of all ingredient when converting
function productConversionIngProportion()
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
}