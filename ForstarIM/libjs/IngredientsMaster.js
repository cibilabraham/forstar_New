function validateIngredientMaster(form)
{
	var category 		= form.category.value;
	var ingredientCode	= form.ingredientCode.value;
	var ingredientName	= form.ingredientName.value;
	var ingMainCategory	= form.ingMainCategory.value;
	var materialType    = form.ingMaterialType.value;
	var selraw_ing = form.selraw_ing.value; 
	var yeild = form.yeild.value; 
	var cleaning_cost = form.cleaning_cost.value; 

	if (ingMainCategory=="") {
		alert("Please select a Category.");
		form.ingMainCategory.focus();
		return false;
	}

	if (category=="") {
		alert("Please select a Sub-Category.");
		form.category.focus();
		return false;
	}
	
	if (ingredientCode=="") {
		alert("Please enter a Ingredient Code.");
		form.ingredientCode.focus();
		return false;
	}
	
	if (ingredientName=="") {
		alert("Please enter a Ingredient Name.");
		form.ingredientName.focus();
		return false;
	}
	
	if(materialType=="") {
		alert("Please Select Material Type");
		form.ingMaterialType.focus();
		return false;
	}
	else if(materialType=='2'){
		if(selraw_ing=="") {
			alert("Please Select Raw Ingredient");
			form.selraw_ing.focus();
			return false;
		}
		if(yeild=="") {
			alert("Please Enter Yeild");
			form.yeild.focus();
			return false;
		}
			if(cleaning_cost=="") {
			alert("Please Enter cleaning_cost");
			form.cleaning_cost.focus();
			return false;
		}
		
	}

	if (!confirmSave()) {
		return false;
	}
	return true;
}

	

