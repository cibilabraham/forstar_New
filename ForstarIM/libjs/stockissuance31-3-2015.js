function validateStockIssuance(form)
{
	var requestNo		=	form.requestNo.value;
	var selDepartment		=	form.selDepartment.value;
	
	
	if( requestNo=="" )
	{
		alert("Please enter a Request Receipt Number.");
		form.requestNo.focus();
		return false;
	}
	
	if( selDepartment=="" )
	{
		alert("Please select a Department.");
		form.selDepartment.focus();
		return false;
	}
	
	var itemCount	=	document.getElementById("hidTableRowCount").value;

		var count = 0;
		for (i=0; i<itemCount; i++)
		{
		   var status = document.getElementById("status_"+i).value;		    
	    	   if (status!='N') 
		    {
			var selStock		=	document.getElementById("selStock_"+i);
			var exisitingQty	=	document.getElementById("exisitingQty_"+i);
			var quantity		=	document.getElementById("quantity_"+i);
			var balanceQty	 	= 	document.getElementById("balanceQty_"+i);
						
			if( selStock.value == "" )
			{
				alert("Please Select a Stock Item.");
				selStock.focus();
				return false;
			}	
			
			if( quantity.value == "" )
			{
				alert("Please enter a quantity.");
				quantity.focus();
				return false;
			}	
			
			if (selStock.value!="" && exisitingQty.value == 0 )
			{
				alert("Sorry!! Selected Stock Item is not Present.");
				selStock.focus();
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

	if (itemCount==count) {
		alert("Please select atleast one stock item");
		return false;
	}
	if(!validateRepeatIssuance()){
		return false;
	}
	if(!confirmSave()){
			return false;
	}
	return true;
}


function newLineIssuance()
{
	document.frmStockIssuance.newline.value = '1';
	document.frmStockIssuance.submit();
}

// Balance Qty
function balanceQty()
{
	var stockStatus = false;
	var rowCount	= document.getElementById("hidTableRowCount").value;
	var total	= 0;
	
	var exisitingQty = "exisitingQty_";
	var pQty	 = "quantity_";
	var balanceQty	 = "balanceQty_";	
	
	for (i=0; i<rowCount; i++) {
	   var status = document.getElementById("status_"+i).value;		    
	   if (status!='N') 
	    {
	  	var quantity =	0;
	 	 if (document.getElementById(pQty+i).value!="") {
			 document.getElementById(balanceQty+i).value	 = document.getElementById(exisitingQty+i).value - document.getElementById(pQty+i).value;
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
		var rv = document.getElementById("selStock_"+j).value;	
		if ( arr.indexOf(rv) != -1 )	{
			alert("Stock Item Cannot be duplicate.");
			document.getElementById("selStock_"+j).focus();
			return false;
		}
		arr[arri++]=rv;
            }
	}
	return true;	
}
// Stock issuance, 
function addNewStockIssuanceItemRow(tableId, cId, qty, bqty, tqty)
{
	alert(cId);
	//var rowCountObj	= formObj.rowCount;
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

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'>";

	var opt			= "<select name='selStock_"+fieldId+"' Style='display:display;' id='selStock_"+fieldId+"' tabindex=1  onchange=\"xajax_getTotalQty(document.getElementById('selStock_"+fieldId+"').value,"+fieldId+"); balanceQty();\"  >";
		opt += "<option value=''>--select--</option>";
	<?
		if( $stockObj !="" )
		{
	?>

	<?
			//$stockRecords		= $stockObj->fetchAllRecords();
	//$stockRecords		= $stockObj->fetchAllActiveRecordsConfirm();
	$stockRecords		=$stockObj->fetchAllActiveRecordsConfirm();
			if( sizeof($stockRecords) > 0 )
			{
				foreach ($stockRecords as $sr) 
				{
					$stockId			=	$sr[0];
					$stockCode			=	stripSlash($sr[1]);
					$stockName			=	stripSlash($sr[2]);
					$selected			=	"";
					if( ($selStockId!="" && $selStockId==$stockId) || ($editStockId!="" && $editStockId==$stockId)) { $selected="selected"; }

	?>	
					if( cId == "<?=$stockId?>")  var sel = "Selected";
					else var sel = "";
	
	opt				+= "<option value='<?=$stockId?>' "+sel+" <?=$selected?>><?=$stockName?></option>";

	<?
				}
			}
		}
	?>
	opt +="</select>";
	
	cell1.innerHTML	= opt;
	cell2.innerHTML	= "<input name='exisitingQty_"+fieldId+"' type='text' id='exisitingQty_"+fieldId+"' value='"+tqty+"' size='4' readonly style='text-align:right; border:none;'/>";
	cell3.innerHTML	= "<input name='quantity_"+fieldId+"' type='text' id='quantity_"+fieldId+"' size='4' style='text-align:right' value='"+qty+"' tabindex="+fieldId+"  onKeyUp='return balanceQty();'>";
	cell4.innerHTML	= "<input name='balanceQty_"+fieldId+"' type='text' id='balanceQty_"+fieldId+"' size='4' readonly style='text-align:right; border:none;' tabindex="+fieldId+"  value='"+bqty+"'>" + hiddenFields;
	cell5.innerHTML = imageButton;
	
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

function disableStockIssuanceButtons(mode)
{		
	//alert(mode);
	if (mode == 1)
	{
		document.getElementById("cmdAdd2").disabled = true;
		document.getElementById("cmdAdd1").disabled = true;	
	}
	else if( mode == 2)
	{
		document.getElementById('cmdSaveChange2').disabled = true;
		document.getElementById('cmdSaveChange1').disabled = true;		
	}
}

function enableStockIssuanceButtons(mode)
{
	if (mode == 1 )
	{		
		document.getElementById('cmdAdd2').disabled = false;
		document.getElementById('cmdAdd1').disabled = false;
	} else if( mode == 2) {
		document.getElementById('cmdSaveChange2').disabled = false;
		document.getElementById('cmdSaveChange1').disabled = false;
	}
}
