function validateWeightmentDatasheet(form)
{
	if (!confirmSave()) return false;
	return true;
}
function addNewWeighmentMultiple(tableId, editProcurmentId,driverId, vehicleNumber, equipmentName, equipmentQty,equipmentIssued,difference,chemicalName,chemicalQty,chemicalIssued)
{
	var tbl			= document.getElementById(tableId);
	var lastRow		= tbl.rows.length;
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "row_"+fieldId;

	var cell1			= row.insertCell(0);
	var cell2			= row.insertCell(1);
	var cell3			= row.insertCell(2);
	var cell4			= row.insertCell(3);
	var cell5			= row.insertCell(4);
	var cell6			= row.insertCell(5);
	var cell7			= row.insertCell(6);
	var cell8			= row.insertCell(7);
	var cell9			= row.insertCell(8);
	var cell10			= row.insertCell(9);

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	cell6.className	=	"fieldName"; cell6.align = "center";
	cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	cell9.className	=	"fieldName"; cell9.align = "center";
	cell10.className	=	"fieldName"; cell9.align = "center";
	
	var ds = "N";	
	var imageButton = "<a href='javascript:void(0);' onClick=\"setIssuanceItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='rmId_"+fieldId+"' id='rmId_"+fieldId+"' value='"+editProcurmentId+"'>";
	
	var cell1Val = '<select onchange="xajax_getProcessCode(this.value,0,'+fieldId+');" id="product_species'+fieldId+'" name="product_species[]" required><option value=""> -- Select --</option>';
	<?
		if(sizeof($productSpecies) > 0)
		{																			
			foreach($productSpecies as $productSpecie)
			{
	?>
				cell1Val+= '<option value="<?=$productSpecie[0];?>"><?=$productSpecie[1];?></option>';
	<?
			}																			
		}
	?>	
	cell1Val+='</select>';

	cell1.innerHTML	= cell1Val;
	cell2.innerHTML	= '<select id="process_code'+fieldId+'" style="display:display;" name="process_code[]"><option value="">--select--</option></select>';
	
	var cell3Val = '<select id="package_type'+fieldId+'" name="package_type[]" required><option value=""> -- Select --</option>';
	<?
		if(sizeof($packageTypeList) > 0)
		{
			foreach($packageTypeList as $lotID)
			{
	?>
		cell3Val+= '<option value="<?=$lotID[0]?>"><?=$lotID[1]?></option>';
	<?
			}																			
		}
	?>	
	cell3Val+= '</select>';
																	
	cell3.innerHTML	= cell3Val;
	cell4.innerHTML	= '<input type="text" name="grade_count[]" id="grade_count'+fieldId+'" size="10" required />';
	cell5.innerHTML	= '<input type="text" name="count_code[]" id="count_code'+fieldId+'" size="10" required />';
	cell6.innerHTML	= '<input type="text" name="weight[]" id="weight'+fieldId+'" size="10" onkeyup="calTotalQty();" required />';
	cell7.innerHTML	= '<input type="text" name="soft_precent[]" id="soft_precent'+fieldId+'" size="10" required />';
	cell8.innerHTML	= '<input type="text" name="soft_weight[]" id="soft_weight'+fieldId+'" size="10" required />';
	cell9.innerHTML	= '<input type="text" name="pkg_nos[]" id="pkg_nos'+fieldId+'" size="10" required />';

	cell10.innerHTML = imageButton;
	
	fieldId		= parseInt(fieldId)+1;
	document.getElementById("hidTableRowCount").value = fieldId;
}
function setIssuanceItemStatus(id)
{
	if (confirmRemoveItem())
	{
		//document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none'; 		
	}
	return false;
}
