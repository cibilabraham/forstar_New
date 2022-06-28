	function validateProductionMatrixMaster(form)
	{
		var prodMatrixMasterId = document.getElementById("hidProductionMatrixMasterId").value;

		if (prodMatrixMasterId!="") {
			var rateConfirmMsg = "Do you want to save this to new Rate list?\nPlease click on OK to create new Rate List, or click CANCEL";
			
			if (confirm(rateConfirmMsg)) {
				document.getElementById("newRateList").value = 'Y';
			} else document.getElementById("newRateList").value = 'N';
		}

		if (!confirmSave()) return false;
		else return true;
	}

//Calculate Electric consumption per day
function calcElectricConsumptionPerDayUnit()
{
	var calcElectricConsumption = 0;

	var noOfShiftsUnit		= document.getElementById("noOfShiftsUnit").value;	
	var electricConsumptionPerShift = document.getElementById("electricConsumptionPerShift").value;
	calcElectricConsumption	= parseFloat(electricConsumptionPerShift) * parseFloat(noOfShiftsUnit);

	if (!isNaN(calcElectricConsumption)) {
		document.getElementById("electricConsumptionPerDayUnit").value =  calcElectricConsumption;
	}	
}

// Claculate gas Per Cylinder Per Day
function calcGasPerCylinderPerDay()
{
	var calcGasPerCylinderPerDay = 0;
	var noOfShiftsUnit			= document.getElementById("noOfShiftsUnit").value;	
	var noOfRetortsUnit			= document.getElementById("noOfRetortsUnit").value;		
	var noOfCylindersPerShiftPerRetort 	= document.getElementById("noOfCylindersPerShiftPerRetort").value;
	calcGasPerCylinderPerDay = parseFloat(noOfCylindersPerShiftPerRetort)*parseFloat(noOfShiftsUnit)*parseFloat(noOfRetortsUnit);

	if (!isNaN(calcGasPerCylinderPerDay)) {
		document.getElementById("gasPerCylinderPerDay").value =  number_format(calcGasPerCylinderPerDay,2,'.','');
	}	
}

//Calculate maintance Cost
function calcMaintenanceCost()
{
	var calcMaintenanceCost = 0;
	var noOfShiftsUnit			= document.getElementById("noOfShiftsUnit").value;	
	var noOfRetortsUnit			= document.getElementById("noOfRetortsUnit").value;
	var noOfWorkingDaysInMonthUnit		= document.getElementById("noOfWorkingDaysInMonthUnit").value;
	var maintenanceCostPerRetortPerShift	= document.getElementById("maintenanceCostPerRetortPerShift").value;	
	 //Maintance cost per retort per shift * No.of Shifts * No.of Retorts* No.of Working days in month
	calcMaintenanceCost = parseFloat(maintenanceCostPerRetortPerShift) * parseFloat(noOfShiftsUnit)* parseFloat(noOfRetortsUnit) * parseFloat(noOfWorkingDaysInMonthUnit);
	if (!isNaN(calcMaintenanceCost)) {
		document.getElementById("maintenanceCost").value =  number_format(calcMaintenanceCost,0,'','');
	}				
}

// Calculate consumablesCost
function calcConsumablesCost()
{
	var calcConsumablesCost = 0;
	var consumableCostPerShiftPerMonth = document.getElementById("consumableCostPerShiftPerMonth").value;
	var noOfShiftsUnit			= document.getElementById("noOfShiftsUnit").value;
	var noOfWorkingDaysInMonthUnit	   = document.getElementById("noOfWorkingDaysInMonthUnit").value;
	//Consumable cost per shift per month * No.of Shift * No.of Working days in month
	calcConsumablesCost = parseFloat(consumableCostPerShiftPerMonth) * parseFloat(noOfShiftsUnit) * parseFloat(noOfWorkingDaysInMonthUnit);
	if (!isNaN(calcConsumablesCost)) {
		document.getElementById("consumablesCost").value =  number_format(calcConsumablesCost,0,'','');
	}	
}

