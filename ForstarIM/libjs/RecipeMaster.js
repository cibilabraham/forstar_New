function validateRecipeMaster(form)
{
	//alert("hii");
	var recipeCode		=	form.recipeCode.value;
	var recipeName		=	form.recipeName.value;
	var productCategory	=	form.productCategory.value;
	var recipeCategory	=	form.recipeCategory.value;
	var cusine 	=	form.cusine.value;
	var productGmsPerPouch	=	form.productGmsPerPouch.value;
	var pouchPerBatch	= 	form.pouchPerBatch.value;
	var fishGmsPerPouch	=	form.fishGmsPerPouch.value; 
	var mode		= document.getElementById("hidMode").value;
	var hidRecipeCode		= document.getElementById("hidRecipeCode").value;
	var hidPCodeExist	= 	document.getElementById("hidPCodeExist").value;
		//alert("huuii");
	
	if (recipeCode=="") {
		alert("Please enter a Recipe Code.");
		form.recipeCode.focus();
		return false;
	}

	if (hidPCodeExist!="") {
		alert("Please check the Product Code.");
		form.productCode.focus();
		return false;
	}

	if (mode==1) {
		var selRecipe = document.getElementById("selRecipe").value;
		if (selRecipe!="") {
			if (hidRecipeCode==recipeCode) {
				alert("Please modifiy the Recipe Code. ");
				form.recipeCode.focus();
				return false;
			}
		}
	}

	if (recipeName=="") {
		alert("Please enter a Recipe Name.");
		form.recipeName.focus();
		return false;
	}

	if (productCategory=="") {
		alert("Please select a Product category.");
		form.productCategory.focus();
		return false;
	}

	if (recipeCategory=="") {
		alert("Please select a Recipe Category.");
		form.recipeCategory.focus();
		return false;
	}

	/*
	if (productStateGroup!="") {
		var productGroup = form.productGroup.value;

		if (productGroup=="") {
			alert("Please select a Product Group.");
			form.productGroup.focus();
			return false;
		}
	}
	*/

	if (productGmsPerPouch=="") {
		alert("Please enter Qty Per Pouch.");
		form.productGmsPerPouch.focus();
		return false;
	}

	if (pouchPerBatch=="") {
		alert("Please enter Pouches per Batch.");
		form.pouchPerBatch.focus();
		return false;
	}

	var itemCount	=	document.getElementById("hidTableRowCount").value;
	var stockSelected = false;

	for (i=0; i<itemCount; i++) {
	    var status = document.getElementById("status_"+i).value;		    
	    if( status!='N')
	    {
		var selStock	= document.getElementById("selIngredient_"+i);
		var quantity	= document.getElementById("quantity_"+i);
		var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked; 
		
		if (selStock!="") {
			if (selStock.value == "") {
				alert("Please select an Ingredient.");
				selStock.focus();
				return false;
			}

			if (quantity.value == "") {
				alert("Please enter Raw quantity.");
				quantity.focus();
				return false;
			}

			
			stockSelected = true;
		}
            }
	}

	if (stockSelected==false) {
		alert("Please add one or more Ingredient");
		return false;
	}

	if (!validateProductOfIngredientRepeat()) {
		return false;
	}
	//alert(fishGmsPerPouch+">"+productGmsPerPouch);
	if (fishGmsPerPouch>=productGmsPerPouch) {
		alert("Please check fixed Qty Per Pouch");
		return false;
	}

	if(!confirmSave()){
			return false;
	}
	return true;
}

//Add a New Line 
function productMasterNewLine() 
{
	document.frmProductMaster.newline.value = '1';
	document.frmProductMaster.submit();
}


//Validate repeated
function validateProductOfIngredientRepeat()
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
	
    var rc = document.getElementById("hidTableRowCount").value;
    var prevOrder = 0;
    var arr = new Array();
    var arri=0;

    for( j=0; j<rc; j++ )    {
      var status = document.getElementById("status_"+j).value;
      if (status!='N')
      {
        var rv = document.getElementById("selIngredient_"+j).value;
        if ( arr.indexOf(rv) != -1 )    {
            alert("Ingredient cannot be duplicate.");
            document.getElementById("selIngredient_"+j).focus();
            return false;
        }
        arr[arri++]=rv;
      }
    }
    return true;
}


//Rate Per batch

