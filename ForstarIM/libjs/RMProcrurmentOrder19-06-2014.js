function validateProcurment(form)
{
//alert('aa');
	var procurmentNo	=	form.procurementIdauto.value;
	var selCompanyName	=	form.selCompanyName.value;
	var selRMSupplierGroup	=	form.selRMSupplierGroup.value;
	var driverName	=	form.driverName.value;
	var vehicleNo	=	form.vehicleNo.value;
	var entryDate	=	form.entryDate.value;
	
	//alert('aa');
	if (procurmentNo=="") {
		alert("Please enter a procurementIdauto No.");
		form.procurmentNo.focus();
		return false;
	}
	if (selCompanyName=="") {
		alert("select selCompanyName Name.");
		form.selCompanyName.focus();
		return false;
	}
	if (selRMSupplierGroup=="") {
		alert("select selRMSupplierGroup .");
		form.selRMSupplierGroup.focus();
		return false;
	}
	if (driverName=="") {
		alert("select driverName.");
		form.driverName.focus();
		return false;
	}
	
	if (vehicleNo=="") {
		alert("select vehicleNo .");
		form.vehicleNo.focus();
		return false;
	}
	if (entryDate=="") {
		alert("select entryDate.");
		form.entryDate.focus();
		return false;
	}
	
 var supplierCount	=	document.getElementById("hidSupplierRowCount").value;

		var scount = 0;
		for (i=0; i<supplierCount; i++)
		{
		   var status = document.getElementById("sstatus_"+i).value;		    
	    	   if (status!='N') 
		    {
			var supplierName		=	document.getElementById("supplierName_"+i);
			var supplierAddress	=	document.getElementById("supplierAddress_"+i);
			var pondName		=	document.getElementById("pondName_"+i);
			var pondAddress	 	= 	document.getElementById("pondAddress_"+i);
			
			
			if( supplierName.value == "" )
			{
				alert("Please Select a Supplier Name.");
				supplierName.focus();
				return false;
			}	
			
			if( supplierAddress.value == "" )
			{
				alert("Please enter a Supplier Address.");
				supplierAddress.focus();
				return false;
			}	
			if( pondName.value == "" )
			{
				alert("Please enter a Pond Name.");
				pondName.focus();
				return false;
			}	
			
			if( pondAddress.value == "" )
			{
				alert("Please enter a Pond Address.");
				pondAddress.focus();
				return false;
			}	
			
			
			
		} else {
			scount++;
		}
	 }
	 
	

	var itemCount	=	document.getElementById("hidTableRowCount").value;

		var count = 0;
		for (i=0; i<itemCount; i++)
		{
		   var status = document.getElementById("status_"+i).value;		    
	    	   if (status!='N') 
		    {
			var euipmentName		=	document.getElementById("equipmentName_"+i);
			var equipmentQty	=	document.getElementById("equipmentQty_"+i);
			var quantity		=	document.getElementById("equipmentIssued_"+i);
			var balanceQty	 	= 	document.getElementById("balanceQty_"+i);
			
			
			if( euipmentName.value == "" )
			{
				alert("Please Select a Equipment Name.");
				euipmentName.focus();
				return false;
			}	
			
			if( quantity.value == "" )
			{
				alert("Please enter a quantity.");
				quantity.focus();
				return false;
			}	
			
			if (euipmentName.value!="" && equipmentQty.value == 0 )
			{
				alert("Sorry!! Selected Stock Item is not Present.");
				euipmentName.focus();
				return false;
			}	
			if (balanceQty.value<0) {
				alert("Required Stock quantity is not available.");
				quantity.focus();
				return false;			
			}
		} else {
			count++;
		}
	 }
	 
	
	 
	 var chemicalCount	=	document.getElementById("hidChemicalRowCount").value;

		var ccount = 0;
		for (i=0; i<chemicalCount; i++)
		{
		   var status = document.getElementById("bstatus_"+i).value;		    
	    	   if (status!='N') 
		    {
			var chemicalName		=	document.getElementById("chemicalName_"+i);
			var chemicalQty	=	document.getElementById("chemicalQty_"+i);
			var chemicalIssued		=	document.getElementById("chemicalIssued_"+i);
			
			
			
			if( chemicalName.value == "" )
			{
				alert("Please Select a Chemical Name.");
				chemicalName.focus();
				return false;
			}	
			
			if( chemicalQty.value == "" )
			{
				alert("Please enter a chemical Quantity.");
				chemicalQty.focus();
				return false;
			}	
			if( chemicalIssued.value == "" )
			{
				alert("Please enter a Chemical Issued.");
				chemicalIssued.focus();
				return false;
			}	
			
			
			
			
			
		} else {
			ccount++;
		}
	 }
	 if(!validateRepeatIssuance()){
	
		return false;
	}
	 
	 return true;

}





