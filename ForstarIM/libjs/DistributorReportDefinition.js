	function validateDistReportDefinition(form)
	{	
		var selDistributor	= form.selDistributor.value;
		var selProductMgn	= form.selProductMgn.value;
		var tableRowCount	= form.hidTableRowCount.value;
		var magnHeadSelected    = false;
		
		if (selDistributor=="") {
			alert("Please select a Distributor.");
			form.selDistributor.focus();
			return false;
		}
	
		if (selProductMgn=="") {
			alert("Please select a Product wise Margin head.");
			form.selProductMgn.focus();
			return false;
		}

		if (tableRowCount>0) {
			for (var i=1; i<=tableRowCount; i++) {
				mgnHead 		= document.getElementById('mgnStructId_'+i).checked;
				var mgnSDisplayName   = document.getElementById('mgnStructDisplayName_'+i);
				if (mgnHead && mgnSDisplayName.value=="") {
					alert("Please enter a Display Name");
					mgnSDisplayName.focus();
					return false;
				} 
				magnHeadSelected = true;
			}
		}
		/*
		

		if (!magnHeadSelected) {
			alert("Please select atleast one discount splitup");
			return false;
		}
		*/
		
		if (!confirmSave()) {
			return false;
		}
		return true;
	}
	
	// Assign Selected option value
	function assignOptionValue(mode, editValueId)
	{
		var spMgn	= document.getElementById("selProductMgn");
		var selIndex = document.getElementById("selProductMgn").selectedIndex;
		//var selProductMgn = document.getElementById("selProductMgn").value;
		//var mgnListLength = document.getElementById("selProductMgn").options.length;		
		//var sIndexValue = spMgn.options[spMgn.selectedIndex].value;
		var selValue = "";
		if (selIndex>0) {
			var optValArr = new Array();
			for (var i=1; i<=selIndex; i++) {
				var idxValue = spMgn.options[i].value;					
				optValArr[i-1]= idxValue;
			}
			selValue = implode(",",optValArr); 
		}

		if (selValue) {
			document.getElementById("selOptionValue").value = selValue;
		} else document.getElementById("selOptionValue").value = "";

		if (mode==2) {
			document.getElementById("editId").value = editValueId;
			document.getElementById("frmDistributorReportDefinition").submit();
		} else document.getElementById("frmDistributorReportDefinition").submit();
		//alert("sel values="+selValue+"<====>;L="+mgnListLength+"SI="+selIndex+"=>v="+sIndexValue);
	}


	/*
	// ADD MULTIPLE Item- ADD ROW START
	function addNewItemRow(tableId, prodCategoryId, stateVatEntryId)
	{
		var tbl		= document.getElementById(tableId);
		var lastRow	= tbl.rows.length;
		// alert(lastRow);
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "row_"+fieldId;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
				
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
			
		var mgnStructureList = "<select name='selMgnStructure_"+fieldId+"' id='selMgnStructure_"+fieldId+"'><option value=''>-- Select --</option>";
		<?php
			if (sizeof($marginStructureRecords)>0) {	
				foreach ($marginStructureRecords as $msr) {
					$marginStructureId 	= $msr[0];
					$marginStructureName	= stripSlash($msr[1]);
		?>	
			if (prodCategoryId== "<?=$marginStructureId?>")  var sel = "Selected";
			else var sel = ""; 
	
		mgnStructureList += "<option value=\"<?=$marginStructureId?>\" "+sel+"><?=$marginStructureName?></option>";	
		<?php
				}
			}
		?>
		mgnStructureList += "</select>";
	
		var operatorList = "<select name='selOperator_"+fieldId+"' id='selOperator_"+fieldId+"'>";
		operatorList += "<option value='A'>Add</option>";
		operatorList += "<option value='L'>Less</option>";
		operatorList += "</select>";
			
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'>";	
		
		cell1.innerHTML	= mgnStructureList;
		cell2.innerHTML	= operatorList;
		cell3.innerHTML = imageButton+hiddenFields;	
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;		
	}
	
	function setRowItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';		
		}
		return false;
	}
	*/