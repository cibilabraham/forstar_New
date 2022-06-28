function validateWeightmentAfterGrading(form)
{
//alert("hii");
	var rm_lot_id   = document.getElementById('rm_lot_id').value;
	var total=document.getElementById('total').value;
	
	if(rm_lot_id == '')
	{
		alert("Please choose the rm lot id");
		return false;
	}
	if(total == '')
	{
		alert("Cannot save data.");
		return false;
	}
	
	var itemCount	= document.getElementById("hidTableRowCount").value;
	var count = 0;
	for (i=0; i<itemCount; i++)
	{
		var status = document.getElementById("status_"+i).value;		    
		if (status!='N') 
		{
			var fish_id      = document.getElementById("fish_id_"+i);
			var process_code = document.getElementById("process_code_"+i);
			//var count_code   = document.getElementById("count_code_"+i);
			var grading		 = document.getElementById("grading_"+i);
			var weight	     = document.getElementById("weight_"+i);
					
			if(fish_id.value == '')
			{
				alert("Please Select a Fish.");
				fish_id.focus();
				return false;
			}
			if(process_code.value == '')
			{
				alert("Please Select a Process Code.");
				process_code.focus();
				return false;
			}
			/*if( count_code.value == "" )
			{
				alert("Please enter a Count code");
				count_code.focus();
				return false;
			}	*/
			if( grading.value == "" )
			{
				alert("Please Select a Grading.");
				grading.focus();
				return false;
			}	
			
			if( weight.value == "" )
			{
				alert("Please enter a Weight.");
				weight.focus();
				return false;
			}	
			
			
		} 
		else 
		{
			count++;
		}
	}
	
	if(!validateRepeatIssuanceGrading()){
	
		 return false;
	}
	

	
	
	if (!confirmSave()) return false;
	return true;

}
function addNewWeightmentGrading(tableId,editWeightmentId,gradingval, weight, mode)
{
	// alert(mode);
	// alert('hi');
//	alert(editProcurmentId);
	//var rowCountObj	= formObj.rowCount;
	var material="";
	var materialType=document.getElementById('material_type').value;
	if(materialType=="Pre process")
	{
		material=1;
	}
	else
	{
		material=0;
	}
	var rm_lot_id = document.getElementById('rm_lot_id').value;
	if(rm_lot_id == '')
	{
		alert('Please choose the rm lot id');
	}
	else
	{
		var tbl			= document.getElementById(tableId);
		
		var lastRow		= tbl.rows.length;
		// alert(lastRow);
		// if(lastRow >= 2 && fieldId == 0)
		// {
			// fieldId		= parseInt(fieldId) + parseInt(lastRow) - 1;
		// }
		var fieldLength=document.getElementById('hidTableRowCount').value;
		//alert();
		if(fieldLength>0)
		{
		var fieldId  = fieldLength;
		}
		var iteration		= lastRow+1;
		var row			= tbl.insertRow(lastRow);
		row.height		= "22";
		row.className 		= "whiteRow";
		row.id 			= "row_"+fieldId;

		var cell1			= row.insertCell(0);
		var cell2			= row.insertCell(1);
		var cell3			= row.insertCell(2);
		var cell4			= row.insertCell(3);
		var cell5			= row.insertCell(4);
		//var cell6			= row.insertCell(5);
		
		/*var cell6			= row.insertCell(5);
		var cell7			= row.insertCell(6);
		var cell8			= row.insertCell(7);
		var cell9			= row.insertCell(8);
		var cell10			= row.insertCell(9);*/

		cell1.className	=	"listing-item"; cell1.align = 'left';
		cell2.className	=	"listing-item"; cell2.align = "left";
		cell3.className	=	"listing-item"; cell3.align = 'left';
		cell4.className	=	"listing-item"; cell3.align = 'left';
		cell5.className	=	"listing-item"; cell3.align = 'left';
		//cell6.className	=	"listing-item"; cell3.align = 'left';
		
		/*cell6.className	=	"fieldName"; cell6.align = "center";
		cell7.className	=	"fieldName"; cell7.align = "center";
		cell8.className	=	"fieldName"; cell8.align = "center";
		cell9.className	=	"fieldName"; cell9.align = "center";
		cell10.className	=	"fieldName"; cell10.align = "center";*/
		/*cell11.className	=	"fieldName"; cell11.align = "center";*/
		
		var ds = "N";	
		var imageButton = "<a href='###' onClick=\"setIssuanceItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

		var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='rmId_"+fieldId+"' id='rmId_"+fieldId+"' value='"+editWeightmentId+"'><input name='lotidAvailable_"+fieldId+"' type='hidden' id='lotidAvailable_"+fieldId+"' value='1'>";

		//var vehicle="<input type='hidden' id='vehicle' name='vehicle' >";
		
		/*var equipmentName			= "<select name='equipmentName_"+fieldId+"' Style='display:display;' id='equipmentName_"+fieldId+"' tabindex=1  onchange=\"xajax_equipmentQuantity(document.getElementById('equipmentName_"+fieldId+"').value,document.getElementById('vehicle').value,"+fieldId+");  balanceQty();\"  >";
			equipmentName += "<option value=''>--select--</option>";
		equipmentName +="</select>";*/
		// alert(mode);
		// if(mode == 'addmode')
		// {
			var fishes = "<select name='fish_id_"+fieldId+"' onchange=\"xajax_getProcessCode(this.value,document.getElementById('rm_lot_id').value,"+fieldId+","+material+");\" id='fish_id_"+fieldId+"' >"+document.getElementById("fish_id_0").innerHTML+"</select>";
		// }
		// else
		// {
			// var fishes = '<select name="fish_id_'+fieldId+'" onchange="xajax_getProcessCode(this.value,'+fieldId+');" id="fish_id_'+fieldId+'" ><option value=""> -- Select -- </option></select>';
		// }
		var process_code = '<select name="process_code_'+fieldId+'" id="process_code_'+fieldId+'" onchange="xajax_getGrading(this.value,'+fieldId+');"><option value=""> -- Select -- </option></select>';
		//var count_code = '<input type="text" name="count_code_'+fieldId+'" size="12" id="count_code_'+fieldId+'" />';
		var grading			= "<select name='grading_"+fieldId+"' Style='display:display;' id='grading_"+fieldId+"'><option value=''> -- Select -- </option>";
			<?php
			if (sizeof($gradingRecs)>0) {	
				foreach ($gradingRecs as $dcw) {
							$gradingId = $dcw[0];
							$gradingName	= stripSlash($dcw[1]);
							
		?>	
		
			if (gradingval=="<?=$gradingId?>")  var sel = "Selected";
			else var sel = "";

		// grading += "<option value=\"<?=$gradingId?>\" "+sel+"><?=$gradingName?></option>";	
		<?php
				}
			}
			
		?>	
		grading += "</select>";
		
		
		/*var chemicalName			= "<select name='chemicalName_"+fieldId+"' Style='display:display;' id='chemicalName_"+fieldId+"' tabindex=1  onchange=\"xajax_chemicalQuantity(document.getElementById('chemicalName_"+fieldId+"').value,document.getElementById('vehicleNo_"+fieldId+"').value,"+fieldId+");\"  >";
		chemicalName += "<option value=''>--select--</option>";
		chemicalName +="</select>";*/

		
		
		
		
		//cell1.innerHTML	= driverName;
		//cell2.innerHTML	= vehicleNo;
		// alert(fishes);
		cell1.innerHTML	= fishes;
		cell2.innerHTML	= process_code;
		cell3.innerHTML	= grading;
		cell4.innerHTML	= "<input name='weight_"+fieldId+"' type='text' id='weight_"+fieldId+"' size='4'  value='"+weight+"' tabindex="+fieldId+" onkeyup='checkValue("+fieldId+");' >";
		cell5.innerHTML = imageButton+hiddenFields;
		/*cell3.innerHTML	= count_code;
		cell4.innerHTML	= grading;
		cell5.innerHTML	= "<input name='weight_"+fieldId+"' type='text' id='weight_"+fieldId+"' size='4'  value='"+weight+"' tabindex="+fieldId+" onkeyup='checkValue("+fieldId+");' >";
		cell6.innerHTML = imageButton+hiddenFields;*/
		if(mode=="addmode")
		{
			// xajax_getGrading(document.getElementById('pondName').value,document.getElementById('rm_lot_id').value,fieldId,'');
		}
		
		
		fieldId		= parseInt(fieldId)+1;
		document.getElementById("hidTableRowCount").value = fieldId;
		// alert(document.getElementById("hidTableRowCount").value);
	}
}



