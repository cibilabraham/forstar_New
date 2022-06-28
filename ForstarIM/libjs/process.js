function validateAddProcess(form,mode)
{	
	//For checking duplication in Process sequence
	if (Array.indexOf != 'function') {  
	Array.prototype.indexOf = function(f, s) {
		if (typeof s == 'undefined') s = 0;
		for (var i = s; i < this.length; i++) {   
		if (f === this[i]) return i; 
		}    
		return -1;  
		}
	}
	
	var processFish		= form.processFish.value;
	//var Rate		= form.processRate.value;
	//var Commission		= form.processCommission.value;
	var preProcessCode	= form.preProcessCode.value;
	var rateList		= form.rateList.value;
	var noFurtherProcess	= form.noProcess.checked;
	var noProcessorExptRate	= form.noProcessorExptRate.value;
	
	
	if (mode==1) {
		var copyFish		= form.selCopyFrom.value;
		var selPreProcessCode	= form.selPreProcessCode.value;
	}
	
	var hidColumnCount	=	form.hidColumnCount.value;
	
		
	if (processFish=="") {
		alert("Please select a Fish.");
		form.processFish.focus();
		return false;
	}
	
	if (preProcessCode=="") {
		alert("Please enter a  Pre-Process Code.");
		form.preProcessCode.focus();
		return false;
	}

	var arr = new Array();
	var arri=0;
	
	for (j=0; j<hidColumnCount; j++) {
		var selProcess = "process_";
		var process    = document.getElementById(selProcess+j).value;
		
		if (j==0 && process=="") {
			alert("Please select first Pre-Process Code");
			document.getElementById(selProcess+j).focus();
			return false;
		}
		if(j==1 && process==""){
			alert("Please select Second Pre-Process Code");
			document.getElementById(selProcess+j).focus();
			return false;
		}
		
		if(process!="" && noFurtherProcess==""){
			if ( arr.indexOf(process) != -1 ) {
				alert("The selection of Process Sequence should be different.");
				document.getElementById(selProcess+j).focus();
				return false;
			}
			arr[arri++]=process;
		}
	}
	
	if ((copyFish=="" && mode==1) || mode==0) {
		
		/*
		var pRCount = parent.iFrame2.document.frmProcessPreProcessors.hidTableRowCount.value;
		for (j=0; j<pRCount; j++) {
			//var statusF = "status_"+j;
			//var rowStatus = parent.iFrame2.document.frmProcessPreProcessors.statusF.value
			//alert(rowStatus); 
			if (rowStatus!='N') {
				
			}
		}
		*/
		var defaultRateExist = document.getElementById("defaultRateExist").value;
		
		if (defaultRateExist=="") {
			alert("Please enter the selected pre-process rate.");			
			return false;
		}

		/*
		if (noProcessorExptRate!="") {
			alert("Please save Processor exception rate.");			
			return false;
		}
		*/
		if (rateList=="") {
			alert("Please select a Rate a list.");
			form.rateList.focus();
			return false;
		}
		
		/*
		if (Rate=="") {
			alert("Please enter Rate.");
			form.processRate.focus();
			return false;
		}
		if (Commission=="") {
			alert("Please enter Commission.");
			form.processCommission.focus();
			return false;
		}
		*/
	} else {
			if (selPreProcessCode=="") {
				alert("Please select Pre-Process Code.");
				form.selPreProcessCode.focus();
				return false;
			}
		
		}
	if (!confirmSave()) return false;
	else return true;
}

	
	function confirmDeleteException()
	{
		var conDelMsg	=	"Do you wish to delete the selected items?";
		if (confirm(conDelMsg)) return true;
		else return false;	
	}

	function disableProcessEntries(form)
	{
		var copyFrom			= form.selCopyFrom.value;
		form.processTime.disabled 	= true;
		form.processRate.disabled	= true;
		form.processCommission.disabled	= true;
		document.getElementById("processCriteria").disabled=true;
		document.getElementById("rateList").disabled=true;
		if (copyFrom=="") enableProcessEntries(form);		
	}

	function enableProcessEntries(form)
	{
		form.processTime.disabled 	=  false;
		form.processRate.disabled	= false;
		form.processCommission.disabled	= false;
		document.getElementById("processCriteria").disabled= false;
		document.getElementById("rateList").disabled= false;
	}

	function validateAddProcessorExpt()
	{
		var selExceptionProcessor = document.getElementById("selExceptionProcessor");

		if (selExceptionProcessor.value=="") {
			alert("Please select a Processor.");
			selExceptionProcessor.focus();	
			return false;
		}
		return true;
	}

	function validateAddProcessorExptR()
	{	
		/*	
		var selPreProcessor = document.getElementById("selPreProcessor");
		var rate	    = document.getElementById("processRate");
		var commission	    = document.getElementById("processCommission");
		if (selPreProcessor.value=="") {
			alert("Please select a Processor.");
			selPreProcessor.focus();	
			return false;
		}
		if (rate.value=="") {
			alert("Please enter Processor exceptin Rate.");
			rate.focus();
			return false;
		}
		if (commission.value=="") {
			alert("Please enter Processor exception Commission.");
			commission.focus();
			return false;
		}
		*/
		
		if (!validateExptRepeat()) {
			return false;
		}

		var rowCount = document.getElementById("hidTableRowCount").value;
		for (j=0; j<rowCount; j++) {
			var rowStatus = document.getElementById("status_"+j).value;
			if (rowStatus!='N') {
				var selProcessor = document.getElementById("selProcessor_"+j);
				var processRate = document.getElementById("processRate_"+j);
				var processCommission = document.getElementById("processCommission_"+j);

				if (selProcessor.value=="") {
					alert("Please select a processor.");
					selProcessor.focus();	
					return false;
				}

				if (processRate.value=="" || processRate.value==0) {
					alert("Please enter pre-process rate.");
					processRate.focus();
					return false;
				}
				if (!checkNumber(processRate.value)) {
					return false;
				}
				if (processCommission.value=="" || processCommission.value==0) {
					alert("Please enter pre-process commission.");
					processCommission.focus();
					return false;
				}
				if (!checkNumber(processCommission.value)) {
					return false;
				}				
			}
		}

		

		if(!confirmSave()){
			return false;
		}
		return true;
	}

	//ADD MULTIPLE Item- ADD ROW START	
	function addNewExceptionRow(tableId, exptProcessorId, exptRate, exptCommission, selCriteria, exceptionId, yieldTolerance)
	{		
		var tbl		= document.getElementById(tableId);
		var lastRow	= tbl.rows.length;
		var iteration	= lastRow+1;
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
				
		cell1.className	= "listing-item"; cell1.align	= "left";cell1.noWrap = "true";
		cell2.className	= "listing-item"; cell2.align	= "center";cell2.noWrap = "true";
		cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
		cell4.className	= "listing-item"; cell4.align	= "center";cell4.noWrap = "true";
		cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";
		cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";
			
		var processorList = "<select name='selProcessor_"+fieldId+"' id='selProcessor_"+fieldId+"'><option value=''>--Select--</option>";
		<?php
			if (sizeof($preProcessorRecords)>0) {
				foreach($preProcessorRecords as $pr) {
					$processorId	= $pr[0];
					$processorName	= stripSlash($pr[1]);					
		?>		
			if (exptProcessorId==<?php echo $processorId?>) var selPrOpt = 'selected=true';
			else var selPrOpt = '';
		processorList += "<option value='<?=$processorId?>' "+selPrOpt+"><?=$processorName?></option>";
		<?php } } ?>
		processorList += "</select>";
	
		var allProcessorList = "<select name='selProcessor_"+fieldId+"' id='selProcessor_"+fieldId+"'><option value='0'>ALL</option>";
		allProcessorList += "</select>";
		

		if (selCriteria==0) var selToCriteria = 'selected=true';
		else 	var selToCriteria = '';
		if (selCriteria==1) var selFromCriteria = 'selected=true';
		else var selFromCriteria = '';
		var criteriaList = "<select name='processCriteria_"+fieldId+"' id='processCriteria_"+fieldId+"'>";
		criteriaList     += "<option value='0' "+selToCriteria+">To</option>";
		criteriaList     += "<option value='1' "+selFromCriteria+">From</option>";
		criteriaList     += "</select>";
	
			
		var ds = "N";	
		if (fieldId!=0) var imageButton = "<a href='###' onClick=\"setItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		else var imageButton="";

		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidExceptionId_"+fieldId+"' type='hidden' id='hidExceptionId_"+fieldId+"' value='"+exceptionId+"'>";
	
		if (fieldId==0) cell1.innerHTML = allProcessorList;
		else cell1.innerHTML = processorList;

		cell2.innerHTML = "<input name='processRate_"+fieldId+"' id='processRate_"+fieldId+"' type='text' size='3' value='"+exptRate+"' style='text-align:right;' autocomplete='off'>";	
		cell3.innerHTML = "<input name='processCommission_"+fieldId+"' id='processCommission_"+fieldId+"' type='text' size='3' value='"+exptCommission+"' style='text-align:right;' autocomplete='off'>";		
		cell4.innerHTML = criteriaList;
		cell5.innerHTML = "<input name='yieldTolerance_"+fieldId+"' id='yieldTolerance_"+fieldId+"' type='text' size='3' value='"+yieldTolerance+"' style='text-align:right;' autocomplete='off'>";
		cell6.innerHTML = imageButton+hiddenFields;

		fieldId		= parseInt(fieldId)+1;
		document.getElementById("hidTableRowCount").value = fieldId;
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
	function validateExptRepeat()
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
	var arr = new Array();
	var arri=0;
	
	for (j=0; j<rc; j++) {
		var status = document.getElementById("status_"+j).value;
		if (status!='N') {
			var rv = document.getElementById("selProcessor_"+j).value;
			if ( arr.indexOf(rv) != -1 )    {
				alert("Please make sure the selected processor is not duplicate.");
				document.getElementById("selProcessor_"+j).focus();
				return false;
			}
			arr[arri++]=rv;
		}
	}
	return true;
	}
	
	// ------------------------------------------------------
	// Duplication check Ends here
	// ------------------------------------------------------