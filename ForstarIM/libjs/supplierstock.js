function validateSupplierStock(form)
{
	
	var selSupplier		=	form.selSupplier.value;
	var selStock		=	form.selStock.value;
	var quotedPrice		=	form.quotedPrice.value;
	var negotiatedPrice	=	form.negotiatedPrice.value;
	var exciseRate		=	form.exciseRate.value;
	var cstRate		=	form.cstRate.value;
	var schedule		=	form.schedule.value;
	var stockType		=	form.stockType.value;
	var supplierRateList	= 	form.supplierRateList.value;
	var mode		= document.getElementById("hidMode").value;
	var startDate=form.startDate.value;
	if (mode==2)
	{
	var newstartDate=form.newstartDate.value;
	}

	
	if (selSupplier=="") {
		alert("Please select a Supplier.");
		form.selSupplier.focus();
		return false;
	}
	
	if (selStock=="") {
		alert("Please select a Stock Item.");
		form.selStock.focus();
		return false;
	}
	
	if( quotedPrice=="" )
	{
		alert("Please enter a Quoted Price.");
		form.quotedPrice.focus();
		return false;
	}
	
	if (negotiatedPrice=="") {
		alert("Please enter a Negotiated Price.");
		form.negotiatedPrice.focus();
		return false;
	}

	if (startDate=="")
	{
		alert("Please enter the startDate.");
		form.startDate.focus();
		return false;
	}
	
	/*if ( exciseRate=="" ) {
		alert("Please enter a Excise Rate.");
		form.exciseRate.focus();
		return false;
	}
	
	if (cstRate=="") {
		alert("Please enter a CST/VAT Rate.");
		form.cstRate.focus();
		return false;
	}
	
	if (schedule=="") {
		alert("Please select a Schedule Date.");
		form.schedule.focus();
		return false;
	}*/
	
	

	if (stockType == 'P') {
		
		var layerKgRate 	= 	form.layerKgRate.value;
		var layerConverRate	=	form.layerConverRate.value;
				
		var rowCount	=	form.hidLayerCount.value;
		
		var layer	=	"layerNo_";
		var quality	=	"paperQuality_";
		var brand	=	"layerBrand_";
		var gsm		=	"layerGsm_";
		var bf		=	"layerBf_";
		var cobb	=	"layerCobb_";
		
		for (i=0; i<rowCount; i++) {
			var layerNo			=	document.getElementById(layer+i);
			var layerQuality	=	document.getElementById(quality+i);
			var layerBrand		=	document.getElementById(brand+i);
			var layerGsm		=	document.getElementById(gsm+i);
			var layerBf			=	document.getElementById(bf+i);
			var layerCobb		=	document.getElementById(cobb+i);
				
			if (layerNo.value=="") {
				alert("Please enter Layer Number.");
				layerNo.focus();
				return false;
			}
			if (layerQuality.value=="") {
				alert("Please enter Layer Quality.");
				layerQuality.focus();
				return false;
			}
			if (layerBrand.value=="") {
				alert("Please enter Layer Brand.");
				layerBrand.focus();
				return false;
			}
			if (layerGsm.value=="") {
				alert("Please enter Layer GSM.");
				layerGsm.focus();
				return false;
			}
			if (layerBf.value=="") {
				alert("Please enter Layer BF.");
				layerBf.focus();
				return false;
			}
			if (layerCobb.value=="") {
				alert("Please enter Layer COBB.");
				layerCobb.focus();
				return false;
			}
				
			}
		if (layerKgRate=="") {
			alert("Please enter a Per Kg Rate.");
			form.layerKgRate.focus();
			return false;
		}
		
		if (layerConverRate=="") {
			alert("Please enter a Conversion Rate.");
			form.layerConverRate.focus();
			return false;
		}		
	}


	/*	
	if (supplierRateList=="") {
		alert("Please select a Rate List.");
		form.supplierRateList.focus();
		return false;
	}
	*/

	if (mode==2)
	{
		if (newstartDate=="")
		{
		alert("Please enter the New Start Date");
		form.newstartDate.focus();
		return false;
		}
		else
		{	
			var firstDate=startDate.split("/"); 
			var testArray = new Array();
			testArray[0]=firstDate[2];
			testArray[1]=firstDate[1];
			testArray[2]=firstDate[0];
			var dt1=testArray.join('-');
			var dateFirst = new Date(dt1);

			var secDate=newstartDate.split("/"); 
			var testArrays = new Array();
			testArrays[0]=secDate[2];
			testArrays[1]=secDate[1];
			testArrays[2]=secDate[0];
			var dt2=testArrays.join('-');
			var dateSecond = new Date(dt2);
			
			if(dateSecond>dateFirst)
			{
				
			}
			else
			{
				alert("New Start Date must be greater than Current Start Date");
				return false;
			}
		}
	}
	
	if (mode==2) {
		var hidenewstartDate=document.getElementById('hidenewstartDate').value;
		if(hidenewstartDate=="")
		{
			var confirmRateListMsg= confirm("Do you want to save this to new Rate list?");
			if (!confirmRateListMsg) {
				return false;
			}
		}
	}	

		//### last updated##############################################################################
		var hidTableRowCount	=	document.getElementById("hidTableRowCount2").value;

		var ccount = 0;
		for (i=0; i<hidTableRowCount; i++)
		{
			var statusUnit = document.getElementById("statusUnit_"+i).value;		    
	    	if (statusUnit!='N') 
		    {
				var companyId		=	document.getElementById("companyId_"+i);
				var punitId	=	document.getElementById("punitId_"+i);
			
				if( companyId.value == "" )
				{
					alert("Please enter a Company.");
					//chemicalQty.focus();
					return false;
				}
				
				if( punitId.value == "" )
				{
					alert("Please enter a Unit.");
					//chemicalQty.focus();
					return false;
				}	
			
			
		} else {
			ccount++;
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
		document.getElementById("selStock").disabled =false ;
		return true;
	}
}

//Validate repeated
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
	
	var vd = document.getElementById("hidTableRowCount2").value;
	var prevOrders = 0;
	
	var arry = new Array();
	var arriy=0;
	for( l=0; l<vd; l++ )	
	{
	    var status = document.getElementById("statusUnit_"+l).value;
	    if (status!='N') 
	    {
		var cm = document.getElementById("companyId_"+l).value;	
		var unt = document.getElementById("punitId_"+l).value;
		var cpunitId=cm+','+unt;
			if ( arry.indexOf(cpunitId) != -1 )	{
				alert("Company Name and Unit  Cannot be duplicate.");
				document.getElementById("punitId_"+l).focus();
				return false;
			}
			arry[arriy++]=cpunitId;
		}
	}
	return true;
}



