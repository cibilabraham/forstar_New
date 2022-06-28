function validateIngredientRateMaster(form,mode)
{
	var selIngredient	= form.selIngredient.value;
	var materialType    = form.materialType.value;
	var ingRatePerKg	= form.ingRatePerKg.value;
	var ingYield		= form.ingYield.value;
	var cleanedCost     = form.cleanCost.value;
	var rawIngredient    = form.rawIngredient.value;
	var effectiveDate    = form.effectiveDate.value;
	if(mode==2)
	{
	var newEffectiveDate = form.newEffectiveDate.value;
	}
	
	
	if (selIngredient=="") {
		alert("Please select a Ingredient.");
		form.selIngredient.focus();
		return false;
	}
	
	if (ingRatePerKg=="") {
		alert("Please enter a Rate/Kg.");
		form.ingRatePerKg.focus();
		return false;
	}
	
	if (ingYield=="" || ingYield==0) {
		alert("Please enter an Yield.");
		form.ingYield.focus();
		return false;
	}
	
	if (materialType=="CLEANED" && rawIngredient=="") {
		alert("Please Select a Raw Ingredient.");
		form.rawIngredient.focus();
		return false;
	}

	if (effectiveDate=="") {
		alert("Please Enter Effective Date.");
		form.effectiveDate.focus();
		return false;
	}
	if(mode==2 && newEffectiveDate=="") {
	    alert("Please Enter a New Effective Date.");
		form.newEffectiveDate.focus();
		return false;
	}

	if (!confirmSave()) {
		return false;
	}
	
	document.getElementById("selIngredient").disabled = false;
	document.getElementById("rawIngredient").disabled = false;
	document.getElementById("materialType").disabled = false;
	
	return true;
}

	/* old functionality
	// Calculate Clean Rate Per Kg
	function calcCleanRatePerKg()
	{
		var calcCleanRate	= 0;
		var ingRatePerKg	= parseFloat(document.getElementById("ingRatePerKg").value);
		var ingYield		= parseFloat(document.getElementById("ingYield").value);
		calcCleanRate		= (ingRatePerKg/ingYield)*100;
		if (!isNaN(calcCleanRate)) 
			document.getElementById("ingLastPrice").value = number_format(calcCleanRate,2,'.','');
	}
	*/

	// Calculate Clean Rate Per Kg
	function calcCleanRatePerKg()
	{
		var calcCleanRate	= 0;
		var ingRatePerKg	= parseFloat(document.getElementById("ingRatePerKg").value);
		var ingYield		= parseFloat(document.getElementById("ingYield").value);
		var cleanedCost		= parseFloat(document.getElementById("cleanedCost").value);
		var materialType=	document.getElementById("materialType").value;
		//alert(cleanedCost+"--"+ingRatePerKg+"--"+ingYield+"----"+materialType);
		if(ingRatePerKg!="" && cleanedCost!="" && ingYield!="")
		{
			if(materialType=="rawmaterial")
			{
				calcCleanRate=(ingRatePerKg+cleanedCost)/ ingYield;
			}
			else if(materialType=="cleaned")
			{
				calcCleanRate=(ingRatePerKg /ingYield)+cleanedCost;
			}
			if (!isNaN(calcCleanRate))
			{
				document.getElementById("ingLastPrice").value = number_format(calcCleanRate,2,'.','');
			}
		}
	}
	
	function displayRawColumn(mode)
	{
		var materialType = document.getElementById("materialType").value;
		
		//alert(mode);
		
		if(materialType == 1)
		{
			document.getElementById("cleanedRate").style.display = "block";
			document.getElementById("cleanedYield").style.display = "block";
			document.getElementById("finalRate").style.display = "block";
			
			document.getElementById("cleanedIngredient").style.display = "none";
			document.getElementById("cleanedCost").style.display = "none";
			document.getElementById("cleaningYield").style.display = "none";
			
			document.getElementById("ingRatePerKg").readOnly = false;
			document.getElementById("ingYield").readOnly = false;
			
			if(mode == 1)
			{
			document.getElementById("ingRatePerKg").value = "";
			document.getElementById("ingYield").value = "";
			document.getElementById("cleanCost").value = "";
			document.getElementById("cleanYield").value = "";
			document.getElementById("ingFinalRate").value = "";
			}
		}
		
		else if(materialType == 2)
		{
			document.getElementById("cleanedIngredient").style.display = "block";
			document.getElementById("cleanedRate").style.display = "block";
			document.getElementById("cleanedYield").style.display = "block";
			document.getElementById("cleanedCost").style.display = "block";
			document.getElementById("finalRate").style.display = "block";
			document.getElementById("cleaningYield").style.display = "block";
			
			document.getElementById("ingRatePerKg").readOnly = true;
			document.getElementById("ingYield").readOnly = true;
			
			if(mode == 1)
			{
			document.getElementById("ingRatePerKg").value = "";
			document.getElementById("ingYield").value = "";
			document.getElementById("cleanCost").value = "";
			document.getElementById("cleanYield").value = "";
			document.getElementById("ingFinalRate").value = "";
			}
			
		}
	}
	
	function changeReadOnly(rawIngredientId)
	{
		//alert("hii");
		if(rawIngredientId !="")
		{
			document.getElementById("ingRatePerKg").readOnly = "true";
			document.getElementById("ingYield").readOnly = "true";
		}
	}
	
	function calcIngFinalRate()
	{
		var materialType = document.getElementById("materialType").value;
		var finalRate = 0;
		var finalYield = 0;
		var result = 0;
		
		if(materialType == 1)
		{
			var rate = document.getElementById("ingRatePerKg").value;
			var yield = document.getElementById("ingYield").value;
			
			if(rate!="" && yield!="")
			{
				finalRate = (rate/yield)*100;
			}
		}
		else if(materialType == 2)
		{
			var rate = document.getElementById("ingRatePerKg").value;
			var yield = document.getElementById("ingYield").value;
			var cleaningCost = document.getElementById("cleanCost").value;
			var cleaningYield = document.getElementById("cleanYield").value;
			
			if(rate!="" && yield!="")
			{
				if(cleaningCost == "")
				{
					cleaningCost = 0;
				}
				if(cleaningYield == "")
				{
					cleaningYield = 1;
				} 
				finalYield = yield * cleaningYield;
				result = (rate/finalYield)*100;
				finalRate = result + parseFloat(cleaningCost);
			}
		}
		
		var finalRateValue = number_format(finalRate, 2, '.', '');
		document.getElementById("ingFinalRate").value = finalRateValue;
	}
	
	function assignMaterialType(materialType)
	{
		if(materialType == 1)
		{
			document.getElementById("materialType").selectedIndex = "1";
			//document.getElementById("materialType").value = 1;
		}
		else if(materialType == 2)
		{
			document.getElementById("materialType").selectedIndex = "2";
			//document.getElementById("materialType").value = 24;
		}
	}