function calcProductRatePerBatch()
{
	var productGmsPerPouch = document.getElementById("productGmsPerPouch").value;	
	var fishGmsPerPouch= document.getElementById("fishGmsPerPouch").value;
	if(fishGmsPerPouch!="" && productGmsPerPouch!="")
	{
		var gravyGmsPerPouch=parseFloat(productGmsPerPouch)-parseFloat(fishGmsPerPouch);
		document.getElementById("gravyGmsPerPouch").value=gravyGmsPerPouch;
		
		
		var fishPercentagePerPouch=(parseFloat(fishGmsPerPouch)/parseFloat(productGmsPerPouch))*100;
		document.getElementById("fishPercentagePerPouch").value=Math.round(fishPercentagePerPouch);
		//alert(fishGmsPerPouch+"--"+productGmsPerPouch);
		var gravyPercentagePerPouch=(parseFloat(gravyGmsPerPouch)/parseFloat(productGmsPerPouch))*100;
		document.getElementById("gravyPercentagePerPouch").value=Math.round(gravyPercentagePerPouch);
		
		var productPercentagePerPouch=parseFloat(gravyPercentagePerPouch)+parseFloat(fishPercentagePerPouch);
		document.getElementById("productPercentagePerPouch").value=Math.round(productPercentagePerPouch);
	}

	var pouchPerBatch= document.getElementById("pouchPerBatch").value;	
	if(pouchPerBatch!="")
	{
		if(fishGmsPerPouch!="")
		{
			var fishKgInPouchPerBatch=pouchPerBatch*fishGmsPerPouch;
			document.getElementById("fishKgInPouchPerBatch").value=Math.round(fishKgInPouchPerBatch);
		}
		if(gravyKgInPouchPerBatch!="")
		{
			var gravyKgInPouchPerBatch=pouchPerBatch*gravyGmsPerPouch;
			document.getElementById("gravyKgInPouchPerBatch").value=Math.round(gravyKgInPouchPerBatch);
		}

		if(fishGmsPerPouch!="" && gravyKgInPouchPerBatch!="")
		{
			var productKgInPouchPerBatch=parseFloat(fishKgInPouchPerBatch)+parseFloat(gravyKgInPouchPerBatch);
			document.getElementById("productKgInPouchPerBatch").value=Math.round(productKgInPouchPerBatch);
		}
	}
	
	var itemCount 	      = document.getElementById("hidTableRowCount").value;	
	var fishRatePerKg=0; var gravyRatePerKg=0; var fishRawPercentagePerPouch=0; var gravyRawPercentagePerPouch=0; var productKgPerBatch=0;
	for (i=0; i<itemCount; i++) 
	{
	    var status = document.getElementById("status_"+i).value;
	    if (status!='N')
	    {
			var selIngredient = document.getElementById("selIngredient_"+i).value;
			var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;		
			var quantity  = document.getElementById("quantity_"+i).value;
			if (fixedQtyChk!="" && selIngredient!="" && quantity!="")
			{
				fishRatePerKg	+= parseFloat(quantity);
				
			}
			else if(selIngredient!="" && quantity!="" && fixedQtyChk=="") 
			{	
				gravyRatePerKg	+= parseFloat(quantity);
			}
		}
	}

	//var fishKgPerBatch=document.getElementById("quantity_0").value;
	if(itemCount==i)
	{
		document.getElementById("fishKgPerBatch").value=Math.round(fishRatePerKg);
		document.getElementById("gravyKgPerBatch").value=Math.round(gravyRatePerKg);
		var productKgPerBatch=parseFloat(fishRatePerKg)+parseFloat(gravyRatePerKg);
		document.getElementById("productKgPerBatch").value=Math.round(productKgPerBatch);
		//alert(fishRatePerKg+"------"+productKgPerBatch);
		if(fishRatePerKg!="")
		{
			var fishRawPercentagePerPouch=(fishRatePerKg/productKgPerBatch)*100;
			document.getElementById("fishRawPercentagePerPouch").value=Math.round(fishRawPercentagePerPouch);
		}

		if(gravyRatePerKg!="")
		{
			var gravyRawPercentagePerPouch=(gravyRatePerKg/productKgPerBatch)*100;
			document.getElementById("gravyRawPercentagePerPouch").value=Math.round(gravyRawPercentagePerPouch);
		}

		if(fishRawPercentagePerPouch!="" &&  gravyRawPercentagePerPouch!="")
		{
			var productRawPercentagePerPouch=parseFloat(fishRawPercentagePerPouch)+parseFloat(gravyRawPercentagePerPouch);
			document.getElementById("productRawPercentagePerPouch").value=Math.round(productRawPercentagePerPouch);
		}

		//alert(fishKgInPouchPerBatch+"----"+gravyKgInPouchPerBatch);
		if(fishRatePerKg!=0)
		{
		var fishPercentageYield=(fishKgInPouchPerBatch/fishRatePerKg)*100;
		document.getElementById("fishPercentageYield").value=Math.round(fishPercentageYield);
		}
		if(gravyRatePerKg!=0)
		{
		var gravyPercentageYield=(gravyKgInPouchPerBatch/gravyRatePerKg)*100;
		document.getElementById("gravyPercentageYield").value=Math.round(gravyPercentageYield);
		}


		
	}

	var  rsfishPerBatch=0; var  rsGravyPerBatch=0;
	for (i=0; i<itemCount; i++) 
	{
	    var status = document.getElementById("status_"+i).value;
	    if (status!='N')
	    {
			var selIngredient = document.getElementById("selIngredient_"+i).value;
			var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;		
			var quantity  = document.getElementById("quantity_"+i).value;
			var percentagePerBatch=(quantity/productKgPerBatch)*100;
			document.getElementById("percentagePerBatch_"+i).value=Math.round(percentagePerBatch);
			var rsperKg=document.getElementById("rsperKg_"+i).value;	
			var ratePerKg=rsperKg*quantity;
			document.getElementById("ratePerKg_"+i).value= Math.round(ratePerKg);

			var ingGmsPerPouch=quantity/pouchPerBatch;
			document.getElementById("ingGmsPerPouch_"+i).value= number_format(ingGmsPerPouch,3, ".", "");
		
			var ratePerPouch=ratePerKg/pouchPerBatch;
			document.getElementById("ratePerPouch_"+i).value=number_format(ratePerPouch,2, ".", ""); 
			
			var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;		
			if (fixedQtyChk!="" && selIngredient!="" && quantity!="")
			{
				rsfishPerBatch	+= parseFloat(ratePerKg);
				
			}
			else if(selIngredient!="" && quantity!="" && fixedQtyChk=="") 
			{	
				rsGravyPerBatch	+= parseFloat(ratePerKg);
			}
		}
	}
	var fishRatePerKgPerBatch=0; var gravyRatePerKgPerBatch=0; 
	if(itemCount==i)
	{
		document.getElementById("fishRatePerBatch").value=Math.round(rsfishPerBatch);
		document.getElementById("gravyRatePerBatch").value=Math.round(rsGravyPerBatch);
		var productRatePerBatch=parseFloat(rsfishPerBatch)+parseFloat(rsGravyPerBatch);
		document.getElementById("productRatePerBatch").value=Math.round(productRatePerBatch);
		if(rsfishPerBatch!="" && fishKgInPouchPerBatch!="")
		{
			var fishRatePerKgPerBatch=rsfishPerBatch/fishKgInPouchPerBatch;
			document.getElementById("fishRatePerKgPerBatch").value=Math.round(fishRatePerKgPerBatch);
		}
		if(rsGravyPerBatch!="" && gravyKgInPouchPerBatch!="")
		{
			var gravyRatePerKgPerBatch=rsGravyPerBatch/gravyKgInPouchPerBatch;
			document.getElementById("gravyRatePerKgPerBatch").value=Math.round(gravyRatePerKgPerBatch);
		}
		//alert(fishRatePerKgPerBatch+"----"+gravyRatePerKgPerBatch);
		if(fishRatePerKgPerBatch!="" && gravyRatePerKgPerBatch!="" )
		{
			var productRatePerKgPerBatch=parseFloat(fishRatePerKgPerBatch)+parseFloat(gravyRatePerKgPerBatch);
			document.getElementById("productRatePerKgPerBatch").value=Math.round(productRatePerKgPerBatch);
		}
		var fishRatePerPouch=fishRatePerKgPerBatch*fishGmsPerPouch;
		document.getElementById("fishRatePerPouch").value=number_format(fishRatePerPouch,2, ".", ""); 

		var gravyRatePerPouch=gravyRatePerKgPerBatch*gravyGmsPerPouch;
		document.getElementById("gravyRatePerPouch").value=number_format(gravyRatePerPouch,2, ".", ""); 
		
		var productRatePerPouch=productRatePerBatch/pouchPerBatch;
		document.getElementById("productRatePerPouch").value=number_format(productRatePerPouch,2, ".", ""); 
		

	}

	 for (i=0; i<itemCount; i++) 
	{
	    var status = document.getElementById("status_"+i).value;
	    if (status!='N')
	    {
			var quantity  = document.getElementById("quantity_"+i).value;
			var rsperKg=document.getElementById("rsperKg_"+i).value;	
			var ratePerKg=rsperKg*quantity;

			var ratePerPouch=ratePerKg/pouchPerBatch;
			document.getElementById("ratePerPouch_"+i).value=number_format(ratePerPouch,2, ".", ""); 
			//alert(ratePerPouch+"---"+productRatePerPouch+"--"+percentageCostPerPouch);
			var percentageCostPerPouch=(ratePerPouch/productRatePerPouch)*100;
			document.getElementById("percentageCostPerPouch_"+i).value=Math.round(percentageCostPerPouch);
		}
	}

}

