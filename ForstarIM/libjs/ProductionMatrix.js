function validateProductionMatrix(form)
{
	var prodName = form.prodName.value;
	var fillingWtPerPouch = form.fillingWtPerPouch.value;
	var prodQtyPerBtch = form.prodQtyPerBtch.value;
	var noOfPouch	= form.noOfPouch.value;
	var noOfHrsPrep = form.noOfHrsPrep.value;
	var noOfHrsCook = form.noOfHrsCook.value;
	var noOfHrsRetort = form.noOfHrsRetort.value;
	var boilerRequired = form.boilerRequired.value;
	var processType = form.processType.value;
		
	if (prodName=="") {
		alert("Please enter a Product Name.");
		form.prodName.focus();
		return false;
	}
	if (processType=="") {
		alert("Please enter a Product Type.");
		form.processType.focus();
		return false;
	}	
	if (fillingWtPerPouch=="") {
		alert("Please enter Filling Wt Per pouch.");
		form.fillingWtPerPouch.focus();
		return false;
	}	
	if (prodQtyPerBtch=="") {
		alert("Please enter Production Qty Per batch.");
		form.prodQtyPerBtch.focus();
		return false;
	}
	if (noOfPouch=="") {
		alert("Please enter No.of Pouches Per batch.");
		form.noOfPouch.focus();
		return false;
	}
	if (noOfHrsPrep=="") {
		alert("Please enter No.of Hours for Prep.");
		form.noOfHrsPrep.focus();
		return false;
	}
	if (noOfHrsCook=="") {
		alert("Please enter No.of Hours for Cooking.");
		form.noOfHrsCook.focus();
		return false;
	}
	if (noOfHrsRetort=="") {
		alert("Please enter No.of Hours for Retorting.");
		form.noOfHrsRetort.focus();
		return false;
	}
	if (boilerRequired=="") {
		alert("Please select Boiler Required Option.");
		form.boilerRequired.focus();
		return false;
	}

		
	if (!confirmSave()) {
		return false;
	}
	return true;
}

// Find Processed Wt per Batch in Kg
function calcProcessedWtPerBatch()
{	
	var fillingWtPerPouch	 = 0;
	var noOfPouch 		 = 0;	
	var processedWtPerBtch 	 = 0;

	if (document.getElementById("fillingWtPerPouch").value) fillingWtPerPouch = parseFloat(document.getElementById("fillingWtPerPouch").value);
	if (document.getElementById("noOfPouch").value) noOfPouch = parseFloat(document.getElementById("noOfPouch").value);
	processedWtPerBtch = fillingWtPerPouch * noOfPouch;
	if(!isNaN(processedWtPerBtch)) document.getElementById("processedWtPerBtch").value = number_format(processedWtPerBtch,0,'','');
	calcNumHrsForFillNSeal();
}

// No of Hours for Cooking
function calcCookingHrs()
{	
	var noOfHrsCook="";
	var noOfGravyCookers = parseFloat(document.getElementById("noOfGravyCookers").value);
	var kettles          = document.getElementById("kettles").value; 
	var hrsForCooking    = parseFloat(document.getElementById("hrsForCooking").value); 
	if(noOfGravyCookers!="" &&  kettles=="Y")
	{	
		noOfHrsCook=hrsForCooking/noOfGravyCookers;
	}
	else
	{
		noOfHrsCook=hrsForCooking/1;
	}
	if(noOfHrsCook!="")
	{
		document.getElementById("noOfHrsCook").value=number_format(noOfHrsCook,3,'.','');
	}
	/*if(noOfGravyCookers!="" &&  kettles=="Y")
	{	
		var noOfHrsCook= 1.25/noOfGravyCookers;
		document.getElementById("noOfHrsCook").value=number_format(noOfHrsCook,2,'.','');
	}
	*/
	calcNumHrsForFirstBtch();
}