// Calculate Lab Cost
function calcLabCost()
{
	var calcLabCost = 0;
	var noOfShiftsUnit		= document.getElementById("noOfShiftsUnit").value;	
	var noOfRetortsUnit		= document.getElementById("noOfRetortsUnit").value;
	var noOfWorkingDaysInMonthUnit	= document.getElementById("noOfWorkingDaysInMonthUnit").value;
	var labCostPerRetort		= document.getElementById("labCostPerRetort").value;	
	// lab cost per retort * No.of Shifts * No.of Retorts* No.of Working days in month
	calcLabCost = parseFloat(labCostPerRetort) * parseFloat(noOfShiftsUnit)* parseFloat(noOfRetortsUnit) * parseFloat(noOfWorkingDaysInMonthUnit);
	if (!isNaN(calcLabCost)) {
		document.getElementById("labCost").value =  number_format(calcLabCost,0,'','');
	}		
}
/*
// Find man Power total Cost
function manPowerTotalCost(unit, puCost, tCost)
{
	var totalCost 	 = 	0;
	var purchaseCost = 	0;
	var totalNumber	 =	0;

	if(document.getElementById(unit).value)
		totalNumber	=	parseFloat(document.getElementById(unit).value);
	if(document.getElementById(puCost).value)
		purchaseCost	=	parseFloat(document.getElementById(puCost).value);

	totalCost = totalNumber * purchaseCost;

	if(!isNaN(totalCost))
		document.getElementById(tCost).value = totalCost;
}

//variableManPowerCostPerDay
function calcVariManPowerCost()
{
	//alert("Here");
	var totalManPowerCost = 0;
	var totalVariableManPowerCost = 0;
	var vegProcessPerShiftTCost 	= document.getElementById("vegProcessPerShiftTCost").value;
	var pouchFillingPerShiftTCost 	= document.getElementById("pouchFillingPerShiftTCost").value;
	var sealingPerShiftTCost	= document.getElementById("sealingPerShiftTCost").value;
	var helperPerShiftTCost		= document.getElementById("helperPerShiftTCost").value;	

	totalManPowerCost = parseFloat(vegProcessPerShiftTCost) + parseFloat(pouchFillingPerShiftTCost) + parseFloat(sealingPerShiftTCost) + parseFloat(helperPerShiftTCost);
	
	var noOfShiftsUnit		= document.getElementById("noOfShiftsUnit").value;
	var noOfWorkingDaysInMonthUnit	= document.getElementById("noOfWorkingDaysInMonthUnit").value;
	//=(SUM(F81:F84)/D47)*D41
	totalVariableManPowerCost	= (totalManPowerCost/noOfWorkingDaysInMonthUnit) * noOfShiftsUnit;
	if (!isNaN(totalVariableManPowerCost)) {
		document.getElementById("variableManPowerCostPerDay").value =  number_format(totalVariableManPowerCost,2,'.','');
	}
}

//Calculate Fixed manPower Cost
function calcFixedManPowerCost()
{
	var totalFixedManPowerCost = 0;
	var totalManPowerCost = 0;
	var foodTechnoTCost 	= document.getElementById("foodTechnoTCost").value;
	var chiefChefTCost 	= document.getElementById("chiefChefTCost").value;	
	var supervisorPerShiftTCost = document.getElementById("supervisorPerShiftTCost").value;
	var technicianPerShiftTCost = document.getElementById("technicianPerShiftTCost").value;
	var cookPerShiftTCost	= document.getElementById("cookPerShiftTCost").value;	
	
	totalManPowerCost = parseFloat(foodTechnoTCost) + parseFloat(chiefChefTCost) + parseFloat(supervisorPerShiftTCost) + parseFloat(technicianPerShiftTCost) + parseFloat(cookPerShiftTCost);
	
	var noOfWorkingDaysInMonthUnit	= document.getElementById("noOfWorkingDaysInMonthUnit").value;
	//=(SUM($F$76:$F$80)/$D$47)
	totalFixedManPowerCost	= (totalManPowerCost/noOfWorkingDaysInMonthUnit);
	if (!isNaN(totalFixedManPowerCost)) {
		document.getElementById("fixedManPowerCostPerDay").value =  number_format(totalFixedManPowerCost,2,'.','');
	}
}
*/

