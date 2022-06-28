	function validateDailyIceUsage(form)
	{	
		var selectDate	= document.getElementById("selectDate");
			
		//var issuedTo 	= document.getElementById("issuedTo");
		var qty 	= document.getElementById("qty");
		var unitId 	= document.getElementById("unitId");
		var sold	= document.getElementById("sold");
		var soldQty	= document.getElementById("soldQty");
		var adjQty	= document.getElementById("adjQty");
		var rate	= document.getElementById("rate");
		var customerType = document.getElementById("customerType");

		if (selectDate.value=="") {
			alert("Please select entry date.");
			selectDate.focus();
			return false;
		}

		if (customerType.value=="") {
			alert("Please select customer type.");	
			customerType.focus();
			return false;
		}
	

		if (customerType.value=='SU') {
			var supplier 	= document.getElementById("supplier");	

			if (supplier.value=="") {
				alert("Please select a supplier.");
				supplier.focus();
				return false;
			}
		} else if (customerType.value=='SE') {
			
			var plantId 	= document.getElementById("plantId");	

			if (plantId.value=="") {
				alert("Please select a Plant/Unit.");
				plantId.focus();
				return false;
			}

		} else if (customerType.value=='NW') {

			var partyName 	= document.getElementById("partyName");
			var partyLocation 	= document.getElementById("partyLocation");		

			if (partyName.value=="") {
				alert("Please enter name of party.");
				partyName.focus();
				return false;
			}

			if (partyLocation.value=="") {
				alert("Please enter party location.");
				partyLocation.focus();
				return false;
			}
		}


		/*
		if (issuedTo.value=="") {
			alert("Please select issued to.");
			issuedTo.focus();
			return false;
		}
		*/

		if (qty.value=="") {
			alert("Please enter qty.");
			qty.focus();
			return false;
		}

		if (unitId.value=="") {
			alert("Please select unit.");
			unitId.focus();
			return false;
		}

		if (sold.value=="" && customerType.value!='SE') {
			alert("Please select sold.");
			sold.focus();
			return false;
		}

		if (sold.value=='Y' && soldQty.value=="") {
			alert("Please enter sold qty.");
			soldQty.focus();
			return false;
		}

		if (sold.value=='Y' && rate.value=="") {
			alert("Please enter rate.");
			rate.focus();
			return false;
		}
		

		if (!confirmSave()) return false;
		else return true;
	}
	
	function validateDailyFreezingChartDetails(form)
	{
		
		var freezerName		=	form.freezerName.value;
	
	
		if (freezerName=="") {
			alert("Please select a Freezer.");
			form.freezerName.focus();
			return false;
		}
	
		if (!confirmSave()) return false;
		else return true;
	}
	
	function dailyFreezingChartTimeCheck()
	{
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

	// calc Total amt
	function calcAmt()
	{
		var calcAmt = 0;
		var soldQty	= parseFloat(document.getElementById("soldQty").value);
		var rate	= parseFloat(document.getElementById("rate").value);
		
		calcAmt = soldQty*rate;
		if (!isNaN(calcAmt)) document.getElementById("amount").value = number_format(calcAmt,2,'.','');		
	}

	