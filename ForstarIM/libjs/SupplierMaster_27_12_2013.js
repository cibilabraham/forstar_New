	function validateSupplier(form)
	{
		var code	= form.code.value;
		var name	= form.supplierName.value;
		var phoneNo	= form.phoneNo.value;
		var email	= form.email.value;
		var pinCode	= form.pinCode.value
		
		if (code=="") {
			alert("Please enter a Code.");
			form.code.focus();
			return false;
		}
		
		if (name=="") {
			alert("Please enter a Supplier Name.");
			form.supplierName.focus();
			return false;
		}	

		if (pinCode!="") {
			if(isPositiveInteger(pinCode)==false)
			{			
				form.pinCode.focus();
				return false;
			}
		}

		if (phoneNo!="") {
			if (checkInternationalPhone(phoneNo)==false){
				alert("Please Enter a Valid Phone Number");				
				form.phoneNo.focus();
				return false;
			}
		}
	
		if (email!="") {
			if (!checkemail(email)){				
				form.email.focus();
				return false;
			}
		}		

		if (!form.frozen.checked && !form.inventory.checked && !form.rte.checked) {
			alert("Please select atleast one section.");
			return false;
		}

		if (form.frozen.checked) {
			var nativePlace		=	form.place.value;
			var landingCenter	=	document.getElementById("landingCenter").value;

			if (nativePlace=="") {
				alert("Please select a Place.");
				form.place.focus();
				return false;
			} 		
			if (landingCenter=="") {
				alert("Please select a Landing center.");
				form.landingCenter.focus();
				return false;
			}
		}
		
		if (!confirmSave()) {
				return false;
		}
		return true;	
	}

	function showFrnSection()
	{
		var frnChk = document.getElementById("frozen").checked
		if (frnChk) document.getElementById("frnSectionId").style.display = '';
		else document.getElementById("frnSectionId").style.display = 'none';		
	}

	function isPositiveInteger(val){
		for (var i = 0; i < val.length; i++) {
			var ch = val.charAt(i);
			if (ch < "0" || ch > "9") {
				alert("Please enter correct Pincode");
				return false;
			}
		}
		return true;
	}

	// Validate Supplier Status (Active/Inactive)
	function validateSuppStatus(supplierId, rowId)
	{
		if (!confirm("Do you wish to change supplier status?")) {
			return false;
		}
		// Ajax 
		xajax_changeSupplierStatus(supplierId, rowId);
		return true;
	}





