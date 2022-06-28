function validateInstalledCapacity(form)
{
	var machinery		=	form.machinery.value;
	var operationType		=	form.operationType.value;
	var capacity		=	form.capacity.value;
	var unitId		=	form.unitId.value;
	var perVal		=	form.perVal.value;
	var monitor		=	form.monitor.value;
	//var monitoringParameter		=	form.monitoringParameter.value;
			
	if (machinery=="") 
	{
		alert("Please enter a Machinery.");
		form.machinery.focus();
		return false;
	}
	if (operationType=="") 
	{
		alert("Please enter a Operation Type.");
		form.operationType.focus();
		return false;
	}
	if (capacity=="") 
	{
		alert("Please enter a Capacity.");
		form.capacity.focus();
		return false;
	}
	if (unitId=="") 
	{
		alert("Please enter a Unit.");
		form.unitId.focus();
		return false;
	}
	if (perVal=="") 
	{
		alert("Please enter a Per.");
		form.perVal.focus();
		return false;
	}
	if (monitor=="") 
	{
		alert("Please enter a Monitor.");
		form.monitor.focus();
		return false;
	}
		/*if (monitoringParameter=="") {
			alert("Please enter a Monitoring Parameter.");
			form.monitoringParameter.focus();
			return false;
		}*/
		
			
	var hidTableRowCount	=	document.getElementById("hidTableRowCount").value;
	
	for (i=0; i<hidTableRowCount; i++)
	{
		var status = document.getElementById("status_"+i).value;		    
	    if (status!='N') 
		{
			var headName		=	document.getElementById("headName_"+i);
			var monitoringParamId		=	document.getElementById("monitoringParamId_"+i);
			var smpStart		=	document.getElementById("smpStart_"+i);
			if( headName.value == "" )
			{
				alert("Please enter Head Name.");
				headName.focus();
				return false;
			}	
			if( monitoringParamId.value == "" )
			{
				alert("Please select Monitoring Factor.");
				monitoringParamId.focus();
				return false;
			}
			if( smpStart.value == "" )
			{
				alert("Please select Start.");
				smpStart.focus();
				return false;
			}
		}
	 }




		if (!confirmSave()) return false;
		else return true;



	}

	//ADD MULTIPLE Item- ADD ROW START
	function addNewMonitorParam(tableId, chkListName, chkPointEntryId)
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
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		cell4.className	= "listing-item"; cell4.align	= "center";
		cell5.className	= "listing-item"; cell5.align	= "center";
		cell6.className	= "listing-item"; cell6.align	= "center";
		cell7.className	= "listing-item"; cell7.align	= "center";
		
		cell6.id = "seqFlagRCol_"+fieldId;

		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setMParamItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='chkListEntryId_"+fieldId+"' type='hidden' id='chkListEntryId_"+fieldId+"' value='"+chkPointEntryId+"'><input type='hidden' value='' id='mParamSeqFlag_"+fieldId+"' name='mParamSeqFlag_"+fieldId+"' readonly />";	

		var mParameterList = "<select name='monitoringParamId_"+fieldId+"' id='monitoringParamId_"+fieldId+"'><option>--select--</option>";
		<?php if (sizeof($monitoringParameterRecords)>0)  {?>
			<?php foreach($monitoringParameterRecords as $mpr) {?>
				mParameterList	+= "<option value='<?=$mpr[0]?>'><?=$mpr[1]?></option>";
			<?php }?>	
		<?php }?>
		mParameterList += "</select>";

		var smpStart 	= "<select name='smpStart_"+fieldId+"' id='smpStart_"+fieldId+"' onchange='validParam();'>";
		smpStart	+= "<option value=''>--Select--</option>";
		smpStart	+= "<option value='Y'>YES</option>";
		smpStart	+= "<option value='N'>NO</option>";
		smpStart	+= "</select>";	

		var smpStop 	= "<select name='smpStop_"+fieldId+"' id='smpStop_"+fieldId+"' onchange='validParam();'>";
		smpStop	+= "<option value=''>--Select--</option>";
		smpStop	+= "<option value='Y'>YES</option>";
		smpStop	+= "<option value='N'>NO</option>";
		smpStop	+= "</select>";	
		
		cell1.innerHTML	= "<input type='text' name='headName_"+fieldId+"' id='headName_"+fieldId+"' value='' size='24' autocomplete='off'>";
		cell2.innerHTML	= mParameterList;	
		cell3.innerHTML	= smpStart;
		cell4.innerHTML	= smpStop;
		cell5.innerHTML	= "<input type='text' name='monitoringInterval_"+fieldId+"' id='monitoringInterval_"+fieldId+"' value='' size='5' autocomplete='off' style='text-align:right;'>";
		cell6.innerHTML	= "<input type='checkbox' name='seqFlag_"+fieldId+"' id='seqFlag_"+fieldId+"' value='Y' class='chkBox' style='display:none;'>";
		cell7.innerHTML = imageButton+hiddenFields;	
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;	
			
		validParam();	
	}

	function setMParamItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
			//seqFlagChk();		
		}
		return false;
	}
	// Check for valid parameter 
	function validParam()
	{
		var monitor  = document.getElementById("monitor").value;
		var rowCount = document.getElementById("hidTableRowCount").value;
		
		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var smpStart		= document.getElementById("smpStart_"+i);				
				var smpStop		= document.getElementById("smpStop_"+i);				
			
				if (monitor=='S'  || (monitor=='M' && smpStop.value=='N')) {
					document.getElementById("monitoringInterval_"+i).style.display = "none";	
					document.getElementById("monitoringInterval_"+i).value = "";
				} else document.getElementById("monitoringInterval_"+i).style.display = "block";
			} // Status check ends here
		} // Loop Ends here
		
		// Seq Flag check
		//seqFlagChk();
	}

	