function addNewWeightmentGradingSpecies(tableId,editWeightmentId,fishVal,gradingval, weight, mode)
{
	// alert(mode);
	// alert('hi');
//	alert(editProcurmentId);
	//var rowCountObj	= formObj.rowCount;
	
	var rm_lot_id = document.getElementById('rm_lot_id').value;
	if(rm_lot_id == '')
	{
		alert('Please choose the rm lot id');
	} 
	else
	{
		var fldLength=document.getElementById('hidTableRowCount').value;
		//alert();
		if(fldLength>0)
		{
		var fieldvalue  = fldLength;
		}
		
		var tbl			= document.getElementById(tableId);
		var lastRow		= tbl.rows.length;
		var iteration		= lastRow+1;
		var row			= tbl.insertRow(lastRow);
		row.height		= "22";
		row.className 		= "whiteRow";
		row.id 			= "row_"+fieldvalue;

		var cell1			= row.insertCell(0);
		var cell2			= row.insertCell(1);
		var cell3			= row.insertCell(2);
		var cell4			= row.insertCell(3);
		var cell5			= row.insertCell(4);
		//var cell6			= row.insertCell(5);
		
		/*var cell6			= row.insertCell(5);
		var cell7			= row.insertCell(6);
		var cell8			= row.insertCell(7);
		var cell9			= row.insertCell(8);
		var cell10			= row.insertCell(9);*/

		cell1.className	=	"listing-item"; cell1.align = 'left';
		cell2.className	=	"listing-item"; cell2.align = "left";
		cell3.className	=	"listing-item"; cell3.align = 'left';
		cell4.className	=	"listing-item"; cell3.align = 'left';
		cell5.className	=	"listing-item"; cell3.align = 'left';
		//cell6.className	=	"listing-item"; cell3.align = 'left';
		
		/*cell6.className	=	"fieldName"; cell6.align = "center";
		cell7.className	=	"fieldName"; cell7.align = "center";
		cell8.className	=	"fieldName"; cell8.align = "center";
		cell9.className	=	"fieldName"; cell9.align = "center";
		cell10.className	=	"fieldName"; cell10.align = "center";*/
		/*cell11.className	=	"fieldName"; cell11.align = "center";*/
		
		var ds = "N";	
		var imageButton = "<a href='###' onClick=\"setIssuanceItemStatus('"+fieldvalue+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

		var hiddenFields = "<input name='status_"+fieldvalue+"' type='hidden' id='status_"+fieldvalue+"' value=''><input name='IsFromDB_"+fieldvalue+"' type='hidden' id='IsFromDB_"+fieldvalue+"' value='"+ds+"'><input type='hidden' name='rmId_"+fieldvalue+"' id='rmId_"+fieldvalue+"' value='"+editWeightmentId+"'><input name='lotidAvailable_"+fieldvalue+"' type='hidden' id='lotidAvailable_"+fieldvalue+"' value='0'>";

		
		
			var fishes = "<select name='fish_id_"+fieldvalue+"' onchange=\"xajax_getAllProcessCode(this.value,"+fieldvalue+",'');\" id='fish_id_"+fieldvalue+"' ><option value=''> -- Select -- </option><";
					<?php
			if (sizeof($fishes_master)>0) {	
				foreach ($fishes_master as $fish) {
							$fishId = $fish[0];
							$fishName	= stripSlash($fish[1]);
							
				?>	
		
			if (fishVal=="<?=$fishId?>")  var sel = "Selected";
			else var sel = "";

		fishes+= "<option value=\"<?=$fishId?>\" "+sel+"><?=$fishName?></option>";	
		<?php
				}
			}
			
		?>			
			
			fishes+="</select>";
		// }
		// else
		// {
			// var fishes = '<select name="fish_id_'+fieldId+'" onchange="xajax_getProcessCode(this.value,'+fieldId+');" id="fish_id_'+fieldId+'" ><option value=""> -- Select -- </option></select>';
		// }
		var process_code = '<select name="process_code_'+fieldvalue+'" id="process_code_'+fieldvalue+'" onchange="xajax_getGrading(this.value,'+fieldvalue+');"><option value=""> -- Select -- </option></select>';
		//var count_code = '<input type="text" name="count_code_'+fieldvalue+'" size="12" id="count_code_'+fieldvalue+'" />';
		var grading			= "<select name='grading_"+fieldvalue+"' Style='display:display;' id='grading_"+fieldvalue+"'><option value=''> -- Select -- </option>";
			<?php
			if (sizeof($gradingRecs)>0) {	
				foreach ($gradingRecs as $dcw) {
							$gradingId = $dcw[0];
							$gradingName	= stripSlash($dcw[1]);
							
		?>	
		
			if (gradingval=="<?=$gradingId?>")  var sel = "Selected";
			else var sel = "";

		// grading += "<option value=\"<?=$gradingId?>\" "+sel+"><?=$gradingName?></option>";	
		<?php
				}
			}
			
		?>	
		grading += "</select>";
		
		
		/*var chemicalName			= "<select name='chemicalName_"+fieldId+"' Style='display:display;' id='chemicalName_"+fieldId+"' tabindex=1  onchange=\"xajax_chemicalQuantity(document.getElementById('chemicalName_"+fieldId+"').value,document.getElementById('vehicleNo_"+fieldId+"').value,"+fieldId+");\"  >";
		chemicalName += "<option value=''>--select--</option>";
		chemicalName +="</select>";*/

		
		
		
		
		//cell1.innerHTML	= driverName;
		//cell2.innerHTML	= vehicleNo;
		// alert(fishes);
		cell1.innerHTML	= fishes;
		cell2.innerHTML	= process_code;
		cell3.innerHTML	= grading;
		cell4.innerHTML	= "<input name='weight_"+fieldvalue+"' type='text' id='weight_"+fieldvalue+"' size='4'  value='"+weight+"' tabindex="+fieldvalue+" onkeyup='checkValue("+fieldvalue+");' >";
		cell5.innerHTML = imageButton+hiddenFields;
		/*cell3.innerHTML	= count_code;
		cell4.innerHTML	= grading;
		cell5.innerHTML	= "<input name='weight_"+fieldvalue+"' type='text' id='weight_"+fieldvalue+"' size='4'  value='"+weight+"' tabindex="+fieldvalue+" onkeyup='checkValue("+fieldvalue+");' >";
		cell6.innerHTML = imageButton+hiddenFields;*/
		if(mode=="addmode")
		{
			// xajax_getGrading(document.getElementById('pondName').value,document.getElementById('rm_lot_id').value,fieldId,'');
		}
		
		
		fieldvalue		= parseInt(fieldvalue)+1;
		document.getElementById("hidTableRowCount").value = fieldvalue;
		// alert(document.getElementById("hidTableRowCount").value);
	}
}