function addNewProcurmentItemRow(tableId,editProcurmentId,vehicle, equipmentName, equipmentQty,equipmentIssued,difference,mode)
{

//	alert(editProcurmentId);
	//var rowCountObj	= formObj.rowCount;
	var tbl			= document.getElementById(tableId);
	
	var lastRow		= tbl.rows.length;
	//alert(lastRow);
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
	/*var cell6			= row.insertCell(5);
	var cell7			= row.insertCell(6);
	var cell8			= row.insertCell(7);
	var cell9			= row.insertCell(8);
	var cell10			= row.insertCell(9);*/

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	/*cell6.className	=	"fieldName"; cell6.align = "center";
	cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	cell9.className	=	"fieldName"; cell9.align = "center";
	cell10.className	=	"fieldName"; cell10.align = "center";*/
	/*cell11.className	=	"fieldName"; cell11.align = "center";*/
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='rmId_"+fieldId+"' id='rmId_"+fieldId+"' value='"+editProcurmentId+"'>";

	var vehicle="<input type='hidden' id='vehicle' name='vehicle' >";
	
	/*var equipmentName			= "<select name='equipmentName_"+fieldId+"' Style='display:display;' id='equipmentName_"+fieldId+"' tabindex=1  onchange=\"xajax_equipmentQuantity(document.getElementById('equipmentName_"+fieldId+"').value,document.getElementById('vehicle').value,"+fieldId+");  balanceQty();\"  >";
		equipmentName += "<option value=''>--select--</option>";
	equipmentName +="</select>";*/
	var equipmentName			= "<select name='equipmentName_"+fieldId+"' Style='display:display;' id='equipmentName_"+fieldId+"' tabindex=1  onchange=\"xajax_equipmentQuantity(document.getElementById('equipmentName_"+fieldId+"').value,document.getElementById('vehicle').value,"+fieldId+");  balanceQty();\"  >";
		<?php
		if (sizeof($harvestingequipmentNameRecs)>0) {	
			foreach ($harvestingequipmentNameRecs as $dcw) {
						$equipmentNameId = $dcw[0];
						$equipmentName	= stripSlash($dcw[1]);
						
	?>	
	
		if (equipmentName=="<?=$equipmentNameId?>")  var sel = "Selected";
		else var sel = "";

	equipmentName += "<option value=\"<?=$equipmentNameId?>\" "+sel+"><?=$equipmentName?></option>";	
	<?php
			}
		}
		
	?>	
	equipmentName += "</select>";
	
	
	/*var chemicalName			= "<select name='chemicalName_"+fieldId+"' Style='display:display;' id='chemicalName_"+fieldId+"' tabindex=1  onchange=\"xajax_chemicalQuantity(document.getElementById('chemicalName_"+fieldId+"').value,document.getElementById('vehicleNo_"+fieldId+"').value,"+fieldId+");\"  >";
	chemicalName += "<option value=''>--select--</option>";
	chemicalName +="</select>";*/

	
	
	
	
	//cell1.innerHTML	= driverName;
	//cell2.innerHTML	= vehicleNo;
	cell1.innerHTML	= equipmentName;
	cell2.innerHTML	= "<input name='equipmentQty_"+fieldId+"' type='text' id='equipmentQty_"+fieldId+"' value='"+equipmentQty+"' size='4' readonly style='text-align:right; border:none;'/>";
	
	cell3.innerHTML	= "<input name='equipmentIssued_"+fieldId+"' type='text' id='equipmentIssued_"+fieldId+"' size='4' style='text-align:right' value='"+equipmentIssued+"' tabindex="+fieldId+" onKeyUp='return balanceQty();'>";
	cell4.innerHTML	= "<input name='balanceQty_"+fieldId+"' type='text' id='balanceQty_"+fieldId+"' size='4' readonly style='text-align:right; border:none;' tabindex="+fieldId+"  value='"+difference+"'>";
	//cell5.innerHTML	= chemicalName;
	//cell6.innerHTML	= "<input name='chemicalQty_"+fieldId+"' type='text' id='chemicalQty_"+fieldId+"' value='"+chemicalQty+"' size='4' readonly style='text-align:right; border:none;'/>";
	//cell7.innerHTML	= "<input name='chemicalIssued_"+fieldId+"' type='text' id='chemicalIssued_"+fieldId+"' size='4' style='text-align:right' value='"+chemicalIssued+"' tabindex="+fieldId+" >"+ hiddenFields;
	cell5.innerHTML = imageButton+hiddenFields+vehicle;
	//if(mode=="addmode")
	//{
	xajax_getDetails(document.getElementById('vehicleNo').value,'',fieldId,'');
	//}
	
	
	fieldId		= parseInt(fieldId)+1;
	document.getElementById("hidTableRowCount").value = fieldId;
	
}

