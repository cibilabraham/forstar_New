/*function validatePhtMonitoring(form)
{
	
	var date	=	form.date.value;
	var rmLotId	=	form.rmLotId.value;
	var supplier	=	form.supplier.value;
	var supplierGroupName	=	form.supplierGroupName.value;
	var specious	=	form.specious.value;
	var supplyQty	=	form.supplyQty.value;
	//var phtCertificateNo	=	form.phtCertificateNo.value;
	//var specious	=	form.specious.value;
	
	
	if (date=="") {
		alert("Please select date.");
		form.date.focus();
		return false;
	}
	if (rmLotId=="") {
		alert("Please select rmLotId.");
		form.rmLotId.focus();
		return false;
	}
	if (supplier=="") {
		alert("Please select supplier.");
		form.supplier.focus();
		return false;
	}
	if (supplierGroupName=="") {
		alert("Please select supplierGroupName.");
		form.supplierGroupName.focus();
		return false;
	}
	if (specious=="") {
		alert("Please display specious.");
		form.specious.focus();
		return false;
	}
	if (supplyQty=="") {
		alert("Please display supplyQty.");
		form.supplyQty.focus();
		return false;
	}
	// if (phtCertificateNo=="") {
		// alert("Please select phtCertificateNo.");
		// form.phtCertificateNo.focus();
		// return false;
	// }
	
	
	
	
	var itemCount	=	document.getElementById("hidTableRowCount").value;

		var count = 0;
		for (i=0; i<itemCount; i++)
		{
		   var status = document.getElementById("status_"+i).value;		    
	    	   if (status!='N') 
		    {
			var phtCertificateNo		=	document.getElementById("phtCertificateNo_"+i);
			var phtQuantity	=	document.getElementById("phtQuantity_"+i);
			var setoffQuantity	=	document.getElementById("setoffQuantity_"+i);
			var balanceQuantity	 	= 	document.getElementById("balanceQuantity_"+i);
			
			
			if( phtCertificateNo.value == "" )
			{
				alert("Please Select a PhtCertificate No.");
				phtCertificateNo.focus();
				return false;
			}	
			
			if( phtQuantity.value == "" )
			{
				alert("Please enter a Pht quantity.");
				phtQuantity.focus();
				return false;
			}	
			
			if( setoffQuantity.value == "" )
			{
				alert("Please enter a Setoff quantity.");
				setoffQuantity.focus();
				return false;
			}	
			if( balanceQuantity.value == "" )
			{
				alert("Please enter a Balance quantity.");
				balanceQuantity.focus();
				return false;
			}	
		} else {
			count++;
		}
	 }
	
	  if(!validateRepeatIssuance()){
	
		 return false;
	}
	
	
	if (!confirmSave()) return false;
	return true;

}*/
function validatePhtMonitoring(form)
{
	var inputStat=form.inputStat.value;
	//alert(inputStat);
	if(inputStat=='Certificate')
	{
		var phtCertificate	=	form.phtCertificate.value;
		var rmlotidCertify	=	form.rmlotidCertify.value;
		if (phtCertificate=="") 
		{
		alert("Please select pht cerificate.");
		form.phtCertificate.focus();
		return false;
		}

		if (rmlotidCertify=="") {
		alert("Please select RM Lot Id.");
		form.rmlotidCertify.focus();
		return false;
		}
		var checkStatus='0';
		var rowCnt	=	form.rowCnt.value;
		for(i=0; i<rowCnt; i++)
		{
			if(document.getElementById('weightmentId_'+i).checked)
			{
				checkStatus='1';
			}
		}
		if(i!='' && checkStatus=='0')
		{
			alert("Please select a record");
			return false;
		}
	}
	else if(inputStat=='Supplier')
	{
		var select_date	=	form.select_date.value;
		var rm_lot_id	=	form.rm_lot_id.value;

		if (select_date=="") {
		alert("Please select select_date.");
		form.select_date.focus();
		return false;
		}

		if (rm_lot_id=="") {
		alert("Please select RM Lot Id.");
		form.rm_lot_id.focus();
		return false;
		}
	
		var certifyStatus='0';
		var supplierRowCnt	=	form.supplierRowCnt.value;
		for(i=0; i<supplierRowCnt; i++)
		{
			if(document.getElementById('certificateNo_'+i).value!='')
			{
				certifyStatus='1';
				if(document.getElementById('availableQtySupplier_'+i).value=='0')
				{
					alert("CertificateNo with Available Qty 0 cannot be used");
					return false;
				}
			}
		}
		if(i!='' && certifyStatus=='0')
		{
			alert("Please select a certificateNo");
			return false;
		}
	}
	
	if (!confirmSave()) return false;
	return true;
}

