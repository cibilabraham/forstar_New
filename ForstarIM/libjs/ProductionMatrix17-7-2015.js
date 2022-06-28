function validateProductionMatrix(form)
{
	var prodCode = form.prodCode.value;
	var prodName = form.prodName.value;
	var fillingWtPerPouch = form.fillingWtPerPouch.value;
	var prodQtyPerBtch = form.prodQtyPerBtch.value;
	var noOfPouch	= form.noOfPouch.value;
	var noOfHrsPrep = form.noOfHrsPrep.value;
	var noOfHrsCook = form.noOfHrsCook.value;
	var noOfHrsRetort = form.noOfHrsRetort.value;
	var boilerRequired = form.boilerRequired.value;

	if (prodCode=="") {
		alert("Please enter a Product Code.");
		form.prodCode.focus();
		return false;
	}	
	if (prodName=="") {
		alert("Please enter a Product Name.");
		form.prodName.focus();
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

	// Mktg Team Cost
	calcMktgTeamCostPerBtch();
	// Mktg Travel Cost
	calcMktgTravelCost();
	// Ad Cost
	calcAdvCostPerPouch();
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
		document.getElementById("noOfHrsFill").value=number_format(noOfHrsFill,2,'.','');
	}


}


/*function calcNumHrsForFillNSeal()
{
	var calcNumOfHrs = 0;
	

	var noOfMinutesForSealing = parseFloat(document.getElementById("noOfMinutesForSealing").value);
	var noOfMinutesPerHour	  = parseFloat(document.getElementById("noOfMinutesPerHour").value);
	var noOfPouchesSealed	  = parseFloat(document.getElementById("noOfPouchesSealed").value);	
	var noOfSealingMachines	  = parseFloat(document.getElementById("noOfSealingMachines").value);
	var noOfPouch		  = parseInt(document.getElementById("noOfPouch").value); 	

	//Fmula ($noOfMinutesForSealing/$noOfMinutesPerHour)+(((($noOfPouchesSealed/$noOfMinutesPerHour)/$noOfMinutesPerHour)*No.of pouch)/$noOfSealingMachines);
	
	calcNumOfHrs = (noOfMinutesForSealing/noOfMinutesPerHour)+((((noOfPouchesSealed/noOfMinutesPerHour)/noOfMinutesPerHour)*noOfPouch)/noOfSealingMachines);

	if (!isNaN(calcNumOfHrs)) document.getElementById("noOfHrsFill").value = number_format(calcNumOfHrs,2,'.','');
}*/

// No of Hours for First Batch
function calcNumHrsForFirstBtch()
{
	var totalHrsFirstBtch = 0;

	var noOfHrsPrep = parseFloat(document.getElementById("noOfHrsPrep").value);
	var noOfHrsCook = parseFloat(document.getElementById("noOfHrsCook").value);
	var noOfHrsFill = parseFloat(document.getElementById("noOfHrsFill").value);
	var noOfHrsRetort = parseFloat(document.getElementById("noOfHrsRetort").value);
	
	totalHrsFirstBtch = noOfHrsPrep + noOfHrsCook + noOfHrsFill + noOfHrsRetort;

	if (!isNaN(totalHrsFirstBtch)) document.getElementById("noOfHrsFirstBtch").value = number_format(totalHrsFirstBtch,2,'.','');
}

// No of Hours for other Batches
function calcNumHrsForOtherBtch()
{
	var totalHrsOtherBtch = 0;

	var noOfHrsFirstBtch = parseFloat(document.getElementById("noOfHrsFirstBtch").value);
	var noOfHrsFill = parseFloat(document.getElementById("noOfHrsFill").value);
	var noOfHrsRetort = parseFloat(document.getElementById("noOfHrsRetort").value);
	
	// =noOfHrsFirstBtch-(noOfHrsFill+noOfHrsRetort)
	totalHrsOtherBtch = (noOfHrsFirstBtch-(noOfHrsFill+noOfHrsRetort));
	//alert(noOfHrsFirstBtch+"-"+"("+noOfHrsFill+"+"+noOfHrsRetort+")");
	if (!isNaN(totalHrsOtherBtch)) document.getElementById("noOfHrsOtherBtch").value = number_format(totalHrsOtherBtch,2,'.','');
}

