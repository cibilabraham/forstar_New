mode =0 ;
	function validateGrossEntry(form)
	{
		var fishName	=	form.fish.value;
		var fishCode	=	form.processCode.value;
		var grossWt		=	form.grossWeight.value;
		if(fishName==""){
			alert("Please select the fish");
			form.fish.focus();
			return false;
		}
		if(fishCode==""){
			alert("Please select the Process Code");
			form.processCode.focus();
			return false;
		}
		if(grossWt==""){
			alert("Please enter the Gross Wt ");
			form.grossWeight.focus();
			return false;
		}
		
		return true;	
	}

	function getKeyCode(e)
	{
		if(window.event!=undefined) return window.event.keyCode;
		return e.which;
	}

function focusNext(e,form,name,i,sos,limit)
{	
	var ecode = getKeyCode(e);		
	if ((ecode==13) || (ecode == 0)){ 		
		var nextControl = eval(form+"."+name);		
		if ( nextControl ) { nextControl.focus(); }
		return false;
    	}
  }

	//For main form
	function focusNextBox(e,form,name)
	{	
		var ecode = getKeyCode(e);
		if ((ecode==13) || (ecode == 0) || ecode==""){			
			var nextControl = eval(form+"."+name);		
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
	}

	//Fid Actual wt from Iframe catchEntryGrossWt.php screen
	function findActualWt()
	{		
		actualWt();
	}


	function actualWt()
	{
		var totalAdj		=	0;
		var netWt		=	document.getElementById("entryGrossNetWt").value;
		var adjust		=	document.getElementById("entryAdjust").value;
		var gradeCountAdj	=	document.getElementById("gradeCountAdj").value;
		var noBilling		= document.getElementById("noBilling").checked;

		var totalActualWt	=	0;
		
		if (gradeCountAdj=="") gradeCountAdj	= 0;			
		if (netWt!="" || adjust!="") {
			//totalAdj	= parseFloat(adjust)+parseFloat(gradeCountAdj); Original 18-02-10				
			totalAdj	= parseFloat(adjust);	
			if (!noBilling) {
				totalAdj = parseFloat(totalAdj)+parseFloat(gradeCountAdj);	
			}
			if (!isNaN(totalAdj)) {
				totalActualWt   =  parseFloat(netWt)-totalAdj;
				document.getElementById("entryActualWt").value	= number_format(Math.abs(totalActualWt),2,'.','');
			}			
			effectiveWt();
		}
	}


	function effectiveWt()
	{
		wastage	=	0;
		soft	=	0;
		local	=	0;
		var total;
		var actualWt	=	document.getElementById("entryActualWt").value;
		var wastage	=	document.getElementById("entryWastage").value;
		var soft	=	document.getElementById("entrySoft").value;
		var local	=	document.getElementById("entryLocal").value;
		var localPer	=	document.getElementById("entryLocalPercent").value;
		var wastePer	=	document.getElementById("entryWastagePercent").value;
		var softPer	=	document.getElementById("entrySoftPercent").value;
		
		if (wastage=="" && soft=="" && local=="") {
			total	=	0;
		} else {
			if (wastage ==""){
					total		=	parseFloat(soft)+parseFloat(local);
				}
				else if(soft==""){
					total		=	parseFloat(wastage)+parseFloat(local);
				}
				else if(local==""){
					total		=	parseFloat(wastage)+parseFloat(soft);
				}
				else {
					total		=	parseFloat(wastage)+parseFloat(soft)+parseFloat(local);
				}
			}
			if(actualWt!="" && !isNaN(total)){
				document.getElementById("entryEffectiveWt").value = number_format(Math.abs(actualWt-total),2,'.','');
			}
		
			//Percentage calc;
			if(actualWt!=0){
				document.getElementById("entryLocalPercent").value  = number_format(Math.abs((local*100)/actualWt),2,'.','');
				document.getElementById("entryWastagePercent").value = number_format(Math.abs((wastage*100)/actualWt),2,'.','');
				document.getElementById("entrySoftPercent").value = number_format(Math.abs((soft*100)/actualWt),2,'.','');
			} else {
				document.getElementById("entryLocalPercent").value = 0.00;
				document.getElementById("entryWastagePercent").value = 0.00;
				document.getElementById("entrySoftPercent").value	=	0.00;
			}
	}

function validateAddDailyCatchEntry(form, mode, saveType)
{	

	// try
	// {
	var fishName		=	form.fish.value;
	var fishCode		=	form.processCode.value;
	var supplyUnit		=	form.unit.value;
	var vechicleNo		=	form.vechicleNo.value;
	var landingCenter	=	form.landingCenter.value;
	var weighChallanNo	=	form.weighChallanNo.value;
	var mainSupplier	=	form.mainSupplier.value;	
	var entryLocal		=	form.entryLocal.value;
	var entryWastage	=	form.entryWastage.value;
	var entrySoft		=	form.entrySoft.value;
	var entryAdjust		=	form.entryAdjust.value;
	var goodPack		=	form.goodPack.value;
	var peeling		=	form.peeling.value;
	var entryGross		=	form.entryGrossNetWt.value;
	var countChanged 	= 	form.saveChangesOk.value;
	
	var hidReceived		=	form.hidReceived.value;
	
	var entryOption		=	form.entryOption.value;
	
	var paymentBy		=	form.paymentBy.checked;
	var selectDate		=	form.selectDate.value;
	//var rm_lot_id		=	form.rm_lot_id.value;
	
	
	//var payment		=	form.payment.value;
	//var count_code		=	form.count_code.value;

	
	var entryEffectiveWt = form.entryEffectiveWt.value;
	var totalDeclaredWt  = document.getElementById("totalDeclaredWt").value;
	var hidSameEntryExist = document.getElementById("hidSameEntryExist").value;
	var hidSameCountAverage = document.getElementById("hidSameCountAverage").value;
	var validChallanDate		= document.getElementById("validDate").value;
		
	
	if (supplyUnit=="") {
		alert("Please select the Unit");
		form.unit.focus();
		return false;
	}
	if (landingCenter=="") {
		alert("Please select the Landing Center");
		form.landingCenter.focus();
		return false;
	}
	if (mainSupplier=="") {
		alert("Please select the Main Supplier");
		form.mainSupplier.focus();
		return false;
	}

	if (vechicleNo=="") {
		alert("Please enter the Vechicle Number");
		form.vechicleNo.focus();
		return false;
	}

	if (weighChallanNo=="") {
		alert("Please enter the Weighment Challan Number");
		form.weighChallanNo.focus();
		return false;
	}
	/*if (rm_lot_id=="") {
		alert("Please select the Lot id");
		form.rm_lot_id.focus();
		return false;
	}
	if (payment=="") {
		alert("Please select the supplier");
		form.payment.focus();
		return false;
	}
	if (count_code=="") {
		alert("Please enter count_code");
		form.count_code.focus();
		return false;
	}*/

	if (selectDate=="") {
		alert("Please select a date");
		form.selectDate.focus();
		return false;
	}
	if (findDaysDiff(selectDate)>0) {
		alert("Please check date");
		form.selectDate.focus();
		return false;
	}	
 	if (!isDate(selectDate)) {	// Check Date Format
		form.selectDate.focus();
		return false
 	}

	if (validChallanDate=='N') {
		alert("Please check the entry date.\nThe entry date is greater than the delayed entry limit.");
		form.selectDate.focus();
		return false;
	}

	if (fishName=="") {
		alert("Please select the fish");
		form.fish.focus();
		return false;
	}
	if (fishCode=="") {
		alert("Please select the Raw material Code");
		form.processCode.focus();
		return false;
	}
	//condition if Received fish type
	if (fishName!="" && hidReceived!="") {
		if(hidReceived=='C'){
			var count			=	form.count.value;
			var countAverage	=	form.countAverage.value;
			if(count==""){
				alert("Please enter the count");
				form.count.focus();
				return false;
			}
			if(countAverage==""){
				alert("Please enter the count Average");
				form.countAverage.focus();
				return false;
			}
		}
		else if(hidReceived=='G') {
			var grade		=	form.selGrade.value;
			if (grade=="") {
				alert("Please select a grade");
				form.selGrade.focus();
				return false;
			}	
		}
	}

	if (entryOption=='B') {
		if (entryGross=="") {
			alert("Please enter the Count Values");
			//form.entryGrossNetWt..focus();
			return false;
		}
	} else {
		if (entryGross=="") {
			alert("Please enter the Net Weight");
			form.entryGrossNetWt.focus();
			return false;
		}	
	}

	if (entryAdjust=="") {
		alert("Please enter the Adjustment quantity");
		form.entryAdjust.focus();
		return false;
	}

	if (entryLocal=="") {
		alert("Please enter the Local Quantity");
		form.entryLocal.focus();
		return false;
	}
	if (entryWastage=="") {
		alert("Please enter the wastage quantity");
		form.entryWastage.focus();
		return false;
	}
	if (entrySoft=="") {
		alert("Please enter the soft quantity");
		form.entrySoft.focus();
		return false;
	}
	if (goodPack=="") {
		alert("Please enter the Good for Packing Percentage");
		form.goodPack.focus();
		return false;
	}
	if (peeling=="") {
		alert("Please enter the peeling Percentage");
		form.peeling.focus();
		return false;
	}
	if (entryOption=='B') {
		var grsWt = document.getElementById("entryGrossNetWt").value;
		/*
			if ( window.frames['catchentrygrosswt'] )	{			
				var grsWt = window.frames['catchentrygrosswt'].document.getElementById("entryGrossNetWt").value;
			} else {
				var grsWt = document.getElementById("catchentrygrosswt").contentDocument.getElementById("entryGrossNetWt").value;
			}		
		if (countChanged=="") {
			alert("Please save the Count items Total");
			return false;
		}
		*/

		if (grsWt=="" || grsWt==0) {
			//alert("Please enter and save the Count details.");
			alert("Please enter the Count details.");
			return false;
		}
	}

	if (!timeCheck()) {
		alert("Please enter a time");
		return false;
	}
	if (paymentBy) {
		
		//if (totalDeclaredWt==0 || totalDeclaredWt=="") {
		if (totalDeclaredWt=="") {
			alert("Please enter declared Wt.");
			return false;
		}
		
		//var dWeight = formatNumber(Math.abs(totalDeclaredWt),2,'','.','','','','','');
		var dWeight = number_format(Math.abs(totalDeclaredWt),2,'.','');

		//if (totalDeclaredWt>0 && dWeight!=entryEffectiveWt) {
		if (dWeight!=entryEffectiveWt) {
			alert("Declared and Effective Weight are not matching");	
			return false;
		}
	}
	
	/*if (hidSameEntryExist!="") {
		alert("The current entry is already existing");
		return false;
	} edited allow entry and display color*/ 

	if (hidSameEntryExist!="") {		
		var cMsg	= "The current entry is already in database. Do you wish to Continue?";
		if (!confirm(cMsg)) {
			return false;
		}	
	}

	if (hidSameCountAverage!="" && hidSameEntryExist=="") {
		var confirmMsg	= "The current count average is already in database. Do you wish to Continue?";
		if (!confirm(confirmMsg)) {
			return false;
		}		
	}

 	if (!confirmSave()) {
		return false;
	} else {
		// Save Entry (Ajax Save)
		if (saveType!=null) saveRMInChallan(mode, saveType);
		return true;
	}
	// }
	// catch(e)
	// {
	// alert(e);
	// return false;
	// }
}


	//find the average of comma seperated numbers
	function findAverage()
	{		
		var count	=	document.getElementById("count").value;
		var splitValue	=	count.split(",");
		var splitLength	=	splitValue.length;
		var sum=0;
		var average=0;
		for (i = 0; i < splitValue.length; ++i) {
			sum+=parseInt(splitValue[i]);
		}
		average = Math.ceil(sum/splitLength);
		
		if (!isNaN(average)) {			
			document.getElementById("countAverage").value = average;	
		}	
	}

	function totalWt(i,sos,limit)
	{
		//alert(sos);
		var lc=parseInt(sos);
		var total	= 0,btotal=0;
		var gWt		=	"grossWt_";
		var totWt	=	"totWt_";
		var bWt		=	"basketWt_";
		var nWt		=	"netWt_";
		var gbWt	=	"grossBasketWt_";
		while( limit > 0 )
		{
			var grossWt=0;
			var basWt=0;
			if(document.getElementById(gWt+lc).value!="")	{
				grossWt= parseFloat(document.getElementById(gWt+lc).value);
				if (grossWt==0)
				{
				basWt=0;
				}
				else{
				basWt= parseFloat(document.getElementById(gbWt+lc).value);
				}
				//alert(grossWt);
				//alert(basWt);
				basWt = number_format(basWt,2,'.','');
				total	= parseFloat(total)+parseFloat(grossWt);
				btotal  = parseFloat(btotal)+parseFloat(basWt);
				grossWtArr.splice((lc-1),1,grossWt);
				bWtArr.splice((lc-1),1,basWt);
			}
			lc++;
			limit--;
		}		
		if (!isNaN(total)) document.getElementById(totWt+i).value = number_format(Math.abs(total),2,'.','');
		if (!isNaN(btotal)) document.getElementById(bWt+i).value = number_format(Math.abs(btotal),2,'.','');		
		var totNWt = parseFloat(total)-parseFloat(btotal);
		document.getElementById(nWt+i).value=number_format((totNWt),2,'.','');	
		/*calcColWiseTot();*/	
		colWiseTot();
	}

function recordSaved(form, mode, saveType)
{
	var countChanged = form.saveChangesOk.value;
	var entryOption		=	form.entryOption.value;

	if(!validateAddDailyCatchEntry(form, mode, saveType)){
	return false;	
	}
	/*
	if(entryOption=='B'){
		if(countChanged==""){
			alert("Please save the Raw Material Count List");
			return false;
		}
	}
	*/	
	return true;	
}

function calcPeeling(form){
	document.getElementById("peeling").value =	100 - document.getElementById("goodPack").value;
}

function timeCheck(){
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

//Search Button Validation In Listing 

function validateSearchCatchEntry(form)
{
	var supplyFrom		=	form.supplyFrom.value;
	var supplyTill		=	form.supplyTill.value;
	
	
	var d = new Date();
	var t_date = d.getDate();      // Returns the day of the month
	var t_mon = d.getMonth() + 1;      // Returns the month as a digit
	var t_year = d.getFullYear();  // Returns 4 digit year
	
	curr_date	=	t_date + "/" + t_mon + "/" + t_year;

	if(supplyFrom==""){
		
			alert("Please select From Date");
			form.supplyFrom.focus();
			return false;
	}
	
	if(findDaysDiff(supplyFrom)>0){
			alert("Supply From Date should be less than or equal to current date");
			form.supplyFrom.focus();
			return false;	
	}
	
	if(supplyTill==""){
		
			alert("Please select Till Date");
			form.supplyTill.focus();
			return false;
	}
		
	if(findDaysDiff(supplyTill)>0){
			alert("Supply Till Date should be less than or equal to current date");
			form.supplyTill.focus();
			return false;	
	}
	if(checkDateSelected(supplyFrom,supplyTill)>0){
		alert("Please check selected From and To date");
		return false;
	}
	return true;
}


	function validateAddRawSelChallan(form)
	{	
		var selWtChallan		=	form.selWtChallan.value;		
		if(selWtChallan==""){			
				alert("Please select a Weighment Challan Number");
				form.selWtChallan.focus();
				return false;
		}		
		return true;
	}

	function validateDeclaredEntry(form)
	{
		var supplierChallanNo = form.supplierChallanNo.value;
		var supplierChallanDate = form.supplierChallanDate.value;
		var declWeight			=	form.declWeight.value;
		var declCount			=	form.declCount.value;
		var declIce				=	form.declIce.value;
		if(supplierChallanNo==""){
			
				alert("Please enter a Supplier Challan Number");
				form.supplierChallanNo.focus();
				return false;
		}
		if(supplierChallanDate==""){
			
				alert("Please select a date");
				form.supplierChallanDate.focus();
				return false;
		}
		if(findDaysDiff(supplierChallanDate)>0){
				alert("Supplier Challan Date should be less than or equal to current date");
				form.supplierChallanDate.focus();
				return false;	
		}
		if(declWeight==""){
			
				alert("Please enter a Declared Weight");
				form.declWeight.focus();
				return false;
		}
		if(declCount==""){
			
				alert("Please enter a Declared Count");
				form.declCount.focus();
				return false;
		}
		if(declIce==""){
			
				alert("Please enter a Declared Ice");
				form.declIce.focus();
				return false;
		}
		if (!confirmSave()) {
			return false;
		} else {
			return true;
		}
	}

	function enableDCEButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAddDailyCatch").disabled = false;
			document.getElementById("cmdAddDailyCatch1").disabled = false;
			document.getElementById("cmdAddRaw").disabled = false;	
			document.getElementById("cmdAddRaw1").disabled = false;
			document.getElementById("cmdAddNewChallan").disabled = false;	
			document.getElementById("cmdAddNewChallan1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdDailySaveChange").disabled = false;
			document.getElementById("cmdDailySaveChange1").disabled = false;
			document.getElementById("cmdAddRaw").disabled = false;	
			document.getElementById("cmdAddRaw1").disabled = false;
		}
	}
	
	function disableDCEButton(mode)
	{			
		if (mode==1) {
			document.getElementById("cmdAddDailyCatch").disabled = true;
			document.getElementById("cmdAddDailyCatch1").disabled = true;
			document.getElementById("cmdAddRaw").disabled = true;	
			document.getElementById("cmdAddRaw1").disabled = true;
			document.getElementById("cmdAddNewChallan").disabled = true;	
			document.getElementById("cmdAddNewChallan1").disabled = true;
			
		} else if (mode==2) {
			document.getElementById("cmdDailySaveChange").disabled = true;
			document.getElementById("cmdDailySaveChange1").disabled = true;
			document.getElementById("cmdAddRaw").disabled = true;	
			document.getElementById("cmdAddRaw1").disabled = true;
		}
	}

	function disableFields()
	{
		document.getElementById('paymentBy').checked = true;
		document.getElementById('subSupplier').disabled = true;
		document.getElementById('supplyChallanNo').readOnly = true;
		document.getElementById('supplyChallanNo').value="";
		document.getElementById('subSupplier').value="";
	}

	function enableFields()
	{
		document.getElementById('paymentBy').checked = false;
		document.getElementById('subSupplier').disabled = false;
		document.getElementById('supplyChallanNo').readOnly = false;		
	}

	function reloadDeclared()
	{
		var catchEntryNewId = document.getElementById("catchEntryNewId").value;
		var mainSupplier = document.getElementById("mainSupplier").value;
		var landingCenter = document.getElementById("landingCenter").value;
		document.getElementById("CatchEntryDeclaredItem").src = "CatchEntryDeclaredItem.php?entryId="+catchEntryNewId+"&mainSupplier="+mainSupplier+"&landingCenter="+landingCenter;
	}

	function reloadQuality()
	{
		var catchEntryNewId = document.getElementById("catchEntryNewId").value;
		document.getElementById("catchEntryQuality").src = "CatchEntryQuality_new.php?entryId="+catchEntryNewId;
	}

	function displayReceivedType(receivedBy)
	{
		var addMode = document.getElementById('addMode').value;

		if (receivedBy=='C') {
			document.getElementById("countRow").style.display = "";
			document.getElementById("countRow").style.visibility="visible";
			document.getElementById("countAvg").style.display = "";
			document.getElementById("countAvg").style.visibility="visible";
			document.getElementById("gradeRow").style.display = "none";
			document.getElementById("gradeRow").style.visibility="hidden";
		} else if (receivedBy=='G') {
			document.getElementById("countRow").style.display = "none";
			document.getElementById("countAvg").style.display = "none";
			document.getElementById("count").value="";
			document.getElementById("countAverage").value="";
			document.getElementById("gradeRow").style.display = "";
			document.getElementById("gradeRow").style.visibility="visible";
		} else if (receivedBy=='B') {
			document.getElementById("countRow").style.display = "";
			document.getElementById("countRow").style.visibility="visible";
			document.getElementById("countAvg").style.display = "";
			document.getElementById("countAvg").style.visibility="visible";
			document.getElementById("gradeRow").style.display = "";
			document.getElementById("gradeRow").style.visibility="visible";
		} else {
			document.getElementById("countRow").style.display = "none";
			document.getElementById("countAvg").style.display = "none";
			document.getElementById("gradeRow").style.display = "none";	
			document.getElementById("gradeRow").style.visibility="hidden";
		}
		/*
		var onChangeCheck = "";
		var onChanageCountAverage = "";
		if (receivedBy=='C' && addMode!="") {
			onChangeCheck = xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value, document.getElementById('processCode').value, document.getElementById('count').value,'');
		} else if (receivedBy=='G' && addMode!="") {
			onChangeCheck = xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, '', document.getElementById('selGrade').value);
		} else if (receivedBy=='B' && addMode!="") {
			onChangeCheck = xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, document.getElementById('count').value, document.getElementById('selGrade').value);
		}
				
		if ((receivedBy=='C' || receivedBy=='B') && addMode!="") {
			onChanageCountAverage = xajax_checkCountAverageSame(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, document.getElementById('countAverage').value);
		}
		*/
		if (addMode) {		

			if (receivedBy=='C' || receivedBy=='B') {
				//On Change Check
				document.getElementById("count").onchange = ajaxCall;
				document.getElementById("count").onkeyup = ajaxCall;	
			}
	
			if (receivedBy=='G' || receivedBy=='B') {
				//On Change Check
				document.getElementById("selGrade").onchange = ajaxCall;		
			}
		}
	}

	// Ajax Call
	function ajaxCall()
	{
		var receivedBy = document.getElementById('hidReceived').value;
		if (receivedBy=='C') {
			xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value, document.getElementById('processCode').value, document.getElementById('count').value,'');
		} else if (receivedBy=='G') {
			xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, '', document.getElementById('selGrade').value);
		} else if (receivedBy=='B') {
			xajax_checkSameEntryExist(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, document.getElementById('count').value, document.getElementById('selGrade').value);
		}
				
		if ((receivedBy=='C' || receivedBy=='B')) {
			xajax_checkCountAverageSame(document.getElementById('entryId').value, document.getElementById('fish').value,  document.getElementById('processCode').value, document.getElementById('countAverage').value);
		}
		findAverage();
	}

	function selEntryType()
	{			
		var entryOption 	= document.getElementById("entryOption").value;	
		var dailyBasketWt	= document.getElementById("dailyBasketWt").value;	
		var catchEntryNewId = document.getElementById("catchEntryNewId").value;
			
		if (entryOption=='B')  {
			document.getElementById("wtCalcTotGrWt").style.display = "";
			document.getElementById("wtCalcTotBsktWt").style.display = "";
			document.getElementById("entryGrossNetWt").readOnly = true;
			document.getElementById("bsktWtRow").style.display = "";			
			document.getElementById("cEntryGrossWtRow").style.display = "";
			document.getElementById("cEntryGrossWtRow").style.visibility="visible";
		}
		else {
			document.getElementById("cEntryGrossWtRow").style.display = "none";
			document.getElementById("cEntryGrossWtRow").style.visibility="hidden";
			document.getElementById("wtCalcTotGrWt").style.display = "none";
			document.getElementById("wtCalcTotBsktWt").style.display = "none";
			document.getElementById("entryGrossNetWt").readOnly = false;
			document.getElementById("bsktWtRow").style.display = "none";
			//document.getElementById("dailyBasketWt").value = "";
		}		
	}

	/*
	document.getElementById("entryOption").onkeypress = fNBox;
	function fNBox()
	{
		//alert("123666");
		var receivedBy = document.getElementById('hidReceived').value;
		if (receivedBy=='C') {
			return focusNextBox(event,'document.frmDailyCatch','count');
		} else if (receivedBy=='G') {
			 return focusNextBox(event,'document.frmDailyCatch','selGrade');
		} else if (receivedBy=='B') {
			 return focusNextBox(event,'document.frmDailyCatch','count');
		}
	}
	*/

	function sv(index, val)
	{	
		document.getElementById("grossBasketWt_"+index).value= val;

		/*	
		if ( window.frames['catchentrygrosswt'] )	{
			window.frames['catchentrygrosswt'].document.getElementById("grossBasketWt_"+index).value= number_format(val,2,'.','');		
		}
		else	{
			//alert(document.getElementById("catchentrygrosswt").contentWindow.document);
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("grossBasketWt_"+index).value=number_format(val,2,'.','');
		}
		*/
	}

	function resetGrossWt(index)
	{		
		var wt = getBWt(index);
		wt = number_format(wt,2,'.','');
		document.getElementById("dailyBasketWt").value = wt; 		
		for(var i=1; i<=300; i++) {
			sv(i,wt);
			bWtArr.splice((i-1),1,wt);
		}	
	}
