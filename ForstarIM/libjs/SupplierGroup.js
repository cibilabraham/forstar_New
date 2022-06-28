var farmEntryExistMsg = "The selected farm is already assigned to another group. Please choose another one."

function validateAddSupplierGroup(form)
{		
	var supplierGroupName	= form.supplierGroupName.value;

	 var entryExist	= document.getElementById("entryExist").value;
		
	if (entryExist!="") {
			alert(farmEntryExistMsg);
			return false;
		}

	if (supplierGroupName=="") {
		alert("Please enter a  supplier Group Name.");
		form.supplierGroupName.focus();
		return false;
	}

	var rowCount	= document.getElementById("hidTableRowCount").value;
	var supplierSelected = false;
	
	if (rowCount>0) {
		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var selSupplier = document.getElementById("supField_"+i);				
				var selSupplierLocation	= document.getElementById("suplocField_"+i);
				var selPond				= document.getElementById("pondField_"+i);
				
				if (selSupplier.value=="") {
					alert("Please select a supplier.");
					selSupplier.focus();
					return false;
				}

				if (selSupplierLocation.value=="") {
					alert("Please select a supplier location.");
					selSupplierLocation.focus();
					return false;
				}

				if (selPond.value=="") {
					alert("Please select a farm.");
					selPond.focus();
					return false;
				}
				

			
				if (selSupplier.value!="") {
					supplierSelected = true;
				}
			}
		}  // For Loop Ends Here
	} // Row Count checking End

	if (!supplierSelected) {
		alert("Please add atleast one farm");
		return false;
	}

	if (!validateSupplierGroup()) {
		return false;
	}
	
	
	if (!confirmSave()) return false;	
	return true;
}

//addNewItemRow('tblAddSupplierData', '<?=$suplierDataId?>', '<?=$suplierName?>', '<?=$suplierLocation?>', '<?=$suplierPond?>');	
//function addNewItemRow(tableId, stkGroupEntryId, supplierName, dbExist, stkFValidation)
function addNewItemRow(tableId, suplierDataId, suplierName, suplierLocation, suplierPond)
{
	var entryExist	= document.getElementById("entryExist").value;
		
	if (entryExist!="") {
			alert(farmEntryExistMsg);
			return false;
		}

	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;	
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
		
	cell1.className	= "listing-item"; cell1.align	= "center";cell1.noWrap = "true";
	cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
	cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
	cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";
	
	
	//if (dbExist=='Y') var stkFieldStatus = "disabled='true'";
	//else var stkFieldStatus = "";
	
	var supplierField = "<select name='supField_"+fieldId+"' id='supField_"+fieldId+"'  onchange=\"xajax_locationName(document.getElementById('supField_"+fieldId+"').value,'',"+fieldId+");\" ><option value=''>--Select--</option>";
																											
	<?php
		if (sizeof($supplierRecs)>0) {	
			foreach ($supplierRecs as $sfr) {
				$supplierId	= $sfr[0];
				$supplier  = $sfr[1];
	?>		
		if (suplierName== "<?=$supplierId?>")  var sel = "Selected";
		else var sel = "";
	supplierField += "<option value=\"<?=$supplierId?>\" "+sel+"><?=$supplier?></option>";	
	<?php
			}
		}
	?>
	supplierField += "</select>";

	var supplierLocField = "<select name='suplocField_"+fieldId+"' id='suplocField_"+fieldId+"'  onchange=\"xajax_pondName(document.getElementById('suplocField_"+fieldId+"').value,'',"+fieldId+");\" ><option value=''>--Select--</option>";
	<?php
		
	?>
	supplierLocField += "</select>";

	var pondField = "<select name='pondField_"+fieldId+"' id='pondField_"+fieldId+"' ><option value=''>--Select--</option>";
	<?php
		
	?>
	pondField += "</select>";

	
	
	var ds = "N";	
	//if (dbExist!='Y') {
	var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//} else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='supplierGroupId_"+fieldId+"' id='supplierGroupId_"+fieldId+"' value='"+suplierDataId+"'>";
	//var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidStkGroupEntryId_"+fieldId+"' type='hidden' id='hidStkGroupEntryId_"+fieldId+"' value='"+stkGroupEntryId+"'><input type='hidden' name='stkGroupInUse_"+fieldId+"' id='stkGroupInUse_"+fieldId+"' value=''><input type='hidden' name='stkGroupEntryIds_"+fieldId+"' id='stkGroupEntryIds_"+fieldId+"' value=''>";	
	//var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidStkGroupEntryId_"+fieldId+"' type='hidden' id='hidStkGroupEntryId_"+fieldId+"' value='"+stkGroupEntryId+"'><input type='hidden' name='stkGroupInUse_"+fieldId+"' id='stkGroupInUse_"+fieldId+"' value=''><input type='hidden' name='stkGroupEntryIds_"+fieldId+"' id='stkGroupEntryIds_"+fieldId+"' value=''>";	
	
	//var hiddenFields = "<input name='status_"+fieldId+"' type='text' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='text' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidStkGroupEntryId_"+fieldId+"' type='hidden' id='hidStkGroupEntryId_"+fieldId+"' value='"+stkGroupEntryId+"'><input type='hidden' name='stkGroupInUse_"+fieldId+"' id='stkGroupInUse_"+fieldId+"' value=''><input type='text' name='stkGroupEntryIds_"+fieldId+"' id='stkGroupEntryIds_"+fieldId+"' value='"+stkGroupEntryId+"'>";		
	//value='"+stkGroupEntryId+"'
	cell1.innerHTML	= supplierField;
	cell2.innerHTML	= supplierLocField;
	cell3.innerHTML	= pondField;		
	cell4.innerHTML = imageButton+hiddenFields;
					

	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;	
}

function setRowItemStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}
function getPackValue(i){



}


// ------------------------------------------------------
// Duplication check starts here
// ------------------------------------------------------
var cArr = new Array();
var cArri = 0;	
function validateSupplierGroup()
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
	var pArr	= new Array();	
	var pa		= 0;
	for (j=0; j<rc; j++) {
		var status = document.getElementById("status_"+j).value;
		if (status!='N') {
			var pond = document.getElementById("pondField_"+j).value;
					
			if (pArr.indexOf(pond)!=-1) {
				alert("pond cannot be duplicate.");
				document.getElementById("pondField_"+j).focus();
				return false;	
			}						
			pArr[pa++]	= pond;					
		}
	}	
	return true;
}


function checkPondUnique(rowId)
{
	
	var supplierId			= document.getElementById("supField_"+rowId).value;
	var supplierLocationId	= document.getElementById("suplocField_"+rowId).value;
	var pondId				= document.getElementById("pondField_"+rowId).value;
	//alert(rowId);
	var supplierGroupId = document.getElementById("hidSupplierGroupId").value;

	//alert(supplierId+","+supplierLocationId+","+pondId+","+supplierGroupId);

	xajax_checkPondUnique(supplierId,supplierLocationId,pondId,supplierGroupId);
	
}