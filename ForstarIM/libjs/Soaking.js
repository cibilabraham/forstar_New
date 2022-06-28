function validateSoaking(form)
{
	isQtyEntered 		 = false;	
	var arrHour = new Array();
 	var arrmin = new Array(); 
	arrHour = ["1","2","3","4","5","6","7","8","9","10","11","12"];
	arrmin =["00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50","51","52","53","54","55","56","57","58","59"];
	//isPreProcessorQtyEntered = false;
	var fish		 	 = form.selFish.value;
	//var rm_lot_id		 = form.rm_lot_id.value;
	var validDPPEnabled	 = document.getElementById("validDPPEnabled").value;
	
	if (fish=="" && !validDPPEnabled) {
		alert("Please select a fish.");
		form.selFish.focus();
		return false;
	}
	/*if(rm_lot_id == '')
	{
		alert("Please select a RM LOT ID");
		form.rm_lot_id.focus();
		return false;
	}*/

	var processRowCount	= document.getElementById("hidProcessRowCount").value;
	if (processRowCount>0) {
		//var columnCount		= document.getElementById("hidColumnCount").value;
		//var openingBalQty	=	"openingBalQty_";
		//var todayArrivalQty	=	"todayArrivalQty_";
		//var totalQty		=	"totalQty_";	
			
		for (i=0; i<processRowCount; i++) {
			
			//var grandType=document.getElementById("gradeName_"+i).value;
			//alert(grandType);
			
			
		var soakInTime=document.getElementById("soak-inTime_"+i).value;
		if(soakInTime!="")
		{
		//alert(soakTime);
			var skIn=soakInTime.split(":");
			if(skIn.length=='1')
			{
				alert("Time format should be between   '12:00am' and '11:59pm' ");
				return false;
			}
			else(skIn.length=='2')
			{
				var hourIn=skIn[0];
			//alert(arrHour.indexOf(hour));
				if(arrHour.indexOf(hourIn)>-1)
				{
					//return true;
					var mintIn=skIn[1];
					var valsIn=mintIn.substr(2,3);
					var minuteIn=mintIn.substr(0,2);
					//alert(minute);
					if (valsIn=="am" || valsIn=="pm")
					{
						if(arrmin.indexOf(minuteIn)>-1)
						{
							//return true;
						}
						else
						{
							alert("Minute should be between 00 and 59");
							return false;
						}
					}
					else
					{
						alert("Time should be in am or pm");
						return false;
					}
				}
				else
				{
					alert( "Hour should be between 1 and 12");
					return false;
				}
			
			}	
		}
			
		var soakOutTime=document.getElementById("soak-outTime_"+i).value;
		//alert(soakOutTime);
		if(soakOutTime!="")
		{
			var skOut=soakOutTime.split(":");
			if(skOut.length=='1')
			{
				alert("Time format should be between   '12:00am' and '11:59pm' ");
				return false;
			}
			else(skOut.length=='2')
			{
				var hourOut=skOut[0];
			//alert(arrHour.indexOf(hour));
				if(arrHour.indexOf(hourOut)>-1)
				{
					//return true;
					var mintOut=skOut[1];
					var valsOut=mintOut.substr(2,3);
					var minuteOut=mintOut.substr(0,2);
					//alert(minute);
					if (valsOut=="am" || valsOut=="pm")
					{
						if(arrmin.indexOf(minuteOut)>-1)
						{
							//return true;
						}
						else
						{
							alert("Minute should be between 00 and 59");
							return false;
						}
					}
					else
					{
						alert("Time should be in am or pm");
						return false;
					}
				}
				else
				{
					alert( "Hour should be between 1 and 12");
					return false;
				}
			
			}	
		}
	
	
			if (document.getElementById('gradeName_'+i).value!="0" && document.getElementById('soak-inQty_'+i).value!="") {
				isQtyEntered = true;	
			}
	
			
			/*for (j=1; j<=columnCount; j++) { 
				var preProcessorQty	= "preProcessorQty_"+j+"_";
				if (document.getElementById(preProcessorQty+i).value!="" ) {
					isQtyEntered = true;
				}
			}*/
		}
	} // Row count check ends here

	if (isQtyEntered==false) {
		alert("Please enter atleast one Process Qty");
		return false;
	}
	 
	 if (confirmSave()) return true;
	 else return false;	 
}

