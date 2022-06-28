function validateAddDriverMaster(form)
{		
	var name	= form.name.value;
	 var permanentAddress	= form.permanentAddress.value;
	 var presentAddress	= form.presentAddress.value;
	 var telephoneNo	= form.telephoneNo.value;
	 var mobileNo	= form.mobileNo.value;
	 var drivingLicenceNo	= form.drivingLicenceNo.value;
	  var licenceExpiryDate	= form.licenceExpiryDate.value;
	 
		
	if (name=="") {
		alert("Please enter a  Name.");
		form.name.focus();
		return false;
	}

	 if (permanentAddress=="") {
		 alert("Please enter  Permanent address.");
		 form.permanentAddress.focus();
		 return false;
	 }
	 if (presentAddress=="") {
		 alert("Please enter  present address.");
		 form.presentAddress.focus();
		 return false;
	 }
	 if (permanentAddress=="") {
		 alert("Please enter  Permanent address.");
		 form.permanentAddress.focus();
		 return false;
	 }
	 if (telephoneNo=="") {
		 alert("Please enter  telephone No.");
		 form.telephoneNo.focus();
		 return false;
	 }
	  if (mobileNo=="") {
		 alert("Please enter  mobile No.");
		 form.mobileNo.focus();
		 return false;
	 }
	 if (mobileNo=="") {
		 alert("Please enter  mobile No.");
		 form.mobileNo.focus();
		 return false;
	 }
	  if (drivingLicenceNo=="") {
		 alert("Please enter  drivingLicence No.");
		 form.drivingLicenceNo.focus();
		 return false;
	 }
	  if (licenceExpiryDate=="") {
		 alert("Please enter  licenceExpiry Date.");
		 form.licenceExpiryDate.focus();
		 return false;
	 }
	 

	
	if (!validateVehicleMasterRepeat()) {
		return false;
	}
	
	if (!confirmSave()) return false;	
	return true;
}

// ADD MULTIPLE Item- ADD ROW START
function addNewRow(tableId,vehicleTypeId,vehicleType)
{

var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	// alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "bRow_"+fldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	
	cell1.id = "srNo_"+fldId;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";

		//alert("entered");
		//alert("<?=$vehileTypeId?>");
		var vehicle	= "<select name='vehicleType_"+fldId+"' id='vehicleType_"+fldId+"' ><option value='0'>--Select--</option>";
	<?php
		if (sizeof($declarVehicleTypeRecords)>0) {	
			foreach ($declarVehicleTypeRecords as $dcw) {
						$vehileTypeId = $dcw[0];
						$vehicleType	= stripSlash($dcw[1]);
						
	?>	
	
		if (vehicleType=="<?=$vehileTypeId?>")  var sel = "Selected";
		else var sel = "";

	vehicle += "<option value=\"<?=$vehileTypeId?>\" "+sel+"><?=$vehicleType?></option>";	
	<?php
			}
		}
		
	?>	
	vehicle += "</select>";
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setTestRowItemStatus('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='bStatus_"+fldId+"' type='hidden' id='bStatus_"+fldId+"' value=''><input name='bIsFromDB_"+fldId+"' type='hidden' id='bIsFromDB_"+fldId+"' value='"+ds+"'><input type='hidden' name='vehicleTypeId_"+fldId+"' id='vehicleTypeId_"+fldId+"' value='"+vehicleTypeId+"'>";

	//cell1.innerHTML	= "<input name='test_"+fldId+"' type='text' id='test_"+fldId+"' value=\""+unescape(vehicleType)+"\" size='24'>";
	cell1.innerHTML	= vehicle;
	cell2.innerHTML = imageButton+hiddenFields;	
	
	fldId		= parseInt(fldId)+1;	
	//document.getElementById("hidTestMethodTableRowCount").value = fldId;	
	document.getElementById("hidVehicleTypeTableRowCount").value = fldId;	






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
function getPackValue(i){



}

// ------------------------------------------------------
// Duplication check starts here
// ------------------------------------------------------
var cArr = new Array();
var cArri = 0;	
function validateVehicleMasterRepeat()
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
	
	var rc = document.getElementById("hidVehicleTypeTableRowCount").value;
	var pArr	= new Array();	
	var pa		= 0;
	for (j=0; j<rc; j++) {
		var status = document.getElementById("bStatus_"+j).value;
		if (status!='N') {
			var vehicleType = document.getElementById("vehicleType_"+j).value;
					
			if (pArr.indexOf(vehicleType)!=-1) {
				alert("vehicle Type cannot be duplicate.");
				document.getElementById("vehicleType_"+j).focus();
				return false;	
			}						
			pArr[pa++]	= vehicleType;					
		}
	}	
	return true;
}