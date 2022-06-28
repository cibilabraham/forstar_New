function validateStockReturn(form)
{
	var requestNo		=	form.requestNo.value;
	var selDepartment		=	form.selDepartment.value;
	
	
	if( requestNo=="" )
	{
		alert("Please enter a Request Number.");
		form.requestNo.focus();
		return false;
	}
	
	if( selDepartment=="" )
	{
		alert("Please select a Department.");
		form.selDepartment.focus();
		return false;
	}
	
	var itemCount	=	document.getElementById("rowCount").value;
	var stockQtyExist = true;
	for (i=0; i<itemCount; i++)
	{
		var status			=	document.getElementById("Status_"+i).value;
		if (status !='N' ) {
			var selStock = document.getElementById("selStock_"+i);
			var selReason = document.getElementById("selReason_"+i); 
			var returnQtyExist = document.getElementById("returnQtyExist_"+i); 
			var quantity		=	document.getElementById("quantity_"+i);

					
			if (selStock.value == "") {
				alert("Please Select a Stock Item.");
				selStock.focus();
				return false;
			}	
			
			if (selReason.value == "") {
				alert("Please select a Reason.");
				selReason.focus();
				return false;
			}	
			
			if (quantity.value == "")
			{
				alert("Please enter a Quantity.");
				quantity.focus();
				return false;
			}
			
			if (returnQtyExist.value=='N' && quantity.value!="") {
				stockQtyExist = false;
			}

			if( ( selReason.value != "L" && selReason.value != "S" ) && document.getElementById("scrapValue_"+i).value=="" )
			{
				alert("Please enter a Total Scrap Value.");
				document.getElementById("scrapValue_"+i).focus();
				return false;
			}
			if( ( selReason.value == "L" || selReason.value == "S" ) && document.getElementById("remark_"+i).value=="" )
			{
				alert("Please enter a Remark.");
				document.getElementById("remark_"+i).focus();
				return false;
			}
		}
	}

	if (!stockQtyExist) {
		alert("Please check the stock return qty");
		return false;
	}

	if(!validateRepeatReturn()){
		return false;
	}
	if(!confirmSave()){
			return false;
	}
	return true;
}
/* Checking Enable button or disable button*/
function chkStockQtyExist()
{
	//alert("Here");
	var itemCount		= document.getElementById("rowCount").value;
	var mode		= document.getElementById("hidMode").value;
	var requestNumExist	= document.getElementById("requestNumExist").value;
	var stockQtyExist = true;
	for (i=0; i<itemCount; i++)
	{
		var status			=	document.getElementById("Status_"+i).value;
		if (status !='N' ) {			
			var returnQtyExist = document.getElementById("returnQtyExist_"+i); 
			var quantity		=	document.getElementById("quantity_"+i);			
			if (returnQtyExist.value=='N' && quantity.value!="") {
				stockQtyExist = false;
			}	
		}
	}

	if (!stockQtyExist || requestNumExist!="") {
		disableStockReturnButtons(mode);	
	} else if (requestNumExist=="") {
		enableStockReturnButtons(mode);
	}
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
	var rowCount	= document.getElementById("rowCount").value;
	var total	= 0;
	
	var exisitingQty = "exisitingQty_";
	var pQty	 = "quantity_";
	var balanceQty	 = "balanceQty_";	
	
	for (i=0; i<rowCount; i++) 
	{
		var quantity =	0;
	 
		if (document.getElementById(pQty+i).value!="") 
		{
			document.getElementById(balanceQty+i).value	 = document.getElementById(exisitingQty+i).value - document.getElementById(pQty+i).value;
		}
		else document.getElementById(balanceQty+i).value =0;
		
		if (document.getElementById(balanceQty+i).value<0) stockStatus = true;			
	}

	if (stockStatus==true) document.getElementById("hidStockItemStatus").value='P';
	else document.getElementById("hidStockItemStatus").value='C';
}

//Validate repeated
function validateRepeatReturn()
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

	var rc = document.getElementById("hidItemCount").value;

	var prevOrder = 0;
	var arr = new Array();
	var arri=0;
	for( j=0; j<rc; j++ )	{
		if( document.getElementById("Status_"+j) != null )
		{
			var status = document.getElementById("Status_"+j).value;        
		}
		else var status =  '';

		if( status!='N')
		{
			var rv = document.getElementById("selStock_"+j).value;	
			if ( arr.indexOf(rv) != -1 )	{
				alert("Stock Item cannot be duplicate.");
				document.getElementById("selStock_"+j).focus();
				return false;
			}
			arr[arri++]=rv;
		}
	}
	return true;	
}

/*
* @desc: add new row of stock issuance
* @param tableId: id of the table
* @param formObj: document.formname
*/

