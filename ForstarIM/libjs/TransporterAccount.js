	function validateTransporterAccount(form)
	{	
		var supplyFrom	=	form.supplyFrom.value;
		var supplyTill	=	form.supplyTill.value;
		var transporter	=	form.transporter.value;
		var billType	= form.billType.value;
	
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
		if (findDaysDiff(supplyFrom)>0 || findDaysDiff(supplyTill)>0) {
			alert("Selected date should be less than or equal to current date");
			return false;	
		}
		if (checkDateSelected(supplyFrom,supplyTill)>0) {
			alert("Please check selected From and To date");
			return false;
		}
		/*
		if (transporter=="") {
			alert("Please select a Transporter");
			form.transporter.focus();
			return false;
		}
		*/
		if (billType=="") {
			alert("Please select a Bill Type");
			form.billType.focus();
			return false;
		}
		/*	
		if( confirmSave()) return true;
		else return false;		
		*/
		return true;
	}

	function updateTransporterAccount(form)
	{
		if (!validateTransporterAccount(form)) {
			return false;
		}
	
		var rowCount = document.getElementById("hidRowCount").value;
		for (var i=1; i<=rowCount; i++) {
			var billNo = document.getElementById("billNo_"+i);
			var settled    = document.getElementById("settled_"+i);
			var docketNum = document.getElementById("docketNum_"+i);
			var billRequired = document.getElementById("billRequired_"+i).value;
			var deliveryDate = document.getElementById("deliveryDate_"+i);		
			if (settled.checked && (docketNum.value=="" || docketNum.value==0)) {
				alert("Please assign a Docket Num.");
				billNo.focus();
				return false;
			}
			if (settled.checked && deliveryDate.value=="") {
				alert("Delivery date missing for the selected Invoice..");
				billNo.focus();
				return false;
			}
			

			//billRequired=='N' &&
			if (settled.checked) {
				var actualCost = document.getElementById("actualCost_"+i);
				// || actualCost.value==0
				if (actualCost.value=="") {
					alert("Please enter actual cost.");
					actualCost.focus();
					return false;
				}
				
				if (!checkNumber(actualCost.value)) {
					actualCost.focus();
					return false;
				}
			}

			if (settled.checked && billNo.value=="" && billRequired=='Y') {
				alert("Please enter a Bill No.");
				billNo.focus();
				return false;
			}
		}
		
		if (confirmSave()) {
			return true;
		} else {
			return false;
		}
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


	// Calculate Transporter Actual Cost
	function calcActualCost()
	{
		var rowCount = document.getElementById("hidRowCount").value;
		var totalActualCost = 0;
		var actualCost = 0;
		for (var i=1; i<=rowCount; i++) {
			//var billRequired = document.getElementById("billRequired_"+i).value;
			//if (billRequired=='N') {
				actualCost = document.getElementById("actualCost_"+i).value;
				actualCost = (actualCost!=0)?actualCost:0;
				totalActualCost += parseFloat(actualCost);
			//}
		}		
		if (!isNaN(totalActualCost))  document.getElementById("totalActualCost").value = number_format(totalActualCost,2,'.','');
	}

	// Calc Grand total When Edit ODA Charge
	/*
	function calcGTotalAmt()
	{
		var rowCount = document.getElementById("hidRowCount").value;
		
		var totalAmt = 0;
		for (var i=1; i<=rowCount; i++) {
			var freightCost	 = document.getElementById("freightCost_"+i).value;
			var fovRate	 = document.getElementById("fovRate_"+i).value;
			var docketRate	 = document.getElementById("docketRate_"+i).value;
			var odaRate	 = document.getElementById("odaRate_"+i).value;			
			totalAmt = parseFloat(freightCost)+parseFloat(fovRate)+parseFloat(docketRate)+parseFloat(odaRate);
			if (!isNaN(totalAmt)) {
				document.getElementById("trptrTotalAmtCol_"+i).innerHTML = totalAmt;		
				document.getElementById("transTotalAmt_"+i).value = totalAmt;
			}
		}		
	}
	*/