function addNewRow(tableId,MonitoringId,phtcertificateNo,phtQuantity,setoffQuantity,balanceQuantity,supplyQuantity,mode)
{
	//alert(tableId);
	//alert(phtcertificateNo);
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
	/*var cell6			= row.insertCell(5);
	var cell7			= row.insertCell(6);
	var cell8			= row.insertCell(7);
	var cell9			= row.insertCell(8);
	var cell10			= row.insertCell(9);*/
  
	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	/*cell6.className	=	"fieldName"; cell6.align = "center";
	cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	cell9.className	=	"fieldName"; cell9.align = "center";
	cell10.className	=	"fieldName"; cell10.align = "center";*/
	/*cell11.className	=	"fieldName"; cell11.align = "center";*/
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceItemStatus('"+fieldId+"'); checkValue('"+fieldId+"')\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	
	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='rmId_"+fieldId+"' id='rmId_"+fieldId+"' value='"+MonitoringId+"'>";
	
	var supplyQuantity="<input type='hidden' id='supplyQuantity' name='supplyQuantity' value='"+supplyQuantity+"' >";
	
	var phtcertificate			= "<select name='phtCertificateNo_"+fieldId+"' Style='display:display;' id='phtCertificateNo_"+fieldId+"' tabindex=1  onchange=\"xajax_Quantity(document.getElementById('phtCertificateNo_"+fieldId+"').value,document.getElementById('supplyQuantity').value,"+fieldId+");\">";
								//phtcertificate+= "<option value='0'>--select--</option>";			  
											  <?php 
											  if (sizeof($phtCertificateRecords)>0) {
												//foreach($phtCertificateRecords as $rm)
													foreach($phtCertificateRecords as $phtCertificateId=>$phtcertificateval)
													{
														//$phtCertificateId		=	$rm[0];
														//$phtcertificateval	=	stripSlash($rm[1]);
												?>
												
											if (phtcertificateNo=="<?=$phtCertificateId?>")  var sel = "Selected";
									else var sel = "";

								phtcertificate += "<option value=\"<?=$phtCertificateId?>\" "+sel+"><?=$phtcertificateval?></option>";	
								<?php
										}
									} else {
									
								?>
									phtcertificate+= "<option value=''>--select--</option>";	
									<?php } ?>
								phtcertificate +="</select>";	
	
	//cell1.innerHTML	= driverName;
	//cell2.innerHTML	= vehicleNo;
	cell1.innerHTML	= phtcertificate;
	cell2.innerHTML	= "<input name='phtQuantity_"+fieldId+"' type='text' id='phtQuantity_"+fieldId+"' value='"+phtQuantity+"' size='4' readonly style='text-align:right; border:none;'/>";
	
	cell3.innerHTML	= "<input name='setoffQuantity_"+fieldId+"' type='text' id='setoffQuantity_"+fieldId+"' size='4' style='text-align:right' value='"+setoffQuantity+"' tabindex="+fieldId+" onkeyup='checkValue("+fieldId+");' >";
	cell4.innerHTML	= "<input name='balanceQuantity_"+fieldId+"' type='text' id='balanceQuantity_"+fieldId+"' size='4' readonly style='text-align:right; border:none;' tabindex="+fieldId+"  value='"+balanceQuantity+"'>";
	//cell5.innerHTML	= chemicalName;
	//cell6.innerHTML	= "<input name='chemicalQty_"+fieldId+"' type='text' id='chemicalQty_"+fieldId+"' value='"+chemicalQty+"' size='4' readonly style='text-align:right; border:none;'/>";
	//cell7.innerHTML	= "<input name='chemicalIssued_"+fieldId+"' type='text' id='chemicalIssued_"+fieldId+"' size='4' style='text-align:right' value='"+chemicalIssued+"' tabindex="+fieldId+" >"+ hiddenFields;
	cell5.innerHTML = imageButton+hiddenFields+supplyQuantity;
	if(mode=="")
	{
	//alert(mode);
	xajax_specious(document.getElementById('rmLotId').value,fieldId,'');
	}
	
	
	
	fieldId		= parseInt(fieldId)+1;
	document.getElementById("hidTableRowCount").value = fieldId;
	// var set=document.getElementById("hidTableRowCount").value; 
	// alert(set);
//code end
	
	
	
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

function checkValue(id)
{
var total=0;
var phtQnty=parseInt(document.getElementById("phtQuantity_"+id).value);
var setQnty=parseInt(document.getElementById("setoffQuantity_"+id).value);
var cntval=parseInt(document.getElementById("hidTableRowCount").value);
var supQnty=parseInt(document.getElementById("supplyQty").value);

if(setQnty > phtQnty)
{
alert("Value of Set off Quantity cannot be more than PHT Quantity");
}
if(setQnty!="")
{
var balanceQnty=phtQnty-setQnty;
document.getElementById("balanceQuantity_"+id).value=balanceQnty;
}

			
//alert(cntval);
 for(i=0; i<cntval; i++)
	{
	
		var v=parseInt(document.getElementById("setoffQuantity_"+i).value);
		var stsus=document.getElementById("status_"+i).value;
		if(v!="" && stsus!="N")
		{ 
			
			total = parseInt(total) + v;
				//alert(total);
		}
	}

if(parseInt(total)>supQnty)
{
alert("Sum of Set off Quantity cannot be more than Supply Quantity");

}
}
function validateRepeatIssuance()
{
//alert('aaa');
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
	//alert('aaa');
	    var status = document.getElementById("status_"+j).value;
	    if (status!='N') 
	    {
		var rv = document.getElementById("phtCertificateNo_"+j).value;	
		if ( arr.indexOf(rv) != -1 )	{
			alert("CertificateNo  Cannot be duplicate.");
			document.getElementById("phtCertificateNo_"+j).focus();
			return false;
		}
		arr[arri++]=rv;
            }
	}
	
	
	return true;	
}