// FIND THE OPENING BALANCE and ARRIVAL QTY SUM
// If (Available Qty==0) use Actual Used Qty (Today Arrival Qty) else Available Qty
function totalArrivalQty()
{
	var processRowCount	= document.getElementById("hidProcessRowCount").value;
	var total		= 0;
	var openingBalQty	= "openingBalQty_";
	var todayArrivalQty	= "todayArrivalQty_";
	var totalQty		= "totalQty_";		
	var totalPreProcessQty  = "totalPreProcessQty_";
	var qtyExist		= false;	
	
	for (i=1; i<=processRowCount; i++) {
		  var tQty		= 0;
		  var OpeningQty	= 0;
		  var todayQty		= 0;
		  var selFishId		= document.getElementById("selFishId_"+i).value;
		  var dppMainId   	= document.getElementById("dppMainId_"+i).value;
		  var availableQty   	= parseFloat(document.getElementById("availableQty_"+i).value);
		
	 	  if (document.getElementById(todayArrivalQty+i).value!="" || document.getElementById(openingBalQty+i).value!="") {
			if (document.getElementById(openingBalQty+i).value=="") {
				document.getElementById(openingBalQty+i).value = 0;
			}
			if (document.getElementById(todayArrivalQty+i).value=="") {
				document.getElementById(todayArrivalQty+i).value = 0;
			}
			tQty		= document.getElementById(totalQty+i).value;
			OpeningQty	= parseFloat(document.getElementById(openingBalQty+i).value);
			todayQty	= parseFloat(document.getElementById(todayArrivalQty+i).value);
			//total		= OpeningQty + todayQty;			
			total		= (availableQty<=0)?todayQty:availableQty;	
	  	}
		//alert(document.getElementById(totalPreProcessQty+i).value);	
		//document.getElementById(totalPreProcessQty+i).value
		//if ((!isNaN(total)) && document.getElementById(todayArrivalQty+i).value!="" ) {			
		if (document.getElementById(totalPreProcessQty+i).value!=0) {
			if (!isNaN(total)) document.getElementById(totalQty+i).value	= number_format(total,2,'.','');	
			qtyExist = true;
			if (document.getElementById(totalPreProcessQty+i).value!=0) {				
				document.getElementById("fishHasVal_"+selFishId).value = 1;
				document.getElementById("fishEntryId_"+selFishId).value = dppMainId;
			}
		} else if (total==0 && !qtyExist) {
			qtyExist = false;
			document.getElementById("fishHasVal_"+selFishId).value = "" ;
			document.getElementById("fishEntryId_"+selFishId).value = "";
		}
 	}
	calActualYield();	
}

function totalPreProcessingQty()
{	
	var processRowCount		= document.getElementById("hidProcessRowCount").value;
	var columnCount			= document.getElementById("hidColumnCount").value;
	var totalPreProcessQty		= "totalPreProcessQty_";
	var totalQty			= "totalQty_";	
	for (i=1; i<=processRowCount; i++) {
		var totalPreProcessing	= 0;
		for (j=1; j<=columnCount; j++) { 
			 	var preProcessorQty	= "preProcessorQty_"+j+"_";
				var eachPreProcessorQty	= 0;

			 	if (document.getElementById(preProcessorQty+i).value!="") {
					eachPreProcessorQty	=	parseFloat(document.getElementById(preProcessorQty+i).value);
					totalPreProcessing += eachPreProcessorQty;
				}
		}
		eachPreProcessoreTotQty	= totalPreProcessing;
		if ((!isNaN(totalPreProcessing))) {
			document.getElementById(totalPreProcessQty+i).value = number_format(eachPreProcessoreTotQty,2,'.','');
		}
	}
	calActualYield();
	
	totalArrivalQty();
}

