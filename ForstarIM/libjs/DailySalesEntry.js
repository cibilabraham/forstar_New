function validateDailySalesEntry(form)
{
	var rtCounterSelected = false;
	var selSalesStaff = form.selSalesStaff.value;
	var entryDate		= form.entryDate.value;
	var editMode		= form.editMode.value;
	
	if (entryDate=="") {
		alert("Please select a entry date.");
		form.entryDate.focus();
		return false;
	}

	if (selSalesStaff=="") {
		alert("Please select a Sales Staff.");
		form.selSalesStaff.focus();
		return false;
	}

	var mainTableRowCount	=	document.getElementById("hidMainTableRowCount").value;

	for (i=0; i<mainTableRowCount; i++) {
	   var status = document.getElementById("status_"+i).value;	
       	   if (status!='N') {
		var selRtCounter =	document.getElementById("selRtCounter_"+i);
		var visitDate	=	document.getElementById("visitDate_"+i);
		var stkTableRowCount = document.getElementById("hidStkTbleRowCount_"+i).value;
					
		if (selRtCounter.value!="") {	
			if (selRtCounter.value == "") {
				alert("Please select a Retail Counter.");
				selRtCounter.focus();
				return false;
			}

			if (visitDate.value == "") {
				alert("Please select a visit date.");
				visitDate.focus();
				return false;
			}
			if (!salesTimeCheck(i)) {
				alert("Please enter a time");
				return false;
			}
			var productSelected = false;
			// Stk Table validation
			for (j=1;j<=stkTableRowCount;j++) {
				//var stkStatus = document.getElementById("status_"+j+"_"+i).value;	
       	   			//if (stkStatus!='N') {
					var selProduct = document.getElementById("selProduct_"+j+"_"+i);
					var numStock  = document.getElementById("numStock_"+j+"_"+i);
					var numOrder  = document.getElementById("numOrder_"+j+"_"+i);
					if (selProduct.value!="") {	
						if (selProduct.value == "") {
							alert("Please select a Product.");
							selProduct.focus();
							return false;
						}
						if (numStock.value == "") {
							alert("Please enter number of stock.");
							numStock.focus();
							return false;
						}
						if (numOrder.value == "") {
							alert("Please enter a order.");
							numOrder.focus();
							return false;
						}
			
						productSelected = true;
					}
				//}				
			}
			// Checking Duplication
			/*
			if (!validateSelProductRepeat(i)) {				
				return false;	
			}
			*/

			if (!productSelected) {
				alert("Please select atleast one Product");
				return false;
			}		
			rtCounterSelected = true;	// Checking Reail Counter Selected
		}
	  }					
	}

	if (!rtCounterSelected) {
		alert("Please select atleast one Retail Counter");
		return false;
	}	

	if (!validateRtCounterRepeat()) {
		return false;
	}

	if (!confirmSave()) {
		return false;
	}
	return true;
}

//Validate repeated
function validateRtCounterRepeat()
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
	
    var rc = document.getElementById("hidMainTableRowCount").value;
    var prevOrder = 0;
    var arr = new Array();
    var arri=0;

    for( j=0; j<rc; j++ )    {
	var status = document.getElementById("status_"+j).value;	
       if (status!='N')
       {
        var rv = document.getElementById("selRtCounter_"+j).value;
        if ( arr.indexOf(rv) != -1 )    {
            alert("Please make sure the selected Retail Counter is not duplicate.");
            document.getElementById("selRtCounter_"+j).focus();
            return false;
        }
        arr[arri++]=rv;
     }
    }
    return true;
}

