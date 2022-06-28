function validateTaxMaster(form)
{
	
	var baseCst	= form.baseCst.value;
	var hidBaseCst  = (document.getElementById("hidBaseCst").value)?parseFloat(document.getElementById("hidBaseCst").value):"";

	var selRateList		= document.getElementById("selRateList").value;
	var cstRateListId = document.getElementById("cstRateListId").value;
	
	if (baseCst=="" ) {
		alert("Please enter base CST.");
		form.baseCst.focus();
		return false;
	}
	
	if (!checkNumber(baseCst)) {
		form.baseCst.value = "";
		return false;
	}

	if (selRateList=="") {		
		var startDate = document.getElementById("startDate");
		
		if (startDate.value=="") {
			alert("Please select a start date.");
			startDate.focus();
			return false;
		}
	}

	if (cstRateListId!="" && baseCst!="" && parseFloat(baseCst)!=hidBaseCst) {
		var effectType = document.getElementById("effectType");

		if (effectType.value=="") {
			alert("Please select effect type.");
			effectType.focus();
			return false;
		}

		if (effectType.value=='F') {
			var sDate = document.getElementById("sDate");
			if (sDate.value=="") {
				alert("Please select a start date.");
				sDate.focus();
				return false;
			}
		}		
	}	
		
	if (!confirmSave()) return false;
	else return true;
}

function validateTaxMaster_gst(form)
{
	alert("gst validation");
	return true;
}

function validateTaxMaster_igst(form)
{
	alert("igst validation");
	return true;
}



function validateExciseTaxMaster(form)
{
	
	var excBaseCst	= form.excBaseCst.value;
	var hidExcBaseCst  = (document.getElementById("hidExcBaseCst").value)?parseFloat(document.getElementById("hidExcBaseCst").value):"";

	var selExciseRateList		= document.getElementById("selExciseRateList").value;
	var excCstRateListId = document.getElementById("excCstRateListId").value;
	
	if (excBaseCst=="" ) {
		alert("Please enter excise base CST.");
		form.excBaseCst.focus();
		return false;
	}
	
	if (!checkNumber(excBaseCst)) {
		form.excBaseCst.value = "";
		return false;
	}

	if (selExciseRateList=="") {		
		var startDate = document.getElementById("excStartDate");
		
		if (startDate.value=="") {
			alert("Please select a start date.");
			startDate.focus();
			return false;
		}
	}

	if (excCstRateListId!="" && excBaseCst!="" && parseFloat(excBaseCst)!=hidExcBaseCst) {
		var effectType = document.getElementById("excEffectType");

		if (effectType.value=="") {
			alert("Please select effect type.");
			effectType.focus();
			return false;
		}

		if (effectType.value=='EF') {
			var sDate = document.getElementById("excSDate");
			if (sDate.value=="") {
				alert("Please select a start date.");
				sDate.focus();
				return false;
			}
		}		
	}	
		
	if (!confirmSave()) return false;
	else return true;
}

function validateECess(form)
{	
	
	var eCess	= form.eCess.value;	
	var hidECess = (document.getElementById("hidECess").value)?parseFloat(document.getElementById("hidECess").value):"";
	var selECessRateList		= document.getElementById("selECessRateList").value;
	var eCessRateListId = document.getElementById("eCessRateListId").value;
	
	if (eCess=="" ) {
		alert("Please enter edu cess duty.");
		form.eCess.focus();
		return false;
	}
	
	if (!checkNumber(eCess)) {
		form.eCess.value = "";
		return false;
	}

	if (selECessRateList=="") {		
		var startDate = document.getElementById("eCessStartDate");
		
		if (startDate.value=="") {
			alert("Please select a start date.");
			startDate.focus();
			return false;
		}
	}

	if (eCessRateListId!="" && eCess!="" && parseFloat(eCess)!=hidECess) {
		var effectType = document.getElementById("eCessEffectType");

		if (effectType.value=="") {
			alert("Please select effect type.");
			effectType.focus();
			return false;
		}

		if (effectType.value=='ECF') {
			var sDate = document.getElementById("eCessSDate");
			if (sDate.value=="") {
				alert("Please select a start date.");
				sDate.focus();
				return false;
			}
		}		
	}	
		
	if (!confirmSave()) return false;
	else return true;
}