function addNewStockSelection(tableId, formObj, mode, cId, qty, totQty, sval,reasonType, remarks, incCosting)
{
	//alert("hii");
	var rowCountObj	= formObj.rowCount;
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length-1;
	var iteration	= lastRow+1;
	var row		= tbl.insertRow(lastRow);
	row.height	= "22";
	row.className 	= "whiteRow";
	row.id 		= "ItemRow_"+fieldId;

	var cell1			= row.insertCell(0);
	var cell2			= row.insertCell(1);
	var cell3			= row.insertCell(2);
	var cell4			= row.insertCell(3);
	var cell5			= row.insertCell(4);
	var cell6			= row.insertCell(5);
	var cell7			= row.insertCell(6);
	

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = 'center';
	cell5.className	=	"fieldName"; cell5.align = "center";
	cell6.className	=	"fieldName"; cell6.align = "right";
	cell7.className	=	"fieldName"; cell7.align = "center";
	
	
	var ds = "N"; 
	
	if( reasonType == 'L' ) var selLost = 'selected';
	if( reasonType == 'S' ) var selStolen = 'selected';
	if( reasonType == 'D' ) var selDmgd = 'selected';
	if( reasonType == 'DR' ) var selDtro = 'selected';

	if (incCosting=='Y') var selIncCosting = 'checked';

	 var reasonBox = "<select name='selReason_"+fieldId+"' tabindex="+fieldId+" id='selReason_"+fieldId+"' onChange='displayAmtInput("+fieldId+");'>";
	 reasonBox += "<option value=''>--select--</option>";
	 reasonBox += "<option value='L' "+selLost+" >Lost</option>";
	 reasonBox += "<option value='S' "+selStolen+" >Stolen</option>";
	 reasonBox += "<option value='D' "+selDmgd+" >Damaged</option>";
	 reasonBox += "<option value='DR' "+selDtro+" >Deteriorated</option>";
	 reasonBox += "</select>";

	if( fieldId >= 1) var imageButton = "<a href='###' onClick=\"setStatus('"+fieldId+"');calculateTotalAmount("+fieldId+");\" ><img title='Click here to remove this item.' SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='Status_"+fieldId+"' type='hidden' id='Status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='returnQtyExist_"+fieldId+"' type='hidden' id='returnQtyExist_"+fieldId+"'>";

	var opts	= "<select name='selStock_"+fieldId+"' Style='display:display;' id='selStock_"+fieldId+"' tabindex=1  onchange=\"duplicateStockIssuance(1,"+fieldId+"); xajax_checkStockIssued(document.getElementById('selDepartment').value,document.getElementById('selStock_"+fieldId+"').value, document.getElementById('quantity_"+fieldId+"').value, '"+mode+"', '"+fieldId+"');\">";
		opts += "<option value=''>--select--</option>";
	<?
	//	if ($stockObj!="") {
	?>

	<?
			//$stockRecords		= $stockObj->fetchAllActiveRecords();
			$stockRecords		=$stockObj->fetchAllActiveRecordsConfirm();
			if (sizeof($stockRecords)>0) {
				foreach ($stockRecords as $sr)  {
					$stockId			=	$sr[0];
					$stockCode			=	stripSlash($sr[1]);
					$stockName			=	stripSlash($sr[2]);
					$selected			=	"";
					if( ($selStockId!="" && $selStockId==$stockId) || ($editStockId!="" && $editStockId==$stockId)) { $selected="selected"; }

	?>	
					if( cId == "<?=$stockId?>")  var selt = "Selected";
					else var selt = "";
	
					opts+= '<option value="<?=$stockId?>" '+selt+'><?=$stockName?></option>';
	
	<?
				}
			}
	//	}
	?>
	opts +="</select>";
	
	cell1.innerHTML	= opts+"<br>"+"<span id='returnErrMsg_"+fieldId+"' class='err1' style='line-height:normal; font-size:12px;'></span>";
	cell2.innerHTML	= reasonBox;
	cell3.innerHTML	= "<input name='quantity_"+fieldId+"' onKeyUp='calculateTotalAmount("+fieldId+");' type='text' id='quantity_"+fieldId+"' size='8' style='text-align:right' value='"+qty+"' tabindex="+fieldId+" onchange=\"xajax_checkStockIssued(document.getElementById('selDepartment').value,document.getElementById('selStock_"+fieldId+"').value, document.getElementById('quantity_"+fieldId+"').value, '"+mode+"', '"+fieldId+"');\">";
	cell4.innerHTML	= "<input name='incCosting_"+fieldId+"' type='checkbox' id='incCosting_"+fieldId+"' value='Y' tabindex="+fieldId+" class='chkBox' "+selIncCosting+">";
	cell5.innerHTML	= "<input name='scrapValue_"+fieldId+"' onKeyUp='calculateTotalAmount("+fieldId+");' type='text' id='scrapValue_"+fieldId+"' size='8' style='text-align:right'  tabindex="+fieldId+"  value='"+sval+"'>" + hiddenFields;
	//cell5.innerHTML = "<input name='totalAmt_"+fieldId+"' type='text' id='totalAmt_"+fieldId+"' size='8' style='text-align:right;border:none;' readonly value='"+totQty+"' tabindex="+fieldId+">";
	cell6.innerHTML = "<textarea name='remark_"+fieldId+"' id='remark_"+fieldId+"' cols='10'  rows='2' class='input-textarea' tabindex="+fieldId+" >"+remarks+"</textarea>";
	cell7.innerHTML = imageButton;

	var newTextBox	=	document.getElementById("selStock_"+fieldId);
	newTextBox.focus();
	
	rowCountObj.value = parseInt(fieldId)+1;
	document.getElementById("hidItemCount").value = rowCountObj.value;
	fieldId = fieldId+1;
	initRow	= 2;
}