//Validate repeated
/*
function validateSelProductRepeat(cId)
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
	
    var rc = document.getElementById("hidStkTbleRowCount_"+cId).value;
    var prevOrder = 0;
    var arr = new Array();
    var arri=0;

    for( j=0; j<rc; j++ )    {
	var status = document.getElementById("status_"+j+"_"+cId).value;	
       if (status!='N') {
        	var rv = document.getElementById("selProduct_"+j+"_"+cId).value;
        	if ( arr.indexOf(rv) != -1 )    {
            		alert("Please make sure the selected Product is not duplicate.");
            		document.getElementById("selProduct_"+j+"_"+cId).focus();
            		return false;
        	}
        arr[arri++]=rv;
     }
    }
    return true;
}
*/
//ADD MULTIPLE Item- ADD ROW START
/*
cPos -> Setting the position of the row
*/
function addNewRtCounterRow(tableId, retailCounterId, vDate, vTime, schemeId, poNum, orderValue, mode, cPos)
{	
	//alert(cPos);
	var tbl		= document.getElementById(tableId);

	if (cPos==1) var lastRow	= tbl.rows.length-2;	
	else if (cPos==0)  var lastRow	= tbl.rows.length-(2+fieldId);

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
		
	cell1.className	= "listing-item"; cell1.align	= "center";	// Rt Counter
	cell2.className	= "listing-item"; cell2.align	= "center";	// Date of visit
	cell3.className	= "listing-item"; cell3.align	= "center"; cell3.noWrap = "true";
        cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";
	cell6.className	= "listing-item"; cell6.align	= "center";
	cell7.className	= "listing-item"; cell7.align	= "center";
	cell8.className	= "listing-item"; cell8.align	= "center";
	cell9.className	= "listing-item"; cell9.align	= "center";
			
	var selectRtCounter = "<select name='selRtCounter_"+fieldId+"' id='selRtCounter_"+fieldId+"' onchange=\"xajax_getBalStkOfRtCt(document.getElementById('selRtCounter_"+fieldId+"').value, document.getElementById('hidStkTbleRowCount_"+fieldId+"').value, '"+fieldId+"');xajax_chkRtSchemeEligible(document.getElementById('selRtCounter_"+fieldId+"').value,'"+fieldId+"');xajax_disChargeEligible(document.getElementById('selRtCounter_"+fieldId+"').value,'"+fieldId+"');\"><option value=''>--Select--</option>";
	<?
		while ($dr=$retailCounterResultSetObj->getRow()) {
			$retailCounterId	= $dr[0];
			$retailCounterCode 	= stripSlash($dr[1]);
			$retailCounterName 	= stripSlash($dr[2]);
	?>
		if (retailCounterId== "<?=$retailCounterId?>")  var sel = "Selected";
		else var sel = "";

	selectRtCounter += "<option value=\"<?=$retailCounterId?>\" "+sel+"><?=$retailCounterName?></option>";
	<? 
		}
	?>
	selectRtCounter += "</select>";

	var eTime = vTime.split("-");
	var eTimeHour = eTime[0];
	var eTimeMinit = eTime[1];
	var eTimeOption = eTime[2];
	//alert("A="+eTime+eTimeHour+"-"+eTimeMinit+"-"+eTimeOption);
		if (eTime=="") var selectTimeHour = <?=date("g")?>; 
		else var selectTimeHour	= eTimeHour;

	var visitTime = "<input type=\"text\" id='selectTimeHour_"+fieldId+"' name='selectTimeHour_"+fieldId+"' size=\"1\" value='"+selectTimeHour+"' onchange=\"return timeCheck();\" style=\"text-align:center;\">&nbsp:&nbsp;";
		if (eTime=="") var selectTimeMints = <?=date("i")?>; 
		else var selectTimeMints	= eTimeMinit;
	visitTime += "<input type=\"text\" id='selectTimeMints_"+fieldId+"' name='selectTimeMints_"+fieldId+"' size=\"1\" value='"+selectTimeMints+"' onchange=\"return timeCheck();\" style=\"text-align:center;\">&nbsp;";
		if (eTime=="") var timeOption = "<?=date('A')?>"; 
		else var timeOption	= eTimeOption;
		if (timeOption=='AM')  var selAM = "selected";
		else var selAM = "";
		if (timeOption=='PM')  var selPM = "selected";
		else var selAM = "";
	visitTime += "<select name='timeOption_"+fieldId+"' id='timeOption_"+fieldId+"'><option value=\"AM\" "+selAM+">AM</option><option value=\"PM\" "+selPM+">PM</option></select>";
	
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setRtCtItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'>";	
	
	cell1.innerHTML	= selectRtCounter;
	cell2.innerHTML	= "<input name='visitDate_"+fieldId+"' type='text' id='visitDate_"+fieldId+"' value='"+vDate+"' size='8' style='text-align:right' autoComplete='off'>";
	cell3.innerHTML	= visitTime + hiddenFields+"";
	cell4.innerHTML = "<div id='disChargeAvailDiv_"+fieldId+"''></div>";	

	var productList = "<table  cellspacing=\"1\" bgcolor=\"#999999\" cellpadding=\"3\" id='stkTable_"+fieldId+"'><tr bgcolor=\"#f2f2f2\" align=\"center\"><td class=\"listing-head\" style='line-height:normal;font-size:11px;'>Product</td><td class=\"listing-head\" nowrap style='line-height:normal;font-size:11px;'>Stock</td><td class=\"listing-head\" style='line-height:normal;font-size:11px;'>Order</td><td class=\"listing-head\" style='line-height:normal;font-size:11px;'>Bal. Stk</td></tr>";	
	<?		
		$j=0;	
		$numStock		= "";
		$numOrder		= "";
		$balStk			= "";
		foreach ($getMrpProductRecs as $pmr) {	
			$j++;	
			$comboMatrixRecId 	= $pmr[0];
			$productCode		= $pmr[1];
			$productName		= $pmr[2];
			// Edit Mode
			$numStock		= ($pmr[4]!="")?$pmr[4]:0;
			$numOrder		= ($pmr[5]!="")?$pmr[5]:0;
			$balStk			= ($pmr[6]!="")?$pmr[6]:0;
	?>
	if (mode==2) {
		var numStock = <?=$numStock?>;	
		var numOrder = <?=$numOrder?>;
		var balStk   = <?=$balStk?>;	
		xajax_getOrderValue(retailCounterId,<?=$comboMatrixRecId?>, numOrder, '<?=$j?>',fieldId);
	} else {
		var numStock = "";
		var numOrder = "";
		var balStk   = "";
	}
	productList += "<tr bgcolor='white'><td class='listing-item' noWrap><?=$productName?><input type='hidden' name='selProduct_<?=$j?>_"+fieldId+"' id='selProduct_<?=$j?>_"+fieldId+"' value='<?=$comboMatrixRecId?>'></td>";
	productList += "<td><input name='numStock_<?=$j?>_"+fieldId+"' type='text' id='numStock_<?=$j?>_"+fieldId+"' value='"+numStock+"' size='6' style='text-align:right' autoComplete='off' onchange=\"xajax_getBalStock(document.getElementById('selRtCounter_"+fieldId+"').value, document.getElementById('selProduct_<?=$j?>_"+fieldId+"').value, document.getElementById('numStock_<?=$j?>_"+fieldId+"').value, '<?=$j?>', '"+fieldId+"');\"></td>";
	productList += "<td><input name='numOrder_<?=$j?>_"+fieldId+"' type='text' id='numOrder_<?=$j?>_"+fieldId+"' value='"+numOrder+"' size='6' style='text-align:right' autoComplete='off' onchange=\"xajax_getOrderValue(document.getElementById('selRtCounter_"+fieldId+"').value, document.getElementById('selProduct_<?=$j?>_"+fieldId+"').value, document.getElementById('numOrder_<?=$j?>_"+fieldId+"').value, '<?=$j?>', '"+fieldId+"');\"></td>";
	productList += "<td><input name='balStk_<?=$j?>_"+fieldId+"' type='text' id='balStk_<?=$j?>_"+fieldId+"' size='8' readonly style='text-align:right' value='"+balStk+"'><input name='productValue_<?=$j?>_"+fieldId+"' type='hidden' id='productValue_<?=$j?>_"+fieldId+"' size='8' readonly style='text-align:right'></td>";	
	productList += "</tr>";
	<? 
		}
	?>
	productList += "</table><input type='hidden' name='hidStkTbleRowCount_"+fieldId+"' id='hidStkTbleRowCount_"+fieldId+"' value='<?=$j?>'>";
	//cell4.innerHTML = "<table  cellspacing=\"1\" bgcolor=\"#999999\" cellpadding=\"3\" id='stkTable_"+fieldId+"'><tr bgcolor=\"#f2f2f2\" align=\"center\"><td class=\"listing-head\">Product</td><td class=\"listing-head\" nowrap>Stock</td><td class=\"listing-head\">Order</td><td class=\"listing-head\">Bal. Stk</td><td></td></tr></table><input type='hidden' name='hidStkTbleRowCount_"+fieldId+"' id='hidStkTbleRowCount_"+fieldId+"' value='0'><a href=\"###\" id='addStkRow' onclick=\"javascript:addStkRow('stkTable_"+fieldId+"', '', '', '', '', '"+fieldId+"',  document.getElementById('hidStkTbleRowCount_"+fieldId+"').value);\" class=\"link1\"><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;'>Add New Product</a>";	
	cell5.innerHTML = productList;

	var selScheme = "<select name='selScheme_"+fieldId+"' id='selScheme_"+fieldId+"'><option value=''>--Select--</option>";
	<?
		while ($smr=$schemeMasterResultSetObj->getRow()) {			
			$schemeMasterId = $smr[0];
			$schemeName	= $smr[1];
	?>
		if (schemeId=="<?=$schemeMasterId?>")  var selSch = "Selected";
		else var selSch = "";

		selScheme += "<option value=\"<?=$schemeMasterId?>\" "+selSch+"><?=$schemeName?></option>";
	<?
		}
	?>
	selScheme     += "</select>selScheme";	
	cell6.innerHTML	= "<div id='schemeAvailableDiv_"+fieldId+"''></div>";
	cell7.innerHTML	= "<input name='poNum_"+fieldId+"' type='text' id='poNum_"+fieldId+"' size='6' value='"+poNum+"'>";
	cell8.innerHTML	= "<input name='orderValue_"+fieldId+"' type='text' id='orderValue_"+fieldId+"' size='8' style='text-align:right;' value='"+orderValue+"' readonly>";
	cell9.innerHTML = imageButton;	
	// If Add Mode
	//if (mode==1) addStkRow('stkTable_'+fieldId, '', '', '', '', fieldId, document.getElementById("hidStkTbleRowCount_"+fieldId).value);	
	
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidMainTableRowCount").value = fieldId;
	// Calender Display
	displayCalender();	
}

	function setRtCtItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';		
			calcProductOrderedValue();
		}
		return false;
	}

	// Claender Display
	function displayCalender()
	{
		var rowCount = 	document.getElementById("hidMainTableRowCount").value;
		for (i=0;i<rowCount;i++) {
			Calendar.setup 
			(	
				{
				inputField  : "visitDate_"+i,         // ID of the input field
				eventName	  : "click",	    // name of event
				button : "visitDate_"+i, 
				ifFormat    : "%d/%m/%Y",    // the date format
				singleClick : true,
				step : 1
				}
			);
		}
	}

