function validateRetailCounterMaster(form)
{
	//var code	= form.code.value;
	var retailCounterName	= form.retailCounterName.value;
	var contactPerson = form.contactPerson.value;
	var state	= form.state.value;
	var city	= form.city.value;
	var distributor = form.distributor.value;
	var area	= document.getElementById("area").value;
	var salesStaff	= form.salesStaff.value;
	var selRtCtCateogry	= form.selRtCtCateogry.value;
	
	//var disCharge	= form.disCharge.value;
	/*
	if (code=="") {
		alert("Please enter a Code.");
		form.code.focus();
		return false;
	}
	*/
	if (retailCounterName=="") {
		alert("Please enter a Retail Counter Name.");
		form.retailCounterName.focus();
		return false;
	}

	if (distributor=="") {
		alert("Please select a distributor.");
		form.distributor.focus();
		return false;
	}

	if (salesStaff=="") {
		alert("Please select a sales staff.");
		form.salesStaff.focus();
		return false;
	}
	
	if (contactPerson=="") {
		alert("Please enter a Contact Person Name.");
		form.contactPerson.focus();
		return false;
	}

	if (state=="") {
		alert("Please select a State.");
		form.state.focus();
		return false;
	}
	
	if (city=="") {
		alert("Please select a City.");
		form.city.focus();
		return false;
	}
	
	if (area=="") {
		alert("Please select atleast one operational area.");
		document.getElementById("area").focus();
		return false;
	}
	if (selRtCtCateogry=="") {
		alert("Please select a Retail Counter Category.");
		form.selRtCtCateogry.focus();
		return false;
	}
	/*
	if (disCharge!="" && !isDigit(disCharge)) {
		alert("please enter a number");
		//document.getElementById("disCharge").value="";
		return false;
	}
	if (disCharge!="") {
		var disTypeM = document.getElementById("disTypeM").checked;
		var disTypeD = document.getElementById("disTypeD").checked;
		//alert(disTypeM+","+disTypeD);
		if (!disTypeM && !disTypeD) {	
			alert("please select Month/Date");
			return false;
		}
		if (disTypeD) {
			var selectFrom = document.getElementById("selectFrom").value;
			var selectTill = document.getElementById("selectTill").value;
			if (selectFrom=="") {
				alert("Please select from date");
				document.getElementById("selectFrom").focus();
				return false;				
			}
			if (selectTill=="") {
				alert("Please select To date");
				document.getElementById("selectTill").focus();
				return false;				
			}
		}
	}	
	*/
	if (!confirmSave()) {
		return false;
	}
	return true;
}


function getState(formObj)
{
showFnLoading(); 
formObj.form.submit();
}