function newLayer(mode)
{
	addNewLayer('tblNewLayer','','','','','','',mode);
}

function addNewLayer(tableId,layer,paperQuality,layerBrand,layerGsm,layerBf,layerCobb,mode)
{
	//alert(mode);
	fieldId=document.getElementById("hidLayerCount").value;
	var tbl		= document.getElementById(tableId);	
	var lastRow	= tbl.rows.length;
	var row		= tbl.insertRow(lastRow);
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "rows_"+fieldId;
	//alert("==================="+fieldIdStock);
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
	var cell6	= row.insertCell(5);
	var cell7	= row.insertCell(6);
	
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";
	cell6.className	= "listing-item"; cell6.align	= "center";
	cell7.className	= "listing-item"; cell7.align	= "center";
	
	var selWtType = "";
	var numLS="";
		
	var layerNo="<input id='layerNo_"+fieldId+"' type='text' style='text-align:center;' value='' size='2' name='layerNo_"+fieldId+"'>";	
	var paperQuality="<input id='paperQuality_"+fieldId+"' type='text' style='text-align:center;' value='' size='8' name='paperQuality_"+fieldId+"'>";	
	var layerBrand="<input id='layerBrand_"+fieldId+"' type='text' style='text-align:center;' value='' size='8' name='layerBrand_"+fieldId+"'>";	
	var layerGsm="<input id='layerGsm_"+fieldId+"' type='text' style='text-align:center;' value='' size='4' name='layerGsm_"+fieldId+"'>";	
	var layerBf="<input id='layerBf_"+fieldId+"' type='text' style='text-align:center;' value='' size='4' name='layerBf_"+fieldId+"'>";	
	var layerCobb="<input id='layerCobb_"+fieldId+"' type='text' style='text-align:center;' value='' size='4' name='layerCobb_"+fieldId+"'>";	

	var ds = "N";	
	var selBrandId="";
	var imageButton = "<a href='###' onClick=\"setPOItemStatusLayer('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='statusLayer_"+fieldId+"' type='hidden' id='statusLayer_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='newLayer_"+fieldId+"' id='newLayer_"+fieldId+"' value='1'><input id='hidLayerId_"+fieldId+"' type='hidden' value='' name='hidLayerId_"+fieldId+"'>";	
	//var hidOtherFields = "<input type='hidden' name='hidBrandId_"+fieldIdStock+"' id='hidBrandId_"+fieldIdStock+"' value='"+selBrandId+"'><input type='hidden' name='frznPkgFilledWt_"+fieldIdStock+"' id='frznPkgFilledWt_"+fieldIdStock+"' value='' readonly><input type='hidden' name='numPacks_"+fieldIdStock+"' id='numPacks_"+fieldIdStock+"' value=''><input type='hidden' name='frznPkgDeclaredWt_"+fieldIdStock+"' id='frznPkgDeclaredWt_"+fieldIdStock+"' value='' readonly><input type='hidden' name='frznPkgUnit_"+fieldIdStock+"' id='frznPkgUnit_"+fieldIdStock+"' value='' readonly>";
	
	

	cell1.innerHTML	= layerNo;
	cell2.innerHTML	= paperQuality;
	cell3.innerHTML	= layerBrand;
	cell4.innerHTML	= layerGsm;
	cell5.innerHTML	= layerBf;
	cell6.innerHTML	= layerCobb;
	cell7.innerHTML = imageButton+hiddenFields;	
	
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidLayerCount").value = fieldId;
	disableField();
	

}


