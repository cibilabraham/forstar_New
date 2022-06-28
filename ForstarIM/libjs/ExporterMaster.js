function validateExporterMaster(form)
	{
		//var companyName		=	form.companyName.value;
		
		var companyName		=	form.name.value;
		/*var address		=	form.address.value;
		var place		=	form.place.value;
		var pinCode		=	form.pinCode.value;
		var country 	=	form.country.value;
		var telNo		=	form.telNo.value;
		var alphaCode	= form.alphaCode.value;
		var displayName	= form.displayName.value;
		var iecCode	= form.iecCode.value;*/
		
		if (companyName=="") {
			alert("Please enter a Name.");
			form.companyName.focus();
			return false;
		}

	/*	if (address=="") {
			alert("Please enter Address.");
			form.address.focus();
			return false;
		}

		if (place=="") {
			alert("Please enter a Place.");
			form.place.focus();
			return false;
		}

		if (pinCode=="") {
			alert("Please enter Pincode.");
			form.pinCode.focus();
			return false;
		}

		if (country=="") {
			alert("Please enter Country.");
			form.country.focus();
			return false;
		}

		if (telNo=="") {
			alert("Please enter Tel.No.");
			form.telNo.focus();
			return false;
		}	
		// Checking Phone number
		if (!checkInternationalPhone(telNo)){
			alert("Please enter a valid phone number");		
			form.telNo.focus();
			return false;
		}

		if (alphaCode=="") {
			alert("Please enter an alpha code.");
			form.alphaCode.focus();
			return false;
		}

		if (displayName=="") {
			alert("Please enter a display name.");
			form.displayName.focus();
			return false;
		}*/
		if (iecCode=="") {
			alert("Please enter a IEC Code.");
			form.iecCode.focus();
			return false;
		}

		if (!confirmSave()) return false;
		else return true;
	}

	function confirmExpMakeDefault(fieldPrefix, rowCount)
	{
		//alert(fieldPrefix+'----'+rowCount);
		var count = 0;
		for (i=1; i<=rowCount; i++ ) {
			if (document.getElementById(fieldPrefix+i).checked) {
				count++;
			}		
		}
		
		if (count==0) {
			alert("Please select a record to make Default.");
			return false;
		}
		
		if (count>1) {
			alert("Please select only one record to make Default.");
			return false;
		}		
		return true;
	}



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
		
		
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
		
		
		
		//cell6.id = "seqFlagRCol_"+fieldId;

		var ds = "N";	
		//if( fieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setMParamItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='chkListEntryId_"+fieldId+"' type='hidden' id='chkListEntryId_"+fieldId+"' value='"+chkPointEntryId+"'><input type='hidden' value='' id='mParamSeqFlag_"+fieldId+"' name='mParamSeqFlag_"+fieldId+"' readonly />";	

		var mParameterList = "<select name='monitoringParamId_"+fieldId+"' id='monitoringParamId_"+fieldId+"'><option value='0'>--Select--</option>";
		<?php if (sizeof($plantsRecords)>0)  {?>
			<?php foreach($plantsRecords as $plant) {
			$mParamId=$plant[0];
			$mParamName=$plant[2];
			?>
				mParameterList	+= "<option value='<?=$mParamId?>'><?=$mParamName?></option>";
			<?php }?>	
		<?php }?>
		mParameterList += "</select>";

	/*	var smpStart 	= "<select name='smpStart_"+fieldId+"' id='smpStart_"+fieldId+"' onchange='validParam();'>";
		smpStart	+= "<option value=''>--Select--</option>";
		smpStart	+= "<option value='Y'>YES</option>";
		smpStart	+= "<option value='N'>NO</option>";
		smpStart	+= "</select>";	*/

	/*	var smpStop 	= "<select name='smpStop_"+fieldId+"' id='smpStop_"+fieldId+"' onchange='validParam();'>";
		smpStop	+= "<option value=''>--Select--</option>";
		smpStop	+= "<option value='Y'>YES</option>";
		smpStop	+= "<option value='N'>NO</option>";
		smpStop	+= "</select>";	
*/
	/*	var selUnit 	= "<select name='selUnit_"+fieldId+"' id='selUnit_"+fieldId+"'>";
		<?php if ($t->suR)  {?>
			<?php foreach($t->suR as $stkUnitId=>$stkUnitName) {?>
				selUnit	+= "<option value='<?=$stkUnitId?>'><?=$stkUnitName?></option>";
			<?php }?>	
		<?php }?>
		selUnit		+= "</select>"; */
		
		cell2.innerHTML	= "<input type='text' name='headName_"+fieldId+"' id='headName_"+fieldId+"' value='' size='24' autocomplete='off'>";
		cell1.innerHTML	= mParameterList;	
		//cell3.innerHTML	= smpStart;
		
		//cell5.innerHTML	= "<input type='text' name='monitoringInterval_"+fieldId+"' id='monitoringInterval_"+fieldId+"' value='' size='5' autocomplete='off' style='text-align:right;'>";
		//cell6.innerHTML	= "<input type='checkbox' name='seqFlag_"+fieldId+"' id='seqFlag_"+fieldId+"' value='Y' class='chkBox' style='display:none;'>";
		//cell7.innerHTML = imageButton+hiddenFields;
		cell3.innerHTML = imageButton+hiddenFields;
		
		fieldId		= parseInt(fieldId)+1;	
		document.getElementById("hidTableRowCount").value = fieldId;	
			
		//validParam();	
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
