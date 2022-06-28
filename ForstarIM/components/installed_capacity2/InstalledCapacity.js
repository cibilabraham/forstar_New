<script language="javascript">
	function validateInstalledCapacity(form)
	{
		var machinery 		= form.machinery.value;
		var operationType	= form.operationType.value;
		var capacity		= form.capacity.value;
		var unitId	 	= form.unitId.value;
		var perVal		= form.perVal.value;
		var monitor		= form.monitor.value;
		//var monitoringParameter	= form.monitoringParameter.value;
		var entryExist		= document.getElementById("entryExist").value;

		
		if (machinery=="") {
			alert("Please enter a machinery name.");
			form.machinery.focus();
			return false;
		}

		if (operationType=="") {
			alert("Please select type of operation.");
			form.operationType.focus();
			return false;
		}

		if (capacity=="") {
			alert("Please enter a capacity.");
			form.capacity.focus();
			return false;
		}

		if (unitId=="") {
			alert("Please select a unit.");
			form.unitId.focus();
			return false;
		}

		if (perVal=="") {
			alert("Please select a per hr.");
			form.perVal.focus();
			return false;
		}

		if (monitor=="") {
			alert("Please select a monitor type.");
			form.monitor.focus();
			return false;
		}

		/*
		if (monitoringParameter=="") {
			alert("Please select a Monitoring Factor.");
			form.monitor.focus();
			return false;
		}
		*/

		// Set Monitoring parameters starts here
		var rowCount	= document.getElementById("hidTableRowCount").value;
		var setMParamSelected = false;
			
		if (rowCount>0) {
			for (i=0; i<rowCount; i++) {
				var status = document.getElementById("status_"+i).value;
				if (status!='N') {
					var headName		= document.getElementById("headName_"+i);
					var sMonitoringFactor	= document.getElementById("monitoringParamId_"+i);
					var smpStart		= document.getElementById("smpStart_"+i);
					var monitoringInterval = document.getElementById("monitoringInterval_"+i);
					var smpStop		= document.getElementById("smpStop_"+i);
					
					if (headName.value=="") {
						alert("Please enter head name.");
						headName.focus();
						return false;
					}
			
					if (sMonitoringFactor.value=="") {
						alert("Please select monitoring factor.");
						sMonitoringFactor.focus();
						return false;
					}
			
					if (smpStart.value=="") {
						alert("Please select start value.");
						smpStart.focus();
						return false;
					}							
						
					if (headName.value!="") {
						setMParamSelected = true;
					}

					if (monitor=='S' && monitoringInterval.value!="" || (monitor=='M' && smpStop.value=='N' && monitoringInterval.value!="")) {
						alert("Monitoring interval is not valid for the selected settings.");
						//alert("Please remove monitoring interval.\nMonitoring Interval is not allowed in this parameter settings.");
						monitoringInterval.focus();
						return false;
					}
					
					if (monitor=='M' && smpStop.value=='Y' && monitoringInterval.value=="") {
						alert("Please enter monitoring interval.");
						monitoringInterval.focus();
						return false;
					}
				}
			}  // For Loop Ends Here
		} // Row Count checking End
		
		if (!setMParamSelected) {
			alert("Please set atleast one monitoring parameter.");
			return false;
		}
		
		if (!validateSetMParamRepeat()) {
			return false;
		}
		// Set Monitoring parameters ends here


		if (entryExist!="") {
			alert("Installed capacity is already exist in database.");
			form.machinery.focus();
			return false;
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

		var mParameterList = "<select name='monitoringParamId_"+fieldId+"' id='monitoringParamId_"+fieldId+"'>";
		<?php if ($t->mParamRecs)  {?>
			<?php foreach($t->mParamRecs as $mParamId=>$mParamName) {?>
				mParameterList	+= "<option value='<?=$mParamId?>'><?=$mParamName?></option>";
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

	/* ------------------------------------------------------ */
	// Duplication check starts here
	/* ------------------------------------------------------ */
	var cArr = new Array();
	var cArri = 0;	
	function validateSetMParamRepeat()
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
				var rv = document.getElementById("headName_"+j).value;
				if ( arr.indexOf(rv) != -1 )    {
					alert("Please make sure the monitoring parameter is not duplicate.");
					document.getElementById("headName_"+j).focus();
					return false;
				}		
				arr[arri++]=rv;
			}
		}
		return true;
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

	function seqFlagChk()
	{	
		// Hide head		
		document.getElementById("seqFlagHCol").style.display="none"; 
		

		var rowCount = document.getElementById("hidTableRowCount").value;
		var mFactArr = new Array();		
		var monitoringFactor = "";
		var seqFlagArr  = new Array();
		var seqFlagExist = false;
		
		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				monitoringFactor	= document.getElementById("monitoringParamId_"+i).value;
				if (monitoringFactor!="") {
					var mFactId = 1;
					var rIdx = i;

					if (typeof(mFactArr[monitoringFactor])!="undefined" && mFactId!=0) {
						mFactId = parseInt(mFactId) + parseInt(mFactArr[monitoringFactor]);					
					}
					if (mFactId!=0) mFactArr[monitoringFactor] = parseInt(mFactId);
					
					if (typeof(seqFlagArr[monitoringFactor])!="undefined") {
						rIdx = rIdx+","+seqFlagArr[monitoringFactor];
					}
					seqFlagArr[monitoringFactor] = rIdx;
				}
				document.getElementById("seqFlagRCol_"+i).style.display="none"; 
			}
		}	
		
		// Uncheck flag
		uncheckSeqFlag();
	
		for (var mfr in mFactArr)
		{
			var totMFact = parseInt(mFactArr[mfr]);
			//alert(totMFact);

			if (totMFact>1) {
				seqFlagExist = true;
				displayNHideFields();
				
				var flagArr = seqFlagArr[mfr].split(",");
				for (i=0; i<flagArr.length; i++) {
					var rowId = flagArr[i];
					
					var mParamSeqFlag = document.getElementById("mParamSeqFlag_"+rowId).value;
					//alert(mParamSeqFlag);
					if (mParamSeqFlag=='Y') document.getElementById("seqFlag_"+rowId).checked = true;
					document.getElementById("seqFlag_"+rowId).style.display = "";
				}
			}			
		}

		if (seqFlagExist) {
			document.getElementById("seqFlagHCol").style.display=""; 
		}

	}

	function displayNHideFields()
	{		
		var rowCount = document.getElementById("hidTableRowCount").value;

		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {								
				document.getElementById("seqFlagRCol_"+i).style.display = "";				
				document.getElementById("seqFlag_"+i).style.display = "none";
				document.getElementById("seqFlag_"+i).checked = false;
			}
		}
	}

	function uncheckSeqFlag()
	{		
		var rowCount = document.getElementById("hidTableRowCount").value;

		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {												
				document.getElementById("seqFlag_"+i).checked = false;
			}
		}
	}

</script>