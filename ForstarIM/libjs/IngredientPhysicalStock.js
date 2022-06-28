function validateIngredientPhysical(form)
{
	//alert("hii");
	var searchMode		=	form.searchMode.value;
	
	//alert("huuii");
	if (searchMode=="") {
		alert("Please enter a Search Mode.");
		form.searchMode.focus();
		return false;
	}

	if(searchMode=="S")
	{
		var selSupplier = form.selSupplier.value;
		if (selSupplier=="") {
			alert("Please enter a Supplier.");
			form.selSupplier.focus();
			return false;
		}
		
		var selIngredient = form.selIngredient.value;
		if (selIngredient=="") {
			alert("Please enter a Ingredient.");
			form.selIngredient.focus();
			return false;
		}

		var expectedQuantity = form.expectedQuantity.value;
		if (expectedQuantity=="") {
			alert("Please enter a expectedQuantity.");
			form.expectedQuantity.focus();
			return false;
		}

		var quantity=form.quantity.value;
		if (quantity=="") {
			alert("Please enter a quantity.");
			form.quantity.focus();
			return false;
		}
		
		var effectiveDate=form.effectiveDate.value;
		if (effectiveDate=="") {
			alert("Please enter a effectiveDate.");
			form.effectiveDate.focus();
			return false;
		}
		
	}
	else
	{
		var bulkDate=form.bulkDate.value;
		if (bulkDate=="") {
			alert("Please enter a bulkDate.");
			form.bulkDate.focus();
			return false;
		}
	}


	if(!confirmSave()){
			return false;
	}
	return true;
}



function getDifference()
{
	var expectedQuantity=document.getElementById('expectedQuantity').value;
	var quantity=document.getElementById('quantity').value;
	if(quantity!="" && expectedQuantity!="")
	{
		if(quantity>=expectedQuantity)
		{
			var difference=parseFloat(quantity)-parseFloat(expectedQuantity);
		}
		else if(expectedQuantity>quantity)
		{
			var difference=parseFloat(expectedQuantity)-parseFloat(quantity);
		}
		document.getElementById('differenceInQuantity').value=parseFloat(difference);
	}
}


function getQtyDifference(i,j)
{

	var expectedQuantity=document.getElementById('expectedQuantity_'+i+'_'+j).value;
	var quantity=document.getElementById('quantity_'+i+'_'+j).value;
	//alert(expectedQuantity+"-----"+quantity);
	if(quantity!="" && expectedQuantity!="")
	{
		if(parseFloat(quantity)>=parseFloat(expectedQuantity))
		{
			var difference=parseFloat(quantity)-parseFloat(expectedQuantity);
		}
		else if(parseFloat(expectedQuantity)>parseFloat(quantity))
		{
			var difference=parseFloat(expectedQuantity)-parseFloat(quantity);
		}
		document.getElementById('differenceInQuantity_'+i+'_'+j).value=parseFloat(difference);
	}
}

function getIngredientPhysicalStock(bulkDate,ingSize)
{
	for(i=0; i<ingSize; i++)
	{
		var ingId=$("#ingId_"+i).val();
		var supplierSz = $('#supplierSize_'+i).length;
		if(supplierSz>0)
		{	
			var supplierSize = $('#supplierSize_'+i).val();
			for(j=0; j<supplierSize; j++)
			{
				var supplierId=$("#supplierId_"+i+"_"+j).val();
				var qtyOld=$("#quantity_"+i+"_"+j).val();
				var qty=xajax_getIngredientPhysicalStock(bulkDate,supplierId,ingId);
				if(!qty)
				{
					$("#quantity_"+i+"_"+j).val(qtyOld);
					$("#quantity_"+i+"_"+j).attr('readonly', false);
					getQtyDifference(i,j);
				}
				else
				{
					$("#quantity_"+i+"_"+j).val(qty);
					$("#quantity_"+i+"_"+j).attr('readonly', true);
					getQtyDifference(i,j);
				}
				//var expectedQuantity=document.getElementById("expectedQuantity_"+i+"_"+j).value;
				//var quantity=document.getElementById("quantity_"+i+"_"+j).value;
			}
		}
	}

}