// commented 4-8-2015
/*function calcProductRatePerBatch()
{


	var gravyGmsPerPouch = 0;
	var itemCount 	      = document.getElementById("hidTableRowCount").value;	
	var productKgPerBatch = parseFloat(document.getElementById("productKgPerBatch").value);
	var pouchPerBatch     = parseFloat(document.getElementById("pouchPerBatch").value);
	var productGmsPerPouch = parseFloat(document.getElementById("productGmsPerPouch").value);	
	var productRatePerPouch = parseFloat(document.getElementById("productRatePerPouch").value);
	//var fishGmsPerPouch = document.getElementById("fishGmsPerPouch").value;
	var fishGmsPerPouch = parseFloat(document.getElementById("totalFixedFishQty").value);	
	calcGravyGmsPerPouch = parseFloat(document.getElementById("productGmsPerPouch").value)-parseFloat(fishGmsPerPouch);
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

	var fishRatePerKg 	= 0;
	var gravyRatePerKg 	= 0;

	for (i=0; i<itemCount; i++) {
		
	    var status = document.getElementById("status_"+i).value;
	    if (status!='N')
	    {
			var selIngredient = document.getElementById("selIngredient_"+i).value;
			var fixedQtyChk =document.getElementById("fixedQtyChk_"+i).checked;
			var percentagePerBatch = document.getElementById("quantity_"+i).value;
			if(percentagePerBatch!="")
			{
				document.getElementById("percentagePerBatch_"+i).value=Math.round(percentagePerBatch);
			}
			var quantity=document.getElementById("quantity_"+i).value;
			var rsperKg = document.getElementById("rsperKg_"+i).value;
			if(rsperKg!="" && quantity!="")
			{
				var ratePerKg = parseFloat(rsperKg)*parseFloat(quantity);
				document.getElementById("ratePerKg_"+i).value=number_format(ratePerKg,'2','.',' ');
			}
			var pouchPerBatch = document.getElementById("pouchPerBatch").value;
			//alert(quantity+"--"+pouchPerBatch);
			if(quantity!="" && pouchPerBatch!="")
			{
				var ingGmsPerPouch=parseFloat(quantity)/parseFloat(pouchPerBatch);
				document.getElementById("ingGmsPerPouch_"+i).value=number_format(ingGmsPerPouch,'2','.',' ');
			}
			if(ratePerKg!="" && pouchPerBatch!="")
			{
				var ratePerPouch=parseFloat(ratePerKg)/parseFloat(pouchPerBatch);
				document.getElementById('ratePerPouch_'+i).value=number_format(ratePerPouch,'2','.',' ');	 
				document.getElementById('percentageCostPerPouch_'+i).value=Math.round(ratePerPouch);
			}
		//}
		//////////////////////////		
		if (fixedQtyChk!="" && selIngredient!="") {
			//fishKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
			fishKgPerBatch += parseFloat(document.getElementById("fixedQty_"+i).value); //Sum of FixedQty	
			//fishRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i).value);
				
			fishRatePerKg	+= parseFloat(ratePerKg);

			fixedKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
		} else if (selIngredient!="") {			
			gravyKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
			//gravyRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i).value);

			gravyRatePerKg	+= parseFloat(ratePerKg);
		}

	// Find the cleaned qty	
		
         } // Status Check ends Here
		
	} //Loop End

	// Assign the values (Fixed Qty)
	document.getElementById("fishGmsPerPouch").value = number_format(fishKgPerBatch,3,'.','');
	document.getElementById("totalFixedFishQty").value = number_format(fishKgPerBatch,3,'.','');

	//Kg (Raw) per Batch
	document.getElementById("fishKgPerBatch").value = number_format(fixedKgPerBatch,2,'.','');
	document.getElementById("gravyKgPerBatch").value = number_format(gravyKgPerBatch,2,'.','');
	document.getElementById("productKgPerBatch").value = number_format(( parseFloat(fixedKgPerBatch)+parseFloat(gravyKgPerBatch)),2,'.','');

	//Rs. Per Kg
	document.getElementById("fishRatePerKg").value = number_format(fishRatePerKg,2,'.','');
	document.getElementById("gravyRatePerKg").value = number_format(gravyRatePerKg,2,'.','');
	document.getElementById("productRatePerKg").value = parseFloat(fishRatePerKg)+parseFloat(gravyRatePerKg);

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

	document.getElementById("productRatePerPouch").value  = number_format((parseFloat(document.getElementById("productRatePerBatch").value)/document.getElementById("pouchPerBatch").value),2,'.','');

	//% (Raw) per Batch
	document.getElementById("fishRawPercentagePerPouch").value = number_format((parseFloat(document.getElementById("fishKgPerBatch").value)/parseFloat(document.getElementById("productKgPerBatch").value))*100,0,'.','');
	document.getElementById("gravyRawPercentagePerPouch").value = number_format(( parseFloat(document.getElementById("gravyKgPerBatch").value)/parseFloat(document.getElementById("productKgPerBatch").value))*100,0,'.','');
	document.getElementById("productRawPercentagePerPouch").value = parseFloat(document.getElementById("fishRawPercentagePerPouch").value) + parseFloat(document.getElementById("gravyRawPercentagePerPouch").value)
	
	// Kg (in Pouch) per Batch
	document.getElementById("fishKgInPouchPerBatch").value = number_format((parseFloat(document.getElementById("pouchPerBatch").value)*parseFloat(fishGmsPerPouch)),2,'.','');
	document.getElementById("gravyKgInPouchPerBatch").value = number_format((parseFloat(document.getElementById("pouchPerBatch").value)*parseFloat(gravyGmsPerPouch)),2,'.','');

	calcProductKgInPouchPerBatch = parseFloat(document.getElementById("fishKgInPouchPerBatch").value) + parseFloat(document.getElementById("gravyKgInPouchPerBatch").value);

	if (!isNaN(calcProductKgInPouchPerBatch)) {
		document.getElementById("productKgInPouchPerBatch").value = number_format(calcProductKgInPouchPerBatch,2,'.','');
	}

	//% per Pouch
	document.getElementById("fishPercentagePerPouch").value  = number_format((fishGmsPerPouch/document.getElementById("productGmsPerPouch").value)*100,0,'.','');
	document.getElementById("gravyPercentagePerPouch").value = number_format((gravyGmsPerPouch/document.getElementById("productGmsPerPouch").value)*100,0,'.','');
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

		// Recalculate
	reCalcProductRatePerBatch();	
}
*/

