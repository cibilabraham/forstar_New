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
		//alert("hii");
		//
			
		var actualWt	=	document.getElementById("entryActualWt").value;
		//alert(actualWt);
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
		
	function effectiveWtNew()
	{
		
		var actualWt	=	document.getElementById("entryActualWt").value;
		var total_new_entry = document.getElementById("total_new_entry").value;
		
		//var entryEffectiveWt = document.getElementById("entryEffectiveWt").value;
		
		var local	=	document.getElementById("entryLocal").value;
		var wastage	=	document.getElementById("entryWastage").value;
		var soft	=	document.getElementById("entrySoft").value;
		var weightTotal=parseFloat(local)+parseFloat(wastage)+parseFloat(soft);
		
		
		var qualityTotal	=	0;
		for(i=0;i<total_new_entry;i++)
		{
		//alert(total_new_entry);
			var entryValueDynamic = document.getElementById('quality_new_'+i).value;
			var entryValue = document.getElementById('qualityWeight_'+i).value;
			//alert(entryValue);
			var Status=document.getElementById('Status_'+i).value;
			if(Status!='N')
			{
				if(entryValue!="" && actualWt!="")
				{
					if(document.getElementById('billing_'+i).checked)
					{
						
						qualityTotal		=	qualityTotal+parseFloat(entryValue);
					}
					
					document.getElementById("qualityPercent_"+i).value = number_format(Math.abs((entryValue*100)/actualWt),2,'.','');
					
				}
			}
		}
			var totalQualityValue=qualityTotal+weightTotal;		
			if(actualWt!="" && !isNaN(qualityTotal)){
				document.getElementById("entryEffectiveWt").value = number_format(Math.abs(actualWt-totalQualityValue),2,'.','');
			}
		
			
			//Percentage calc;
			
	}
	function effectiveWtNew_old()
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
		var total_new_entry = document.getElementById("total_new_entry").value;
		total	=	0;
		
		for(i=0;i<total_new_entry;i++)
		{
		
			
			// if ($('#quality_new_'+i).length > 0){
			// alert("hii");
			// }
			var entryValueDynamic = document.getElementById('quality_new_'+i).value;
			var entryValue = document.getElementById('qualityWeight_'+i).value;
			//alert(entryValue);
			// var entryValueDynamic = document.getElementById('entry_new_'+i).value;
			// var entryValue = document.getElementById('entry'+entryValueDynamic).value;
			total		=	parseFloat(soft)+parseFloat(local);
			if (wastage =="")
			{
					total =	parseFloat(entryValue)+parseFloat(soft)+parseFloat(local);
			}
			else if(soft==""){
				total		=	parseFloat(entryValue)+parseFloat(wastage)+parseFloat(local);
			}
			else if(local=="")
			{
				total		=	parseFloat(entryValue)+parseFloat(wastage)+parseFloat(soft);
			}
			else 
			{
				if(document.getElementById('billing_'+i).checked == true)
				{
					total		=	parseFloat(entryValue)+parseFloat(wastage)+parseFloat(soft)+parseFloat(local);
				}
			}
		}
		// if (wastage=="" && soft=="" && local=="") {
			// total	=	0;
		// } else {
			// if (wastage ==""){
					// total		=	parseFloat(soft)+parseFloat(local);
				// }
				// else if(soft==""){
					// total		=	parseFloat(wastage)+parseFloat(local);
				// }
				// else if(local==""){
					// total		=	parseFloat(wastage)+parseFloat(soft);
				// }
				// else {
					// total		=	parseFloat(wastage)+parseFloat(soft)+parseFloat(local);
				// }
				// for(i=0;i<total_new_entry;i++)
				// {
				
				// }
			// }
			
			if(actualWt!="" && !isNaN(total)){
				document.getElementById("entryEffectiveWt").value = number_format(Math.abs(actualWt-total),2,'.','');
			}
		
			
			//Percentage calc;
			if(actualWt!=0){
			//alert("hii");
			
				document.getElementById("entryLocalPercent").value  = number_format(Math.abs((local*100)/actualWt),2,'.','');
				document.getElementById("entryWastagePercent").value = number_format(Math.abs((wastage*100)/actualWt),2,'.','');
				document.getElementById("entrySoftPercent").value = number_format(Math.abs((soft*100)/actualWt),2,'.','');
				for(i=0;i<total_new_entry;i++)
				{
					var entryValueDynamic = document.getElementById('quality_new_'+i).value;
					var entryValue = document.getElementById('qualityWeight_'+i).value;
					//alert(entryValue);
					document.getElementById("qualityPercent_"+i).value = number_format(Math.abs((entryValue*100)/actualWt),2,'.','');
					// var entryValueDynamic = document.getElementById('entry_new_'+i).value;
					// var entryValue = document.getElementById('entry'+entryValueDynamic).value;
					// document.getElementById("entry"+entryValueDynamic+"Percent").value = number_format(Math.abs((entryValue*100)/actualWt),2,'.','');
				}
			} else {
				document.getElementById("entryLocalPercent").value = 0.00;
				document.getElementById("entryWastagePercent").value = 0.00;
				document.getElementById("entrySoftPercent").value	=	0.00;
			}
	}