////////////////////////////////////////////////////
function displayPopUp()
{
	$( "#dialog" ).dialog({ width: 370, resizable: true, modal: true   });
}

function checkBoxStatus()
{
	$(document).ready(function(){
		 var $unique = $('input.inputType');
		$unique.click(function() {
			$unique.filter(':checked').not(this).removeAttr('checked');
		});
		});
}

function addNewCertificateTableRow(rowCnt)
{
	addCertificate('tblAddCertificateDetail','','','','',rowCnt);
}

function addCertificate(tableId,editProcurmentVehicleId,VehicleId,VehicleNumber,mode,rowCnt)
{
	var fldId=document.getElementById("certificateSize").value;
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "crow_"+fldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
		
	cell1.id = "crNo_"+fldId;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	
	var allRMLotid=document.getElementById('rmlotid_0').innerHTML;
	var rmlotid	= "<select name='rmlotid_"+fldId+"' id='rmlotid_"+fldId+"' onchange=\"xajax_certificateNo(this.value,'"+fldId+"');\" >";
		rmlotid+=allRMLotid;	
		rmlotid += "</select>";	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceCertificateStatus('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='cstatus_"+fldId+"' type='hidden' id='cstatus_"+fldId+"' value=''><input name='IsFromDB_"+fldId+"' type='hidden' id='IsFromDB_"+fldId+"' value='"+ds+"'><input type='hidden' name='crmId_"+fldId+"' id='crmId_"+fldId+"' value=''>";
	cell1.innerHTML	= rmlotid;
	cell2.innerHTML = "<input id='cerificateQty_"+fldId+"' type='text' name='cerificateQty_"+fldId+"' value='' size='15' readonly style='text-align:right; border:none;'>";	
	cell3.innerHTML	= "<input id='adjustedCertificateQty_"+fldId+"' type='text' name='adjustedCertificateQty_"+fldId+"' value='' size='15'  style='text-align:right; border:none;'><input id='qntyStatusCertify_"+fldId+"' type='hidden' value='' tabindex='0' style='text-align:right; border:none;' size='15' name='qntyStatusCertify_"+fldId+"'>";
	cell4.innerHTML = imageButton+hiddenFields;	
	
	fldId		= parseInt(fldId)+1;	
	document.getElementById("certificateSize").value = fldId;	
//code end
}

function setIssuanceCertificateStatus(id)
{  
	if(id==0)
	{
		alert("Cannot delete first row");
	}
	else
	{
		if (confirmRemoveItem()) {
			document.getElementById("cstatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("crow_"+id).style.display = 'none';
		}
	}
	return false;
}

function addNewSupplierTableRow(rowCnt)
{
	addSupplier('tblAddSupplierDetail','','','','',rowCnt);
}

function addSupplier(tableId,editProcurmentVehicleId,VehicleId,VehicleNumber,mode,rowCnt)
{
	var fieldvalue=document.getElementById("supplierSize").value;
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "srow_"+fieldvalue;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
		
	cell1.id = "srNo_"+fieldvalue;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	
	var allCerificate=document.getElementById('certificateNo_0').innerHTML;
	var certificate	= "<select name='certificateNo_"+fieldvalue+"' id='certificateNo_"+fieldvalue+"' onchange=\"xajax_certificateNo(this.value,'"+fieldvalue+"','"+rowCnt+"');\" >";
		certificate+=allCerificate;	
		certificate += "</select>";	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceSupplierStatus('"+fieldvalue+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='sstatus_"+fieldvalue+"' type='hidden' id='sstatus_"+fieldvalue+"' value=''><input name='IsFromDB_"+fieldvalue+"' type='hidden' id='IsFromDB_"+fieldvalue+"' value='"+ds+"'><input type='hidden' name='srmId_"+fieldvalue+"' id='srmId_"+fieldvalue+"' value=''>";
	cell1.innerHTML	= certificate;
	cell2.innerHTML = "<input id='supplyQty_"+fieldvalue+"' type='text' name='supplyQty_"+fieldvalue+"' value='' size='15' readonly style='text-align:right; border:none;'>";	
	cell3.innerHTML	= "<input id='adjustedQty_"+fieldvalue+"' type='text' name='adjustedQty_"+fieldvalue+"' value='' size='15'  style='text-align:right; border:none;'><input id='qntyStatus_"+fieldvalue+"' type='hidden' value='' tabindex='0' style='text-align:right; border:none;' size='15' name='qntyStatus_"+fieldvalue+"'>";
	cell4.innerHTML = imageButton+hiddenFields;	
	
	fieldvalue		= parseInt(fieldvalue)+1;	
	document.getElementById("supplierSize").value = fieldvalue;	
	
	
//code end
	
}

function setIssuanceSupplierStatus(id)
{  
	if(id==0)
	{
		alert("Cannot delete first row");
	}
	else
	{
		if (confirmRemoveItem()) {
			document.getElementById("sstatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("srow_"+id).style.display = 'none';
		}
	}
	return false;
}

function displayData(row)
{
	var checkedSize=document.getElementById("checkedSize").value;
	if(checkedSize=="0")
	{
		document.getElementById("checkedSize").value=1;
		var certifyQnty=document.getElementById("certifyQnty").value;
		tableStructure();
	}
	else
	{	var rowSz=parseInt(checkedSize)-1;
		var certifyQnty=document.getElementById("balance_"+rowSz).value;
		document.getElementById("checkedSize").value=parseInt(checkedSize)+1;
	}
	var weightmentId=document.getElementById("weightmentId_"+row).value;
	document.getElementById("weightmentId_"+row).style.display='none';
	//alert(weightmentId);
	xajax_displaySelected(row,checkedSize,weightmentId,certifyQnty);

}

function tableStructure()
{
	var rowCnt=document.getElementById("rowCnt").value;
	xajax_displayTable(rowCnt);
}
function alloteData(row)
{
	var balanceQty=document.getElementById('balanceQty').innerHTML;
	var supplyQty=document.getElementById('supplyQty_'+row).innerHTML;
	var adjustedQty=document.getElementById('adjustedQty').innerHTML;
	if(document.getElementById('weightmentId_'+row).checked)
	{
		//alert(adjustedQty);
		if(parseInt(balanceQty)>=parseInt(supplyQty))
		{
			var newBalanceQty=parseInt(balanceQty)-parseInt(supplyQty);
			document.getElementById('balanceQty').innerHTML=newBalanceQty;
			document.getElementById('hideAdjustedQty_'+row).value=supplyQty;
			document.getElementById('hideBalanceQty_'+row).value=0;
			document.getElementById('hidePhtCertifyQty_'+row).value=balanceQty;
			document.getElementById('adjustedQty_'+row).innerHTML=supplyQty;
			document.getElementById('balanceQty_'+row).innerHTML=0;
			var newAdjustedQty=parseInt(adjustedQty)+parseInt(supplyQty);
			document.getElementById('adjustedQty').innerHTML=newAdjustedQty;
		}
		else if(parseInt(supplyQty)>parseInt(balanceQty))
		{
			//var newBalanceQty=0;
			document.getElementById('balanceQty').innerHTML=0;
			var newBalanceQty=parseInt(supplyQty)-parseInt(balanceQty);
			document.getElementById('hideAdjustedQty_'+row).value=balanceQty;
			document.getElementById('adjustedQty_'+row).innerHTML=balanceQty;
			document.getElementById('hideBalanceQty_'+row).value=newBalanceQty;
			document.getElementById('balanceQty_'+row).innerHTML=newBalanceQty;
			document.getElementById('hidePhtCertifyQty_'+row).value=balanceQty;
			var newAdjustedQty=parseInt(adjustedQty)+parseInt(balanceQty);
			document.getElementById('adjustedQty').innerHTML=newAdjustedQty;
		}
	}
	else
	{
		var hideAdjustedQty=document.getElementById('hideAdjustedQty_'+row).value;
		var newadjustedQty=parseInt(adjustedQty)-parseInt(hideAdjustedQty);
		document.getElementById('adjustedQty').innerHTML=newadjustedQty;
		document.getElementById('balanceQty').innerHTML=parseInt(balanceQty)+parseInt(hideAdjustedQty);
		document.getElementById('adjustedQty_'+row).innerHTML=0;
		document.getElementById('hideAdjustedQty_'+row).value=0;
		document.getElementById('hideBalanceQty_'+row).value=parseInt(supplyQty)-0;
		document.getElementById('balanceQty_'+row).innerHTML=parseInt(supplyQty)-0;
		document.getElementById('hidePhtCertifyQty_'+row).value=parseInt(document.getElementById('hidePhtCertifyQty_'+row).value)+parseInt(hideAdjustedQty);
		
	}

	if(document.getElementById('balanceQty').innerHTML=='0')
	{	var rowCnt=document.getElementById('rowCnt').value;
		for(i=(parseInt(row)+1); i<rowCnt; i++)
		{
			document.getElementById("weightmentId_"+i).style.display='none';
		}
	}
	//alert(supplyQty);
	//hidePhtCertifyQty=pht_quantity; 
	//hideAdjustedQty=setoff_quantity;
	//balance_quantity=hidePhtCertifyQty-hideAdjustedQty;
}

function adjustedQnty(availQty,row)
{
	//alert(supplyQty);
	var supplyQty=document.getElementById('supplyQty_'+row).innerHTML;
	var weightmentSupplier=document.getElementById('weightmentSupplier_'+row).value;
	var certificateId=document.getElementById('certificateNo_'+row).value;
	
	//alert(weightmentSupplier);
	if(parseInt(availQty)>parseInt(supplyQty))
	{
		document.getElementById('adjustedQtySupplier_'+row).value=supplyQty;
		var balanceQty=parseInt(availQty)-parseInt(supplyQty);
		document.getElementById('balanceQtySupplier_'+row).value=balanceQty;
		xajax_saveTemporary(weightmentSupplier,certificateId,availQty,supplyQty,balanceQty);
	}
	else if(parseInt(supplyQty)>parseInt(availQty))
	{
		var adjust=parseInt(supplyQty)-parseInt(availQty);
		document.getElementById('adjustedQtySupplier_'+row).value=availQty;
		document.getElementById('balanceQtySupplier_'+row).value=0;
		xajax_saveTemporary(weightmentSupplier,certificateId,availQty,availQty,0);
		//document.getElementById('adjustedQtySupplier_'+row).value=adjust;
	}

}


function setCerificateEdit(id)
{
	if (confirmRemoveItem())
	{
	//alert(document.getElementById("dIsFromDB_"+id).value);
		document.getElementById("dStatus_"+id).value = document.getElementById("dIsFromDB_"+id).value;
		document.getElementById("drow_"+id).style.display = 'none';
		changeFieldValue();
	}
	return false;
}
function changeFieldValue()
{
	var certificateCnt=document.getElementById("certificateCnt").value;
	var cerificateqtyOriginal=document.getElementById("cerificateqtyOriginal").value; 
	//alert(cerificateqtyOriginal);
	var adjustSum=0; var certificateBalance=''; 
	for(i=0; i<certificateCnt; i++)
	{	
		var dStatus=document.getElementById("dStatus_"+i).value;
		//alert(dStatus);
		if(dStatus!='N')
		{
			if(certificateBalance=='')
			{
				var hidesupplyQnt=document.getElementById("hidesupplyQnt_"+i).value; 
				var adjustedQty=document.getElementById("adjustedQty_"+i).value;
				//alert(adjustedQty);
				var balanceSupply=parseInt(hidesupplyQnt)-parseInt(adjustedQty);
				document.getElementById("balanceQty_"+i).value=balanceSupply;
				var certificateBalance=parseInt(cerificateqtyOriginal)-parseInt(adjustedQty);
				//alert(certificateBalance);
				document.getElementById("hideCertificateBalanceQty_"+i).value=certificateBalance;
				var adjust=parseInt(adjustedQty);
				adjustSum += adjust;	
				//alert(adjustSum);
			}
			else if(certificateBalance!='')
			{
				var hidesupplyQnt=document.getElementById("hidesupplyQnt_"+i).value; 
				var adjustedQty=document.getElementById("adjustedQty_"+i).value;
				var certificateBalance=parseInt(certificateBalance)-parseInt(adjustedQty);
				document.getElementById("hideCertificateBalanceQty_"+i).value=certificateBalance;
				adjust=parseInt(adjustedQty);
				adjustSum += adjust;
				//adjustSum+=adjustedQty;		
			}
			//alert(adjustSum);
		}
	}
	if(i==certificateCnt)
	{	//alert(i+'---'+adjustSum);
		document.getElementById('adjustedQty').innerHTML=adjustSum;
		var balanceQty=parseInt(cerificateqtyOriginal)-parseInt(adjustSum);
		document.getElementById('balanceQty').innerHTML=balanceQty;
	}
	//alert(certificateCnt);
}

function chkAdjustQty(id)
{
	var adjustedQty=document.getElementById('adjustedQty_'+id).value;
	var hideAdjustedQty=document.getElementById('hideAdjustedQty_'+id).value;
	var hidesupplyQnt=document.getElementById('hidesupplyQnt_'+id).value;
	var balanceQty=document.getElementById('balanceQty').value;
	if(parseInt(adjustedQty)>parseInt(hidesupplyQnt))
	{
		alert("cannot change value");
		document.getElementById('adjustedQty_'+id).value=hideAdjustedQty;
	}
	else
	{
		if(parseInt(hideAdjustedQty)>=parseInt(adjustedQty))
		{
			var balanceSupply=parseInt(hidesupplyQnt)-parseInt(adjustedQty);
			document.getElementById('balanceQty_'+id).value=balanceSupply;
			changeFieldValue();
		}
		else if(parseInt(hideAdjustedQty)<parseInt(adjustedQty))
		{
			var availBalance=parseInt(adjustedQty)-parseInt(hideAdjustedQty);
			if(balanceQty<availBalance)
			{
				alert("certificate doesnot have enough qty");
				document.getElementById('adjustedQty_'+id).value=hideAdjustedQty;
			}
			else
			{
				var balanceSupply=parseInt(hidesupplyQnt)-parseInt(adjustedQty);
				document.getElementById('balanceQty_'+id).value=balanceSupply;
				changeFieldValue();
			}
		}
	}

}

function formSubmit()
{
	document.getElementById("frmPhtMonitoring").submit();
}