//No of Batches per Day

function calcNumBtchPerDay()
{
	var totalNumOfBtchPerDay = 0;
	var noOfHoursPerShift = parseFloat(document.getElementById("noOfHoursPerShift").value);
	var noOfShifts		= parseFloat(document.getElementById("noOfShifts").value);
	var noOfRetorts		= parseFloat(document.getElementById("noOfRetorts").value);
	var noOfHrsCook		= parseFloat(document.getElementById("noOfHrsCook").value);
	var noOfHrsFill		= parseFloat(document.getElementById("noOfHrsFill").value);
	var noOfHrsRetort		= parseFloat(document.getElementById("noOfHrsRetort").value);
	var noOfHrsPrep= parseFloat(document.getElementById("noOfHrsPrep").value);
	var noOfHrsFilling		= parseFloat(document.getElementById("noOfHrsFilling").value);
	//var noOfHrsFirstBtch	= parseFloat(document.getElementById("noOfHrsFirstBtch").value);
	//var noOfHrsOtherBtch 	= parseFloat(document.getElementById("noOfHrsOtherBtch").value);
	//var noOfHrsFilling		= parseFloat(document.getElementById("noOfHrsFilling").value);
	
	//((($noOfHoursPerShift-noOfHrsFirstBtch)/noOfHrsOtherBtch)+1)*$noOfRetorts*$noOfShifts;
	//totalNumOfBtchPerDay = (((noOfHoursPerShift-noOfHrsFirstBtch)/noOfHrsOtherBtch)+1)*noOfRetorts*noOfShifts;
	//if (!isNaN(totalNumOfBtchPerDay)) document.getElementById("noOfBtchsPerDay").value = number_format((Math.abs(totalNumOfBtchPerDay)),2,'.','');
	
	//alert(noOfHoursPerShift+"--"+noOfShifts+"--"+noOfHrsPrep+"--"+noOfHrsCook+"--"+noOfHrsFilling+"--"+noOfHrsFill+"--"+noOfHrsRetort+"--"+noOfRetorts);
	totalNumOfBtchPerDay =(((noOfHoursPerShift*noOfShifts)-noOfHrsPrep)/(noOfHrsCook+noOfHrsFilling+noOfHrsFill+noOfHrsRetort))*noOfRetorts;
	
	if (!isNaN(totalNumOfBtchPerDay)) document.getElementById("noOfBtchsPerDay").value = number_format(totalNumOfBtchPerDay,2,'.','');
	}

/*
function calcNumBtchPerDay()
{
	var totalNumOfBtchPerDay = 0;

	var noOfHoursPerShift = parseFloat(document.getElementById("noOfHoursPerShift").value);
	var noOfRetorts		= parseFloat(document.getElementById("noOfRetorts").value);
	var noOfShifts		= parseInt(document.getElementById("noOfShifts").value);
	var noOfHrsFirstBtch	= parseFloat(document.getElementById("noOfHrsFirstBtch").value);
	var noOfHrsOtherBtch 	= parseFloat(document.getElementById("noOfHrsOtherBtch").value);
	//((($noOfHoursPerShift-noOfHrsFirstBtch)/noOfHrsOtherBtch)+1)*$noOfRetorts*$noOfShifts;
	totalNumOfBtchPerDay = (((noOfHoursPerShift-noOfHrsFirstBtch)/noOfHrsOtherBtch)+1)*noOfRetorts*noOfShifts;
	if (!isNaN(totalNumOfBtchPerDay)) document.getElementById("noOfBtchsPerDay").value = number_format((Math.abs(totalNumOfBtchPerDay)),2,'.','');
	//Electricity cost per Batch
	calcElectricCostPerBatch();
	// Water Cost
	calcWaterCostPerBtch();
	//Gas Cost
	calcGasCostPerBtch();
	//Total Fuel Cost
	calcTotalFuelCostPerBatch();
	// maintence Cost 
	calcMaintCostPerBtch();
	// Variable man Power Cost
	calcVariableManPwerCostPerBtch();
	// Mktg Team Cost
	calcMktgTeamCostPerBtch();
	// mktg Travel Cost
	calcMktgTravelCost();
	//Ad Cost
	calcAdvCostPerPouch();
}
*/