// PC -> basket weight 
	var pc2WtArr = "";	
	function getBWt(index)
	{
		var v = pc2WtArr.split(",");		
		return parseFloat(v[index-1]);
	}
	
	
	// Save & Add New Raw Material in Challan	
	function saveRMInChallan(mode, saveType)
	{
		/* -------- Main Section ---------- */
		var unit 		= document.getElementById("unit").value;
		var landingCenter 	= document.getElementById("landingCenter").value;
		var mainSupplier 	= document.getElementById("mainSupplier").value;
		var vechicleNo 		= document.getElementById("vechicleNo").value;
		var weighChallanNo 	= document.getElementById("weighChallanNo").value;
		var selectDate		= document.getElementById("selectDate").value;
		var selectTimeHour 	= document.getElementById("selectTimeHour").value;
		var selectTimeMints 	= document.getElementById("selectTimeMints").value;
		var timeOption 		= document.getElementById("timeOption").value;
		var selectTime 		= selectTimeHour+"-"+selectTimeMints+"-"+timeOption;		
		var paymentBy 		= document.getElementById("paymentBy").checked;
		paymentBy		= (paymentBy)?'D':'E';
		var subSupplier 	= document.getElementById("subSupplier").value;
		var supplyChallanNo 	= document.getElementById("supplyChallanNo").value;
		var billingCompany 	= document.getElementById("billingCompany").value;
		var alphaCode 		= document.getElementById("alphaCode").value;
		var entryId		= document.getElementById("entryId").value; // main Id
		/* --------- Entry Section ------------- */
		var fish 		= document.getElementById("fish").value;
		var processCode		= document.getElementById("processCode").value;
		//$ice
		var count		= document.getElementById("count").value;
		var countAverage	= document.getElementById("countAverage").value;
		var entryLocal		= document.getElementById("entryLocal").value;
		var entryWastage	= document.getElementById("entryWastage").value;
		var entrySoft		= document.getElementById("entrySoft").value;
		var reasonAdjust	= document.getElementById("reasonAdjust").value;
		var entryAdjust		= document.getElementById("entryAdjust").value;
		var goodPack		= document.getElementById("goodPack").value;
		var peeling		= document.getElementById("peeling").value;
		var entryRemark		= document.getElementById("entryRemark").value;
		var entryActualWt	= document.getElementById("entryActualWt").value;
		var entryEffectiveWt	= document.getElementById("entryEffectiveWt").value;
		var entryTotalGrossWt	= document.getElementById("entryTotalGrossWt").value;
		var entryTotalBasketWt	= document.getElementById("entryTotalBasketWt").value;
		var entryGrossNetWt	= document.getElementById("entryGrossNetWt").value;
		//var declWeight	= document.getElementById("declWeight").value;
		//var declCount		= document.getElementById("declCount").value;
		var selGrade		= document.getElementById("selGrade").value; // Chk
		var dailyBasketWt	= document.getElementById("dailyBasketWt").value;
		var reasonLocal		= document.getElementById("reasonLocal").value;
		var reasonWastage	= document.getElementById("reasonWastage").value;
		var reasonSoft		= document.getElementById("reasonSoft").value;
		var entryOption		= document.getElementById("entryOption").value;				
		var gradeCountAdj	= document.getElementById("gradeCountAdj").value;
		var gradeCountAdjReason	= document.getElementById("gradeCountAdjReason").value;
		var catchEntryNewId	= document.getElementById("catchEntryNewId").value; // Entry Id
		var userId		= document.getElementById("hidUserId").value;
		var noBilling		= document.getElementById("noBilling").checked;
		noBilling		= (noBilling)?'Y':'N';

		var cntArr = new Array();
		var cntArrdel = new Array();
		var delArr = "";
		if (entryOption=='B') {
			// Save Count data
			var j=0;
			var j1=0;
			var grossId = 0;
			for (var i=1; i<=300; i++) {
				var grossId	= document.getElementById("grossId_"+i).value;
				var grossWt	= document.getElementById("grossWt_"+i).value; 
				var basketWt	= document.getElementById("grossBasketWt_"+i).value;
				if (grossWt!="" && grossWt!=0) {
					var joinCnt    =  grossId+":"+grossWt+":"+basketWt;
					cntArr[j] = joinCnt;
					j++;
				}
				else if (grossWt=="" || grossWt==0)
				{
					var joinCntdel    =  grossId+":"+grossWt+":"+basketWt;
					cntArrdel[j1] = joinCntdel;
					j1++;
				}
			} 
			// Count Loop Ends here
			delArr = document.getElementById("delArr").value;
		} // Basket Wt Entry Chk ends here
		
		if (entryId || catchEntryNewId) {
			cntArrStr = cntArr.join(",");
			cntArrdelStr=cntArrdel.join(",");
			// Update Main table
			xajax_saveChallan(unit, landingCenter, mainSupplier, vechicleNo, weighChallanNo, selectDate, selectTime, entryId, paymentBy, subSupplier, supplyChallanNo, billingCompany, alphaCode, fish, processCode, count, countAverage, entryLocal, entryWastage, entrySoft, reasonAdjust, entryAdjust, goodPack, peeling, entryRemark, entryActualWt, entryEffectiveWt, entryTotalGrossWt, entryTotalBasketWt, entryGrossNetWt, selGrade, dailyBasketWt, reasonLocal, reasonWastage, reasonSoft, entryOption, catchEntryNewId, gradeCountAdj, gradeCountAdjReason, mode, saveType, userId, cntArrStr, delArr, noBilling,cntArrdelStr);
		}
		clearRMFields(saveType, mode);
	}

	function resetCatchEntryNewId(entryId, catchEntryNewId)
	{
		document.getElementById("entryId").value = entryId;
		document.getElementById("catchEntryNewId").value = catchEntryNewId;
	}
	
	function clearRMFields(saveType, mode)
	{
		/* -------- Main Section ---------- */	
		if (saveType=='NC') {
			document.getElementById("selRawMaterial").length=0;
			document.getElementById("unit").value="";;
			document.getElementById("landingCenter").value = "";;
			document.getElementById("mainSupplier").value = "";
			document.getElementById("vechicleNo").value = "";
			document.getElementById("weighChallanNo").value = "";
			//document.getElementById("selectDate").value="";
			//document.getElementById("selectTimeHour").value = "";
			//document.getElementById("selectTimeMints").value = "";
			//document.getElementById("timeOption").value ="";
			document.getElementById("paymentBy").checked = false;		
			document.getElementById("subSupplier").value = "";
			document.getElementById("supplyChallanNo").value = "";
			//document.getElementById("billingCompany").value = "";
			//document.getElementById("alphaCode").value = "";			
		}
		/* --------- Entry Section ------------- */
		if (saveType=='NC' || saveType=='RM') {	
			document.getElementById("fish").value = "";
			document.getElementById("processCode").value = "";		
			document.getElementById("count").value = "";
			document.getElementById("countAverage").value = "";
			document.getElementById("entryLocal").value = 0;
			document.getElementById("entryWastage").value = 0;
			document.getElementById("entrySoft").value = 0;
			document.getElementById("reasonAdjust").value = "";
			document.getElementById("entryAdjust").value = "";
			document.getElementById("goodPack").value = 100;
			document.getElementById("peeling").value = 0;
			document.getElementById("entryRemark").value = "";
			document.getElementById("entryActualWt").value = "";
			document.getElementById("entryEffectiveWt").value = "";
			document.getElementById("entryTotalGrossWt").value = "";
			document.getElementById("entryTotalBasketWt").value = "";
			document.getElementById("entryGrossNetWt").value = "";		
			document.getElementById("selGrade").value = ""; 
			document.getElementById("dailyBasketWt").value = "";
			document.getElementById("reasonLocal").value = "";
			document.getElementById("reasonWastage").value = "";
			document.getElementById("reasonSoft").value = "";
			//document.getElementById("entryOption").value = "";				
			document.getElementById("gradeCountAdj").value = "";
			document.getElementById("gradeCountAdjReason").value = "";
			document.getElementById("totalGrossWt").value = "";
			document.getElementById("totalBasketWt").value = "";
			document.getElementById("noBilling").checked = false;
			setTimeout("reloadDeclared()",1000);	
			setTimeout("reloadQuality()",1000);
			clearCountDetails();
			resetCountData();
			if (mode==2) alert("Successfully updated the Daily Raw Material entry.");//$msg_succDailyCatchUpdate
			else alert("Daily Raw Material Entry added successfully.");//$msg_succAddDailyCatch
		}		
	}

	function clearCountDetails()
	{	
		for(var i=1; i<=300; i++) {			
			resetSV(i)
		}	
		
		for(var i=1; i<=20; i++) {			
			resetColV(i)
		}

		/*
		var catchEntryNewId = document.getElementById("catchEntryNewId").value;
		if ( window.frames['catchentrygrosswt'] )	{			
			window.frames['catchentrygrosswt'].document.getElementById("entryId").value=catchEntryNewId;
			window.frames['catchentrygrosswt'].document.getElementById("dailyBasketWt").value="";
			window.frames['catchentrygrosswt'].document.getElementById("curBasketWt").value="";
			window.frames['catchentrygrosswt'].document.getElementById("entryTotalGrossWt").value="";
			window.frames['catchentrygrosswt'].document.getElementById("entryTotalBasketWt").value="";
			window.frames['catchentrygrosswt'].document.getElementById("entryGrossNetWt").value="";
			window.frames['catchentrygrosswt'].document.getElementById("countSaved").value="";
			window.frames['catchentrygrosswt'].document.getElementById("isSaved").value="";
			window.frames['catchentrygrosswt'].document.getElementById("declNetWt").value="";			
		}
		else {			
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("entryId").value=catchEntryNewId;
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("dailyBasketWt").value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("curBasketWt").value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("entryTotalGrossWt").value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("entryTotalBasketWt").value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("entryGrossNetWt").value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("countSaved").value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("isSaved").value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("declNetWt").value="";
		}
		*/		
	}

	function resetSV(index)
	{	
		document.getElementById("grossBasketWt_"+index).value="";	
		document.getElementById("grossWt_"+index).value="";	
		document.getElementById("grossId_"+index).value="";		
	
		/*
		if ( window.frames['catchentrygrosswt'] )	{
			window.frames['catchentrygrosswt'].document.getElementById("grossBasketWt_"+index).value="";	
			window.frames['catchentrygrosswt'].document.getElementById("grossWt_"+index).value="";	
			window.frames['catchentrygrosswt'].document.getElementById("grossId_"+index).value="";
		}
		else	{			
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("grossBasketWt_"+index).value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("grossWt_"+index).value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("grossId_"+index).value="";
		}
		*/
		
	}

	function resetColV(index)
	{	
		document.getElementById("totWt_"+index).value="";
		document.getElementById("basketWt_"+index).value="";
		document.getElementById("netWt_"+index).value="";
		/*	
		if ( window.frames['catchentrygrosswt'] )	{			
			window.frames['catchentrygrosswt'].document.getElementById("totWt_"+index).value="";
			window.frames['catchentrygrosswt'].document.getElementById("basketWt_"+index).value="";
			window.frames['catchentrygrosswt'].document.getElementById("netWt_"+index).value="";
		}
		else {			
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("totWt_"+index).value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("basketWt_"+index).value="";
			document.getElementById("catchentrygrosswt").contentDocument.getElementById("netWt_"+index).value="";
		}
		*/
	}

	function resetBWt(bWt)
	{
		var catchEntryNewId = document.getElementById("catchEntryNewId").value;
		bWt = number_format(bWt,2,'.','');
		for(var i=1; i<=300; i++) {
			sv(i,bWt);
			bWtArr.splice((i-1),1,bWt);
		}
		if (catchEntryNewId) xajax_updateBWt(catchEntryNewId, bWt);

		var grandTotWt = 0;
		var totBasketWt = 0;
		var grandNetWt = 0;
		var row=15;
		for(var i=1; i<=20; i++) {
			var sos	=	(i-1)*row+1;			
			totalRSetWt(i,sos,row);
			var totWt = document.getElementById("totWt_"+i).value; 
			grandTotWt = parseFloat(grandTotWt) + parseFloat(totWt);
			var basketWt = document.getElementById("basketWt_"+i).value;
			totBasketWt = parseFloat(totBasketWt) + parseFloat(basketWt);
			var netWt = document.getElementById("netWt_"+i).value;
			grandNetWt = parseFloat(grandNetWt) + parseFloat(netWt);
			/*
			if ( window.frames['catchentrygrosswt'] ) var totWt =window.frames['catchentrygrosswt'].document.getElementById("totWt_"+i).value; 
			else var totWt =  document.getElementById("catchentrygrosswt").contentDocument.getElementById("totWt_"+i).value;
			if ( window.frames['catchentrygrosswt']) var basketWt = window.frames['catchentrygrosswt'].document.getElementById("basketWt_"+i).value;
			else var basketWt = document.getElementById("catchentrygrosswt").contentDocument.getElementById("basketWt_"+i).value;		
			if ( window.frames['catchentrygrosswt']) var netWt = window.frames['catchentrygrosswt'].document.getElementById("netWt_"+i).value;
			else var netWt = document.getElementById("catchentrygrosswt").contentDocument.getElementById("netWt_"+i).value;
			*/
		}
	
		if (!isNaN(grandTotWt))  {
			document.getElementById("entryTotalGrossWt").value = number_format(grandTotWt,2,'.','');
			document.getElementById("totalGrossWt").value = number_format(grandTotWt,2,'.','');
		}

		if (!isNaN(totBasketWt))  {
			document.getElementById("entryTotalBasketWt").value = number_format(totBasketWt,2,'.','');
			document.getElementById("totalBasketWt").value = number_format(totBasketWt,2,'.','');
		}

		if (!isNaN(grandNetWt))  {
			document.getElementById("entryGrossNetWt").value = number_format(grandNetWt,2,'.','');
		}
		findActualWt();
	}

	function totalRSetWt(i,sos, limit)
	{
		var lc=parseInt(sos);		
		var total	= 0,btotal=0;
		var gWt		= "grossWt_";
		var gbWt	= "grossBasketWt_";

		var totWt	= "totWt_";
		var bWt		= "basketWt_";
		var nWt		= "netWt_";
		
		while( limit > 0 ) {
			var gWtVal = document.getElementById(gWt+lc).value;
			var gbWtVal = document.getElementById(gbWt+lc).value;				
			/*
			if ( window.frames['catchentrygrosswt'] )	{			
				var gWtVal = window.frames['catchentrygrosswt'].document.getElementById(gWt+lc).value;
				var gbWtVal = window.frames['catchentrygrosswt'].document.getElementById(gbWt+lc).value;				
			} else {			
				var gWtVal = document.getElementById("catchentrygrosswt").contentDocument.getElementById(gWt+lc).value;
				var gbWtVal = document.getElementById("catchentrygrosswt").contentDocument.getElementById(gbWt+lc).value;
			}
			*/
			var grossWt=0;
			var basWt=0;
			if(gWtVal!="")	{
				grossWt= parseFloat(gWtVal);
				basWt= parseFloat(gbWtVal);
				total	+=	grossWt;
				btotal  +=	basWt;
			}			
			lc++;
			limit--;
		}
		
		if(!isNaN(total)) {			
			document.getElementById(totWt+i).value = number_format(Math.abs(total),2,'.','');
			/*
			if ( window.frames['catchentrygrosswt'] )	{			
				window.frames['catchentrygrosswt'].document.getElementById(totWt+i).value = number_format(Math.abs(total),2,'.','');
			} else {			
				document.getElementById("catchentrygrosswt").contentDocument.getElementById(totWt+i).value = number_format(Math.abs(total),2,'.','');
			}
			*/
		}
		
		if (!isNaN(btotal)) {			
			document.getElementById(bWt+i).value = number_format(Math.abs(btotal),2,'.','');
			/*
			if ( window.frames['catchentrygrosswt'] )	{			
				window.frames['catchentrygrosswt'].document.getElementById(bWt+i).value = number_format(Math.abs(btotal),2,'.','');
			} else {			
				document.getElementById("catchentrygrosswt").contentDocument.getElementById(bWt+i).value = number_format(Math.abs(btotal),2,'.','');
			}
			*/	
		}
		document.getElementById(nWt+i).value = number_format((total-btotal),2,'.','');
		/*	
		if ( window.frames['catchentrygrosswt'] )	{			
			window.frames['catchentrygrosswt'].document.getElementById(nWt+i).value = number_format(Math.abs(total-btotal),2,'.','');
		} else {			
			document.getElementById("catchentrygrosswt").contentDocument.getElementById(nWt+i).value = number_format(Math.abs(total-btotal),2,'.','');
		}
		*/
	}

	//function save
	var grossWtArr = new Array();
	var bWtArr	= new Array();
	var idArr	= new Array();

	function setCountData()
	{
		for (var i=0; i<idArr.length; i++) {
			var j = i+1;					
			document.getElementById("delId_"+j).value = idArr[i];
			document.getElementById("grossId_"+j).value = idArr[i];
			document.getElementById("grossWt_"+j).value = grossWtArr[i];
			document.getElementById("grossBasketWt_"+j).value = number_format(bWtArr[i],2,'.','');			
		} 
		// Hide Loading
		//hideFnLoading();
	}
	
	var delArr = new Array();
	function delCountData()
	{
		var wt = parseFloat(document.getElementById("dailyBasketWt").value);
		wt = number_format(wt,2,'.','');
		var dc=0;
		for (var i=1; i<=300; i++) {
			var delChk = document.getElementById("delId_"+i);			
			if (delChk.checked) {				
				var j= i-1-dc;							
				grossWtArr.splice(j,1);
				grossWtArr.push("");
				bWtArr[j]= wt;
				//del id.push(id);
				if (delChk.value!=0) delArr.push(delChk.value);
				delChk.checked = false;
				idArr.splice(j,1);
				idArr.push(0);
				dc++;
			}
		}
		delArrStr = delArr.join(",");
		document.getElementById("delArr").value = delArrStr;		
		setCountData();	
		calcColWiseTot();	
	}

	function calcColWiseTot()
	{
		var grandTotWt = 0;
		var totBasketWt = 0;
		var grandNetWt = 0;
		var row=15;
		for(var i=1; i<=20; i++) {
			var sos	=	(i-1)*row+1;			
			findColWiseTotWt(i,sos,row); // Find col wise tot
			var totWt = document.getElementById("totWt_"+i).value; 
			grandTotWt = parseFloat(grandTotWt) + parseFloat(totWt);

			var basketWt = document.getElementById("basketWt_"+i).value;
			totBasketWt = parseFloat(totBasketWt) + parseFloat(basketWt);
			var netWt = document.getElementById("netWt_"+i).value;
			grandNetWt = parseFloat(grandNetWt) + parseFloat(netWt);
		}
		
		if (!isNaN(grandTotWt))  {
			document.getElementById("entryTotalGrossWt").value = number_format(grandTotWt,2,'.','');
			document.getElementById("totalGrossWt").value = number_format(grandTotWt,2,'.','');
		}

		if (!isNaN(totBasketWt))  {
			document.getElementById("entryTotalBasketWt").value = number_format(totBasketWt,2,'.','');
			document.getElementById("totalBasketWt").value = number_format(totBasketWt,2,'.','');
		}

		if (!isNaN(grandNetWt))  {
			document.getElementById("entryGrossNetWt").value = number_format(grandNetWt,2,'.','');
		}
		findActualWt();
	}

	function findColWiseTotWt(i,sos, limit)
	{
		var lc=parseInt(sos);
		var total	= 0,btotal=0;
		var gWt		=	"grossWt_";
		var gbWt	=	"grossBasketWt_";

		var totWt	=	"totWt_";
		var bWt		=	"basketWt_";
		var nWt		=	"netWt_";
		
		while( limit > 0 ) {			
			var gWtVal = document.getElementById(gWt+lc).value;
			var gbWtVal = document.getElementById(gbWt+lc).value;
			var grossWt=0;
			var basWt=0;
			if(gWtVal!="")	{
				grossWt= parseFloat(gWtVal);
				basWt= parseFloat(gbWtVal);
				total	+= grossWt;
				btotal  += basWt;
			}			
			lc++;
			limit--;
		}		
		if(!isNaN(total)) document.getElementById(totWt+i).value = number_format(Math.abs(total),2,'.','');		
		if(!isNaN(btotal)) document.getElementById(bWt+i).value = number_format(Math.abs(btotal),2,'.','');
		document.getElementById(nWt+i).value = number_format((total-btotal),2,'.','');
	}

	function resetCountData()
	{
		for (var i=1; i<=300; i++) {
			var j= i-1;
			grossWtArr.splice(j,1,'');
			bWtArr.splice(j,1,'');
			idArr.splice(j,1,'');
		}
	}

	function colWiseTot()
	{		
		var grandTotWt = 0;
		var totBasketWt = 0;
		var grandNetWt = 0;		
		var totWt = 0;
		var basketWt = 0;
		var netWt = 0;
		for(var i=1; i<=20; i++) {			
			totWt = document.getElementById("totWt_"+i).value; 
			totWt = (totWt!="")?totWt:0;
			grandTotWt = parseFloat(grandTotWt) + parseFloat(totWt);
			basketWt = document.getElementById("basketWt_"+i).value;
			basketWt = (basketWt!="")?basketWt:0;
			totBasketWt = parseFloat(totBasketWt) + parseFloat(basketWt);
			netWt = document.getElementById("netWt_"+i).value;
			netWt = (netWt!="")?netWt:0;
			grandNetWt = parseFloat(grandNetWt)+parseFloat(netWt);
		}
		if (!isNaN(grandTotWt))  {
			document.getElementById("entryTotalGrossWt").value = number_format(grandTotWt,2,'.','');
			document.getElementById("totalGrossWt").value = number_format(grandTotWt,2,'.','');
		}

		if (!isNaN(totBasketWt))  {
			document.getElementById("entryTotalBasketWt").value = number_format(totBasketWt,2,'.','');
			document.getElementById("totalBasketWt").value = number_format(totBasketWt,2,'.','');
		}

		if (!isNaN(grandNetWt))  {
			document.getElementById("entryGrossNetWt").value = number_format(grandNetWt,2,'.','');
		}
		actualWt();
	}