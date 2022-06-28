	function validateAddDailyFreezingChart(form)
	{
		var selectDate 		= document.getElementById("selectDate");
		var installedCapacity	= document.getElementById("installedCapacity");

		var paramRowCount 	= document.getElementById("hidParamRowCount").value;		

		if (selectDate.value=="") {
			alert("Please select a date.");
			selectDate.focus();
			return false;
		}

		if (installedCapacity.value=="") {
			alert("Please select a machinery.");
			installedCapacity.focus();
			return false;
		}

		//dfcTChk(selTimeHour, selTimeMints)
		for (var i=0;i<paramRowCount;i++) {			
			var paramEntryExist	= document.getElementById("paramEntryExist_"+i).value;
			var stop		= document.getElementById("stop_"+i).value;
			
			if (!paramEntryExist) {
				var startTimeHour 	= document.getElementById("startTimeHour_"+i).value;
				var startTimeMints	= document.getElementById("startTimeMints_"+i).value;
				if (!dfcTChk("startTimeHour_"+i, "startTimeMints_"+i)) return false;
				
				if (stop=='Y') {
					var stopTimeHour	= document.getElementById("stopTimeHour_"+i).value;
					var stopTimeMints	= document.getElementById("stopTimeMints_"+i).value;

					if (!dfcTChk("stopTimeHour_"+i, "stopTimeMints_"+i)) return false;
				}
			}

			// Param Monitor interval entry 
			if (paramEntryExist) {
				
				var startedAtHr 	= document.getElementById("startedAtHr_"+i).value;
				var startedAtMints 	= document.getElementById("startedAtMints_"+i).value;
				//var stoppedAtHr 	= document.getElementById("stoppedAtHr_"+i).value;
				//var stoppedAtMints 	= document.getElementById("stoppedAtMints_"+i).value;

				var tblRowCount 	= document.getElementById("hidTableRowCount_"+i).value;
				
				if (!dfcTChk("startedAtHr_"+i, "startedAtMints_"+i)) return false;
				//if (!dfcTChk("stoppedAtHr_"+i, "stoppedAtMints_"+i)) return false;

				for (var j=0; j<tblRowCount; j++) {
					var miStartTimeHour	= document.getElementById("startTimeHour_"+i+"_"+j);
					var miStartTimeMints 	= document.getElementById("startTimeMints_"+i+"_"+j);
					var startTemp		= document.getElementById("startTemp_"+i+"_"+j);

					var miStopTimeHour	= document.getElementById("stopTimeHour_"+i+"_"+j);
					var miStopTimeMints 	= document.getElementById("stopTimeMints_"+i+"_"+j);
					var stopTemp		= document.getElementById("stopTemp_"+i+"_"+j);

					if (miStartTimeHour.value=="") {
						alert("Please enter start time.");
						miStartTimeHour.focus();
						return false;
					}

					if (miStartTimeMints.value=="") {
						alert("Please enter start time minutes.");
						miStartTimeMints.focus();
						return false;
					}

					if (!dfcTChk("startTimeHour_"+i+"_"+j, "startTimeMints_"+i+"_"+j)) return false;

					if (startTemp.value=="") {
						alert("Please enter start value.");
						startTemp.focus();
						return false;
					}

					/*
					if (miStopTimeHour.value=="") {
						alert("Please enter stop time.");
						miStopTimeHour.focus();
						return false;
					}

					if (miStopTimeMints.value=="") {
						alert("Please enter stop time minutes.");
						miStopTimeMints.focus();
						return false;
					}

					if (!dfcTChk("stopTimeHour_"+i+"_"+j, "stopTimeMints_"+i+"_"+j)) return false;

					if (stopTemp.value=="") {
						alert("Please enter stop value.");
						stopTemp.focus();
						return false;
					}
					*/
	
				} // Sub tbl loop ends here
			}
		} // Main Loop ends here

		// Check time sequence flag correct
		if (!chkStartTime()) {
			return false;
		}

		/*
		var entryRecSize	=	parent.iFrame1.document.frmDailyFreezingChartDetails.entryRecSize.value;	
		if (entryRecSize==0) {
			alert("Please enter PF/BF details.");
			return false;
		}
		*/
	
		if (!confirmSave()) return false;
		else return true;
	}

	function validateDailyFreezingChartDetails(form)
	{
		
		var freezerName		=	form.freezerName.value;
	
	
		if (freezerName=="") {
			alert("Please select a Freezer.");
			form.freezerName.focus();
			return false;
		}
	
		if (!confirmSave()) return false;
		else return true;
	}

	function dfcTimeCheck()
	{
		selectTimeHour	=	document.getElementById("selectTimeHour").value;
		selectTimeMints	=	document.getElementById("selectTimeMints").value;
		if (selectTimeHour>12 || selectTimeHour<=0) {
			alert("hour is wrong");
			document.getElementById("selectTimeHour").focus();
			return false;
		}
	
		if (selectTimeMints>59 || selectTimeMints<0){
			alert("minute is wrong");
			document.getElementById("selectTimeMints").focus();
			return false;
		}
		return true;
	}

	function allocMoniIntrval(rowId)
	{		
		var mdiff = 0;
		var hdiff = 0;
		// Converting time to Mints
		var monitoringInterval = document.getElementById("monitoringInterval_"+rowId).value;		
		var mInSplit = monitoringInterval.split(".");
		var monIntTimeMints = parseInt(mInSplit[0])*60+parseInt(mInSplit[1]);		

		var startedAtHr = parseInt(document.getElementById("startedAtHr_"+rowId).value);
		var startedAtMints = parseInt(document.getElementById("startedAtMints_"+rowId).value);
		var startedAtOption = document.getElementById("startedAtOption_"+rowId).value;
		var stoppedAtHr = parseInt(document.getElementById("stoppedAtHr_"+rowId).value);
		var stoppedAtMints = parseInt(document.getElementById("stoppedAtMints_"+rowId).value);
		var stoppedAtOption = document.getElementById("stoppedAtOption_"+rowId).value;
		
		if (startedAtOption=="PM" && startedAtHr<12) startedAtHr = startedAtHr+12;			
		if (stoppedAtOption=="PM" && stoppedAtHr<12) stoppedAtHr = stoppedAtHr+12;
		if (startedAtOption == "AM" && startedAtHr == 12) startedAtHr = 24;

		if (stoppedAtOption == "AM" && stoppedAtHr == 12) stoppedAtHr = 24;

		if (startedAtOption == "PM" && stoppedAtOption == "AM" && stoppedAtHr < 24) stoppedAtHr = stoppedAtHr + 24;

		if (startedAtOption == stoppedAtOption && startedAtHr > stoppedAtHr) stoppedAtHr = stoppedAtHr + 24;

		if (stoppedAtMints < startedAtMints) {
			stoppedAtMints = stoppedAtMints + 60;
			stoppedAtHr = stoppedAtHr - 1;
		}
		
		var time1 = startedAtHr*60*60+startedAtMints*60;
		var time2 = stoppedAtHr*60*60+stoppedAtMints*60;
		//if (time2<time1) alert("Please check time settings");
		//var timeDiff = time1-time2;
		var timeDiff = time2-time1;
		mdiff = ((timeDiff%60)%60);
		hdiff = ((timeDiff/60)/60);
		// Number of Rows
		var numSplit = Math.ceil((hdiff*60)/monIntTimeMints);
		
		document.getElementById("numSplit_"+rowId).value = (!isNaN(numSplit))?numSplit:0;
		
		//alert(hdiff+"::"+monitoringInterval+"="+numSplit);
	}


	//ADD MULTIPLE Item- ADD ROW START
	function addNewParam(tableId, rowId, rowFieldId)
	{		
		rowFieldId = (rowFieldId)?rowFieldId:0;

		var tbl		= document.getElementById(tableId);	
		var lastRow	= tbl.rows.length;	
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "row_"+rowId+"_"+rowFieldId;
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
		var cell4	= row.insertCell(3);
		var cell5	= row.insertCell(4);
		var cell6	= row.insertCell(5);

		cell1.className	= "listing-item"; cell1.align	= "center"; cell1.noWrap = "center";
		cell2.className	= "listing-item"; cell2.align	= "center"; cell2.noWrap = "center";
		cell3.className	= "listing-item"; cell3.align	= "center"; cell3.noWrap = "center";
		cell4.className	= "listing-item"; cell4.align	= "center"; cell4.noWrap = "center";
		cell5.className	= "listing-item"; cell5.align	= "center"; cell5.noWrap = "center";
		cell6.className	= "listing-item"; cell6.align	= "center"; cell6.noWrap = "center";

		cell3.style.display = "none";
		cell4.style.display = "none";
		cell5.id = "stopFCol_"+rowId+"_"+rowFieldId;

		var ds = "N";	
		//if( rowFieldId >= 1) 
		var imageButton = "<a href='###' onClick=\"setMParamItemStatus('"+rowFieldId+"', '"+rowId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
		var hiddenFields = "<input name='status_"+rowId+"_"+rowFieldId+"' type='hidden' id='status_"+rowId+"_"+rowFieldId+"' value=''><input name='IsFromDB_"+rowId+"_"+rowFieldId+"' type='hidden' id='IsFromDB_"+rowId+"_"+rowFieldId+"' value='"+ds+"'><input name='chkListEntryId_"+rowId+"_"+rowFieldId+"' type='hidden' id='chkListEntryId_"+rowId+"_"+rowFieldId+"' value=''>";	

		var startTime = "<input type='text' id='startTimeHour_"+rowId+"_"+rowFieldId+"' name='startTimeHour_"+rowId+"_"+rowFieldId+"' size='1' value='' onkeyup=\"dfcTChk('startTimeHour_"+rowId+"_"+rowFieldId+"', 'startTimeMints_"+rowId+"_"+rowFieldId+"');addTime('"+rowId+"');\" style='text-align:center;' maxlength='2' autocomplete='off'>: ";
		startTime += "<input type='text' id='startTimeMints_"+rowId+"_"+rowFieldId+"' name='startTimeMints_"+rowId+"_"+rowFieldId+"' size='1' value='' onkeyup=\"dfcTChk('startTimeHour_"+rowId+"_"+rowFieldId+"', 'startTimeMints_"+rowId+"_"+rowFieldId+"');addTime('"+rowId+"');\" style='text-align:center;' maxlength='2' autocomplete='off'> ";
		startTime += "<select name='startTimeOption_"+rowId+"_"+rowFieldId+"' id='startTimeOption_"+rowId+"_"+rowFieldId+"'>";
		startTime += "<option value='AM'>AM</option>";
		startTime += "<option value='PM'>PM</option>";
		startTime += "</select>";

		var stopTime = "<input type='text' id='stopTimeHour_"+rowId+"_"+rowFieldId+"' name='stopTimeHour_"+rowId+"_"+rowFieldId+"' size='1' value='' onkeyup=\"dfcTChk('stopTimeHour_"+rowId+"_"+rowFieldId+"', 'stopTimeMints_"+rowId+"_"+rowFieldId+"');\" style='text-align:center;' maxlength='2' autocomplete='off'>: ";
		stopTime += "<input type='text' id='stopTimeMints_"+rowId+"_"+rowFieldId+"' name='stopTimeMints_"+rowId+"_"+rowFieldId+"' size='1' value='' onkeyup=\"dfcTChk('stopTimeHour_"+rowId+"_"+rowFieldId+"', 'stopTimeMints_"+rowId+"_"+rowFieldId+"');\" style='text-align:center;' maxlength='2' autocomplete='off'> ";
		stopTime += "<select name='stopTimeOption_"+rowId+"_"+rowFieldId+"' id='stopTimeOption_"+rowId+"_"+rowFieldId+"'>";
		stopTime += "<option value='AM'>AM</option>";
		stopTime += "<option value='PM'>PM</option>";
		stopTime += "</select>";
		
		cell1.innerHTML	= startTime;
		cell2.innerHTML	= "<input type='text' size='3' name='startTemp_"+rowId+"_"+rowFieldId+"' id='startTemp_"+rowId+"_"+rowFieldId+"' value='' style='text-align:right;' autocomplete='off'>";
		cell3.innerHTML	= stopTime;
		cell4.innerHTML	= "<input type='text' size='3' name='stopTemp_"+rowId+"_"+rowFieldId+"' id='stopTemp_"+rowId+"_"+rowFieldId+"' value='' style='text-align:right;' autocomplete='off'>";
		cell5.innerHTML	= "<input type='checkbox' name='stopMonitoring_"+rowId+"' id='stopMonitoring_"+rowId+"' value='Y' class='chkBox' />";
		cell6.innerHTML = imageButton+hiddenFields;
	
		rowFieldId		= parseInt(rowFieldId)+1;	
		document.getElementById("hidTableRowCount_"+rowId).value = rowFieldId;				

		addTime(rowId);
		chkStopFlag(rowId);
	}

	function setMParamItemStatus(id, rowId)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+rowId+"_"+id).value = document.getElementById("IsFromDB_"+rowId+"_"+id).value;
			document.getElementById("row_"+rowId+"_"+id).style.display = 'none';	
			chkStopFlag(rowId);	
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

	function chkMonitoringAlloc(rowId)
	{
		var numSplit = (document.getElementById("numSplit_"+rowId).value)?document.getElementById("numSplit_"+rowId).value:1;
		var rowcount = document.getElementById("hidTableRowCount_"+rowId).value;
		//alert(numSplit);	

		var rc=0;
		for (j=0; j<rowcount; j++) {
			var status = document.getElementById("status_"+rowId+"_"+j).value;
			if (status!='N') {
				rc++;	
			}
		}
		//alert(numSplit+"=="+rc);
		if (rc>=numSplit) {
			alert("Please check monitoring interval.\nCan't allocate Monitoring interval.");
			return false;
		} 
		return true;
	}

	
	function dfcTChk(selTimeHour, selTimeMints)
	{
		selectTimeHour	=	document.getElementById(selTimeHour).value;
		selectTimeMints	=	document.getElementById(selTimeMints).value;
		if (selectTimeHour>12 || selectTimeHour<=0) {
			alert("hour is wrong");
			document.getElementById(selTimeHour).focus();
			return false;
		}
	
		if (selectTimeMints>59 || selectTimeMints<0){
			alert("minute is wrong");
			document.getElementById(selTimeMints).focus();
			return false;
		}
		return true;
	}


	function addTime(rowId)
	{
		var monitoringInterval = document.getElementById("monitoringInterval_"+rowId).value;		
		var mInSplit = monitoringInterval.split(".");
		var monIntTimeMints = parseInt(mInSplit[0])*60+parseInt(mInSplit[1]);	
		
		//alert(document.getElementById("numSplit_"+rowId).value);
		/*
		var startedAtHr = parseInt(document.getElementById("startedAtHr_"+rowId).value);
		var startedAtMints = parseInt(document.getElementById("startedAtMints_"+rowId).value);
		var startedAtOption = document.getElementById("startedAtOption_"+rowId).value;
		
		var miStartTimeHour	= document.getElementById("startTimeHour_"+i+"_"+j);
					var miStartTimeMints 	= document.getElementById("startTimeMints_"+i+"_"+j);
					var startTemp		= document.getElementById("startTemp_"+i+"_"+j);
		*/

		var rc = document.getElementById("hidTableRowCount_"+rowId).value;	
		
		/*
		startedAtHr = parseInt(document.getElementById("startedAtHr_"+rowId).value);
		//document.getElementById("startTimeHour_"+rowId+"_"+0).value = startedAtHr;

		var sTimeHr = (document.getElementById("startTimeHour_"+rowId+"_"+subRowId).value)?parseInt(document.getElementById("startTimeHour_"+rowId+"_"+subRowId).value):0;
		
		if (sTimeHr!=0) startedAtHr = sTimeHr+1;
		*/
		var startedAtHr = parseInt(document.getElementById("startedAtHr_"+rowId).value);
		var startedAtMints = parseInt(document.getElementById("startedAtMints_"+rowId).value);
		var startedAtOption = document.getElementById("startedAtOption_"+rowId).value;
		/*
		if (startedAtOption=="PM" && startedAtHr<12) startedAtHr = startedAtHr+12;
		if (startedAtOption == "AM" & startedAtHr == 12) startedAtHr = 24;
		var timeInSec = startedAtHr*60*60+startedAtMints*60;
		var newTimeInMin = (timeInSec/60)+monIntTimeMints;
		*/
		

		for (i=0; i<rc; i++) {
			var startHr = (document.getElementById("startTimeHour_"+rowId+"_"+i).value)?parseFloat(document.getElementById("startTimeHour_"+rowId+"_"+i).value):0;
			var startMin = (document.getElementById("startTimeMints_"+rowId+"_"+i).value)?parseFloat(document.getElementById("startTimeMints_"+rowId+"_"+i).value):0;
			var startOption = document.getElementById("startTimeOption_"+rowId+"_"+i).value;

			if (startHr!=0 || startMin!=0) {
				var startTimeHr = startHr;
				if (startOption=="PM" && startHr<12) startTimeHr = startHr+12;
				if (startOption == "AM" & startHr == 12) startTimeHr = 24;
				var timeInSec = startTimeHr*60*60+startMin*60+monIntTimeMints*60;
				startHr = Math.floor(timeInSec / (60 * 60));
				//alert(startHr);
				var divisor_for_minutes = timeInSec % (60 * 60);
				startMin = Math.floor(divisor_for_minutes / 60);
					
				if (startHr>11) { startOption = "PM"; }
				if (startHr>12) { startHr = startHr - 12; }
				if (startHr==0) { startHr = 12; }
			}		

			document.getElementById("startTimeHour_"+rowId+"_"+i).value = startedAtHr;
			document.getElementById("startTimeMints_"+rowId+"_"+i).value = startedAtMints;
			document.getElementById("startTimeOption_"+rowId+"_"+i).value = startedAtOption;
			
			startedAtHr = startHr;
			startedAtMints = startMin;
			startedAtOption = startOption;
		} // Loop Ends here
	}


	function chkMParamAlloc(rowId)
	{
		var stopMonitoring = document.getElementById("stopMonitoring_"+rowId).checked;
		if (stopMonitoring) {
			alert("Monitoring Entry is not allowed.");
			return false;
		}
		else return true;
	}

	function getTimeInSec(startTimeHr, startMin, startOption)
	{		
		if (startOption=="PM" && startTimeHr<12) startTimeHr = startTimeHr+12;
		if (startOption == "AM" & startTimeHr == 12) startTimeHr = 24;
		var timeInSec = startTimeHr*60*60+startMin*60;
		//alert(timeInSec);
		return timeInSec;
	}


	Array.prototype.compare = function(testArr) {
		if (this.length != testArr.length) return false;
		for (var i = 0; i < testArr.length; i++) {
			if (this[i].compare) { 
			if (!this[i].compare(testArr[i])) return false;
			}
			if (this[i] !== testArr[i]) return false;
		}
		return true;
	}


	var seqMParamArr = new Array();

	function chkStartTime()
	{
		var paramRowCount = document.getElementById("hidParamRowCount").value;

		var secArr = new Array();
		var prevSec = "";
		for (var i=0;i<paramRowCount;i++) {
			var paramEntryExist	= document.getElementById("paramEntryExist_"+i).value;

			var monitoringParamId	= document.getElementById("monitoringParamId_"+i).value;
			var seqMParamId		= document.getElementById("seqMParamId_"+i).value;
		
			if (!paramEntryExist) {
				var startTimeHour 	= document.getElementById("startTimeHour_"+i).value;
				var startTimeMints	= document.getElementById("startTimeMints_"+i).value;
				var startTimeOption	= document.getElementById("startTimeOption_"+i).value;

				var startSec = getTimeInSec(startTimeHour, startTimeMints, startTimeOption);
				secArr[i] = startSec;
				seqMParamArr[monitoringParamId] = startSec; 
			}

			if (paramEntryExist) {
				var stopMonitoring 	= document.getElementById("stopMonitoring_"+i).checked;

				var startedAtHr 	= document.getElementById("startedAtHr_"+i).value;
				var startedAtMints 	= document.getElementById("startedAtMints_"+i).value;
				var startedAtOption	= document.getElementById("startedAtOption_"+i).value;

				var mSec = getTimeInSec(startedAtHr, startedAtMints, startedAtOption);
				secArr[i] = mSec;
				seqMParamArr[monitoringParamId] = mSec; 
			}
			//typeof(mFactArr[monitoringFactor])!="undefined"
			//alert(seqMParamArr[seqMParamId]+""+seqMParamArr[monitoringParamId]);
			//if (secArr[i]<prevSec) {
			if (seqMParamArr[seqMParamId]!="undefined" && seqMParamArr[monitoringParamId]<seqMParamArr[seqMParamId]) {
				alert("Please check time sequence.");
				return false;
			}
			//alert(prevSec+"=="+secArr[i]);
			prevSec = secArr[i];
		} // Loop ends here
		return true;
	}

	function compare(a, b) 
	{
		// psudeo code.
		if (a < b) {
			return -1;
		}
		if (a > b) {
			return 1;
		}
		if (a == b) {
			return 0;
		}
	}

	function chkStopFlag(rowId)
	{		
		var rowCount = document.getElementById("hidTableRowCount_"+rowId).value;
		
		var activeArr = new Array();
		var k = 0;
		var timeArr = new Array();
		for (j=0; j<rowCount; j++) {
			var status = document.getElementById("status_"+rowId+"_"+j).value;
			if (status!='N') {
				activeArr[k] = j;
				var startTimeHour	= document.getElementById("startTimeHour_"+rowId+"_"+j).value;
				var startTimeMints 	= document.getElementById("startTimeMints_"+rowId+"_"+j).value;
				var startTimeOption	= document.getElementById("startTimeOption_"+rowId+"_"+j).value;
				var selTime = startTimeHour+"-"+startTimeMints+"-"+startTimeOption;
				timeArr[k] = selTime;
				k++;
			}
		}

		
		for (i=0; i<activeArr.length; i++) {
			var activeRowId = activeArr[i];
			var activeTime  = timeArr[i];

			//alert((i+1)+"::"+activeArr.length+"="+activeRowId);
			if ((i+1)!=activeArr.length) document.getElementById("stopFCol_"+rowId+"_"+activeRowId).innerHTML = "";
			else {
				document.getElementById("stopFCol_"+rowId+"_"+activeRowId).innerHTML = "<input type='checkbox' name='stopMonitoring_"+rowId+"' id='stopMonitoring_"+rowId+"' value='Y' class='chkBox' />";
				
				document.getElementById("stoppedAtTime_"+rowId).value = activeTime;
			}
		}		
	}
