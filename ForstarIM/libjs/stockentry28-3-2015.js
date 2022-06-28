function validateStock(form)
{
	//var stockCode		=	form.stockCode.value;
	var stockName		=	form.stockName.value;
	var category		=	form.category.value;
	var subCategory		=	form.subCategory.value;
	//var stockQuantity	=	form.stockQuantity.value;
	var reorderPoint	=	form.reorderPoint.value;
	var stockType		=	form.stockType.value;
	var weight		=	form.weight.value;
	var numLayer		=	form.numLayer.value;
	//var packingWeight	=	form.packingWeight.value;
	//var numColors		=	form.numColors.value;
	var cartonWeight	=	form.cartonWeight.value;
	var reorderRequired	= 	form.reorderRequired.value;
	var toleranceLevel	=	form.toleranceLevel.value;
	//var Unit=form.plantUnit.value;
	
	/*	
	if (stockCode=="") {
		alert("Please enter a Code.");
		form.stockCode.focus();
		return false;
	}
	*/
	
	if (stockName=="") {
		alert("Please enter a Stock Name.");
		form.stockName.focus();
		return false;
	}
	
	if (category=="") {
		alert("Please select a Category.");
		form.category.focus();
		return false;
	}

	if (subCategory=="") {
		alert("Please select a Sub Category.");
		form.subCategory.focus();
		return false;
	}

	/*if (Unit=="")
	{
		alert("Please select a Unit");
		form.unit.focus();
		return false;
	}*/


var rowCountUnit=document.getElementById("hidTableRowCount2").value;
//alert(rowCount);
		for (i=0;i<rowCountUnit;i++) {		
	   var rowStatusUnit = document.getElementById("statusUnit_"+i).value;		   
	       		if (rowStatusUnit!='N') {		
					var punitIdUnit=document.getElementById("punitId_"+i).value;
			var stockQtyUnit=document.getElementById("stockQty_"+i).value;
			//alert(packingkg);
			//alert(packingmcid);
			if (punitIdUnit=="0") {
				alert("Please select the Unit");
				//packingkg.focus();
				document.getElementById("punitId_"+i).focus();
				return false;
			}

			if (stockQtyUnit=="") {

				alert("Please enter the Stock Quantity.");
				//packingmcid.focus();
				document.getElementById("stockQty_"+i).focus();
				return false;
			}
				}
		}










	
	if (reorderRequired=="") {
		alert("Please select Reorder Required.");
		form.reorderRequired.focus();
		return false;
	}
	
	if (reorderPoint=="" && reorderRequired=='Y') {
		alert("Please enter Reorder Point.");
		form.reorderPoint.focus();
		return false;
	}

	if (!isDigit(reorderPoint) && reorderRequired=='Y') {
		form.reorderPoint.value="";
		alert("Please enter numeric value only.");
		form.reorderPoint.focus();
		return false;
	}

	/*if (stockQuantity=="") {
		alert("Please enter Quantity.");
		form.stockQuantity.focus();
		return false;
	}*/

	if (form.stockType[0].checked==false && form.stockType[1].checked==false) {
		alert("Please select any one stock item Type.");
		return false;
	}
	
	// For ORDINARY STOCK	
	if (form.stockType[1].checked==true) {

		var unit = form.unit.value;
		var basicUnitQty = form.basicUnitQty.value; 
		//var unitPricePer = form.unitPricePer.value;
		//var unitPricePerItem = form.unitPricePerItem.value;
		var minOrderUnit = form.minOrderUnit.value;
		var minOrderQtyPerUnit = form.minOrderQtyPerUnit.value;
		//unitPricePerOneItem
		// Optional Fields
		var brand		= form.brand.value;
		var brandType		= form.brandType.value;
		var modelNo		= form.modelNo.value;
		var size		= form.size.value;
		var dimensionLength	= form.dimensionLength.value;
		var dimensionBreadth	= form.dimensionBreadth.value;
		var dimensionHeight 	= form.dimensionHeight.value;
		var dimensionDiameter 	= form.dimensionDiameter.value;
		var dimensionRadius	= form.dimensionRadius.value;		
		var color		= form.color.value;	
		var made		= form.made.value;
		var particularsDescription = form.particularsDescription.value;

		if (unit=="") {
			alert("Please select a Basic Unit.");
			form.unit.focus();
			return false;
		}

		if (basicUnitQty=="") {
			//alert("Please enter basic unit qty.");
			alert("Please enter basic qty.");
			form.basicUnitQty.focus();
			return false;
		}

		if (minOrderUnit=="") {
			//alert("Please enter Minimum order unit.");
			alert("Please enter Packed Qty.");
			form.minOrderUnit.focus();
			return false;
		}

		if (minOrderQtyPerUnit=="") {
			//alert("Please enter Minimum Order Quantity Per Unit.");
			alert("Please enter Minimum Order/Package.");
			form.minOrderQtyPerUnit.focus();
			return false;
		}		
		
	} // Ordinary Stock
	// For PACKING STOCK	
	if (form.stockType[0].checked==true) {
		
		if (numLayer=="") {
			alert("Please enter No of Layer.");
			form.numLayer.focus();
			return false;
		}

	var selGrade		=	document.getElementById("selRawGrade").value;
var rowCount=document.getElementById("hidTableRowCount").value;
//alert(rowCount);
		for (i=0;i<rowCount;i++) {		
	   var rowStatus = document.getElementById("status_"+i).value;		   
	       		if (rowStatus!='N') {		
					var packingkg=document.getElementById("packingKg_"+i).value;
			var packingmcid=document.getElementById("packing_"+i).value;
			//alert(packingkg);
			//alert(packingmcid);
			if (packingkg=="0") {
				alert("Please select the packingKg");
				//packingkg.focus();
				document.getElementById("packingKg_"+i).focus();
				return false;
			}

			if (packingmcid=="") {

				alert("Please select the Mcpacking.");
				//packingmcid.focus();
				document.getElementById("packing_"+i).focus();
				return false;
			}
				}
		}




		if (selGrade=="")
		{
			alert("Please select the FrozenCode.");
			document.getElementById("selFrozenCode").focus();
			return false;
		}
	
		if (cartonWeight=="") {
			alert("Please enter Total Weight of Carton.");
			form.cartonWeight.focus();
			return false;
		}
		if (toleranceLevel=="")
		{
			alert("Please enter Tolerance level");
			form.toleranceLevel.focus();
			return false;

		}
		

	}


	var stockDescription = false;
			<?php
			$sg = 0;
			foreach($stockGroupRecs as $sgr) {
				$sg++;
				$stkGroupEntryId = $sgr[5];
				$stkLabelName	= $sgr[7];
				$stkFieldType	= $inputTypeArr[$sgr[8]];
				$stkFieldName	= $sgr[9];
				$stkFieldDefaultValue = $sgr[10];
				$stkFieldSize	= $sgr[11];
				$stkFieldVDation  = $sgr[12];
				$stkFieldDataType = $sgr[13];
				$stkFType	= $sgr[8];
				$stkUnitGroupId	= $sgr[14]; 	
				if ($stkFieldVDation=='Y') {	
			?>
				var stkType  	= "<?=$stkFType?>";
				var stkFDType 	= "<?=$stkFieldDataType?>"; // Data Type: NUMBER OR ALPHANUMERIC
				var stkUnitGroup = "<?=$stkUnitGroupId?>";	
				var fieldName = document.getElementById("<?=$stkFieldName?>_<?=$sg?>");
				var stkUnitId = "";
				if (stkUnitGroup!=0) var stkUnitId = document.getElementById("stkUnitId_<?=$sg?>");
						if (fieldName.value=="" && stkType =='T') {
							alert("Please enter a <?=$stkLabelName?>.");
							fieldName.focus();
							return false;
						} else if (fieldName.checked=="" && stkType !='T') {
							alert("Please select a <?=$stkLabelName?>.");
							fieldName.focus();
							return false;
						}

						if (fieldName.value!="" && stkType =='T' && stkFDType=='NUM') {
							if (!checkDigit(fieldName.value)) {
								alert("Please enter a valid number!")
								fieldName.focus();
								return false;
							}
						}
						
						if (stkUnitGroup!=0 && stkUnitId.value=="") {
							alert("Please select <?=$stkLabelName?> unit.");
							stkUnitId.focus();
							return false;
						}
			<?php 
				} // Field Validation ends here
			?>
				stockDescription = true;
			<?php
				}
			?>
		/*if (!stockDescription) {
			alert("Please define stock Description.");			
			return false;
		}*/

	/*
	if (!isDigit (stockQuantity)) {
		form.stockQuantity.value="";
		alert("Please enter numeric value only.");
		form.stockQuantity.focus();
		return false;
	}
	*/	

var copyfromstock = document.getElementById("copyfromstock").value;
var hidcopyfromstock=document.getElementById("hidcopyfromstock").value;
		if (copyfromstock!="") {
			if (hidcopyfromstock==stockName) {
				alert("Please modifiy the Stock Entry Name. ");
				form.stockName.focus();
				return false;
			}
		}




if(!validateRepeatReturn()){
		return false;
	}





	if (!confirmSave()) {
		return false;
	}
	return true;
}

	function Hide()
	{
		document.getElementById( "ordinary" ).style.display = "none";
		document.getElementById( "packing" ).style.display = "none";
	}

	function showPacking()
	{
		document.getElementById( "ordinary" ).style.display = "none";
		document.getElementById( "packing" ).style.display = "block";
	}
	function showOrdinary()
	{
		document.getElementById( "ordinary" ).style.display = "block";
		document.getElementById( "packing" ).style.display = "none";
	}

	// When select Drop Down Box Display Text
	function displayActualWtUnit()
	{		
		var idexValue = document.getElementById("unit").selectedIndex;
		if (idexValue!=0) {
			
			document.getElementById("displayMTxt").innerHTML = document.getElementById("unit").options[idexValue].text;
			// basic Unit Qty
			document.getElementById("basicUnitQtyTxt").innerHTML = "("+ document.getElementById("unit").options[idexValue].text+")";			
			// Minimum Order Unit
			document.getElementById("minOrderUnitTxt").innerHTML = "&nbsp;"+ document.getElementById("unit").options[idexValue].text;
			// Minimum Order Quantity per Unit
			if (document.getElementById("minOrderUnit").value!="") {
				document.getElementById("minOrderQtyRowTxt").innerHTML = "&nbsp;("+ document.getElementById("minOrderUnit").value+"&nbsp;"+ document.getElementById("unit").options[idexValue].text+")";
				
				// find the rate
				var minOrderQtyPerUnit = document.getElementById("minOrderQtyPerUnit").value;
				var minOrderUnit = document.getElementById("minOrderUnit").value;
				var calcTotalOrderQty = 0;
				if (minOrderQtyPerUnit!="") {
					calcTotalOrderQty = parseFloat(minOrderUnit*minOrderQtyPerUnit);
					if (!isNaN(calcTotalOrderQty))
						document.getElementById("minOrderQtyPerUnitTxt").innerHTML = "&nbsp;"+calcTotalOrderQty+"&nbsp;"+document.getElementById("unit").options[idexValue].text;
				}				
			}		

		} else {
			document.getElementById("displayMTxt").innerHTML 	 = "";
			document.getElementById("basicUnitQtyTxt").innerHTML 	 = "";
			document.getElementById("unitPricePerTxt").innerHTML 	 = "";
			document.getElementById("unitPricePerItemTxt").innerHTML = "";
			document.getElementById("minOrderUnitTxt").innerHTML 	 = "";
			document.getElementById("minOrderQtyPerUnitTxt").innerHTML = "";
		}
				
		// Disable dimension options
		disableDimensionOption();
	}

	// Hide Reorder Point Row
	function hidReorderPointRow()
	{
		var reorderRequired = document.getElementById("reorderRequired").value;
		if (reorderRequired=='Y') {
			document.getElementById("reOrderPointRow").style.display = '';
		} else {
			document.getElementById("reOrderPointRow").style.display ="none";
			document.getElementById("reorderPoint").value = "";
		}		
	}

	// Disable Dimension Option
	function disableDimensionOption()
	{
		var dimensionLength  = document.getElementById("dimensionLength").value;
		var dimensionBreadth = document.getElementById("dimensionBreadth").value;
		var dimensionDiameter = document.getElementById("dimensionDiameter").value;
		var dimensionRadius = document.getElementById("dimensionRadius").value;
		if (dimensionLength!="" || dimensionBreadth!="") {
			document.getElementById("dimensionDiameter").disabled = true;
			document.getElementById("dimensionRadius").disabled = true;
		} else if (dimensionDiameter!="" || dimensionRadius!="") {
			document.getElementById("dimensionLength").disabled = true;
			document.getElementById("dimensionBreadth").disabled = true;		
		} else {
			document.getElementById("dimensionLength").disabled = false;
			document.getElementById("dimensionBreadth").disabled = false;
			document.getElementById("dimensionDiameter").disabled = false;
			document.getElementById("dimensionRadius").disabled = false;
		}
	}

	// Bulk Update
	function validateBulkStockUpdateRec()
	{
		var recordModified = false;		
		var rowCount = document.getElementById("hidRowCount").value;
		for (i=1; i<=rowCount; i++) {
			var holdingPercent = document.getElementById("holdingPercent_"+i).value;
			var hidHoldingPercent = document.getElementById("hidHoldingPercent_"+i).value;
			var stockingPeriod = document.getElementById("stockingPeriod_"+i).value;
			var hidStockingPeriod  = document.getElementById("hidStockingPeriod_"+i).value;

			if (holdingPercent!=hidHoldingPercent || stockingPeriod!=hidStockingPeriod) {
				recordModified = true;
			}
			if (!isDigit(holdingPercent) || holdingPercent>100) {
				alert("Please enter a holding cost percent number between 1 to 100");
				document.getElementById("holdingPercent_"+i).focus();
				return false;
			}
			if (!isDigit(stockingPeriod)) {
				alert("Please enter a number in stocking period");
				document.getElementById("stockingPeriod_"+i).focus();
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


function addNewPOItem1(tableId,  selFishId, selProcessCodeId, selFreezingStageId, selFrozenCodeId, selMCPackingId,selGradeId,numMC,cpyItem,mode)
{
	
	var tbl		= document.getElementById(tableId);	
	//alert(tableId);
	var lastRow	= tbl.rows.length-1;
	//var lastRow	= tbl.rows.length;
	//lastRow=1;
	//alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;
	//alert(fieldId);
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	
	
	

	cell1.id = "srNo_"+fieldId;	
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	
	
	
	var selWtType = "";
	var numLS="";
	// Copy Item
	
	var packingKg	= "<select name='packingKg_"+fieldId+"' id='packingKg_"+fieldId+"' onchange=\"getPackValue('"+fieldId+"');\"><option value='0'>--Select--</option>";
	<?php
		if (sizeof($declaredwtRecords)>0) {	
			foreach ($declaredwtRecords as $dcw) {
						$declaredId = $dcw[0];
						$declaredWt	= stripSlash($dcw[0]);
						
	?>	
		if (selFishId== "<?=$declaredId?>")  var sel = "Selected";
		else var sel = "";

	packingKg += "<option value=\"<?=$declaredId?>\" "+sel+"><?=$declaredWt?></option>";	
	<?php
			}
		}
	?>
	packingKg += "</select>";




/*var selectFish	= "<select name='selFish_"+fieldId+"' id='selFish_"+fieldId+"' onchange=\"xajax_getProcessCodes(document.getElementById('selFish_"+fieldId+"').value, "+fieldId+", '');xajax_getFrznCodes("+fieldId+", document.getElementById('selFish_"+fieldId+"').value, '', '');\"><option value=''>--Select--</option>";
	<?php
		if (sizeof($fishMasterRecords)>0) {	
			foreach($fishMasterRecords as $fr) {
				$fishId		= $fr[0];
				$fishName	= stripSlash($fr[1]);
	?>	
		if (selFishId== "<?=$fishId?>")  var sel = "Selected";
		else var sel = "";

	selectFish += "<option value=\"<?=$fishId?>\" "+sel+"><?=$fishName?></option>";	
	<?php
			}
		}
	?>
	selectFish += "</select>";*/













	
/*var packing	= "<select name='packing_"+fieldId+"' id='packing_"+fieldId+"'   onchange=\"xajax_getquickEntrylist(document.getElementById('packingKg_"+fieldId+"').value,document.getElementById('packing_"+fieldId+"').value)\";><option value=''>--Select--</option>";
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
	packing += "</select>";*/


	var packing	= "<select name='packing_"+fieldId+"' id='packing_"+fieldId+"'   onchange=\"getPackValue('"+fieldId+"');\"><option value=''>--Select--</option>";
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


	


	var ds = "N";	
	var selBrandId="";
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setPOItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='poEntryId_"+fieldId+"' id='poEntryId_"+fieldId+"' value=''>";	

	var hidOtherFields = "<input type='hidden' name='hidBrandId_"+fieldId+"' id='hidBrandId_"+fieldId+"' value='"+selBrandId+"'><input type='hidden' name='frznPkgFilledWt_"+fieldId+"' id='frznPkgFilledWt_"+fieldId+"' value='' readonly><input type='hidden' name='numPacks_"+fieldId+"' id='numPacks_"+fieldId+"' value=''><input type='hidden' name='frznPkgDeclaredWt_"+fieldId+"' id='frznPkgDeclaredWt_"+fieldId+"' value='' readonly><input type='hidden' name='frznPkgUnit_"+fieldId+"' id='frznPkgUnit_"+fieldId+"' value='' readonly>";
	
	var packingweightid="<input name='packingweightid_"+fieldId+"' type='hidden' id='packingweightid_"+fieldId+"' value='"+selFreezingStageId+"'>";	


	
	cell1.innerHTML	= packingKg;
	cell2.innerHTML	= packing;
	cell3.innerHTML = imageButton+hiddenFields+hidOtherFields+packingweightid;	
	


	
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;
	
	//assignSrNo();
	//if (cpyItem) calcTotalOrderVal();
}


function addNewPOItem2(tableId,  selFishId, selProcessCodeId, selFreezingStageId, selFrozenCodeId, selMCPackingId,selGradeId,numMC,cpyItem,mode)
{
	
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
	
	var unitId	= "<select name='punitId_"+fieldIdStock+"' id='punitId_"+fieldIdStock+"' onchange=\"getPackValue('"+fieldIdStock+"');\"><option value='0'>--Select--</option>";
	<?php
		//if (sizeof($declaredwtRecords)>0) {	
			//foreach ($declaredwtRecords as $dcw) {
						//$declaredId = $dcw[0];
						//$declaredWt	= stripSlash($dcw[0]);


if (sizeof($plantUnitRecords)>0) {	
			foreach ($plantUnitRecords as $dcw) {
						$plantId = $dcw[0];
						$plantName	= stripSlash($dcw[2]);
						
						
	?>	
		if (selFishId== "<?=$plantId?>")  var sel = "Selected";
		else var sel = "";

	unitId += "<option value=\"<?=$plantId?>\" "+sel+"><?=$plantName?></option>";	
	<?php
			}
		}
	?>
	unitId += "</select>";




/*var selectFish	= "<select name='selFish_"+fieldId+"' id='selFish_"+fieldId+"' onchange=\"xajax_getProcessCodes(document.getElementById('selFish_"+fieldId+"').value, "+fieldId+", '');xajax_getFrznCodes("+fieldId+", document.getElementById('selFish_"+fieldId+"').value, '', '');\"><option value=''>--Select--</option>";
	<?php
		if (sizeof($fishMasterRecords)>0) {	
			foreach($fishMasterRecords as $fr) {
				$fishId		= $fr[0];
				$fishName	= stripSlash($fr[1]);
	?>	
		if (selFishId== "<?=$fishId?>")  var sel = "Selected";
		else var sel = "";

	selectFish += "<option value=\"<?=$fishId?>\" "+sel+"><?=$fishName?></option>";	
	<?php
			}
		}
	?>
	selectFish += "</select>";*/













	
/*var packing	= "<select name='packing_"+fieldId+"' id='packing_"+fieldId+"'   onchange=\"xajax_getquickEntrylist(document.getElementById('packingKg_"+fieldId+"').value,document.getElementById('packing_"+fieldId+"').value)\";><option value=''>--Select--</option>";
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
	packing += "</select>";*/

 var stockQty = "<input name='stockQty_"+fieldIdStock+"' type='text' id='stockQty_"+fieldIdStock+"' value='"+selProcessCodeId+"'>";
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


	


	var ds = "N";	
	var selBrandId="";
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setPOItemStatusUnit('"+fieldIdStock+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='statusUnit_"+fieldIdStock+"' type='hidden' id='statusUnit_"+fieldIdStock+"' value=''><input name='IsFromDB_"+fieldIdStock+"' type='hidden' id='IsFromDB_"+fieldIdStock+"' value='"+ds+"'><input type='hidden' name='poEntryId_"+fieldIdStock+"' id='poEntryId_"+fieldIdStock+"' value=''>";	
//alert("entered1***"+fieldIdStock);
	var hidOtherFields = "<input type='hidden' name='hidBrandId_"+fieldIdStock+"' id='hidBrandId_"+fieldIdStock+"' value='"+selBrandId+"'><input type='hidden' name='frznPkgFilledWt_"+fieldIdStock+"' id='frznPkgFilledWt_"+fieldIdStock+"' value='' readonly><input type='hidden' name='numPacks_"+fieldIdStock+"' id='numPacks_"+fieldIdStock+"' value=''><input type='hidden' name='frznPkgDeclaredWt_"+fieldIdStock+"' id='frznPkgDeclaredWt_"+fieldIdStock+"' value='' readonly><input type='hidden' name='frznPkgUnit_"+fieldIdStock+"' id='frznPkgUnit_"+fieldIdStock+"' value='' readonly>";
	
	var stkid="<input name='stockqtyid_"+fieldIdStock+"' type='hidden' id='stockqtyid_"+fieldIdStock+"' value='"+selFreezingStageId+"'>";	


	
	cell1.innerHTML	= unitId;
	cell2.innerHTML	= stockQty;
	cell3.innerHTML = imageButton+hiddenFields+hidOtherFields+stkid;	
	


	
	fieldIdStock		= parseInt(fieldIdStock)+1;	
	document.getElementById("hidTableRowCount2").value = fieldIdStock;
	
	//assignSrNo();
	//if (cpyItem) calcTotalOrderVal();
}




function addAll(availableList, selectedList, selectType)
	{
		//alert(selectedList);
		var len = availableList.length -1;
		for(i=len; i>0; i--) {
			selectedList.appendChild(availableList.item(i));
		}
		selectNone(selectedList,availableList);
		if (selectType=='R') selRawArrVal(selectedList);
		else selFrznArrVal(selectedList);
		sortList(selectedList);
	}
	

	function delAll(availableList, selectedList, selectType)
	{
		var len = selectedList.length -1;
		for(i=len; i>0; i--){
			//if (!chkGradeInUse(selectedList.options[i].value)) {
				availableList.appendChild(selectedList.item(i));
			//}
			
			/*else
			selectedList.item(i).style.color="Red";	*/
		}
		selectNone(selectedList,availableList);
		if (selectType=='R') selRawArrVal(selectedList);
		else selFrznArrVal(selectedList);	

		//sortList(availableList);	
	}
	

	// Moving values from one selection box to another Starts here
	function selectNone(list1,list2)
	{
		list1.selectedIndex = -1;
		list2.selectedIndex = -1;
		addIndex = -1;
		selIndex = -1;
	}
	
	function chkGradeInUse(gradeId)
	{
		var processCodeId = document.getElementById('hidProcessCodeId').value;
		if (processCodeId) return xajax_chkPCGradeUsage(processCodeId, gradeId);
		else return false;
	}

	function addAttribute(availableList, selectedList, selectType)
	{
		var addIndex = availableList.selectedIndex;
		if(addIndex <= 0) return;
		selectedList.appendChild(availableList.options.item(addIndex));
		selectNone(selectedList,availableList);
		if (selectType=='R') selRawArrVal(selectedList);
		else selFrznArrVal(selectedList);
		
		sortList(selectedList);
	}

	function delAttribute(availableList, selectedList, selectType)
	{
		//alert(availableList+selectedList+selectType);
		var selIndex = selectedList.selectedIndex;
		if(selIndex <=0) return;
			/*if (!chkGradeInUse(selectedList.value)) {	*/
				availableList.appendChild(selectedList.options.item(selIndex))
			/*} else selectedList.item(selIndex).style.color="Red";
			*/
			
		selectNone(selectedList,availableList);
		if (selectType=='R') selRawArrVal(selectedList);
		else selFrznArrVal(selectedList);	

		//sortList(availableList);
	}
	function setPOItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
			//getPackValue(id);

			var no=document.getElementById("hidTableRowCount").value;
			var wtArrayCw = new Array();
			var wtArrayCp = new Array();
			for (var i=0; i<no; i++) {
			
			if (document.getElementById("status_"+i).value!='N')
			{
				//alert("row----"+i);
			var joinCntWt=parseFloat(document.getElementById("packingKg_"+i).value);
			var joinCntPk=document.getElementById("packing_"+i).value;
			wtArrayCw[i] = joinCntWt;
			wtArrayCp[i] = joinCntPk;
			}
			
			//alert(wtArrayCw[i]);
			//alert(wtArrayCp[i]);
		}
		xajax_getquickEntrylist(wtArrayCw,wtArrayCp);
			//assignSrNo();
			totRowVal(id);
		}
		return false;
	}

	function selRawArrVal(selectedList)
	{
		var len = selectedList.length -1;
		var grArray = new Array();
		for (var i=0; i<len; i++) {
			grArray[i] = selectedList.options[i+1].value;		
		}
		selGrade = implode(",",grArray);
	
		document.getElementById("selRawGrade").value = selGrade;
	}

	// Frozen imploded value (grade seperation)
	function selFrznArrVal(selectedList)
	{
		var len = selectedList.length -1;
		var grArray = new Array();
		for (var i=0; i<len; i++) {
			grArray[i] = selectedList.options[i+1].value;		
		}
		selGrade = implode(",",grArray);
	
		document.getElementById("selFrozenGrade").value = selGrade;
	}
/*var A;
A="";
B="";*/
function getPackValue(i){

var no;

no=document.getElementById("hidTableRowCount").value;
//alert(no);
var wtArrayCw = new Array();
var wtArrayCp = new Array();
		for (var i=0; i<no; i++) {
			//var joinCnt    =  document.getElementById("packingKg_"+i).value+":"+ document.getElementById("packing_"+i).value;
			//if (document.getElementById("status_"+i).value!='N')
			//{
				//alert(i);
			var joinCntWt=parseFloat(document.getElementById("packingKg_"+i).value);
			var joinCntPk=document.getElementById("packing_"+i).value;
			wtArrayCw[i] = joinCntWt;
			wtArrayCp[i] = joinCntPk;
			//}
			
			//alert(wtArrayCw[i]);
			//alert(wtArrayCp[i]);
		}
		xajax_getquickEntrylist(wtArrayCw,wtArrayCp);
/*a=document.getElementById("packing_"+i).value;
alert(a);
b=document.getElementById("packingKg_"+i).value;
alert(b);
A=A+":"+a;
alert(A);
B=B+":"+b;
alert(B);
xajax_getquickEntrylist(A,B);
A="";*/

}

/*function getMaxRowId()
	{
		//alert("hello");
		var cnt = 0;
		var rc = document.getElementById("hidTableRowCount").value;
		for (i=0; i<rc; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				//cnt++;
				cnt = i;
			}

		}
		return cnt;
	}*/

function getLoading(formObj)
{
showFnLoading(); 
formObj.form.submit();
}


function setPOItemStatusUnit(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("statusUnit_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
			
		}
		return false;
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

	var rc = document.getElementById("hidTableRowCount2").value;

	var prevOrder = 0;
	var arr = new Array();
	var arri=0;
	for( j=0; j<rc; j++ )	{
		if( document.getElementById("statusUnit_"+j) != null )
		{
			var statusUnit = document.getElementById("statusUnit_"+j).value;        
		}
		else var statusUnit =  '';

		if( statusUnit!='N')
		{
			var rv = document.getElementById("punitId_"+j).value;	
			if ( arr.indexOf(rv) != -1 )	{
				alert("Stock Unit cannot be duplicate.");
				document.getElementById("punitId_"+j).focus();
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