// No of Hours for Filling & Sealing
function calcNumHrsForFillNSeal()
{
	var calcNumOfHrs = 0; var assignValue="";
	var fillingWtPerPouch = parseFloat(document.getElementById("fillingWtPerPouch").value);
	var noOfPouch	  = parseFloat(document.getElementById("noOfPouch").value);
	var noOfSealingMachines	  = parseFloat(document.getElementById("noOfSealingMachines").value);
	if(fillingWtPerPouch>1)
	{ 
		assignValue=50; 
	} 
	else 
	{ 
		if(fillingWtPerPouch>0.5)
		{ 
			assignValue=95; 
		} 
		else 
		{ 
			if(fillingWtPerPouch>0.3)
			{ 
				assignValue=180; 
			} 
			else 
			{ 
				if(fillingWtPerPouch>0.2)
				{ 
					assignValue=200; 
				} 
				else 
				{ 
					if(fillingWtPerPouch>0.1)
					{ 
						assignValue=250; 
					} 
					else 
					{
						assignValue=350; 
						
					} 
				} 
			} 
		} 
	}

	if(assignValue!="")
	{
		var  noOfHrsFill=noOfPouch/(parseFloat(assignValue)*parseFloat(noOfSealingMachines));
		document.getElementById("noOfHrsFill").value=number_format(noOfHrsFill,3,'.','');
	}
	calcNumHrsForFirstBtch();
 }

// No of Hours for First Batch
function calcNumHrsForFirstBtch()
{
	var totalHrsFirstBtch = 0; var noOfHrsCook=0; var noOfHrsFill=0; var noOfHrsRetort=0; var noOfHrsPrep=0;
	
	if(document.getElementById("noOfHrsPrep").value!="")
	{
		noOfHrsPrep= parseFloat(document.getElementById("noOfHrsPrep").value);
	}
	if(document.getElementById("noOfHrsCook").value!="")
	{
		noOfHrsCook= parseFloat(document.getElementById("noOfHrsCook").value);
	}
	if(document.getElementById("noOfHrsFill").value!="")
	{
		noOfHrsFill= parseFloat(document.getElementById("noOfHrsFill").value);
	}
	if(document.getElementById("noOfHrsRetort").value!="")
	{
		noOfHrsRetort= parseFloat(document.getElementById("noOfHrsRetort").value);
	}
	//alert("hii");
	totalHrsFirstBtch = noOfHrsPrep + noOfHrsCook + noOfHrsFill + noOfHrsRetort;
	//alert(noOfHrsPrep+"--"+noOfHrsCook+"--"+noOfHrsFill+"--"+noOfHrsRetort);
	if (!isNaN(totalHrsFirstBtch)) document.getElementById("noOfHrsFirstBtch").value = number_format(totalHrsFirstBtch,3,'.','');
	calcNumHrsForOtherBtch();
}

// No of Hours for other Batches
function calcNumHrsForOtherBtch()
{
	var totalHrsOtherBtch = 0; var noOfHrsFill=0; var noOfHrsRetort=0;

	var noOfHrsFirstBtch = parseFloat(document.getElementById("noOfHrsFirstBtch").value);
	if(document.getElementById("noOfHrsFill").value!="")
	{
		noOfHrsFill= parseFloat(document.getElementById("noOfHrsFill").value);
	}
	if(document.getElementById("noOfHrsRetort").value!="")
	{
		noOfHrsRetort= parseFloat(document.getElementById("noOfHrsRetort").value);
	}
	// =noOfHrsFirstBtch-(noOfHrsFill+noOfHrsRetort)
	totalHrsOtherBtch = (noOfHrsFirstBtch-(noOfHrsFill+noOfHrsRetort));
	//alert(noOfHrsFirstBtch+"-"+"("+noOfHrsFill+"+"+noOfHrsRetort+")");
	if (!isNaN(totalHrsOtherBtch)) document.getElementById("noOfHrsOtherBtch").value = number_format(totalHrsOtherBtch,3,'.','');
	calcNumBtchPerDay();
}

//No of Batches per Day