// Find man Power Costs
function calcManPowerCost()
{
	var totalCost 	 = 	0;
	var purchaseCost = 	0;
	var totalNumber	 =	0;
	var manPowerType = "";
	var fixedManPowerCost = 0;
	var variableManPowerCost = 0;

	var noOfShiftsUnit		= document.getElementById("noOfShiftsUnit").value;
	var noOfWorkingDaysInMonthUnit	= document.getElementById("noOfWorkingDaysInMonthUnit").value;

	var hidManPowerCount = document.getElementById("hidManPowerCount").value;
	
	for (i=1; i<=hidManPowerCount; i++) {
		totalNumber 	= 	parseFloat(document.getElementById("manPowerUnit_"+i).value);
		purchaseCost	=	parseFloat(document.getElementById("manPowerPuCost_"+i).value);
		manPowerType	=	document.getElementById("manPowerType_"+i).value;
		totalCost = totalNumber * purchaseCost;
		if (!isNaN(totalCost)) {
			document.getElementById("manPowerTCost_"+i).value = number_format(totalCost,2,'.','');
		}
	
		if (manPowerType=='F') {
			fixedManPowerCost += totalCost;
		} else if (manPowerType=='V') {
			variableManPowerCost += totalCost;
		}		
	}
	if (!isNaN(fixedManPowerCost)) {
		document.getElementById("fixedManPowerCostPerDay").value = number_format((fixedManPowerCost/noOfWorkingDaysInMonthUnit),2,'.','');
	}
	
	if (!isNaN(variableManPowerCost)) {
		document.getElementById("variableManPowerCostPerDay").value =  number_format(((variableManPowerCost/noOfWorkingDaysInMonthUnit)*noOfShiftsUnit),2,'.','');
	}	
}

	// Function to calculate Marketing Cost
	function calcMktgCost()
	{
		var mktgActual 	= 0;
		var mktgIdeal	= 0;
		var mktgPuCost	= 0;
		var mktgTotalCost= 0;
		var mktgAvgCost = 0;
		var totalActual = 0;
		var totalIdeal= 0;
		var grandTotalMktgTCost = 0;
		var grandTotalMktgACost = 0;

		var hidMktgCostCount = document.getElementById("hidMktgCostCount").value;
		for (i=1; i<=hidMktgCostCount; i++) {
			mktgActual = parseInt(document.getElementById("mktgActual_"+i).value);
			mktgIdeal  = parseInt(document.getElementById("mktgIdeal_"+i).value);
			mktgPuCost = parseInt(document.getElementById("mktgPuCost_"+i).value);
			mktgTotalCost = mktgIdeal * mktgPuCost;
			mktgAvgCost = mktgActual * mktgPuCost;
			if (!isNaN(mktgTotalCost)) {
				document.getElementById("mktgTotCost_"+i).value = number_format(mktgTotalCost,0,'','');
			}
			if (!isNaN(mktgAvgCost)) {
				document.getElementById("mktgAvgCost_"+i).value = number_format(mktgAvgCost,0,'','');
			}
			// Find total Actual Value
			totalActual += 	mktgActual;	
			// Find total Ideal Value
			totalIdeal += 	mktgIdeal;

			// Find the Grand total Mktg Cost
			grandTotalMktgTCost += mktgTotalCost;
			// Find the Grand total Avg Cost
			grandTotalMktgACost += mktgAvgCost;
		}

		if (!isNaN(totalActual)) {
			document.getElementById("totalMktgCostActual").value = number_format(totalActual,0,'','');
		}
		if (!isNaN(totalIdeal)) {
			document.getElementById("totalMktgCostIdeal").value = number_format(totalIdeal,0,'','');
		}
		if (!isNaN(grandTotalMktgTCost)) {
			document.getElementById("totalMktgCostTCost").value = number_format(grandTotalMktgTCost,0,'','');
		}
		if (!isNaN(grandTotalMktgACost)) {
			document.getElementById("totalMktgCostACost").value = number_format(grandTotalMktgACost,0,'','');
		}
	}

	// Function to calculate Travel Cost
	function calcTravelCost()
	{
		var travelActual 	= 0;
		var travelIdeal	= 0;
		var travelPuCost	= 0;
		var travelTotalCost= 0;
		var travelAvgCost = 0;
		var totalActual = 0;
		var totalIdeal= 0;
		var grandTotalTravelTCost = 0;
		var grandTotalTravelACost = 0;

		var hidTravelCostCount = document.getElementById("hidTravelCostCount").value;
		for (i=1; i<=hidTravelCostCount; i++) {
			travelActual = parseInt(document.getElementById("travelActual_"+i).value);
			travelIdeal  = parseInt(document.getElementById("travelIdeal_"+i).value);
			travelPuCost = parseInt(document.getElementById("travelPuCost_"+i).value);
			travelTotalCost = travelIdeal * travelPuCost;
			travelAvgCost = travelActual * travelPuCost;
			if (!isNaN(travelTotalCost)) {
				document.getElementById("travelTotCost_"+i).value = number_format(travelTotalCost,0,'','');
			}
			if (!isNaN(travelAvgCost)) {
				document.getElementById("travelAvgCost_"+i).value = number_format(travelAvgCost,0,'','');
			}
			// Find total Actual Value
			totalActual += 	travelActual;	
			// Find total Ideal Value
			totalIdeal += 	travelIdeal;

			// Find the Grand total Travel Cost
			grandTotalTravelTCost += travelTotalCost;
			// Find the Grand total Avg Cost
			grandTotalTravelACost += travelAvgCost;
		}
	
		if (!isNaN(grandTotalTravelTCost)) {
			document.getElementById("totalTravelCost").value = number_format(grandTotalTravelTCost,0,'','');
		}
		if (!isNaN(grandTotalTravelACost)) {
			document.getElementById("totalTravelACost").value = number_format(grandTotalTravelACost,0,'','');
		}
	}