	function validateAssignDocketNo(form, saveBtn)
	{	
		var supplyFrom	=	form.supplyFrom.value;
		var supplyTill	=	form.supplyTill.value;
		var transporter	=	form.transporter.value;
	
		if (supplyFrom=="") {
			alert("Please select From Date");
			form.supplyFrom.focus();
			return false;
		}
			
		if (supplyTill=="") {
			alert("Please select To Date");
			form.supplyTill.focus();
			return false;
		}
		if (findDaysDiff(supplyFrom)>0 || findDaysDiff(supplyTill)>0){
			alert("Selected date should be less than or equal to current date");
			return false;	
		}
		if (checkDateSelected(supplyFrom,supplyTill)>0){
			alert("Please check selected From and To date");
			return false;
		}
		/*
		if(transporter=="")
			{
				alert("Please select a Transporter");
				form.transporter.focus();
				return false;
			}
		*/
		if (saveBtn) {	
			var rowCount = 	document.getElementById("hidRowCount").value;		
			for (i=1;i<=rowCount;i++) {
				var dispatchDate  = document.getElementById("dispatchDate_"+i);
				var deliveryDate  = document.getElementById("deliveryDate_"+i);
				if (deliveryDate.value!="") {
					if (convertTime(deliveryDate.value)<convertTime(dispatchDate.value)) {
						alert("Delivery date must be greater than or equal to invoice dispatch date");
						deliveryDate.focus();
						return false;
					}	
				}
			}
		} // Save btn check ends here
		return true;		
	}

	function updateTransporterSORec(form, saveBtn)
	{
		if (!validateAssignDocketNo(form, saveBtn)) {
			return false;
		}
	
		if (confirmSave()) {
			return true;
		} else {
			return false;
		}
	}

	// Submit function
	function getTransporter()
	{
		if(document.getElementById("supplyFrom").value!="" && document.getElementById("supplyTill").value!="")
		{
			document.frmAssignDocketNo.submit();
		}
		return false;
	}

	function nextBox(e,form,name)
	{
		var ecode = getKeyCode(e);
		var sName = name.split("_");
		dArrowName = sName[0]+"_"+(sName[1]-2);
		
		if ((ecode==13) || (ecode == 9) || (ecode==40)){
			var nextControl = eval(form+"."+name);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		if ((ecode==38)){
			var nextControl = eval(form+"."+dArrowName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}		
	}

	// Generate Delivery Date calender code
	function displayCalender(rowCount)
	{
		//var rowCount = 	document.getElementById("hidRowCount").value;		
		for (i=1;i<=rowCount;i++) {
			Calendar.setup 
			(	
				{
				inputField  : "deliveryDate_"+i,         // ID of the input field
				eventName	  : "click",	    // name of event
				button : "deliveryDate_"+i, 
				ifFormat    : "%d/%m/%Y",    // the date format
				singleClick : true,
				step : 1
				}
			);
		}
	}