function calcNumBtchPerDay()
{
	var totalNumOfBtchPerDay = 0; var noOfHrsCook=0; var noOfHrsFill=0; var noOfHrsRetort=0; var noOfHrsPrep=0; var noOfHrsFilling=0;
	var noOfHoursPerShift = parseFloat(document.getElementById("noOfHoursPerShift").value);
	var noOfShifts		= parseFloat(document.getElementById("noOfShifts").value);
	var noOfRetorts		= parseFloat(document.getElementById("noOfRetorts").value);
	if(document.getElementById("noOfHrsPrep").value!="")
	{
		noOfHrsPrep= parseFloat(document.getElementById("noOfHrsPrep").value);
	}
	if(document.getElementById("noOfHrsCook").value!="")
	{
		noOfHrsCook= parseFloat(document.getElementById("noOfHrsCook").value);
	}
	if(document.getElementById("noOfHrsFill").value!="")
	{
		noOfHrsFill= parseFloat(document.getElementById("noOfHrsFill").value);
	}
	if(document.getElementById("noOfHrsRetort").value!="")
	{
		noOfHrsRetort= parseFloat(document.getElementById("noOfHrsRetort").value);
	}
	if(document.getElementById("noOfHrsFilling").value!="")
	{
		noOfHrsFilling= parseFloat(document.getElementById("noOfHrsFilling").value);
	}
	
	//alert(noOfHoursPerShift+"--"+noOfShifts+"--"+noOfHrsPrep+"--"+noOfHrsCook+"--"+noOfHrsFilling+"--"+noOfHrsFill+"--"+noOfHrsRetort+"--"+noOfRetorts);
	totalNumOfBtchPerDay =(((noOfHoursPerShift*noOfShifts)-noOfHrsPrep)/(noOfHrsCook+noOfHrsFilling+noOfHrsFill+noOfHrsRetort))*noOfRetorts;
	
	if (!isNaN(totalNumOfBtchPerDay)) document.getElementById("noOfBtchsPerDay").value = number_format(totalNumOfBtchPerDay,3,'.','');
	//calcDieselCostPerBtch();
}

// Diesel Cost per Batch

function  calcDieselCostPerBtch()
{
	//alert("hiii");
	var dieselCostPerBtch = 0; var boilerConstants=""; var boilerVal=""; 
	var boilerRequiredProcessing	= document.getElementById("boilerRequiredProcessing").value;
	var boilerRequired	= document.getElementById("boilerRequired").value;
	var dieselConsumptionOfBoiler = parseFloat(document.getElementById("dieselConsumptionOfBoiler").value);
	var dieselCostPerLitre	      = parseFloat(document.getElementById("dieselCostPerLitre").value);
	var noOfHrsFirstBtch	= parseFloat(document.getElementById("noOfHrsFirstBtch").value);
	var noOfHrsOtherBtch 	= parseFloat(document.getElementById("noOfHrsOtherBtch").value);
	var noOfBtchsPerDay	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	var noOfHrsRetort	= parseFloat(document.getElementById("noOfHrsRetort").value);
	var noOfHrsFilling=parseFloat(document.getElementById("noOfHrsFilling").value);
	var noOfHrsCook=parseFloat(document.getElementById("noOfHrsCook").value);
	
	if(boilerRequiredProcessing=="Y")
	{
		if(boilerRequired=="N")
		{
			boilerVal=0.5;
		}
		else
		{
			boilerVal=1;
		}
		//alert(boilerVal);
		if(boilerVal!="" && noOfHrsFilling!="")
		{
			boilerConstants=(noOfHrsRetort*boilerVal)+(noOfHrsFilling*0.7);
		}
		else
		{
			boilerConstants=noOfHrsRetort*boilerVal;
		}
		//dieselCostPerBtch=dieselConsumptionOfBoiler*dieselCostPerLitre*((noOfHrsFirstBtch-noOfHrsOtherBtch)+(noOfHrsOtherBtch*noOfBtchsPerDay));
	}
	else
	{
		if(boilerRequired=="Y")
		{
			boilerConstants=noOfHrsCook;
		}
		else
		{
			boilerConstants=0;
		}
		//dieselCostPerBtch="0";
	}
	//alert(dieselConsumptionOfBoiler+"--"+dieselCostPerLitre+"--"+boilerConstants);
	if(boilerConstants!="")
	{
		dieselCostPerBtch=parseFloat(dieselConsumptionOfBoiler)*parseFloat(dieselCostPerLitre)*parseFloat(boilerConstants);
	}

	
	if(!isNaN(dieselCostPerBtch)) document.getElementById("dieselCostPerBtch").value = number_format(dieselCostPerBtch,3,'.','');
	calcElectricCostPerBatch();
}