function setIssuanceItemStatus(id)
{
	if (confirmRemoveItem())
	{
	
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none'; 		
	}
	return false;
}
function addNewRMProcurmentSupplierRow(tableId,editProcurmentId,supplierGroup,supplierName1, supplierAddress,pondName1,pondAddress,mode)
{

//	alert(editProcurmentId);
	//var rowCountObj	= formObj.rowCount;
	var tbl			= document.getElementById(tableId);
	
	var lastRow		= tbl.rows.length;
	//alert(lastRow);
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "srow_"+fieldvalue;

	var cell1			= row.insertCell(0);
	var cell2			= row.insertCell(1);
	var cell3			= row.insertCell(2);
	var cell4			= row.insertCell(3);
	var cell5			= row.insertCell(4);
	/*var cell6			= row.insertCell(5);
	var cell7			= row.insertCell(6);
	var cell8			= row.insertCell(7);
	var cell9			= row.insertCell(8);
	var cell10			= row.insertCell(9);*/

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	/*cell6.className	=	"fieldName"; cell6.align = "center";
	cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	cell9.className	=	"fieldName"; cell9.align = "center";
	cell10.className	=	"fieldName"; cell10.align = "center";*/
	/*cell11.className	=	"fieldName"; cell11.align = "center";*/
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceSupplierStatus('"+fieldvalue+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='sstatus_"+fieldvalue+"' type='hidden' id='sstatus_"+fieldvalue+"' value=''><input name='IsFromDB_"+fieldvalue+"' type='hidden' id='IsFromDB_"+fieldvalue+"' value='"+ds+"'><input type='hidden' name='srmId_"+fieldvalue+"' id='srmId_"+fieldvalue+"' value='"+editProcurmentId+"'>";

	var supplierGroup="<input type='hidden' id='supplierGroup' name='supplierGroup' >";
	//alert('aa');
	var supplierName= "<select name='supplierName_"+fieldvalue+"' Style='display:display;' id='supplierName_"+fieldvalue+"' tabindex=1  onchange=\"xajax_rmProcurmentSupplierAddress(document.getElementById('supplierName_"+fieldvalue+"').value,"+fieldvalue+",''); \"  >";
	<?php 
										foreach($supplierRecs as $sr)
										{
										//alert($sr[0]);
						$supplierNameId		=	$sr[1];
						$supplierNameValue	=	stripSlash($sr[2]);
						?>
						if(supplierName1=="<?=$supplierNameId?>") var sel="Selected";
					  else var sel = "";
					  
                       supplierName+="<option value=\"<?=$supplierNameId?>\" "+sel+"><?=$supplierNameValue?></option>";
                                                    <? }
								?>	
					supplierName += "</select>";
		
		
		
		
	
	
	
	/*var supplierName			= "<select name='supplierName_"+fieldvalue+"' Style='display:display;' id='supplierName_"+fieldvalue+"' tabindex=1  onchange=\"xajax_rmProcurmentSupplierAddress(document.getElementById('supplierName_"+fieldvalue+"').value,"+fieldvalue+",''); \"  >";
		supplierName += "<option value=''>--select--</option>";
	supplierName +="</select>";
	var pondName			= "<select name='pondName_"+fieldvalue+"' Style='display:display;' id='pondName_"+fieldvalue+"' tabindex=1  onchange=\"xajax_rmProcurmentPondAddress(document.getElementById('pondName_"+fieldvalue+"').value,"+fieldvalue+"); \"  >";
		pondName += "<option value=''>--select--</option>";
	pondName +="</select>";*/
	
	
	var pondName= "<select name='pondName_"+fieldvalue+"' Style='display:display;' id='pondName_"+fieldvalue+"' tabindex=1  onchange=\"xajax_rmProcurmentPondAddress(document.getElementById('pondName_"+fieldvalue+"').value,"+fieldvalue+"); \"  >";
	<?php 	foreach($pondRecs as $pondval)
			{
										foreach($pondval as $pnd)
										{
										//alert($sr[0]);
						$pondNameId		=	$pnd[1];
						$pondNameValue	=	stripSlash($pnd[2]);
						?>
						if(pondName1=="<?=$pondNameId?>") var sel="Selected";
					  else var sel = "";
					  
                       pondName+="<option value=\"<?=$pondNameId?>\" "+sel+"><?=$pondNameValue?></option>";
                                                    <? }
													}
								?>	
					pondName += "</select>";
	
	
	
	/*var chemicalName			= "<select name='chemicalName_"+fieldId+"' Style='display:display;' id='chemicalName_"+fieldId+"' tabindex=1  onchange=\"xajax_chemicalQuantity(document.getElementById('chemicalName_"+fieldId+"').value,document.getElementById('vehicleNo_"+fieldId+"').value,"+fieldId+");\"  >";
	chemicalName += "<option value=''>--select--</option>";
	chemicalName +="</select>";*/

	
	
	
	
	//cell1.innerHTML	= driverName;
	//cell2.innerHTML	= vehicleNo;
	cell1.innerHTML	= supplierName;
	cell2.innerHTML	= "<input name='supplierAddress_"+fieldvalue+"' type='text' id='supplierAddress_"+fieldvalue+"' value='"+supplierAddress+"' size='15' readonly style='text-align:right; border:none;'/>";
	
	cell3.innerHTML	= pondName;
	cell4.innerHTML	= "<input name='pondAddress_"+fieldvalue+"' type='text' id='pondAddress_"+fieldvalue+"' size='15' readonly style='text-align:right; border:none;' tabindex="+fieldvalue+"  value='"+pondAddress+"'>";
	//cell5.innerHTML	= chemicalName;
	//cell6.innerHTML	= "<input name='chemicalQty_"+fieldId+"' type='text' id='chemicalQty_"+fieldId+"' value='"+chemicalQty+"' size='4' readonly style='text-align:right; border:none;'/>";
	//cell7.innerHTML	= "<input name='chemicalIssued_"+fieldId+"' type='text' id='chemicalIssued_"+fieldId+"' size='4' style='text-align:right' value='"+chemicalIssued+"' tabindex="+fieldId+" >"+ hiddenFields;
	cell5.innerHTML = imageButton+hiddenFields+supplierGroup;
	if(mode=="addmode")
	{
	xajax_rmProcurmentSupplierName(document.getElementById('selRMSupplierGroup').value,fieldvalue,'');
	}
	fieldvalue		= parseInt(fieldvalue)+1;
	document.getElementById("hidSupplierRowCount").value = fieldvalue;
	
}








function setIssuanceSupplierStatus(id)
{
	if (confirmRemoveItem())
	{
	
		document.getElementById("sstatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("srow_"+id).style.display = 'none'; 		
	}
	return false;
}


function addNewRMProcurmentChemicalItemRow(tableId,editProcurmentId, vehicle,chemicalName,chemicalQty,chemicalIssued,mode)
{

//	alert(editProcurmentId);
	//var rowCountObj	= formObj.rowCount;
	var tbl			= document.getElementById(tableId);
	
	var lastRow		= tbl.rows.length;
	//alert(lastRow);
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "brow_"+fld;

	var cell1			= row.insertCell(0);
	var cell2			= row.insertCell(1);
	var cell3			= row.insertCell(2);
	var cell4			= row.insertCell(3);
	//var cell5			= row.insertCell(4);
	/*var cell6			= row.insertCell(5);
	var cell7			= row.insertCell(6);
	var cell8			= row.insertCell(7);
	var cell9			= row.insertCell(8);
	var cell10			= row.insertCell(9);*/

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	//cell5.className	=	"fieldName"; cell5.align = "center";
	/*cell6.className	=	"fieldName"; cell6.align = "center";
	cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	cell9.className	=	"fieldName"; cell9.align = "center";
	cell10.className	=	"fieldName"; cell10.align = "center";*/
	/*cell11.className	=	"fieldName"; cell11.align = "center";*/
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"bsetIssuanceItemStatus('"+fld+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='bstatus_"+fld+"' type='hidden' id='bstatus_"+fld+"' value=''><input name='IsFromDB_"+fld+"' type='hidden' id='IsFromDB_"+fld+"' value='"+ds+"'><input type='hidden' name='brmId_"+fld+"' id='brmId_"+fld+"' value='"+editProcurmentId+"'>";

	var vehicle="<input type='hidden' id='vehicle' name='vehicle' >";
	
	
	
	var chemicalName			= "<select name='chemicalName_"+fld+"' Style='display:display;' id='chemicalName_"+fld+"' tabindex=1  onchange=\"xajax_chemicalQuantity(document.getElementById('chemicalName_"+fld+"').value,document.getElementById('vehicle').value,"+fld+");\"  >";
	chemicalName += "<option value=''>--select--</option>";
	chemicalName +="</select>"
	
	
	
	
	//cell1.innerHTML	= driverName;
	//cell2.innerHTML	= vehicleNo;
	cell1.innerHTML	= chemicalName;
	
	cell2.innerHTML	= "<input name='chemicalQty_"+fld+"' type='text' id='chemicalQty_"+fld+"' value='"+chemicalQty+"' size='4' readonly style='text-align:right; border:none;'/>";
	cell3.innerHTML	= "<input name='chemicalIssued_"+fld+"' type='text' id='chemicalIssued_"+fld+"' size='4' style='text-align:right' value='"+chemicalIssued+"' tabindex="+fld+" >";
	//cell3.innerHTML	= "<input name='chemicalIssued_"+fld+"' type='text' id='chemicalIssued_"+fld+"' size='4' style='text-align:right' value='"+chemicalIssued+"' tabindex="+fld+" >"+ hiddenFields;
	cell4.innerHTML = imageButton+hiddenFields+vehicle;
	
	xajax_getDetailvalue(document.getElementById('vehicleNo').value,'',fld,'');
	
	fld		= parseInt(fld)+1;
	document.getElementById("hidChemicalRowCount").value = fld;
}




function bsetIssuanceItemStatus(id)
{
	if (confirmRemoveItem())
	{
	
		document.getElementById("bstatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("brow_"+id).style.display = 'none'; 		
	}
	return false;
}


/*function addNewRMProcurmentChemicalItemRow(tableId,editProcurmentId, vehicle,chemicalName,chemicalQty,chemicalIssued,mode)
{

	
	//var rowCountObj	= formObj.rowCount;
	var tbl			= document.getElementById(tableId);
	
	var lastRow		= tbl.rows.length;
	//alert(lastRow);
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "brow_"+fieldvalueId;

	var cell1			= row.insertCell(0);
	var cell2			= row.insertCell(1);
	var cell3			= row.insertCell(2);
	var cell4			= row.insertCell(3);
	*/
	
	/*var cell5			= row.insertCell(4);
	var cell6			= row.insertCell(5);
	var cell7			= row.insertCell(6);
	var cell8			= row.insertCell(7);
	var cell9			= row.insertCell(8);
	var cell10			= row.insertCell(9);*/

	/*cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	*/
	//cell5.className	=	"fieldName"; cell5.align = "center";
	/*cell6.className	=	"fieldName"; cell6.align = "center";
	cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	cell9.className	=	"fieldName"; cell9.align = "center";
	cell10.className	=	"fieldName"; cell10.align = "center";*/
	/*cell11.className	=	"fieldName"; cell11.align = "center";*/
	
	/*var ds = "N";	
	var imageButton = "<a href='###' onClick=\"bsetIssuanceItemStatus('"+fieldvalueId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='histatus_"+fieldvalueId+"' type='hidden' id='histatus_"+fieldvalueId+"' value=''><input name='IsFromDB_"+fieldvalueId+"' type='hidden' id='IsFromDB_"+fieldvalueId+"' value='"+ds+"'><input type='hidden' name='brmId_"+fieldvalueId+"' id='brmId_"+fieldvalueId+"' value='"+editProcurmentId+"'>";

	var vehicle="<input type='hidden' id='vehicle' name='vehicle' >";*/
	
	/*var equipmentName			= "<select name='equipmentName_"+fld+"' Style='display:display;' id='equipmentName_"+fld+"' tabindex=1  onchange=\"xajax_equipmentQuantity(document.getElementById('equipmentName_"+fld+"').value,document.getElementById('vehicle').value,"+fld+");  balanceQty();\"  >";
		equipmentName += "<option value=''>--select--</option>";
	equipmentName +="</select>";*/
	
	/*var chemicalName			= "<select name='chemicalName_"+fieldvalueId+"' Style='display:display;' id='chemicalName_"+fieldvalueId+"' tabindex=1  onchange=\"xajax_chemicalQuantity(document.getElementById('chemicalName_"+fieldvalueId+"').value,document.getElementById('vehicle').value,"+fieldvalueId+");\"  >";
	chemicalName += "<option value=''>--select--</option>";
	chemicalName +="</select>";*/

	
	
	
	
	//cell1.innerHTML	= driverName;
	//cell2.innerHTML	= vehicleNo;
	// cell1.innerHTML	= equipmentName;
	// cell2.innerHTML	= "<input name='equipmentQty_"+fieldId+"' type='text' id='equipmentQty_"+fieldId+"' value='"+equipmentQty+"' size='4' readonly style='text-align:right; border:none;'/>";
	
	// cell3.innerHTML	= "<input name='equipmentIssued_"+fieldId+"' type='text' id='equipmentIssued_"+fieldId+"' size='4' style='text-align:right' value='"+equipmentIssued+"' tabindex="+fieldId+" onKeyUp='return balanceQty();'>";
	// cell4.innerHTML	= "<input name='balanceQty_"+fieldId+"' type='text' id='balanceQty_"+fieldId+"' size='4' readonly style='text-align:right; border:none;' tabindex="+fieldId+"  value='"+difference+"'>";
	/*cell1.innerHTML	= chemicalName;
	cell2.innerHTML	= "<input name='chemicalQty_"+fieldvalueId+"' type='text' id='chemicalQty_"+fieldvalueId+"' value='"+chemicalQty+"' size='4' readonly style='text-align:right; border:none;'/>";
	cell3.innerHTML	= "<input name='chemicalIssued_"+fieldvalueId+"' type='text' id='chemicalIssued_"+fieldvalueId+"' size='4' style='text-align:right' value='"+chemicalIssued+"' tabindex="+fieldvalueId+" >"+ hiddenFields;
	cell4.innerHTML = imageButton+hiddenFields+vehicle;
	
	xajax_getDetailvalue(document.getElementById('vehicleNo').value,'',fieldvalueId,'');
	
	
	
	
	fieldvalueId		= parseInt(fieldvalueId)+1;
	document.getElementById("hidChemicalRowCount").value = fieldvalueId;*/
	
/*}
function bsetIssuanceItemStatus(id)
{
	if (confirmRemoveItem())
	{
	
		document.getElementById("histatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("brow_"+id).style.display = 'none'; 		
	}
	return false;
}*/


// Balance Qty
function balanceQty()
{
	
	var stockStatus = false;
	var rowCount	= document.getElementById("hidTableRowCount").value;
	
	var total	= 0;
	
	var equipmentQty = "equipmentQty_";
	var pQty	 = "equipmentIssued_";
	var balanceQty	 = "balanceQty_";	
	
	for (i=0; i<rowCount; i++) {
	   var status = document.getElementById("status_"+i).value;		
	   if (status!='N') 
	    {
		
	  	var quantity =	0;
	 	 if (document.getElementById(pQty+i).value!="") {
			 document.getElementById(balanceQty+i).value	 = document.getElementById(equipmentQty+i).value - document.getElementById(pQty+i).value;
	  	} else {
			document.getElementById(balanceQty+i).value =0;
		}

		if (document.getElementById(balanceQty+i).value<0) {
			stockStatus = true;			
		} 
	  }
	}

	if (stockStatus==true) {
		document.getElementById("hidStockItemStatus").value='P';
	} else {
		document.getElementById("hidStockItemStatus").value='C';
	}	
}

//Validate repeated
function validateRepeatIssuance()
{
//alert('aaa');
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
	
	var prevOrder = 0;
	var arr = new Array();
	var arri=0;
	for( j=0; j<rc; j++ )	{
	    var status = document.getElementById("status_"+j).value;
	    if (status!='N') 
	    {
		var rv = document.getElementById("equipmentName_"+j).value;	
		if ( arr.indexOf(rv) != -1 )	{
			alert("Equipment  Cannot be duplicate.");
			document.getElementById("equipmentName_"+j).focus();
			return false;
		}
		arr[arri++]=rv;
            }
	}
	
	var ch = document.getElementById("hidChemicalRowCount").value;
	var ar = new Array();
	var arrl=0;
	for( j=0; j<ch; j++ )	{
	    var status = document.getElementById("bstatus_"+j).value;
	    if (status!='N') 
	    {
		var rv = document.getElementById("chemicalName_"+j).value;	
		if ( ar.indexOf(rv) != -1 )	{
			alert("Chemical Cannot be duplicate.");
			document.getElementById("chemicalName_"+j).focus();
			return false;
		}
		ar[arrl++]=rv;
            }
	}
	
	var sc = document.getElementById("hidSupplierRowCount").value;
	
	
	var arra = new Array();
	var arrk=0;
	for( j=0; j<sc; j++ )	{
	    var status = document.getElementById("sstatus_"+j).value;
	    if (status!='N') 
	    {
		var rv = document.getElementById("pondName_"+j).value;	
		if ( arr.indexOf(rv) != -1 )	{
			alert("Pond Name  Cannot be duplicate.");
			document.getElementById("pondName_"+j).focus();
			return false;
		}
		arr[arrk++]=rv;
            }
	}
	return true;	
}