function calActualYield()
{
	var processRowCount		= document.getElementById("hidProcessRowCount").value;
	var totalPreProcessQty		= "totalPreProcessQty_";
	var totalQty			= "totalQty_";
	var actualYield			= "actualYield_";
	var idealYield			= "idealYield_";
	var diffYield			= "diffYield_";
	var actualUsedQty		= "todayArrivalQty_";
	
	var actualYieldAverage		= 0;
	var findAverageYield		= 0;
	
	for (i=1; i<=processRowCount; i++) {
		/*
		if (document.getElementById(totalQty+i).value!=0) {
			findAverageYield	= ((document.getElementById(totalPreProcessQty+i).value/document.getElementById(totalQty+i).value)*100) ;
		} else findAverageYield	=	0;		
		*/
		if (document.getElementById(actualUsedQty+i).value!=0) {
			findAverageYield = ((document.getElementById(totalPreProcessQty+i).value/document.getElementById(actualUsedQty+i).value)*100) ;
		} else findAverageYield	= 0;

		actualYieldAverage = number_format(Math.abs(findAverageYield),2,'.','');
		
		if(!isNaN(actualYieldAverage)) {
			document.getElementById(actualYield+i).value	= actualYieldAverage;			
			var calcDiffYield = parseFloat(actualYieldAverage)-parseFloat(document.getElementById(idealYield+i).value);
			if (document.getElementById(actualUsedQty+i).value!=0) {
				document.getElementById(diffYield+i).value	= number_format(Math.abs(calcDiffYield),2,'.','');
			} else document.getElementById(diffYield+i).value	= "";
		}
	}
}

