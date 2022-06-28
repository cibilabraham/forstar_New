function validateUSDValue(form)
{
	var currencyCode		=	document.getElementById("currencyCode");
	var currencyValue		=	document.getElementById("currencyValue");
	var currencyDisplayMsg  =	document.getElementById("currencyDisplayMsg").value;
	var startDate			=	document.getElementById("startDate");
	var hidCurrencyCode = (document.getElementById("hidCurrencyCode").value)?(document.getElementById("hidCurrencyCode").value):"";
	var hidCurrencyValue = (document.getElementById("hidCurrencyValue").value)?parseFloat(document.getElementById("hidCurrencyValue").value):"";
	var hidCYRLId = document.getElementById("hidCYRLId").value;
	
	if ( currencyCode.value=="" ) {
		alert("Please enter Currency Code.");
		currencyCode.focus();
		return false;
	}

	if ( currencyValue.value=="" ) {
		alert("Please enter "+currencyDisplayMsg);
		currencyValue.focus();
		return false;
	}
	
	if(!isDigit(currencyValue.value)){
		alert("Please enter a number.");
		currencyValue.focus();
		return false;
	}

	if ( startDate.value=="" ) {
		alert("Please select a start date.");
		startDate.focus();
		return false;
	}

	//alert(currencyCode+"::hidCurrencyCode="+hidCurrencyCode+":currencyValue="+currencyValue+":hidCurrencyValue="+hidCurrencyValue);
	if ((currencyCode.value!=hidCurrencyCode || parseFloat(currencyValue.value)!=parseFloat(hidCurrencyValue)) && currencyValue!="" && hidCYRLId!="") {

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

function changeEffectType()
{
	var effectType = document.getElementById("effectType").value;
	if (effectType=='F') document.getElementById("futureRow").style.display="";
	else {
		document.getElementById("futureRow").style.display="none";
		document.getElementById("sDate").value = "";
	}
}

function chkCYChange()
{
	var currencyCode = (document.getElementById("currencyCode").value)?(document.getElementById("currencyCode").value):"";	
	var hidCurrencyCode = (document.getElementById("hidCurrencyCode").value)?(document.getElementById("hidCurrencyCode").value):"";
	var currencyValue = (document.getElementById("currencyValue").value)?parseFloat(document.getElementById("currencyValue").value):"";
	var hidCurrencyValue = (document.getElementById("hidCurrencyValue").value)?parseFloat(document.getElementById("hidCurrencyValue").value):"";
	var hidCYRLId = document.getElementById("hidCYRLId").value;
	if ((currencyCode!=hidCurrencyCode || currencyValue!=hidCurrencyValue) && currencyValue!="" && hidCYRLId!="") {
		//alert(baseCst+"="+hidBaseCst);
		document.getElementById("rateListRow").style.display="";			
	} else document.getElementById("rateListRow").style.display="none";
}

function curLoad(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();
	}