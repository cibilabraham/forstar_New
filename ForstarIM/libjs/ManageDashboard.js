	function validateManageDashboard(form)
	{		
		var selRole		= form.selRole.value;
		var dashBoardRowCount	= form.dashBoardRowCount.value;
		var dashboardSelected 	= false;

		if (selRole=="") {
			alert("Please select a Role.");
			form.selRole.focus();
			return false;
		}

		for (i=1; i<=dashBoardRowCount; i++) {
			var selDashBoard	= document.getElementById("selDashBoard_"+i);
			if (selDashBoard.checked)  dashboardSelected = true;
		}	

		if (!dashboardSelected) {
			alert("Please select atleast one Dashboard.");
			return false;
		} 

		if (!confirmSave()) return false;
		else return true;
	}

	// Distributor account section updation
	function pChqUpdate()
	{
		var pChqDays 		= document.getElementById("pChqDays");
		var crBalDisplayLimit	= document.getElementById("crBalDisplayLimit");
		var overdueDisplayLimit = document.getElementById("overdueDisplayLimit");

		if (pChqDays.value=="" && crBalDisplayLimit.value=="" && overdueDisplayLimit.value=="") {
			alert("Please enter distributor account dashboard details.");
			return false;
		}

		if (!confirmSave()) return false;
		else return true;
	}