// Diesel Cost per Batch

function  calcDieselCostPerBtch()
{
	var dieselCostPerBtch = 0; var boilerConstants="";
	var boilerRequiredProcessing	= document.getElementById("boilerRequiredProcessing").value;
	var boilerRequired	= document.getElementById("boilerRequired").value;
	var dieselConsumptionOfBoiler = parseFloat(document.getElementById("dieselConsumptionOfBoiler").value);
	var dieselCostPerLitre	      = parseFloat(document.getElementById("dieselCostPerLitre").value);
	var noOfHrsRetort		= parseFloat(document.getElementById("noOfHrsRetort").value);
	var noOfHrsFilling		= parseFloat(document.getElementById("noOfHrsFilling").value);
	var noOfHrsCook		= parseFloat(document.getElementById("noOfHrsCook").value);


	var noOfHrsFirstBtch	= parseFloat(document.getElementById("noOfHrsFirstBtch").value);
	var noOfHrsOtherBtch 	= parseFloat(document.getElementById("noOfHrsOtherBtch").value);
	var noOfBtchsPerDay	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	//Boiler=Y : $dieselConsumptionOfBoiler* $dieselCostPerLitre* (noOfHrsFirstBtch+ (noOfHrsOtherBtch* (noOfBtchsPerDay-1))))/ noOfBtchsPerDay;
	//alert("hii");

	
	if(boilerRequiredProcessing!="")
	{
		
		if(boilerRequiredProcessing=="Y")
		{
			dieselCostPerBtch=dieselConsumptionOfBoiler*dieselCostPerLitre*((noOfHrsFirstBtch-noOfHrsOtherBtch)+(noOfHrsOtherBtch*noOfBtchsPerDay));
		}
		else
		{
			dieselCostPerBtch="0";
		}

	//alert(dieselConsumptionOfBoiler+"--"+dieselCostPerLitre+"--"+boilerConstants);
		if(dieselCostPerBtch!="")
		{
			dieselCostPerBtch=parseFloat(dieselConsumptionOfBoiler)*parseFloat(dieselCostPerLitre)*parseFloat(boilerConstants);
		}
	}
	
	if(!isNaN(dieselCostPerBtch)) document.getElementById("dieselCostPerBtch").value = number_format(dieselCostPerBtch,2,'.','');

	calcElectricCostPerBatch();
	// Water Cost
	calcWaterCostPerBtch();
	 calcGasCostPerBtch();
	//Total Fuel Cost
	calcTotalFuelCostPerBatch();
	calcMaintCostPerBtch();
	calcVariableManPwerCostPerBtch();
	calcMktgTeamCostPerBtch();
	calcAdvCostPerPouch();
	facilityCostPerDay();
	calcCoordinationCostPerPouch();
	calcMktgTravelCost();
	
}

