	function validatePackingInstruction(form, packingConfirm)
	{		
		var hidProductRowCount  = document.getElementById("hidProductRowCount").value;
		var hidMCActRowCount	= document.getElementById("hidMCActRowCount").value;
		var hidItemTbleRowCount = document.getElementById("hidItemTbleRowCount").value;

		var mcDoneBy		= form.mcDoneBy.value;
		var verifiedBy		= form.verifiedBy.value;
		/*var packingConfirm	= form.packingConfirm.checked;*/
		
		for (i=0; i<hidProductRowCount; i++) {
			var hidPrBtchNoRowCount = document.getElementById("hidPrBtchNoRowCount_"+i).value;
			var hidPkngDtlsRowCount = document.getElementById("hidPkngDtlsRowCount_"+i).value;
			var hidMCActualWtRowCount = document.getElementById("hidMCActualWtRowCount_"+i).value;
			
			// Add Product Wise Actual Gross Wt
			for (var mc=0; mc<hidMCActualWtRowCount; mc++) {				
				var mcGrossWt 	 = document.getElementById("mcActualGrossWt_"+i+"_"+mc);
				var prdMCPkageWt = document.getElementById("mcPackageWt_"+i+"_"+mc);

				if (packingConfirm && mcGrossWt.value=="") {
					alert("Please enter MC Actual Gross wt.");
					mcGrossWt.focus();
					return false;
				}
				if (mcGrossWt.value!="") {
					if (parseFloat(mcGrossWt.value)>parseFloat(prdMCPkageWt.value)) {
						alert("The entered MC gross wt is greater than the declared MC Wt.");
						mcGrossWt.focus();
						return false;
					}
				}
			} // MC Ends Here	

			var btchNoAdded = 0;
			for (j=0; j<hidPrBtchNoRowCount; j++) {
				var btchRowStatus 	= document.getElementById("btchRowStatus_"+i+"_"+j).value;
				var productBatchNo 	= document.getElementById("productBatchNo_"+i+"_"+j);

				if (btchRowStatus!='N' && productBatchNo.value=="" && packingConfirm) {
					alert("Please enter a Batch No.");
					productBatchNo.focus();
					return false;
				}
				if (btchRowStatus=='N') {
					btchNoAdded++;
				}
			} // Product Batch No Ends Here
			if (hidPrBtchNoRowCount==btchNoAdded) {
				alert("Please add atleast one Product Batch No.");
				return false;
			}

			// Add Pkng Details
			var pkngDtlsAdded = 0;

			for (k=0; k<hidPkngDtlsRowCount; k++) {
				var pkngDtlsRowStatus 	= document.getElementById("pkngDtlsRowstatus_"+i+"_"+k).value;
				var pkngMaterialBatchNo	= document.getElementById("pkngMaterialBatchNo_"+i+"_"+k);
				var pkngMaterialName 	= document.getElementById("pkngMaterialName_"+i+"_"+k);
				var pkngQtyUsed 	= document.getElementById("pkngQtyUsed_"+i+"_"+k);
					
				if (pkngDtlsRowStatus!='N' && pkngMaterialBatchNo.value=="" && pkngMaterialName.value=="" && pkngQtyUsed.value=="" && packingConfirm) {
					alert("Please enter Packing Details.");
					pkngMaterialBatchNo.focus();
					return false;
				}
				if (pkngDtlsRowStatus=='N') {
					pkngDtlsAdded++;
				}
			} // Product Pkng Details Ends Here

			if (hidPkngDtlsRowCount==pkngDtlsAdded) {
				alert("Please add atleast one Packing Details.");
				return false;
			}
		} // Poduct Loop Ends Here

	

		// MC Actual Wt
		for (l=0; l<hidMCActRowCount; l++) {
			var mcActualGrossWt	= document.getElementById("mcActualGrossWt_"+l);
			var mcPackageWt		= document.getElementById("mcPackageWt_"+l);

			if (mcActualGrossWt.value=="" && packingConfirm) {
				alert("Please enter MC Actual Goss Wt");			
				mcActualGrossWt.focus();
				return false;
			}
			
			if (mcActualGrossWt.value!="") {
				if (parseFloat(mcActualGrossWt.value)>parseFloat(mcPackageWt.value)) {
					alert("The entered MC gross wt is greater than the declared MC Wt.");
					mcActualGrossWt.focus();
					return false;
				}
			}
		}

	if (packingConfirm) {
		// Additional Item
		var adnlItem = 0;	
		for (m=0; m<hidItemTbleRowCount; m++) {
			var sStatus = document.getElementById("status_"+m).value;
			var itemName	= document.getElementById("itemName_"+m);
			var itemWt	= document.getElementById("itemWt_"+m);
			if (sStatus!='N' && (itemName.value=="" || itemWt.value=="")) {
				alert("Please enter a additional item");
				itemName.focus();
				return false;
			}
			if (sStatus=='N') {
				adnlItem++;
			}
		} // Additional Item Ends Here	
		if (hidItemTbleRowCount==adnlItem) {
			alert("Please add atleast one additional item.");
			return false;
		}
	
		if (mcDoneBy=="") {
			alert("Please enter MC Done by.");
			form.mcDoneBy.focus();
			return false;
		}

		if (verifiedBy=="") {
			alert("Please enter verified by.");
			form.verifiedBy.focus();
			return false;	
		}
	}
		
		if (!confirmSave()) {
			return false;
		}
		return true;
	}

	/*
	* Add New Additional Item
	*/
	function addNewSOAdditionalItemRow(tableId, itemName, ItemWt)
	{
			
		var tbl		= document.getElementById(tableId);	
		var lastRow	= tbl.rows.length-1;	
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "aRow_"+fieldId;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
			
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
	
		
		var ds = "N";			
		var imageButton = "<a href='###' onClick=\"setAdditionItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		
	
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'>";	
			
		cell1.innerHTML = "<input name='itemName_"+fieldId+"' type='text' id='itemName_"+fieldId+"' size='24' value='"+itemName+"' autocomplete='off'>"+hiddenFields;
		cell2.innerHTML = "<input name='itemWt_"+fieldId+"' type='text' id='itemWt_"+fieldId+"' size='6' style='text-align:right;' value='"+ItemWt+"' autocomplete='off' onkeyup='cAdditionalItem();'>";	
		cell3.innerHTML = imageButton;	
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidItemTbleRowCount").value = fieldId;	

		enbleCfmBtn();	
	}

	function setAdditionItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("aRow_"+id).style.display = 'none';
			cAdditionalItem();	
		}
		return false;
	}

	// Product Btch No Add
	function addNewPrdBtchNo(tableId, rowId, subRowId, pBtchNo)
	{
		
		var tbl		= document.getElementById(tableId+rowId);	
		//var lastRow	= tbl.rows.length-1;
		var lastRow	= tbl.rows.length;		
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "prdBtchRow_"+rowId+"_"+subRowId;	
		
		//alert(tableId+","+rowId+","+tbl+"=>"+lastRow+"Sub=>"+subRowId);

		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
			
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		
		
		var ds = "N";			
		var imageButton = "<a href='###' onClick=\"setPrdBtchNoStatus('"+rowId+"', '"+subRowId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		
	
		var hiddenFields = "<input name='btchRowStatus_"+rowId+"_"+subRowId+"' type='hidden' id='btchRowStatus_"+rowId+"_"+subRowId+"' value=''><input name='IsFromDB_"+rowId+"_"+subRowId+"' type='hidden' id='IsFromDB_"+rowId+"_"+subRowId+"' value='"+ds+"'>";	
			
		cell1.innerHTML = "<input name='productBatchNo_"+rowId+"_"+subRowId+"' type='text' id='productBatchNo_"+rowId+"_"+subRowId+"' size='8' value='"+pBtchNo+"' autocomplete='off'>"+hiddenFields;
		cell2.innerHTML = imageButton;	
		
		subRowId		= parseInt(subRowId)+1;	
		document.getElementById("hidPrBtchNoRowCount_"+rowId).value = subRowId;	

		//enbleCfmBtn();
	}

	function setPrdBtchNoStatus(id, subRowId)
	{
		if (confirmRemoveItem()) {
			document.getElementById("btchRowStatus_"+id+"_"+subRowId).value = document.getElementById("IsFromDB_"+id+"_"+subRowId).value;
			document.getElementById("prdBtchRow_"+id+"_"+subRowId).style.display = 'none';
			//calcAdditionalItem();	
		}
		return false;
	}

	// Pkgn Details
	function addNewPkngDtls(tableId, rowId, subRowId, pkngBtchNo, pkngMatName, pkngQty)
	{
		
		var tbl		= document.getElementById(tableId+rowId);	
		//var lastRow	= tbl.rows.length-1;
		var lastRow	= tbl.rows.length;		
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "prdPkngDtlsRow_"+rowId+"_"+subRowId;	
		
		//alert(tableId+","+rowId+","+tbl+"=>"+lastRow+"Sub=>"+subRowId);

		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
		var cell4	= row.insertCell(3);
			
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		cell4.className	= "listing-item"; cell4.align	= "center";
		
		
		var ds = "N";			
		var imageButton = "<a href='###' onClick=\"setPkngDtlsStatus('"+rowId+"', '"+subRowId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		
	
		var hiddenFields = "<input name='pkngDtlsRowstatus_"+rowId+"_"+subRowId+"' type='hidden' id='pkngDtlsRowstatus_"+rowId+"_"+subRowId+"' value=''><input name='IsFromDB_"+rowId+"_"+subRowId+"' type='hidden' id='IsFromDB_"+rowId+"_"+subRowId+"' value='"+ds+"'>";	
			
		cell1.innerHTML = "<input name='pkngMaterialBatchNo_"+rowId+"_"+subRowId+"' type='text' id='pkngMaterialBatchNo_"+rowId+"_"+subRowId+"' size='8' value='"+pkngBtchNo+"' autocomplete='off'>"+hiddenFields;
		cell2.innerHTML = "<input name='pkngMaterialName_"+rowId+"_"+subRowId+"' type='text' id='pkngMaterialName_"+rowId+"_"+subRowId+"' size='12' value='"+pkngMatName+"' autocomplete='off'>";
		cell3.innerHTML = "<input name='pkngQtyUsed_"+rowId+"_"+subRowId+"' type='text' id='pkngQtyUsed_"+rowId+"_"+subRowId+"' size='6' style='text-align:right;' value='"+pkngQty+"' autocomplete='off'>";
		cell4.innerHTML = imageButton;	
		
		subRowId	= parseInt(subRowId)+1;	
		document.getElementById("hidPkngDtlsRowCount_"+rowId).value = subRowId;		
	}

	function setPkngDtlsStatus(id, subRowId)
	{
		if (confirmRemoveItem()) {
			document.getElementById("pkngDtlsRowstatus_"+id+"_"+subRowId).value = document.getElementById("IsFromDB_"+id+"_"+subRowId).value;
			document.getElementById("prdPkngDtlsRow_"+id+"_"+subRowId).style.display = 'none';		
		}
		return false;
	}

	// Calc Additional Item
	function cAdditionalItem()
	{
		var rowCount 	= document.getElementById("hidItemTbleRowCount").value;
		var totalItemWt = 0;
		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;	
	    		var itemName  = document.getElementById("itemName_"+i).value;
	    		if (status!='N' && itemName!="") {
				var itemWt = document.getElementById("itemWt_"+i).value;
				totalItemWt = parseFloat(totalItemWt)+parseFloat(itemWt);
			}
		}
		if (!isNaN(totalItemWt)) {
			document.getElementById("additionalItemTotalWt").value = number_format(totalItemWt,3,'.','');
		}
		calcTotalGrossWt();
	}

	function calcMCActualGWt()
	{
		var rowCount 	= document.getElementById("hidMCActRowCount").value;
		var totalActGrossWt = 0;
		var grossWt;
		var gWt;
		for (i=0; i<rowCount; i++) {
			grossWt = document.getElementById("mcActualGrossWt_"+i);
			gWt = (grossWt.value!="")?grossWt.value:0;
			totalActGrossWt = parseFloat(totalActGrossWt)+parseFloat(gWt);			
		}
		if (!isNaN(totalActGrossWt)) {
			document.getElementById("mcTotalActualWt").value = number_format(totalActGrossWt,3,'.','');
		}
		calcTotalGrossWt();
	}

	function calcTotalGrossWt()
	{
		var addItemTotalWt 	= document.getElementById("additionalItemTotalWt");
		var mcTotActWt		= document.getElementById("mcTotalActualWt");
		var prdMCTotActWt  = document.getElementById("prdMCTotalActualWt");

		var additionalItemTotalWt 	= (addItemTotalWt.value!="")?addItemTotalWt.value:0;
		var mcTotalActualWt		= (mcTotActWt.value!="")?mcTotActWt.value:0;
		var prdMCTotalActualWt		= (prdMCTotActWt.value!="")?prdMCTotActWt.value:0;

		var calcTotalGrossWt		= parseFloat(additionalItemTotalWt)+parseFloat(mcTotalActualWt)+parseFloat(prdMCTotalActualWt);
		if (!isNaN(calcTotalGrossWt)) {
			document.getElementById("totalGrossWt").value = number_format(calcTotalGrossWt,3,'.','');
		}	
		enbleCfmBtn();			
	}

	// Prd Wise Total Gross Wt
	function calcPrdWiseTotalGrossWt()
	{
		var hidProductRowCount  = document.getElementById("hidProductRowCount").value;
		var totalActGrossWt = 0;
		var grossWt;
		var gWt;
		for (i=0; i<hidProductRowCount; i++) {
			var hidMCActualWtRowCount = document.getElementById("hidMCActualWtRowCount_"+i).value;
			
			for (j=0; j<hidMCActualWtRowCount; j++) {
				grossWt = document.getElementById("mcActualGrossWt_"+i+"_"+j);
				gWt = (grossWt.value!="")?grossWt.value:0;
				totalActGrossWt = parseFloat(totalActGrossWt)+parseFloat(gWt);	
			} // MC Ends here
		}
		if (!isNaN(totalActGrossWt)) {
			document.getElementById("prdMCTotalActualWt").value = number_format(totalActGrossWt,3,'.','');
		}
		calcTotalGrossWt();
	}

	// Confirm Make Defaut
	function cfmPrintProforma(fieldPrefix, rowCount)
	{
		var count = 0;
		var sOId = "";
		var pkgConfirmed = false;
		for (i=1; i<=rowCount; i++ )
		{
			if(document.getElementById(fieldPrefix+i).checked) {
				var sOId = document.getElementById("hidSOId_"+i).value;
				var pkgInstStatus = document.getElementById("hidPknginstStatus_"+i).value;
				if (pkgInstStatus=='C') pkgConfirmed = true;
				count++;
			}		
		}
		
		if(count==0) {
			//alert("Please select a record to Print Packing Details Proforma.");
			alert("Please select a record to Print Label.");
			return false;
		}
		
		if (count>1) {
			//alert("Please select only one record to Print Proforma.");
			alert("Please select only one record to Print Label.");
			return false;
		}

		if (pkgConfirmed) {
			alert("Please verify that the selected invoice is not confirmed.");
			return false;
		}		
		
		if (sOId!="") {
			//printWindow('PrintPackingProforma.php?selSOId='+sOId,700,600);
			printWindow('PrintPackingLabel.php?selSOId='+sOId,700,600);			
		}
		return true;
	}

	

	function printPkngAdvice(fieldPrefix, rowCount)
	{
		var count = 0;
		var sOId = "";
		for (i=1; i<=rowCount; i++ )
		{
			if(document.getElementById(fieldPrefix+i).checked) {
				var sOId = document.getElementById("hidSOId_"+i).value;
				count++;
			}		
		}
		
		if(count==0) {
			alert("Please select a record to print packing advice.");
			return false;
		}
		
		if (count>1) {
			alert("Please select only one record to  print  packing advice.");
			return false;
		}		
		
		if (sOId!="") {			
			printWindow('PrintSOPackingAdvice.php?selSOId='+sOId,700,600);
		}
		return true;
	}

	// Enable Print btn
	function enbleCfmBtn()
	{
		//alert("h");
		var hidProductRowCount  = document.getElementById("hidProductRowCount").value;
		var hidMCActRowCount	= document.getElementById("hidMCActRowCount").value;
		var hidItemTbleRowCount = document.getElementById("hidItemTbleRowCount").value;

		var mcDoneBy = document.getElementById("mcDoneBy").value;
		var verifiedBy = document.getElementById("verifiedBy").value;
		packingConfirm = true;

		var count = 0;
		for (i=0; i<hidProductRowCount; i++) {
			var hidPrBtchNoRowCount = document.getElementById("hidPrBtchNoRowCount_"+i).value;
			var hidPkngDtlsRowCount = document.getElementById("hidPkngDtlsRowCount_"+i).value;
			var hidMCActualWtRowCount = document.getElementById("hidMCActualWtRowCount_"+i).value;
			
			// Add Product Wise Actual Gross Wt
			for (var mc=0; mc<hidMCActualWtRowCount; mc++) {				
				var mcGrossWt 	 = document.getElementById("mcActualGrossWt_"+i+"_"+mc);
				var prdMCPkageWt = document.getElementById("mcPackageWt_"+i+"_"+mc);

				if (packingConfirm && mcGrossWt.value=="") {
					count++;
					//alert("Please enter MC Actual Gross wt.");
					//mcGrossWt.focus();
					//return false;
				}
				/*
				if (mcGrossWt.value!="") {
					if (parseFloat(mcGrossWt.value)>parseFloat(prdMCPkageWt.value)) {
						alert("The entered MC gross wt is greater than the declared MC Wt.");
						mcGrossWt.focus();
						return false;
					}
				}
				*/
			} // MC Ends Here	

			var btchNoAdded = 0;
			for (j=0; j<hidPrBtchNoRowCount; j++) {
				var btchRowStatus 	= document.getElementById("btchRowStatus_"+i+"_"+j).value;
				var productBatchNo 	= document.getElementById("productBatchNo_"+i+"_"+j);

				if (btchRowStatus!='N' && productBatchNo.value=="") {
					count++;
					/*
					alert("Please enter a Batch No.");
					productBatchNo.focus();
					return false;
					*/
				}
				if (btchRowStatus=='N') {
					btchNoAdded++;
				}
			
			} // Product Batch No Ends Here
			if (hidPrBtchNoRowCount==btchNoAdded) {
				count++;
				//alert("Please add atleast one Product Batch No.");
				//return false;
			}

			// Add Pkng Details
			var pkngDtlsAdded = 0;

			for (k=0; k<hidPkngDtlsRowCount; k++) {
				var pkngDtlsRowStatus 	= document.getElementById("pkngDtlsRowstatus_"+i+"_"+k).value;
				var pkngMaterialBatchNo	= document.getElementById("pkngMaterialBatchNo_"+i+"_"+k);
				var pkngMaterialName 	= document.getElementById("pkngMaterialName_"+i+"_"+k);
				var pkngQtyUsed 	= document.getElementById("pkngQtyUsed_"+i+"_"+k);
					
				if (pkngDtlsRowStatus!='N' && pkngMaterialBatchNo.value=="" && pkngMaterialName.value=="" && pkngQtyUsed.value=="") {
					count++;
					/*
					alert("Please enter Packing Details.");
					pkngMaterialBatchNo.focus();
					return false;
					*/
				}
				if (pkngDtlsRowStatus=='N') {
					pkngDtlsAdded++;
				}
			} // Product Pkng Details Ends Here

			if (hidPkngDtlsRowCount==pkngDtlsAdded) {
				count++;
				/*
				alert("Please add atleast one Packing Details.");
				return false;
				*/
			}
		} // Poduct Loop Ends Here

	

		// MC Actual Wt
		for (l=0; l<hidMCActRowCount; l++) {
			var mcActualGrossWt	= document.getElementById("mcActualGrossWt_"+l);
			var mcPackageWt		= document.getElementById("mcPackageWt_"+l);

			if (mcActualGrossWt.value=="" && packingConfirm) {
				count++;	
				/*
				alert("Please enter MC Actual Goss Wt");			
				mcActualGrossWt.focus();
				return false;
				*/
			}
			
			/*
			if (mcActualGrossWt.value!="") {
				if (parseFloat(mcActualGrossWt.value)>parseFloat(mcPackageWt.value)) {
					alert("The entered MC gross wt is greater than the declared MC Wt.");
					mcActualGrossWt.focus();
					return false;
				}				
			}
			*/
		}

	if (packingConfirm) {
		// Additional Item
		var adnlItem = 0;	
		for (m=0; m<hidItemTbleRowCount; m++) {
			var sStatus = document.getElementById("status_"+m).value;
			var itemName	= document.getElementById("itemName_"+m);
			var itemWt	= document.getElementById("itemWt_"+m);
			if (sStatus!='N' && (itemName.value=="" || itemWt.value=="")) {
				count++;
				/*
				alert("Please enter a additional item");
				itemName.focus();
				return false;
				*/
			}
			if (sStatus=='N') {
				adnlItem++;
			}
		} // Additional Item Ends Here	
		if (hidItemTbleRowCount==adnlItem) {
			count++;
			/*
			alert("Please add atleast one additional item.");
			return false;
			*/
		}
	
		if (mcDoneBy=="") {
			count++;
			/*
			alert("Please enter MC Done by.");
			form.mcDoneBy.focus();
			return false;
			*/
		}

		if (verifiedBy=="") {
			count++;
			/*
			alert("Please enter verified by.");
			form.verifiedBy.focus();
			return false;	
			*/
		}
	}
		if (count==0) {
			document.getElementById("cmdSaveConfirm").disabled = false;			
		} else {			
			document.getElementById("cmdSaveConfirm").disabled = true;
		}	
	}

	function nextTBox(e, form, name)
	{
		var ecode = getKeyCode(e);
		//alert("keycode="+ecode);
		var sName = name.split("_");
		
		upArrowName = sName[0]+"_"+sName[1]+"_"+(parseInt(sName[2])-2);
		//alert("keycode="+ecode+"="+sName[0]+":"+sName[1]+"=="+upArrowName);
		if ((ecode==13) || (ecode==40)) {
			var nextControl = eval(form+"."+name);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		if ((ecode==0) || (ecode==39)){
			var nextControl = eval(form+"."+upArrowName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
	}

	// Check Actual Wt Variation @ Parm i is Product Row id
	function chkActualWtVariation(i)
	{
		var grossWt;		
		var mcPackageWt		  = document.getElementById("selMCPackageWt_"+i).value;
		var pkgWtTolerance	  = parseFloat(document.getElementById("selPkgWtTolerance_"+i).value)/1000; // Gm-Kg
		var addPkgWtTolerance	  = parseFloat(mcPackageWt)+parseFloat(pkgWtTolerance);
		var subtractPkgWtTolerance	  = parseFloat(mcPackageWt)-parseFloat(pkgWtTolerance);
		//alert("Add="+addPkgWtTolerance+"Subtract="+subtractPkgWtTolerance);
		var hidMCActualWtRowCount = document.getElementById("hidMCActualWtRowCount_"+i).value;
		//var resultArr  = new Array(); 
		for (j=0; j<hidMCActualWtRowCount; j++) {
			var selRow = i+"_"+j;
			grossWt = parseFloat(document.getElementById("mcActualGrossWt_"+i+"_"+j).value);	
			//document.getElementById("mcActualGrossWt_"+i+"_"+j).className='highlightTxt'; 
			//alert("Add="+addPkgWtTolerance+"Subtract="+subtractPkgWtTolerance+"="+grossWt);	
			if (!(parseFloat(grossWt)>=subtractPkgWtTolerance && parseFloat(grossWt)<=addPkgWtTolerance) && grossWt!="") {
				document.getElementById("mcActualGrossWt_"+i+"_"+j).className='highlightTxt'; 
				//resultArr[grossWt] = selRow; 
			} else 	document.getElementById("mcActualGrossWt_"+i+"_"+j).className='input'; 	
		} // MC Ends here	
		
		/*
		for (var ar in resultArr)
		{
			var selRow = resultArr[ar]; // Get Total Loose Pack
			alert("Ar Wt="+ar+"Row="+selRow);
		}
		*/		
	}

	// This function is called from ChangesUpdateMaster_ajax.php
	function updateEditedMainRec(editingId)
	{
		//alert(editingId);
		xajax_updatePkgInsEditingTime(editingId);
	}