function setIssuanceItemStatus(id)
{

if (confirmRemoveItem())
	{
	
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none'; 

		//alert("hii");	
		var weight=parseFloat(document.getElementById("weight_"+id).value);	
		var effectiveWeight=parseFloat(document.getElementById("effectiveWeight").value);	
		var	difference=parseFloat(document.getElementById("difference").value);	
		var total=parseFloat(document.getElementById("total").value);	
		var differenceval=difference+weight;
		document.getElementById("difference").value=differenceval;
		var totalVal=total-weight;
		document.getElementById("total").value=totalVal;	
	}
	


	return false;


}
function checkValue(id)
{
	var total=0;

	var cntval=parseInt(document.getElementById("hidTableRowCount").value);
	//var effectiveWeight = parseFloat(document.getElementById("effectiveWeight").value);
	// alert(cntval+'-----'+effectiveWeight);
	for(i=0; i<cntval; i++)
	{
	
		var wt=parseInt(document.getElementById("weight_"+i).value);
		var stsus=document.getElementById("status_"+i).value;
		// alert(wt+'-----'+stsus);
		if(wt!="" && stsus!="N")
		{ 
			//alert(wt);
			total = parseInt(total) + wt;
				//alert(total);
		}
	}
	// alert(total+'-----'+effectiveWeight);
	document.getElementById("total").value=total;
	
	/*if(effectiveWeight!="")
	{
		// alert('hi');
	var difference=effectiveWeight-parseFloat(total);
	document.getElementById("difference").value=difference;
	if(difference<0)
	{
		alert("difference must be equal to zero");
	}	
	}*/
	
}

