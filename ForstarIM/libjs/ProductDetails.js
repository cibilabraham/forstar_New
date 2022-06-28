function calculateProductForPouch()
{
	var pouchesProductGmsPerPouch 	= parseFloat(document.getElementById("pouchesProductGmsPerPouch").value);	
	var pouchesFishGmsPerPouch 	= parseFloat(document.getElementById("pouchesFishGmsPerPouch").value);
	//Total Pouch
	var pouchesPerBatch		= document.getElementById("pouchesPerBatch").value;
	var pouchesFishKgInPouchPerBatch = parseFloat(document.getElementById("pouchesFishKgInPouchPerBatch").innerHTML);

	document.getElementById("pouchesFishKgInPouchPerBatch").innerHTML = formatNumber(Math.abs(pouchesPerBatch * pouchesFishGmsPerPouch),2,'','.','','','','','');;
	var pouchesFishKgInPouchPerBatch = parseFloat(document.getElementById("pouchesFishKgInPouchPerBatch").innerHTML);

	document.getElementById("pouchesGravyGmsPerPouch").innerHTML = formatNumber(Math.abs(pouchesProductGmsPerPouch-pouchesFishGmsPerPouch),3,'','.','','','','','');

	var pouchesGravyGmsPerPouch = parseFloat(document.getElementById("pouchesGravyGmsPerPouch").innerHTML);
	
	document.getElementById("pouchesGravyKgInPouchPerBatch").innerHTML = number_format(Math.abs(pouchesPerBatch*pouchesGravyGmsPerPouch),2,'.','');

	var pouchesGravyKgInPouchPerBatch = parseFloat(document.getElementById("pouchesGravyKgInPouchPerBatch").innerHTML);

	calcPouchesProductKgInPouchPerBatch = parseFloat(pouchesFishKgInPouchPerBatch)+parseFloat(pouchesGravyKgInPouchPerBatch);
	if (!isNaN(calcPouchesProductKgInPouchPerBatch)) {
		document.getElementById("pouchesProductKgInPouchPerBatch").innerHTML= number_format(Math.floor(calcPouchesProductKgInPouchPerBatch),0,'.','');
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
	for (i=1; i<=itemCount; i++) {
		//var ingType 		= document.getElementById("ingType_"+i).value;
		var fixedQty 		= document.getElementById("fixedQty_"+i).value;
		var quantity 		= document.getElementById("quantity_"+i).value;
		var ratePerBatch	= parseFloat(document.getElementById("ratePerBatch_"+i).value);
		
		if (fixedQty=='Y') {
			document.getElementById("pouchesKgPerBatch_"+i).innerHTML = number_format(Math.abs(((quantity/fishKgInPouchPerBatch)*pouchesFishKgInPouchPerBatch)),2,'.','');
			pouchesFishKgPerBatch += document.getElementById("pouchesKgPerBatch_"+i).innerHTML;
			
		} else if (fixedQty=='N') {
			document.getElementById("pouchesKgPerBatch_"+i).innerHTML = number_format(Math.abs(((quantity/gravyKgInPouchPerBatch)*pouchesGravyKgInPouchPerBatch)),2,'.','');
			pouchesGravyKgPerBatch += document.getElementById("pouchesKgPerBatch_"+i).innerHTML;
		}


		var pouchesKgPerBatch = parseFloat(document.getElementById("pouchesKgPerBatch_"+i).innerHTML);

		ingPouchesRatePerkg = ((ratePerBatch/quantity)*(pouchesKgPerBatch/pouchesPerBatch));
		//alert(ingPouchesRatePerkg);
		if (!isNaN(ingPouchesRatePerkg)) {
			document.getElementById("pouchesRatePerBatch_"+i).innerHTML = number_format(Math.abs(ingPouchesRatePerkg),3,'.','');
		}

		//alert(document.getElementById("pouchesRatePerBatch_"+i).innerHTML);
		//Rs. Per Batch
		if (fixedQty=='Y') {
			pouchesFishRatePerBatch += parseFloat(document.getElementById("pouchesRatePerBatch_"+i).innerHTML);
		} else if (fixedQty=='N') {
			pouchesGravyRatePerBatch += parseFloat(document.getElementById("pouchesRatePerBatch_"+i).innerHTML);
		}


		ingPouchesWtPerBatch = parseFloat(pouchesKgPerBatch/(pouchesPerBatch*pouchesProductGmsPerPouch));
		if (!isNaN(ingPouchesWtPerBatch)) {
			document.getElementById("pouchesWtPerBatch_"+i).innerHTML = number_format(Math.abs(ingPouchesWtPerBatch),2,'.','');
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
		document.getElementById("pouchesFishRatePerBatch").innerHTML = number_format(Math.abs(parseFloat(pouchesFishRatePerBatch*pouchesPerBatch)),0,'','');

		document.getElementById("pouchesGravyRatePerBatch").innerHTML = number_format(Math.abs(parseFloat(pouchesGravyRatePerBatch*pouchesPerBatch)),0,'','');
		//alert(pouchesFishRatePerBatch+"*"+pouchesPerBatch);
		calcPouchesProductRatePerBatch = parseFloat(document.getElementById("pouchesFishRatePerBatch").innerHTML) + parseFloat(document.getElementById("pouchesGravyRatePerBatch").innerHTML);
		if (!isNaN(calcPouchesProductRatePerBatch)) {
			document.getElementById("pouchesProductRatePerBatch").innerHTML = number_format(Math.abs(calcPouchesProductRatePerBatch),0,'','');
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
	document.getElementById("pouchesFishPercentagePerPouch").innerHTML = number_format(Math.abs(((pouchesFishGmsPerPouch/pouchesProductGmsPerPouch)*100)),0,'','');

	document.getElementById("pouchesGravyPercentagePerPouch").innerHTML  =
	number_format(Math.abs(((pouchesGravyGmsPerPouch/pouchesProductGmsPerPouch)*100)),0,'','');

	calcPouchesProductPercentagePerPouch = parseFloat(document.getElementById("pouchesFishPercentagePerPouch").innerHTML)+parseFloat(document.getElementById("pouchesGravyPercentagePerPouch").innerHTML);
	if (!isNaN(calcPouchesProductPercentagePerPouch)) {
		document.getElementById("pouchesProductPercentagePerPouch").innerHTML  = number_format(Math.abs(calcPouchesProductPercentagePerPouch),0,'','');
	}

	//Rs. Per Pouch
	multiPlyPouchesFishRatePerPouch = parseFloat(document.getElementById("pouchesFishRatePerKgPerbatch").innerHTML) * pouchesFishGmsPerPouch;
	if (!isNaN(multiPlyPouchesFishRatePerPouch)) {
		document.getElementById("pouchesFishRatePerPouch").innerHTML  = number_format(Math.abs(multiPlyPouchesFishRatePerPouch),2,'.','');
	}
	multiPlyPouchesGravyRatePerPouch = parseFloat(document.getElementById("pouchesGravyRatePerKgPerbatch").innerHTML) * pouchesGravyGmsPerPouch;
	if (!isNaN(multiPlyPouchesGravyRatePerPouch)) {
		document.getElementById("pouchesGravyRatePerPouch").innerHTML = number_format(Math.abs(multiPlyPouchesGravyRatePerPouch),2,'.','');
	}
	
	calcPouchesProductRatePerPouch = parseFloat(document.getElementById("pouchesProductRatePerBatch").innerHTML)/pouchesPerBatch;

	if (!isNaN(calcPouchesProductRatePerPouch)) {
		document.getElementById("pouchesProductRatePerPouch").innerHTML = number_format(Math.abs(calcPouchesProductRatePerPouch),2,'.','');
	}

	//% Yield
	calcPouchesFishPercentageYield = parseFloat(document.getElementById("pouchesFishKgInPouchPerBatch").innerHTML)/parseFloat(document.getElementById("pouchesFishKgPerBatch").innerHTML);
	if (!isNaN(calcPouchesFishPercentageYield)) {
		document.getElementById("pouchesFishPercentageYield").innerHTML  = number_format(Math.abs(calcPouchesFishPercentageYield),0,'.','');
	}

	calcPouchesGravyPercentageYield = parseFloat(document.getElementById("pouchesGravyKgInPouchPerBatch").innerHTML)/parseFloat(document.getElementById("pouchesGravyKgPerBatch").innerHTML);

	if (!isNaN(calcPouchesGravyPercentageYield)) {
		document.getElementById("pouchesGravyPercentageYield").innerHTML  = number_format(Math.abs(calcPouchesGravyPercentageYield),0,'.','');
	}
}




//For Fish Calculation
function calculateProductForFish()
{
	// Gms per Pouch
	var productGmsPerPouchForFish = parseFloat(document.getElementById("productGmsPerPouchForFish").value);
	var fishGmsPerPouchForFish  = parseFloat(document.getElementById("fishGmsPerPouchForFish").value);
	calcGravyGmsPerPouchOfFish = productGmsPerPouchForFish-fishGmsPerPouchForFish;
	if (!isNaN(calcGravyGmsPerPouchOfFish)) {
		document.getElementById("gravyGmsPerPouchForFish").innerHTML = number_format(Math.abs(calcGravyGmsPerPouchOfFish),3,'.','');
		var gravyGmsPerPouchForFish = parseFloat(document.getElementById("gravyGmsPerPouchForFish").innerHTML);
	}
	//Kg (in Pouch) per Batch
	var fishKgInPouchPerBatchForFish = parseFloat(document.getElementById("fishKgInPouchPerBatchForFish").value);

	//Pouches per Batch
	var calcPouchPerBatchForFish = fishKgInPouchPerBatchForFish/fishGmsPerPouchForFish;
	if (!isNaN(calcPouchPerBatchForFish)) {
		document.getElementById("pouchPerBatchForFish").innerHTML = number_format(Math.abs(calcPouchPerBatchForFish),0,'','');
		var pouchPerBatchForFish = eval(document.getElementById("pouchPerBatchForFish").innerHTML);
	}
	//Kg (in Pouch) per Batch
	var calcGravyKgInPouchPerBatchForFish = parseFloat(document.getElementById("pouchPerBatchForFish").innerHTML)*parseFloat(document.getElementById("gravyGmsPerPouchForFish").innerHTML);
	if (!isNaN(calcGravyKgInPouchPerBatchForFish)) {
		document.getElementById("gravyKgInPouchPerBatchForFish").innerHTML = number_format(Math.abs(calcGravyKgInPouchPerBatchForFish),2,'.','');

		var gravyKgInPouchPerBatchForFish = parseFloat(document.getElementById("gravyKgInPouchPerBatchForFish").innerHTML);
	}
	

	var calcProductKgInPouchPerBatchForFish = fishKgInPouchPerBatchForFish + parseFloat(document.getElementById("gravyKgInPouchPerBatchForFish").innerHTML);
	if (!isNaN(calcProductKgInPouchPerBatchForFish)) {
		document.getElementById("productKgInPouchPerBatchForFish").innerHTML = number_format(Math.abs(calcProductKgInPouchPerBatchForFish),2,'.','');
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
	for (i=1; i<=itemCount; i++) {
		var fixedQty 		= document.getElementById("fixedQty_"+i).value;
		//var ingType 		= document.getElementById("ingType_"+i).value;
		var quantity 		= document.getElementById("quantity_"+i).value;
		var ratePerBatch	= parseFloat(document.getElementById("ratePerBatch_"+i).value);
		
		if (fixedQty=='Y') {
			document.getElementById("kgPerBatchForFish_"+i).innerHTML = number_format(Math.abs(((quantity/fishKgInPouchPerBatch)*fishKgInPouchPerBatchForFish)),2,'.','');
			fishKgPerBatchForFish += document.getElementById("kgPerBatchForFish_"+i).innerHTML;
			
		} else if (fixedQty=='N') {
			document.getElementById("kgPerBatchForFish_"+i).innerHTML = number_format(Math.abs(((quantity/gravyKgInPouchPerBatch)*gravyKgInPouchPerBatchForFish)),2,'.','');
			gravyKgPerBatchForFish += document.getElementById("kgPerBatchForFish_"+i).innerHTML;
		}

		var kgPerBatchForFish = parseFloat(document.getElementById("kgPerBatchForFish_"+i).innerHTML);

		ingRatePerkgForFish = ((ratePerBatch/quantity)*kgPerBatchForFish/pouchPerBatchForFish);
		if (!isNaN(ingRatePerkgForFish)) {
			document.getElementById("ratePerBatchForFish_"+i).innerHTML = number_format(Math.abs(ingRatePerkgForFish),3,'.','');
		}

		//Rs. Per Batch
		if (fixedQty=='Y') {
			fishRatePerBatchForFish += parseFloat(document.getElementById("ratePerBatchForFish_"+i).innerHTML);
		} else if (fixedQty=='N') {
			gravyRatePerBatchForFish += parseFloat(document.getElementById("ratePerBatchForFish_"+i).innerHTML);
		}


		ingWtPerBatchForFish = (kgPerBatchForFish/(pouchPerBatchForFish*productGmsPerPouchForFish));
		if (!isNaN(ingWtPerBatchForFish)) {
			document.getElementById("wtPerBatchForFish_"+i).innerHTML = number_format(Math.abs(ingWtPerBatchForFish),2,'.','');
		}
	}

	//Kg (Raw) per Batch
		if (!isNaN(fishKgPerBatchForFish)) {
			document.getElementById("fishKgPerBatchForFish").innerHTML = number_format(Math.abs(fishKgPerBatchForFish),2,'.','');
		}
		if (!isNaN(gravyKgPerBatchForFish)) {
			document.getElementById("gravyKgPerBatchForFish").innerHTML = number_format(Math.abs(gravyKgPerBatchForFish),2,'.','');
		}
	
		calcProductKgPerBatchForFish = parseFloat(document.getElementById("fishKgPerBatchForFish").innerHTML)+parseFloat(document.getElementById("gravyKgPerBatchForFish").innerHTML);
	
		if (!isNaN(calcProductKgPerBatchForFish)) {
			document.getElementById("productKgPerBatchForFish").innerHTML = number_format(Math.abs(calcProductKgPerBatchForFish),2,'.','');
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
			document.getElementById("fishRatePerBatchForFish").innerHTML = number_format(Math.abs(calcFishRatePerBatchForFish),0,'','');
		}
		calcGravyRatePerBatchForFish = (gravyRatePerBatchForFish*pouchPerBatchForFish);
		if (!isNaN(calcGravyRatePerBatchForFish)) {
			document.getElementById("gravyRatePerBatchForFish").innerHTML = number_format(Math.abs(calcGravyRatePerBatchForFish),0,'','');
		}

		calcProductRatePerBatchForFish = parseFloat(document.getElementById("fishRatePerBatchForFish").innerHTML) + parseFloat(document.getElementById("gravyRatePerBatchForFish").innerHTML);
		if (!isNaN(calcProductRatePerBatchForFish)) {
			document.getElementById("productRatePerBatchForFish").innerHTML = number_format(Math.abs(calcProductRatePerBatchForFish),0,'','');
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
		document.getElementById("fishPercentageYieldForFish").innerHTML  = number_format(Math.abs(calcFishPercentageYieldForFish),0,'','');
	}

	calcGravyPercentageYieldForFish = ( parseFloat(document.getElementById("gravyKgInPouchPerBatchForFish").innerHTML)/parseFloat(document.getElementById("gravyKgPerBatchForFish").innerHTML));

	if (!isNaN(calcGravyPercentageYieldForFish)) {
		document.getElementById("gravyPercentageYieldForFish").innerHTML  = number_format(Math.abs(calcGravyPercentageYieldForFish),0,'','');
	}
}

//Rate Per batch
function calcRatePerBatch()
{
	var itemCount 	      = document.getElementById("hidItemCount").value;
	var productKgPerbatch = document.getElementById("productKgPerbatch").value;
	var pouchPerBatch     = document.getElementById("pouchPerBatch").value;
	var productGmsPerPouch = document.getElementById("productGmsPerPouch").value;
	var pouchPerBatch = document.getElementById("pouchPerBatch").value;
	var productRatePerPouch = document.getElementById("productRatePerPouch").value;
	var fishGmsPerPouch = document.getElementById("fishGmsPerPouch").value;
	var gravyGmsPerPouch = document.getElementById("gravyGmsPerPouch").value;

	var getIngPrice = 0;
	var getPercentagePerbatch = 0;
	fishKgPerbatch = 0;
	gravyKgPerbatch = 0;
	fishRatePerBatch=0;
	gravyRatePerBatch = 0;
	for (i=1; i<=itemCount; i++) {
		var fixedQty = document.getElementById("fixedQty_"+i).value;
		//var ingType   = document.getElementById("ingType_"+i).value;
		var quantity  = document.getElementById("quantity_"+i).value;
		var lastPrice = document.getElementById("lastPrice_"+i).value;
		//Find Rate for each Ingredient
		getIngPrice =  quantity*lastPrice;
		if (!isNaN(getIngPrice)) {
			document.getElementById("ratePerBatch_"+i).value = number_format(Math.abs(getIngPrice),2,'.','');
		}
		// Find Percentage for Each Item
		getPercentagePerbatch = (quantity/productKgPerbatch);
		if (!isNaN(getPercentagePerbatch)) {
			document.getElementById("percentagePerBatch_"+i).value = number_format(Math.abs(getPercentagePerbatch*100),0,'','');
		}
		//Find Gms Per pouch
		getGmsPerPouch = (quantity/pouchPerBatch);
		if (!isNaN(getGmsPerPouch)) {
			document.getElementById("gmsPerPouch_"+i).value = number_format(getGmsPerPouch,3,'.','');
		}
		//Find Percentage Wt
		getPercentageWtPerpouch = ((document.getElementById("gmsPerPouch_"+i).value/productGmsPerPouch)*100);
		if (!isNaN(getPercentageWtPerpouch)) {
			document.getElementById("percentageWtPerPouch_"+i).value = number_format(Math.abs(getPercentageWtPerpouch),2,'.','');
		}
		//Find Rate Per Pouch
		getRatePerPouch = (document.getElementById("ratePerBatch_"+i).value/pouchPerBatch);
		if (!isNaN(getRatePerPouch)) {
			document.getElementById("ratePerPouch_"+i).value = number_format(Math.abs(getRatePerPouch),2,'.','');
		}
		//Find Percentage Cost Per Pouch
		getPercentageCostPerPouch = ((document.getElementById("ratePerPouch_"+i).value/productRatePerPouch)*100);
		if (!isNaN(getPercentageCostPerPouch)) {
			document.getElementById("percentageCostPerPouch_"+i).value = number_format(Math.abs(getPercentageCostPerPouch),0,'','');
		}
		//////////////////////////

		if (fixedQty=='Y') {
			fishKgPerbatch += document.getElementById("quantity_"+i).value;
			fishRatePerBatch += document.getElementById("ratePerBatch_"+i).value;
		} else if (fixedQty=='N') {
			gravyKgPerbatch += document.getElementById("quantity_"+i).value;
			gravyRatePerBatch += document.getElementById("ratePerBatch_"+i).value;
		}
		
	} //Loop End
	//Kg (Raw) per Batch
	document.getElementById("fishKgPerbatch").value = number_format(fishKgPerbatch,2,'.','');
	document.getElementById("gravyKgPerbatch").value = number_format(gravyKgPerbatch,2,'.','');
	document.getElementById("productKgPerbatch").value = parseFloat(fishKgPerbatch)+parseFloat(gravyKgPerbatch);

	//Rs. Per Batch
	document.getElementById("fishRatePerBatch").value = number_format(fishRatePerBatch,2,'.','');
	document.getElementById("gravyRatePerBatch").value = number_format(gravyRatePerBatch,2,'.','');
	document.getElementById("productRatePerBatch").value = parseFloat(fishRatePerBatch)+parseFloat(gravyRatePerBatch);

	//Rs. Per Kg per Batch
	document.getElementById("fishRatePerKgPerBatch").value = number_format(( document.getElementById("fishRatePerBatch").value/document.getElementById("fishKgInPouchPerBatch").value),2,'.','');
	document.getElementById("gravyRatePerKgPerBatch").value = number_format((document.getElementById("gravyRatePerBatch").value/document.getElementById("gravyKgInPouchPerBatch").value),2,'.','');
	document.getElementById("productRatePerKgPerBatch").value = number_format(( parseFloat(document.getElementById("fishRatePerKgPerBatch").value)+parseFloat(document.getElementById("gravyRatePerKgPerBatch").value)),2,'.','');
	
	//Rs. Per Pouch
	document.getElementById("fishRatePerPouch").value  =number_format(( document.getElementById("fishRatePerKgPerBatch").value * fishGmsPerPouch),2,'.','');

	document.getElementById("gravyRatePerPouch").value  = number_format(( document.getElementById("gravyRatePerKgPerBatch").value*gravyGmsPerPouch),2,'.','');

	document.getElementById("productRatePerPouch").value  = number_format((document.getElementById("productRatePerBatch").value/pouchPerBatch),2,'.','');

	//% (Raw) per Batch
	document.getElementById("fishRawPercentagePerPouch").value = number_format((document.getElementById("fishKgPerbatch").value/document.getElementById("productKgPerbatch").value)*100,0,'.','');
	document.getElementById("gravyRawPercentagePerPouch").value = number_format(( document.getElementById("gravyKgPerbatch").value/document.getElementById("productKgPerbatch").value)*100,0,'.','');
	document.getElementById("productRawPercentagePerPouch").value = parseFloat(document.getElementById("fishRawPercentagePerPouch").value) + parseFloat(document.getElementById("gravyRawPercentagePerPouch").value)
	
	// Kg (in Pouch) per Batch
	document.getElementById("fishKgInPouchPerBatch").value = number_format((pouchPerBatch*fishGmsPerPouch),2,'.','');
	document.getElementById("gravyKgInPouchPerBatch").value = number_format((pouchPerBatch*gravyGmsPerPouch),2,'.','');
	document.getElementById("productKgInPouchPerBatch").value = parseFloat(document.getElementById("fishKgInPouchPerBatch").value) + parseFloat(document.getElementById("gravyKgInPouchPerBatch").value);

	//% per Pouch
	document.getElementById("fishPercentagePerPouch").value  = number_format((fishGmsPerPouch/productGmsPerPouch)*100,0,'.','');
	document.getElementById("gravyPercentagePerPouch").value = number_format((gravyGmsPerPouch/productGmsPerPouch)*100,0,'.','');
	productPercentagePerPouch = parseFloat(document.getElementById("fishPercentagePerPouch").value) + parseFloat(document.getElementById("gravyPercentagePerPouch").value);

	//% Yield
	document.getElementById("fishPercentageYield").value  = number_format((document.getElementById("fishKgInPouchPerBatch").value/document.getElementById("fishKgPerbatch").value),0,'.','');
	document.getElementById("gravyPercentageYield").value  = number_format(( document.getElementById("gravyKgInPouchPerBatch").value/document.getElementById("gravyKgPerbatch").value),0,'.','');
	
}	