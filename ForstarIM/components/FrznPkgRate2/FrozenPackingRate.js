<script language="javascript">
	var prevId = "";
	var prevInnerId=""	
	function validateFrznPkgRate(form)
	{
		var rowCount = document.getElementById("hidRowCount").value;
		for (var i=1; i<=rowCount; i++) {
			var defaultRate = document.getElementById("defaultRate_"+i); 
			var gradeRowCount = document.getElementById("gradeRowCount_"+i).value;
			
			if (defaultRate.value=="") {
				alert("Please enter default rate.");
				defaultRate.focus();
				return false;	
			}

			if (!checkNumber(defaultRate.value)) {
				defaultRate.focus();
				return false;
			}

			for (var j=1; j<=gradeRowCount; j++) {
				var gradeRate = document.getElementById("gradeRate_"+j+"_"+i);
				//alert(gradeRate);
				if (gradeRate.value!="" && !checkNumber(gradeRate.value)) {
					gradeRate.focus();
					return false;
				}	
			}
		}

		if (!confirmSave()) return false;
		else return true;
	}

	function getGrade(processcodeId, freezingStageId, qualityId, frozencodeId, rowId, rateListId, fishId, selRowId, fishCategoryId)
	{
		ifId=gmobj("gradeExptIFrame");
		ifId.src="FrznPkgGradeException.php?"+"processCodeId="+processcodeId+"&freezingStageId="+freezingStageId+"&qualityId="+qualityId+"&frozenCodeId="+frozencodeId+"&iframe=Y"+"&rowId="+rowId+"&selRowId="+selRowId+"&rateListId="+rateListId+"&fishId="+fishId+"&fishCategoryId="+fishCategoryId;
		openbox('Grade Exception Rate', 1);
	}

	function closeLightBox()
	{
		document.getElementById('box').style.display='none';
		document.getElementById('filter').style.display='none';
	}

	function changeFZN(rowId, innerId)
	{
		//alert(innerId);		
		document.getElementById(rowId).style.backgroundColor="#ffcc00";
		if (prevId!="" && prevId!=rowId) {
			document.getElementById(prevId).style.backgroundColor="#FF8080";
			document.getElementById(prevInnerId).innerHTML = "";
		}
		prevId = rowId;
		prevInnerId = innerId;
	}

	function chkModified(pcId)
	{
		var itemRowCount = document.getElementById("itemRowCount").value;
		for (var i=1; i<=itemRowCount ; i++) {
			
		}		
		document.getElementById("rateModified_"+pcId).value = 1;
		
		//alert(itemRowCount);
	}

	// Add Grade
	function addGrade(processcodeId, freezingStageId, qualityId, frozencodeId, rowId, rateListId, selRowId, frznPkgRateId, fishId, selGroupEntry, fishCategoryId)
	{
		var rowCount = document.getElementById("rowCount").value;
		var exptRate = document.getElementById("exptRate").value;
		var gradeAll = document.getElementById("gradeAll").checked;
		var preProcessorId = document.getElementById("processorId").value;
		var gradeChked = false;
		
		for (i=0; i<rowCount; i++) {
			var gradeChk = document.getElementById("gradeId_"+i).checked;
			if (gradeChk) gradeChked = true;
		}

		if (!gradeAll && !gradeChked) {
			alert("Please make atleast one grade selection.");
			return false;
		}
		
		if (exptRate=="") {
			alert("Please enter rate.");
			document.getElementById("exptRate").focus();
			return false;
		}
		
		// All Grade same rate
		if (gradeAll && !selGroupEntry) {
			xajax_addGrade(fishId, processcodeId, freezingStageId, qualityId, frozencodeId, rateListId, frznPkgRateId, exptRate, "A", '', '', preProcessorId);
		}
		
		// Exception rate
		if (!gradeAll && !selGroupEntry) {
			var gArr = new Array();			
			var j=0;
			for (i=0; i<rowCount; i++) {
				var gradeChk = document.getElementById("gradeId_"+i).checked;
				if (gradeChk) {
					var gradeId = document.getElementById("gradeId_"+i).value;
					gArr[j] = gradeId;
					j++;
				}	
			}
			
			if (gArr.length>0) {
				gArrStr = gArr.join(",");			
				xajax_addGrade(fishId, processcodeId, freezingStageId, qualityId, frozencodeId, rateListId, frznPkgRateId, exptRate, "E", gArrStr, '', preProcessorId);
			}
		}

		// Edit and Update Section
		if (selGroupEntry) {
			xajax_addGrade(fishId, processcodeId, freezingStageId, qualityId, frozencodeId, rateListId, frznPkgRateId, exptRate, '', '', selGroupEntry, preProcessorId);
		}

		var selUrl = "FrznPkgGradeException.php?"+"processCodeId="+processcodeId+"&freezingStageId="+freezingStageId+"&qualityId="+qualityId+"&frozenCodeId="+frozencodeId+"&iframe=Y"+"&rowId="+rowId+"&selRowId="+selRowId+"&rateListId="+rateListId+"&fishId="+fishId+"&fishCategoryId="+fishCategoryId;
		setTimeout("reloadSelIFrame('"+selUrl+"')",1000);
			
		//parent.document.getElementById("gradeExptIFrame").src="FrznPkgGradeException.php?"+"processCodeId="+processcodeId+"&freezingStageId="+freezingStageId+"&qualityId="+qualityId+"&frozenCodeId="+frozencodeId+"&iframe=Y"+"&rowId="+rowId+"&selRowId="+selRowId+"&rateListId="+rateListId+"&fishId="+fishId+"&fishCategoryId="+fishCategoryId;
		return true;
	}

	function chkGradeExist()
	{
		var rowCount = document.getElementById("rowCount").value;
		var exptRate = document.getElementById("exptRate").value;
		var gradeAll = document.getElementById("gradeAll").checked;
		
		for (i=0; i<rowCount; i++) {
			if (gradeAll) {
				document.getElementById("gradeId_"+i).checked = false;
				document.getElementById("gradeId_"+i).disabled = true;
			} else {
				document.getElementById("gradeId_"+i).disabled = false;
			}
		}		
	}

	function closeSelLightBox(fishCategoryId, fishId, processCodeId, selRowId, rateListId)
	{		
		xajax_getQEL(fishCategoryId,fishId,processCodeId, selRowId, rateListId, 'CW');
		parent.document.getElementById('box').style.display='none';
		parent.document.getElementById('filter').style.display='none';
	}

	function reloadSelIFrame(url)
	{
		parent.document.getElementById("gradeExptIFrame").src = url;
	}	

</script>