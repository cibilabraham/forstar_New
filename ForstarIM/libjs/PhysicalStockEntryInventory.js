function validatePhysicalStockEntry(form)
{
	//alert("hii");
	var CompanyName		=	form.CompanyName.value;
	var unit		=	form.unit.value;
	var stockDate		=	form.stockDate.value;

	if (CompanyName=="") {
		alert("Please select a Company Name.");
		form.CompanyName.focus();
		return false;
	}
	if (unit=="") {
		alert("Please select a unit.");
		form.unit.focus();
		return false;
	}

	if (stockDate=="") {
		alert("Please Enter Stock Date.");
		form.stockDate.focus();
		return false;
	}


	var hidStockQuantityRowCount	=	document.getElementById("hidStockQuantityRowCount").value;
	var scount = 0;
	for (i=0; i<hidStockQuantityRowCount; i++)
	{
		 var status = document.getElementById("statusUnit_"+i).value;		    
	     if (status!='N') 
		 {
			var itemId		=	document.getElementById("itemId_"+i);
			var supplierId		=	document.getElementById("supplierId_"+i);
			var stockQty		=	document.getElementById("stockQty_"+i);
			
			if( itemId.value == "" )
			{
				alert("Please Select a Stock.");
				itemId.focus();
				return false;
			}	

			if( supplierId.value == "" )
			{
				alert("Please Select a Supplier Name.");
				supplierId.focus();
				return false;
			}

			if( stockQty.value == "" )
			{
				alert("Please Select a Supplier Quantity.");
				stockQty.focus();
				return false;
			}
			
		} else {
			scount++;
		}
	 }

	if(!validateRepeatIssuance()){
		return false;
	}
	
	
	if (!confirmSave()) 
	{
		return false;
	}
	else
	{ 
		document.getElementById('CompanyName').disabled=false;
		document.getElementById('unit').disabled=false;
		var hidStockQuantityRowCount	=	document.getElementById("hidStockQuantityRowCount").value;
		for(i=0; i<hidStockQuantityRowCount; i++)
		{
			document.getElementById("itemId_"+i).disabled=false;
			document.getElementById("supplierId_"+i).disabled=false;
		}
		return true;
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
	
	

	var vd = document.getElementById("hidStockQuantityRowCount").value;
	var prevOrders = 0;
	
	var arry = new Array();
	var arriy=0;
	for( l=0; l<vd; l++ )	{
	    var status = document.getElementById("statusUnit_"+l).value;
	    if (status!='N') 
	    {
				
			var itemId = document.getElementById("itemId_"+l).value;	
			var supplierId = document.getElementById("supplierId_"+l).value;	
			var dv=itemId+','+supplierId;
			
			if (arry.indexOf(dv)!= -1 )	{
				alert("Combination of Item and  supplier Cannot be duplicate.");
				document.getElementById("itemId_"+l).focus();
				return false;
			}
		arry[arriy++]=dv;
            }
	}
	return true;
	
}

/*function addNewPOItem2(tableId,selCompanyId,selUnitId,selCompanyUnit,mode)
{
	//alert(mode);
	var tbl		= document.getElementById(tableId);	
	//alert("---"+tableId);
	//var lastRow	= tbl.rows.length-1;
	var lastRow	= tbl.rows.length;
	//lastRow=1;
	//alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	//fieldId2=fieldId;
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldIdStock;
	//alert("==================="+fieldIdStock);
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	
	//cell1.id = "srNo_"+fieldIdStock;	
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	
	var selWtType = "";
	var numLS="";
	// Copy Item
		
	var companyId	= "<select name='companyId_"+fieldIdStock+"' id='companyId_"+fieldIdStock+"' onchange=\"xajax_getUnit(document.getElementById('supplier').value,document.getElementById('item').value,document.getElementById('supplierStockId').value,document.getElementById('companyId_"+fieldIdStock+"').value,'"+fieldIdStock+"','');\">";
	if(fieldIdStock>0)
	{
		companyId+=document.getElementById('companyId_0').innerHTML;	
	}
	else
	{
		companyId+="<option value='0'>--Select--</option>";
	}
	//document.getElementById('companyId_0').html
		<?/* if (sizeof($companyRecords)>0) {	
				foreach ($companyRecords as $cmp=>$value) {
							$companyId = $cmp;
							$companyName	= stripSlash($value);
							
							
		?>	
			
			var company='<?=$companyId?>';
			if ((selCompanyId== "<?=$companyId?>" )|| (selCompanyId=="" && company==defaultCompany) ) var sel = "Selected";
			else var sel = "";

		companyId += "<option value=\"<?=$companyId?>\" "+sel+"><?=$companyName?></option>";	
		<?php
				}
			}
			*/
		?>
/*		companyId+= "</select>";
	
	
	var unitId	= "<select name='punitId_"+fieldIdStock+"' id='punitId_"+fieldIdStock+"' ><option value='0'>--Select--</option>";
	<? /* if (sizeof($plantUnitRecords)>0) {	
			foreach($plantUnitRecords as $dcw=>$pntVal) {
						$plantId = $dcw;
						$plantName	= stripSlash($pntVal);
						
						
	?>	
		if (selFishId== "<?=$plantId?>")  var sel = "Selected";
		else var sel = "";

	unitId += "<option value=\"<?=$plantId?>\" "+sel+"><?=$plantName?></option>";	
	<?php
			}
		}
		*/
	?>
/*	unitId += "</select>";






/* var stockQty = "<input name='stockQty_"+fieldIdStock+"' type='text' id='stockQty_"+fieldIdStock+"' value='"+selProcessCodeId+"'>";
	var packing	= "<select name='packing_"+fieldIdStock+"' id='packing_"+fieldIdStock+"'><option value=''>--Select--</option>";
<?php
		if (sizeof($mcpackingRecords)>0) {	
			foreach ($mcpackingRecords as $mcp) {
						$mcpackingId = $mcp[0];
						$mcpackingName	= stripSlash($mcp[1]);
						
						
	?>	
		if (selProcessCodeId== "<?=$mcpackingId?>")  var sel = "Selected";
		else
		var sel = "";

	packing  += "<option value=\"<?=$mcpackingId?>\" "+sel+"><?=$mcpackingName?></option>";	
	<?php
			}
		}
	?>
	packing += "</select>";

*/
	


/*	var ds = "N";	
	var selBrandId="";
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setPOItemStatusUnit('"+fieldIdStock+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='statusUnit_"+fieldIdStock+"' type='hidden' id='statusUnit_"+fieldIdStock+"' value=''><input name='IsFromDB_"+fieldIdStock+"' type='hidden' id='IsFromDB_"+fieldIdStock+"' value='"+ds+"'><input type='hidden' name='poEntryId_"+fieldIdStock+"' id='poEntryId_"+fieldIdStock+"' value=''>";	
//alert("entered1***"+fieldIdStock);
	//var hidOtherFields = "<input type='hidden' name='hidBrandId_"+fieldIdStock+"' id='hidBrandId_"+fieldIdStock+"' value='"+selBrandId+"'><input type='hidden' name='frznPkgFilledWt_"+fieldIdStock+"' id='frznPkgFilledWt_"+fieldIdStock+"' value='' readonly><input type='hidden' name='numPacks_"+fieldIdStock+"' id='numPacks_"+fieldIdStock+"' value=''><input type='hidden' name='frznPkgDeclaredWt_"+fieldIdStock+"' id='frznPkgDeclaredWt_"+fieldIdStock+"' value='' readonly><input type='hidden' name='frznPkgUnit_"+fieldIdStock+"' id='frznPkgUnit_"+fieldIdStock+"' value='' readonly>";
	
	var stkid="<input name='stockCmpUnitid_"+fieldIdStock+"' type='hidden' id='stockCmpUnitid_"+fieldIdStock+"' value='"+selCompanyUnit+"'>";	


	cell1.innerHTML	= companyId;
	cell2.innerHTML	= unitId;
	//cell2.innerHTML	= stockQty;
	//cell3.innerHTML = imageButton+hiddenFields+hidOtherFields+stkid;	
	cell3.innerHTML = imageButton+hiddenFields+stkid;	
	if(mode=="2")
	{	
		document.getElementById("companyId_"+fieldIdStock).value=''; 
	}
	fieldIdStock		= parseInt(fieldIdStock)+1;	
	document.getElementById("hidTableRowCount2").value = fieldIdStock;
	disableField();
	
	
	//assignSrNo();
	//if (cpyItem) calcTotalOrderVal();
}
*/

function addNewStock(tableId,selCompanyId,selUnitId,selCompanyUnit,mode)
{
	//alert(mode);
	var tbl		= document.getElementById(tableId);	
	//alert("---"+tableId);
	var lastRow	= tbl.rows.length;
	var row		= tbl.insertRow(lastRow);
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldIdStock;
	//alert("==================="+fieldIdStock);
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	
	//cell1.id = "srNo_"+fieldIdStock;	
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	
	var selWtType = "";
	var numLS="";
	// Copy Item
		
	var itemId	= "<select name='itemId_"+fieldIdStock+"' id='itemId_"+fieldIdStock+"' onchange=\"xajax_getSupplier(document.getElementById('CompanyName').value,document.getElementById('unit').value,document.getElementById('itemId_"+fieldIdStock+"').value,'"+fieldIdStock+"','');\">";
	if(fieldIdStock>0)
	{
		itemId+=document.getElementById('itemId_0').innerHTML;	
	}
	else
	{
		itemId+="<option value='0'>--Select--</option>";
	}
	itemId+= "</select>";
	
	
	var supplierId	= "<select name='supplierId_"+fieldIdStock+"' id='supplierId_"+fieldIdStock+"' onchange=\"xajax_getSupplierStockId(this.value,document.getElementById('itemId_"+fieldIdStock+"').value,'"+fieldIdStock+"',document.getElementById('CompanyName').value,document.getElementById('unit').value);\"><option value='0'>--Select--</option>";
	supplierId += "</select>";
	var stockQty = "<input name='stockQty_"+fieldIdStock+"' type='text' id='stockQty_"+fieldIdStock+"' value='' size='5'>";
	var ds = "N";	
	var selBrandId="";
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setPOItemStatusUnit('"+fieldIdStock+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='statusUnit_"+fieldIdStock+"' type='hidden' id='statusUnit_"+fieldIdStock+"' value=''><input name='IsFromDB_"+fieldIdStock+"' type='hidden' id='IsFromDB_"+fieldIdStock+"' value='"+ds+"'><input type='hidden' name='poEntryId_"+fieldIdStock+"' id='poEntryId_"+fieldIdStock+"' value=''>";	
	var stkid="<input name='supplierStockId_"+fieldIdStock+"' type='hidden' id='supplierStockId_"+fieldIdStock+"' value=''><input name='companyUnitId_"+fieldIdStock+"' type='hidden' id='companyUnitId_"+fieldIdStock+"' value=''><input name='physicalStockEntry_"+fieldIdStock+"' type='hidden' id='physicalStockEntry_"+fieldIdStock+"' value=''>";	
	cell1.innerHTML	= itemId;
	cell2.innerHTML	= supplierId;
	cell3.innerHTML	= stockQty;
	cell4.innerHTML = imageButton+hiddenFields+stkid;	
	//alert(mode);
	if(mode=="2")
	{	
		document.getElementById("itemId_"+fieldIdStock).value=''; 
	}
	fieldIdStock		= parseInt(fieldIdStock)+1;	
	document.getElementById("hidStockQuantityRowCount").value = fieldIdStock;
	disableField();
	
	
	//assignSrNo();
	//if (cpyItem) calcTotalOrderVal();
}




function setPOItemStatusUnit(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("statusUnit_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none';
		disableField();
	}
	return false;
}

/*disable or enable the stock field*/
function disableField()
{	
	var totalCnt=0;
	var rowCnt=document.getElementById("hidStockQuantityRowCount").value;
	for(i=0; i<rowCnt; i++)
	{
		var statusUnit=document.getElementById("statusUnit_"+i).value;
		if(statusUnit!='N')
		{
			totalCnt=totalCnt+1;	
		}
	}
	//alert(totalCnt);
	if((totalCnt>1) && (i==rowCnt))
	{
		document.getElementById("CompanyName").disabled =true ;
		document.getElementById("unit").disabled =true ;
	}
	else
	{
		document.getElementById("CompanyName").disabled =false ;
		document.getElementById("unit").disabled =false ;
	}
}
