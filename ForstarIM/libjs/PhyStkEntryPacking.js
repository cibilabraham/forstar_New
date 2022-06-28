function validatePhysicalStockEntry(form)
{
		
	var selDate		=	form.selDate.value;
	var allowDate	=	form.allowDate.value;
	var rowCount	= document.getElementById("hidTableRowCount").value;	
	
	var itemsSelected = false;	

	if (selDate=="") {
		alert("Please select a Date.");
		form.selDate.focus();
		return false;
	}

	if (findDaysDiff(selDate)>0) {
		alert("Please check date");
		form.selDate.focus();
		return false;
	}

	if (allowDate!="")
	{
		var elem = selDate.split('/');  
		day = elem[0];
		month = elem[1];
		month=parseInt(month)-1;
		year = elem[2];
		var aelem = allowDate.split('/');  
		aday = aelem[0];  
		amonth = aelem[1]; 
		amonth=parseInt(amonth)-1;
		ayear = aelem[2];	
		var dt = new Date(ayear,amonth,aday);
		var tod = new Date(year,month,day);			
			
		//alert(tod);	
		//alert(dt);	
		if (tod>dt)
		{
		//alert("This Date is allowed");
		//return true;
		}
		else
		{
		alert("This Date is not allowed");
		return false;
		} 
	}


	//alert("validation");

	for (i=0; i<rowCount; i++) {
		
	   var rowStatus = document.getElementById("status_"+i).value;	
	   
	       		if (rowStatus!='N') {					
			var fish	= document.getElementById("selFish_"+i);
			//alert("The value of fish is"+fish);
			var processCode	= document.getElementById("selProcessCode_"+i);			
			var selGrade	= document.getElementById("selGrade_"+i);
			var freezingStage = document.getElementById("selFreezingStage_"+i);
			var frozenCode	= document.getElementById("selFrozenCode_"+i);
			var mCPacking	= document.getElementById("selMCPacking_"+i);
			var numMC	= document.getElementById("numMC_"+i);
			
			//alert("vali");
			if (fish.value=="") {

				alert("Please select a Fish.");
				fish.focus();
				return false;
			}

			if (processCode.value=="") {
				alert("Please select a Process Code.");
				processCode.focus();
				return false;
			}

			//alert("vali1");
			
			
			if (freezingStage.value=="") {
				alert("Please select a Freezing Stage.");
				freezingStage.focus();
				return false;
			}
			//alert("vali2");
			
			if (frozenCode.value=="") {
				alert("Please select a Frozen Code.");
				frozenCode.focus();
				return false;
			}

			if (mCPacking.value=="") {
				alert("Please select MC Packing.");
				mCPacking.focus();
				return false;
			}
			//alert("vali3");
			if (selGrade.value=="") {
				alert("Please select a Grade.");
				selGrade.focus();
				return false;
				}
				//alert("vali4");
				if (numMC.value=="") {
				alert("Please enter No. of MC.");
				numMC.focus();
				return false;
				}
				if (numMC.value=="0") {
				alert("Please enter No. of MC.");
				numMC.focus();
				return false;
				}
				if (fish.value!="") {	
				itemSelected = true;
				}
				}			
			}

if (!validateItemRepeat()) {
		return false;
			}
	
	if (!confirmSave()) {
			return false;
	}
	//alert(rowCount);
	return true;
}