//ADD MULTIPLE Item- ADD ROW START
/*
cRCId - > Main Table Row Count Id
fId-> Sub Table Row Count Id
*/
/*
function addStkRow(tableId, selProductId, numStock, numOrder, balStk, cRCId, fId)
{
	var tbl		= document.getElementById(tableId);	
	var lastRow	= tbl.rows.length;
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fId+"_"+cRCId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
        cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true"
			
	var selectStock	= "<select name='selProduct_"+fId+"_"+cRCId+"' id='selProduct_"+fId+"_"+cRCId+"' onchange=\"xajax_getBalStock(document.getElementById('selRtCounter_"+cRCId+"').value, document.getElementById('selProduct_"+fId+"_"+cRCId+"').value, document.getElementById('numStock_"+fId+"_"+cRCId+"').value, '"+fId+"', '"+cRCId+"');xajax_getOrderValue(document.getElementById('selRtCounter_"+cRCId+"').value, document.getElementById('selProduct_"+fId+"_"+cRCId+"').value, document.getElementById('numOrder_"+fId+"_"+cRCId+"').value, '"+fId+"', '"+cRCId+"')\"><option value=''>--Select--</option>";
	<?
		foreach ($getMrpProductRecs as $pmr) {
			$comboMatrixRecId 	= $pmr[0];
			$productCode		= $pmr[1];
			$productName		= $pmr[2];
	?>
		if (selProductId==<?=$comboMatrixRecId?>) var selOpt = "selected";
		else var selOpt = "";
	selectStock += "<option value=\"<?=$comboMatrixRecId?>\" "+selOpt+"><?=$productName?></option>";
	<?
		}
	?>
	selectStock += "</select>";
	
	var ds = "N";	
	//if( fId >= 1) 
	var imageButton = "<a href='###' onClick=\"setStkRowItemStatus('"+fId+"','"+cRCId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fId+"_"+cRCId+"' type='hidden' id='status_"+fId+"_"+cRCId+"' value=''><input name='IsFromDB_"+fId+"_"+cRCId+"' type='hidden' id='IsFromDB_"+fId+"_"+cRCId+"' value='"+ds+"'><input name='productValue_"+fId+"_"+cRCId+"' type='hidden' id='productValue_"+fId+"_"+cRCId+"' size='8' readonly style='text-align:right'>";	
	
	cell1.innerHTML	= selectStock;
	cell2.innerHTML	= "<input name='numStock_"+fId+"_"+cRCId+"' type='text' id='numStock_"+fId+"_"+cRCId+"' value='"+numStock+"' size='6' style='text-align:right' autoComplete='off' onchange=\"xajax_getBalStock(document.getElementById('selRtCounter_"+cRCId+"').value, document.getElementById('selProduct_"+fId+"_"+cRCId+"').value, document.getElementById('numStock_"+fId+"_"+cRCId+"').value, '"+fId+"', '"+cRCId+"');\">";
	cell3.innerHTML	= "<input name='numOrder_"+fId+"_"+cRCId+"' type='text' id='numOrder_"+fId+"_"+cRCId+"' value='"+numOrder+"' size='6' style='text-align:right' autoComplete='off' onchange=\"xajax_getOrderValue(document.getElementById('selRtCounter_"+cRCId+"').value, document.getElementById('selProduct_"+fId+"_"+cRCId+"').value, document.getElementById('numOrder_"+fId+"_"+cRCId+"').value, '"+fId+"', '"+cRCId+"');\">"+hiddenFields+"";
	cell4.innerHTML	= "<input name='balStk_"+fId+"_"+cRCId+"' type='text' id='balStk_"+fId+"_"+cRCId+"' size='8' readonly style='text-align:right' value='"+balStk+"'>";
	cell5.innerHTML = imageButton;	
	
	fId		= parseInt(fId)+1;			
	document.getElementById("hidStkTbleRowCount_"+cRCId).value = fId;	
}

	function setStkRowItemStatus(id, mainId)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id+"_"+mainId).value = document.getElementById("IsFromDB_"+id+"_"+mainId).value;
			document.getElementById("row_"+id+"_"+mainId).style.display = 'none';		
			calcProductOrderedValue();
		}
		return false;
	}
*/
function salesTimeCheck(i)
{
	selectTimeHour	=	document.getElementById("selectTimeHour_"+i).value;
	selectTimeMints	=	document.getElementById("selectTimeMints_"+i).value;
	if (selectTimeHour>12 || selectTimeHour<=0) { 
		alert("hour is wrong");
		document.getElementById("selectTimeHour_"+i).focus();
		return false;
	}
	if (selectTimeMints>59 || selectTimeMints<0){
		alert("minute is wrong");
		document.getElementById("selectTimeMints_"+i).focus();
		return false;
	}
	return true;
}

