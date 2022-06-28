	function validateTransporterMaster(form)
	{
		var selTransporter	= form.selTransporter.value;	
		var selZone		= form.selZone.value;
		var mode		= document.getElementById("hidMode").value;		
			
		if (selTransporter=="") {
			alert("Please select a Transporter.");
			form.selTransporter.focus();
			return false;
		}

		if (selZone=="") {
			alert("Please select a Zone.");
			form.selZone.focus();
			return false;
		}		
		
			var WtSlabExist = document.getElementById("WtSlabExist").value
			if (WtSlabExist>0) {
				var weightSlabRowCount  = document.getElementById("hidTableRowCount").value;
				for (j=1; j<=weightSlabRowCount; j++) {
					var weightSlabId	= document.getElementById("weightSlabId_"+j);
					var rate		= document.getElementById("rate_"+j);
		
					if (rate.value=="" || rate.value==0) {
						alert("Please enter a rate.");
						rate.focus();
						return false;
					}
		
					if (!checkNumber(rate.value)) {
						rate.focus();
						return false;
					}
				}	
			}
			if (WtSlabExist==0) {
				alert("Please define transporter wise Weight Slab.");
				return false;
			}		

		if (mode==2) {
			var confirmRateListMsg= confirm("Do you want to save this to new Rate list?");
			if (!confirmRateListMsg) {
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

	// RPW - rate Per Kg, FRC = Fized rate per consignement
	/*
	function showRateType()
	{
		var trptrRateTypeRPW = document.getElementById("trptrRateTypeRPW").checked;
		var trptrRateTypeFRC = document.getElementById("trptrRateTypeFRC").checked;
		if (trptrRateTypeRPW) {			
			document.getElementById("fixedRateRow").style.display='none';
			document.getElementById("ratePerKgRow").style.display='';
			document.getElementById("fixedRate").value = "";
		} else if (trptrRateTypeFRC) {			
			document.getElementById("ratePerKgRow").style.display='none';
			document.getElementById("fixedRateRow").style.display='';
		} else {			
			document.getElementById("fixedRateRow").style.display='none';
			document.getElementById("ratePerKgRow").style.display='none';
			document.getElementById("fixedRate").value = "";
		}
	}
	*/
	