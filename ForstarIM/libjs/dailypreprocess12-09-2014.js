function validateAddDailyPreProcess(form)
{
	isQtyEntered 		 = false;	
	isPreProcessorQtyEntered = false;
	var fish		 	 = form.selFish.value;
	//var rm_lot_id		 = form.rm_lot_id.value;
	var validDPPEnabled	 = document.getElementById("validDPPEnabled").value;
	
	// if (fish=="" && !validDPPEnabled) {
		// alert("Please select a fish.");
		// form.selFish.focus();
		// return false;
	// }
	/*if(rm_lot_id == '')
	{
		alert("Please select a RM LOT ID");
		form.rm_lot_id.focus();
		return false;
	}*/

	var processRowCount	= document.getElementById("hidProcessRowCount").value;
	if (processRowCount>0) {
		var columnCount		= document.getElementById("hidColumnCount").value;
		var openingBalQty	=	"openingBalQty_";
		var todayArrivalQty	=	"todayArrivalQty_";
		var totalQty		=	"totalQty_";	
			
		for (i=1; i<=processRowCount; i++) {
			/*
			if (document.getElementById(openingBalQty+i).value!="" && document.getElementById(todayArrivalQty+i).value!="") {
				isQtyEntered = true;	
			}
			*/
			for (j=1; j<=columnCount; j++) { 
				var preProcessorQty	= "preProcessorQty_"+j+"_";
				if (document.getElementById(preProcessorQty+i).value!="" ) {
					isQtyEntered = true;
				}
			}
		}
	} // Row count check ends here

	if (isQtyEntered==false) {
		alert("Please enter atleast one pre-Process Qty");
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
	//alert("hii");
	for (i=1; i<=processRowCount; i++) {
		  var tQty		= 0;
		  var OpeningQty	= 0;
		  var todayQty		= 0;
		  var selFishId		= document.getElementById("selFishId_"+i).value;
		  var dppMainId   	= document.getElementById("dppMainId_"+i).value;
		  var availableQty   	= parseFloat(document.getElementById("availableQty_"+i).value);
		//alert(availableQty);
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
			//	alert(total);
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
				//document.getElementById(diffYield+i).value	= number_format(calcDiffYield,2,'.','');
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
			document.getElementById("cmdAddDailyPreProcess").disabled = true;
			document.getElementById("cmdAddDailyPreProcess1").disabled = true;
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
		//alert(i);
			document.getElementById("cmdEdit_"+i).disabled = true;			
		}
	}

	function confirmDPPEntry(selDate)
	{
		//alert("hii");
		if (!confirmSave()) {
			return false;
		} 		
		//return true;
		return xajax_confirmDailyPreProcessEntry(selDate);
	}

	// Change Avaialble Qty
	function changeAvailableQty(rowId, selProcessId)
	{	
		//alert(rowId+"--->"+selProcessId);
		var pcCount = document.getElementById("pcCount_"+selProcessId).value;
		
		avQty = parseFloat(document.getElementById("availableQty_"+rowId).value);
		avQty -= parseFloat(document.getElementById("todayArrivalQty_"+rowId).value);
		
		//alert(avQty);
		
		for (i=parseInt(rowId)+1; i<=pcCount; i++) {
		//alert(i);
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
		//alert(i);
			var processFrom = document.getElementById("processFrom_"+i).value;
			//alert(processFrom);
			//alert(i);
			//alert(i+","+processFrom);
		changeAvailableQty(i, processFrom);
			
		}
	}

	function functionLoad(formObj)
	{
		//alert("hai"+value);
		var selFish=document.getElementById('selFish').value;
		showFnLoading(); 
		formObj.form.submit();
	
	}