/*
//Rate Per batch
function calcProductRatePerBatch()
{
	var gravyGmsPerPouch = 0;
	var itemCount 	      = document.getElementById("hidTableRowCount").value;	
	var productKgPerBatch = parseFloat(document.getElementById("productKgPerBatch").value);
	var pouchPerBatch     = parseFloat(document.getElementById("pouchPerBatch").value);
	var productGmsPerPouch = parseFloat(document.getElementById("productGmsPerPouch").value);	
	var productRatePerPouch = parseFloat(document.getElementById("productRatePerPouch").value);
	//var fishGmsPerPouch = document.getElementById("fishGmsPerPouch").value;
	var fishGmsPerPouch = parseFloat(document.getElementById("totalFixedFishQty").value);	
	calcGravyGmsPerPouch = parseFloat(document.getElementById("productGmsPerPouch").value)-parseFloat(fishGmsPerPouch);
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

	var fishRatePerKg 	= 0;
	var gravyRatePerKg 	= 0;

	for (i=0; i<itemCount; i++) {
	    var status = document.getElementById("status_"+i).value;
	    if (status!='N')
	    {
		var selIngredient = document.getElementById("selIngredient_"+i).value;
		var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;		
		var quantity  = parseFloat(document.getElementById("quantity_"+i).value);
		var lastPrice  = parseFloat(document.getElementById("lastPrice_"+i).value);
		var ratePerKg	= parseFloat(document.getElementById("ratePerKg_"+i).value);
		//Find Rate for each Ingredient
		getIngPrice =  quantity*lastPrice;
		if (!isNaN(getIngPrice)) {
			document.getElementById("ratePerBatch_"+i).value = number_format(Math.abs(getIngPrice),2,'.','');
		}

		// Find Percentage for Each Item
		getPercentagePerbatch = parseFloat(quantity/document.getElementById("productKgPerBatch").value);	
		if (!isNaN(getPercentagePerbatch)) {
			document.getElementById("percentagePerBatch_"+i).value = number_format(Math.abs(getPercentagePerbatch*100),2,'.','');
		}
		
		//Find Gms Per pouch
		getGmsPerPouch = (quantity/document.getElementById("pouchPerBatch").value);
		if (!isNaN(getGmsPerPouch)) {
			document.getElementById("ingGmsPerPouch_"+i).value  = number_format(getGmsPerPouch,3,'.','');
		}
		//Find Percentage Wt
		getPercentageWtPerpouch = ((parseFloat(document.getElementById("ingGmsPerPouch_"+i).value) /document.getElementById("productGmsPerPouch").value)*100);
		if (!isNaN(getPercentageWtPerpouch)) {
			document.getElementById("percentageWtPerPouch_"+i).value = number_format(Math.abs(getPercentageWtPerpouch),2,'.','');
		}
		//Find Rate Per Pouch
		getRatePerPouch = (parseFloat(document.getElementById("ratePerBatch_"+i).value)/document.getElementById("pouchPerBatch").value);
		if (!isNaN(getRatePerPouch)) {
			document.getElementById("ratePerPouch_"+i).value = number_format(Math.abs(getRatePerPouch),2,'.','');
		}
		//Find Percentage Cost Per Pouch
		getPercentageCostPerPouch = ((parseFloat(document.getElementById("ratePerPouch_"+i).value)/document.getElementById("productRatePerPouch").value)*100);
		if (!isNaN(getPercentageCostPerPouch)) {
			document.getElementById("percentageCostPerPouch_"+i).value = number_format(Math.abs(getPercentageCostPerPouch),2,'.','');
		}
		//////////////////////////		
		if (fixedQtyChk!="" && selIngredient!="") {
			//fishKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
			fishKgPerBatch += parseFloat(document.getElementById("fixedQty_"+i).value); //Sum of FixedQty	
			fishRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i).value);
				
			fishRatePerKg	+= parseFloat(ratePerKg);

			fixedKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
		} else if (selIngredient!="") {			
			gravyKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
			gravyRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i).value);

			gravyRatePerKg	+= parseFloat(ratePerKg);
		}

	// Find the cleaned qty	
		var declYield = parseFloat(document.getElementById("declYield_"+i).value);
		var calcCleanedQty = (quantity*declYield)/100;
		if (!isNaN(calcCleanedQty)) {
			document.getElementById("cleanedQty_"+i).value = number_format(calcCleanedQty,2,'.','');
		} else {
			document.getElementById("cleanedQty_"+i).value = "";
		}
         } // Status Check ends Here
		
	} //Loop End

	// Assign the values (Fixed Qty)
	document.getElementById("fishGmsPerPouch").value = number_format(fishKgPerBatch,3,'.','');
	document.getElementById("totalFixedFishQty").value = number_format(fishKgPerBatch,3,'.','');

	//Kg (Raw) per Batch
	document.getElementById("fishKgPerBatch").value = number_format(fixedKgPerBatch,2,'.','');
	document.getElementById("gravyKgPerBatch").value = number_format(gravyKgPerBatch,2,'.','');
	document.getElementById("productKgPerBatch").value = number_format(( parseFloat(fixedKgPerBatch)+parseFloat(gravyKgPerBatch)),2,'.','');

	//Rs. Per Kg
	document.getElementById("fishRatePerKg").value = number_format(fishRatePerKg,2,'.','');
	document.getElementById("gravyRatePerKg").value = number_format(gravyRatePerKg,2,'.','');
	document.getElementById("productRatePerKg").value = parseFloat(fishRatePerKg)+parseFloat(gravyRatePerKg);

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

	document.getElementById("productRatePerPouch").value  = number_format((parseFloat(document.getElementById("productRatePerBatch").value)/document.getElementById("pouchPerBatch").value),2,'.','');

	//% (Raw) per Batch
	document.getElementById("fishRawPercentagePerPouch").value = number_format((parseFloat(document.getElementById("fishKgPerBatch").value)/parseFloat(document.getElementById("productKgPerBatch").value))*100,0,'.','');
	document.getElementById("gravyRawPercentagePerPouch").value = number_format(( parseFloat(document.getElementById("gravyKgPerBatch").value)/parseFloat(document.getElementById("productKgPerBatch").value))*100,0,'.','');
	document.getElementById("productRawPercentagePerPouch").value = parseFloat(document.getElementById("fishRawPercentagePerPouch").value) + parseFloat(document.getElementById("gravyRawPercentagePerPouch").value)
	
	// Kg (in Pouch) per Batch
	document.getElementById("fishKgInPouchPerBatch").value = number_format((parseFloat(document.getElementById("pouchPerBatch").value)*parseFloat(fishGmsPerPouch)),2,'.','');
	document.getElementById("gravyKgInPouchPerBatch").value = number_format((parseFloat(document.getElementById("pouchPerBatch").value)*parseFloat(gravyGmsPerPouch)),2,'.','');

	calcProductKgInPouchPerBatch = parseFloat(document.getElementById("fishKgInPouchPerBatch").value) + parseFloat(document.getElementById("gravyKgInPouchPerBatch").value);

	if (!isNaN(calcProductKgInPouchPerBatch)) {
		document.getElementById("productKgInPouchPerBatch").value = number_format(calcProductKgInPouchPerBatch,2,'.','');
	}

	//% per Pouch
	document.getElementById("fishPercentagePerPouch").value  = number_format((fishGmsPerPouch/document.getElementById("productGmsPerPouch").value)*100,0,'.','');
	document.getElementById("gravyPercentagePerPouch").value = number_format((gravyGmsPerPouch/document.getElementById("productGmsPerPouch").value)*100,0,'.','');
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
	
	// Recalculate
	reCalcProductRatePerBatch();	
}
*/