//Electricity Cost per Batch

function calcElectricCostPerBatch()
{
	var electricCostPerBtch = 0;
	var electricConsumptionPerDayUnit = parseFloat(document.getElementById("electricConsumptionPerDayUnit").value);
	var electricCostPerUnit = parseFloat(document.getElementById("electricCostPerUnit").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	//($electricConsumptionPerDayUnit*$electricCostPerUnit)/noOfBtchsPerDay;
	//alert(electricConsumptionPerDayUnit+"--"+electricCostPerUnit+"--"+noOfBtchsPerDay);
	electricCostPerBtch = (electricConsumptionPerDayUnit*electricCostPerUnit)/noOfBtchsPerDay;
	if (!isNaN(electricCostPerBtch)) document.getElementById("electricityCostPerBtch").value = number_format(electricCostPerBtch,3,'.','');	
	calcWaterCostPerBtch();
}



//Water Cost per Batch
function calcWaterCostPerBtch()
{
	var waterConsumptionPerRetort = 0;
	var waterCostPerBtch = 0;
	var boilerRequiredProcessing	= document.getElementById("boilerRequiredProcessing").value;
	var waterConsumptionPerRetortBatchUnit = parseFloat(document.getElementById("waterConsumptionPerRetortBatchUnit").value);
	var generalWaterConsumptionPerDayUnit = parseFloat(document.getElementById("generalWaterConsumptionPerDayUnit").value);
	var noOfShifts = parseFloat(document.getElementById("noOfShifts").value);
	var noOfWorkingDaysInMonth = parseInt(document.getElementById("noOfWorkingDaysInMonth").value);
	var costPerLitreOfWater = parseFloat(document.getElementById("costPerLitreOfWater").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	
	if (boilerRequiredProcessing=='Y') {
		waterConsumptionPerRetort = waterConsumptionPerRetortBatchUnit;
	} 
	else
	{
		waterConsumptionPerRetort=0;
	}
	//alert(waterConsumptionPerRetort+"--"+generalWaterConsumptionPerDayUnit+"--"+noOfShifts+"--"+noOfBtchsPerDay+"--"+costPerLitreOfWater);
	waterCostPerBtch = (waterConsumptionPerRetort+((generalWaterConsumptionPerDayUnit*noOfShifts)/noOfBtchsPerDay))*costPerLitreOfWater;
	if (!isNaN(waterCostPerBtch)) document.getElementById("waterCostPerBtch").value = number_format(waterCostPerBtch,3,'.','');
	calcGasCostPerBtch();
}


//Gas cost per Batch
function calcGasCostPerBtch()
{
	var gasCostPerBtch = 0; var gasValue="";
	var gasPerCylinderPerDay = parseFloat(document.getElementById("gasPerCylinderPerDay").value);
	var costOfCylinder = parseFloat(document.getElementById("costOfCylinder").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	//($costOfCylinder*$gasPerCylinderPerDay)/noOfBtchsPerDay
	var gasRequired	= document.getElementById("gasRequired").value;
	var noOfHrsFilling =0;	
	if(document.getElementById("noOfHrsFilling").value!="")
	{
		noOfHrsFilling= parseFloat(document.getElementById("noOfHrsFilling").value);
	}
	if(gasRequired=="Y")
	{ 
		if(noOfHrsFilling=="" || noOfHrsFilling=="0") 
		{ 
			gasValue=1;
		} 
		else 
		{
			gasValue=3;
		} 
		if(gasValue!="")
		{
			gasCostPerBtch=(costOfCylinder*gasPerCylinderPerDay)/noOfBtchsPerDay/gasValue;
		}
	} 
	else 
	{ 
		gasCostPerBtch=0;
	} 

	gasCostPerBtch = (costOfCylinder*gasPerCylinderPerDay)/noOfBtchsPerDay;
	if (!isNaN(gasCostPerBtch)) document.getElementById("gasCostPerBtch").value = number_format(gasCostPerBtch,3,'.','');
	//Total Fuel Cost
	calcTotalFuelCostPerBatch();
}

//Total Fuel cost per Batch
function calcTotalFuelCostPerBatch()
{
	var totalFuelCost = 0;
	var dieselCostPerBtch = parseFloat(document.getElementById("dieselCostPerBtch").value);
	var electricityCostPerBtch = parseFloat(document.getElementById("electricityCostPerBtch").value);
	var waterCostPerBtch 	= parseFloat(document.getElementById("waterCostPerBtch").value);
	var gasCostPerBtch = parseFloat(document.getElementById("gasCostPerBtch").value);
	//alert(electricityCostPerBtch+"--"+dieselCostPerBtch+"--"+waterCostPerBtch+"--"+gasCostPerBtch);
	totalFuelCost = dieselCostPerBtch+electricityCostPerBtch+waterCostPerBtch+gasCostPerBtch;
	if (!isNaN(totalFuelCost)) document.getElementById("totFuelCostPerBtch").value = number_format(totalFuelCost,3,'.','');	
	calcMaintCostPerBtch();
}

//Maint/Cons. Cost per Batch
function calcMaintCostPerBtch()
{
	var maintenanceCost = parseFloat(document.getElementById("maintenanceCost").value);
	var consumablesCost = parseFloat(document.getElementById("consumablesCost").value);
	var labCost = parseFloat(document.getElementById("labCost").value);		
	var noOfWorkingDaysInMonth = parseFloat(document.getElementById("noOfWorkingDaysInMonth").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	//alert(maintenanceCost+"--"+consumablesCost+"--"+labCost+"--"+noOfWorkingDaysInMonth+"--"+noOfBtchsPerDay);
	maintCostPerBtch = (maintenanceCost+consumablesCost+labCost)/noOfWorkingDaysInMonth/noOfBtchsPerDay;
	if (!isNaN(maintCostPerBtch)) document.getElementById("maintCostPerBtch").value = number_format(maintCostPerBtch,3,'.','');
	calcVariableManPwerCostPerBtch();
}

//Variable Manpower Cost per Batch
function calcVariableManPwerCostPerBtch()
{
	var variManPowerCost = 0; var boilerValue=""; var varManPowerValue = 0;

	var variableManPowerCostPerDay = parseFloat(document.getElementById("variableManPowerCostPerDay").value);
	var noOfWorkingDaysInMonth = parseFloat(document.getElementById("noOfWorkingDaysInMonth").value);
	var noOfShifts = parseFloat(document.getElementById("noOfShifts").value);
	
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	var boilerRequired	= document.getElementById("boilerRequired").value;
	
	if(boilerRequired=="N")
	{
		boilerValue=0.8;
	}
	else
	{
		boilerValue=1;
	} 
	
	varManPowerValue = (variableManPowerCostPerDay/noOfWorkingDaysInMonth)*noOfShifts/1;
	//alert("varManPowerValue "+varManPowerValue);
	variManPowerCost = (varManPowerValue/noOfBtchsPerDay)*boilerValue;
	
	if (!isNaN(variManPowerCost)) document.getElementById("variManPwerCostPerBtch").value = number_format(variManPowerCost,2,'.','');
	 calcMktgTeamCostPerBtch();
}

//Mktg Team cost per pouch
function calcMktgTeamCostPerBtch()
{
	var mktgTeamCostPerBtch = 0;
	//var totalMktgCost = 0;
	var totalMktgCostTCost = parseFloat(document.getElementById("totalTravelCost").value);
	var noOfWorkingDaysInMonth = parseInt(document.getElementById("noOfWorkingDaysInMonth").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	var noOfPouch		= parseInt(document.getElementById("noOfPouch").value);
	//alert(totalMktgCostTCost+"--"+noOfWorkingDaysInMonth+"--"+noOfPouch+"--"+noOfBtchsPerDay);

	mktgTeamCostPerBtch = totalMktgCostTCost/(noOfWorkingDaysInMonth*noOfPouch*noOfBtchsPerDay);
	if (!isNaN(mktgTeamCostPerBtch)) document.getElementById("mktgTeamCostPerPouch").value = number_format(mktgTeamCostPerBtch,3,'.','');
	calcCoordinationCostPerPouch();
}

//CoordinationCostPerPouch
function calcCoordinationCostPerPouch()
{
	var coordinationCostPerBtch = 0;
	var totalCoordinationCost = parseFloat(document.getElementById("totalCoordinationCost").value);
	var noOfWorkingDaysInMonth = parseFloat(document.getElementById("noOfWorkingDaysInMonth").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	var noOfPouch		= parseInt(document.getElementById("noOfPouch").value);
	//$E$190/($D$91*G14*T14)
	//alert(totalCoordinationCost+"--"+noOfWorkingDaysInMonth+"--"+noOfPouch+"--"+noOfBtchsPerDay);
	coordinationCostPerBtch = totalCoordinationCost/(noOfWorkingDaysInMonth*noOfPouch*noOfBtchsPerDay);
	if (!isNaN(coordinationCostPerBtch)) document.getElementById("coordinationCostPerPouch").value = number_format(coordinationCostPerBtch,3,'.','');
	calcMktgTravelCost();
}


//Mktg Travel Cost
function calcMktgTravelCost()
{
	var mktgTravelCost = 0;
	var totalTravelCost = parseFloat(document.getElementById("totalMktgCostTCost").value);
	var noOfWorkingDaysInMonth = parseFloat(document.getElementById("noOfWorkingDaysInMonth").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	var noOfPouch		= parseFloat(document.getElementById("noOfPouch").value);
	//$E$202/($D$91*G14*T14)
	mktgTravelCost = totalTravelCost/(noOfWorkingDaysInMonth*noOfPouch*noOfBtchsPerDay);

	if (!isNaN(mktgTravelCost)) document.getElementById("mktgTravelCost").value = number_format(mktgTravelCost,3,'.','');
	calcAdvCostPerPouch();
}

//Advt Cost per pouch
function calcAdvCostPerPouch()
{
	var advCostPerPouch 	= 0;
	var advtCostPerMonth 	= parseFloat(document.getElementById("advtCostPerMonth").value);
	var noOfWorkingDaysInMonth = parseInt(document.getElementById("noOfWorkingDaysInMonth").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	var noOfPouch		= parseInt(document.getElementById("noOfPouch").value);
	
	advCostPerPouch = advtCostPerMonth/(noOfWorkingDaysInMonth*noOfPouch*noOfBtchsPerDay);

	if (!isNaN(advCostPerPouch)) document.getElementById("adCostPerPouch").value = number_format(advCostPerPouch,3,'.','');
	facilityCostPerDay();
}

//facility Cost Per Day
function facilityCostPerDay()
{
	var facCostPerDay 	= 0;
	var totFuelCostPerBtch 	= parseFloat(document.getElementById("totFuelCostPerBtch").value);
	var maintCostPerBtch 	= parseFloat(document.getElementById("maintCostPerBtch").value);
	var noOfBtchsPerDay     = parseFloat(document.getElementById("noOfBtchsPerDay").value);
	facCostPerDay =(totFuelCostPerBtch+maintCostPerBtch)*noOfBtchsPerDay;
	if (!isNaN(facCostPerDay)) document.getElementById("facilityCostPerDay").value = number_format(facCostPerDay,3,'.','');
}

