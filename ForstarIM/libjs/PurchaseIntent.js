function validatePurchaseIntent(form)
{
	var itemSelected=false;
	var rowCount	=	document.getElementById("hidRowCount").value;

	for (i=1; i<=rowCount; i++) {		
		var pPlanId	=	document.getElementById("planId_"+i);
		if (pPlanId.checked) {
			itemSelected = true;
		}
	}

	if (!itemSelected) {
		alert("Please select atleast one item");
		return false;
	}

	if (!confirmContinue()) {
		return false;
	}
	return true;
}






function validateProductionPlanning(form)
{	
	var selProduct	= form.selProduct.value;
	var pDate	= form.pDate.value;
	//var productGmsPerPouch = 	form.productGmsPerPouch.value;
	var pouchPerBatch	=	form.pouchPerBatch.value;		
	
	if (pDate=="") {
		alert("Please select a date.");
		form.pDate.focus();
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
	*/

	if (pouchPerBatch=="") {
		alert("Please enter Pouches Per Batch.");
		form.pouchPerBatch.focus();
		return false;
	}		
	
	if (!plannedDateCheck()) {
		return false;
	}

	if (!confirmSave()) {
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

function ingQtyProportion()
{
	var pouchPerBatch 	= parseFloat(document.getElementById("pouchPerBatch").value);
	var hidPouchPerBatch	= parseFloat(document.getElementById("hidPouchPerBatch").value);
	var itemCount = document.getElementById("hidItemCount").value;
	var ingPerPouch		= 0;
	var calcIngQty		= 0;
	for (i=1; i<=itemCount; i++) {
		var qty = document.getElementById("quantity_"+i).value;
		var hidQuantity = document.getElementById("hidQuantity_"+i).value;
		ingPerPouch = hidQuantity/hidPouchPerBatch;
		// Calculating Pouch Per Batch Qty
		calcIngQty = ingPerPouch*pouchPerBatch;
		if (!isNaN(calcIngQty)) {
			document.getElementById("quantity_"+i).value = number_format(Math.abs(calcIngQty),2,'.','');
		}		
	}
	// Calc Ing Values
	getProductionPlanRatePerBatch();	
}

//Rate Per batch
function getProductionPlanRatePerBatch()
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

		/* Find the cleaned qty*/		
		var declYield = parseFloat(document.getElementById("declYield_"+i).value);
		//alert("Qty="+quantity+"Dec="+declYield);
		var calcCleanedQty = (quantity*declYield)/100;
		if (!isNaN(calcCleanedQty)) {
			document.getElementById("cleanedQty_"+i).value = number_format(calcCleanedQty,2,'.','');
		} else {			
			document.getElementById("cleanedQty_"+i).value = "";
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
}


function plannedDateCheck()
{
	var d = new Date();
	var t_date = d.getDate();      // Returns the day of the month
	if (t_date<10){
		t_date	=	"0"+t_date;
	}
	var t_mon = d.getMonth() + 1;      // Returns the month as a digit
	if(t_mon<10){
		t_mon	=	"0"+t_mon;
	}
	var t_year = d.getFullYear();  // Returns 4 digit year
	
	var curr_date	=	t_date + "/" + t_mon + "/" + t_year;	
	CDT		=	curr_date.split("/");
	var CD_time	=	new Date(CDT[2], CDT[1], CDT[0]);
	
	var pDate	=	document.getElementById("pDate").value;	
	LDT		=	pDate.split("/");
	var LD_time	=	new Date(LDT[2], LDT[1], LDT[0]);
	
	var one_day=1000*60*60*24
	//Calculate difference btw the two dates, and convert to days
	var extendedDays	=	Math.ceil((LD_time.getTime()-CD_time.getTime())/(one_day));	
	//alert(extendedDays);	
	if (extendedDays<0) {
		alert("Selected date should be greater than or equal to current date");
		document.getElementById("pDate").focus();
		return false;
	}
	return true;	
}