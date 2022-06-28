function validatePhysicalStockEntry(form)
{
	var selDate		=	form.selDate.value;
	var selStkType		=	form.selStkType.value;

	var rowCount	= document.getElementById("hidTableRowCount").value;	
	var itemsSelected = false;	

	if (selDate=="") {
		alert("Please select a Date.");
		form.selDate.focus();
		return false;
	}

	if (findDaysDiff(selDate)>0) {
		alert("Please check date");
		form.selDate.focus();
		return false;
	}

	if (selStkType=="") {
		alert("Please select a Stock type.");
		form.selStkType.focus();
		return false;
	}
	
	for (i=1; i<=rowCount; i++) {
		var physicalStkQty = document.getElementById("physicalStkQty_"+i);
		
		if (physicalStkQty.value!="" && physicalStkQty.value!=0) {
			itemsSelected = true;
		}

		if (physicalStkQty.value!="" && !checkNumber(physicalStkQty.value)) {
			return false;
		}
	}
	
	if (itemsSelected==false) {
		alert("Please enter atleast one physical stock qty");
		return false;
	}

	if (!confirmSave()) return false;
	return true;
}

	//Key moving
	function nextStockBox(e,form,name)
	{
		var ecode = getKeyCode(e);	
		//alert(ecode);
		var sName = name.split("_");
		upArrowName = sName[0]+"_"+(parseInt(sName[1])-2);
		if ((ecode==13) || (ecode == 0) || (ecode==40)){
			var nextControl = eval(form+"."+name);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		if ((ecode==38)){
			var nextControl = eval(form+"."+upArrowName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
	}

	/*
	Find Stk diff
	*/
	function calcStkDiff(rowId)
	{
		var calcDiffQty 	= 0;
		var stkQty 		= (document.getElementById("stkQty_"+rowId).value!="")?document.getElementById("stkQty_"+rowId).value:0;
		var physicalStkQty	= (document.getElementById("physicalStkQty_"+rowId).value!="")?document.getElementById("physicalStkQty_"+rowId).value:0;
		calcDiffQty		= physicalStkQty-stkQty;
		if (!isNaN(calcDiffQty)) document.getElementById("diffStkQty_"+rowId).value = calcDiffQty;
	}