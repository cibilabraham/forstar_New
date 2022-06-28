function validateAddVehicleMaster(form)
{		
	var vehicleNumber	= form.vehicleNumber.value;
	 var vehicleType	= form.vehicleType.value;
	
	 
		
	if (vehicleNumber=="") {
		alert("Please enter a  vehicle Number.");
		form.vehicleNumber.focus();
		return false;
	}

	 if (vehicleType=="") {
		 alert("Please select vehicle Type.");
		 form.vehicleType.focus();
		 return false;
	 }
	
	 

	
	if (!validateHarvestingEquipment()) {
		return false;
	}
	if (!validateHarvestingChemical()) {
		return false;
	}
	
	if (!confirmSave()) return false;	
	return true;
}

// ADD MULTIPLE Item- ADD ROW START
//addNewRow('tblHarvestingEquipment','<?=$harvestingEquipmentId?>', '<?=$harvestingEquipmentName?>',, '<?=$harvestingEquipmentQuantity?>');		
function addNewRow(tableId,EquipmentId,harvestingEquipmentName,harvestingEquipmentQuantity)
{

var tbl		= document.getElementById(tableId);

	var lastRow	= tbl.rows.length;
	 alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "Row_"+fldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	
	cell1.id = "srNo_"+fldId;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";

		//alert("entered");
		//alert("<?=$vehileTypeId?>");
		var harvestingEqu	= "<select name='harvestingEquipment_"+fldId+"' id='harvestingEquipment_"+fldId+"' ><option value='0'>--Select--</option>";
	<?php
		if (sizeof($harvestingEquipmentRecs)>0) {	
			foreach ($harvestingEquipmentRecs as $dcw) {
						$harvestingEquipmentId = $dcw[0];
						$harvestingEquipment	= stripSlash($dcw[1]);
						
	?>	
	
		if (harvestingEquipmentName=="<?=$harvestingEquipmentId?>")  var sel = "Selected";
		else var sel = "";

	harvestingEqu += "<option value=\"<?=$harvestingEquipmentId?>\" "+sel+"><?=$harvestingEquipment?></option>";	
	<?php
			}
		}
		
	?>	
	harvestingEqu += "</select>";
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setTestRowItemStatusVal('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
	var EquipmentQty = "Qty:<input name='harvestingQty_"+fldId+"' type='text' id='harvestingQty_"+fldId+"' value='"+harvestingEquipmentQuantity+"'>";
	
	var hiddenFields = "<input name='Status_"+fldId+"' type='hidden' id='Status_"+fldId+"' value=''><input name='IsFromDB_"+fldId+"' type='hidden' id='IsFromDB_"+fldId+"' value='"+ds+"'><input type='hidden' name='equipmentId_"+fldId+"' id='equipmentId_"+fldId+"' value='"+EquipmentId+"'>";

	//cell1.innerHTML	= "<input name='test_"+fldId+"' type='text' id='test_"+fldId+"' value=\""+unescape(vehicleType)+"\" size='24'>";
	cell1.innerHTML	= harvestingEqu;
	cell2.innerHTML	=EquipmentQty;	
	cell3.innerHTML = imageButton+hiddenFields;	
	
	fldId		= parseInt(fldId)+1;	
	//document.getElementById("hidTestMethodTableRowCount").value = fldId;	
	document.getElementById("hidHarvestingEquipmentsTableRowCount").value = fldId;	






//code end
	
	
	
}

//addChemicalRow('tblHarvestingChemical','<?=$harvestingChemicalId?>','<?=$harvestingChemicalName?>','<?=$harvestingChemicalQuantity?>');	
function addChemicalRow(tableId,harvestingChemicalId,harvestingChemicalName,harvestingChemicalQuantity)
{

var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	// alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "bRow_"+fld;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	
	cell1.id = "srNo_"+fld;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";

		//alert("entered");
		//alert("<?=$vehileTypeId?>");
		var harvestingChemical	= "<select name='harvestingChemical_"+fld+"' id='harvestingChemical_"+fld+"' ><option value='0'>--Select--</option>";
	<?php
		if (sizeof($harvestingChemicalRecs)>0) {	
			foreach ($harvestingChemicalRecs as $dcw) {
						$harvestingChemicalId = $dcw[0];
						$harvestingChemical	= stripSlash($dcw[1]);
						
	?>	
	
		if (harvestingChemicalName=="<?=$harvestingChemicalId?>")  var sel = "Selected";
		else var sel = "";

	harvestingChemical += "<option value=\"<?=$harvestingChemicalId?>\" "+sel+"><?=$harvestingChemical?></option>";	
	<?php
			}
		}
		
	?>	
	harvestingChemical += "</select>";
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setTestRowItemStatus('"+fld+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
	var Qty = "Qty:<input name='Qty_"+fld+"' type='text' id='Qty_"+fld+"' value='"+harvestingChemicalQuantity+"'>";
	
	var hiddenFields = "<input name='bStatus_"+fld+"' type='hidden' id='bStatus_"+fld+"' value=''><input name='bIsFromDB_"+fld+"' type='hidden' id='bIsFromDB_"+fld+"' value='"+ds+"'><input type='hidden' name='chemicalId_"+fld+"' id='chemicalId_"+fld+"' value='"+harvestingChemicalId+"'>";

	//cell1.innerHTML	= "<input name='test_"+fldId+"' type='text' id='test_"+fldId+"' value=\""+unescape(vehicleType)+"\" size='24'>";
	cell1.innerHTML	= harvestingChemical;
	cell2.innerHTML	= Qty;	
	cell3.innerHTML = imageButton+hiddenFields;	
	
	fld		= parseInt(fld)+1;	
	//document.getElementById("hidTestMethodTableRowCount").value = fldId;	
	document.getElementById("hidHarvestingChemicalTableRowCount").value = fld;	






//code end
	
	
	
}

function setTestRowItemStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("bStatus_"+id).value = document.getElementById("bIsFromDB_"+id).value;
		document.getElementById("bRow_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}

function setTestRowItemStatusVal(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("Status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("Row_"+id).style.display = 'none';
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
function validateHarvestingEquipment()
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
	
	var rc = document.getElementById("hidHarvestingEquipmentsTableRowCount").value;
	var pArr	= new Array();	
	var pa		= 0;
	for (j=0; j<rc; j++) {
		var status = document.getElementById("Status_"+j).value;
		if (status!='N') {
			var equipment = document.getElementById("harvestingEquipment_"+j).value;
					
			if (pArr.indexOf(equipment)!=-1) {
				alert("harvesting equipment cannot be duplicate.");
				document.getElementById("harvestingEquipment_"+j).focus();
				return false;	
			}						
			pArr[pa++]	= equipment;					
		}
	}	
	return true;
}

var cArr1 = new Array();
var cArri1 = 0;
function validateHarvestingChemical()
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
	
	var rc1 = document.getElementById("hidHarvestingChemicalTableRowCount").value;
	
	var pArr1	= new Array();	
	var pa1		= 0;
	
	for (l=0; l<rc1; l++) {
		var status = document.getElementById("bStatus_"+l).value;
		
		if (status!='N') {
			var chemical = document.getElementById("harvestingChemical_"+l).value;
					
			if (pArr1.indexOf(chemical)!=-1) {
				alert("harvesting chemical cannot be duplicate.");
				document.getElementById("harvestingChemical_"+l).focus();
				return false;	
			}						
			pArr1[pa1++]	= chemical;					
		}
	}	
	return true;
}