function validateSecECess(form)
{
	var secECess	= form.secECess.value;
	var hidSecECess = (document.getElementById("hidSecECess").value)?parseFloat(document.getElementById("hidSecECess").value):"";

	var selSecECessRateList		= document.getElementById("selSecECessRateList").value;
	var secECessRateListId = document.getElementById("secECessRateListId").value;
	
	if (secECess=="" ) {
		alert("Please enter secondary edu cess duty.");
		form.secECess.focus();
		return false;
	}
	
	if (!checkNumber(secECess)) {
		form.secECess.value = "";
		return false;
	}

	if (selSecECessRateList=="") {		
		var startDate = document.getElementById("secECessStartDate");
		
		if (startDate.value=="") {
			alert("Please select a start date.");
			startDate.focus();
			return false;
		}
	}

	if (secECessRateListId!="" && secECess!="" && parseFloat(secECess)!=hidSecECess) {
		var effectType = document.getElementById("secECessEffectType");

		if (effectType.value=="") {
			alert("Please select effect type.");
			effectType.focus();
			return false;
		}

		if (effectType.value=='SECF') {
			var sDate = document.getElementById("secECessSDate");
			if (sDate.value=="") {
				alert("Please select a start date.");
				sDate.focus();
				return false;
			}
		}		
	}	
		
	if (!confirmSave()) return false;
	else return true;
}
	function chkCSTChange()
	{
		var baseCst = (document.getElementById("baseCst").value)?parseFloat(document.getElementById("baseCst").value):"";
		var hidBaseCst = (document.getElementById("hidBaseCst").value)?parseFloat(document.getElementById("hidBaseCst").value):"";
		var cstActive  = document.getElementById("cstActive").checked;
		var hidCSTActive = (document.getElementById("hidCSTActive").value=='Y')?true:false;
		var cstRateListId = document.getElementById("cstRateListId").value;
		//alert(baseCst+"="+hidBaseCst);
		if ((baseCst!=hidBaseCst || cstActive!=hidCSTActive) && baseCst!="" && cstRateListId!="") {
			//alert(baseCst+"="+hidBaseCst);
			document.getElementById("rateListRow").style.display="";			
		} else document.getElementById("rateListRow").style.display="none";
	}
	function chkExcCSTChange()
	{
		var excBaseCst = (document.getElementById("excBaseCst").value)?parseFloat(document.getElementById("excBaseCst").value):"";
		var hidExcBaseCst = (document.getElementById("hidExcBaseCst").value)?parseFloat(document.getElementById("hidExcBaseCst").value):"";
		var excCstActive  = document.getElementById("excCstActive").checked;
		var hidExcCstActive = (document.getElementById("hidExcCstActive").value=='Y')?true:false;
		var excCstRateListId = document.getElementById("excCstRateListId").value;
		//alert(baseCst+"="+hidBaseCst);
		if ((excBaseCst!=hidExcBaseCst || excCstActive!=hidExcCstActive) && excBaseCst!="" && excCstRateListId!="") {
			//alert(baseCst+"="+hidBaseCst);
			document.getElementById("excRateRow").style.display="";			
		} else document.getElementById("excRateRow").style.display="none";
	}
	
	function chkECessChange()
	{		
		var eCess = (document.getElementById("eCess").value)?parseFloat(document.getElementById("eCess").value):"";
		var hidECess = (document.getElementById("hidECess").value)?parseFloat(document.getElementById("hidECess").value):"";
		var eCessActive  = document.getElementById("eCessActive").checked;
		var hidECessActive = (document.getElementById("hidECessActive").value=='Y')?true:false;
		var eCessRateListId = document.getElementById("eCessRateListId").value;
		//alert(eCess+"="+hidECess);
		if ((eCess!=hidECess || eCessActive!=hidECessActive) && eCess!="" && eCessRateListId!="") {
			//alert(baseCst+"="+hidBaseCst);
			document.getElementById("eCessRateRow").style.display="";			
		} else document.getElementById("eCessRateRow").style.display="none";
	}

	function chkSecECessChange()
	{
		var secECess = (document.getElementById("secECess").value)?parseFloat(document.getElementById("secECess").value):"";
		var hidSecECess = (document.getElementById("hidSecECess").value)?parseFloat(document.getElementById("hidSecECess").value):"";
		var secECessActive  = document.getElementById("secECessActive").checked;
		var hidSecECessActive = (document.getElementById("hidSecECessActive").value=='Y')?true:false;
		var secECessRateListId = document.getElementById("secECessRateListId").value;
		//alert(secECess+"="+hidSecECess);
		if ((secECess!=hidSecECess || secECessActive!=hidSecECessActive) && secECess!="" && secECessRateListId!="") {
			//alert(baseCst+"="+hidBaseCst);
			document.getElementById("secECessRateRow").style.display="";			
		} else document.getElementById("secECessRateRow").style.display="none";
	}

	function changeEffectType()
	{
		var effectType = document.getElementById("effectType").value;
		//alert(effectType);
		if (effectType=='F') document.getElementById("futureRow").style.display="";
		else {
			document.getElementById("futureRow").style.display="none";
			document.getElementById("sDate").value = "";
		}
	}

	function changeExcEffectType()
	{
		var effectType = document.getElementById("excEffectType").value;
		//alert(effectType);
		if (effectType=='EF') document.getElementById("excFutureRow").style.display="";
		else {
			document.getElementById("excFutureRow").style.display="none";
			document.getElementById("excSDate").value = "";
		}
	}

	function changeECessEffectType()
	{
		var effectType = document.getElementById("eCessEffectType").value;
		//alert(effectType);
		if (effectType=='ECF') document.getElementById("eCessFutureRow").style.display="";
		else {
			document.getElementById("eCessFutureRow").style.display="none";
			document.getElementById("eCessSDate").value = "";
		}
	}

	function changeSecECessEffectType()
	{
		var effectType = document.getElementById("secECessEffectType").value;
		//alert(effectType);
		if (effectType=='SECF') document.getElementById("secECessFutureRow").style.display="";
		else {
			document.getElementById("secECessFutureRow").style.display="none";
			document.getElementById("secECessSDate").value = "";
		}
	}

	function cfmDel()
	{
		var cfmMsg = "Do you wish to delete the selected rate list record?";
		if (!confirm(cfmMsg)) return false;
		return true;
	}

	function showTax(taxType)
	{
		hideAlltax();
		$("#taxSummaryDiv").hide();
		if (taxType=='BCST') $("#baseCSTDiv").show();		
		else if (taxType=='EDUC') $("#eduCessDiv").show();
		else if (taxType=='SEDUC') $("#secEduCessDiv").show();
		else if (taxType=='GST') $("#GSTDiv").show();
		else if (taxType=='IGST') $("#IGSTDiv").show();

	}

	function hideAlltax()
	{
		$("#baseCSTDiv").hide();
		$("#eduCessDiv").hide();
		$("#secEduCessDiv").hide();
		$("#GSTDiv").hide();
		$("#IGSTDiv").hide();
	}

	function cancelTax()
	{
		hideAlltax();
		$("#taxSummaryDiv").show();
	}

