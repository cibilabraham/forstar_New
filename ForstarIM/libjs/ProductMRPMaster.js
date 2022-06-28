function validateProductMRPMaster(form)
{
	var selProduct		=	form.selProduct.value;
	//var mrp			=	form.mrp.value;
	var productMRPRateList	=	form.productMRPRateList.value;
	var productExist	= document.getElementById("productExist").value;
	
	if (selProduct=="") {
		alert("Please select a Product.");
		form.selProduct.focus();
		return false;
	}

	if (productExist!="") {
		alert("The selected product is already exist in database.");
		form.selProduct.focus();
		return false;
	}

	/*
	if (mrp=="") {
		alert("Please enter a MRP.");
		form.mrp.focus();
		return false;
	}
	if (!checkNumber(mrp)) {
		form.mrp.value = "";
		form.mrp.focus();
		return false;
	}
	*/

	if (productMRPRateList=="") {
		alert("Please select a Rate List.");
		form.productMRPRateList.focus();
		return false;
	}

	if (!validateExptRepeat()) return false;

	var rowCount = document.getElementById("hidTableRowCount").value;
	for (j=0; j<rowCount; j++) {
		var rowStatus = document.getElementById("status_"+j).value;
		if (rowStatus!='N') {
			var mrp = document.getElementById("mrp_"+j);

				if (mrp.value=="" || mrp.value==0) {
					alert("Please enter a MRP.");
					mrp.focus();
					return false;
				}
				if (!checkNumber(mrp.value)) {
					return false;
				}
			}
		}

	if (!confirmSave()) return false;
	return true;
}

//ADD MULTIPLE Item- ADD ROW START	
	function addNewExceptionRow(tableId)
	{	
		
		var tbl		= document.getElementById(tableId);
		var lastRow	= tbl.rows.length;
		var iteration	= lastRow+1;
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "row_"+fieldId;
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
		var cell4	= row.insertCell(3);
						
		cell1.className	= "listing-item"; cell1.align	= "left";cell1.noWrap = "true";
		cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
		cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
		cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";		
			
		var stateList = "<select name='selState_"+fieldId+"' id='selState_"+fieldId+"' onchange=\"xajax_getDistributorList(document.getElementById('selState_"+fieldId+"').value, '"+fieldId+"', '');\"><option value='0'>--Select All--</option>";
		<?php
			if (sizeof($stateRecs)!="") {
				foreach($stateRecs as $sr) {
					$stateId 	= $sr[0];
					$stateName	= stripSlash($sr[2]);
		?>
		var selPrOpt = '';
		stateList += "<option value='<?=$stateId?>' "+selPrOpt+"><?=$stateName?></option>";
		<?php 
				} 
			} 
		?>
		stateList += "</select>";
	
		var allStateList = "<select name='selState_"+fieldId+"' id='selState_"+fieldId+"'><option value='0'>--Select All--</option>";
		allStateList += "</select>";

		var distributorList = "<select name='selDistributor_"+fieldId+"' id='selDistributor_"+fieldId+"'><option value='0'>--Select All--</option>";
		<?php
			if (sizeof($distributorRecs)>0) {
				foreach ($distributorRecs as $dr) {
					$distributorId	 = $dr[0];
					$distributorName = stripSlash($dr[2]);
		?>
		var selPrOpt = '';
		distributorList += "<option value='<?=$distributorId?>' "+selPrOpt+"><?=$distributorName?></option>";
		<?php 
				} 
			} 
		?>
		distributorList += "</select>";	
		var allDistributorList = "<select name='selDistributor_"+fieldId+"' id='selDistributor_"+fieldId+"'><option value='0'>--Select All--</option>";
		allDistributorList += "</select>";
		
		var ds = "N";	
		if (fieldId!=0) var imageButton = "<a href='###' onClick=\"setItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		else var imageButton="";

		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='productExptEntryId_"+fieldId+"' type='hidden' id='productExptEntryId_"+fieldId+"' value=''>";
	
		if (fieldId==0) cell1.innerHTML = allStateList;
		else cell1.innerHTML = stateList;

		if (fieldId==0) cell2.innerHTML = allDistributorList;
		else cell2.innerHTML = distributorList;

		cell3.innerHTML = "<input name='mrp_"+fieldId+"' id='mrp_"+fieldId+"' type='text' size='3' value='' style='text-align:right;' autocomplete='off'>";	

		cell4.innerHTML = imageButton+hiddenFields;

		fieldId		= parseInt(fieldId)+1;
		document.getElementById("hidTableRowCount").value = fieldId;
	}
	
	function setItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none'; 		
		}
		return false;
	}

	/* ------------------------------------------------------ */
	// Duplication check starts here
	/* ------------------------------------------------------ */
	var cArr = new Array();
	var cArri = 0;	
	function validateExptRepeat()
	{	
		if (Array.indexOf != 'function') {  
			Array.prototype.indexOf = function(f, s) {
			if (typeof s == 'undefined') s = 0;
				for (var i = s; i < this.length; i++) {   
				if (f === this[i]) return i; 
				} 
			return -1;  
			}
		}
		
	var rc = document.getElementById("hidTableRowCount").value;	
	var arr = new Array();
	var arri=0;
	
	for (j=0; j<rc; j++) {
		var status = document.getElementById("status_"+j).value;
		if (status!='N') {
			var selState 		= document.getElementById("selState_"+j).value;
			var selDistributor 	= document.getElementById("selDistributor_"+j).value;

			var rv = selState+""+selDistributor;
			if ( arr.indexOf(rv) != -1 )    {
				alert("Exception cannot be duplicate.");
				document.getElementById("selState_"+j).focus();
				return false;
			}
			arr[arri++]=rv;
		}
	}
	return true;
	}
	
	// ------------------------------------------------------
	// Duplication check Ends here
	// ------------------------------------------------------