/*
function  calcDieselCostPerBtch()
{
	var dieselCostPerBtch = 0; var boilerConstants="";
	var boilerRequiredProcessing	= document.getElementById("boilerRequiredProcessing").value;
	var boilerRequired	= document.getElementById("boilerRequired").value;
	var dieselConsumptionOfBoiler = parseFloat(document.getElementById("dieselConsumptionOfBoiler").value);
	var dieselCostPerLitre	      = parseFloat(document.getElementById("dieselCostPerLitre").value);
	var dieselCostPerLitre	      = parseFloat(document.getElementById("dieselCostPerLitre").value);
	var noOfHrsRetort		= parseFloat(document.getElementById("noOfHrsRetort").value);
	var noOfHrsFilling		= parseFloat(document.getElementById("noOfHrsFilling").value);
	var noOfHrsCook		= parseFloat(document.getElementById("noOfHrsCook").value);


	var noOfHrsFirstBtch	= parseFloat(document.getElementById("noOfHrsFirstBtch").value);
	var noOfHrsOtherBtch 	= parseFloat(document.getElementById("noOfHrsOtherBtch").value);
	var noOfBtchsPerDay	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	//Boiler=Y : $dieselConsumptionOfBoiler* $dieselCostPerLitre* (noOfHrsFirstBtch+ (noOfHrsOtherBtch* (noOfBtchsPerDay-1))))/ noOfBtchsPerDay;
	//alert("hii");

	IF(S14="Y";($D$95*$C$96*((N14-O14)+(O14*T14))))/T14


	if(boilerRequiredProcessing!="")
	{
		
		if(boilerRequiredProcessing=="Y")
		{ 
			//alert(noOfHrsRetort+"--"+noOfHrsFilling);
			if(noOfHrsFilling!=0 || noOfHrsFilling!="")
			{
				boilerConstants=parseFloat(noOfHrsRetort)+parseFloat(noOfHrsFilling*0.7);
			}
			else
			{
				boilerConstants=parseFloat(noOfHrsRetort)+0.7;
			}
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
		}
		//alert(dieselConsumptionOfBoiler+"--"+dieselCostPerLitre+"--"+boilerConstants);
		if(boilerConstants!="")
		{
			dieselCostPerBtch=parseFloat(dieselConsumptionOfBoiler)*parseFloat(dieselCostPerLitre)*parseFloat(boilerConstants);
		}
	}
	
	if(!isNaN(dieselCostPerBtch)) document.getElementById("dieselCostPerBtch").value = number_format(dieselCostPerBtch,2,'.','');

	calcElectricCostPerBatch();
	// Water Cost
	calcWaterCostPerBtch();
	 calcGasCostPerBtch();
	//Total Fuel Cost
	calcTotalFuelCostPerBatch();
	calcMaintCostPerBtch();
	calcVariableManPwerCostPerBtch();
	calcMktgTeamCostPerBtch();
	calcAdvCostPerPouch();
	facilityCostPerDay();

	calcCoordinationCostPerPouch();
	
}
*/

/*
function  calcDieselCostPerBtch()
{
	var dieselCostPerBtch = 0;
	var boilerRequired	= document.getElementById("boilerRequired").value;
	var dieselConsumptionOfBoiler = parseFloat(document.getElementById("dieselConsumptionOfBoiler").value);
	var dieselCostPerLitre	      = parseFloat(document.getElementById("dieselCostPerLitre").value);
	var noOfHrsFirstBtch	= parseFloat(document.getElementById("noOfHrsFirstBtch").value);
	var noOfHrsOtherBtch 	= parseFloat(document.getElementById("noOfHrsOtherBtch").value);
	var noOfBtchsPerDay	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	//Boiler=Y : $dieselConsumptionOfBoiler* $dieselCostPerLitre* (noOfHrsFirstBtch+ (noOfHrsOtherBtch* (noOfBtchsPerDay-1))))/ noOfBtchsPerDay;
	if (boilerRequired=='Y') {
		dieselCostPerBtch = (dieselConsumptionOfBoiler*dieselCostPerLitre*(noOfHrsFirstBtch+(noOfHrsOtherBtch*(noOfBtchsPerDay-1))))/noOfBtchsPerDay;
	} 
	if(!isNaN(dieselCostPerBtch)) document.getElementById("dieselCostPerBtch").value = number_format(dieselCostPerBtch,2,'.','');

	// Water Cost
	calcWaterCostPerBtch();
	//Total Fuel Cost
	calcTotalFuelCostPerBatch();
}*/

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
	if (!isNaN(electricCostPerBtch)) document.getElementById("electricityCostPerBtch").value = number_format(electricCostPerBtch,2,'.','');	
}

