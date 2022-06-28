	function validateCityMaster(form)
	{
		var cityName	= form.cityName.value;
		var state	= form.state.value;
		var octroi = document.getElementById("octroi").checked;
		
	
		if (cityName=="") {
			alert("Please enter a City Name.");
			form.cityName.focus();
			return false;
		}
	
		if (state=="") {
			alert("Please select a State.");
			form.state.focus();
			return false;
		}

		if (octroi) {
			var octroiPercent = document.getElementById("octroiPercent").value;
			if (octroiPercent=="" && octroiPercent==0) {
				alert("Please enter Octroi value.");
				document.getElementById("octroiPercent").focus();
				return false;
			}	

			if (!checkDigit(octroiPercent)) {
				alert("Please enter digit only in octroi percent.");
				document.getElementById("octroiPercent").focus();
				return false;
			}	

		}
		
		if (!confirmSave()) return false;
		return true;
	}

	// Octroi Percent
	function disOctroiPercent()
	{
		var octroi = document.getElementById("octroi").checked;
		if (octroi) {
			document.getElementById("octroiPercentCol").style.display = "";
		} else {
			document.getElementById("octroiPercentCol").style.display = "none";
			document.getElementById("octroiPercent").value = "";
		}
	}