function validateAddDailyCatchEntry(form, mode, saveType)
{	
	

	var notInWeightment=document.getElementById("notInWeightment").value;
	if(notInWeightment==1)
	{
		alert("You need to add data in Weighment data sheet before adding in dailycatch entry ");
		return false;
	}
	var rmavailable=document.getElementById("lotIdAvailable");
	if(rmavailable.checked!=true)
	{
	//alert("hii");
		var supplyUnit		=	form.unit.value;
		var landingCenter	=	form.landingCenter.value;
		var mainSupplier	=	form.mainSupplier.value;
		//var subSupplier		=	form.subSupplier.value;
		var vechicleNo		=	form.vechicleNo.value;
		var supplyChallanNo =	form.supplyChallanNo.value;
		var billingCompany =	form.billingCompany.value;
	

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
		
				/*if (subSupplier=="") {
					alert("Please enter the Sub Supplier");
					form.subSupplier.focus();
					return false;
				}*/
		if (vechicleNo=="") {
			alert("Please enter the Vehicle Number");
			form.vechicleNo.focus();
			return false;
		}
		
	/*	if (supplyChallanNo=="") {
			alert("Please enter the Supply Challan Number");
			form.supplyChallanNo.focus();
			return false;
		}*/

		if (billingCompany=="") {
			alert("Please enter the Billing Company");
			form.billingCompany.focus();
			return false;
		}
	
	
	}
	else
	{
		/*var rm_lot_id =	form.rm_lot_id.value;
		var lotUnit =	form.lotUnit.value;
		var supplyLotChallanNo =	form.supplyLotChallanNo.value;
		var payment =	form.payment.value;
		var landingCenterLot =	form.landingCenterLot.value;
		var count_code =	form.count_code.value;*/

		
		var rm_lot_id =	document.getElementById("rm_lot_id").value;
		var lotUnit =	document.getElementById("lotUnit").value;
		var supplyLotChallanNo =	document.getElementById("supplyLotChallanNo").value;
		var payment =	document.getElementById("payment").value;
		var landingCenterLot =	document.getElementById("landingCenterLot").value;
		var count_code =	document.getElementById("count_code").value;
		var billingCompanyLot=document.getElementById("billingCompanyLot").value;




		if (rm_lot_id=="") {
			alert("Please enter the Rmlotid");
			form.rm_lot_id.focus();
			return false;
		}
		if (lotUnit=="") {
			alert("Please enter the Unit");
			form.lotUnit.focus();
			return false;
		}
		if (supplyLotChallanNo=="") {
			alert("Please enter the Supply Challan Number");
			form.supplyLotChallanNo.focus();
			return false;
		}
		if (payment=="") {
			alert("Please enter the Supplier Name");
			form.payment.focus();
			return false;
		}

		//alert(billingCompanyLot);
		if (billingCompanyLot=="") {
			alert("Please enter the Billing Company");
			//form.billingCompanyLot.focus();
			return false;
		}
		
		if (landingCenterLot=="") {
			alert("Please enter the Landing Center");
			 form.landingCenterLot.focus();
			 return false;
		}
		if (count_code=="") {
			alert("Please enter the count code");
			form.count_code.focus();
			return false;
		}
	}
	
	//alert("hii");
	
	
	var fishName		=	form.fish.value;
	var fishCode		=	form.processCode.value;
	var weighChallanNo	=	form.weighChallanNo.value;
	var entryLocal		=	form.entryLocal.value;
	var entryWastage	=	form.entryWastage.value;
	var entrySoft		=	form.entrySoft.value;
	var entryAdjust		=	form.entryAdjust.value;
	var goodPack		=	form.goodPack.value;
	var peeling		    =	form.peeling.value;
	var entryGross		=	form.entryGrossNetWt.value;
	var countChanged 	= 	form.saveChangesOk.value;
	var hidReceived		=	form.hidReceived.value;
	var entryOption		=	form.entryOption.value;
	var paymentBy		=	form.paymentBy.checked;
	var selectDate		=	form.selectDate.value;
	
	var entryEffectiveWt = form.entryEffectiveWt.value;
	var totalDeclaredWt  = document.getElementById("totalDeclaredWt").value;
	var hidSameEntryExist = document.getElementById("hidSameEntryExist").value;
	var hidSameCountAverage = document.getElementById("hidSameCountAverage").value;
	var validChallanDate		= document.getElementById("validDate").value;
	var hiddenWeighChallanNo		= document.getElementById("hiddenWeighChallanNo").value;

	
	
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
		/*if (totalDeclaredWt=="") {
			alert("Please enter declared Wt.");
			return false;
		}*/
		
		//var dWeight = formatNumber(Math.abs(totalDeclaredWt),2,'','.','','','','','');
		var dWeight = number_format(Math.abs(totalDeclaredWt),2,'.','');
		//alert(dWeight+'---'+entryEffectiveWt);
		//if (totalDeclaredWt>0 && dWeight!=entryEffectiveWt) {
		if (dWeight!=entryEffectiveWt) {
			alert("Declared and Effective Weight are not matching");	
			return false;
		}
	}
	
	if(!validateRepeatIssuance()){
		return false;
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
	if(rmavailable.checked==true)
	{
		var hiddenWeighChallanNo=document.getElementById("hiddenWeighChallanNo").value;
		if(hiddenWeighChallanNo!='')
		{
			if(weighChallanNo!=hiddenWeighChallanNo)
			{
				alert("Please make sure the challan no you have entered has the same supplier details .");
				return false;
			}
		}
	}
	var chkunit		= document.getElementById("unit").value;
	if(chkunit=="")
	{
		var chkunit		= document.getElementById("lotUnit").value;
	}
		
	var chklandingCenter 	= document.getElementById("landingCenter").value;
	if(chklandingCenter=="")
	{
		var chklandingCenter		= document.getElementById("landingCenterLot").value;
	}
	var chkbillingCompany 	= document.getElementById("billingCompany").value;
	if(chkbillingCompany=="")
	{
		chkbillingCompany=document.getElementById("billingCompanyLot").value;
	}
	var chkSupplier 	= document.getElementById("mainSupplier").value;
	if(chkSupplier=="")
	{
		var chkSupplier 	= document.getElementById("payment").value;
	}
	var catchEntryNewId 	= document.getElementById("catchEntryNewId").value;
	//alert(weighChallanNo+'--,--'+chkunit+'--,--'+chkbillingCompany+'--,--'+chklandingCenter+'--,--'+chkSupplier);
	var chckStat=xajax_checkChallanStatus(weighChallanNo,chkunit,chkbillingCompany,chklandingCenter,chkSupplier);
	//alert(chckStat);
	if(!chckStat && hiddenWeighChallanNo=="")
	{
		alert("Please make sure the supplier details of challan no you have entered is not duplicate.");
		return false;
	}
	else
	{
		if (!confirmSave()) {
		return false;
		} else {
		//enable the lotid,company,unit,supplier,farm
		jQuery('#lotUnit').attr("disabled", false); 
		jQuery('#billingCompany').attr("disabled", false); 
		jQuery('#payment').attr("disabled", false); 
		jQuery('#pondName').attr("disabled", false);
		jQuery('#landingCenterLot').attr("disabled", false);
		jQuery('#billingCompanyLot').attr("disabled", false); 
			// Save Entry (Ajax Save)
			
			
			if (saveType!=null)
			//temp	
			saveRMInChallan(mode, saveType);
			return true;
		}
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
		//alert(splitLength);
		if(splitLength>1)
		{
			jQuery('#countAvg').show();
		}
		else
		{
			jQuery('#countAvg').hide();
		}
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
	//alert('rekha');
	//return false;
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
		document.getElementById('supplyChallanNo').value="";
		document.getElementById('supplyChallanNo').readOnly = true;
		document.getElementById('subSupplier').value="";
	}

	function enableFields()
	{
		document.getElementById('paymentBy').checked = false;
		document.getElementById('subSupplier').disabled = false;
		document.getElementById('supplyChallanNo').readOnly = false;		
	}

	function reloadDeclared(mainSupplier,landingCenter)
	{
		var catchEntryNewId = document.getElementById("catchEntryNewId").value;
		//var mainSupplier = document.getElementById("mainSupplier").value;
		//var landingCenter = document.getElementById("landingCenter").value;
		document.getElementById("CatchEntryDeclaredItem").src = "CatchEntryDeclaredItem.php?entryId="+catchEntryNewId+"&mainSupplier="+mainSupplier+"&landingCenter="+landingCenter;
	}

	/* function reloadQuality()
	 {
		 var catchEntryNewId = document.getElementById("catchEntryNewId").value;
		 document.getElementById("catchEntryQuality").src = "CatchEntryQuality_new.php?entryId="+catchEntryNewId;
	 }*/

	function displayReceivedType(receivedBy)
	{
		var addMode = document.getElementById('addMode').value;

		if (receivedBy=='C') {
			document.getElementById("countRow").style.display = "";
			document.getElementById("countRow").style.visibility="visible";
			//document.getElementById("countRowDesp").style.display = "";
			//document.getElementById("countRowDesp").style.visibility="visible";
			//document.getElementById("countAvg").style.display = "";
			//document.getElementById("countAvg").style.visibility="visible";
			document.getElementById("gradeRow").style.display = "none";
			document.getElementById("gradeRow").style.visibility="hidden";
		} else if (receivedBy=='G') {
			document.getElementById("countRow").style.display = "none";
			//document.getElementById("countRowDesp").style.display = "none";
			//document.getElementById("countAvg").style.display = "none";
			document.getElementById("count").value="";
			//document.getElementById("countAverage").value="";
			document.getElementById("gradeRow").style.display = "";
			document.getElementById("gradeRow").style.visibility="visible";
		} else if (receivedBy=='B') {
			document.getElementById("countRow").style.display = "";
			document.getElementById("countRow").style.visibility="visible";
			//document.getElementById("countRowDesp").style.display = "";
			//document.getElementById("countRowDesp").style.visibility="visible";
			//document.getElementById("countAvg").style.display = "";
			//document.getElementById("countAvg").style.visibility="visible";
			document.getElementById("gradeRow").style.display = "";
			document.getElementById("gradeRow").style.visibility="visible";
		} else {
			document.getElementById("countRow").style.display = "none";
			//document.getElementById("countRowDesp").style.display = "none";
			//document.getElementById("countAvg").style.display = "none";
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
	function setGrossWt(wt)
	{
		document.getElementById("dailyBasketWt").value = wt; 		
		for(var i=1; i<=300; i++) {
			sv(i,wt);
			bWtArr.splice((i-1),1,wt);
		}	
	}
	
	// Save & Add New Raw Material in Challan	
	/*function saveRMInChallanold(mode, saveType)
	{
		//-------- Main Section ---------- 
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
		// --------- Entry Section -------------
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
		
		var supplyDetails = "";
		var make_payment = '';
		var payment = '';
		var count_code = '';
		var lotIdAvailable  = '';
		var supplierGroup = '';
		var pondName = '';
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
			// alert(billingCompany);
			// document.write(unit+'-----'+landingCenter+'#-----'+mainSupplier+'#-----'+vechicleNo+'#-----'+weighChallanNo+'#-----'+selectDate+'#-----'+selectTime+'#-----'+entryId+'#-----'+paymentBy+'#-----'+subSupplier+'#-----'+supplyChallanNo+'#-----'+billingCompany+'#-----'+alphaCode+'#-----'+fish+'#-----'+processCode+'#-----'+count+'#-----'+countAverage+'#-----'+entryLocal+'#-----'+entryWastage+'#-----'+entrySoft+'#-----'+reasonAdjust+'#-----'+entryAdjust+'#-----'+goodPack+'#-----'+peeling+'#-----'+entryRemark+'#-----'+entryActualWt+'#-----'+entryEffectiveWt+'#-----'+entryTotalGrossWt+'#-----'+entryTotalBasketWt+'#-----'+entryGrossNetWt+'#-----'+selGrade+'#-----'+dailyBasketWt+'#-----'+reasonLocal+'#-----'+reasonWastage+'#-----'+reasonSoft+'#-----'+entryOption+'#-----'+catchEntryNewId+'#-----'+gradeCountAdj+'#-----'+gradeCountAdjReason+'#-----'+mode+'#-----'+saveType+'#-----'+userId+'#-----'+cntArrStr+'#-----'+delArr+'#-----'+noBilling+'#-----'+cntArrdelStr);
			// var rm_lot_id = document.getElementById('rm_lot_id').value;
			xajax_saveChallan(unit, landingCenter, mainSupplier, vechicleNo, weighChallanNo, selectDate, selectTime, entryId, paymentBy, subSupplier, supplyChallanNo, billingCompany, alphaCode, fish, processCode, count, countAverage, entryLocal, entryWastage, entrySoft, reasonAdjust, entryAdjust, goodPack, peeling, entryRemark, entryActualWt, entryEffectiveWt, entryTotalGrossWt, entryTotalBasketWt, entryGrossNetWt, selGrade, dailyBasketWt, reasonLocal, reasonWastage, reasonSoft, entryOption, catchEntryNewId, gradeCountAdj, gradeCountAdjReason, mode, saveType, userId, cntArrStr, delArr, noBilling,cntArrdelStr,rm_lot_id,supplyDetails,make_payment,payment,count_code,lotIdAvailable,supplierGroup,pondName);
			// var rm_lot_id = 
			// if(rm_lot_id != '') {
				// document.getElementById("wtCalcTotGrWt").style.display = "";
			// }
		}
		clearRMFields(saveType, mode);
	}*/

	// Save & Add New Raw Material in Challan	
	function saveRMInChallan(mode, saveType)
	{
		//alert("hii");
		/* -------- Main Section ---------- */
		var unit		= document.getElementById("unit").value;
		if(unit=="")
		{
			var unit		= document.getElementById("lotUnit").value;
		}
		
		var landingCenter 	= document.getElementById("landingCenter").value;
		if(landingCenter=="")
		{
			var landingCenter		= document.getElementById("landingCenterLot").value;
		}
		
		//alert(landingCenter);

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
		if(supplyChallanNo=="")
		{
		var supplyChallanNo		= document.getElementById("supplyLotChallanNo").value;
		}
		
		var billingCompany 	= document.getElementById("billingCompany").value;
		if(billingCompany=="")
		{
			billingCompany=document.getElementById("billingCompanyLot").value;
		}
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
		var payment     	=document.getElementById("payment").value;
		var pondName = document.getElementById("pondName").value;
		//alert(pondName);
		var count_code=document.getElementById("count_code").value;
		var weightId		= document.getElementById("weightmentId").value;
		
		var rm_lot_id=document.getElementById("rm_lot_id").value;
		var noBilling		= (noBilling)?'Y':'N';
		if(rm_lot_id=="")
		{
			var lotIdAvailable  = "0";
		}
		else
		{
			var lotIdAvailable  = "1";
		}
		//var lotIdAvailable  = document.getElementById("lotIdAvailable").value;
		var make_payment = document.getElementById("make_payment").value;
		//selGrade
		var supplyDetails = "";
		//var make_payment = '';
		//var payment = '';
		//var count_code = '';
		//var lotIdAvailable  = '';
		var supplierGroup = '';
		//var pondName = '';
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
		}
		
		 
		
		
		
		
		// Basket Wt Entry Chk ends here
		 cntQuantityArr=new Array();
		// alert("hii");
		var i1=0;
		 var total_new_entryVal=document.getElementById("total_new_entry").value;
		
		//alert(total_new_entryVal);
		for(i=0; i < total_new_entryVal; i++)
		{
		//alert("hii"+i);
			var status =  document.getElementById('Status_'+i).value;
				if (status!='N') 
				{
					//alert("then"+i);
						var qualityId    =  document.getElementById('qualityId_'+i).value;
						var nameOfquality  = document.getElementById('quality_new_'+i).value;
						//alert(nameOfquality);
						var quality_wt     =  document.getElementById('qualityWeight_'+i).value;
						var qualityPercent =  document.getElementById('qualityPercent_'+i).value;
						var reason       =  document.getElementById('qualityReason_'+i).value;
						var weightmentStatus       =  document.getElementById('weightmentStatus_'+i).value;
						var billing       =  document.getElementById('billing_'+i).value;
		//alert(total_new_entryVal);
		// var nameOfEnrty  = document.getElementById('entry_new_'+i).value;
		// var qualityId    = document.getElementById('qualityId_'+i).value;
		// var entry_wt     = document.getElementById('entry'+qualityId).value;
		// var entryPercent     = document.getElementById('entry'+qualityId+'Percent').value;
		// var reason     = document.getElementById('reason'+qualityId).value;
		
					if (qualityId!="" && qualityId!=0) {
					 
						var joinCntquantity    = total_new_entryVal+":"+nameOfquality+":"+quality_wt+":"+qualityPercent+":"+reason+":"+qualityId+":"+weightmentStatus+":"+billing;
						//alert(joinCntquantity);
						cntQuantityArr[i1] = joinCntquantity;
						i1++;
					}
				}
		}
		
		

		
		if (entryId || catchEntryNewId) {
			cntArrStr = cntArr.join(",");
			cntArrdelStr=cntArrdel.join(",");
			cntArrQuantityStr=cntQuantityArr.join(",");
			 //alert(cntArrQuantityStr);
			// Update Main table
			// alert(billingCompany);
			// document.write(unit+'-----'+landingCenter+'#-----'+mainSupplier+'#-----'+vechicleNo+'#-----'+weighChallanNo+'#-----'+selectDate+'#-----'+selectTime+'#-----'+entryId+'#-----'+paymentBy+'#-----'+subSupplier+'#-----'+supplyChallanNo+'#-----'+billingCompany+'#-----'+alphaCode+'#-----'+fish+'#-----'+processCode+'#-----'+count+'#-----'+countAverage+'#-----'+entryLocal+'#-----'+entryWastage+'#-----'+entrySoft+'#-----'+reasonAdjust+'#-----'+entryAdjust+'#-----'+goodPack+'#-----'+peeling+'#-----'+entryRemark+'#-----'+entryActualWt+'#-----'+entryEffectiveWt+'#-----'+entryTotalGrossWt+'#-----'+entryTotalBasketWt+'#-----'+entryGrossNetWt+'#-----'+selGrade+'#-----'+dailyBasketWt+'#-----'+reasonLocal+'#-----'+reasonWastage+'#-----'+reasonSoft+'#-----'+entryOption+'#-----'+catchEntryNewId+'#-----'+gradeCountAdj+'#-----'+gradeCountAdjReason+'#-----'+mode+'#-----'+saveType+'#-----'+userId+'#-----'+cntArrStr+'#-----'+delArr+'#-----'+noBilling+'#-----'+cntArrdelStr);
			// var rm_lot_id = document.getElementById('rm_lot_id').value;
			//alert("rekha");
		
			xajax_saveChallan(unit, landingCenter, mainSupplier, vechicleNo, weighChallanNo, selectDate, selectTime, entryId, paymentBy, subSupplier, supplyChallanNo, billingCompany, alphaCode, fish, processCode, count, countAverage, entryLocal, entryWastage, entrySoft, reasonAdjust, entryAdjust, goodPack, peeling, entryRemark, entryActualWt, entryEffectiveWt, entryTotalGrossWt, entryTotalBasketWt, entryGrossNetWt, selGrade, dailyBasketWt, reasonLocal, reasonWastage, reasonSoft, entryOption, catchEntryNewId, gradeCountAdj, gradeCountAdjReason, mode, saveType, userId, cntArrStr, delArr, noBilling,cntArrdelStr,rm_lot_id,supplyDetails,make_payment,payment,count_code,lotIdAvailable,supplierGroup,pondName,cntArrQuantityStr,weightId);
		
			//alert('rekha1');
		//return false;
		
		
			// var rm_lot_id = 
			// if(rm_lot_id != '') {
				// document.getElementById("wtCalcTotGrWt").style.display = "";
			// }
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
			document.getElementById("landingCenter").value = "";
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
			clearQuality();
			
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
			//setTimeout("reloadQuality()",1000);
			clearQuality();
			clearCountDetails();
			resetCountData();
			if (mode==2) alert("Successfully updated the Daily Raw Material entry.");//$msg_succDailyCatchUpdate
			else alert("Daily Raw Material Entry added successfully.");//$msg_succAddDailyCatch
		}
		
	}

	function clearQuality()
	{
		var totalEntry=document.getElementById("total_new_entry").value;
		for(j=0; j<totalEntry; j++)
		{
			if (totalEntry>0) {			
				if(document.getElementById("Row_"+j)!=null) {
					var tRIndex = document.getElementById("Row_"+j).rowIndex;	
					document.getElementById('tblQuality').deleteRow(tRIndex);	
				}
			}
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
	
	
	
function addQuality(tableId,qualityid,billingVal,qualityListName,percent,kg,reason,quality_new)
{
//alert("hii");
var qualitys=document.getElementById('allQualityhide').value;
if(qualitys!="")
{
var qual=JSON.parse(qualitys);
var len=qual.length;
}

var total_new_entry=document.getElementById('total_new_entry').value;
	 if(total_new_entry!="")
	{
	fldId=total_new_entry;
	}
	
/*if(document.getElementById('lotIdAvailable').checked==true)
{
 //alert("hii");
 var total_new_entry=document.getElementById('total_new_entry').value;
	 if(total_new_entry!="")
	{
	fldId=total_new_entry;
	}
}*/


//alert(len);
	var tbl		= document.getElementById(tableId);

	var lastRow	= tbl.rows.length;
	// alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "Row_"+fldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
	//var cell6	= row.insertCell(5);
	
	cell1.id = "srNo_"+fldId;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";
	//cell6.className	= "listing-item"; cell6.align	= "center";

		//alert("entered");
		//alert("<?=$vehileTypeId?>");
		var qualityName	= "<select name='qualityId_"+fldId+"' id='qualityId_"+fldId+"' onchange=\"xajax_getQualityDet(document.getElementById('qualityId_"+fldId+"').value,"+fldId+");\"  style='width:96px'><option value='0'>--Select--</option>";
		if(qualitys!="")
		{
			for(i=0;i< len; i++)
			{
			//alert(qual[i][0]);
			qualityName += "<option value='"+qual[i][0]+"' >"+qual[i][1]+"</option>";
			//alert(qual[i][0]);
			}
		}
	qualityName += "</select>";
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setTestRowItemStatusVal('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
		if(qualitys>0)
		{
			for(i=0;i< len; i++)
			{
			var checkin=qual[i][2];
			//document.getElementById('billing_"+fldId+"').checked = true;
			 //document.getElementById('billing_"+fldId+"').checked=true;
			 //var billing = "<input name='billing_"+fldId+"' type='checkbox' id='billing_"+fldId+"' size='15' value='"+billingVal+"'>";
			}
		}
			
	var billing = "<input name='billing_"+fldId+"' type='checkbox' id='billing_"+fldId+"' size='15' value='"+billingVal+"' onclick=\"checkStatus('"+fldId+"');\">";
	var hiddenFields = "<input name='Status_"+fldId+"' type='hidden' id='Status_"+fldId+"' value=''><input name='IsFromDB_"+fldId+"' type='hidden' id='IsFromDB_"+fldId+"' value='"+ds+"'><input type='hidden' name='quality_new_"+fldId+"' id='quality_new_"+fldId+"' value='"+quality_new+"'> <input type='hidden' name='weightmentStatus_"+fldId+"' id='weightmentStatus_"+fldId+"' value='0' />";

	
	//cell1.innerHTML	= billing;
	cell1.innerHTML	=qualityName+billing;
	cell2.innerHTML	="<input name='qualityPercent_"+fldId+"' type='text' id='qualityPercent_"+fldId+"' size='4' value='0.00' style='text-align:right' readonly/>%";	
	cell3.innerHTML	="<input name='qualityWeight_"+fldId+"' type='text' id='qualityWeight_"+fldId+"' size='4' onkeyup='return effectiveWtNew();' value=''  />Kg";
	cell4.innerHTML	="Reason<input name='qualityReason_"+fldId+"' type='text' id='qualityReason_"+fldId+"' size='18' value='' autocomplete='off'/>";
	cell5.innerHTML = imageButton+hiddenFields;	
	
	fldId		= parseInt(fldId)+1;
	document.getElementById('total_new_entry').value=fldId;	
	
	
	/*if(document.getElementById('lotIdAvailable').checked==true)
	{
	document.getElementById('total_new_entry').value=fldId;	
	}
	else
	{
	document.getElementById("hidQualityTableRowCount").value = fldId;	
	}*/

	//
	//
//code end

}	

function qualityCheckValue(val,rowcnt)
{
//alert("hii");
	if(val=="1")
	{
	document.getElementById("billing_"+rowcnt).checked=true;
	}
	else
	{
	document.getElementById("billing_"+rowcnt).checked=false;
	}
//alert(rowcnt);
//if(val)
}
function setTestRowItemStatusVal(id)
{
	//alert(id);
	if (confirmRemoveItem()) {
	
		document.getElementById("Status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("Row_"+id).style.display = 'none';
			
			if(document.getElementById("billing_"+id).checked)
			{
				var quantityWt=document.getElementById("qualityWeight_"+id).value;
				var effectiveWt=document.getElementById("entryEffectiveWt").value;
				//alert(quantityWt);
				//alert(effectiveWt);
				//document.getElementById("entryEffectiveWt").value=parseFloat(effectiveWt)-parseFloat(quantityWt);
				document.getElementById("entryEffectiveWt").value=parseFloat(effectiveWt)+parseFloat(quantityWt);
			}
			//effectiveWtNew();
	}
	return false;
}
function checkStatus(rowid)	
{
	var quantityWt=document.getElementById("qualityWeight_"+rowid).value;
	var effectiveWt=document.getElementById("entryEffectiveWt").value;
	//alert(quantityWt+'------'+effectiveWt);
	
		if(document.getElementById("billing_"+rowid).checked)
		 {	
			if((quantityWt!="" && quantityWt!="0") && (effectiveWt!="" && effectiveWt!="0"))
			{
				var entryEffectiveWt=parseFloat(effectiveWt)-parseFloat(quantityWt);
				document.getElementById("entryEffectiveWt").value=number_format(Math.abs(entryEffectiveWt),2,'.','');
			}
				document.getElementById("billing_"+rowid).value=1;
				
				/*document.getElementById("entryEffectiveWt").value=parseFloat(effectiveWt)+parseFloat(quantityWt);
				document.getElementById("billing_"+rowid).value=1;*/
		 }
		 else
		 {		
			if((quantityWt!="" && quantityWt!="0") && (effectiveWt!="" && effectiveWt!="0"))
			{
				var entryEffectiveWt=parseFloat(effectiveWt)+parseFloat(quantityWt);
				document.getElementById("entryEffectiveWt").value=number_format(Math.abs(entryEffectiveWt),2,'.','');
			}
				document.getElementById("billing_"+rowid).value=0;
				
				/*	document.getElementById("entryEffectiveWt").value=parseFloat(effectiveWt)-parseFloat(quantityWt);
				document.getElementById("billing_"+rowid).value=0;*/
		  }
	
}
function fieldHidden()
{
	jQuery('#lotUnit').attr("disabled", true); 
	jQuery('#billingCompany').attr("disabled", true); 
	jQuery('#payment').attr("disabled", true); 
	jQuery('#pondName').attr("disabled", true);
	var weigh=jQuery('#weighChallanNo').val();	
	jQuery('#hiddenWeighChallanNo').val(weigh);
	
}
function getDetail(rm_lot_id)
{
	document.getElementById("weightmentId").value="";
	xajax_getSuppierDetails(rm_lot_id);
	$('html').addClass('overlay');
	//var activePopup = $(this).attr('data-popup-target');
	$('#example-popup').addClass('visible');
	document.getElementById("notInWeightment").value=0;
}
function requireInDatasheet(rm_lot_id)
{
	
	jQuery('#lotUnit').attr("disabled", false); 
	jQuery('#billingCompanyLot').attr("disabled", false);
	jQuery('#payment').attr("disabled", false); 
	jQuery('#pondName').attr("disabled", false);
	
	document.getElementById("weightmentId").value="";
	document.getElementById("lotUnit").value="";
	document.getElementById("supplyLotChallanNo").value="";
	document.getElementById("billingCompanyLot").value="";
	document.getElementById("make_payment").checked=false;
	document.getElementById("payment").value="";
	document.getElementById("pondName").value="";
	document.getElementById("count_code").value="";
	document.getElementById("notInWeightment").value=1;
	// var fishOptions = "<option value='0'>--select--</option>";
	// jQuery('#fish').html(fishOptions);
	var processCodeOptions = "<option value='0'>--select--</option>";
	jQuery('#processCode').html(processCodeOptions);
	xajax_getCountCode();
	
	alert("You need to add data in Weighment data sheet before adding in dailycatch entry ");
	return false;
}
function confirmInDatasheet(rm_lot_id)
{
	
	jQuery('#lotUnit').attr("disabled", false); 
	jQuery('#billingCompanyLot').attr("disabled", false);
	jQuery('#payment').attr("disabled", false); 
	jQuery('#pondName').attr("disabled", false);
	
	document.getElementById("weightmentId").value="";
	document.getElementById("lotUnit").value="";
	document.getElementById("supplyLotChallanNo").value="";
	document.getElementById("billingCompanyLot").value="";
	document.getElementById("make_payment").checked=false;
	document.getElementById("payment").value="";
	document.getElementById("pondName").value="";
	document.getElementById("count_code").value="";
	document.getElementById("notInWeightment").value=1;
	// var fishOptions = "<option value='0'>--select--</option>";
	// jQuery('#fish').html(fishOptions);
	var processCodeOptions = "<option value='0'>--select--</option>";
	jQuery('#processCode').html(processCodeOptions);
	xajax_getCountCode();
	
	alert("You need to confirm data in Weighment data sheet before adding in dailycatch entry ");
	return false;
}

function getSupplierDetail(rm_lot_id)
{
	xajax_getSuppierDetailsWeightment(rm_lot_id);
	$('html').addClass('overlay');
	//var activePopup = $(this).attr('data-popup-target');
	$('#example-popup').addClass('visible');
	document.getElementById("notInWeightment").value=0;
}

function getChellanDetail(mode)
{
	var rmavailable=document.getElementById("lotIdAvailable");
	if(rmavailable.checked!=true)
	{
		
		xajax_chkValidCNum(document.getElementById('billingCompany').value, document.getElementById('weighChallanNo').value, document.getElementById('selectDate').value, document.getElementById('entryId').value,document.getElementById('unit').value, mode);
	}
	else
	{ 
		
		//alert(document.getElementById('billingCompanyLot').value+","+document.getElementById('weighChallanNo').value+","+document.getElementById('selectDate').value+","+document.getElementById('entryId').value+","+document.getElementById('lotUnit').value);
		xajax_chkValidCNum(document.getElementById('billingCompanyLot').value, document.getElementById('weighChallanNo').value, document.getElementById('selectDate').value, document.getElementById('entryId').value,document.getElementById('lotUnit').value, mode);
	}
	
}



function validateRepeatIssuance()
{
//alert('aaa');
	if (Array.indexOf != 'function') {  
	Array.prototype.indexOf = function(f, s) {
		if (typeof s == 'undefined') s = 0;
		for (var i = s; i < this.length; i++) {   
		if (f === this[i]) return i; 
		}    
		return -1;  
		}
	}
	
	
	
	var vd = document.getElementById("total_new_entry").value;
	var prevOrders = 0;
	
	var arry = new Array();
	var arriy=0;
	for( l=0; l<vd; l++ )	{
	    var status = document.getElementById("Status_"+l).value;
	    if (status!='N') 
	    {
			var dv = document.getElementById("qualityId_"+l).value;	
			if ( arry.indexOf(dv) != -1 )	{
			alert("Quality Name  Cannot be duplicate.");
			document.getElementById("qualityId_"+l).focus();
			return false;
		}
		arry[arriy++]=dv;
            }
	}
	
	return true;
}


function dispMsg()
{
	//alert("hii");
	var subSupplier=$("#subSupplier").val();
    var supplierChallanNo=$("#supplierChallanNo").val();
	var data="{subSupplier:"+subSupplier+"&supplierChallanNo:"+supplierChallanNo+"}";
	alert(data);
    $.ajax({
        type:"post",
		dataType:'json',
		contentType: "application/json",
		url:"CatchEntryDeclaredItem.php?action=displayMsg",
        data:"{subSupplier="+subSupplier+",supplierChallanNo="+supplierChallanNo+"}",
		success:function(data)
		{
			//	$("#name").val('');
			//	$("#message").val('');
            //    $("#comment").html(data);
		}
  });
		  
}