// While Changing Retail Counter
function findBalStk(rtCounterId, subTbleRowCount, mainRId)
{
	//alert(rtCounterId+"-"+subTbleRowCount+"-"+mainRId);
	for (j=0;j<subTbleRowCount;j++) {
		var stkStatus = document.getElementById("status_"+j+"_"+mainRId).value;	
       	 	if (stkStatus!='N') {
			var selProduct = document.getElementById("selProduct_"+j+"_"+mainRId).value;
			var numStock  = document.getElementById("numStock_"+j+"_"+mainRId).value;
			xajax_getBalStock(rtCounterId, selProduct,numStock,j,mainRId);
		}
	}
}

function calcProductOrderedValue()
{
	var mainTableRowCount	=	document.getElementById("hidMainTableRowCount").value;	
	var grandTotalProductValue = 0;
	var grandTotalOrder	   = 0;
	for (i=0; i<mainTableRowCount; i++) {
	   var status = document.getElementById("status_"+i).value;	
       	   if (status!='N') {
		var selRtCounter = document.getElementById("selRtCounter_"+i);
		//var orderValue   = document.getElementById("orderValue_"+i);
		var stkTableRowCount = document.getElementById("hidStkTbleRowCount_"+i).value;
						
		if (selRtCounter.value!="") {				
			// Stk Table validation
			var totalProductvalue	= 0;
			var productValue	= 0;
			var totalOrder	= 0;
			var numOrder 	= 0;
			for (j=1;j<=stkTableRowCount;j++) {
				//var stkStatus = document.getElementById("status_"+j+"_"+i).value;	
       	   			//if (stkStatus!='N') {
				//var selProduct = document.getElementById("selProduct_"+j+"_"+i);	
				if (document.getElementById("productValue_"+j+"_"+i).value!="")
					productValue  = parseFloat(document.getElementById("productValue_"+j+"_"+i).value);
				else productValue  = 0;
				if (document.getElementById("numOrder_"+j+"_"+i).value!="")
					numOrder  = parseFloat(document.getElementById("numOrder_"+j+"_"+i).value);
				else numOrder  = 0;
				//if (selProduct.value!="") {	
					totalProductvalue += productValue;
					totalOrder	  += numOrder;
					//alert(productValue);
				//}
				//}				
			}
		document.getElementById("orderValue_"+i).value = number_format(totalProductvalue,2,'.','');	
			grandTotalProductValue += totalProductvalue;
			grandTotalOrder		+= totalOrder;
		}
	  }					
	}
	document.getElementById("totalValueOrder").value = number_format(grandTotalProductValue,2,'.','');
	document.getElementById("totalPacksSold").value = number_format(grandTotalOrder,2,'.','');	
}
