	function validatePackingMaterialCost(form)
	{
		var category = form.category.value;
		var subCategory = form.subCategory.value;
		var selStock = form.selStock.value;
		var selSupplier = form.selSupplier.value;
		var costPerItem = form.costPerItem.value;
		var mode = document.getElementById("hidMode").value;
	
		if (category=="") {
			alert("Please select a Category.");
			form.category.focus();
			return false;
		}
		
		if (subCategory=="") {
			alert("Please select a Sub-Category.");
			form.subCategory.focus();
			return false;
		}
	
		if (selStock=="") {
			alert("Please select a stock.");
			form.selStock.focus();
			return false;
		}
	
		if (selSupplier=="") {
			alert("Please select a Supplier.");
			form.selSupplier.focus();
			return false;
		}
		
		if (costPerItem=="") {
			alert("Please enter a cost.");
			form.costPerItem.focus();
			return false;
		}	
		
		if (mode==2) {
			var confirmRateListMsg= confirm("Do you want to save this to new Rate list?");
			if (confirmRateListMsg) {		
				alert("Please create a new Rate list and then update the selected record.");
				return false;
			}		
		}
	
		if (!confirmSave()) return false;
		return true;
	}