function setPOItemStatusLayer(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("statusLayer_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("rows_"+id).style.display = 'none';
		disableField();
	}
	return false;
}

function newLayer_old() 
{
	document.frmSupplierStock.newline.value = '1';
	document.frmSupplierStock.submit();
}

// When select Drop Down Box Display Text
	function displaySupplierStockUnitPrice()
	{	//alert("hii");	
		var selUnit = document.getElementById("hidUnitName").value;
		if (selUnit!="") {
			//document.getElementById("displayMTxt").innerHTML = selUnit;
			// basic Unit Qty
			//document.getElementById("basicUnitQtyTxt").innerHTML = "("+selUnit+")";
			// Unit Price Per
			document.getElementById("unitPricePerTxt").innerHTML ="&nbsp;"+selUnit;
			// Unit Price Per item
			if (document.getElementById("unitPricePer").value!="") {
				document.getElementById("unitPricePerItemTxt").innerHTML = document.getElementById("unitPricePer").value+"&nbsp;"+selUnit;
			}
			// Unit Price Per One Item
			var calcUnitPerPerItem = 0;
			if (document.getElementById("unitPricePer").value>1 && document.getElementById("negotiatedPrice").value!="") {		
				document.getElementById("unitPricePerOneItemTxt").innerHTML = "&nbsp;"+selUnit;
				calcUnitPerPerItem = parseFloat(document.getElementById("negotiatedPrice").value/document.getElementById("unitPricePer").value);
				//alert(calcUnitPerPerItem);
				if (!isNaN(calcUnitPerPerItem)) {
					document.getElementById("unitPricePerOneItem").value = number_format(calcUnitPerPerItem,0,'','');
				}
			}
		}
		// Row Hide
		hidRowOfOneItemPrice();
	}

	// Hide rowOfOneItemPrice
	function  hidRowOfOneItemPrice()
	{
		var unitPricePer = document.getElementById("unitPricePer").value;
		if (unitPricePer>1) {
			document.getElementById("rowOfOneItemPrice").style.display = '';
		} else {
			document.getElementById("rowOfOneItemPrice").style.display ="none";
		}		
	}

