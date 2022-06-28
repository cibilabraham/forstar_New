	function validateString(field) 
	{
		var valid = "\abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_"
		// ALL OTHER CHARACTERS ARE INVALID
		var validChar = false;
		var temp;
		for (var i=0; i<field.length; i++) {
			temp = "" + field.substring(i, i+1);
			if (valid.indexOf(temp) == "-1") validChar = true;
		}
		if (validChar) return false;
		else return true;
	}

	function validateStockField(form)
	{
		var labelName	= document.getElementById("labelName");
		var inputType	= document.getElementById("inputType");
		//var stkFieldName = document.getElementById("stkFieldName");
		var stkFieldSize = document.getElementById("stkFieldSize");
		var stkFieldValue = document.getElementById("stkFieldValue");
		var fieldDataType = document.getElementById("fieldDataType");

				if (labelName.value == "") {
					alert("Please enter a Label Name.");
					labelName.focus();
					return false;
				}

				if (inputType.value == "") {
					alert("Please select a Input type.");
					inputType.focus();
					return false;
				}
				/*
				if (stkFieldName.value == "") {
					alert("Please enter a Field Name.");
					stkFieldName.focus();
					return false;
				}
				
				if (!validateString(stkFieldName.value)) {
					alert("Please enter a valid Field Name. Avoid special character including space.");
					stkFieldName.focus();
					return false;
				}
				*/
				if (inputType.value=='T' && fieldDataType.value=="") {
					alert("Please select a field data type.");
					fieldDataType.focus();
					return false;
				}
				if (inputType.value == 'C' && stkFieldValue.value=="") {
					alert("Please enter a default value.");
					stkFieldValue.focus();
					return false;
				}
	
		if (!confirmSave()) {
			return false;
		}
		return true;
	}

	// Show or Hide fields
	function showFields()
	{
		var inputType = document.getElementById("inputType").value;
		if (inputType=='C') {
			document.getElementById("fDataTypeRow").style.display="none";
			document.getElementById("fUnitGroupRow").style.display="none";
			document.getElementById("fSizeRow").style.display="none";
			document.getElementById("fieldDataType").value = "";
			document.getElementById("unitGroup").value = "";
			document.getElementById("stkFieldSize").value = "";
		} else {
			document.getElementById("fDataTypeRow").style.display="";
			document.getElementById("fUnitGroupRow").style.display="";
			document.getElementById("fSizeRow").style.display="";
		}
	}