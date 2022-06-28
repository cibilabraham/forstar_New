
 
suppIngArr = new Array();
 
function validateIngredientPurchaseOrder(form)
{
	var selSupplier		=	form.selSupplier.value;
	var poNumber		=	form.textfield.value;
	var genPoid = document.getElementById("genPoId").value;

	if (poNumber=="" && genPoid==0 ) {
		alert("Please enter a Purchase Order ID.");
		form.textfield.focus();
		return false;
	}
	
	if (selSupplier=="") {
		alert("Please select a Supplier.");
		form.selSupplier.focus();
		return false;
	}	

	var itemCount	=	document.getElementById("hidTableRowCount").value;
	var statusCount =0;
	for (i=0; i<itemCount; i++) {
	  var status = document.getElementById("status_"+i).value;
	  if (status!='N') {
		var selStock	=	document.getElementById("selIngredient_"+i);
		var unitPrice	=	document.getElementById("unitPrice_"+i);
		var quantity	=	document.getElementById("quantity_"+i);
					
			
		if (selStock.value == "") {
			alert("Please Select a Stock Item.");
			selStock.focus();
			return false;
		}
		if (unitPrice.value == "") {
			alert("Please enter a rate.");
			unitPrice.focus();
			return false;
		}
		if (quantity.value == "") {
			alert("Please enter a quantity.");
			quantity.focus();
			return false;
		}
	 } else {
		statusCount++;
	  } 
					
	}

	if (itemCount==statusCount) {
		alert("Please add atleast one Ingredient");
		return false;
	}
	if(!validateIngredientPORepeat()){
		return false;
	}

	if(!confirmSave()){
			return false;
	}

document.getElementById('company').disabled=false;
document.getElementById('unit').disabled=false;
document.getElementById('selSupplier').disabled=false;

	return true;
}

//Add a New Line 
function ingredientPONewLine()
{
	document.frmIngredientPO.newline.value = '1';
	document.frmIngredientPO.submit();
}

// multiply with the selected PO Item
function multiplyIngPOItem(poItem)
{
	
	var rowCount	=	document.getElementById("hidTableRowCount").value;	
	var total	= 0;	
	var pUnit	=	"unitPrice_";
	var pQty	=	"quantity_";
	var pTotal	=	"total_";
	var colCt  	= 	"hidSupplierCount_";

	var columnCount = "";
	for (i=0; i<rowCount; i++) 
	{
	var status = document.getElementById("status_"+i).value;
	if (status!='N') {
		if (poItem!="") {
			columnCount = document.getElementById(colCt+i).value;			
			var negotiatedPrice = "";
			var selSupplier = "";
			for (j=1; j<=columnCount; j++)
	  		{
				var nPrice    = "negoPrice_";
				var sSupplier = "selSupplier_";
				selSupplier = document.getElementById(sSupplier+j+"_"+i).checked;
				if (selSupplier) {
					negotiatedPrice = document.getElementById(nPrice+j+"_"+i).value;
				}
			}
		}
		
		var quantity	=	0;		
		
	 	 if (document.getElementById(pQty+i).value!="" && poItem!="") {
			document.getElementById(pTotal+i).value = negotiatedPrice * document.getElementById(pQty+i).value;
		} else if (document.getElementById(pQty+i).value!="" && poItem=="") {			
			document.getElementById(pTotal+i).value = document.getElementById(pUnit+i).value * document.getElementById(pQty+i).value;
	  	} else {
			document.getElementById(pTotal+i).value =0;
		}
		quantity= document.getElementById(pTotal+i).value;
		total	= parseFloat(total)+parseFloat(quantity);
	  }
	}
	
	if (!isNaN(total)) {
		document.getElementById("totalQuantity").value = number_format(total,2,'.','');	
	}
}

//Validate repeated
function validateIngredientPORepeat()
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
     var status = document.getElementById("status_"+j).value;
     if (status!='N') {
        var rv = document.getElementById("selIngredient_"+j).value;
        if ( arr.indexOf(rv) != -1 )    {
            alert("Ingredient cannot be duplicate.");
            document.getElementById("selIngredient_"+j).focus();
            return false;
        }
        arr[arri++]=rv;
     }
    }
    return true;
}

