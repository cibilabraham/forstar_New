function validatePhtMonitoring(form)
{
	
	var date	=	form.date.value;
	var rmLotId	=	form.rmLotId.value;
	var supplier	=	form.supplier.value;
	var supplierGroupName	=	form.supplierGroupName.value;
	var specious	=	form.specious.value;
	var supplyQty	=	form.supplyQty.value;
	var phtCertificateNo	=	form.phtCertificateNo.value;
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
	if (phtCertificateNo=="") {
		alert("Please select phtCertificateNo.");
		form.phtCertificateNo.focus();
		return false;
	}
	

	
	
	if (!confirmSave()) return false;
	return true;

}


function addNewRow(tableId,MonitoringId,phtcertificateNo,phtQuantity,setoffQuantity,balanceQuantity,supplyQuantity)
{
	//alert(tableId);
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
	var imageButton = "<a href='###' onClick=\"setIssuanceItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	
	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='rmId_"+fieldId+"' id='rmId_"+fieldId+"' value='"+MonitoringId+"'>";
	
	var supplyQuantity="<input type='hidden' id='supplyQuantity' name='supplyQuantity' value='"+supplyQuantity+"' >";
	
	var phtcertificate			= "<select name='phtCertificateNo_"+fieldId+"' Style='display:display;' id='phtCertificateNo_"+fieldId+"' tabindex=1  onchange=\"xajax_Quantity(document.getElementById('phtCertificateNo_"+fieldId+"').value,document.getElementById('supplyQuantity').value,"+fieldId+");\">";
								phtcertificate+= "<option value='0'>--select--</option>";			  
											  <?php 
											  if (sizeof($phtCertificateRecords)>0) {
												foreach($phtCertificateRecords as $rm)
													{
														$phtCertificateId		=	$rm[0];
														$phtcertificateval	=	stripSlash($rm[1]);
												?>
												
											if (phtcertificateNo=="<?=$phtCertificateId?>")  var sel = "Selected";
									else var sel = "";

								phtcertificate += "<option value=\"<?=$phtCertificateId?>\" "+sel+"><?=$phtcertificateval?></option>";	
								<?php
										}
									}
									
								?>
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
	//if(mode=="addmode")
	//{
	//xajax_getDetails(document.getElementById('vehicleNo').value,'',fieldId,'');
	//}
	
	
	fieldId		= parseInt(fieldId)+1;
	document.getElementById("hidTableRowCount").value = fieldId;

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
		//alert(v);
		 if(v!="")
		 { 
		//alert(total);
			total = parseInt(total) + v;
			
		}
		
	 }
//alert(total);
if(parseInt(total)>supQnty)
{
alert("Sum of Set off Quantity cannot be more than Supply Quantity");

}

}
