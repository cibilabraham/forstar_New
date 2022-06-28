function validatePurchaseOrderInventory(form)
{	

	var company			=   form.company.value;
	if (company=="")
	{
		alert("Please select company");
		form.company.focus();
		return false;
	} 

	var unitpo			=   form.unitpo.value;
	if (unitpo=="")
	{
		alert("Please select unit");
		form.unitpo.focus();
		return false;
	}

	var selSupplier		=	form.selSupplier.value;
	if (selSupplier=="") {
		alert("Please select a Supplier.");
		form.selSupplier.focus();
		return false;
	}

	var poNumber		=	form.textfield.value;
	if( poNumber=='') 
	{
		alert("Please enter a Purchase Order ID.");
		form.textfield.focus();
		return false;
	}	
	
	/*var stockItem		= 	form.stockItem.value;
	var unitpo			=   form.unitpo.value;
	if (stockItem=="") {	
		var selSupplier		=	form.selSupplier.value;
		var poNumber		=	form.textfield.value;
		var genPoid = document.getElementById("genPoId").value;	
		if (unitpo=="")
		{
		alert("Please select unit");
		form.unitpo.focus();
		return false;
		}
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

	}*/
	
	var itemCount	= document.getElementById("hidTableRowCount").value;
	var columnCount = "";
	var count = 0;
	var orderItemStatus = true;
	for (i=0; i<itemCount; i++) {
	var status = document.getElementById("Status_"+i).value;
	var selStock  = document.getElementById("selStock_"+i).value;		
	if( status!='N' && selStock!="")
	{
		var selStock	=	document.getElementById("selStock_"+i);		
		var quantity	=	document.getElementById("quantity_"+i);
		var notover	=	document.getElementById("notover_"+i);
	//	var selPlant=document.getElementById("selPlant_"+i).value;
		var unitPrice=document.getElementById("unitPrice_"+i);
		//var stockItem		= 	form.stockItem.value;
		var hidStockStatus = document.getElementById("hidStockStatus_"+i).value;		
		//if (stockItem=="") {
			if (selStock.value == "") {
				alert("Please Select a Stock Item.");
				selStock.focus();
				return false;
			}

			if (unitPrice.value == "" || unitPrice.value==0) {
				alert("Unit Price Can't be zero");
				unitPrice.focus();
				return false;
			}
			
			if (quantity.value == "" || quantity.value==0) {
				alert("Please enter a quantity.");
				quantity.focus();
				return false;
			}

			if (notover.value == "" || notover.value==0) {
				alert("Please enter a notover quantity.");
				quantity.focus();
				return false;
			}

		/*	if (selPlant== "" || selPlant==0)
			{

				alert("Please select the Unit.");
				document.getElementById("selPlant_"+i).focus();
				return false;
			}
*/
			
		//}
		
	/*	if (stockItem!="") {
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
		}*/
		 if (hidStockStatus!="") {
			orderItemStatus = false;
		 }	
		} else {
			count++;
		}				
	 }

	var factory=document.getElementById('factory');
	var bearer=document.getElementById('bearer');
	if(factory.checked==false && bearer.checked==false)	 
	{
		alert("Please select any one of the option for delivering the item");
		return false;
	}

	
	if(document.getElementById('transportS').checked==false && document.getElementById('transportN').checked==false && document.getElementById('transportNA').checked==false)	 
	{
		alert("Please select any one of the option for Transport");
		return false;
	}

	if(document.getElementById('exciseS').checked==false && document.getElementById('exciseN').checked==false && document.getElementById('exciseNA').checked==false)	 
	{
		alert("Please select any one of the option for Excise");
		return false;
	}

	if(document.getElementById('vatS').checked==false && document.getElementById('vatN').checked==false && document.getElementById('vatNA').checked==false)	 
	{
		alert("Please select any one of the option for Vat");
		return false;
	}

	var delivarydate		=	form.delivarydate.value;
	if (delivarydate=="") {
		alert("Please select a  Delivary Date.");
		form.delivarydate.focus();
		return false;
	}


	if (itemCount==count) {
		alert("Please add atleast one Stock Item");
		return false;
	}
	if (orderItemStatus==false) {
		alert("Please check the stock order quantity");
		return false;
	}

	if (!validateRepeat()) {
		return false;
	}

	if(!confirmSave()){
		return false;
	}
	else
	{
		document.getElementById("company").disabled =false ;
		document.getElementById("unitpo").disabled =false ;
		document.getElementById("selSupplier").disabled =false ;
		return true;
	}
}


