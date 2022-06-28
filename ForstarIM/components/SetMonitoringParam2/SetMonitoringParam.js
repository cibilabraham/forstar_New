<script language="javascript" >

	function validateSetMonitoringParam(form)
	{
		var installedCapacityId		= document.getElementById("installedCapacityId").value;
		var headName			= document.getElementById("headName").value;
		var monitoringParameter		= document.getElementById("monitoringParameter").value;
		var start			= document.getElementById("start").value;
			
		if (installedCapacityId=="") {
			alert("Please select a machinery.");
			document.getElementById("installedCapacityId").focus();
			return false;
		}

		if (headName=="") {
			alert("Please enter head name.");
			document.getElementById("headName").focus();
			return false;
		}

		if (monitoringParameter=="") {
			alert("Please select monitoring parameter.");
			document.getElementById("monitoringParameter").focus();
			return false;
		}

		if (start=="") {
			alert("Please select start value.");
			document.getElementById("start").focus();
			return false;
		}

		if (!confirmSave()) return false;
		else return true;
	}

</script>