// setting the removing item status
function setStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("Status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("ItemRow_"+id).style.display = 'none';
		chkStockQtyExist();
	}
	return false;
}

function displayAmtInput(id)
{
	if (document.getElementById("selReason_"+id).value == "L" || document.getElementById("selReason_"+id).value == "S") {
		document.getElementById("scrapValue_"+id).value = "";	
		document.getElementById("scrapValue_"+id).style.display = 'none';
		//document.getElementById("totalAmt_"+id).value = "";
	}
	else document.getElementById("scrapValue_"+id).style.display = '';
}

function confirmRemoveItem()
{
	if( confirm("Do you wish to remove this item?") ) return true;
	return false;
}

function duplicateStockIssuance(mode,id)
{
	if (mode==1) {
		if (!validateRepeatReturn()) document.getElementById("selStock_"+id).value = "";
	}
}

function calculateTotalAmount(id)
{
	/*if( document.getElementById("selReason_"+id).value == 'S' || document.getElementById("selReason_"+id).value == 'L' ) document.getElementById("totalAmt_"+id).value = "";
	else 
	{
		if( document.getElementById("quantity_"+id).value !=""  && document.getElementById("scrapValue_"+id).value !="" )
		{
			var amt = parseInt(document.getElementById("quantity_"+id).value) * parseFloat( document.getElementById("scrapValue_"+id).value) ;
			document.getElementById("totalAmt_"+id).value = number_format(amt,2,".","");
		}
	}*/
	
	calcSubTotalValues();
}

function calcSubTotalValues()
{
	var rc = document.getElementById("rowCount").value;
	
	var qty = 0;
	var scrapVal = 0;
	var totAmt = 0;
	
	for (p=0; p<rc; p++) {
		var status  = document.getElementById("Status_"+p).value;
		if (status!='N') {
			qty = qty+parseInt(document.getElementById("quantity_"+p).value);
			var sv = document.getElementById("scrapValue_"+p).value; 
			//var ta = document.getElementById("totalAmt_"+p).value; 
			
			if( document.getElementById("scrapValue_"+p).value!='' ) scrapVal += parseFloat(sv);
			//if( document.getElementById("totalAmt_"+p).value!='' ) totAmt  += parseFloat(ta);
		}
	}	
	document.getElementById("subTotalQuantity").innerHTML = qty;
	document.getElementById("subTotalScrapVal").innerHTML = number_format(scrapVal,2,".","");
}
/* Disable button based on mode*/
function disableStockReturnButtons(mode)
{		
	if (mode == 1) {
		document.getElementById("cmdAdd2").disabled = true;
		document.getElementById("cmdAdd1").disabled = true;
	} else if( mode == 2) {
		document.getElementById('cmdSaveChange2').disabled = true;
		document.getElementById('cmdSaveChange1').disabled = true;
	}
}
/* Enable button based on mode*/
function enableStockReturnButtons(mode)
{
	if (mode == 1 ) {
		document.getElementById('cmdAdd2').disabled = false;
		document.getElementById('cmdAdd1').disabled = false;
	} else if( mode == 2) {
		document.getElementById('cmdSaveChange2').disabled = false;
		document.getElementById('cmdSaveChange1').disabled = false;
	}
}

// While changing the department
function  getDepartmentWiseStock(departmentId, rowCount, mode)
{
	
	for (i=0; i<rowCount ;i++ )
	{		
		xajax_checkStockIssued(departmentId,document.getElementById('selStock_'+i).value, document.getElementById('quantity_'+i).value, mode, i);
	}	
}