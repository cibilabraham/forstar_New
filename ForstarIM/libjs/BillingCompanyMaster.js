function validateBillingCompanyMaster(form)
{
	var companyName		=	form.companyName.value;
	var address		=	form.address.value;
	var place		=	form.place.value;
	var pinCode		=	form.pinCode.value;
	var country 		=	form.country.value;
	/*var telNo		=	form.telNo.value;*/

	//var challanNumberFrom		= form.challanNumberFrom.value;
	//var challanNumberTo		= form.challanNumberTo.value;	
	//var challanDEntryLimitDays	= form.challanDEntryLimitDays.value;
	var alphaCode			= form.alphaCode.value;
	var displayName			= form.displayName.value;
	var vatTin		=	form.vatTin.value;
	var cstTin		=	form.cstTin.value;
	var range		=	form.range.value;
	var division		=	form.division.value;
	var commissionerate 		=	form.commissionerate.value;
	var exciseNo		=	form.exciseNo.value;
	var notificationDetails 		=	form.notificationDetails.value;
	var panNo		=	form.panNo.value;
	
	if (companyName=="") {
		alert("Please enter a Name.");
		form.companyName.focus();
		return false;
	}

	if (address=="") {
		alert("Please enter Address.");
		form.address.focus();
		return false;
	}

	if (place=="") {
		alert("Please enter a Place.");
		form.place.focus();
		return false;
	}

	if (pinCode=="") {
		alert("Please enter Pincode.");
		form.pinCode.focus();
		return false;
	}

	if (country=="") {
		alert("Please enter Country.");
		form.country.focus();
		return false;
	}

	/*if (telNo=="") {
		alert("Please enter Tel.No.");
		form.telNo.focus();
		return false;
	}*/	
	// Checking Phone number
	/*if (!checkInternationalPhone(telNo)){
		alert("Please enter a valid phone number");		
		form.telNo.focus();
		return false;
	}*/

	if (alphaCode=="") {
		alert("Please enter an alpha code.");
		form.alphaCode.focus();
		return false;
	}

	if (displayName=="") {
		alert("Please enter a display name.");
		form.displayName.focus();
		return false;
	}
	
	/*
	if (vatTin=="") {
		alert("Please enter VAT TIN.");
		form.vatTin.focus();
		return false;
	}

	if (cstTin=="") {
		alert("Please enter cst Tin.");
		form.cstTin.focus();
		return false;
	}

	if (range=="") {
		alert("Please enter a range.");
		form.range.focus();
		return false;
	}

	if (division=="") {
		alert("Please enter division.");
		form.division.focus();
		return false;
	}

	if (commissionerate=="") {
		alert("Please enter commissionerate.");
		form.commissionerate.focus();
		return false;
	}

	if (exciseNo=="") {
		alert("Please enter excise No.");
		form.exciseNo.focus();
		return false;
	}
		
	if (notificationDetails=="") {
	alert("Please enter notificationDetails.");
	form.notificationDetails.focus();
	return false;
	}

	if (panNo=="") {
		alert("Please enter pan No.");
		form.panNo.focus();
		return false;
	}

	var rowCount	= document.getElementById("hidTableRowCount").value;
	if (rowCount>0) {
		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var bankName 	= document.getElementById("bankName_"+i);
				var accountNo 	= document.getElementById("accountNo_"+i);

				if (bankName.value!="" && accountNo.value=="") {
					alert("Please enter a bank account no.");
					accountNo.focus();
					return false;
				}

				if (bankName.value=="" && accountNo.value!="") {
					alert("Please enter a bank name.");
					bankName.focus();
					return false;
				}
			}
		}
	}
	
	var telephoneExist='0'; var mobilephoneExist='0';
	var rowCountContact	= document.getElementById("hidTableRowCountContact").value;
	if (rowCountContact>0) {
		for (i=0; i<rowCountContact; i++) {
			var status = document.getElementById("cstatus_"+i).value;
			if (status!='N') {
				var telephoneNo 	= document.getElementById("telephoneNo_"+i);
				var mobileNo 	= document.getElementById("mobileNo_"+i);
				var email 	= document.getElementById("email_"+i);
				//alert(telephoneNo);
				if(telephoneNo.value!='')
				{
					var telNo=telephoneNo.value;
					if (!checkInternationalPhone(telNo)){
						alert("Please enter a valid phone number");		
						//form.telNo.focus();
						document.getElementById("telephoneNo_"+i).focus();
						return false;
					}
					else
					{
						telephoneExist='1';
					}
				}
				if(mobileNo.value!='')
				{
					mobilephoneExist='1';
				}
				
				if(email.value!='')
				{
					if(!checkemail(email.value)) {
						document.getElementById("email_"+i).focus();
						 return false;
					}
				}

			}
		}
	}

*/

	
	/*
	if (parseInt(challanNumberFrom)>parseInt(challanNumberTo)) {
		alert("Please make sure Challan No. From should be less than Challan No. To.");
		return false;
	}

	if (!isInteger(challanDEntryLimitDays))
	{
		alert("Please enter challan Delayed entry limit days.");
		form.challanDEntryLimitDays.focus();
		return false;
	}*/

/*
	if(telephoneExist=='0')
	{
		alert("Must enter atleast one Telephone NO");
		return false;
	}
	if(mobilephoneExist=='0')
	{
		alert("Must enter atleast one mobile No ");
		return false;
	}
	if(!validateBankACRepeat()){
		return false;
	}
*/
	
	if (!confirmSave()) return false;	
	else return true;
	
//validateBankACRepeat()

}

	// Confirm Make Defaut
	function confirmMakeDefault(fieldPrefix, rowCount)
	{
		var count = 0;
		for (i=1; i<=rowCount; i++ ) {
			if (document.getElementById(fieldPrefix+i).checked) {
				count++;
			}		
		}
		
		if (count==0) {
			alert("Please select a record to make Default.");
			return false;
		}
		
		if (count>1) {
			alert("Please select only one record to make Default.");
			return false;
		}		
		return true;
	}

	//ADD MULTIPLE Item- ADD ROW START
	function addNewBankAC(tableId)
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
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		cell4.className	= "listing-item"; cell4.align	= "center";
				
		
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setBankACItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='bankACEntryId_"+fieldId+"' type='hidden' id='bankACEntryId_"+fieldId+"' value=''>";	
				
		cell1.innerHTML	= "<input type='text' name='bankName_"+fieldId+"' id='bankName_"+fieldId+"' size='24'>";	
		cell2.innerHTML	= "<input type='text' name='accountNo_"+fieldId+"' id='accountNo_"+fieldId+"' size='24' autocomplete='off'>";
		cell3.innerHTML	= "<input type='checkbox' name='defaultAC_"+fieldId+"' id='defaultAC_"+fieldId+"' value='Y' class='chkBox' onclick=\"cpnyDefaultAcChk('"+fieldId+"');\">";
		cell4.innerHTML = imageButton+hiddenFields;	
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;				
	}

	function setBankACItemStatus(id)
	{
		if (confirmRemoveItem()) {			
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
		}
		return false;
	}

		//ADD MULTIPLE Item- ADD ROW START
	function addNewContacts(tableId)
	{
		var tbl		= document.getElementById(tableId);	
		var lastRow	= tbl.rows.length;	
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "crow_"+fieldvalue;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);		
		var cell4	= row.insertCell(3);
		var cell5	= row.insertCell(4);
		var cell6	= row.insertCell(5);
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		cell4.className	= "listing-item"; cell4.align	= "center";
		cell5.className	= "listing-item"; cell5.align	= "center";
		cell6.className	= "listing-item"; cell6.align	= "center";
		
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setContactItemStatus('"+fieldvalue+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		var hiddenFields = "<input name='cstatus_"+fieldvalue+"' type='hidden' id='cstatus_"+fieldvalue+"' value=''><input name='cIsFromDB_"+fieldvalue+"' type='hidden' id='cIsFromDB_"+fieldvalue+"' value='"+ds+"'>";	
		cell1.innerHTML	= "<input type='text' name='telephoneNo_"+fieldvalue+"' id='telephoneNo_"+fieldvalue+"' size='16'>";	
		cell2.innerHTML	= "<input type='text' name='mobileNo_"+fieldvalue+"' id='mobileNo_"+fieldvalue+"' size='16' autocomplete='off'>";
		cell3.innerHTML	= "<input type='text' name='fax_"+fieldvalue+"' id='fax_"+fieldvalue+"' size='16' autocomplete='off'>";
		cell4.innerHTML	= "<input type='text' name='email_"+fieldvalue+"' id='email_"+fieldvalue+"' size='24' autocomplete='off'>";
		cell5.innerHTML	= "<input type='checkbox' name='defaultCD_"+fieldvalue+"' id='defaultCD_"+fieldvalue+"' value='Y' class='chkBox' onclick=\"checkDefaultContact('"+fieldvalue+"');\">";
		cell6.innerHTML = imageButton+hiddenFields;	
		
		fieldvalue		= parseInt(fieldvalue)+1;	
		document.getElementById("hidTableRowCountContact").value = fieldvalue;				
	}

	function setContactItemStatus(id)
	{
		if (confirmRemoveItem()) {			
			document.getElementById("cstatus_"+id).value = document.getElementById("cIsFromDB_"+id).value;
			document.getElementById("crow_"+id).style.display = 'none';
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
		
		var rcc = document.getElementById("hidTableRowCountContact").value;
		//var prevOrder = 0;
		var arra = new Array();
		var arrai=0;

		for (i=0; i<rcc; i++) {
			var status = document.getElementById("cstatus_"+i).value;
			if (status!='N') {
				var tn = document.getElementById("telephoneNo_"+i).value;
				var mbn = document.getElementById("mobileNo_"+i).value;
					if ( arra.indexOf(tn) != -1 )    {
					alert("Please make sure the telephone no is not duplicate.");					
					document.getElementById("telephoneNo_"+i).focus();
					return false;
				}	
				if ( arra.indexOf(mbn) != -1 )    {
					alert("Please make sure the mobile no is not duplicate.");					
					document.getElementById("mobileNo_"+i).focus();
					return false;
				}	

				arra[arrai++]=tn;
				arra[arrai++]=mbn;
			}
		}

		
		var hdc = document.getElementById("hidTableRowCountContact").value;
		//var prevOrder = 0;
		var arr = new Array();
		var arri=0;

		for (i=0; i<hdc; i++) {
			var status = document.getElementById("cstatus_"+i).value;
			if (status!='N') {
				var fx = document.getElementById("fax_"+i).value;
				var eml = document.getElementById("email_"+i).value;
					if(fx!='')
					{
						if ( arr.indexOf(fx) != -1 )    {
							alert("Please make sure the fax is not duplicate.");					
							document.getElementById("fax_"+i).focus();
							return false;
						}
					}
					if(eml!='')
					{
						if ( arr.indexOf(eml) != -1 )    {
							alert("Please make sure the email is not duplicate.");					
							document.getElementById("email_"+i).focus();
							return false;
						}	
					}
				arr[arri++]=fx;
				arr[arri++]=eml;
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

	function cpnyDefaultAcChk(rowId)
	{	
		if (!document.getElementById("defaultAC_"+rowId).checked) chk = false;
		else chk = true;	
		var rc = document.getElementById("hidTableRowCount").value;
		for (j=0; j<rc; j++) {
			document.getElementById("defaultAC_"+j).checked = false;
		}
		document.getElementById("defaultAC_"+rowId).checked = chk;
	}

	function validateCompanyStatus(companyId, rowId)
	{
		if (!confirm("Do you wish to change Company status?")) {
			return false;
		}
		// Ajax 
		xajax_changeCompanyStatus(companyId, rowId);
		//xajax_updateCompanyStatus(companyId, rowId);
			
		
		return true;
	}
	function checkDefaultContact(id)
	{
		//alert(id);
		var cnt='0';
		var rcc = document.getElementById("hidTableRowCountContact").value;
		for(i=0; i<rcc; i++)
		{
			//alert(i);
			if(document.getElementById("defaultCD_"+i).checked)
			{
				cnt++;
			}
		}
		//alert(cnt);
		if(parseInt(cnt)>2)
		{
			alert("Exceeds maximum limit of default");
			document.getElementById("defaultCD_"+id).checked=false;
		}
	}