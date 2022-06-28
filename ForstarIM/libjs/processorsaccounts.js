function actualValue(form)
{
	var rowCount	=	document.getElementById("hidRowCount").value;
	var total	= 	0;
	var commission	=	"commission_";
	var rate	=	"rate_";
	var actualRate	=	"totalRate_";
	var totalArrivalQty 		=	"totalArrivalQty_";
	var totalPreProcessedQty 	= 	"totalPreProcessedQty_";
	var preProcessedQty 		=	"preProcessedQty_";
	var preProcessRate		=	"preProcessRate_";
	var preProcessCommission	=	"preProcessCommission_";
	var criteria			=	"criteria_";
	var idealYield			=	"idealYield_";
	var actualYield			=	"actualYield_";
	var totalPreProcessAmt		=	0;
	var ratePerKg			=	0;
	var amount			=	0;
	var totalPreProcessorsQty	=	0;
	var defaultYieldTolerance = document.getElementById("defaultYieldTolerance").value;
	
	for (i=1; i<=rowCount; i++) {
		totalPreProcessorsQty	= document.getElementById(totalPreProcessedQty+i).value;
		var diffYield		= parseFloat(document.getElementById("diffYield_"+i).value);
		var ppYieldTolerance	= parseFloat(document.getElementById("ppYieldTolerance_"+i).value);
		var yieldTolerance 	= (ppYieldTolerance!=0)?ppYieldTolerance:defaultYieldTolerance;
				
		if (document.getElementById(commission+i).value!="" && document.getElementById(rate+i).value!="") {
	  	
		 if (document.getElementById(criteria+i).value==1) {
			
			//if (From) and actual yield> ideal yield  then yield=actual yield
			if (parseFloat(document.getElementById(actualYield+i).value) > parseFloat(document.getElementById(idealYield+i).value) && diffYield<yieldTolerance) {
				totalPreProcessAmt  =	(document.getElementById(totalPreProcessedQty+i).value/(document.getElementById(actualYield+i).value/100)) * document.getElementById(rate+i).value + document.getElementById(totalPreProcessedQty+i).value * document.getElementById(commission+i).value;
			} else {
				
				totalPreProcessAmt  =	(document.getElementById(totalPreProcessedQty+i).value/(document.getElementById(idealYield+i).value/100)) * document.getElementById(rate+i).value + document.getElementById(totalPreProcessedQty+i).value * document.getElementById(commission+i).value;
			}
		} else {
    			totalPreProcessAmt	=	document.getElementById(totalPreProcessedQty+i).value*document.getElementById(rate+i).value + document.getElementById(totalPreProcessedQty+i).value * document.getElementById(commission+i).value;
		}
		
		ratePerKg	=	 totalPreProcessAmt/totalPreProcessorsQty;
		
		amount		=	document.getElementById(preProcessedQty+i).value * ratePerKg;
		
		document.getElementById(actualRate+i).value = formatNumber(Math.abs(amount),2,'','.','','','','','');
		
	  	}
	 	total	= parseFloat(total)+parseFloat(document.getElementById(actualRate+i).value);
	}
	
	if(!isNaN(total)){
		form.netPayable.value 	= number_format(total,2,'.','');
		form.totalProcessRate.value = number_format(total,2,'.','');
	}
}


function validateSettlement(form)
{
	var processor = form.selProcessor.value;
	var selProcessCode = form.selProcessCode.value;
	
	if (processor=="" && selProcessCode=="") {
		alert(" Please select a Pre-Process Code or Pre-Processor");
		return false;
	}
	return true;
}

function validateProcessorsSettlement(form) 
{
	var processor = form.selProcessor.value;
	var selProcessCode = form.selProcessCode.value;

	if (processor=="" && selProcessCode=="") {
		alert(" Please select a Pre-Process Code or Pre-Processor");
		return false;
	}

	if(!confirmSave()) {
		return false;
	} else {
		return true;
	}
}