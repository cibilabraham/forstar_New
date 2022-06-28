	function validatePackingGroupMaster(form)
	{
		var selPState_L		= form.selPState_L.value;
		var selPGroup_L		= form.selPGroup_L.value;
		var selNetWt_L		= form.selNetWt_L.value;
		
		var selPState_R		= form.selPState_R.value;
		var selPGroup_R		= form.selPGroup_R.value;
		var selNetWt_R		= form.selNetWt_R.value;		

		var pLeftSel		= selPState_L+","+selPGroup_L+","+selNetWt_L;
		var pRightSel		= selPState_R+","+selPGroup_R+","+selNetWt_R;

		if (selPState_L=="") {
			alert("Please select a Product State.");
			form.selPState_L.focus();
			return false;
		}
		if (selPGroup_L=="") {
			alert("Please select a Product Group.");
			form.selPGroup_L.focus();
			return false;
		}
		if (selNetWt_L=="") {
			alert("Please select a Prouct Net Wt.");
			form.selNetWt_L.focus();
			return false;
		}	

		if (selPState_R=="") {
			alert("Please select a Product State.");
			form.selPState_R.focus();
			return false;
		}
		if (selPGroup_R=="") {
			alert("Please select a Product Group.");
			form.selPGroup_R.focus();
			return false;
		}
		if (selNetWt_R=="") {
			alert("Please select a Prouct Net Wt.");
			form.selNetWt_R.focus();
			return false;
		}

		if (pLeftSel==pRightSel) {
			alert(" Please make sure the selected values are not duplicate. ");
			return false;
		}	
				
		if (!confirmSave()) {
			return false;
		}
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
	