function reCalcProductRatePerBatch()
{
	var gravyGmsPerPouch = 0;
	var itemCount 	      = document.getElementById("hidTableRowCount").value;	
	var productKgPerBatch = parseFloat(document.getElementById("productKgPerBatch").value);
	var pouchPerBatch     = parseFloat(document.getElementById("pouchPerBatch").value);
	var productGmsPerPouch = parseFloat(document.getElementById("productGmsPerPouch").value);	
	var productRatePerPouch = parseFloat(document.getElementById("productRatePerPouch").value);
	//var fishGmsPerPouch = document.getElementById("fishGmsPerPouch").value;
	var fishGmsPerPouch = parseFloat(document.getElementById("totalFixedFishQty").value);	
	calcGravyGmsPerPouch = parseFloat(document.getElementById("productGmsPerPouch").value)-parseFloat(fishGmsPerPouch);
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

	var fishRatePerKg 	= 0;
	var gravyRatePerKg 	= 0;

	for (i=0; i<itemCount; i++) {
	    var status = document.getElementById("status_"+i).value;
	    if (status!='N')
	    {
			var selIngredient = document.getElementById("selIngredient_"+i).value;
			var fixedQtyChk =document.getElementById("fixedQtyChk_"+i).checked;
			var percentagePerBatch = document.getElementById("quantity_"+i).value;
			if(percentagePerBatch!="")
			{
				document.getElementById("percentagePerBatch_"+i).value=Math.round(percentagePerBatch);
			}
			var quantity=document.getElementById("quantity_"+i).value;
			var rsperKg = document.getElementById("rsperKg_"+i).value;
			if(rsperKg!="" && quantity!="")
			{
				var ratePerKg = parseFloat(rsperKg)*parseFloat(quantity);
				document.getElementById("ratePerKg_"+i).value=number_format(ratePerKg,'2','.',' ');
			}
			var pouchPerBatch = document.getElementById("pouchPerBatch").value;
			//alert(quantity+"--"+pouchPerBatch);
			if(quantity!="" && pouchPerBatch!="")
			{
				var ingGmsPerPouch=parseFloat(quantity)/parseFloat(pouchPerBatch);
				document.getElementById("ingGmsPerPouch_"+i).value=number_format(ingGmsPerPouch,'2','.',' ');
			}
			if(ratePerKg!="" && pouchPerBatch!="")
			{
				var ratePerPouch=parseFloat(ratePerKg)/parseFloat(pouchPerBatch);
				document.getElementById('ratePerPouch_'+i).value=number_format(ratePerPouch,'2','.',' ');	 
				document.getElementById('percentageCostPerPouch_'+i).value=Math.round(ratePerPouch);
			}
		//////////////////////////		
		if (fixedQtyChk!="" && selIngredient!="") {
			//fishKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
			fishKgPerBatch += parseFloat(document.getElementById("fixedQty_"+i).value); //Sum of FixedQty	
			//fishRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i).value);
			fishRatePerKg	+= parseFloat(ratePerKg);
			fixedKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
		} else if (selIngredient!="") {			
			gravyKgPerBatch += parseFloat(document.getElementById("quantity_"+i).value);
			//gravyRatePerBatch += parseFloat(document.getElementById("ratePerBatch_"+i).value);
			gravyRatePerKg	+= parseFloat(ratePerKg);
		}

		//}
         } // Status Check ends Here
		
	} //Loop End

	// Assign the values (Fixed Qty)
	document.getElementById("fishGmsPerPouch").value = number_format(fishKgPerBatch,3,'.','');
	document.getElementById("totalFixedFishQty").value = number_format(fishKgPerBatch,3,'.','');

	//Kg (Raw) per Batch
	document.getElementById("fishKgPerBatch").value = number_format(fixedKgPerBatch,2,'.','');
	document.getElementById("gravyKgPerBatch").value = number_format(gravyKgPerBatch,2,'.','');
	document.getElementById("productKgPerBatch").value = number_format(( parseFloat(fixedKgPerBatch)+parseFloat(gravyKgPerBatch)),2,'.','');

	//Rs. Per Kg
	document.getElementById("fishRatePerKg").value = number_format(fishRatePerKg,2,'.','');
	document.getElementById("gravyRatePerKg").value = number_format(gravyRatePerKg,2,'.','');
	document.getElementById("productRatePerKg").value = parseFloat(fishRatePerKg)+parseFloat(gravyRatePerKg);

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

	document.getElementById("productRatePerPouch").value  = number_format((parseFloat(document.getElementById("productRatePerBatch").value)/document.getElementById("pouchPerBatch").value),2,'.','');

	//% (Raw) per Batch
	document.getElementById("fishRawPercentagePerPouch").value = number_format((parseFloat(document.getElementById("fishKgPerBatch").value)/parseFloat(document.getElementById("productKgPerBatch").value))*100,0,'.','');
	document.getElementById("gravyRawPercentagePerPouch").value = number_format(( parseFloat(document.getElementById("gravyKgPerBatch").value)/parseFloat(document.getElementById("productKgPerBatch").value))*100,0,'.','');
	document.getElementById("productRawPercentagePerPouch").value = parseFloat(document.getElementById("fishRawPercentagePerPouch").value) + parseFloat(document.getElementById("gravyRawPercentagePerPouch").value)
	
	// Kg (in Pouch) per Batch
	document.getElementById("fishKgInPouchPerBatch").value = number_format((parseFloat(document.getElementById("pouchPerBatch").value)*parseFloat(fishGmsPerPouch)),2,'.','');
	document.getElementById("gravyKgInPouchPerBatch").value = number_format((parseFloat(document.getElementById("pouchPerBatch").value)*parseFloat(gravyGmsPerPouch)),2,'.','');

	calcProductKgInPouchPerBatch = parseFloat(document.getElementById("fishKgInPouchPerBatch").value) + parseFloat(document.getElementById("gravyKgInPouchPerBatch").value);

	if (!isNaN(calcProductKgInPouchPerBatch)) {
		document.getElementById("productKgInPouchPerBatch").value = number_format(calcProductKgInPouchPerBatch,2,'.','');
	}

	//% per Pouch
	document.getElementById("fishPercentagePerPouch").value  = number_format((fishGmsPerPouch/document.getElementById("productGmsPerPouch").value)*100,0,'.','');
	document.getElementById("gravyPercentagePerPouch").value = number_format((gravyGmsPerPouch/document.getElementById("productGmsPerPouch").value)*100,0,'.','');
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

// Hide  the Fixed Qty div Box
/*function hidFixedQtyDiv()
{		
	var itemCount 	      = document.getElementById("hidTableRowCount").value;
	
	for (i=0; i<itemCount; i++) {		
		var selIngredient = document.getElementById("selIngredient_"+i).value;
		var fixedQtyChk = document.getElementById("fixedQtyChk_"+i).checked;
		if (!fixedQtyChk) {
			document.getElementById("fixedQtyDiv_"+i).style.display='none';
		} else {
			document.getElementById("fixedQtyDiv_"+i).style.display='block';
		}		
	}
}
*/

//ADD MULTIPLE Item- ADD ROW START
function addNewIngItemRow(tableId)
{
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	var iteration	= lastRow+1;
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
	var cell6	= row.insertCell(5);
	var cell7	= row.insertCell(6);
	var cell8	= row.insertCell(7);
	var cell9	= row.insertCell(8);
	var cell10	= row.insertCell(9);	
	
	
	cell1.className	= "listing-item"; cell1.align	= "center";cell1.noWrap = "true";
	cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
        cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
        cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";
	cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";
	cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";
        cell7.className	= "listing-item"; cell7.align	= "center";cell7.noWrap = "true";
        cell8.className	= "listing-item"; cell8.align	= "center";cell8.noWrap = "true";
	cell9.className	= "listing-item"; cell9.align	= "center";cell9.noWrap = "true";
        cell10.className = "listing-item"; cell10.align	= "center";cell10.noWrap = "true";
	
	var selectIngredient	= "<select name='selIngredient_"+fieldId+"' id='selIngredient_"+fieldId+"' onchange=\"xajax_getIngRate(document.getElementById('selIngredient_"+fieldId+"').value,"+fieldId+",'');calcProductRatePerBatch("+fieldId+");\"><option value=''>--Select--</option>";
	<?php
	if (sizeof($ingredientRecords)>0) {
		$ingredientId = "";
		foreach ($ingredientRecords as $irr) {
			$ingredientId   = $irr[0];
			$ingredientName	= $irr[1];
	?>
	selectIngredient += "<option value='<?=$ingredientId;?>'><?=$ingredientName;?></option>";
	<?php
		}
	}
	?>
	selectIngredient += "</select>";
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIngItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='ingType_"+fieldId+"' type='hidden' id='ingType_"+fieldId+"'>";

	cell1.innerHTML	= selectIngredient;
	cell2.innerHTML	= "<input name='rsperKg_"+fieldId+"' type='text' id='rsperKg_"+fieldId+"' value='' size='6' style='text-align:right' onkeyup='calcProductRatePerBatch();' autoComplete='off'><input name='ingRateId_"+fieldId+"' type='hidden' id='ingRateId_"+fieldId+"' value='' size='6' style='text-align:right'  autoComplete='off'>";
	cell3.innerHTML	= "<input name='quantity_"+fieldId+"' type='text' id='quantity_"+fieldId+"' value='' size='6' style='text-align:right' onkeyup='calcProductRatePerBatch("+fieldId+");' autoComplete='off'>"+hiddenFields+"";
	cell4.innerHTML	= "<input type='text' name='percentagePerBatch_"+fieldId+"' id='percentagePerBatch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>%";
	cell5.innerHTML	= "<input type='text' name='ratePerKg_"+fieldId+"' id='ratePerKg_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>";
	
	cell6.innerHTML	= "<input name='fixedQtyChk_"+fieldId+"' type='checkbox' id='fixedQtyChk_"+fieldId+"' value='Y' size='6' class='chkBox' onClick='calcProductRatePerBatch();'>";
	cell7.innerHTML = "<input type='text' name='ingGmsPerPouch_"+fieldId+"' id='ingGmsPerPouch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>";
	cell8.innerHTML = "<input type='text' name='ratePerPouch_"+fieldId+"' id='ratePerPouch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>";
	cell9.innerHTML = "<input type='text' name='percentageCostPerPouch_"+fieldId+"' id='percentageCostPerPouch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>%";
	cell10.innerHTML = imageButton;
	
	fieldId		= parseInt(fieldId)+1;
	document.getElementById("hidTableRowCount").value = fieldId;
	
}

function setIngItemStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none';
 		calcProductRatePerBatch();
	}
	return false;
}

