<script language="javascript" >

	function validateFrznPkgAccounts(save)
	{
		var dateFrom 	= document.getElementById("dateFrom").value;
		var dateTo 	= document.getElementById("dateTo").value;
		var selProcessor = document.getElementById("selProcessor").value;
		
		if (dateFrom=="") {
			alert("Please select from date.");
			document.getElementById("dateFrom").focus();
			return false;
		}

		if (dateTo=="") {
			alert("Please select to date.");
			document.getElementById("dateTo").focus();
			return false;
		}

		if (selProcessor=="") {
			alert("Please select a Processor.");
			document.getElementById("selProcessor").focus();
			return false;
		}

		if (save=="Y" && !confirmSave()) return false;
		
		return true;
	}

	// Calc Frzn Pkg Amt
	function calcFPRAmt()
	{
		var rowCount = document.getElementById("hidRowCount").value;
		var calcRate;
		var totAmt = 0;	
	
		for (var i=1; i<=rowCount; i++) {
			var pkdQty 	 = parseFloat(document.getElementById("pkdQty_"+i).value);
			pkdQty = (pkdQty!="" && !isNaN(pkdQty))?pkdQty:0;
			var pkgRate	 = parseFloat(document.getElementById("pkgRate_"+i).value);
			pkgRate = (pkgRate!="" && !isNaN(pkgRate))?pkgRate:0;
			//var totalPkgRate = document.getElementById("totalPkgRate_"+i);			
			calcRate = pkdQty*pkgRate;
			totAmt = totAmt+calcRate;
			if (!isNaN(calcRate)) document.getElementById("totPkgAmt_"+i).value = number_format(calcRate,2,'.','');
		}

		if (!isNaN(totAmt)) document.getElementById("totalAmt").value = number_format(totAmt,2,'.','');
	}

</script>