function validateRepeatIssuance()
{
//alert('aaa');
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
	for( j=0; j<rc; j++ )	{
	//alert('aaa');
	    var status = document.getElementById("status_"+j).value;
	    if (status!='N') 
	    {
		var rv = document.getElementById("grading_"+j).value;	
		if ( arr.indexOf(rv) != -1 )	{
			alert("Grading  Cannot be duplicate.");
			document.getElementById("grading_"+j).focus();
			return false;
		}
		arr[arri++]=rv;
            }
	}
	
	
	return true;	
}
function validateRepeatIssuanceGrading()
{
	var totalRows = document.getElementById("hidTableRowCount").value;
	var arr = [];
	for(i=0;i<totalRows;i++)
	{
		var status        = document.getElementById("status_"+i).value;
		var fish_id       = document.getElementById("fish_id_"+i).value;
		var process_code  = document.getElementById("process_code_"+i).value;
		var grading       = document.getElementById("grading_"+i).value;
		
		var arrayInsertValue = fish_id+','+process_code+','+grading;
		arr.push(arrayInsertValue);
	}
	var sorted_arr = arr.sort(); // You can define the comparing function here. 
                             // JS by default uses a crappy string compare.
	var results = [];
	for (var i = 0; i < arr.length - 1; i++) {
		if (sorted_arr[i + 1] == sorted_arr[i]) {
			results.push(sorted_arr[i]);
		}
	}
	if(results != '')
	{
		alert("Fish , process code and grading combination must be unique");
		return false;
	}
	else{
		return true;
	}
}
function pendingRecordsInDailyCatchEntry()
{
	alert("All entries for this rm lotid is not entered in daily catch entry ");
	return false;
}