/*
function calcElectricCostPerBatch()
{
	var electricCostPerBtch = 0;
	var electricConsumptionPerDayUnit = parseFloat(document.getElementById("electricConsumptionPerDayUnit").value);
	var electricCostPerUnit = parseFloat(document.getElementById("electricCostPerUnit").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	//($electricConsumptionPerDayUnit*$electricCostPerUnit)/noOfBtchsPerDay;
	electricCostPerBtch = (electricConsumptionPerDayUnit*electricCostPerUnit)/noOfBtchsPerDay;
	if (!isNaN(electricCostPerBtch)) document.getElementById("electricityCostPerBtch").value = number_format(electricCostPerBtch,2,'.','');	
}
*/


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
	if (!isNaN(waterCostPerBtch)) document.getElementById("waterCostPerBtch").value = number_format(waterCostPerBtch,2,'.','');
}



/*
function calcWaterCostPerBtch()
{
	var waterConsumptionPerRetort = 0;
	var waterCostPerBtch = 0;

	var boilerRequired	= document.getElementById("boilerRequired").value;
	var waterConsumptionPerRetortBatchUnit = parseFloat(document.getElementById("waterConsumptionPerRetortBatchUnit").value);
	var generalWaterConsumptionPerDayUnit = parseFloat(document.getElementById("generalWaterConsumptionPerDayUnit").value);
	var noOfShifts = parseInt(document.getElementById("noOfShifts").value);
	var noOfWorkingDaysInMonth = parseInt(document.getElementById("noOfWorkingDaysInMonth").value);
	var costPerLitreOfWater = parseFloat(document.getElementById("costPerLitreOfWater").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	
	if (boilerRequired=='Y') {
		waterConsumptionPerRetort = waterConsumptionPerRetortBatchUnit;
	} 
	
	waterCostPerBtch = ((waterConsumptionPerRetort)+((generalWaterConsumptionPerDayUnit * noOfShifts)/noOfWorkingDaysInMonth/noOfBtchsPerDay)) * costPerLitreOfWater;
	if (!isNaN(waterCostPerBtch)) document.getElementById("waterCostPerBtch").value = number_format(waterCostPerBtch,2,'.','');
}
*/