function checkboxSel()
{
	var atLeastOneIsChecked = false; 
	$('input.fsaChkbx:radiobutton').each(function () {
	if ($(this).is(':checked')) {
	atLeastOneIsChecked = true;      
    return false;
    }
});

  	if (!atLeastOneIsChecked){
		alert("Please select a Record from the table");
		return false;
	}
	return true;
}

function validatePurchaseOrder(form)
{
	
	if ((form.searchMode[0].checked==false ) && (form.searchMode[1].checked==false ))
	{
		alert("Please select an option");
		return false;
	}
	if (form.searchMode[1].checked==true )
	{
		if (form.itemSelect.value=="")
		{
			alert("Please select an item");
		return false;
		}

		if(!checkboxSel()){
		return false;
	}

	}
	else if (form.searchMode[0].checked==true )
	{
		if (form.supplierSelect.value=="")
		{
		alert("Please select a supplier");
		return false;
		}
	}	
}

function newLine() {
	document.frmPurchaseOrderInventory.newline.value = '1';
	document.frmPurchaseOrderInventory.submit();
}

function uncheckSelected(chkId,suppId)
{
	var rowCount	=	document.getElementById("hidTableRowCount").value;	
	for (i=0; i<rowCount; i++){
		var status = document.getElementById("Status_"+i).value;	
		var selStock  = document.getElementById("selStock_"+i).value;	
		if( status!='N' && selStock!=""){
			var columnCount = document.getElementById("hidSupplierCount_"+i).value;
			for (j=1; j<=columnCount; j++){
				if(suppId==i) document.getElementById("selSupplier_"+j+"_"+i).checked = false;
			}
		}
	}
	document.getElementById(chkId).checked = true;
}


function calculateTotal(form, poItem,i)
{
	var company	=	document.getElementById("company").value;	
	var plant	=	document.getElementById("unitpo").value;	
	var rowCount	=	document.getElementById("hidTableRowCount").value;	
	var total	= 0;	
	var pUnit	=	"unitPrice_";
	var pQty	=	"quantity_";
	var pTotal	=	"total_";
	var colCt  = "hidSupplierCount_";	
	var selSupplier=document.getElementById("selSupplier").value;
	var columnCount = "";
	var status = document.getElementById("Status_"+i).value;	
	var selStock  = document.getElementById("selStock_"+i).value;
	var quantity=document.getElementById("quantity_"+i).value;	
	xajax_getRowUnitPrice(selSupplier,'', '', 0,selStock,i,quantity);	
	xajax_getOtherSuppliersStockRec(selStock, selSupplier, poItem, i);
	xajax_getLastPurchaseStockRec(selStock, selSupplier, poItem, i);	
	xajax_getMinimumRequisitionQty(i,selStock,company,plant,quantity,selSupplier);

	xajax_hideFunction();
}

function getSum(form,poItem,i)
{
	
	var rowCount	=	document.getElementById("hidTableRowCount").value;
	var status = "";	
	var selStock  = "";		
	var total=0;
	var sum=0;
	var i=0;	
	for (i=0; i<rowCount; i++){
		status = document.getElementById("Status_"+i).value;	
		selStock  = document.getElementById("selStock_"+i).value;
		if( status!='N' && selStock!="") {
			total=parseInt(document.getElementById("total_"+i).value);		
			sum=parseInt(sum)+parseInt(total);
		}
	}
	if (!isNaN(total)) {		
	document.getElementById("totalQuantity").value = number_format(sum,2,'.','');
	}
}