// Checking any value changed
function supplierStockValueChanged()
{
	var priceModified = false;
	var scheduleModified = false;
	var rowCount = document.getElementById("hidRowCount").value;
	for (i=1; i<=rowCount; i++) {
		var negotiatedPrice    = document.getElementById("negotiatedPrice_"+i).value;
		var hidNegotiatedPrice = document.getElementById("hidNegotiatedPrice_"+i).value;
		var supplySchedule 	= document.getElementById("supplySchedule_"+i).value;
		var hidSupplySchedule   = document.getElementById("hidSupplySchedule_"+i).value;

		if (number_format(negotiatedPrice,2,'.','')!=hidNegotiatedPrice) {
			priceModified = true;
		}
		if (supplySchedule!=hidSupplySchedule) {
			scheduleModified = true;
		}
	}
		
	if (priceModified==true) {
		document.getElementById("priceModified").value = 1;
	} else {
		document.getElementById("priceModified").value = "";
	}
	if (scheduleModified==true) {
		document.getElementById("scheduleModified").value = 1;
	} else {
		document.getElementById("scheduleModified").value = "";
	}	
}

// Bulk Update
function  validateSupplierStockBulkUpdate()
{
		var supplierFilter 	   = document.getElementById("supplierFilter").value;
		var supplierRateListFilter = document.getElementById("supplierRateListFilter").value;
		var recordModified = false;		
		var rowCount = document.getElementById("hidRowCount").value;

		if (supplierFilter=="") {
			alert("Please select a Supplier");
			document.getElementById("supplierFilter").focus();
			return false;
		}
		if (supplierRateListFilter=="") {
			alert("Please select a Rate List");
			document.getElementById("supplierRateListFilter").focus();
			return false;
		}

		for (i=1; i<=rowCount; i++) {
			var negotiatedPrice    = document.getElementById("negotiatedPrice_"+i).value;
			var hidNegotiatedPrice = document.getElementById("hidNegotiatedPrice_"+i).value;
			var supplySchedule 	= document.getElementById("supplySchedule_"+i).value;
			var hidSupplySchedule   = document.getElementById("hidSupplySchedule_"+i).value;

			if (number_format(negotiatedPrice,2,'.','')!=hidNegotiatedPrice || supplySchedule!=hidSupplySchedule) {
				recordModified = true;
			}
			if (!isDigit(negotiatedPrice) && negotiatedPrice!="") {
				alert("Please enter a number in negotiated price");
				document.getElementById("negotiatedPrice_"+i).focus();
				return false;
			}
			if (!isDigit(supplySchedule) && supplySchedule!="") {
				alert("Please enter a number in Supply Schedule");
				document.getElementById("supplySchedule_"+i).focus();
				return false;
			}
		}
		if (!recordModified) {
			alert("No modifications to be applied");
			return false;
		}
		if (!confirmSave()) {
			return false;
		}
		return true;
}

	// Validate Revise PO
	function validateRevisePO()
	{
		var poSelected = false;
		var rowCount = document.getElementById("hidReviseRowCount").value;
		for (i=1; i<=rowCount; i++) {
			var poId = document.getElementById("poMainId_"+i).checked;
			if (poId!="") {
				poSelected = true;
			}
		}

		if (!poSelected) {
			alert(" Please select a purchase order");	
			return false;
		}
		if (!confirmSave()) {
			return false;
		}
		return true;
	}

	function enableSupplierStockButton(mode)
	{
		if (mode==1) {			
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
			document.getElementById("cmdAddAnother").disabled = false;			
			document.getElementById("cmdAddAnother1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableSupplierStockButton(mode)
	{		
		if (mode==1) {			
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
			document.getElementById("cmdAddAnother").disabled = true;			
			document.getElementById("cmdAddAnother1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

function addNewPOItem2(tableId,selCompanyId,selUnitId,selCompanyUnit,mode)
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
		
	var companyId	= "<select name='companyId_"+fieldIdStock+"' id='companyId_"+fieldIdStock+"' onchange=\"xajax_getUnit(document.getElementById('selStock').value,document.getElementById('companyId_"+fieldIdStock+"').value,'"+fieldIdStock+"','');\">";
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
		companyId+= "</select>";
	
	
	var unitId	= "<select name='punitId_"+fieldIdStock+"' id='punitId_"+fieldIdStock+"' onchange=\"getPackValue('"+fieldIdStock+"');\"><option value='0'>--Select--</option>";
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
	unitId += "</select>";






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
	


	var ds = "N";	
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
	//alert(mode);
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
	var rowCnt=document.getElementById("hidTableRowCount2").value;
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
		document.getElementById("selStock").disabled =true ;
	}
	else
	{
		document.getElementById("selStock").disabled =false ;
	}
}


function enableSubmit()
{
	document.getElementById("frmSupplierStock").submit();
}