//Key moving

	function nextStockBox(e,form,name)
	{
		var ecode = getKeyCode(e);	
		//alert(ecode);
		var sName = name.split("_");
		upArrowName = sName[0]+"_"+(parseInt(sName[1])-2);
		//|| (ecode==9)
		if ((ecode==13) || (ecode == 0) || (ecode==40)){
			var nextControl = eval(form+"."+name);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		if ((ecode==38)){
			var nextControl = eval(form+"."+upArrowName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
	}

	/* Find Stk diff */
	function calcStkDiff(rowId)
	{
		var calcDiffQty 	= 0;
		var stkQty 		= (document.getElementById("stkQty_"+rowId).value!="")?document.getElementById("stkQty_"+rowId).value:0;
		var physicalStkQty	= (document.getElementById("physicalStkQty_"+rowId).value!="")?document.getElementById("physicalStkQty_"+rowId).value:0;
		calcDiffQty		= physicalStkQty-stkQty;
		if (!isNaN(calcDiffQty)) {
			document.getElementById("diffStkQty_"+rowId).value = calcDiffQty;
		}
				
	}

// ------------------------------------------------------
	// Duplication check starts here
	// ------------------------------------------------------	
	function validateItemRepeat()
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
		var pArr	= new Array();	
		var pa		= 0;
	
		for (i=0; i<rc; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var fish	= document.getElementById("selFish_"+i).value;
				var processCode	= document.getElementById("selProcessCode_"+i).value;
				
				var selGrade	= document.getElementById("selGrade_"+i).value;
				var freezingStage = document.getElementById("selFreezingStage_"+i).value;
				var frozenCode	= document.getElementById("selFrozenCode_"+i).value;
				var mCPacking	= document.getElementById("selMCPacking_"+i).value;
				var selRMLotID	= document.getElementById("selRMLotID_"+i).value;
				var addVal = fish+""+processCode+""+selGrade+""+freezingStage+""+frozenCode+""+mCPacking+""+selRMLotID;
				
				if (pArr.indexOf(addVal)!=-1) {
					alert(" Raw item cannot be duplicate.");
					document.getElementById("selFish_"+i).focus();
					return false;	
				}
							
				pArr[pa++]	= addVal;
			}
		}	
		return true;
	}	
	// ------------------------------------------------------
	// Duplication check Ends here
	// ------------------------------------------------------



	function addNewPOItem1(tableId,  selFishId, selProcessCodeId, selFreezingStageId, selFrozenCodeId, selMCPackingId,selGradeId,selRMLotID,numMC,cpyItem,mode)
{
	
	var tbl		= document.getElementById(tableId);		
	var lastRow	= tbl.rows.length-1;	
	var row		= tbl.insertRow(lastRow);
	//alert("row"+row);
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;	
	//alert(fieldId);
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
	
	cell1.id = "srNo_"+fieldId;	
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";
	cell6.className	= "listing-item"; cell6.align	= "center";
	cell7.className	= "listing-item"; cell7.align	= "center";
	cell8.className	= "listing-item"; cell8.align	= "center";
	cell9.className	= "listing-item"; cell9.align	= "center";
	cell10.className	= "listing-item"; cell10.align	= "center";
	cell11.className	= "listing-item"; cell11.align	= "center";
	var selWtType = "";
	var numLS="";
	// Copy Item
	if (cpyItem) {
		//alert("............"+cpyItem);
		var fFieldId = getMaxRowId();
		//alert(fFieldId);
		if (fFieldId>=0) {
			selFishId 	 = document.getElementById('selFish_'+fFieldId).value;
			selProcessCodeId = document.getElementById('selProcessCode_'+fFieldId).value;			
			
			selGradeId	 = document.getElementById('selGrade_'+fFieldId).value;
			selFreezingStageId = document.getElementById('selFreezingStage_'+fFieldId).value;
			selFrozenCodeId	= document.getElementById('selFrozenCode_'+fFieldId).value;
			selMCPackingId	= document.getElementById('selMCPacking_'+fFieldId).value;	
			selRMLotID = document.getElementById('selRMLotID_'+fFieldId).value;
			numMC	= document.getElementById('numMC_'+fFieldId).value;	
			numLS = document.getElementById('numLS_'+fFieldId).value;
			//alert(selRMLotID);
			
		}
	}

	var selectFish	= "<select name='selFish_"+fieldId+"' id='selFish_"+fieldId+"' onchange=\"xajax_getProcessCodes(document.getElementById('selFish_"+fieldId+"').value,"+fieldId+","+fieldId+");xajax_getFrznCodes("+fieldId+", document.getElementById('selFish_"+fieldId+"').value, '', '');\"><option value=''>--Select--</option>";
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
	selectFish += "</select>";
	var selectPC	= "<select name='selProcessCode_"+fieldId+"' id='selProcessCode_"+fieldId+"' onchange=\"xajax_getGradeRecs(document.getElementById('selProcessCode_"+fieldId+"').value, "+fieldId+", ''); xajax_getFrznCodes("+fieldId+", document.getElementById('selFish_"+fieldId+"').value, document.getElementById('selProcessCode_"+fieldId+"').value, '');\"><option value=''>--Select--</option>";
	selectPC += "</select>";
	var selectFrStage	= "<select name='selFreezingStage_"+fieldId+"' id='selFreezingStage_"+fieldId+"'><option value=''>--Select--</option>";
	<?php
		if (sizeof($freezingStageRecords)>0) {	
			foreach($freezingStageRecords as $fsr) {
				$freezingStageId	= $fsr[0];
				$freezingStageCode	= stripSlash($fsr[1]);
	?>	
		if (selFreezingStageId== "<?=$freezingStageId?>")  var sel = "Selected";
		else var sel = "";

	selectFrStage += "<option value=\"<?=$freezingStageId?>\" "+sel+"><?=$freezingStageCode?></option>";	
	<?php
			}
		}
	?>
	selectFrStage += "</select>";

	var selectFrznCode	= "<select name='selFrozenCode_"+fieldId+"' id='selFrozenCode_"+fieldId+"' onchange=\"xajax_getFilledWt(document.getElementById('selFrozenCode_"+fieldId+"').value, "+fieldId+"); xajax_getMCPkgs("+fieldId+", document.getElementById('selFrozenCode_"+fieldId+"').value, ''); \"><option value=''>--Select--</option>";
	<?php
		if (sizeof($frozenPackingRecords)>0) {	
			 foreach($frozenPackingRecords as $fpr) {
				$frozenPackingId = $fpr[0];
				$frozenPackingCode = stripSlash($fpr[1]);
	?>	
		if (selFrozenCodeId== "<?=$frozenPackingId?>")  var sel = "Selected";
		else var sel = "";

	selectFrznCode += "<option value=\"<?=$frozenPackingId?>\" "+sel+"><?=$frozenPackingCode?></option>";	
	<?php
			}
		}
	?>
	selectFrznCode += "</select>";

	var selectMCPkg	= "<select name='selMCPacking_"+fieldId+"' id='selMCPacking_"+fieldId+"' onchange=\"xajax_getNumMC(document.getElementById('selMCPacking_"+fieldId+"').value, "+fieldId+");\"><option value=''>--Select--</option>";
	<?php
		if (sizeof($mcpackingRecords)>0) {	
			 foreach($mcpackingRecords as $mcp) {
				$mcpackingId	= $mcp[0];
				$mcpackingCode	= stripSlash($mcp[1]);
	?>	
		if (selMCPackingId== "<?=$mcpackingId?>")  var sel = "Selected";
		else var sel = "";

	selectMCPkg += "<option value=\"<?=$mcpackingId?>\" "+sel+"><?=$mcpackingCode?></option>";	
	<?php
			}
		}
	?>
	selectMCPkg += "</select>";

	var selectGrade	= "<select name='selGrade_"+fieldId+"' id='selGrade_"+fieldId+"'   onchange=\"xajax_getRmLotId(document.getElementById('selFish_"+fieldId+"').value,document.getElementById('selProcessCode_"+fieldId+"').value,document.getElementById('selGrade_"+fieldId+"').value, '"+fieldId+"','');\"><option value=''>--Select--</option>";
	selectPC += "</select>";

	var selectRmLot	= "<select name='selRMLotID_"+fieldId+"' id='selRMLotID_"+fieldId+"' ><option value=''>--Select--</option>";
	<?php
		if (sizeof($rmLotIdRecords)>0) {	
			foreach ($rmLotRecs as $rmId=>$rmName) {
				
	?>	
		if (selRMLotID =="<?=$rmId?>")  var selt = "Selected";
		else var selt = "";

	selectRmLot += "<option value=\"<?=$rmId?>\" "+selt+"><?=$rmName?></option>";	
	<?php
			}
		}
	?>
	selectRmLot += "</select>";
	

	var ds = "N";	
	var selBrandId="";
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setPOItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='poEntryId_"+fieldId+"' id='poEntryId_"+fieldId+"' value=''>";	

	var hidOtherFields = "<input type='hidden' name='hidBrandId_"+fieldId+"' id='hidBrandId_"+fieldId+"' value='"+selBrandId+"'><input type='hidden' name='frznPkgFilledWt_"+fieldId+"' id='frznPkgFilledWt_"+fieldId+"' value='' readonly><input type='hidden' name='numPacks_"+fieldId+"' id='numPacks_"+fieldId+"' value=''><input type='hidden' name='frznPkgDeclaredWt_"+fieldId+"' id='frznPkgDeclaredWt_"+fieldId+"' value='' readonly><input type='hidden' name='frznPkgUnit_"+fieldId+"' id='frznPkgUnit_"+fieldId+"' value='' readonly>";
	
	


	cell1.innerHTML	= "";//(fieldId+1);
	cell2.innerHTML	= selectFish;
	cell3.innerHTML	= selectPC;
	cell4.innerHTML	= selectFrStage;
	cell5.innerHTML	= selectFrznCode;
	cell6.innerHTML	= selectMCPkg;
	cell7.innerHTML	= selectGrade;
	cell8.innerHTML	=selectRmLot;
	cell9.innerHTML = "<input type='text' name='numMC_"+fieldId+"' id='numMC_"+fieldId+"' size='6' value='"+numMC+"' onkeyup='totRowVal("+fieldId+");' style='text-align:right;'>";
	cell10.innerHTML = "<input type='text' name='numLS_"+fieldId+"' id='numLS_"+fieldId+"' size='6' value='"+numLS+"' onkeyup='totRowVal("+fieldId+");' style='text-align:right;'>";
	cell11.innerHTML = imageButton+hiddenFields+hidOtherFields;	
	if (cpyItem && fFieldId>=0) {
		xajax_getProcessCodes(selFishId, fieldId, selProcessCodeId);
		xajax_getGradeRecs(selProcessCodeId, fieldId, selGradeId);
		xajax_getFilledWt(selFrozenCodeId, fieldId);
		xajax_getNumMC(selMCPackingId, fieldId);
		
	}

	if (fieldId>0) {	
		xajax_getFrznPkgCode(fieldId, selFrozenCodeId);
		xajax_getFrznCodes(fieldId, selFishId, selProcessCodeId, selFrozenCodeId);
		xajax_getMCPkg(fieldId, selMCPackingId); 
		xajax_getMCPkgs(fieldId, selFrozenCodeId, selMCPackingId);
		xajax_getRmLotId(selFishId,selProcessCodeId,selGradeId,fieldId,selRMLotID);
	}

	
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;	
	assignSrNo();
	//if (cpyItem) calcTotalOrderVal();
}
function setPOItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
			assignSrNo();
			totRowVal(id)
		}
		return false;
	}
	function getMaxRowId()
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
	}

	function totRowVal(rowId)
	{
		var totalValueUSD  = 0;
		var oneUSDToINR	   = 0;
		var totalValueINR  = 0;
		var prdWt = 0;
	
		//var wtType			= document.getElementById("wtType_"+rowId).value;
		//var filledWt		= document.getElementById("frznPkgFilledWt_"+rowId).value;
		//var declaredWt		= document.getElementById("frznPkgDeclaredWt_"+rowId).value;
		var numPacks		= document.getElementById("numPacks_"+rowId).value;// Packing val
		var numMC			= document.getElementById("numMC_"+rowId).value;
		//var pricePerKg		= document.getElementById("pricePerKg_"+rowId).value;		
		//oneUSDToINR 		= document.getElementById("oneUSDToINR").value;
		//var frznCodeUnit	= document.getElementById("frznPkgUnit_"+rowId).value;
		
		//var selPrdWt = (wtType=='NW')?declaredWt:filledWt;
		//selPrdWt = (selUnitId==2 && frznCodeUnit=='Kg')?number_format((KG2LBS*selPrdWt),3,'.',''):selPrdWt;
		//totalValueUSD	=	selPrdWt*numPacks*numMC*pricePerKg;
		//totalValueINR	=	totalValueUSD * oneUSDToINR;
		
		//prdWt = selPrdWt*numPacks*numMC;
		/*if(!isNaN(prdWt)){
			document.getElementById("prdTotalWt_"+rowId).value = number_format(prdWt,3,'.','');
		}*/

		/*if(!isNaN(totalValueUSD)){
			document.getElementById("valueInUSD_"+rowId).value = number_format(totalValueUSD,2,'.','');
			document.getElementById("valueInINR_"+rowId).value = number_format(totalValueINR,2,'.','');
		}*/

		//calcTotalOrderVal();
	}

	// Calc total Order value
	function totRowVal()
	{
		var rowCount 	= document.getElementById("hidTableRowCount").value;
		var totNumMC 	= 0;
		var totUSD	= 0;
		var totINR	= 0;
		var totNetWt = 0;
		var totNumLS=0;
		for (i=0; i<rowCount; i++) {
			var rowStatus = document.getElementById("status_"+i).value;	
			
			if (rowStatus!='N') {
				var numMC 	=  parseInt(document.getElementById("numMC_"+i).value);	
				totNumMC 	+= numMC;

				var numLS 	=  parseInt(document.getElementById("numLS_"+i).value);	
				totNumLS 	+= numLS;
				/*var valueUSD 	= parseFloat(document.getElementById("valueInUSD_"+i).value);
				totUSD += valueUSD;
				var valueINR	= parseFloat(document.getElementById("valueInINR_"+i).value);
				totINR += valueINR;	
				
				var prdWt	= parseFloat(document.getElementById("prdTotalWt_"+i).value);
				totNetWt += prdWt;	*/
			}
		}
		
		if (!isNaN(totNumMC)) document.getElementById("totalNumMC").value = totNumMC;
		if (!isNaN(totNumLS)) document.getElementById("totalNumLS").value = totNumLS;
		//if (!isNaN(totUSD)) document.getElementById("totalValUSD").value = number_format(totUSD,2,'.','');
		//if (!isNaN(totINR)) document.getElementById("totalValINR").value = number_format(totINR,2,'.','');
		//if (!isNaN(totNetWt)) document.getElementById("totalNetWt").value = number_format(totNetWt,3,'.','');*/
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

	function loadFrznGrade()
	{
		//alert(screen.height+"=="+screen.width);

		var itemCount	=	document.getElementById("hidTableRowCount").value;
		
		var j = 0;
		for (i=(itemCount-1); i>=0; i--) {
			var sStatus = document.getElementById("status_"+i).value;
			if (sStatus!='N') {
				var fish	= document.getElementById("selFish_"+i).value;
				var processCode	= document.getElementById("selProcessCode_"+i).value;
				if (fish!="" &&  processCode!="") break;
			}
		}	

		ifId=gmobj("addNewIFrame");
		ifId.src="ProcessCode.php?popupWindow=1&selFilter="+fish+"&selProcessCodeId="+processCode;		
		openModalBox("", 1, "spo-filter", "spo-box", "");
	}

	// Frozen Packing
	function loadFrznPkgCode()
	{
		ifId=gmobj("addNewIFrame");
		ifId.src="FrozenPacking.php?popupWindow=1";
		openModalBox("", 1, "spo-filter", "spo-box", "");
	}

	// MC Pkg
	function loadMCPkg()
	{
		ifId=gmobj("addNewIFrame");
		ifId.src="MCPacking.php?popupWindow=1";
		openModalBox("", 1, "spo-filter", "spo-box", "");
	}