// M<ultiply PO Item
function multiplyPOItem(form, poItem)
{
	//showFnLoading();
	var rowCount	=	document.getElementById("hidTableRowCount").value;	
	var total	= 0;	
	var pUnit	=	"unitPrice_";
	var pQty	=	"quantity_";
	var pTotal	=	"total_";
	var colCt  = "hidSupplierCount_";
	var columnCount = "";
	for (i=0; i<rowCount; i++)
	  {
	    var status = document.getElementById("Status_"+i).value;	
	    var selStock  = document.getElementById("selStock_"+i).value;
	    if( status!='N' && selStock!="") {
		if (poItem!="") {
			columnCount = document.getElementById(colCt+i).value;
			var negotiatedPrice = "";
			var selSupplier = "";
			for (j=1; j<=columnCount; j++)
	  		{				
				var nPrice    = "negoPrice_";
				var sSupplier = "selSupplier_";
				if(document.getElementById(sSupplier+j+"_"+i)==null ) continue;
				else {
					selSupplier = document.getElementById(sSupplier+j+"_"+i).checked;
					if (selSupplier) {
						negotiatedPrice = document.getElementById(nPrice+j+"_"+i).value;
					}
				}
			}
		}
		
		var quantity	= 0;		
	 	if (document.getElementById(pQty+i).value!="" && poItem!="") {
		var t  = negotiatedPrice * document.getElementById(pQty+i).value;
		if ( !isNaN(t))  document.getElementById(pTotal+i).value  = number_format(t,2,".","");
		} else if (document.getElementById(pQty+i).value!="" && poItem=="") {
		var t  = document.getElementById(pUnit+i).value * document.getElementById(pQty+i).value;			
		if ( !isNaN(t))  document.getElementById(pTotal+i).value = number_format(t,2,".","");
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
	//hideFnLoading();
}

//Validate repeated
function validateRepeat()
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
	var status = document.getElementById("Status_"+j).value;	
	if (status!='N')
	{
    var rv = document.getElementById("selStock_"+j).value;
        	if ( arr.indexOf(rv) != -1 )    {
            		alert("Stock Item cannot be duplicate.");
            		document.getElementById("selStock_"+j).focus();
            		return false;
        	}
        	arr[arri++]=rv;
	}
    }
    return true;
}

function printPurchaseOrderWindow(url,width,height)
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


//ADD MULTIPLE Item- ADD ROW START
function addNewStockItemRow(tableId, poItem, selStockId, qty, totalAmt, supplierRateListId, mode,selPlantId,notOver,description,unitprice,prindesc,stkQty)
{
	//alert("poItem"+poItem);

	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length-1;
	//var lastRow	= tbl.rows.length;
	var row		= tbl.insertRow(lastRow);
	var description=description;
	var unitprice=unitprice;
	var	prindesc=prindesc;
	var	notOver=notOver;
	//alert(mode);
	var mode=mode;
	//alert(mode);
	//if (mode==1)
	//{
		if (typeof description == 'undefined') description = "";
		if (typeof unitprice == 'undefined') unitprice = "";
		if (typeof prindesc == 'undefined') prindesc = "";
		if (typeof notOver == 'undefined') notOver="";
		//description=description;
		//unitprice="";
		//prindesc="";
		//notOver="";
	//}
	
	//var description=description;	
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
	var cell10	= row.insertCell(9);
	var cell11	= row.insertCell(10);
	var cell12	= row.insertCell(11);	

	cell1.className	= "listing-item"; cell1.align	= "center";
	if (poItem=="") {
	 cell2.className	= "listing-item"; cell2.align	= "center";
	} else {
	 cell2.style.display = "none";	
	}	
    cell3.className	= "listing-item"; cell3.align	= "center";
    cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";
	cell5.noWrap = "true"
	cell6.className	= "listing-item"; cell6.align	= "center";
	cell7.className	= "listing-item"; cell7.align	= "center";
	cell8.className	= "listing-item"; cell8.align	= "center";
	cell9.className	= "listing-item"; cell9.align	= "center";
	cell10.className	= "listing-item"; cell10.align	= "center";
	cell11.className	= "listing-item"; cell11.align	= "center";
	cell12.className	= "listing-item"; cell12.align	= "center";
	if (poItem=="") {
		supplierSelected = "document.getElementById('selSupplier').value";
	} else {
		supplierSelected = "''";
	}
	 var chk="";
	if (prindesc=="Yes"){
		chk="true";
	}
	else{
		chk="false";
	}	
	
	

	var selectStock	= "<select name='selStock_"+fieldId+"' id='selStock_"+fieldId+"' onchange=\"xajax_getAllRecords('"+fieldId+"',"+supplierSelected+",document.getElementById('selStock_"+fieldId+"').value,document.getElementById('unitpo').value,'<?=$poItem?>',document.getElementById('hidSupplierRateListId').value,document.getElementById('company').value,'"+mode+"');  xajax_getStockBalanceQty('"+fieldId+"',document.getElementById('selStock_"+fieldId+"').value,document.getElementById('selSupplier').value,document.getElementById('company').value,document.getElementById('unitpo').value);\" >";
	var sel;
	//alert(mode);
	if(mode=='1')
	{
		if(fieldId>0)
		{
			selectStock+=document.getElementById('selStock_0').innerHTML;	
		}
		else
		{
			selectStock+="<option value='0'>--Select--</option>";
		}
	}
	else if(mode=='0')
	{
		<?php //printr($data);
			if (sizeof($data)>0) {	
				foreach($data as $stockId=>$stockName) {			
		?>	
			if (selStockId== "<?=$stockId?>")  var sel = "Selected";
			else var sel = "";
		selectStock+= "<option value=\"<?=$stockId?>\" "+sel+"><?=$stockName?></option>";	
		<?php
				}
			}
		?>
	}
	selectStock+= "</select>";	
	
	
	/*var selectPlant	= "<select name='selPlant_"+fieldId+"' id='selPlant_"+fieldId+"' \">";
	<?php
		if (sizeof($dataPlant)>0) {	
		foreach($dataPlant as $plantId=>$plantName) {				
	?>	
		if (selPlantId== "<?=$plantId?>")  var sel = "Selected";
		else var sel = "";
	selectPlant += "<option value=\"<?=$plantId?>\" "+sel+"><?=$plantName?></option>";	
	<?php
	}
	}
	?>
	selectPlant += "</select>";	*/
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setPOItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='Status_"+fieldId+"' type='hidden' id='Status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'>";	
	cell1.innerHTML	= selectStock;
	cell2.innerHTML	= "<input name='unitPrice_"+fieldId+"' type='text' id='unitPrice_"+fieldId+"' value='"+unitprice+"' size='6' style='text-align:right' readonly>";
	cell3.innerHTML	= "<input name='quantity_"+fieldId+"' type='text' id='quantity_"+fieldId+"' value='"+qty+"' size='6' style='text-align:right' autoComplete='off' onKeyUp=\"xajax_getMinimumRequisitionQty("+fieldId+",document.getElementById('selStock_"+fieldId+"').value,document.getElementById('company').value,document.getElementById('unitpo').value,document.getElementById('quantity_"+fieldId+"').value,document.getElementById('selSupplier').value); return multiplyPOItem(document.frmPurchaseOrderInventory,'<?=$poItem?>');\" onchange=\"xajax_chkStockItemOrderQty(document.getElementById('selStock_"+fieldId+"').value, "+fieldId+",document.getElementById('quantity_"+fieldId+"').value, '"+mode+"');\"><input name='hidSelStock_"+fieldId+"' type='hidden' id='hidSelStock_"+fieldId+"' readonly value='"+selStockId+"'>"+hiddenFields+"<input name='hidStockStatus_"+fieldId+"' type='hidden' id='hidStockStatus_"+fieldId+"'> <span id='orderQtyDivId_"+fieldId+"' style='line-height:normal; font-size:10px; color:red;'></span>";
	cell4.innerHTML	= "<input name='minimumRequiredQty_"+fieldId+"' type='text' id='minimumRequiredQty_"+fieldId+"' size='7' readonly style='text-align:right' ><div id='requirementStatus_"+fieldId+"' style='line-height:normal; font-size:10px; color:red;'></div>";
	cell5.innerHTML	= "<input name='total_"+fieldId+"' type='text' id='total_"+fieldId+"' size='7' readonly style='text-align:right' value='"+totalAmt+"'>";
	cell6.innerHTML	= "<div id='balanceQty_"+fieldId+"'></div>";	
	cell7.innerHTML	= "<div id='otherSupplierDiv_"+fieldId+"'></div>";
	cell8.innerHTML	= "<div id='LastPurchaseDiv_"+fieldId+"'></div>";
	cell9.innerHTML	= "<input type='text' name='notover_"+fieldId+"' id='notover_"+fieldId+"' value='"+notOver+"'  />";
	cell10.innerHTML = "<textarea name='proddesc_"+fieldId+"' id='proddesc_"+fieldId+"' >"+description+"</textarea>";
	if (chk=="true"){
		cell11.innerHTML = "<input type='checkbox' name='printdesc_"+fieldId+"' value='Yes' checked='true' >";
	}
	else{
	cell11.innerHTML = "<input type='checkbox' name='printdesc_"+fieldId+"' value='Yes'  >";	
	}
	cell12.innerHTML = imageButton;

	if(mode=="2")
	{	
		document.getElementById("selStock_"+fieldId).value=''; 
	}
	//cell1.innerHTML = selectPlant;	
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;
	if(mode!='0')
	{
		disableField();
	}
}

function setPOItemStatus(id)
{
	if( confirmRemoveItem() )
	{
		document.getElementById("Status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none';
		multiplyPOItem('','<?=$poItem?>');
	}
	return false;
}

function enablePOButton(mode)
{
	if (mode==1) {
		document.getElementById("cmdAdd").disabled = false;
		document.getElementById("cmdAdd1").disabled = false;
	} else if (mode==0) {
		document.getElementById("cmdSaveChange").disabled = false;
		document.getElementById("cmdSaveChange1").disabled = false;
	}
}

function disablePOButton(mode)
{		
	if (mode==1) {
		document.getElementById("cmdAdd").disabled = true;
		document.getElementById("cmdAdd1").disabled = true;
	} else if (mode==0) {
		document.getElementById("cmdSaveChange").disabled = true;
		document.getElementById("cmdSaveChange1").disabled = true;
	}
}

/*disable or enable the stock field*/
function disableField()
{	
	var totalCnt=0;
	var rowCnt=document.getElementById("hidTableRowCount").value;
	for(i=0; i<rowCnt; i++)
	{
		var Status=document.getElementById("Status_"+i).value;
		if(Status!='N')
		{
			totalCnt=totalCnt+1;	
		}
	}
	//alert(totalCnt);
	if((totalCnt>1) && (i==rowCnt) )
	{
		document.getElementById("company").disabled =true ;
		document.getElementById("unitpo").disabled =true ;
	}
	else
	{
		document.getElementById("company").disabled =false ;
		document.getElementById("unitpo").disabled =false ;
	}

}

function getSupplierRows()
{

/*document.getElementById("supRows1").style.display="block";
document.getElementById("supRows2").style.display="block";
document.getElementById("supRows3").style.display="block";
document.getElementById("supRows4").style.display="block";
document.getElementById("supRows5").style.display="block";
document.getElementById("supRows6").style.display="block";*/
}

function showItemList(formObj)
{
//showFnLoading(); 
//formObj.form.submit();
//alert("entered");

document.getElementById("showSp").style.display="none";
document.getElementById("showIt").style.display="block";
//document.getElementById("showItdetails").style.display="block";

}


function showSupplierList(formObj)
{
//showFnLoading(); 
//formObj.form.submit();

document.getElementById("showSp").style.display="block";
document.getElementById("showIt").style.display="none";

//document.getElementById("showItdetails").style.display="none";

}


function getOtherSupplierStockRecords(selStock1,selSupplier1,poitem1,supplierRateListId1,hidTableRowCount1,hidSelStock1,mode1)
{
	
	xajax_showFunction();
	xajax_getSupplierStockRecordsAll(selStock1,selSupplier1,poitem1,supplierRateListId1,hidTableRowCount1,hidSelStock1,mode1);	
	xajax_showFunction();	
	xajax_getnetTotal();	
	xajax_hideFunction();
}


function getNetTotalerr()
{
	
var cntArr = new Array();
var cntArrStr="";
var selSupplier=document.getElementById("selSupplier").value;
var rowCount	=	document.getElementById("hidTableRowCount").value;	
var total	= 0;
var sum=0;
var j=0;
for (i=0; i<rowCount; i++){
		var status = document.getElementById("Status_"+i).value;	
		var selStock  = document.getElementById("selStock_"+i).value;
		var quantity  = document.getElementById("quantity_"+i).value;		
		var joinCnt="";
		if( status!='N' && selStock!=""){	
		joinCnt   =  selStock+":"+quantity+":"+selSupplier;
		cntArr[j] = joinCnt;
		j++;
}
}

cntArrStr = cntArr.join(",");
xajax_calculateNetTotalAmount(cntArrStr);
xajax_hideFunction();
}
function delay()
{
showFnLoading();
}

function undelay()
{
hideFnLoading(); 		
}


//get the details of items ie, price,quantity....
/*function getAllRecords(fieldid,supplierSelected,stockid,plantid,poitem,supplierRateListId,mode)
{	
	//alert(fieldid+'--,--'+supplierSelected+'--,--'+stockid+'--,--'+plantid+'--,--'+poitem+'--,--'+supplierRateListId+'--,--'+mode);
	xajax_getAllRecords(fieldid,supplierSelected,stockid,plantid,poitem,supplierRateListId,mode);
}*/

//commented on 21-3-2015
/*function getAllRecords_old(fieldid,supplierSelected,stockid,plantid,poitem,supplierRateListId,mode){	
	xajax_showFunction();
	xajax_getStockUnitRate(supplierSelected,stockid,fieldid,supplierRateListId,mode); 
	xajax_getStockMinimumOrderQty(stockid,fieldid);		
	xajax_getStockBalanceQty(stockid,fieldid,plantid);	
	xajax_getOtherSuppliersStockRec(stockid,supplierSelected,poitem,fieldid);
	xajax_getLastPurchaseStockRec(stockid,supplierSelected,poitem,fieldid);	
	xajax_getitemDescription(stockid,fieldid);	
	xajax_hideFunction();
}*/
/*function getAllUnitRecords(fieldid,stockid,plantid){
xajax_showFunction();
xajax_getStockBalanceQty(stockid,fieldid,plantid);
xajax_hideFunction();
}*/
function itemLoad(formObj)
	{
		//alert(formObj)
		showFnLoading(); 
		formObj.form.submit();
		//hideFnLoading();
		
	}

	function supplierLoad(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();
		//hideFnLoading();
	}
	function pageLoad(formObj)
	{
		formObj.form.submit();
		//document.myform.submit();
	}

	