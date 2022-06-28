	// Bulk Update
	function validateStockHoldingCostReport()
	{				
		var excessStockTolerance = document.getElementById("excessStockTolerance").value;
		
		if (!isDigit(excessStockTolerance) || excessStockTolerance>100) {
			alert("Please enter stock tolerance percentage between 1 to 100");
			document.getElementById("excessStockTolerance").focus();
			return false;
		}
		return true;
	}

