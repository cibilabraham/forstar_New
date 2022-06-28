function validateDistMarginStructureMaster(form)
{
	var selDistributor	= form.selDistributor.value;	
	var distMarginRateList	= form.distMarginRateList.value;
	var stateSelected	= false;
	var mode		= document.getElementById("hidMode").value;
	var hidSelection	= document.getElementById("hidSelection").value;
	var copyFromEnabled	= document.getElementById("copyFromEnabled").value; /* Enabled value 1 otherwise 0*/

	var selectionType = document.getElementById("selectionType").value;

	if (hidSelection=='I' || mode==1) {
		var selProduct		= form.selProduct.value;	
	}

	if (selDistributor=="") {
		alert("Please select a Distributor.");
		form.selDistributor.focus();
		return false;
	}

	if (mode==1 && selectionType=="") {
		var selProductCategory = form.selProductCategory.value;
		var selProductState    = document.getElementById("selProductState").value;
		var selProductGroup    = document.getElementById("selProductGroup").value;
		if (selProduct=="" && selProductCategory=="" && selProductState!=0) {
			var dMsg = " Distributor margin will apply against all Product category.\n Do you wish to Continue? ";
			if (!confirm(dMsg)) return false;
		} else if (selProduct=="" && selProductCategory=="" && selProductState==0) {
			var dMsg = " Distributor margin will apply against all Product category and State.\n Do you wish to Continue? ";
			if (!confirm(dMsg)) return false;
		} else if (selProduct=="" && selProductCategory!="" && selProductState==0) {
			var dMsg = " Distributor margin will apply against all Product State.\n Do you wish to Continue? ";
			if (!confirm(dMsg)) return false;
		}
	} 
	else  if (selProduct=="" && hidSelection=='I' && copyFromEnabled==0) {
		alert("Please select a Product.");
		form.selProduct.focus();
		return false;
	} 

	if (selectionType=='I') {
		var selProduct		= form.selProduct.value;
		if (selProduct=="") {
			alert("Please select a Product.");
			form.selProduct.focus();
			return false;
		} 
	}

	if (mode==1) {
		var mgnCopyChk 	= document.getElementById("marginSelection1").checked;
		var mgnSetChk 	= document.getElementById("marginSelection2").checked;
		if (!mgnCopyChk && !mgnSetChk) {
			alert("Please select copy/Set Margin");
			document.getElementById("marginSelection2").focus();
			return false;
		}
	}

	if (mode==1 && copyFromEnabled==1) {
		var selDistMargin 	= document.getElementById("selDistMargin").value;
		var copyFromDistId 	= document.getElementById("copyFromDistId").value;

		if (copyFromDistId=="") {
			alert("Please select a Distributor.");
			document.getElementById("copyFromDistId").focus();
			return false;
		}
		
		if (selDistMargin=="") {
			alert("Please select a Margin.");
			document.getElementById("selDistMargin").focus();
			return false;
		}
	}	
	
	if (copyFromEnabled==0) {
		var hidDistStateRowCount = document.getElementById("hidDistStateRowCount").value;
		for (j=1; j<=hidDistStateRowCount; j++) {
			var avgMargin 		= document.getElementById("avgMargin_"+j);
			var octroi		= document.getElementById("octroi_"+j);
			var vat			= document.getElementById("vat_"+j);
			var freight		= document.getElementById("freight_"+j);	
			var transportCost 	= document.getElementById("transportCost_"+j);

			if (avgMargin.value=="" || avgMargin.value==0) {
				alert("Please define Distributor Margin Structure.");
				return false;
			}

			if (octroi.value=="") {
				alert("Please enter Octroi Percentage.");
				octroi.focus();
				return false;
			}
			if (vat.value=="") {
				alert("Please enter VAT Percentage.");
				vat.focus();
				return false;
			}
			if (freight.value=="") {
				alert("Please enter Freight Percentage.");
				freight.focus();
				return false;
			}
			if (transportCost.value=="") {
				alert("Please enter Transport Cost.");
				transportCost.focus();
				return false;
			}
			stateSelected	= true;
		}
	
		if (!stateSelected) {
			alert("Please assign a state for the selected distributor");
			return false;
		}
	}	
	if (mode==2) {
		var confirmRateListMsg= confirm("Do you want to save this to new Rate list?");
		if (confirmRateListMsg) {
			alert("Please create a new distributor margin rate list and then update the record.");
			return false;
		}		
	}

	if (!confirmSave()) return false;
	return true;
}

	function valiateSwitchMargin()
	{
		var selMargin = document.getElementById("selMargin").value;
		if (selMargin=="") {
			alert("Please select an existing Margin");
			document.getElementById("selMargin").focus();
			return false;
		}

		if (!confirmSave()) return false;
		return true;		
	}

	function valiateDeleteMargin()
	{		
		var selProduct = document.getElementById("selProduct").value;
		
		if (selProduct=="") {
			alert("Please select a Product.");
			document.getElementById("selProduct").focus();
			return false;
		}
		var conDelMsg	= "Do you wish to delete the selected items?";
		if (!confirm(conDelMsg)) return false;
		return true;		
	}

	// display CST % When select Form F= Y
	/*
	function calcDisplayCSTDisc()
	{
		var hidCstRate = document.getElementById("hidCstRate").value;
		//var cstDisc	= document.getElementById("cstDisc").value;
		var billingForm = document.getElementById("billingForm").value;
		if (billingForm=='Y') document.getElementById("cstDisc").value = hidCstRate;
		else document.getElementById("cstDisc").value = 0;
	}
	*/


	/****************************************
	Execution Order is 1) expression in brackets 2) division 3) multiplication 4) addition and 5) subtraction 
	aVG MAGN=>1-((((1-Retailer Margin)*(1-Sub-Dist Margin))/(1+VAT Comp)/(1+Entry Tax))*((1-Dist Margin)*(1-Dist CD))*((1-SS Margin)*(1-SS CD))/(1+CST Disc));	
	=>1-((((1-(20/100))*(1-(0/100)))/(1+(4/100))/(1+(0/100)))*((1-(8/100))*(1-(2/100)))*((1-(0/100))*(1-(0/100)))/(1+(0/100)))
	***************************************/

	// Calculate Average distributor margin Structure
	/************************************************
		markup  /(1+%) -- markdown *(1-%)
	************************************************/
