function validateRMProcurmentGatePass(form)
{		
	 var supervisor	= form.supervisor.value;
	 if (supervisor=="") {
		 alert("Please select supervisor.");
		 form.supervisor.focus();
		 return false;
	 }
	
	var inseal	=	document.getElementById("in_seal_0").value;
	if (inseal=="") 
	{
		 alert("Please select seal in.");
		 document.getElementById("in_seal_0").focus();
		 return false;
	}

	var labour	=	document.getElementById("labour_0").value;
	if (labour=="") 
	{
		 alert("Please select labour.");
		 document.getElementById("labour_0").focus();
		 return false;
	}
		
	if (!timeCheck()) {
		alert("Please enter a time");
		return false;
	}
	
	var equipmentSize=document.getElementById("equipmentSize").value;
	
	if(equipmentSize>0)
	{
		for (j=0; j<equipmentSize; j++)
			{
			var equipment_required_quantity =document.getElementById("equipment_required_quantity_"+j).value;
				if(equipment_required_quantity!="")
				{
					var equipment_issued_quantity =document.getElementById("equipment_issued_quantity_"+j).value;
					if(equipment_issued_quantity=="")
					{
						alert("Please enter Equipment Issued quantity.");
						 document.getElementById("equipment_issued_quantity_"+j).focus();
						 return false;
					}
				}
			}
	}
	
	var chemicalSize=document.getElementById("chemicalSize").value;
	if(chemicalSize>0)
	{
		for (k=0; k<chemicalSize; k++)
			{
			var chemical_required_quantity =document.getElementById("chemical_required_quantity_"+k).value;
				if(chemical_required_quantity!="")
				{
					var chemical_issued_quantity =document.getElementById("chemical_issued_quantity_"+k).value;
					if(chemical_issued_quantity=="")
					{
						alert("Please enter Chemical Issued quantity.");
						 document.getElementById("chemical_issued_quantity_"+k).focus();
						 return false;
					}
				}
			}
		}
			
	if(!validateRepeatIssuance()){
	return false;
	}
	if(!checkSealDuplicate())
	{
		return false;	 
	}
	
	return checkSeal();
	
	
}


	
	function timeCheck(){
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

// ADD MULTIPLE Item- ADD ROW START
//addNewRow('tblHarvestingEquipment','<?=$harvestingEquipmentId?>', '<?=$harvestingEquipmentName?>',, '<?=$harvestingEquipmentQuantity?>');		
function addNewLabourRowOld(tableId,LabourId,labourName)
{

var tbl		= document.getElementById(tableId);

	var lastRow	= tbl.rows.length;
	// alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "Row_"+fldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	//var cell3	= row.insertCell(2);
	
	cell1.id = "srNo_"+fldId;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setTestRowItemStatusVal('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
	var labour = "<input name='labour_"+fldId+"' type='text' id='labour_"+fldId+"' value='"+labourName+"'>";
	
	var hiddenFields = "<input name='Status_"+fldId+"' type='hidden' id='Status_"+fldId+"' value=''><input name='IsFromDB_"+fldId+"' type='hidden' id='IsFromDB_"+fldId+"' value='"+ds+"'><input type='hidden' name='labourId_"+fldId+"' id='labourId_"+fldId+"' value='"+LabourId+"'>";

	//cell1.innerHTML	= "<input name='test_"+fldId+"' type='text' id='test_"+fldId+"' value=\""+unescape(vehicleType)+"\" size='24'>";
	cell1.innerHTML	= labour;
	
	cell2.innerHTML = imageButton+hiddenFields;	
	
	fldId		= parseInt(fldId)+1;	
		
	document.getElementById("hidLabourTableRowCount").value = fldId;	






//code end
	
	
	
}


function addNewSealRow(tableId,SealId,sealNumber,mode)
{

	var tbl		= document.getElementById(tableId);

	var lastRow	= tbl.rows.length;
	// alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "bRow_"+fld;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	
	cell1.id = "srNo_"+fld;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	
	if(mode==1)
	{
	
	var seal="<input name='newSeal_"+fld+"' type='text' id='newSeal_"+fld+"' value='"+sealNumber+"'>"
	}
	else
	{
	
		var seal	= "<select name='newSeal_"+fld+"' id='newSeal_"+fld+"' ><option value=''>--Select--</option>";
	<?php
		if (sizeof($sealNumbers)>0) {	
			foreach ($sealNumbers as $dcw) {
						$sealNoId = $dcw[0];
						$seal	= stripSlash($dcw[1]);
						
	?>	
	
		if (sealNumber=="<?=$sealNoId?>")  var sel = "Selected";
		else var sel = "";

	seal += "<option value=\"<?=$sealNoId?>\" "+sel+"><?=$seal?></option>";	
	<?php
			}
		}
		
	?>	
	seal += "</select>";
	}
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"bsetTestRowItemStatusVal('"+fld+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	
	var hiddenFields = "<input name='bStatus_"+fld+"' type='hidden' id='bStatus_"+fld+"' value=''><input name='bIsFromDB_"+fld+"' type='hidden' id='bIsFromDB_"+fld+"' value='"+ds+"'><input type='hidden' name='sealId_"+fld+"' id='sealId_"+fld+"' value='"+SealId+"'>";

	//cell1.innerHTML	= "<input name='test_"+fld+"' type='text' id='test_"+fld+"' value=\""+unescape(vehicleType)+"\" size='24'>";
	cell1.innerHTML	= seal;
	
	cell2.innerHTML = imageButton+hiddenFields;	
	
	fld		= parseInt(fld)+1;	
	//document.getElementById("hidTestMethodTableRowCount").value = fld;	
	document.getElementById("hidOtherSeal").value = fld;	

//code end

}





function setTestRowItemStatusVal(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("Status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		//alert('hai');
		document.getElementById("Row_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}


function bsetTestRowItemStatusVal(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("bStatus_"+id).value = document.getElementById("bIsFromDB_"+id).value;
		//alert('hai');
		document.getElementById("bRow_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
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

	var rc = document.getElementById("hidInSealSize").value;
	
	var prevOrder = 0;
	var arr = new Array();
	var arri=0;
	for( j=0; j<rc; j++ )	{
	    var status = document.getElementById("status_"+j).value;
		//alert(status);
	    if (status!='N') 
	    {
		
		var inSealNum = document.getElementById("in_seal_"+j).value;
		var inSealAlpha = document.getElementById("hidinsealAlpha_"+j).value;
		var insealnumgenid = document.getElementById("in_seal_num_genid_"+j).value;
		var rv=inSealNum+','+inSealAlpha+','+insealnumgenid;
		if ( arr.indexOf(rv) != -1 )	{
			alert("Seal  Cannot be duplicate.");
			document.getElementById("in_seal_"+j).focus();
			return false;
		}
		arr[arri++]=rv;
            }
	}
	
	return true;
}

function checkSealDuplicate()
{	var sealDuplicate='0';
	if(document.getElementById("out_seal").value!="")
	{
		var outSeal=document.getElementById("out_seal").value;
		var hidoutSealAlpha=document.getElementById("hidoutSealAlpha").value;
		var outsealnumgenid=document.getElementById("outseal_numgen_id").value;
		var outsl=outSeal+','+hidoutSealAlpha+','+outsealnumgenid;
		var rc = document.getElementById("hidInSealSize").value;
		for( j=0; j<rc; j++ )	
		{
			var status = document.getElementById("status_"+j).value;
		//alert(status);
			if (status!='N') 
			{
			
			var inSealNum = document.getElementById("in_seal_"+j).value;
			var inSealAlpha = document.getElementById("hidinsealAlpha_"+j).value;
			var insealnumgenid = document.getElementById("in_seal_num_genid_"+j).value;
			var insl=inSealNum+','+inSealAlpha+','+insealnumgenid;
				if(outsl==insl)
				{
					sealDuplicate=1;	
				}

			}
		}

		if(sealDuplicate=='1')
		{
			alert("Inseal and out seal cannot be same");
			return false;
		}
		else
		{
			return true;
		}


	}
	else
	{
		 return true;
	}

}
function checkSeal()
{
	var outsealAll="";
	//###for out seal
	if(document.getElementById("out_seal").value!="")
	{
		var outSeal=document.getElementById("out_seal").value;
		var hidoutSealAlpha=document.getElementById("hidoutSealAlpha").value;
		var outsealnumgenid=document.getElementById("outseal_numgen_id").value;
		outsealAll+=outSeal+"/"+hidoutSealAlpha+"/"+outsealnumgenid;
		//alert(outSeal+"--"+hidoutSealAlpha+"--"+outsealnumgenid);
		//xajax_checkSealUsedOut(outSeal,hidoutSealAlpha,outsealnumgenid);
	}

	//###for in seal
	var rc = document.getElementById("hidInSealSize").value;
	//alert("rc==>"+rc);
	var insealNumArr=new Array();
	var insealAlphaArr=new Array();
	var insealnumgenidArr=new Array();
	if(rc > 0)
	{	var l=0;
		for( j=0; j<rc; j++ )	
		{
			var status = document.getElementById("status_"+j).value;
			//alert(status);
			
			if (status!='N') 
			{
				var inSealNum = document.getElementById("in_seal_"+j).value;
				var inSealAlpha = document.getElementById("hidinsealAlpha_"+j).value;
				var insealnumgenid = document.getElementById("in_seal_num_genid_"+j).value;
				//alert(inSealNum+"--"+inSealAlpha+"--"+insealnumgenid);
				if(insealNumArr=="")
				{
					insealNumArr=inSealNum+"/"+inSealAlpha+"/"+insealnumgenid;
				}
				else
				{
					insealNumArr+=","+inSealNum+"/"+inSealAlpha+"/"+insealnumgenid;
				}
				l++;
				//xajax_checkSealUsedIn(inSealNum,inSealAlpha,insealnumgenid);

				
			}
		}
		
		if(insealNumArr!='' && outsealAll!="")
		{
			insealNumArr+=","+outsealAll;
			xajax_checkSealUsedIn(insealNumArr);
		}
		else if(insealNumArr!='')
		{
			xajax_checkSealUsedIn(insealNumArr);		
		}
	}

return false;

}


function calculateChemicalDiff(diffValue,diffFromId,resultShowId)
{
	if(isNaN(diffValue))
	{
		alert('Please enter the valid quantity');
	}
	else
	{
		var diffFrom = parseInt(document.getElementById(diffFromId).value);
		if(diffValue > diffFrom)
		{
			alert('Issued quantity must be less than required quantity');
		}
		else
		{
			var result = parseInt(diffFrom) - parseInt(diffValue);
			document.getElementById(resultShowId).value = result;
		}
	}
}
function calculateEquipDiff(diffValue,diffFromId,resultShowId)
{
	if(isNaN(diffValue))
	{
		alert('Please enter the valid quantity');
	}
	else
	{
		var diffFrom = parseInt(document.getElementById(diffFromId).value);
		if(diffValue > diffFrom)
		{
			alert('Issued quantity must be less than required quantity');
		}
		else
		{
			var result = parseInt(diffFrom) - parseInt(diffValue);
			document.getElementById(resultShowId).value = result;
		}
	}
}
function addNewLabourRow()
{
	//fld		= parseInt(fld)+1;	

	fld=document.getElementById("labourSize").value ;
	tableId = 'tblNewLabour';
	var tbl		= document.getElementById(tableId);

	var lastRow	= parseInt(tbl.rows.length) - 1;
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "SlNLABRow_"+fld;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	
	cell1.id = "srNoLa_"+fld;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	var ds = "N";
	var cell_1_content = '<input type="text" size="15" id="labour_'+fld+'" name="labour_'+fld+'">';
	var cell_2_content = '<a onclick="setTestRowItemStatusLabour('+fld+')" href="javascript:void(0);"><img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item"></a><input name="sstatus_'+fld+'" type="hidden" id="sstatus_'+fld+'" value=""><input name="IsFromDB_'+fld+'" type="hidden" id="IsFromDB_'+fld+'" value="'+ds+'">';
	
	cell1.innerHTML = cell_1_content;
	cell2.innerHTML = cell_2_content;

	fld		= parseInt(fld)+1;
	document.getElementById("labourSize").value = fld;
	
	//fillInSeals();
	//fillInSeal(fld);
}
function setTestRowItemStatusLabour(hideRowId)
{
	if(hideRowId == 0)
	{
		alert('You can not delete first row');
	}
	else
	{
		if (confirmRemoveItem())
		{
	
		document.getElementById("sstatus_"+hideRowId).value = document.getElementById("IsFromDB_"+hideRowId).value;
		document.getElementById("SlNLABRow_"+hideRowId).style.display = 'none'; 		
		}
		return false;
		
	}
	//fillInSeals();
}
function addNewInSealRow()
{
	var alpha_code = '<?=$alpha_code?>';
	fld		= parseInt(fld)+1;	
	tableId = 'tblNewInSeal';
	var tbl		= document.getElementById(tableId);

	var lastRow	= parseInt(tbl.rows.length) - 1;
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "SlNRow_"+fld;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);

	cell1.id = "srNo_"+fld;		cell2.id = "insealAlpha_"+fld;	
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center"; 
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	var ds = "N";
	var cell_1_content = '&nbsp;';
	var cell_2_content = alpha_code;
	var cell_3_content = ' <input type="text" class="in_seal_class" size="15" id="in_seal_'+fld+'" name="in_seal_'+fld+'"><input type="hidden"  size="15" id="in_seal_num_genid_'+fld+'" name="in_seal_num_genid_'+fld+'"><input type="hidden" value="" name="hidinsealAlpha_'+fld+'" id="hidinsealAlpha_'+fld+'"/>';
	var cell_4_content = '<a onclick="setTestRowItemStatus('+fld+')" href="javascript:void(0);"><img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item"></a><input name="status_'+fld+'" type="hidden" id="status_'+fld+'" value=""><input name="IsFromDB_'+fld+'" type="hidden" id="IsFromDB_'+fld+'" value="'+ds+'">';
	
	cell1.innerHTML = cell_1_content;
	cell2.innerHTML = cell_2_content;
	cell3.innerHTML = cell_3_content;
	cell4.innerHTML = cell_4_content;
	fillInsealRow(fld);
	//fillInSeals();
	//fillInSeal(fld);
}
function setTestRowItemStatus(hideRowId)
{
	if(hideRowId == 0)
	{
		alert('You can not delete first row');
	}
	else
	{
		if (confirmRemoveItem())
		{
		
			document.getElementById("status_"+hideRowId).value = document.getElementById("IsFromDB_"+hideRowId).value;
			document.getElementById("SlNRow_"+hideRowId).style.display = 'none'; 		
		}
		return false;
		//document.getElementById('SlNRow_'+hideRowId).style.display = 'none';
		//document.getElementById('SlNRow_'+hideRowId).innerHTML = '';
	}
	//fillInSeals();
}
function fillInSeals()
{
	var startNo    = '<?=$startNo?>';
	var alpha_code = '<?=$alpha_code?>';
	var out_seal = document.getElementById('out_seal').value;
	if(out_seal == '')
	{
		var insealstart = parseInt(startNo);
	}
	else
	{
		var insealstart = parseInt(startNo) + 1;
	}
	var in_seal = document.getElementsByName('in_seal[]');
	for(i=0;i<in_seal.length;i++)
	{
		var insealprintno = parseInt(insealstart) + parseInt(i);
		in_seal[i].value = insealprintno;
	}
}
function validateProcurment()
{
	var sealsAvailable = document.getElementById('sealsAvailable').value;
	
	var procurementIdsValues = '<?=$procurementIds?>';
	var procurementIds = procurementIdsValues.split(',');
	var in_seal = document.getElementsByName('in_seal[]');
	var out_seal = document.getElementById('out_seal').value;
	var supervisor = document.getElementById('supervisor').value;
	var labour = document.getElementsByName('labour[]');
	if(sealsAvailable != '')
	{
		alert(sealsAvailable);
		return false;
	}
	for(i=0;i<in_seal.length;i++)
	{
		if(in_seal[i].value == '')
		{
			alert('Please enter in seal no');
			in_seal[i].focus();
			return false;
		}
		else if(in_seal[i].value == out_seal)
		{
			alert('Seal no is assigned in out seal,Please choose other no');
			in_seal[i].focus();
			return false;
		}
		else
		{
			if(i != 0)
			{
				for(vd=0;vd<i;vd++)
				{
					if(in_seal[vd].value == in_seal[i].value)
					{
						alert('Seal no must be uniq');
						in_seal[i].focus();
						return false;
					}
				}
			}
		}
	}
	if(out_seal != '')
	{
		if (!timeCheck()) {
			// alert("Please enter a time");
			return false;
		}
	}
	for(t=0;t<labour.length;t++)
	{
		if(labour[t].value == '')
		{
			alert('Please enter labour name');
			labour[t].focus();
			return false;
		}
	}	
	if(supervisor == '')
	{
		alert('Please choose the receiving supervisor');
		document.getElementById('supervisor').focus();
		return false;
	}
	for(k=0;k<procurementIds.length;k++)
	{
		equipment_required_quantity = document.getElementsByName('equipment_required_quantity[]');
		equipment_issued_quantity   = document.getElementsByName('equipment_issued_quantity[]');
		equipmifference             = document.getElementsByName('equipmifference[]');
		
		chemical_required_quantity  = document.getElementsByName('chemical_required_quantity[]');
		chemical_issued_quantity    = document.getElementsByName('chemical_issued_quantity[]');
		chemical_difference         = document.getElementsByName('chemical_difference[]');
		for(v=0;v<equipment_issued_quantity.length;v++)
		{
			if(equipment_issued_quantity[v].value == '' || isNaN(equipment_issued_quantity[v].value))
			{
				alert('Please enter valid equipment issued quantity');
				equipment_issued_quantity[v].focus();
				return false;
			}
			else if(parseInt(equipment_issued_quantity[v].value) > parseInt(equipment_required_quantity[v].value))
			{
				alert('Equipment issued quantity must be less than or equal to required quantity');
				equipment_issued_quantity[v].focus();
				return false;
			}
			if(chemical_issued_quantity[v].value == '' || isNaN(chemical_issued_quantity[v].value))
			{
				alert('Please enter valid chemical issued quantity');
				chemical_issued_quantity[v].focus();
				return false;
			}
			else if(parseInt(chemical_issued_quantity[v].value) > parseInt(chemical_required_quantity[v].value))
			{
				alert('Chemical issued quantity must be less than or equal to required quantity');
				chemical_issued_quantity[v].focus();
				return false;
			}
		}
	}
}

//assigned next52 seal number
function getNext(i,id,startno,endno,alphacode,inputStatus,row,startOriginal,pageNo)
{
	//alert("hii");
	var newStartNo=parseInt(startno)+52;
	var newPage=parseInt(pageNo)+1;
	xajax_getAllSealNo(i,id,newStartNo,endno,alphacode,inputStatus,row,startOriginal,newPage);
}

//assigned previous52 seal number
function getPrevious(i,id,startno,endno,alphacode,inputStatus,row,startOriginal,pageNo)
{
	//alert("hii");
	var newStartNo=parseInt(startno)-52;
	var newPage=parseInt(pageNo)-1;
	xajax_getAllSealNo(i,id,newStartNo,endno,alphacode,inputStatus,row,startOriginal,newPage);
}

//assigned next52 seal number according to the seal number
function getNextSearch(i,id,startno,endno,alphacode,inputStatus,row,startOriginal,pageNo,searchNo)
{
	//alert("hii");
	//document.getElementById("searchSeal_"+i).value=searchNo;
	var newPage=parseInt(pageNo)+1;
	xajax_getSearchSealNo(i,id,startno,endno,alphacode,inputStatus,row,startOriginal,newPage,searchNo);
}

//assigned previous52 seal number according to the seal number
function getPreviousSearch(i,id,startno,endno,alphacode,inputStatus,row,startOriginal,pageNo,searchNo)
{
	//alert("hii");
	var newPage=parseInt(pageNo)-1;
	xajax_getSearchSealNo(i,id,startno,endno,alphacode,inputStatus,row,startOriginal,newPage,searchNo);
}

//assigning seal numbers filtered by seal number in search
function getSearchResult(i,id,startno,endno,alphacode,inputStatus,row,startOriginal,pageNo)
{
	//alert("hii");
	//alert(document.getElementById("searchSeal_"+i).value);
	var searchNo=document.getElementById("searchSeal_"+i).value;
	if(searchNo!="")
	{
		//alert("hii");
		//
		xajax_getSearchSealNo(i,id,startOriginal,endno,alphacode,inputStatus,row,startOriginal,pageNo,searchNo);
		//xajax_getSearchSealNo(i,id,startno,endno,alphacode,inputStatus,row,startOriginal,pageNo,searchNo);
	}
	else
	{	//alert("hui");
		xajax_getAllAvailableAlphaPrefix(inputStatus,row);
	}
}

function sealStatusRen(result)
{
	//alert(result);
	if(result=="0")
	{
	  if (!confirmSave())
		{
		  	return false;	
		}
		else
		{
			showFnLoading(); 
			document.getElementById("cmdsave").value='1';
			document.RMProcurmentGatePass.submit();
		}
	 	
	}
	else
	{
		return false;
	}
}