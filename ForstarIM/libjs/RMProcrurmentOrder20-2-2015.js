function validateProcurment(form)
{
	
	var procurmentNo	=	document.getElementById('procurmentNo').value;
	var selCompanyName	=	document.getElementById('selCompanyName').value;
	var entryDate	=	document.getElementById('entryDate').value;
	var schedule_date	=	document.getElementById('schedule_date').value;
	var hidVehicleAndDriverTableRowCount=document.getElementById('hidVehicleAndDriverTableRowCount').value;
	//var selRMSupplierGroup	=	form.selRMSupplierGroup.value;
	//var driverName	=	form.driverName.value;
	//var vehicleNo	=	form.vehicleNo.value;
	if (procurmentNo=="") {
		alert("Please enter a procurement No.");
		document.getElementById("procurmentNo").focus();
		return false;
	}
	if (selCompanyName=="") {
		alert("select Company Name.");
		form.selCompanyName.focus();
		return false;
	}
	/*if (selRMSupplierGroup=="") {
		alert("select selRMSupplierGroup .");
		form.selRMSupplierGroup.focus();
		return false;
	}*/
	// if (driverName=="") {
		// alert("select driverName.");
		// form.driverName.focus();
		// return false;
	// }
	
	// if (vehicleNo=="") {
		// alert("select vehicleNo .");
		// form.vehicleNo.focus();
		// return false;
	//}
	if (entryDate=="") {
		alert("select Entry Date.");
		form.entryDate.focus();
		return false;
	}
	if (schedule_date=="") {
		alert("select Schedule Date.");
		form.schedule_date.focus();
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
			//var supplierAddress	=	document.getElementById("supplierAddress_"+i);
			//var pondName		=	document.getElementById("pondName_"+i);
			//var pondAddress	 	= 	document.getElementById("pondAddress_"+i);
			
			
			if( supplierName.value == "" )
			{
				alert("Please Select a Supplier Name.");
				supplierName.focus();
				return false;
			}	
			
			/*if( supplierAddress.value == "" )
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
			}*/	
			
			
			
		} else {
			scount++;
		}
	 }
	 
	//### last updated##############################################################################
	var itemCount	=	document.getElementById("hidHarvestingEquipmentsTableRowCount").value;

		var count = 0;
		for (i=0; i<itemCount; i++)
		{
		
		   var status = document.getElementById("Status_"+i).value;		    
	    	   if (status!='N') 
		    {
			//var equipment	=	document.getElementById("harvestingEquipment_"+i);
			var equipmentName		=	document.getElementById("harvestingEquipment_"+i);
			var equipmentQty	=	document.getElementById("harvestingQty_"+i);
			
			//alert(equipmentName.value);
			// if( equipmentName.value == "" || equipmentName.value == "0"  )
			// {
				// alert("Please Select a equipment Name.");
				// equipmentName.focus();
				// return false;
			// }
			if( equipmentName.value != 0  )
			{	
				if( equipmentQty.value == "" )
				{
					alert("Please enter a quantity.");
					equipmentQty.focus();
					return false;
				}	
			}	
			
		} else {
			count++;
		}
	 }
	
	//### last updated##############################################################################
	var chemicalCount	=	document.getElementById("hidHarvestingChemicalTableRowCount").value;

		var ccount = 0;
		for (i=0; i<chemicalCount; i++)
		{
		   var status = document.getElementById("bStatus_"+i).value;		    
	    	   if (status!='N') 
		    {
			var chemicalName		=	document.getElementById("harvestingChemical_"+i);
			var chemicalQty	=	document.getElementById("Qty_"+i);
			
			
			
			// if( chemicalName.value == "" )
			// {
				// alert("Please Select a Chemical Name.");
				// chemicalName.focus();
				// return false;
			// }	
			if( chemicalName.value != 0 )
			{
				if( chemicalQty.value == "" )
				{
					alert("Please enter a chemical Quantity.");
					chemicalQty.focus();
					return false;
				}	
			}
			
			
			
			
		} else {
			ccount++;
		}
	 }
	 
	 
	 
	 
	 
	 
	 
	/*var itemCount	=	document.getElementById("hidTableRowCount").value;

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
	 
	*/
	 
	 /*var chemicalCount	=	document.getElementById("hidChemicalRowCount").value;

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
	 }*/
	if(!validateRepeatIssuance()){
	
	
		return false;
	}
	if(!confirmSaveEquipment())
	{
		return false;
	}
	if(!confirmSave()) {
		return false;
	} else {
		for (i=0; i<hidVehicleAndDriverTableRowCount; i++) 
		{
			document.getElementById('vehicleNumber_'+i).disabled=false;
		}
		 
		 return true;
	}
	

}






