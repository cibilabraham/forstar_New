function validateProductStatus(form)
{
	var rowCount	= document.getElementById("hidTableRowCount").value;
	var itemsSelected = false;
		
	if (rowCount>0) {
		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var selProduct = document.getElementById("selProduct_"+i);
				var selStatus  = document.getElementById("selStatus_"+i);
				
					if (selProduct.value=="") {
						alert("Please select a Product.");
						selProduct.focus();
						return false;
					}
					/*
					if (!selStatus.checked) {
						alert("Please select inactive.");
						selStatus.focus();
						return false;
					}
					*/
					
					if (selProduct.value!="") {
						itemsSelected = true;
					}
				}
			}  // For Loop Ends Here
		} // Row Count checking End
		if (itemsSelected==false) {
			alert("Please add atleast one product");
			return false;
		}
		if (!validateItemRepeat()) {
			return false;
		}	
	
	if (!confirmSave()) return false;
	return true;
}

function validateSearchProductStatus(form)
{
	var selState = document.getElementById("selState").value;
	if (selState=="") {
		alert("Please select a State");
		document.getElementById("selState").focus();
		return false;
	}
	return true;
}

// ADD MULTIPLE Item- ADD ROW START
function addNewProductStatusItemRow(tableId, productId, inactive)
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
			
	var selProduct = "<select name='selProduct_"+fieldId+"' id='selProduct_"+fieldId+"' onchange=\"xajax_getDistributorRecs(document.getElementById('selProduct_"+fieldId+"').value,"+fieldId+",'');\"><option value=''>-- Select --</option>";
	<?php
		if (sizeof($productRecords)>0) {	
			 foreach ($productRecords as $pr) {
				$mproductId	= $pr[0];					
				$mproductName	= $pr[2];
	?>	
		if (productId== "<?=$mproductId?>")  var sel = "Selected";
		else var sel = ""; 

	selProduct += "<option value=\"<?=$mproductId?>\" "+sel+"><?=$mproductName?></option>";	
	<?php
			}
		}
	?>
	selProduct += "</select>";

	var selDistributor = "<select name='selDistributor_"+fieldId+"' id='selDistributor_"+fieldId+"' onChange = \"xajax_getDistStateList(document.getElementById('selDistributor_"+fieldId+"').value,"+fieldId+",''); \"><option value='0'>-- Select All --</option>";	
	selDistributor += "</select>";

	var selState	= "<select name='selState_"+fieldId+"' id='selState_"+fieldId+"'><option value='0'>-- Select All --</option>";
	selState += "</select>";
	
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setProdItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'>";	
	
	if (inactive== 'Y')  var chkStatus = "Checked";
	else var chkStatus = ""; 
	
	cell1.innerHTML	= selProduct;
	cell2.innerHTML	= selDistributor;
	cell3.innerHTML	= selState+hiddenFields;
	
	cell4.innerHTML	= "<input name='selStatus_"+fieldId+"' type='checkbox' id='selStatus_"+fieldId+"' class='chkBox' value='Y' "+chkStatus+">";
	cell5.innerHTML = imageButton;	
	
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;		
}

function setProdItemStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none';		
	}
	return false;
}

// ------------------------------------------------------
// Duplication check starts here
// ------------------------------------------------------
var cArr = new Array();
var cArri = 0;	
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
	var pc = 0;
	var ps = 0;
	var pg = 0;	
	var pArr	= new Array();	
	var pa		= 0;

	for (j=0; j<rc; j++) {
		var status = document.getElementById("status_"+j).value;
		if (status!='N') {
			var selProduct 	= document.getElementById("selProduct_"+j).value;		
			var selDistributor = document.getElementById("selDistributor_"+j).value;
			var selState 	= document.getElementById("selState_"+j).value;

			var addVal = selProduct+""+selDistributor+""+selState;			
			
			if (pArr.indexOf(addVal)!=-1) {
				alert(" Combination cannot be duplicate.");
				document.getElementById("selProduct_"+j).focus();
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

	function validateAssignRemove(distMarginId, distMarginStateEntryId, rowId, selDistributorId, mproductId, selStateId, selRateListId, xjxRedirectUrl)
	{
		if (!confirm("Do you wish to remove the selected product Margin?")) {
			return false;
		}
		xajax_removeDistMargin(distMarginId,distMarginStateEntryId,rowId,selDistributorId, mproductId, selStateId, selRateListId, xjxRedirectUrl);	
	
		
		return true;
	}

	// Validate Multiple Product
	function validateMultipleProduct(fieldPrefix, rowCount)
	{
		//var selectionType1 = document.getElementById("selectionType1").checked;
		//var selectionType2 = document.getElementById("selectionType2").checked;
		var count = 0;
		var productAlreadyAssigned = false;
		var distStateRowCount = document.getElementById("distStateRowCount").value;
		for (i=1; i<=rowCount; i++ ) {
			if (document.getElementById(fieldPrefix+i).checked) {
				count++;
				for (j=1; j<=distStateRowCount; j++ ) {
					var productAssigned = document.getElementById("productAssign_"+i+""+j).value;
					if (productAssigned) productAlreadyAssigned = true;
				}
			}		
		}

		if (count==0) {
			alert("Please select a record.");
			return false;
		}
	
		if (count==1) {
			alert("Please select multiple product to assign margin.");
			return false;
		}	

		if (productAlreadyAssigned) {
			alert("Product you have selected is already assigned.");
			return false;
		}	

		/*
		if (!selectionType1 && !selectionType2) {
			alert("Please select Individual/Group margin assigning.");
			return false;
		}
		*/

		if (!confirmContinue()) return false;
		return true;
	}
	
	

