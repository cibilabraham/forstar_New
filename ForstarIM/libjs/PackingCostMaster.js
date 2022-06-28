function validatePackingCostMaster(form)
{
	var vatRateForPackingMaterial = form.vatRateForPackingMaterial.value;
	var innerCartonWstage = form.innerCartonWastage.value;
	var costOfGum = form.costOfGum.value;
	

	if (vatRateForPackingMaterial=="") {
		alert("Please enter a VAT Rate.");
		form.vatRateForPackingMaterial.focus();
		return false;
	}
	
	if (innerCartonWstage=="") {
		alert("Please enter Inner Carton Wastage.");
		form.innerCartonWstage.focus();
		return false;
	}
	
	if (costOfGum=="") {
		alert("Please enter cost of Gum.");
		form.costOfGum.focus();
		return false;
	}	
	
	if (!confirmSave()) return false;
	return true;
}

// Find the total Packing Material Cost
function calcPackingMaterialTotCost()
{
	var innerCartonWstage = parseFloat(document.getElementById("innerCartonWstage").value);
	var hidMaterialCostRowCount = document.getElementById("hidMaterialCostRowCount").value;
	var calcTotMaterialCost = 0;
	for (k=1; k<=hidMaterialCostRowCount; k++) {
		var materialCostPerItem = parseFloat(document.getElementById("materialCostPerItem_"+k).value);
		//$calcTotMaterialCost = $materialCost/(1-($innerCartonWstage/100)); 
		calcTotMaterialCost = (materialCostPerItem/(1-(innerCartonWstage/100)));
		
		if (!isNaN(calcTotMaterialCost)) {
			document.getElementById("totMaterialCost_"+k).value = number_format(calcTotMaterialCost,2,'.','');
		}
			
	}
}


function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;

        return true;
    }   