function addNewRMProcurmentSupplierRow(tableId,editProcurmentId,supplierName1, supplierGroup,pondName1,pondLocation,pondQty,mode)
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
	var cell6			= row.insertCell(5);
	/*var cell7			= row.insertCell(6);
	var cell8			= row.insertCell(7);
	var cell9			= row.insertCell(8);
	var cell10			= row.insertCell(9);*/

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	cell6.className	=	"fieldName"; cell6.align = "center";
	/*cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	cell9.className	=	"fieldName"; cell9.align = "center";
	cell10.className	=	"fieldName"; cell10.align = "center";*/
	/*cell11.className	=	"fieldName"; cell11.align = "center";*/
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceSupplierStatus('"+fieldvalue+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='sstatus_"+fieldvalue+"' type='hidden' id='sstatus_"+fieldvalue+"' value=''><input name='IsFromDB_"+fieldvalue+"' type='hidden' id='IsFromDB_"+fieldvalue+"' value='"+ds+"'><input type='hidden' name='srmId_"+fieldvalue+"' id='srmId_"+fieldvalue+"' value='"+editProcurmentId+"'>";

	//var supplierGroup="<input type='hidden' id='supplierGroup' name='supplierGroup' >";
	//alert('aa');
	var supplierName= "<select name='supplierName_"+fieldvalue+"' Style='display:display;' id='supplierName_"+fieldvalue+"' tabindex=1  onchange=\"xajax_rmProcurmentSupplierGroup(document.getElementById('supplierName_"+fieldvalue+"').value,"+fieldvalue+",''); \"  ><option value=''>--select--</option>";
	<?php 
										foreach($supplierNameRecs as $sr)
										{
										//alert($sr[0]);
						$supplierNameId		=	$sr[0];
						$supplierNameValue	=	stripSlash($sr[1]);
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
	
	
	var pondName= "<select name='pondName_"+fieldvalue+"' Style='display:display;' id='pondName_"+fieldvalue+"' tabindex=1  onchange=\"xajax_rmProcurmentPondDetails(document.getElementById('pondName_"+fieldvalue+"').value,"+fieldvalue+"); \"  ><option value=''>--select--</option>";
	
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
	cell2.innerHTML	= "<input name='supplierGroup_"+fieldvalue+"' type='text' id='supplierGroup_"+fieldvalue+"' value='"+supplierGroup+"' size='15' readonly style='text-align:right; border:none;'/>";
	
	cell3.innerHTML	= pondName;
	cell4.innerHTML	= "<input name='pondLocation_"+fieldvalue+"' type='text' id='pondLocation_"+fieldvalue+"' size='15' readonly style='text-align:right; border:none;' tabindex="+fieldvalue+"  value='"+pondLocation+"'>";
	cell5.innerHTML	= "<input name='pondQty_"+fieldvalue+"' type='text' id='pondQty_"+fieldvalue+"' size='15' readonly style='text-align:right; border:none;' tabindex="+fieldvalue+"  value='"+pondQty+"'>";
	/*cell6.innerHTML	= "<input name='pondSize_"+fieldvalue+"' type='text' id='pondSize_"+fieldvalue+"' size='15' readonly style='text-align:right; border:none;' tabindex="+fieldvalue+"  value='"+pondLocation+"'>";
	cell7.innerHTML	= "<input name='totalQnty_"+fieldvalue+"' type='text' id='totalQnty_"+fieldvalue+"' size='15' readonly style='text-align:right; border:none;' tabindex="+fieldvalue+"  value='"+pondLocation+"'>";
	*/
	//cell5.innerHTML	= chemicalName;
	//cell6.innerHTML	= "<input name='chemicalQty_"+fieldId+"' type='text' id='chemicalQty_"+fieldId+"' value='"+chemicalQty+"' size='4' readonly style='text-align:right; border:none;'/>";
	//cell7.innerHTML	= "<input name='chemicalIssued_"+fieldId+"' type='text' id='chemicalIssued_"+fieldId+"' size='4' style='text-align:right' value='"+chemicalIssued+"' tabindex="+fieldId+" >"+ hiddenFields;
	//cell5.innerHTML = imageButton+hiddenFields+supplierGroup;
	cell6.innerHTML = imageButton+hiddenFields;
	if(mode=="addmode")
	{
	//xajax_rmProcurmentSupplierName(document.getElementById('selRMSupplierGroup').value,fieldvalue,'');
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

/*function addNewProcurmentItemRow(tableId,editProcurmentId,vehicle, equipmentName, equipmentQty,equipmentIssued,difference,mode)
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
	//var cell6			= row.insertCell(5);
	//var cell7			= row.insertCell(6);
	//var cell8			= row.insertCell(7);
	//var cell9			= row.insertCell(8);
	//var cell10			= row.insertCell(9);

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	//cell6.className	=	"fieldName"; cell6.align = "center";
	//cell7.className	=	"fieldName"; cell7.align = "center";
	//cell8.className	=	"fieldName"; cell8.align = "center";
	//cell9.className	=	"fieldName"; cell9.align = "center";
	//cell10.className	=	"fieldName"; cell10.align = "center";
	//cell11.className	=	"fieldName"; cell11.align = "center";
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='rmId_"+fieldId+"' id='rmId_"+fieldId+"' value='"+editProcurmentId+"'>";

	var vehicle="<input type='hidden' id='vehicle' name='vehicle' >";
	
	//var equipmentName			= "<select name='equipmentName_"+fieldId+"' Style='display:display;' id='equipmentName_"+fieldId+"' tabindex=1  onchange=\"xajax_equipmentQuantity(document.getElementById('equipmentName_"+fieldId+"').value,document.getElementById('vehicle').value,"+fieldId+");  balanceQty();\"  >";
	//	equipmentName += "<option value=''>--select--</option>";
	//equipmentName +="</select>";
	var equipmentName			= "<select name='equipmentName_"+fieldId+"' Style='display:display;' id='equipmentName_"+fieldId+"' tabindex=1  onchange=\"xajax_equipmentQuantity(document.getElementById('equipmentName_"+fieldId+"').value,document.getElementById('vehicle').value,"+fieldId+");  balanceQty();\"  >";
		<?php
		if (sizeof($harvestingEquipmentRecs)>0) {	
			foreach ($harvestingEquipmentRecs as $dcw) {
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
	
	
	//var chemicalName			= "<select name='chemicalName_"+fieldId+"' Style='display:display;' id='chemicalName_"+fieldId+"' tabindex=1  onchange=\"xajax_chemicalQuantity(document.getElementById('chemicalName_"+fieldId+"').value,document.getElementById('vehicleNo_"+fieldId+"').value,"+fieldId+");\"  >";
	//chemicalName += "<option value=''>--select--</option>";
	//chemicalName +="</select>";

	
	
	
	
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
}*/
function addVehicleAndDriverRow(tableId,editProcurmentDriverId,VehicleId,DriverId,CopyData,mode)
{
		var schedule=document.getElementById('schedule_date').value;
		var tbl		= document.getElementById(tableId);
			var lastRow	= tbl.rows.length;
			var row		= tbl.insertRow(lastRow);
			
			row.height	= "28";
			row.className 	= "whiteRow";
			row.align 	= "center";
			row.id 		= "dRow_"+fdId;	
			
			var cell1	= row.insertCell(0);
			var cell2	= row.insertCell(1);
			var cell3	= row.insertCell(2);
			
			cell1.id = "srNo_"+fdId;		
			cell1.className	= "listing-item"; cell1.align	= "center";
			cell2.className	= "listing-item"; cell2.align	= "center";
			cell3.className	= "listing-item"; cell3.align	= "center";
			
			var hidProcurmentId=document.getElementById('hidProcurmentId').value;
			//alert(hidProcurmentId);
			if (CopyData) {
			var fFieldId = getMaxRowId();
			//alert(fFieldId);
			if (fFieldId>=0) {
			VehicleId 	 = document.getElementById('vehicleNumber_'+fFieldId).value;
			DriverId 	 ='';
			//DriverId 	 = document.getElementById('driverName_'+fFieldId).value;
			schedule_date= document.getElementById('schedule_date').value;
			//alert(VehicleId+','+DriverId);
			}
			
			}
			<?php
			//$valu='<script language=javascript>document.write(schedule_date);</script>';
			//$driverRecs 			= $rmProcurmentOrderObj->fetchAllDriverName(schedule_date,'');
			?>
		
			var vehicle	= "<select name='vehicleNumber_"+fdId+"' id='vehicleNumber_"+fdId+"'  ><option value='0'>--Select--</option>";
			vehicle += "</select>";	
				
				
			var driver	= "<select name='driverName_"+fdId+"' id='driverName_"+fdId+"' ><option value='0'>--Select--</option>";
			driver += "</select>";
			
			var ds = "N";	
			//if( fieldId >= 1) 
			var imageButton = "<a href='###' onClick=\"setTestRowVehicleAndDriverStatus('"+fdId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
			
			//var Qty = "<input name='Qty_"+fld+"' size='15' type='text' id='Qty_"+fld+"' value='"+harvestingChemicalQuantity+"'>";
			
			var hiddenFields = "<input name='dStatus_"+fdId+"' type='hidden' id='dStatus_"+fdId+"' value=''><input name='dIsFromDB_"+fdId+"' type='hidden' id='dIsFromDB_"+fdId+"' value='"+ds+"'><input type='hidden' name='editProcurmentDriverId_"+fdId+"' id='editProcurmentDriverId_"+fdId+"' value='"+editProcurmentDriverId+"'>";

			//cell1.innerHTML	= "<input name='test_"+fldId+"' type='text' id='test_"+fldId+"' value=\""+unescape(vehicleType)+"\" size='24'>";
			cell1.innerHTML	= vehicle;
			cell2.innerHTML	= driver;
			//cell2.innerHTML	= Qty;	
			cell3.innerHTML = imageButton+hiddenFields;	
			if(schedule!="")
			{
				if(mode=="addmode")
				{
					xajax_rmProcurmentScheduleDriverAndVehicleDetails(document.getElementById('schedule_date').value,fdId,DriverId,VehicleId,hidProcurmentId,'','1');
				//xajax_rmProcurmentScheduleDriverAndVehicleDetails(document.getElementById('schedule_date').value,fdId,DriverId,VehicleId,'','','1');
				}
			}
			if (fFieldId>=0) {
			document.getElementById('vehicleNumber_'+fdId).disabled=true;
			}
			fdId		= parseInt(fdId)+1;	
			//document.getElementById("hidTestMethodTableRowCount").value = fldId;	
			document.getElementById("hidVehicleAndDriverTableRowCount").value = fdId;	
			//var fldValId=parseInt(fFieldId)+1;
			//alert(fldValId);
			





}
function setTestRowVehicleAndDriverStatus(id)
{
	if (confirmRemoveItem()) {
	
		document.getElementById("dStatus_"+id).value = document.getElementById("dIsFromDB_"+id).value;
		
		document.getElementById("dRow_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}
function getMaxRowId()
	{
		var cnt = 0;
		var rc = document.getElementById("hidVehicleAndDriverTableRowCount").value;
		for (i=0; i<rc; i++) {
			var status = document.getElementById("dStatus_"+i).value;
			if (status!='N') {
				//cnt++;
				cnt = i;
			}

		}
		return cnt;
	}
/*function addDriverRow(tableId,editProcurmentDriverId,DriverId,DriverName,mode)
{

	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
//alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "dRow_"+fdId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	//var cell3	= row.insertCell(2);
	
	cell1.id = "srNo_"+fdId;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	//cell3.className	= "listing-item"; cell3.align	= "center";

	//alert(DriverId);
		//alert("<?=$vehileTypeId?>");
	var driver	= "<select name='driverName_"+fdId+"' id='driverName_"+fdId+"' ><option value='0'>--Select--</option>";
	driver += "</select>";
	
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setTestRowDriverStatus('"+fdId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
	//var Qty = "<input name='Qty_"+fld+"' size='15' type='text' id='Qty_"+fld+"' value='"+harvestingChemicalQuantity+"'>";
	
	var hiddenFields = "<input name='dStatus_"+fdId+"' type='hidden' id='dStatus_"+fdId+"' value=''><input name='dIsFromDB_"+fdId+"' type='hidden' id='dIsFromDB_"+fdId+"' value='"+ds+"'><input type='hidden' name='editProcurmentDriverId_"+fdId+"' id='editProcurmentDriverId_"+fdId+"' value='"+editProcurmentDriverId+"'>";

	//cell1.innerHTML	= "<input name='test_"+fldId+"' type='text' id='test_"+fldId+"' value=\""+unescape(vehicleType)+"\" size='24'>";
	cell1.innerHTML	= driver;
	//cell2.innerHTML	= Qty;	
	cell2.innerHTML = imageButton+hiddenFields;	
	
	if(mode=="addmode")
	{
	xajax_rmProcurmentScheduleDriverDetails(document.getElementById('schedule_date').value,fdId,'','');
	}
	
	
	
	fdId		= parseInt(fdId)+1;	
	//document.getElementById("hidTestMethodTableRowCount").value = fldId;	
	document.getElementById("hidDriverTableRowCount").value = fdId;	






//code end
	
	
	
}

function setTestRowDriverStatus(id)
{
//alert('hai');
	if (confirmRemoveItem()) {
	
		document.getElementById("dStatus_"+id).value = document.getElementById("dIsFromDB_"+id).value;
		
		document.getElementById("dRow_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}


function addVehicleRow(tableId,editProcurmentVehicleId,VehicleId,VehicleNumber,mode)
{

	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "vRow_"+fieldvalue;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
		
	cell1.id = "srNo_"+fieldvalue;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
		var vehicle	= "<select name='vehicleNumber_"+fieldvalue+"' id='vehicleNumber_"+fieldvalue+"' ><option value='0'>--Select--</option>";
	
	
	vehicle += "</select>";
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setTestRowVehicleStatus('"+fieldvalue+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='vStatus_"+fieldvalue+"' type='hidden' id='vStatus_"+fieldvalue+"' value=''><input name='vIsFromDB_"+fieldvalue+"' type='hidden' id='vIsFromDB_"+fieldvalue+"' value='"+ds+"'><input type='hidden' name='editProcurmentVehicleId_"+fieldvalue+"' id='editProcurmentVehicleId_"+fieldvalue+"' value='"+editProcurmentVehicleId_+"'>";
	cell1.innerHTML	= vehicle;
	cell2.innerHTML = imageButton+hiddenFields;	
	if(mode=="addmode")
	{
	xajax_rmProcurmentScheduleVehicleDetails(document.getElementById('schedule_date').value,fieldvalue,'');
	}
	fieldvalue		= parseInt(fieldvalue)+1;	
	document.getElementById("hidVehicleTableRowCount").value = fieldvalue;	
	
	
//code end
	
}

function setTestRowVehicleStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("vStatus_"+id).value = document.getElementById("vIsFromDB_"+id).value;
		document.getElementById("vRow_"+id).style.display = 'none';
	}
	return false;
}
*/






function addNewRow(tableId,EquipmentId,harvestingEquipmentName,harvestingEquipmentQuantity)
{

var tbl		= document.getElementById(tableId);

	var lastRow	= tbl.rows.length;
	// alert(lastRow);
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
	
	var EquipmentQty = "<input name='harvestingQty_"+fldId+"' type='text' id='harvestingQty_"+fldId+"' size='15' value='"+harvestingEquipmentQuantity+"'>";
	
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

function setTestRowItemStatusVal(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("Status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		//alert('hai');
		document.getElementById("Row_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}

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
	//alert(harvestingChemicalName);
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
	
	var Qty = "<input name='Qty_"+fld+"' size='15' type='text' id='Qty_"+fld+"' value='"+harvestingChemicalQuantity+"'>";
	
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
//alert('hai');
	if (confirmRemoveItem()) {
	
		document.getElementById("bStatus_"+id).value = document.getElementById("bIsFromDB_"+id).value;
		
		document.getElementById("bRow_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}




/*function addNewRMProcurmentChemicalItemRow(tableId,editProcurmentId, vehicle,chemicalName,chemicalQty,chemicalIssued,mode)
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
	//var cell6			= row.insertCell(5);
	//var cell7			= row.insertCell(6);
	//var cell8			= row.insertCell(7);
	//var cell9			= row.insertCell(8);
	//var cell10			= row.insertCell(9);

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	//cell5.className	=	"fieldName"; cell5.align = "center";
	//cell6.className	=	"fieldName"; cell6.align = "center";
	//cell7.className	=	"fieldName"; cell7.align = "center";
	//cell8.className	=	"fieldName"; cell8.align = "center";
	//cell9.className	=	"fieldName"; cell9.align = "center";
	//cell10.className	=	"fieldName"; cell10.align = "center";
	//cell11.className	=	"fieldName"; cell11.align = "center";
	
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
*/




// Balance Qty
/*function balanceQty()
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
}*/

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
	
	
	
	var vd = document.getElementById("hidVehicleAndDriverTableRowCount").value;
	var prevOrders = 0;
	
	var arry = new Array();
	var arriy=0;
	for( l=0; l<vd; l++ )	{
	    var status = document.getElementById("dStatus_"+l).value;
	    if (status!='N') 
	    {
		var dv = document.getElementById("driverName_"+l).value;	
		if ( arry.indexOf(dv) != -1 )	{
			alert("Driver Name  Cannot be duplicate.");
			document.getElementById("driverName_"+l).focus();
			return false;
		}
		arry[arriy++]=dv;
            }
	}
	
	
	
	
	
	
	
	
	

	var sc = document.getElementById("hidSupplierRowCount").value;
	
	
	var arra = new Array();
	var arrGP = new Array();
	var arrk=0;
	for( j=0; j<sc; j++ )	{
	    var status = document.getElementById("sstatus_"+j).value;
	    if (status!='N') 
	    {
			var rv = document.getElementById("pondName_"+j).value;
			//alert(rv);
			if(rv!="" || rv!="0")
			{			
			if ( arra.indexOf(rv) != -1 )	{
				alert("Pond Name  Cannot be duplicate.");
				document.getElementById("pondName_"+j).focus();
				return false;
			}
				
			arra[arrk++]=rv;
			}
			var sg = document.getElementById("supplierGroup_"+j).value;
			arrGP[j] = sg;
			//alert( >0);
			
		if ( arrGP.indexOf(sg) > 0 )	{
			alert("Supplier Group  must be same.");
			document.getElementById("supplierGroup_"+j).focus();
			return false;
		}
		
            }
	}
	
//return false;

	
	var rc = document.getElementById("hidHarvestingEquipmentsTableRowCount").value;
	var prevOrder = 0;
	
	var arr = new Array();
	var arri=0;
	for( j=0; j<rc; j++ )	{
	    var status = document.getElementById("Status_"+j).value;
	    if (status!='N') 
	    {
		var rv = document.getElementById("harvestingEquipment_"+j).value;	
		if ( arr.indexOf(rv) != -1 )	{
			alert("Equipment  Cannot be duplicate.");
			document.getElementById("harvestingEquipment_"+j).focus();
			return false;
		}
		arr[arri++]=rv;
            }
	}
	
	var ch = document.getElementById("hidHarvestingChemicalTableRowCount").value;
	var ar = new Array();
	var arrl=0;
	for( j=0; j<ch; j++ )	{
	    var status = document.getElementById("bStatus_"+j).value;
	    if (status!='N') 
	    {
		var rv = document.getElementById("harvestingChemical_"+j).value;	
		if ( ar.indexOf(rv) != -1 )	{
			alert("Chemical Cannot be duplicate.");
			document.getElementById("harvestingChemical_"+j).focus();
			return false;
		}
		ar[arrl++]=rv;
            }
	}
	
	return true;
}
		
function supplierGroupExist($supplierGroupSize)
{
//alert($supplierGroupSize);
	if($supplierGroupSize>0)
	{
	supplierGroup=0; 
	}
	else
	{
	supplierGroup=1; 
	}
}
/*function validateRepeatIssuance()
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
}*/


function confirmGenerate(form,prefix,rowcount)
{
	var rowCount	=	rowcount;
	var fieldPrefix	=	prefix;
	
	// alert(isAnyCheckedgenerate(rowCount,fieldPrefix));
	var returnVal = isAnyCheckedgenerate(rowCount,fieldPrefix);
	if(returnVal == false)
	{
		alert("Please select a record to generate.");
		return false;
	}
	else if(returnVal == '1')
	{
		return false;
	}
	else
	{
		window.location='RMProcurmentGatePass.php?procurementId='+returnVal;
	}
	// if(!isAnyCheckedgenerate(rowCount,fieldPrefix))
	// {
		// alert("Please select a record to generate.");
		// return false;
	// }

	return false;
}




function isAnyCheckedgenerate(rowCount,fieldPrefix)
{
	var procurementNo = '';var procurementId = 0;
	for ( i=1; i<=rowCount; i++ )
	{
		if(document.getElementById(fieldPrefix+i).checked == true)
		{
			var checkVal=document.getElementById(fieldPrefix+i).value;
			//input = Base64.encode('erg');
			//alert(input);
				generatedCount = document.getElementById('generated_count_'+i).value;
				if(generatedCount > 0)
				{
					var procurementNo = document.getElementById('procurementNo_'+i).value;
				}
				if(procurementId=="0")
				{
					procurementId=Base64.encode(checkVal);
				}
				else
				{
					procurementId+=','+Base64.encode(checkVal);
				}
				//return true;
		}
		//return true;
		
		
	}
	if(procurementId=="0")
	{
		return false;
	}
	else if(procurementNo != '')
	{
		alert('Gate pass already generated for '+procurementNo+'. Please choose another one.');
		return '1';
	}
	else
	{
		// window.location='RMProcurmentGatePass.php?procurementId='+procurementId;
		return procurementId;
	}
	//alert(procurementId);
		// return false;
	
}
function getDriverArr(json, tableRowCount)
{		
		
		var myObjects = eval('(' + json + ')');
//alert(myObjects);
		for (var i=0; i<tableRowCount; i++) {
			document.getElementById("driverName_"+i).length=0;	
			for (var keys in myObjects) {	
//alert(keys);			
				addDropDownList("driverValId_"+i,"driverName_"+i,keys,myObjects[keys]);
				
			}
		}
}
	
function getVehicleArr(json, tableRowCount)	
{				
		var myObject = eval('(' + json + ')');

		for (var i=0; i<tableRowCount; i++) {
			document.getElementById("vehicleNumber_"+i).length=0;	
			for (var key in myObject) {				
				addDropDownList("vehicleValId_"+i,"vehicleNumber_"+i,key,myObject[key]);
				
			}
		}
}


function CheckSchedule(mode,tableRowCount)
{
//alert(tableRowCount);
var schedule_date=document.getElementById('schedule_date').value;
var hide_schedule_date=document.getElementById('hide_schedule_date').value;
var hidTableRowCountedit=document.getElementById('hidTableRowCountedit').value;
//alert(hidTableRowCountedit);
//alert(schedule_date+hide_schedule_date);
	if(schedule_date==hide_schedule_date)
	{
		xajax_rmProcurmentScheduleDriverAndVehicleDetails(document.getElementById('schedule_date').value,'0','','',document.getElementById('hidProcurmentId').value,document.getElementById('hidVehicleAndDriverTableRowCount').value,mode);
		
		if(hidTableRowCountedit>1) 
		{
			for(i=1; i<hidTableRowCountedit; i++)
			{
			//alert(i);
				document.getElementById("dStatus_"+i).value = '';
		
			document.getElementById("dRow_"+i).style.display = '';
			}
		}	
	
	}
	else
	{
	//alert("hii");
		xajax_rmProcurmentScheduleDriverAndVehicleDetails(document.getElementById('schedule_date').value,'0','','','',document.getElementById('hidVehicleAndDriverTableRowCount').value,mode);
		//alert("hui");
		if(tableRowCount>1) 
		{
			for(i=1; i<tableRowCount; i++)
			{
				document.getElementById("dStatus_"+i).value = document.getElementById("dIsFromDB_"+i).value;
		
			document.getElementById("dRow_"+i).style.display = 'none';
			}
		}		
		
		
		
		
	}
}


function confirmSaveEquipment()
{
	var equipvalue='';
	var itemCount	=	document.getElementById("hidHarvestingEquipmentsTableRowCount").value;

		var count = 0;
		for (i=0; i<itemCount; i++)
		{
			var status = document.getElementById("Status_"+i).value;		    
	    	if (status!='N') 
		    {
			//var equipment	=	document.getElementById("harvestingEquipment_"+i);
				var equipmentName		=	document.getElementById("harvestingEquipment_"+i);
				
				//alert(equipmentQty);
				if( equipmentName.value == "" || equipmentName.value == "0"  )
				{
					equipvalue=0;
				}
				else
				{			
					equipvalue=1;
				}
			} 
			else 
			{
				count++;
			}
	 }

	 var chemicalvalue='';
	 var chemicalCount	=	document.getElementById("hidHarvestingChemicalTableRowCount").value;

		var ccount = 0;
		for (i=0; i<chemicalCount; i++)
		{
		   var status = document.getElementById("bStatus_"+i).value;		    
	    	if (status!='N') 
		    {
				var chemicalName		=	document.getElementById("harvestingChemical_"+i);
				if( chemicalName.value == ""  || chemicalName.value == "0")
				{
					chemicalvalue=0;
				}
				else
				{
					chemicalvalue=1;
				}			
			} 
			else 
			{
				ccount++;
			}
	 }
	 
	 
	if(equipvalue==0 && chemicalvalue==0)
	{
	var saveMsg	=	"Do you wish to save the changes with out equipment and chemical?";
	if(confirm(saveMsg))
	{
		return true;
	}
	return false;
	}
	else
	{
	return true;
	}
	
}