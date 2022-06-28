	function validateTransporterOtherCharge(form)
	{
		var selTransporter	= form.selTransporter.value;	
		
		var fovCharge		=	form.fovCharge.value;
		var docketCharge	=	form.docketCharge.value;
		var serviceTax		=	form.serviceTax.value;
		var octroiServiceCharge	=	form.octroiServiceCharge.value;	
		var mode		= 	document.getElementById("hidMode").value;
	
		if (selTransporter=="") {
			alert("Please select a Transporter.");
			form.selTransporter.focus();
			return false;
		}
	
		if (fovCharge=="" ) {
			alert("Please enter FOV Charge.");
			form.fovCharge.focus();
			return false;
		}
		
		if (!checkNumber(fovCharge)) {
			form.fovCharge.value = "";
			return false;
		}

		if (docketCharge=="" ) {
			alert("Please enter Docket Charges.");
			form.docketCharge.focus();
			return false;
		}
		
		if (!checkNumber(docketCharge)) {
			form.docketCharge.value = "";
			return false;
		}

		if (serviceTax=="" ) {
			alert("Please enter Service Tax Rate.");
			form.serviceTax.focus();
			return false;
		}
		
		if (!checkNumber(serviceTax)) {
			form.serviceTax.value = "";
			return false;
		}

		if (octroiServiceCharge=="" ) {
			alert("Please enter Octroi Service Charge.");
			form.octroiServiceCharge.focus();
			return false;
		}
		
		if (!checkNumber(octroiServiceCharge)) {
			form.octroiServiceCharge.value = "";
			return false;
		}

		if (mode==2) {
			var confirmRateListMsg= confirm("Do you want to save this to new Rate list?");
			/*
			if (!confirmRateListMsg) {
				return false;
			}
			*/
			if (confirmRateListMsg) {		
				alert("Please create a new Rate list and then update the selected record.");
				return false;
			}		
		}
		if (!confirmSave()) {
			return false;
		}
		return true;
	}


	function enableTransporterRateButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableTransporterRateButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

	//Key moving
	function nextTBox(e, form, name)
	{
		var ecode = getKeyCode(e);
		//alert("keycode="+ecode);
		var sName = name.split("_");
		
		upArrowName = sName[0]+"_"+(parseInt(sName[1])-2);

		//var weightSlabRowCount = document.getElementById("hidTableRowCount").value;
		//alert(parseInt(sName[1])+"="+weightSlabRowCount);	
		
		if ((ecode==13) || (ecode==40)) {
			var nextControl = eval(form+"."+name);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		if ((ecode==0) || (ecode==39)){
			var nextControl = eval(form+"."+upArrowName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
	}
	