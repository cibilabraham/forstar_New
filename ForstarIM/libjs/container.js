function validateAddContainer(form, confirmed)
{	
	var itemSelected = false;
	var selectDate		=	form.selectDate.value;
	var shippingLine	=	form.shippingLine.value;
	var containerNo		=	form.containerNo.value;
	var sealNo		=	form.sealNo.value;
	var sailingDate		=	form.sailingDate.value;
	var expectedDate	=	form.expectedDate.value;	
	var checkSelect		=	false;	
	var selGrade		=	"gradeEntryId_";
	
	if (selectDate=="" ) {
		alert("Please Select a Date.");
		form.selectDate.focus();
		return false;
	}
	
	if (shippingLine=="") {
		alert("Please select Shipping Line.");
		form.shippingLine.focus();
		return false;
	}
	
	if (confirmed && containerNo=="") {
		alert("Please enter Container Number.");
		form.containerNo.focus();
		return false;
	}
	
	if (confirmed && sealNo=="") {
		alert("Please enter Seal no.");
		form.sealNo.focus();
		return false;
	}
	
	if (confirmed && sailingDate=="") {
		alert("Please select a sailing date.");
		form.sailingDate.focus();
		return false;
	}
	
	if (confirmed && expectedDate=="") {
		alert("Please select a Expected date.");
		form.expectedDate.focus();
		return false;
	}	
	
	if (confirmed) {
		var itemCount	=	document.getElementById("hidTableRowCount").value;
	
		for (i=0; i<itemCount; i++) {
		var rowStatus = document.getElementById("status_"+i).value;	
			if (rowStatus!='N') {
				var selInvoiceId	= document.getElementById("selInvoiceId_"+i);
				
				if (selInvoiceId.value=="") {
					alert("Please select a Invoice.");
					selInvoiceId.focus();
					return false;
				}			
	
				if (selInvoiceId.value!="") {	
					itemSelected = true;
				}
			}
		} // Loop Ends here	
		
		if (!itemSelected) {
			alert("Please select atleast one Invoice");
			return false;
		}
	}
	
	if (!validateItemRepeat()) {
		return false;
	}
	
	if (!confirmSave()) return false;
	else return true;
}

function validateContainerSearch(form)
{
	var selectFrom	=	form.selectFrom.value;
	var selectTill	=	form.selectTill.value;
	
	if (selectFrom=="") {
		alert("Please select From Date.");
		form.selectFrom.focus();
		return false;
	}
	
	if (selectTill=="") {
		alert("Please select Till Date.");
		form.selectTill.focus();
		return false;
	}
	return true;
}

	function enableSPOButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
			document.getElementById("cmdAddNewContainer").disabled = false;
			document.getElementById("cmdAddNewContainer1").disabled = false;			
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
			document.getElementById("cmdSaveAndConfirm").disabled = false;
		}
	}
	
	function disableSPOButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
			document.getElementById("cmdAddNewContainer").disabled = true;
			document.getElementById("cmdAddNewContainer1").disabled = true;	
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
			document.getElementById("cmdSaveAndConfirm").disabled = true;
			
		}
	}

//ADD MULTIPLE Item- ADD ROW START
function addNewPOItem(tableId, poEntryId, selInvoiceId)
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

	cell1.id = "srNo_"+fieldId;	
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";


	var selectFish	= "<select name='selInvoiceId_"+fieldId+"' id='selInvoiceId_"+fieldId+"'>";
	//var selectFish	= "<option value=''>--Select--</option>";
	<?php
		if (sizeof($purchaseOrderRecords)>0) {	
			foreach($purchaseOrderRecords as $poId=>$poTxt) {
				
	?>	
		if (selInvoiceId== "<?=$poId?>")  var sel = "Selected";
		else var sel = "";

	selectFish += "<option value=\"<?=$poId?>\" "+sel+"><?=$poTxt?></option>";	
	<?php
			}
		}
	?>
	selectFish += "</select>";

	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setPOItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='poEntryId_"+fieldId+"' type='hidden' id='poEntryId_"+fieldId+"' value='"+poEntryId+"'>";	
	
	cell1.innerHTML	= "";//(fieldId+1);
	cell2.innerHTML	= selectFish;
	cell3.innerHTML = imageButton+hiddenFields;

	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;	
	assignSrNo();
}
	

	function setPOItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
			assignSrNo();
		}
		return false;
	}

	function assignSrNo()
	{
		var itemCount	=	document.getElementById("hidTableRowCount").value;

		var j = 0;
		for (i=0; i<itemCount; i++) {
			var sStatus = document.getElementById("status_"+i).value;	
			if (sStatus!='N') {
				j++;	
				document.getElementById("srNo_"+i).innerHTML = j;
			}
		}
	}


	// ------------------------------------------------------
	// Duplication check starts here
	// ------------------------------------------------------	
	function validateItemRepeat()
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
	
		for (i=0; i<rc; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var selInvoiceId	= document.getElementById("selInvoiceId_"+i).value;					
				var addVal = selInvoiceId;
				
				if (pArr.indexOf(addVal)!=-1) {
					alert(" Invoice cannot be duplicate.");
					document.getElementById("selInvoiceId_"+i).focus();
					return false;	
				}
							
				pArr[pa++]	= addVal;
			}
		}	
		return true;
	}	
	// ------------------------------------------------------
	// Duplication check Ends here
	// ------------------------------------------------------