//ADD MULTIPLE Item- ADD ROW START
function addNewIngredientItemRow(tableId, poItem, selIngredientId, qty, totalAmt)
{	
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length-1;
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
	var cell6	= row.insertCell(5);	
	var cell7	= row.insertCell(6);	
	var cell8	= row.insertCell(7);	
	var cell9	= row.insertCell(8);	
	
	cell1.id = "srNo_"+fieldId;	
	cell1.className	= "listing-item"; cell1.align	= "center";

	cell2.className	= "listing-item"; cell2.align	= "center";
	if (poItem=="") {
	 cell3.className	= "listing-item"; cell3.align	= "center";
	} else {
	 cell3.style.display = "none";	
	}	
    cell4.className	= "listing-item"; cell4.align	= "center";
    cell5.className	= "listing-item"; cell5.align	= "center";
	cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true"	
	cell7.className	= "listing-item"; cell7.align	= "center";
	cell8.className	= "listing-item"; cell8.align	= "center";
	cell9.className	= "listing-item"; cell9.align	= "center";
			
	var selectIng	= "<select name='selIngredient_"+fieldId+"' id='selIngredient_"+fieldId+"' onchange=\"xajax_getIngRate(document.getElementById('selSupplier').value,document.getElementById('selIngredient_"+fieldId+"').value,"+fieldId+");\">";
	if (!suppIngArr.length) selectIng += "<option value=''>--Select--</option>";
	for (var ingId in suppIngArr) {
		selectIng += "<option value='"+ingId+"'>"+suppIngArr[ingId]+"</option>";
	}	 
	selectIng += "</select>";
	
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setPOIngItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidSelIng_"+fieldId+"' type='hidden' id='hidSelIng_"+fieldId+"' readonly value='"+selIngredientId+"'><input name='hidSupplierIng_"+fieldId+"' type='hidden' id='hidSupplierIng_"+fieldId+"' readonly value=''>";
	
	cell1.innerHTML	= "";//(fieldId+1);
	cell2.innerHTML	= selectIng;
	cell3.innerHTML	= "<input name='unitPrice_"+fieldId+"' type='text' id='unitPrice_"+fieldId+"' value='' size='6' style='text-align:right' readonly>";
	cell4.innerHTML	= "<input name='quantity_"+fieldId+"' type='text' id='quantity_"+fieldId+"' value='"+qty+"' size='6' style='text-align:right' autoComplete='off' onKeyUp=\"return multiplyIngPOItem('<?=$poItem?>');\">"+hiddenFields+"";
	cell5.innerHTML	= "<input name='total_"+fieldId+"' type='text' id='total_"+fieldId+"' size='6' readonly style='text-align:right' value='"+totalAmt+"'>";
	cell6.innerHTML	= "<div id='otherSupplierDiv_"+fieldId+"'></div>";
	cell7.innerHTML	= "<div id='LastPurchaseDiv_"+fieldId+"'></div>";
	cell8.innerHTML	= "<div id='balanceQty_"+fieldId+"'></div>";
	cell9.innerHTML = imageButton;	
	
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;	
	assignSrNo();
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

function setPOIngItemStatus(id)
{
	if( confirmRemoveItem() )
	{
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none';
		multiplyIngPOItem('<?=$poItem?>');
		assignSrNo();
	}
	return false;
}

function printIngPurchaseOrderWindow(url,width,height)
{
	var POId = document.getElementById("selPOId").value;
	var displayUrl = url+"?selPOId="+POId;
	var winl = (screen.width - width) / 2;
     	var wint = (screen.height - height) / 2;
	eval("page = window.open(displayUrl, 'Forstar_Foods', 'top="+ wint +", left="+ winl +",  status=1,scrollbars=1,location=0,resizable=1,width="+ width +",height="+ height +"');");
}

// Disable and enable the Print PO Button
function disablePrintPOButton()
{
	if (document.getElementById("selPOId").value=="") {
		document.getElementById("cmdPrintPO").disabled = true;
	} else {
		document.getElementById("cmdPrintPO").disabled = false;
	}
}

function enableIngPOButton(mode)
{
	if (mode==1) {
		document.getElementById("cmdAdd").disabled = false;
		document.getElementById("cmdAdd1").disabled = false;
	} else if (mode==0) {
		document.getElementById("cmdSaveChange").disabled = false;
		document.getElementById("cmdSaveChange1").disabled = false;
	}
}

function disableIngPOButton(mode)
{		
	if (mode==1) {
		document.getElementById("cmdAdd").disabled = true;
		document.getElementById("cmdAdd1").disabled = true;
	} else if (mode==0) {
		document.getElementById("cmdSaveChange").disabled = true;
		document.getElementById("cmdSaveChange1").disabled = true;
	}
}

function uncheckSelected(field,rowId)
{
	//alert(field+","+suppId);
	if (!document.getElementById(field).checked) chk = false;
	else chk = true;
	if (document.getElementById("hidSupplierCount_"+rowId)!=null ) { 
		var columnCount = document.getElementById("hidSupplierCount_"+rowId).value;		
	}
	for (j=1; j<=columnCount; j++) {
		document.getElementById("selSupplier_"+j+"_"+rowId).checked = false;
	}
	document.getElementById(field).checked = chk;
	/* 
		Edited on 26-12-08
		var rowCount	=	document.getElementById("hidTableRowCount").value;		
		for (i=0; i<rowCount; i++) {
			if( document.getElementById("hidSupplierCount_"+i)!=null ) { 
				var columnCount = document.getElementById("hidSupplierCount_"+i).value;		
			}
			for (j=1; j<=columnCount; j++) {
				if(suppId==i) document.getElementById("selSupplier_"+j+"_"+i).checked = false;
	
			}
		}
		document.getElementById(chkId).checked = true;
	*/
}
/*
 Calculate selected Ing Amt
*/
function multiplyIngPO(poItem)
{
	var rowCount	=	document.getElementById("hidTableRowCount").value;
	
	var total	= 0;	
	var pUnit	=	"unitPrice_";
	var pQty	=	"quantity_";
	var pTotal	=	"total_";
	var colCt  	= 	"hidSupplierCount_";

	var columnCount = "";
	for (i=0; i<rowCount; i++) 
	{
		var status = document.getElementById("status_"+i).value;
		if (status!='N') {
			if (poItem!="") {			
			if( document.getElementById(colCt+i) !=null )
			{
			 	columnCount = document.getElementById(colCt+i).value;
			}
			var negotiatedPrice = "";
			var selSupplier = "";
			for (j=1; j<=columnCount; j++)
	  		{
				var nPrice    = "negoPrice_";
				var sSupplier = "selSupplier_";
				selSupplier = document.getElementById(sSupplier+j+"_"+i).checked;
				if (selSupplier) {
					//negotiatedPrice = document.getElementById(nPrice+j+"_"+i).value;
				}
			}
		}
		
		var quantity	=	0;	
		if (document.getElementById(pQty+i).value!="") {
			document.getElementById(pTotal+i).value = document.getElementById(pUnit+i).value * document.getElementById(pQty+i).value;
	  	} else {
			document.getElementById(pTotal+i).value =0;
		}
		quantity= document.getElementById(pTotal+i).value;
		total	= parseFloat(total)+parseFloat(quantity);
	  }
	}
	
	if (!isNaN(total)) {
		document.getElementById("totalQuantity").value = number_format(total,2,'.','');	
	}
}

function POIngStatus(id)
{
	if( confirmRemoveItem() )
	{
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none';
		multiplyIngPO('<?=$poItem?>');
	}
	return false;
}

function addNewUpdatePOItem(tableId,formObj, mode)
{	
	var rowCountObj	= formObj.totalRowCount;	
	var tbl = document.getElementById(tableId);
	var lastRow = tbl.rows.length-1;
	var iteration = lastRow+1;
	var row = tbl.insertRow(lastRow);
	row.height	= "22";
	row.className = "whiteRow";
	row.id = "row_"+fieldId;

	var cell1 = row.insertCell(0);
	var cell2 = row.insertCell(1);
	var cell3 = row.insertCell(2);
	var cell4 = row.insertCell(3);
	var cell5 = row.insertCell(4);	
	var cell6 = row.insertCell(5);	
	var cell7 = row.insertCell(6);

	cell1.className = "fieldName"; cell1.align = 'left';
	cell2.className = "fieldName"; cell2.align = 'center';
	cell3.className = "fieldName"; cell3.align = 'center';
	cell4.className = "fieldName"; cell4.align = "center";
	cell5.className = "fieldName"; cell5.align = "right";	
	cell6.className = "fieldName"; cell6.align = "center";
	cell6.id = "OtherSuppList_"+fieldId;	
	cell7.className = "fieldName"; 
	cell7.align = "center";		
	cell7.noWrap = true;
	
	var optBox = "<select name='selIngredient_"+fieldId+"'   id='selIngredient_"+fieldId+"' onChange=\"xajax_getQuantitiesOfStock(document.getElementById('selIngredient_"+fieldId+"').value, "+fieldId+", document.getElementById('hidSupplierIdRec').value);\" ><option value=''>--select--</option>";	
	<?php
	if (sizeof($ingredientRecords)>0) {			
		foreach ($ingredientRecords as $irr) {					
			$ingredientId   = $irr[1];					
			$ingredientName	= $irr[8];
	
	?>				
		optBox+= "<option value='<?=$ingredientId;?>'><?=$ingredientName;?></option>";	
	<?				
			}			
		}			
	?>	
	optBox+= "</select> ";

	var ds = "N";		
	if( fieldId >= 1) var imageButton = "<a href='#' onClick=\"POIngStatus('"+fieldId+"');\" ><img SRC='images/delIcon.gif' BORDER='0' style='border:none;' title='Click here to remove this item.'></a>";
	else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'>";	
	cell1.innerHTML = optBox;	
	cell2.innerHTML = "<input name=\"unitPrice_"+fieldId+"\" type=\"text\" id=\"unitPrice_"+fieldId+" value='' size='6' readonly style='text-align:right;border:none'/>";
	cell3.innerHTML = '<input name="quantity_'+fieldId+'" type="text" id="quantity_'+fieldId+'" size="6" style="text-align:right" value="" onKeyUp=\'return multiplyIngPO(document.frmPurchaseOrderInventory,"Y");\' > ' + hiddenFields;		
	cell4.innerHTML = '<input name="total_'+fieldId+'" type="text" id="total_'+fieldId+'" size="6" readonly style="text-align:right" value="">';	
	cell5.innerHTML = '<div id="bqty_'+fieldId+'" ></div>';	
	cell6.innerHTML = '<font color="red"><span style="line-height:normal; font-size:9px;">No suppliers found<span></font>';	
	cell7.innerHTML = imageButton;	
	var newTextBox	=	document.getElementById("selIngredient_"+fieldId);
	newTextBox.focus();
	rowCountObj.value = parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = rowCountObj.value;	
	fieldId = fieldId+1;
	initRow	= 2;
}

function validateUpdateIngPO(form)
{	
	var stockItem		= 	form.selItem.value;

	if (stockItem=="") {	
		var selSupplier		=	form.selSupplier.value;
		var poNumber		=	form.textfield.value;
		var genPoid = document.getElementById("genPoId").value;
		
		if( poNumber=='' && genPoid==0 ) {
			alert("Please enter a Purchase Order ID.");
			form.textfield.focus();
			return false;
		}		
		if (selSupplier=="") {
			alert("Please select a Supplier.");
			form.selSupplier.focus();
			return false;
		}
	}
	
	var itemCount	= document.getElementById("hidTableRowCount").value;
	var columnCount = "";
	var count = 0;
	var orderItemStatus = true;
	for (i=0; i<itemCount; i++) {
		var status = document.getElementById("status_"+i).value;
		var selStock  = document.getElementById("selIngredient_"+i).value;		
		
		if (status!='N') {
			var selStock	=	document.getElementById("selIngredient_"+i);		
			var quantity	=	document.getElementById("quantity_"+i);		
		
				if (selStock.value == "") {
					alert("Please Select a Stock Item.");
					selStock.focus();
					return false;
				}	
				if (quantity.value == "" || quantity.value==0) {
					alert("Please enter a quantity.");
					quantity.focus();
					return false;
				}
					
			if (stockItem!="") {
				columnCount = document.getElementById("hidSupplierCount_"+i).value;
				var supplierSelected = false;	
				for (j=1; j<=columnCount; j++) {
					if (document.getElementById("selSupplier_"+j+"_"+i).checked) {
						supplierSelected = true;	
					}
				}
				if (supplierSelected==false&&columnCount>0) {
					alert("Please select a supplier.");
					return false;
				}
	
				if (columnCount>0 && quantity.value == "") {
					alert("Please enter a quantity.");
					quantity.focus();
					return false;
				}
				if (columnCount==0) {
					alert("Please define a supplier for the selected item");
					selStock.focus();
					return false;
				}
			}
				
			} else {
				count++;
			}				
	 }	
	if (itemCount==count) {
		alert("Please add atleast one Stock Item");
		return false;
	}

	if (!validateIngredientPORepeat()) {
		return false;
	}

	if(!confirmSave()){
		return false;
	}
	return true;
}

// Calculate total Values
function calcTotalValues(form)
{
	 var rc = form.hidSuppCount.value;
	 var subTotalUnitPrice = 0;
	 var subTotalTotalPrice = 0;
	 var subTotalQuantiy = 0;

	 for (p=0;p<rc ;p++) {
		var unitPrice = document.getElementById("hidTotUnitPrice_"+p).value;
		var totalPrice = document.getElementById("hidTotPrice_"+p).value;
		var totalQuantity = document.getElementById("hidTotQuantity_"+p).value;
		
		subTotalUnitPrice += parseInt(unitPrice);
		subTotalTotalPrice += parseInt(totalPrice);
		subTotalQuantiy += parseInt(totalQuantity);
	 }
	
	document.getElementById("subTotalUP").innerHTML = number_format(subTotalUnitPrice,2,".","");
	document.getElementById("subTotalQTY").innerHTML = number_format(subTotalQuantiy,2,".","");
	document.getElementById("subTotalTTL").innerHTML = number_format(subTotalTotalPrice,2,".","");
	
	return false;
}

// Validate Wen Updating Orders
function validateIngPOId(form)
{	
	var rc = form.hidSuppCount.value;
	var genPoid = form.genPoid.value;
	var poIdExist = false;	
	var poIdDuplicate = false;
	var prevPoId = "";
	for (i=0;i<rc ;i++ ) {
		if (genPoid==0) {
			var poid = document.getElementById("inpPOid_"+i).value;
			var isPoExist = document.getElementById("isPoExist_"+i).value;
			if (poid=="" && genPoid==0) {
				alert("Please enter a PO ID. ");
				document.getElementById("inpPOid_"+i).focus();
				return false;
			}

			if (isPoExist=='Y') poIdExist=true; 
					
			if (prevPoId=poid)  poIdDuplicate = true;			
			prevPoId = poid;
		}
	}
	if (document.getElementById("hidConf").value == "Y") return true;
	else {
		if (poIdExist && genPoid==0) {
			alert("Please check the PO Id");
			return false;
		}		
		if (confirmSave()) {
			return true
		}
		return false;
	}
}

// Chk Update btton
function chkUpdateBtnField()
{
	var poIdExist = false;	
	var rc = document.getElementById("hidSuppCount").value;
	for (i=0;i<rc;i++) {
		var isPoExist = document.getElementById("isPoExist_"+i).value;
		if (isPoExist=='Y') poIdExist=true; 
	}	
	if (poIdExist) {
		document.getElementById('cmdUpdateOrder').disabled =true;
		document.getElementById('cmdUpdateOrder1').disabled =true;
	} else {
		document.getElementById('cmdUpdateOrder').disabled =false;
		document.getElementById('cmdUpdateOrder1').disabled =false;
	}	
}

	function fillIngDropDown(ingIdArr, ingNameArr, rowCount)
	{
		idArr 	= ingIdArr.split(",");
		nameArr = ingNameArr.split(",");
		for (var i=0; i<rowCount; i++) {
			suppIngArr[idArr[i]]= nameArr[i];
		}
	}

	function fillDropDowns(ingredientId, ingName)
	{	
		suppIngArr[ingredientId]= ingName;
	}

	function fillListedDropDown(tbleRowCount)
	{		
		for (i=0; i<tbleRowCount; i++) {			
			document.getElementById('selIngredient_'+i).length=0;
			for (var ingId in suppIngArr) {
				addDropDownList('hidSelIng_'+i,'selIngredient_'+i,ingId,suppIngArr[ingId]);
			}
		}
		
	}

function getPOInvId()
{
	//alert("hii");
		//var dataValue="showMsg";
		var company=document.getElementById('company').value;
		var unit=document.getElementById('unit').value;
		var dataValue={"Company":company,"Unit":unit};
		$.ajax({
			type:"POST",
			dataType:'json',// if specifies datatype then sucess data will be in the json array format $response_array['status'] = 'success'; and also header('Content-type: application/json');
			url:"IngredientPO.php?action=displayMsg",
			data:{myData:JSON.stringify(dataValue)},
			success:function(data)
			{
				//alert(data[1]);
				var message=data[0];
				var poId=data[1];
				var numGen=data[2];
				if(message!="")
				{
					$("#message").html(message);
					$("#cmdAdd").attr('disabled', true);
				}
				else
				{
					$("#textfield").val(poId);
					$("#number_gen_id").val(numGen);
					$("#cmdAdd").attr('disabled', false);
				}
			
			}
	 });
		
}
