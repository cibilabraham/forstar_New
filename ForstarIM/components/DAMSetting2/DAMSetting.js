<script language="javascript" >

	function validateDAMSetting(form)
	{
		var headName 	= document.getElementById("headName");
		var totalHead	= document.getElementById("totalHead");
		var rowCount = 	document.getElementById("hidTableRowCount").value;

		if (headName.value=="") {
			alert("Please enter head name.");
			headName.focus();
			return false;
		}

		if (totalHead.value=="") {
			alert("Please enter number of heads.");
			totalHead.focus();
			return false;
		}

		if (!chkValidNumber(totalHead.value)) {
			totalHead.focus();
			return false;
		}
	

		for (i=0; i<rowCount; i++) {
			if (document.getElementById("row_"+i)!=null) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var subheadName	= document.getElementById("subheadName_"+i);
					var produced	= document.getElementById("produced_"+i);
					var stocked	= document.getElementById("stocked_"+i);
					var osSupply	= document.getElementById("osSupply_"+i);
					var osSale	= document.getElementById("osSale_"+i);
					var openingBalance = document.getElementById("openingBalance_"+i);
					var selUnit	= document.getElementById("selUnit_"+i);
					var startDate 	= document.getElementById("startDate_"+i);
	
					if (subheadName.value=="") {
						alert("Please enter sub-head name.");
						subheadName.focus();
						return false;
					}
					
					if (produced.value=="") {
						alert("Please select produced.");
						produced.focus();
						return false;
					}
	
					if (stocked.value=="") {
						alert("Please select stocked.");
						stocked.focus();
						return false;
					}

					/*	
					if (osSupply.value=="") {
						alert("Please select O/S Supply.");
						osSupply.focus();
						return false;
					}
	
					if (osSale.value=="") {
						alert("Please select O/S Sale.");
						osSale.focus();
						return false;
					}
					*/

					if (openingBalance.value=="") {
						alert("Please enter Opening Balance.");
						openingBalance.focus();
						return false;
					}

					if (!chkValidNumber(openingBalance.value)) {
						openingBalance.focus();
						return false;
					}
	
					if (selUnit.value=="") {
						alert("Please select unit.");
						selUnit.focus();
						return false;
					}
	
					if (startDate.value=="") {
						alert("Please select date.");
						startDate.focus();
						return false;
					}
	
				} // Status ends here
		   } // Row chk ends 
		} // For loop ends here

		if (!confirmSave()) return false;
		else return true;
	}

	//ADD MULTIPLE Item- ADD ROW START
	function addNewItemRow(tableId, chkListName, chkPointEntryId)
	{
		var tbl		= document.getElementById(tableId);	
		var lastRow	= tbl.rows.length;
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
		//var cell9	= row.insertCell(8);
		
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		cell4.className	= "listing-item"; cell4.align	= "center";
		cell5.className	= "listing-item"; cell5.align	= "center";
		cell6.className	= "listing-item"; cell6.align	= "center";
		cell7.className	= "listing-item"; cell7.align	= "center";
		cell8.className	= "listing-item"; cell8.align	= "center";
		//cell9.className	= "listing-item"; cell9.align	= "center";

		
		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='damEntryId_"+fieldId+"' type='hidden' id='damEntryId_"+fieldId+"' value='"+chkPointEntryId+"'>";	

		var produced 	= "<select name='produced_"+fieldId+"' id='produced_"+fieldId+"'>";
		produced	+= "<option value=''>--Select--</option>";
		produced	+= "<option value='Y'>YES</option>";
		produced	+= "<option value='N'>NO</option>";
		produced	+= "</select>";	

		var stocked 	= "<select name='stocked_"+fieldId+"' id='stocked_"+fieldId+"'>";
		stocked	+= "<option value=''>--Select--</option>";
		stocked	+= "<option value='Y'>YES</option>";
		stocked	+= "<option value='N'>NO</option>";
		stocked	+= "</select>";

		var osSupply 	= "<select name='osSupply_"+fieldId+"' id='osSupply_"+fieldId+"'>";
		osSupply	+= "<option value=''>--Select--</option>";
		osSupply	+= "<option value='Y'>YES</option>";
		osSupply	+= "<option value='N'>NO</option>";
		osSupply	+= "</select>";

		var osSale 	= "<select name='osSale_"+fieldId+"' id='osSale_"+fieldId+"'>";
		osSale	+= "<option value=''>--Select--</option>";
		osSale	+= "<option value='Y'>YES</option>";
		osSale	+= "<option value='N'>NO</option>";
		osSale	+= "</select>";

		var selUnit 	= "<select name='selUnit_"+fieldId+"' id='selUnit_"+fieldId+"'>";
		<?php if ($t->suR)  {?>
			<?php foreach($t->suR as $stkUnitId=>$stkUnitName) {?>
				selUnit	+= "<option value='<?=$stkUnitId?>'><?=$stkUnitName?></option>";
			<?php }?>	
		<?php }?>
		selUnit		+= "</select>";
		
		cell1.innerHTML	= "<input type='text' name='subheadName_"+fieldId+"' id='subheadName_"+fieldId+"' value='"+chkListName+"' size='38' autocomplete='off'>";
		cell2.innerHTML	= produced;
		cell3.innerHTML	= stocked;
		cell4.innerHTML	= osSupply;
		cell5.innerHTML	= osSale;
		cell6.innerHTML	= "<input type='text' name='openingBalance_"+fieldId+"' id='openingBalance_"+fieldId+"' value='"+chkListName+"' size='6' autocomplete='off' style='text-align:right;'>";
		cell7.innerHTML	= selUnit;
		cell8.innerHTML	= "<input type='text' name='startDate_"+fieldId+"' id='startDate_"+fieldId+"' value='"+chkListName+"' size='8' autocomplete='off' style='text-align:right;'>"+hiddenFields;
		
		//cell9.innerHTML = imageButton+hiddenFields;	
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;	

		// Calender Display
		displayCalender();	
	}

	function setItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';		
		}
		return false;
	}

	/* ------------------------------------------------------ */
	// Duplication check starts here
	/* ------------------------------------------------------ */
	var cArr = new Array();
	var cArri = 0;	
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
		var prevOrder = 0;
		var arr = new Array();
		var arri=0;
		
		for (j=0; j<rc; j++) {
			var status = document.getElementById("status_"+j).value;
			if (status!='N') {
				var rv = document.getElementById("chkListName_"+j).value;
				if ( arr.indexOf(rv) != -1 )    {
					alert("Please make sure the check list is not duplicate.");
					document.getElementById("chkListName_"+j).focus();
					return false;
				}		
				arr[arri++]=rv;
			}
		}
		return true;
	}

	function displayCalender()
	{
		var rowCount = 	document.getElementById("hidTableRowCount").value;
		for (i=0;i<rowCount;i++) {
			Calendar.setup 
			(	
				{
				inputField  : "startDate_"+i,         // ID of the input field
				eventName	  : "click",	    // name of event
				button : "startDate_"+i, 
				ifFormat    : "%d/%m/%Y",    // the date format
				singleClick : true,
				step : 1
				}
			);
		}
	}

	// Display Sub head
	function displaySubhead(numSubhead)
	{
		numSubhead = (numSubhead!="" && numSubhead!=0)?numSubhead:1;

		var prevTotalHead = (document.getElementById("hidTotalHead").value!="")?parseInt(document.getElementById("hidTotalHead").value):1;
				
		if (prevTotalHead>numSubhead) {
			var dH = prevTotalHead;
			for (var i=0; i<(prevTotalHead-numSubhead); i++) {				
				document.getElementById('tblSubhead').deleteRow(dH);	
				dH--;
				if (fieldId>0) fieldId--;	
			}
		} else {			
			for (var i=0; i<(numSubhead-prevTotalHead); i++) {
				//alert("-->"+i+" FID="+fieldId);
				addNewItemRow('tblSubhead','','');
			}
		}
		
		document.getElementById("hidTotalHead").value =  numSubhead;
	}

	function singleHead()
	{
		var headName 	= document.getElementById("headName").value;
		var totalHead	= document.getElementById("totalHead").value;
		var addMode	= document.getElementById("addMode").value;

		if (addMode) {
			if (totalHead==1) {
				document.getElementById("subheadName_0").value = headName;
				document.getElementById("subheadName_0").readOnly = true;
			} else {
				document.getElementById("subheadName_0").value = "";	
				document.getElementById("subheadName_0").readOnly = false;
			}
		}
	}

	function stkStatusDisplay(rowId)
	{
		if (document.getElementById("stocked_"+rowId).value=='N') {
		
		}
	}

</script>