function validateRetailCounterStock(form)
{
	var productSelected	= false;
	var selDate		= form.selDate.value;	
	var selDistributor	= form.selDistributor.value;	
	var selRetailCounter	= form.selRetailCounter.value;
	var editMode		= form.editMode.value;
	
	if (selDate=="") {
		alert("Please select a date.");
		form.selDate.focus();
		return false;
	}

	if (selDistributor=="") {
		alert("Please select a Distributor.");
		form.selDistributor.focus();
		return false;
	}

	if (selRetailCounter=="") {
		alert("Please select a Retail Counter.");
		form.selRetailCounter.focus();
		return false;
	}

	var itemCount	=	document.getElementById("hidTableRowCount").value;

	for (i=0; i<itemCount; i++) {
		
		var selProduct	  =	document.getElementById("selProduct_"+i);
		var availableQty  =	document.getElementById("availableQty_"+i);
		var usedQty	  =	document.getElementById("usedQty_"+i);
					
		var status = document.getElementById("status_"+i).value;		
			if (status!='N') {	
				if (selProduct.value == "") {
					alert("Please select a Product.");
					selProduct.focus();
					return false;
				}
	
				if (availableQty.value == "") {
					alert("Please enter available Quantity.");
					availableQty.focus();
					return false;
				}
				if (usedQty.value == "") {
					alert("Please enter used Quantity.");
					usedQty.focus();
					return false;
				}
				if (selProduct.value!="") productSelected = true;
			}
	}
	if (!productSelected) {
		alert("Please select atleast one Product");
		return false;
	}	

	if (!validateRetailCounterProductRepeat()) {
		return false;
	}

	if (!confirmSave()) {
		return false;
	}
	return true;
}

//Validate repeated
function validateRetailCounterProductRepeat()
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

    for( j=0; j<rc; j++ )    {
        var rv = document.getElementById("selProduct_"+j).value;
        if ( arr.indexOf(rv) != -1 )    {
            alert("Please make sure the selected product is not duplicate.");
            document.getElementById("selProduct_"+j).focus();
            return false;
        }
        arr[arri++]=rv;
    }
    return true;
}

//Add a New Line 
function retailCounterStockRecNewLine()
{
	document.frmRetailCounterStock.newline.value = '1';
	document.frmRetailCounterStock.submit();
}

// Find the total Amount
function balanceRetailCounterStock()
{
	var rowCount = document.getElementById("hidTableRowCount").value;	
	var totalAmount = 0;
	
	var calcBalanceQty = 0;
	for (i=0; i<rowCount; i++) {
		var availableQty = 0;
		var usedQty = 0;
		var selProduct = document.getElementById("selProduct_"+i).value;
		if (document.getElementById("availableQty_"+i).value!="") {
			availableQty = parseFloat(document.getElementById("availableQty_"+i).value);
		}
		if (document.getElementById("usedQty_"+i).value!="") {
			usedQty = parseFloat(document.getElementById("usedQty_"+i).value);
		}
		
		if (selProduct!="") {
			calcBalanceQty = availableQty-usedQty;	// Find Each Row Amount		
		} else {
			calcBalanceQty = 0;
		}
		if (!isNaN(calcBalanceQty)) {
			document.getElementById("balanceQty_"+i).value = number_format(calcBalanceQty,2,'.','');
		} 
	}	
}


	//ADD MULTIPLE Item- ADD ROW START
	function addNewRCSRow(tableId)
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
		var cell5	= row.insertCell(4);
				
		cell1.className	= "listing-item"; cell1.align	= "center";cell1.noWrap = "true";
		cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
		cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
		cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";
		cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";
			
		var prodList = "<select name='selProduct_"+fieldId+"' id='selProduct_"+fieldId+"' style='width:180px;'><option value=''>-- Select --</option>";
		<?php
			if (sizeof($productMasterRecords)>0) {
				foreach ($productMasterRecords as $pmr) {
					$productId	=	$pmr[0];
					$productName	=	$pmr[2];
		?>		
			var selStateOpt = '';
		prodList += "<option value='<?=$productId?>' "+selStateOpt+"><?=$productName?></option>";
		<?
				}
			}
		?>
		prodList += "</select>";	
			
		var ds = "N";	
		var imageButton = "<a href='###' onClick=\"setItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";		

		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='retailCounterStkEntryId_"+fieldId+"' type='hidden' id='retailCounterStkEntryId_"+fieldId+"' value=''>";
	
		cell1.innerHTML = prodList;
		cell2.innerHTML = "<input name='availableQty_"+fieldId+"' type='text' id='availableQty_"+fieldId+"' value='' size='6' style='text-align:right' autoComplete='off' onKeyUp='balanceRetailCounterStock();'>";
		cell3.innerHTML = "<input name='usedQty_"+fieldId+"' type='text' id='usedQty_"+fieldId+"' size='6' style='text-align:right' value='' onKeyUp='balanceRetailCounterStock();'>";
		cell4.innerHTML = "<input name='balanceQty_"+fieldId+"' type='text' id='balanceQty_"+fieldId+"' size='8' readonly style='text-align:right' value=''>";
		cell5.innerHTML = imageButton+hiddenFields;
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