//Key moving
function nextProcess(e,form,name)
{
	var ecode = getKeyCode(e);	
	//alert(name);
	var sName = name.split("_");
	dArrowName = sName[0]+"_"+sName[1]+"_"+(sName[2]-2);
	
	rightArrow = sName[0]+"_"+(parseInt(sName[1])+1)+"_"+(sName[2]-1);

	leftArrow = sName[0]+"_"+(parseInt(sName[1])-1)+"_"+(sName[2]-1);
	
	if ((ecode==13) || (ecode == 0) || (ecode==40)){
		var nextControl = eval(form+"."+name);
		if ( nextControl ) { nextControl.focus(); }
		return false;
   	 }
	if ((ecode==38)){
		var nextControl = eval(form+"."+dArrowName);
		if ( nextControl ) { nextControl.focus(); }
		return false;
   	 }
	if ((ecode==39)){
		//alert(rightArrow);
		var nextControl = eval(form+"."+rightArrow);
		if ( nextControl ) { nextControl.focus(); }
		return false;
   	 }
	if ((ecode==37)){
		//alert(leftArrow);
		var nextControl = eval(form+"."+leftArrow);
		if ( nextControl ) { nextControl.focus(); }
		return false;
   	 }
}

	function enableDPPButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAddDailyPreProcess").disabled = false;
			document.getElementById("cmdAddDailyPreProcess1").disabled = false;
			document.getElementById("cmdSaveAdd").disabled = false;
			document.getElementById("cmdSaveAdd1").disabled = false;
			
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableDPPButton(mode)
	{	
			
		if (mode==1) {
			//alert(mode);
			document.getElementById("cmdAddSoaking").disabled = true;
			document.getElementById("cmdAddSoaking1").disabled = true;
			document.getElementById("cmdSaveAdd").disabled = true;
			document.getElementById("cmdSaveAdd1").disabled = true;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

	function disableConfirmBtn()
	{		
		document.getElementById("cmdConfirm").disabled = true;	
		document.getElementById("cmdConfirm1").disabled = true;	
		var hidRowCount = document.getElementById("hidRowCount").value;
		for (i=1; i<=hidRowCount; i++) {
			document.getElementById("cmdEdit_"+i).disabled = true;			
		}
	}

	function confirmDPPEntry(selDate)
	{
		if (!confirmSave()) {
			return false;
		} 		
		//return true;
		return xajax_confirmDailyPreProcessEntry(selDate);
	}

	// Change Avaialble Qty
	function changeAvailableQty(rowId, selProcessId)
	{			
		var pcCount = document.getElementById("pcCount_"+selProcessId).value;

		avQty = parseFloat(document.getElementById("availableQty_"+rowId).value);
		avQty -= parseFloat(document.getElementById("todayArrivalQty_"+rowId).value);

		for (i=parseInt(rowId)+1; i<=pcCount; i++) {
			var actualUsedQty = parseFloat(document.getElementById("todayArrivalQty_"+i).value);			
			document.getElementById("availableQty_"+i).value = number_format(avQty,2,'.','');
			avQty -= actualUsedQty;
		}		
	}	

	// Update Avaialble RM Qty
	function updateAvailableRMQty()
	{
		var uptdMsg	= "Do you wish to update the selected day's RM Qty?";
		if (confirm(uptdMsg)) {
			var selDate = document.getElementById("selDate").value;
			xajax_uptdAvailableRMQty(selDate);
			return true;
		}
		return false;
	}

	function updateAvailableQty()
	{
		var processRowCount = document.getElementById("hidProcessRowCount").value;
		//alert("---->"+processRowCount);
		for (i=1; i<=processRowCount; i++) {
			var processFrom = document.getElementById("processFrom_"+i).value;
			//alert(i+","+processFrom);
			changeAvailableQty(i, processFrom);
			
		}
	}

	function functionLoad(formObj)
	{
		//alert("hai"+value);
		showFnLoading(); 
		formObj.form.submit();
	}
	
	function getSoakInTime(i)
	{
		//alert("hii");
		 $('#soak-inTime_'+i).timepicker({ 'step': 1 });
	}
	
	function getSoakOutTime(i)
	{
		 $('#soak-outTime_'+i).timepicker({ 'step': 1 });
	}
	
	function assignSoakType(i,soakType)
	{
		//alert(soakType);
		if(soakType==1)
		{
			$("#soak-inCount_"+i).show()
			$("#soak-inGrade_"+i).hide()
			$("#soak-outCount_"+i).show()
			$("#soak-outGrade_"+i).hide()
		}
        else if(soakType==2)
		{
			$("#soak-inCount_"+i).hide()
			$("#soak-inGrade_"+i).show()
			$("#soak-outCount_"+i).hide()
			$("#soak-outGrade_"+i).show()
        }
		else 
		{
			$("#soak-inCount_"+i).hide()
			$("#soak-inGrade_"+i).hide()
			$("#soak-outCount_"+i).hide()
			$("#soak-outGrade_"+i).hide()
		}
	}
	
	function checkTimeFormat(fld,type)
	{
		var arrHour = new Array(); 	var arrmin = new Array(); 
		arrHour = ["1","2","3","4","5","6","7","8","9","10","11","12"];
		arrmin =["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50","51","52","53","54","55","56","57","58","59","60"];
		var soakTime=document.getElementById("soak-"+type+"Time_"+fld).value;
		//alert(soakTime);
		var sk=soakTime.split(":");
		if(sk.length=='1')
		{
			alert("Time format should be between   '12:00am' and '11:59pm' ");
			return false;
		}
		else(sk.length=='2')
		{
			var hour=sk[0];
		//alert(arrHour.indexOf(hour));
			if(arrHour.indexOf(hour)>-1)
			{
				//return true;
				var mint=sk[1];
				var val=mint.substr(2,3);
				var minute=mint.substr(0,2);
				//alert(minute);
				if(val!="am" || val!="pm")
				{
					alert("Time should be in am or pm");
					return false;
				}
				else
				{
					//arrmin.indexOf(hour)>-1
					if(arrmin.indexOf(minute)>-1)
					{
						return true;
					}
					else
					{
						alert("Minute should be between 0 and 59");
						return false;
					}
				}
				//alert(val);
			}
			else
			{
				alert( "Hour should be between 1 and 12");
				return false;
			}
		
		}	
	}
	
	
	
	/*function assignSoakType(i,soakType)
	{
		//alert("hii");
		if(soakType==="Count"){
			$("#soak-inTypeCount_"+i).show()
			$("#soak-inTypeGrade_"+i).hide()
			$("#soak-outTypeCount_"+i).show()
			$("#soak-outTypeGrade_"+i).hide()
		}
        else{
			$("#soak-inTypeCount_"+i).hide()
			$("#soak-inTypeGrade_"+i).show()
			$("#soak-outTypeCount_"+i).hide()
			$("#soak-outTypeGrade_"+i).show()
        }
	}*/
	