//Gas cost per Batch
function calcGasCostPerBtch()
{
	var gasCostPerBtch = 0; var gasValue="";
	var gasPerCylinderPerDay = parseFloat(document.getElementById("gasPerCylinderPerDay").value);
	var costOfCylinder = parseFloat(document.getElementById("costOfCylinder").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	//($costOfCylinder*$gasPerCylinderPerDay)/noOfBtchsPerDay
	var gasRequired	= document.getElementById("gasRequired").value;
	var noOfHrsFilling 	= parseFloat(document.getElementById("noOfHrsFilling").value);

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
	if (!isNaN(gasCostPerBtch)) document.getElementById("gasCostPerBtch").value = number_format(gasCostPerBtch,2,'.','');
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
	if (!isNaN(totalFuelCost)) document.getElementById("totFuelCostPerBtch").value = number_format(totalFuelCost,2,'.','');	
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
	if (!isNaN(maintCostPerBtch)) document.getElementById("maintCostPerBtch").value = number_format(maintCostPerBtch,2,'.','');
}

//Variable Manpower Cost per Batch
function calcVariableManPwerCostPerBtch()
{
	var variManPowerCost = 0; var boilerValue="";

	var variableManPowerCostPerDay = parseFloat(document.getElementById("variableManPowerCostPerDay").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	var boilerRequired	= document.getElementById("boilerRequired").value;
	//($C$152/T14)*IF(Q14="N";(0.8);1)
	if(boilerRequired=="N")
	{
		boilerValue=0.8;
	}
	else
	{
		boilerValue=1;
	}
	variManPowerCost = (variableManPowerCostPerDay/noOfBtchsPerDay)*boilerValue;
	if (!isNaN(variManPowerCost)) document.getElementById("variManPwerCostPerBtch").value = number_format(variManPowerCost,2,'.','');
}

//Mktg Team cost per pouch
function calcMktgTeamCostPerBtch()
{
	var mktgTeamCostPerBtch = 0;
	var totalMktgCostTCost = parseFloat(document.getElementById("totalMktgCostTCost").value);
	var noOfWorkingDaysInMonth = parseInt(document.getElementById("noOfWorkingDaysInMonth").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	var noOfPouch		= parseInt(document.getElementById("noOfPouch").value);
	//alert(totalMktgCostTCost+"--"+noOfWorkingDaysInMonth+"--"+noOfPouch+"--"+noOfBtchsPerDay);
//	$E$184/($D$91*G14*T14)
	mktgTeamCostPerBtch = totalMktgCostTCost/(noOfWorkingDaysInMonth*noOfPouch*noOfBtchsPerDay);
	if (!isNaN(mktgTeamCostPerBtch)) document.getElementById("mktgTeamCostPerPouch").value = number_format(mktgTeamCostPerBtch,2,'.','');
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
	if (!isNaN(coordinationCostPerBtch)) document.getElementById("coordinationCostPerPouch").value = number_format(coordinationCostPerBtch,2,'.','');
}



//Mktg Travel Cost
function calcMktgTravelCost()
{
	var mktgTravelCost = 0;
	var totalTravelCost = parseFloat(document.getElementById("totalTravelCost").value);
	var noOfWorkingDaysInMonth = parseFloat(document.getElementById("noOfWorkingDaysInMonth").value);
	var noOfBtchsPerDay 	= parseFloat(document.getElementById("noOfBtchsPerDay").value);
	var noOfPouch		= parseFloat(document.getElementById("noOfPouch").value);
	//$E$202/($D$91*G14*T14)
	mktgTravelCost = totalTravelCost/(noOfWorkingDaysInMonth*noOfPouch*noOfBtchsPerDay);

	if (!isNaN(mktgTravelCost)) document.getElementById("mktgTravelCost").value = number_format(mktgTravelCost,2,'.','');
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

	if (!isNaN(advCostPerPouch)) document.getElementById("adCostPerPouch").value = number_format(advCostPerPouch,2,'.','');
}

//facility Cost Per Day
function facilityCostPerDay()
{
	var facCostPerDay 	= 0;
	var totFuelCostPerBtch 	= parseFloat(document.getElementById("totFuelCostPerBtch").value);
	var maintCostPerBtch 	= parseFloat(document.getElementById("maintCostPerBtch").value);
	var noOfBtchsPerDay     = parseFloat(document.getElementById("noOfBtchsPerDay").value);
	facCostPerDay =(totFuelCostPerBtch+maintCostPerBtch)*noOfBtchsPerDay;

	if (!isNaN(facCostPerDay)) document.getElementById("facilityCostPerDay").value = number_format(facCostPerDay,2,'.','');
}
/*
function calcProcessedWtPerBatch(firstField, secondField, ansField)
{
	var finalValue 	 = 	0;
	var secondValue = 	0;
	var firstValue	 =	0;	

	if(document.getElementById(firstField).value)
		firstValue	=	parseFloat(document.getElementById(firstField).value);
	if(document.getElementById(secondField).value)
		secondValue	=	parseFloat(document.getElementById(secondField).value);

	finalValue = firstValue * secondValue;

	if(!isNaN(finalValue))
		document.getElementById(ansField).value = number_format(finalValue,0,'','');
}
*/

// No of Hours for Cooking
function calcCookingHrs()
{	
	
	var noOfGravyCookers = parseFloat(document.getElementById("noOfGravyCookers").value);
	if(noOfGravyCookers!="")
	{	
		var noOfHrsCook= 1.25/noOfGravyCookers;
		document.getElementById("noOfHrsCook").value=number_format(noOfHrsCook,2,'.','');
	}
}