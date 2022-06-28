function validateManageProduct(form)
{
	var productCode		=	form.productCode.value;
	var productName		=	form.productName.value;
	var productCategory	=	form.productCategory.value;
	var productState	=	form.productState.value;
	var productStateGroup 	=	form.productStateGroup.value;
	var netWt		=	form.netWt.value;
	var hidProductCode	= 	document.getElementById("hidProductCode").value;
	var addMode		= 	document.getElementById("hidAddMode").value;
	var hidPCodeExist	= 	document.getElementById("hidPCodeExist").value;
	var hidPIdentifiedNoExist = 	document.getElementById("hidPIdentifiedNoExist").value;
	//var hidPExciseCodeExist	= document.getElementById("hidPExciseCodeExist").value;	
	var editMode		= $("#hidEditMode").val();	

	if (productCode=="") {
		alert("Please enter a Product Code.");
		form.productCode.focus();
		return false;
	}

	if (hidPCodeExist!="") {
		alert("Please check the Product Code.");
		form.productCode.focus();
		return false;
	}
	

	if (addMode!="") {
		var selProduct = document.getElementById("selProduct").value;
		if (selProduct!="") {
			if (hidProductCode==productCode) {
				alert("Please modifiy the Product Code. ");
				form.productCode.focus();
				return false;
			}
		}
	}

	if (hidPIdentifiedNoExist!="") {
		alert("Please check the Identification No.");
		form.identifiedNo.focus();
		return false;
	}

	/*
	if (hidPExciseCodeExist!="") {
		alert("Please check the Product Excise Code.");
		form.hidPExciseCodeExist.focus();
		return false;
	}
	*/
	if (productName=="") {
		alert("Please enter a Product Name.");
		form.productName.focus();
		return false;
	}

	if (productCategory=="") {
		alert("Please select a Product category.");
		form.productCategory.focus();
		return false;
	}

	if (productState=="") {
		alert("Please select a Product State.");
		form.productState.focus();
		return false;
	}

	if (productStateGroup!="") {
		var productGroup = form.productGroup.value;

		if (productGroup=="") {
			alert("Please select a Product Group.");
			form.productGroup.focus();
			return false;
		}
	}

	if (editMode!="") {
		var prdExemptionActive	= $("#hidChShExemptionActive").val();
		if (prdExemptionActive!=0) {
			var orgExciseCode = $("#hidOrgChaptSubhead").val();
			var newExciseCode = $("#newChapterSubhead").val();
			if (orgExciseCode==newExciseCode) {
				alert("Please change chapter/subheading.");
				$("#newChapterSubhead").focus();
				return false;
			}			
		}
	}

	if (netWt=="") {
		alert("Please enter Net Wt.");
		form.netWt.focus();
		return false;
	}
	
	if (!checkDigit(netWt) || netWt==0) {
		alert("Please enter a valid Net Wt.");
		form.netWt.focus();
		return false;
	}	

	if(!confirmSave()){
			return false;
	}
	return true;
}

	function changeChaptSubHead()
	{
		$('#chaptSubheadTr').toggle();
		$('#changeHrf').hide();
		$("#hidChShExemptionActive").attr("value",1);
	}

	function removeChaptSubHeadExmpt()
	{
		$('#chaptSubheadTr').toggle();
		$('#changeHrf').show();
		$("#hidChShExemptionActive").attr("value",0);
	}