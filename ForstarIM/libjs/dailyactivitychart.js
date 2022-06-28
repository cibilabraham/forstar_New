function validateDailyActivityChart(form)
{
	var selectDate = document.getElementById("selectDate");

	if (selectDate.value=="") {
		alert("Please select a date.");
		selectDate.focus();
		return false;
	}

	if (!activityTimeCheck()) {
		return false;
	}

	if (!confirmSave()) return false;
	else return true;	
}

//Validate Ice Brine Entries
function validateDailyActivityChartIceBrine(form)
{
	
	var iceBrine	= form.iceBrine.value;
	var volt	= form.volt.value;
	var ampere	= form.ampere.value;

	if (iceBrine=="") {
		alert("Please enter ice brine Temp.");
		form.iceBrine.focus();
		return false;
	}

	if (volt=="") {
		alert("Please enter Volt.");
		form.volt.focus();
		return false;
	}
	
	if (ampere=="") {
		alert("Please enter Ampere.");
		form.ampere.focus();
		return false;
	}

	if (!confirmSave()) return false;
	else return true;	
}

//Validate Cold Temp Entries
function validateDailyActivityChartColdTemp(form)
{
	var coldTemp1	= form.coldTemp1.value;	
	var coldTemp2	= form.coldTemp2.value;	
	var coldTemp3	= form.coldTemp3.value;	
	var coldTemp4	= form.coldTemp4.value;	
	var coldTemp5	= form.coldTemp5.value;	
	var IQF		= form.IQF.value;		

	if (coldTemp1=="" || coldTemp2=="" || coldTemp3=="" || coldTemp4=="" || coldTemp5=="") {
		alert("Please enter cold storage Temperature.");
		//form.iceBrine.focus();
		return false;
	}

	if (IQF=="") {
		alert("Please enter IQF.");
		form.IQF.focus();
		return false;
	}

	if (!confirmSave()) return false;
	else return true;	
}

function activityTimeCheck(){
	selectTimeHour	=	document.getElementById("selectTimeHour").value;
	selectTimeMints	=	document.getElementById("selectTimeMints").value;
	if (selectTimeHour>12 || selectTimeHour<=0) { 
		alert("hour is wrong");
		document.getElementById("selectTimeHour").focus();
		return false;
	}
	if (selectTimeMints>59 || selectTimeMints<0){
		alert("minute is wrong");
		document.getElementById("selectTimeMints").focus();
		return false;
	}
	return true;
}




//Calculate the diesel stock

function calculateDieselStock()
{
	var closingBalance 	= 	0;
	var dieselOB		=	0;
	var dieselUsed		=	0;
	var dieselPurchase	=	0;
	
	if(document.getElementById("dieselOB").value)
		dieselOB	=	parseFloat(document.getElementById("dieselOB").value);
	
	if(document.getElementById("dieselUsed").value)
		dieselUsed	=	parseFloat(document.getElementById("dieselUsed").value);
	
	if(document.getElementById("dieselPurchase").value)
		dieselPurchase	=	parseFloat(document.getElementById("dieselPurchase").value);
	
	//Calculation 
	closingBalance		=	(dieselOB + dieselPurchase) - dieselUsed ;
	if(!isNaN(closingBalance))
		document.getElementById("dieselCB").value = closingBalance;
}
// Find the stock balance
function calculateIceStock()
{
	var openingBalance	= 	0;
	var closingBalance 	= 	0;
	var iceProduced		=	0;
	var iceSelfProduction	=	0;
	var iceDispatch		=	0;
	var iceSold		=	0;
	var icePurchased	=	0;
	var iceCB		=	0;

	if(document.getElementById("iceOB").value)
		openingBalance	=	parseFloat(document.getElementById("iceOB").value);
	if(document.getElementById("iceProduced").value)
		iceProduced	=	parseFloat(document.getElementById("iceProduced").value);
	if(document.getElementById("iceSelfProduction").value)
		iceSelfProduction	=	parseFloat(document.getElementById("iceSelfProduction").value);
	if(document.getElementById("iceDispatch").value)
		iceDispatch	=	parseFloat(document.getElementById("iceDispatch").value);
	if(document.getElementById("iceSold").value)
		iceSold	=	parseFloat(document.getElementById("iceSold").value);
	if(document.getElementById("icePurchased").value)
		icePurchased	=	parseFloat(document.getElementById("icePurchased").value);

	//Calculation 
	closingBalance		=	(openingBalance+iceProduced + icePurchased) - (iceSelfProduction+iceDispatch+iceSold) ;
	if(!isNaN(closingBalance))
		document.getElementById("iceCB").value = closingBalance;
}

// Find other balance

function dailyActivityClosingBalance(opening, closng, balance)
{
	var closingBalance 	= 	0;
	var meterReadingOpening	= 	0;
	var meterReadingClosing	=	0;
	

	if(document.getElementById(opening).value)
		meterReadingOpening	=	parseFloat(document.getElementById(opening).value);
	if(document.getElementById(closng).value)
		meterReadingClosing	=	parseFloat(document.getElementById(closng).value);

	closingBalance = meterReadingOpening - meterReadingClosing;
	if(!isNaN(closingBalance))
		document.getElementById(balance).value = closingBalance;
}


/**
* daily Activity chart calculation
*/
function calcActChart()
{
	var dActHeadRowCount = document.getElementById("dActHeadRowCount").value;
	
	var fieldArr = new Array();
	fieldArr["CB"] = 'closingBal_';
	fieldArr["OB"] = 'openingBal_';
	fieldArr["DIFF"] = 'diffBal_';
	fieldArr["PRODUCED"] = 'produced_';
	fieldArr["PURCHASED"] = 'purchased_';
	fieldArr["USED"] = 'used_';
	fieldArr["OSSUPPLY"] = 'osSupply_';
	fieldArr["OSSALE"] = 'osSale_';
		
	var fieldVal = "";
	for (i=1; i<=dActHeadRowCount; i++) {
		var subHeadRowCount = document.getElementById("subHeadRowCount_"+i).value;
		for (j=1; j<=subHeadRowCount; j++) {
			var fArrRowCount = document.getElementById("fArrRowCount_"+i+"_"+j).value;	
			var calcVal = "";
			
			for (k=1; k<=fArrRowCount; k++) {
				var hidRowVal = document.getElementById("hidRowVal_"+i+"_"+j+"_"+k).value;
				
				fieldVal = 0;
				if (typeof(fieldArr[hidRowVal])!="undefined" ) { 
					fieldVal =  parseFloat(document.getElementById((fieldArr[hidRowVal])+i+"_"+j).value);
					fieldVal = (!isNaN(fieldVal))?number_format(fieldVal,2,'.',''):0;
				} else fieldVal = hidRowVal;
				
				if (fieldVal!="=" && fArrRowCount!=k) {
					//alert(fieldVal);
					calcVal += fieldVal;
				}				
			} // Field row ends here
			document.getElementById((fieldArr[hidRowVal])+i+"_"+j).value = number_format(eval(calcVal),2,'.','');

		} // Sub head loop ends here	
	} // Main Head Loop ends here
}

	function enableBtn(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableBtn(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}