function calcDistAvgMarginStruct()
{	
	var hidDistStateRowCount = document.getElementById("hidDistStateRowCount").value;
	
	var hidCstRate 		= document.getElementById("hidCstRate").value;
	//var eduCessPercent 	= $("#hidEduCessPercent").val();
	//var secEduCessPercent 	= $("#hidSecEduCessPercent").val();

	var taxRate = 0;
	var calcTaxRate = 0;

	for (j=1; j<=hidDistStateRowCount; j++) {			
		var sBillingForm = document.getElementById("billingForm_"+j).value; // Distributor State Base
		var taxType	 = document.getElementById("taxType_"+j).value;
		var billingForm	= "";
		//Billing Form VN: VAT NO, CFF: Form F, FC:Form C, FN:Form None
		if (sBillingForm=='FF' || sBillingForm=='FC' || sBillingForm=='FN') {
			billingForm = 'Y';
		} else if (sBillingForm=='VN') {
			billingForm = 'N';
		}
		var hidFieldRowCount 	= document.getElementById("hidFieldRowCount_"+j).value;
		var vatOrCSTRate	= parseFloat(document.getElementById("vat_"+j).value/100);
		var octroiMarkupValue = 0;
		var octroi	= parseFloat(document.getElementById("octroi_"+j).value/100);
		// If VAT/CST included in Margin==Y  don't Add VAT/CST in AVG Margin else add
		var vatorCstInc 	= document.getElementById("vatorCstInc_"+j).checked; //Y/N
		// Excise Duty
		var exciseDutyRate   = parseFloat(document.getElementById("exciseDuty_"+j).value/100);
		
		var actualValue = 0;
		var calcDistMargin = 0;	
		
		var calcMarkUpValue=0;
		var totalMarkUpValue=1;
		var totalMarkDownValue = 1;
		var distMarginPercent = 0;
		tMarkUpValue = 1;
		tMarkDownValue = 1;
		cMarkUpValue = 0;
		cMarkDownValue = 0;
		var calcFinalDistMgn = 0;
		var calcDiscountMgn = 0;
		
		for (i=1; i<=hidFieldRowCount; i++) {
			if (document.getElementById("distMarginPercent_"+i+"_"+j).value!="") {
				var distMarginPercent = parseFloat(document.getElementById("distMarginPercent_"+i+"_"+j).value);
			} else {
				document.getElementById("distMarginPercent_"+i+"_"+j).value = 0;
			}
			var priceCalcType = document.getElementById("priceCalcType_"+i+"_"+j).value;	
			var useAvgDistMagn = document.getElementById("useAvgDistMagn_"+i+"_"+j).value;
			var mgnStructBillingOnFormF = document.getElementById("mgnStructBillingOnFormF_"+i+"_"+j).value;
			
			if (mgnStructBillingOnFormF=='Y' && billingForm=='Y') {
				document.getElementById("distMarginPercent_"+i+"_"+j).value = hidCstRate;
			} else if(mgnStructBillingOnFormF=='Y' && billingForm=='N') {
				document.getElementById("distMarginPercent_"+i+"_"+j).value = 0;
			}

			actualValue =  parseFloat(distMarginPercent/100);
			if (useAvgDistMagn=='Y') {				
				if (priceCalcType=='MU') {
					calcMarkUpValue = parseFloat(1+actualValue);
					totalMarkUpValue /= calcMarkUpValue;
				}

				if (priceCalcType=='MD') {
					calcMarkDownValue = parseFloat(1-actualValue);
					totalMarkDownValue *= calcMarkDownValue;
				}
			}
			
			if (useAvgDistMagn=='N') {				
				if (priceCalcType=='MU') {					
					cMarkUpValue = parseFloat(1+actualValue);
					tMarkUpValue /= cMarkUpValue;			
				}

				if (priceCalcType=='MD') {
					cMarkDownValue = parseFloat(1-actualValue);				
					tMarkDownValue *= cMarkDownValue;
				}
			}	
		} // Field Row count ends here	
		
		if (!vatorCstInc) {
			if (taxType=='CST') 		taxRate = parseFloat(hidCstRate/100);
			else if (taxType=='VAT')	taxRate = vatOrCSTRate;
			totalMarkUpValue = totalMarkUpValue/parseFloat(1+taxRate);
		}

		calcDistMargin = (1-(totalMarkUpValue*totalMarkDownValue))*100;
		
		if (!isNaN(calcDistMargin)) {
			document.getElementById("avgMargin_"+j).value = number_format(calcDistMargin,4,'.','');	
		}

		// Excise Duty
		var exDutyMarkUpValue	= parseFloat(1+exciseDutyRate);
		var basicMarkDownValue	= totalMarkDownValue*exDutyMarkUpValue;
		var calcBasicMgn	= (1-(1-(calcDistMargin/100))*exDutyMarkUpValue)*100;
		if (!isNaN(calcBasicMgn)) {
			document.getElementById("basicMargin_"+j).value = number_format(calcBasicMgn,4,'.','');	
		}

		// VAT/CST		
		var vatMarkUpValue	= parseFloat(1+vatOrCSTRate);
		// IF VAT, OCTROI => Default Markup but the calculation is mark down,
		//finalMarkDownValue = totalMarkDownValue*vatMarkUpValue; // 17 MAY 11	
		var finalMarkDownValue = basicMarkDownValue*vatMarkUpValue;
			
		// Final Margin
		calcFinalDistMgn = (1-(1-(calcDistMargin/100))*vatMarkUpValue*exDutyMarkUpValue)*100;

		if (!isNaN(calcFinalDistMgn)) {
			document.getElementById("finalMargin_"+j).value = number_format(calcFinalDistMgn,4,'.','');	
		}
		
		// Calc Total Mgn (Mark up /, Mark down *)		
		calcDiscountMgn = (parseFloat(totalMarkUpValue)/parseFloat(tMarkUpValue))*(parseFloat(finalMarkDownValue)*parseFloat(tMarkDownValue));
		
		// Octroi
		octroiMarkupValue = parseFloat(1+octroi);
		//calcDiscountMgn = calcDiscountMgn*octroiMarkupValue; 29-06
		calcDiscountMgn = parseFloat(calcDiscountMgn)/parseFloat(octroiMarkupValue);

		// Calc Actual Mgn		
		var calcActualDistMgn = (1-(calcDiscountMgn))*100;
		if (!isNaN(calcActualDistMgn)) {
			document.getElementById("actualMargin_"+j).value = number_format(calcActualDistMgn,4,'.','');	
		}
	} // State Count loop Ends Here
} /*Function Ends here*/


	function enableDistMgnStructButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableDistMgnStructButton(mode)
	{	
		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

	function hideCopyFromRows()
	{
		var mgnCopyChk 	= document.getElementById("marginSelection1").checked;
		var mgnSetChk 	= document.getElementById("marginSelection2").checked;

		//var copyFrom = document.getElementById("copyFromDistId").value;
		if (mgnCopyChk) {
			//document.getElementById("row0").style.display = "none";
			//document.getElementById("row1").style.display = "none";
			document.getElementById("row2").style.display = "none";	
			document.getElementById("copyFromRow").style.display = "";
			document.getElementById("copyFromEnabled").value = 1;		
		} else {
			//document.getElementById("row0").style.display = "";
			//document.getElementById("row1").style.display = "";
			
			document.getElementById("copyFromRow").style.display = "none";
			document.getElementById("row2").style.display = "";			
			document.getElementById("copyFromEnabled").value = 0;		
		}

		//if (mgnSetChk) 
	}

	function hideProductSpex(mode)
	{
		var selProduct = document.getElementById("selProduct").value;
		var selectionType = document.getElementById("selectionType").value;
		
		if (selProduct!="" || selectionType!="") {
			document.getElementById("selProductCategory").value = "";
			document.getElementById("selProductState").value = "";
			document.getElementById("selProductGroup").value = "";				
			document.getElementById("column0").style.display = "none";
			document.getElementById("column1").style.display = "none";	
			document.getElementById("singleProdEnabled").value = 1;		
		} else {
			document.getElementById("selProduct").value = "";
			document.getElementById("column0").style.display = "";
			document.getElementById("column1").style.display = "";					
			document.getElementById("singleProdEnabled").value = 0;	
		}

		if (selectionType=='G')  {
			document.getElementById("row1").style.display = "none"
			document.getElementById("selProduct").value = "";
		}
		else if (selectionType=='I') document.getElementById("row1").style.display = ""
	}

	//Key moving
	function nextTBox(e, form, name)
	{
		var ecode = getKeyCode(e);
		//alert(ecode);
		var sName = name.split("_");
		upArrowName = sName[0]+"_"+(parseInt(sName[1])-2)+"_"+(sName[2]);		
		rightArrow = sName[0]+"_"+(parseInt(sName[1])-1)+"_"+(parseInt(sName[2])+1);	
		leftArrow = sName[0]+"_"+(parseInt(sName[1])-1)+"_"+(sName[2]-1);

		var hidFieldRowCount = document.getElementById("hidFieldRowCount_"+parseInt(sName[2])).value;	
		if ((ecode==13) || (ecode == 0) || (ecode==40)) {
			if ((parseInt(sName[1])-1)==hidFieldRowCount) {
				var nextControl = eval(form+"."+"octroi_"+sName[2]);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			} else {
				var nextControl = eval(form+"."+name);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}
		}
		if ((ecode==38)){
			var nextControl = eval(form+"."+upArrowName);
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

	/*
		e= event, form= form name, nName=Next txt Name, cName = Current Txt Name, bName = back Txt Name
	*/
	function focusNextBox(e, form, nName, cName, bName)
	{		
		var ecode = getKeyCode(e);	
		
		var sName  = nName.split("_");
		var cTName = cName.split("_");
		var bTName = bName.split("_");
		upArrowName = bTName[0]+"_"+(parseInt(bTName[1]));		
		rightArrow = cTName[0]+"_"+(parseInt(cTName[1])+1);	
		leftArrow = cTName[0]+"_"+(parseInt(cTName[1])-1);
		var hidFieldRowCount = document.getElementById("hidFieldRowCount_"+parseInt(cTName[1])).value;	

		if ((ecode==13) || (ecode == 0) || (ecode==40)) {		
			var nextControl = eval(form+"."+nName);			
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}

		if ((ecode==38)) {
			if (cTName[0]==bTName[0]) {
				var nextControl = eval(form+"."+"distMarginPercent_"+hidFieldRowCount+"_"+cTName[1]);
				if (nextControl) { nextControl.focus(); }
				return false;
			} else {
				var nextControl = eval(form+"."+upArrowName);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}
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

	function cDelete()
	{
		var rowCount = document.getElementById("hidRowCount").value;
		var chkSel = false;
		for (i=1; i<=rowCount; i++) {
			var pRowCount = document.getElementById("pRowCount_"+i).value;
			for (j=1; j<=pRowCount; j++) {	
				var distMarginChk =	document.getElementById("delId_"+j+"_"+i).checked;
				if (distMarginChk) chkSel = true;
			}
		}
		
		if (chkSel) {
			var conDelMsg	=	"Do you wish to delete the selected items?";
			if (confirm(conDelMsg)) {
				return true;
			}		
		} else {
			alert("Please select a record to delete.");
			return false;
		}
		return false;
	}

	/*
		Validating Continue section
	*/
	function validateEditSelection()
	{
		var editSelection1 = document.getElementById("editSelection1").checked;
		var editSelection2 = document.getElementById("editSelection2").checked;
		if (!editSelection1 && !editSelection2) {
			alert("Please select Individual/Group");
			return false;
		}
		return true;
	}

	function hideListedRows()
	{
		var selMargin = document.getElementById("selMargin").value;
		if (selMargin!="") {			
			document.getElementById("row2").style.display = "none";	
			document.getElementById("rateListRow").style.display = "none";	
			document.getElementById("switchMarginEnabled").value = 1;			
		} else {			
			document.getElementById("row2").style.display = "";			
			document.getElementById("rateListRow").style.display = "";
			document.getElementById("switchMarginEnabled").value = 0;		
		}
	}

	/* Disable Rows in Add Section */
	function disableRows()
	{
		document.getElementById("copyFromRow").style.display 	= "none";
		document.getElementById("row2").style.display 		= "none";			
	}

	// Get State Wise Vat
	function getStateWiseVat()
	{
		var selProduct 		= document.getElementById("selProduct").value;
		var selDistributor	= document.getElementById("selDistributor").value;
		var stateCount 		= document.getElementById("hidDistStateRowCount").value;
		var distMarginRateList	= document.getElementById("distMarginRateList").value;

		for (i=1;i<=stateCount; i++ ) {			
			var selStateId = document.getElementById("selStateId_"+i).value;
			var billingForm = document.getElementById("billingForm_"+i).value;	
			if (billingForm=='ZP') document.getElementById("vat_"+i).value = 0; 			
			else xajax_getStateWiseVat(selProduct, selStateId, stateCount, i, selDistributor, distMarginRateList);
		}
		
	}

	//  Via Product Category
	function getStateVatPercent()
	{		
		var selDistributor	= document.getElementById("selDistributor").value;
		var stateCount 		= document.getElementById("hidDistStateRowCount").value;
		var selProductCategory  = document.getElementById("selProductCategory").value;
		var selProductState  	= document.getElementById("selProductState").value;
		var selProductGroup  	= document.getElementById("selProductGroup").value;
		var distMarginRateList	= document.getElementById("distMarginRateList").value;
		
		for (i=1;i<=stateCount; i++ ) {			
			var selStateId = document.getElementById("selStateId_"+i).value;
			xajax_getStateVatPercent(selDistributor, selStateId, i, selProductCategory, selProductState, selProductGroup, distMarginRateList);
		}		
	}

	// Get Octroi Percent
	function getOctroiPercent()
	{
		var stateCount = document.getElementById("hidDistStateRowCount").value;
		for (i=1;i<=stateCount; i++ ) {			
			var selCityId = document.getElementById("selCityId_"+i).value;
			var octroiApplicable = document.getElementById("hidOctroiApplicable_"+i).value;
			var octroiExempted = document.getElementById("hidOctroiExempted_"+i).value;

			if (octroiApplicable=='Y' && octroiExempted!='Y') xajax_getOctroiPercent(i, selCityId);
			else if (octroiApplicable=='Y' && octroiExempted=='Y') document.getElementById("octroi_"+i).value = 0;
		}
		//calcDistAvgMarginStruct();
	}

	// Update
	function updateDistMgnStruct()
	{
		var distributorId = document.getElementById("distributorFilter").value;
		var rateListId    = document.getElementById("distributorRateListFilter").value;
		
		var uptdMsg	= "Do you wish to revise the Distributor Margin Structure?";
		if(confirm(uptdMsg)) {
			var xv = xajax_updateRevisedMgnStruct(distributorId,rateListId);
			return true;
		}
		return false;	
	}
		