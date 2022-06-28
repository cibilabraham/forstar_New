	function validateMCPkgWtMaster(form)
	{
		var pkgName		= form.pkgName.value;		
		var selMcPkg		= form.selMcPkg.value;	
		var packingWt		= form.packingWt.value;	
		var selNetWt		= form.selNetWt.value;	
		var pkgWtTolerance	= form.pkgWtTolerance.value;

		if (pkgName=="") {
			alert("Please enter a name.");
			form.pkgName.focus();
			return false;
		}
		if (selNetWt=="") {
			alert("Please select a Net Wt.");
			form.selNetWt.focus();
			return false;
		}
		if (selMcPkg=="") {
			alert("Please select a MC Packing.");
			form.selMcPkg.focus();
			return false;
		}	
		if (packingWt=="") {
			alert("Please enter MC Packing Wt.");
			form.packingWt.focus();
			return false;
		}
		

		if (pkgWtTolerance!="") {
			if (!checkNumber(pkgWtTolerance)) {
				form.pkgWtTolerance.focus();
				return false;
			}
			
			if (parseFloat(pkgWtTolerance)>=parseFloat(selNetWt)) {
				alert("Pkg Wt tolerance must be less than the Net wt");
				form.pkgWtTolerance.focus();
				return false;
			}
		}
	
		var existPkgName = document.getElementById("existPkgName").value;
		var existPkgWt   = document.getElementById("existPkgWt").value;
		var existingWtArr = existPkgWt.split(',');

		if (pkgName==existPkgName)
		{
			alert("Please choose/enter another name");
			form.pkgName.focus();
			return false;
		}
		
		if (in_array( parseFloat(packingWt), existingWtArr ))
		{
			alert("Packing Weight for the same combination is already exist in database. Please enter a different wt.");
			form.packingWt.focus();
			return false;
		}
		

		if (!confirmSave()) return false;
		return true;
	}
	
	function enableStateVatButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableStateVatButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

	function enableAddBtn(mode)
	{
		enableStateVatButton(mode);
		
		document.getElementById("displayExistingRec").style.display = "none";
		document.getElementById("divStateIdExistTxt").innerHTML = "";
	}
	

