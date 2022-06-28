function validateCompanyDetails(form)
{
	var companyName		=	form.companyName.value;
	var companyAddress		=	form.companyAddress.value;
	var companyPlace		=	form.companyPlace.value;
	var companyPinCode		=	form.companyPinCode.value;
	var companyCountry 		=	form.companyCountry.value;
	var companyTelNo		=	form.companyTelNo.value;
	
	if (companyName=="") {
		alert("Please enter a Name.");
		form.companyName.focus();
		return false;
	}

	if (companyAddress=="") {
		alert("Please enter Address.");
		form.companyAddress.focus();
		return false;
	}

	if (companyPlace=="") {
		alert("Please enter a Place.");
		form.companyPlace.focus();
		return false;
	}

	if (companyPinCode=="") {
		alert("Please enter Pincode.");
		form.companyPinCode.focus();
		return false;
	}

	if (companyCountry=="") {
		alert("Please enter Country.");
		form.companyCountry.focus();
		return false;
	}

	if (companyTelNo=="") {
		alert("Please enter Tel.No.");
		form.companyTelNo.focus();
		return false;
	}	
	// Checking Phone number
	if (!checkInternationalPhone(companyTelNo)){
		alert("Please Enter a Valid Phone Number");
		//form.supplierTelNo.value="";
		form.companyTelNo.focus();
		return false;
	}

	if (!confirmSave()) return false;	
	return true;
}



//ADD MULTIPLE Item- ADD ROW START
	function addNewCOBankAC(tableId)
	{
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
		var cell5	= row.insertCell(4);
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		cell4.className	= "listing-item"; cell4.align	= "center";
		cell5.className	= "listing-item"; cell5.align	= "center";
				
		
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setCOBankACItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='bankACEntryId_"+fieldId+"' type='hidden' id='bankACEntryId_"+fieldId+"' value=''>";	
		
		cell1.innerHTML	= "<input type='text' name='accountNo_"+fieldId+"' id='accountNo_"+fieldId+"' size='24' autocomplete='off'>";		
		cell2.innerHTML	= "<input type='text' name='bankName_"+fieldId+"' id='bankName_"+fieldId+"' size='24'>";		
		cell3.innerHTML	= "<textarea name='bankAddr_"+fieldId+"' id='bankAddr_"+fieldId+"' rows='4' col='4'></textarea>";
		cell4.innerHTML	= "<input type='text' name='bankADCode_"+fieldId+"' id='bankADCode_"+fieldId+"' size='24'>";		
		cell5.innerHTML = imageButton+hiddenFields;	
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;				
	}

	function setCOBankACItemStatus(id)
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
	function validateBankACRepeat()
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
		var prevOrder = 0;
		var arr = new Array();
		var arri=0;

		for (j=0; j<rc; j++) {
			var status = document.getElementById("status_"+j).value;
			if (status!='N') {
				var rv = document.getElementById("bankName_"+j).value;
				
				if ( arr.indexOf(rv) != -1 )    {
					alert("Please make sure the bank ac is not duplicate.");					
					document.getElementById("bankName_"+j).focus();
					return false;
				}		
				arr[arri++]=rv;
			}
		}
		return true;
	}