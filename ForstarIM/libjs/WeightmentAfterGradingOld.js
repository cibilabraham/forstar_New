function validateAddDriverMaster(form)
{

if (!validateGradeRepeat()) {
		return false;
	}

}



// ADD MULTIPLE Item- ADD ROW START
function addNewRow(tableId,lotid,gradeId,gradeValue,harvestingEquipmentQuantity)
{
alert(lotid);
var tbl		= document.getElementById(tableId);

	var lastRow	= tbl.rows.length;
	// alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "bRow_"+fldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	
	cell1.id = "srNo_"+fldId;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";

		//alert("entered");
		//alert("<?=$vehileTypeId?>");
		var grade	= "Grade:<select name='gradeType_"+fldId+"' id='gradeType_"+fldId+"' ><option value='0'>--Select--</option>";
	<?php
	
	$getGradeRecs = $weightmentAfterGradingObj->getGrade(lotid);
		if (sizeof($getGradeRecs)>0) {	
			foreach ($getGradeRecs as $dcw) {
						$gradeId = $dcw[0];
						$gradeValue	= stripSlash($dcw[1]);
						
	?>	
	
		if (gradeValue=="<?=$gradeId?>") { var sel = "Selected";}
		else { var sel = "";}

	grade += "<option value=\"<?=$gradeId?>\" "+sel+"><?=$gradeValue?></option>";	
	<?php
			}
		}
		
	?>	
	grade += "</select>";
	alert(grade);
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setTestRowItemStatus('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var weight = "Weight:<input name='weight_"+fldId+"' type='text' id='weight_"+fldId+"' value='"+harvestingEquipmentQuantity+"'>";
	
	
	var hiddenFields = "<input name='bStatus_"+fldId+"' type='hidden' id='bStatus_"+fldId+"' value=''><input name='bIsFromDB_"+fldId+"' type='hidden' id='bIsFromDB_"+fldId+"' value='"+ds+"'><input type='hidden' name='gradeId_"+fldId+"' id='gradeId_"+fldId+"' value='"+gradeId+"'>";

	//cell1.innerHTML	= "<input name='test_"+fldId+"' type='text' id='test_"+fldId+"' value=\""+unescape(gradeValue)+"\" size='24'>";
	cell1.innerHTML	= grade;
	cell2.innerHTML	= weight;
	cell3.innerHTML = imageButton+hiddenFields;	
	
	fldId		= parseInt(fldId)+1;	
	//document.getElementById("hidTestMethodTableRowCount").value = fldId;	
	document.getElementById("hidWeightmentTableRowCount").value = fldId;	






//code end
	
	
	
}

function setTestRowItemStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("bStatus_"+id).value = document.getElementById("bIsFromDB_"+id).value;
		document.getElementById("bRow_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}
function getPackValue(i){



}

// ------------------------------------------------------
// Duplication check starts here
// ------------------------------------------------------
var cArr = new Array();
var cArri = 0;	
function validateGradeRepeat()
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
	
	var rc = document.getElementById("hidWeightmentTableRowCount").value;
	var pArr	= new Array();	
	var pa		= 0;
	for (j=0; j<rc; j++) {
		var status = document.getElementById("bStatus_"+j).value;
		if (status!='N') {
			var gradeValue = document.getElementById("gradeType_"+j).value;
					
			if (pArr.indexOf(gradeValue)!=-1) {
				alert("grade Type cannot be duplicate.");
				document.getElementById("gradeType_"+j).focus();
				return false;	
			}						
			pArr[pa++]	= gradeValue;					
		}
	}	
	return true;
}