//ADD MULTIPLE Item- ADD ROW START
/*function addNewIngItemRow(tableId)
{
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	var iteration	= lastRow+1;
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
	var cell6	= row.insertCell(5);
	var cell7	= row.insertCell(6);
	var cell8	= row.insertCell(7);
	var cell9	= row.insertCell(8);
	var cell10	= row.insertCell(9);	
	var cell11	= row.insertCell(10);	
	var cell12	= row.insertCell(11);
	var cell13	= row.insertCell(12);
	var cell14	= row.insertCell(13);
	
	cell1.className	= "listing-item"; cell1.align	= "center";cell1.noWrap = "true";
	cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
        cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
        cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";
	cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";
	cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";
        cell7.className	= "listing-item"; cell7.align	= "center";cell7.noWrap = "true";
        cell8.className	= "listing-item"; cell8.align	= "center";cell8.noWrap = "true";
	cell9.className	= "listing-item"; cell9.align	= "center";cell9.noWrap = "true";
        cell10.className = "listing-item"; cell10.align	= "center";cell10.noWrap = "true";
	cell11.className = "listing-item"; cell11.align	= "center";cell11.noWrap = "true";
	cell12.className = "listing-item"; cell12.align	= "center";cell12.noWrap = "true";
	cell13.className = "listing-item"; cell13.align	= "center";cell13.noWrap = "true";
	cell14.className = "listing-item"; cell14.align	= "center";cell14.noWrap = "true";
	
	var selectIngredient	= "<select name='selIngredient_"+fieldId+"' id='selIngredient_"+fieldId+"' onchange=\"xajax_getIngRate(document.getElementById('selIngredient_"+fieldId+"').value,"+fieldId+",'');calcProductRatePerBatch();\"><option value=''>--Select--</option>";
	<?php
	if (sizeof($ingredientRecords)>0) {
		$ingredientId = "";
		foreach ($ingredientRecords as $kVal=>$irr) {
			$ingredientId   = $irr[0];
			$ingredientName	= $irr[1];
	?>
	selectIngredient += "<option value='<?=$ingredientId;?>'><?=$ingredientName;?></option>";
	<?php
		}
	}
	?>
	selectIngredient += "</select>";
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIngItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='ingType_"+fieldId+"' type='hidden' id='ingType_"+fieldId+"'>";

	cell1.innerHTML	= selectIngredient;
	cell2.innerHTML	= "<input name='quantity_"+fieldId+"' type='text' id='quantity_"+fieldId+"' value='' size='6' style='text-align:right' onkeyup='calcProductRatePerBatch();' autoComplete='off'>"+hiddenFields+"";
	cell3.innerHTML	= "<input name='cleanedQty_"+fieldId+"' type='text' id='cleanedQty_"+fieldId+"' value='' size='6' style='text-align:right;border:none;' autoComplete='off' readOnly>";	
	cell4.innerHTML	= "<input name='declYield_"+fieldId+"' type='text' id='declYield_"+fieldId+"' value='' size='6' style='text-align:right;border:none;' autoComplete='off'>&nbsp;%";
	cell5.innerHTML	= "<input name='fixedQtyChk_"+fieldId+"' type='checkbox' id='fixedQtyChk_"+fieldId+"' value='Y' size='6' class='chkBox' onClick='hidFixedQtyDiv();calcProductRatePerBatch();'>";
	//cell6.innerHTML	= "<div id='fixedQtyDiv_"+fieldId+"' style='display:none'><table><TR><TD><input name='fixedQty_"+fieldId+"' type='text' id='fixedQty_"+fieldId+"' value='' size='6' style='text-align:right' onkeyup='calcProductRatePerBatch();'></TD></TR></table></div>";
	cell6.innerHTML	= "<div id='fixedQtyDiv_"+fieldId+"' style='display:none'><input name='fixedQty_"+fieldId+"' type='text' id='fixedQty_"+fieldId+"' value='' size='6' style='text-align:right' onkeyup='calcProductRatePerBatch();'></div>";
	cell7.innerHTML	= "<input type='text' name='percentagePerBatch_"+fieldId+"' id='percentagePerBatch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>%";
	cell8.innerHTML	= "<input type='text' name='ratePerKg_"+fieldId+"' id='ratePerKg_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>";
	cell9.innerHTML	= "<input type='hidden' name='lastPrice_"+fieldId+"' id='lastPrice_"+fieldId+"' value=''><input type='text' name='ratePerBatch_"+fieldId+"' id='ratePerBatch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>";
	cell10.innerHTML = "<input type='text' name='ingGmsPerPouch_"+fieldId+"' id='ingGmsPerPouch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>";
	cell11.innerHTML = "<input type='text' name='percentageWtPerPouch_"+fieldId+"'' id='percentageWtPerPouch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>%";
	cell12.innerHTML = "<input type='text' name='ratePerPouch_"+fieldId+"' id='ratePerPouch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>";
	cell13.innerHTML = "<input type='text' name='percentageCostPerPouch_"+fieldId+"' id='percentageCostPerPouch_"+fieldId+"' style='text-align:right;border:none' readonly value='' size='6'>%";
	cell14.innerHTML = imageButton;
	
	fieldId		= parseInt(fieldId)+1;
	document.getElementById("hidTableRowCount").value = fieldId;
	
}
*/