	

	// Using to activate only one search option
	function selectChk(field)
	{	
		clearDARFields();
		if (!document.getElementById(field).checked) chk = false;
		else chk = true;
	
		document.getElementById("cbChqRt").checked 	= false;
		document.getElementById("cbPmtDate").checked 	= false;
		document.getElementById("cbPmtAmt").checked 	= false;

		document.getElementById(field).checked = chk;
	}
	
	function validateDistributorAccountReport()
	{
		var cbChqRt	= document.getElementById("cbChqRt").checked;
		var cbPmtDate	= document.getElementById("cbPmtDate").checked;
		var cbPmtAmt	= document.getElementById("cbPmtAmt").checked;

		if (!cbChqRt && !cbPmtDate && !cbPmtAmt) {
			alert("Please select atleast one search option");			
			return false;
		}

		if (cbChqRt) {
			var ddChqRt	= document.getElementById("ddChqRt").value;
			var chqRtNo	= document.getElementById("chqRtNo").value;

			if (ddChqRt=="") {
				alert("Please select cheque/RTGS");
				document.getElementById("ddChqRt").focus();
				return false;
			}

			if (chqRtNo=="") {
				if (ddChqRt=='CHQN') alert("Please enter cheque No.");
				else if (ddChqRt=='RTGSN') alert("Please enter RTGS No.");
				document.getElementById("chqRtNo").focus();
				return false;
			}

		}

		if (cbPmtDate) {
			var ddPmtDate		= document.getElementById("ddPmtDate").value;
			var txtPmtDate		= document.getElementById("txtPmtDate").value;
			var txtPmtEndDate	= document.getElementById("txtPmtEndDate").value;
			var ddPmtDateType	= document.getElementById("ddPmtDateType").value;

			if (ddPmtDate=="") {
				alert("Please select cheque/value date");
				document.getElementById("ddPmtDate").focus();
				return false;
			}

			if (txtPmtDate=="") {
				var pmtDateMsg = (ddPmtDateType=='DR')?"start":"";
				
				if (ddPmtDate=='CHQD') alert("Please select "+pmtDateMsg+" cheque date.");
				else if (ddPmtDate=='VALD') alert("Please select "+pmtDateMsg+" value date.");
				document.getElementById("txtPmtDate").focus();
				return false;
			}

			if (ddPmtDateType=='DR' && txtPmtEndDate=="") {
				var pmtEndDateMsg = "end";
				
				if (ddPmtDate=='CHQD') alert("Please select "+pmtEndDateMsg+" cheque date.");
				else if (ddPmtDate=='VALD') alert("Please select "+pmtEndDateMsg+" value date.");
				document.getElementById("txtPmtEndDate").focus();
				return false;
			}

		}

		if (cbPmtAmt) {
			var txtPmtAmt	= document.getElementById("txtPmtAmt").value;
			if (txtPmtAmt=="") {
				alert("Please enter amount.");
				document.getElementById("txtPmtAmt").focus();
				return false;
			}
		}


		return true;
	}


	function clearDARFields()
	{		
		document.getElementById("ddChqRt").value	= "";
		document.getElementById("chqRtNo").value	= "";		
		document.getElementById("cbShowSimilar").checked 	= false;

		document.getElementById("ddPmtDate").value	= "";
		document.getElementById("txtPmtDate").value	= "";
		
		document.getElementById("txtPmtAmt").value	= "";		
		document.getElementById("ddPmtDateType").value	= "SD";
		document.getElementById("txtPmtEndDate").value	= "";
		displayDateType();
	}

	function validateAdvanceSearch()
	{
		var selectFrom = $("#selectFrom").val();
		var selectTill = $("#selectTill").val();
		
		if (selectFrom=="") {
			alert("Please select from date.");
			$("#selectFrom").focus();
			return false;
		}

		if (selectTill=="") {
			alert("Please select till date.");
			$("#selectTill").focus();
			return false;
		}
		
		var cbSelected = false;
		//$("input[rel^=cbAdvSearch][checked]").each(function(i) {
		$("input[rel^=cbAdvSearch]").each(function(i) {
			var selCB = $(this).attr('checked');
			//alert($(this).attr('value'));
			if (selCB) cbSelected = true;
		});

		if (!cbSelected) {
			alert("Please select atleast one option");
			return false;
		}

		return true;
	}

	function displayDateType()
	{
		var ddPmtDateType = $("#ddPmtDateType").val();

		$("#endDateRow").hide();
		$("#startSpan").hide();

		if (ddPmtDateType=='DR')
		{
			$("#endDateRow").show();
			$("#startSpan").show();
		}
	}